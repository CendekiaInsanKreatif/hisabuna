<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Coa;
use App\Models\Saldo;
use App\Imports\JurnalDetailImport;
use App\Exports\JurnalSampleExport;
use Maatwebsite\Excel\Facades\Excel;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jurnal = Jurnal::with('details')->whereNull('is_deleted')->where('created_by', auth()->user()->id)->get()->toArray();

        return view('jurnal.index', compact('jurnal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coa = Coa::whereNull('is_deleted')->where('created_by', auth()->user()->id)->get()->toArray();
        return view('jurnal.form', compact('coa'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            if(array_sum($input['debit']) != array_sum($input['kredit'])){
                return redirect()->route('jurnal.index')->with('message', 'Debit tidak sama dengan kredit.')->with('color', 'red');
            }

            $jurnal = Jurnal::whereNull('is_deleted')->where('created_by', auth()->user()->id)->get();

            if ($jurnal->isNotEmpty()) {
                $countJenis = $jurnal->where('jenis', strtoupper($input['jenis']))->count();
                $input['no_transaksi'] = $countJenis + 1;
            } else {
                $input['no_transaksi'] = 1;
            }

            $dataJurnal = Jurnal::create([
                'jenis' => strtoupper($input['jenis']),
                'no_transaksi' => $input['no_transaksi'],
                'jurnal_tgl' => now(),
                'subtotal' => array_sum($input['debit']),
                'keterangan' => $input['keterangan_header'],
                'created_by' => Auth::user()->id,
                'created_at' => now()
            ]);

            $details = [];
            foreach ($input['no_akun'] as $index => $noAkun) {
                $saldo = Saldo::where('coa_akun', $noAkun)->first();

                $debit = (int)$input['debit'][$index];
                $kredit = (int)$input['kredit'][$index];

                $saldo_awal_debit = $saldo->current_saldo_debit ?: $saldo->saldo_awal_debit;
                $saldo_awal_kredit = $saldo->current_saldo_kredit ?: $saldo->saldo_awal_kredit;

                $current_debit = $saldo_awal_debit + $debit - $kredit;
                $current_credit = $saldo_awal_kredit + $kredit - $debit;

                $saldo->where('coa_akun', $input['no_akun'][$index])->update([
                    'periode_saldo' => date('Y'),
                    'saldo_awal_debit' => $saldo_awal_debit,
                    'saldo_awal_kredit' => $saldo_awal_kredit,
                    'current_saldo_debit' => $current_debit,
                    'current_saldo_kredit' => $current_credit,
                    'saldo_total_transaksi_debit' => $saldo->saldo_total_transaksi_debit + $debit,
                    'saldo_total_transaksi_kredit' => $saldo->saldo_total_transaksi_kredit + $kredit,
                    'updated_at' => now()
                ]);

                $details[] = [
                    'jurnal_id' => $dataJurnal->id,
                    'coa_akun' => $noAkun,
                    'debit' => $debit,
                    'credit' => $kredit,
                    'keterangan' => $input['keterangan'][$index] ?: $input['keterangan_header'],
                    'created_by' => Auth::user()->id,
                    'created_at' => now()
                ];
            }

            foreach ($details as $detail) {
                JurnalDetail::create($detail);
            }

            DB::commit();
            return redirect()->route('jurnal.index')->with('message', 'Jurnal berhasil dibuat.')->with('color', 'green');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('jurnal.index')->with('message', 'Gagal membuat jurnal: ' . $e->getMessage())->with('color', 'red');
        }
    }

    public function show(Jurnal $jurnal){

    }

    public function edit(Jurnal $jurnal)
    {
        $jurnal = Jurnal::with(['details.coa'])
                ->whereNull('is_deleted')
                ->where('created_by', auth()->user()->id)
                ->orderBy('jurnal_tgl', 'desc')
                ->find($jurnal->id);
        $coa = Coa::whereNull('is_deleted')
                ->where('created_by', auth()->user()->id)
                ->where('level', 5)
                ->get()
                ->toArray();

        return view('jurnal.form', compact('jurnal', 'coa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurnal $jurnal)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            if(array_sum($input['debit']) != array_sum($input['kredit'])){
                return redirect()->route('jurnal.edit', $jurnal)->with('message', 'Debit tidak sama dengan kredit.')->with('color', 'red');
            }

            $existingJournals = Jurnal::whereNull('is_deleted')
                ->where('created_by', auth()->user()->id)
                ->where('id', '<>', $jurnal->id)
                ->get();

            if ($existingJournals->isNotEmpty()) {
                $countJenis = $existingJournals->where('jenis', strtoupper($input['jenis']))->count();
                $input['no_transaksi'] = $countJenis + 1;
            } else {
                $input['no_transaksi'] = 1;
            }

            $jurnal->jenis = strtoupper($input['jenis']);
            $jurnal->no_transaksi = $input['no_transaksi'];
            $jurnal->jurnal_tgl = now();
            $jurnal->subtotal = array_sum($input['debit']);
            $jurnal->keterangan = $input['keterangan_header'];
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->updated_at = now();
            $jurnal->save();

            $deletedDetails = JurnalDetail::where('jurnal_id', $jurnal->id)->get();
            foreach ($deletedDetails as $detail) {
                $saldo = Saldo::where('coa_akun', $detail->coa_akun)->first();

                $saldo_awal_debit = $saldo->current_saldo_debit ?: $saldo->saldo_awal_debit;
                $saldo_awal_kredit = $saldo->current_saldo_kredit ?: $saldo->saldo_awal_kredit;

                $current_debit = $saldo_awal_debit - $detail->debit;
                $current_credit = $saldo_awal_kredit - $detail->credit;

                $saldo->where('coa_akun', $detail->coa_akun)->update([
                    'periode_saldo' => date('Y'),
                    'saldo_awal_debit' => $saldo_awal_debit,
                    'saldo_awal_kredit' => $saldo_awal_kredit,
                    'current_saldo_debit' => $current_debit,
                    'current_saldo_kredit' => $current_credit,
                    'saldo_total_transaksi_debit' => $saldo->saldo_total_transaksi_debit - $detail->debit,
                    'saldo_total_transaksi_kredit' => $saldo->saldo_total_transaksi_kredit - $detail->credit,
                    'updated_at' => now()
                ]);
            }

            JurnalDetail::where('jurnal_id', $jurnal->id)->delete();

            $details = [];
            foreach ($input['no_akun'] as $index => $noAkun) {
                $saldo = Saldo::where('coa_akun', $noAkun)->first();

                $debit = (int)$input['debit'][$index];
                $kredit = (int)$input['kredit'][$index];

                $saldo_awal_debit = $saldo->current_saldo_debit ?: $saldo->saldo_awal_debit;
                $saldo_awal_kredit = $saldo->current_saldo_kredit ?: $saldo->saldo_awal_kredit;

                $current_debit = $saldo_awal_debit + $debit - $kredit;
                $current_credit = $saldo_awal_kredit + $kredit - $debit;

                $saldo->where('coa_akun', $noAkun)->update([
                    'periode_saldo' => date('Y'),
                    'saldo_awal_debit' => $saldo_awal_debit,
                    'saldo_awal_kredit' => $saldo_awal_kredit,
                    'current_saldo_debit' => $current_debit,
                    'current_saldo_kredit' => $current_credit,
                    'saldo_total_transaksi_debit' => $saldo->saldo_total_transaksi_debit + $debit,
                    'saldo_total_transaksi_kredit' => $saldo->saldo_total_transaksi_kredit + $kredit,
                    'updated_at' => now()
                ]);

                $details[] = [
                    'jurnal_id' => $jurnal->id,
                    'coa_akun' => $noAkun,
                    'debit' => $debit,
                    'credit' => $kredit,
                    'keterangan' => $input['keterangan'][$index] ?: $input['keterangan_header'],
                    'created_by' => Auth::user()->id,
                    'created_at' => now()
                ];
            }

            foreach ($details as $detail) {
                JurnalDetail::create($detail);
            }

            DB::commit();

            return redirect()->route('jurnal.index')->with('message', 'Jurnal berhasil diperbarui.')->with('color', 'green');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('jurnal.edit', $jurnal)->with('message', 'Gagal memperbarui jurnal: ' . $e->getMessage())->with('color', 'red');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurnal $jurnal)
    {
        //
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $path = $request->file('file')->store('temp');

        try {
            $import = new JurnalDetailImport;
            $data = Excel::toCollection($import, $path);

            $detail = [];
            foreach ($data[0] as $row) {
                if ($row['akun_coa'] !== null) {
                    $akun = explode('|', $row['akun_coa']);
                    $detail[] = [
                        'no_akun' => $akun[0],
                        'nama_akun' => $akun[1],
                        'debit' => $row['debit'],
                        'kredit' => $row['kredit'],
                        'keterangan' => $row['keterangan']
                    ];
                }
            }


            if ($detail) {
                return response()->json([
                    'success' => true,
                    'rows' => $detail
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to import data. Please check your file format.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sampleExport()
    {
        return Excel::download(new JurnalSampleExport(), 'jurnal_sample.xlsx');
    }
}
