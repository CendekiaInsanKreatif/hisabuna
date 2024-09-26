<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Exports\CoaExport;
use App\Imports\CoaImport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Alert;


use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('coas.index');
    }


    public function filterCoa(Request $request)
    {
        $users      = auth()->user()->id;
        $kepala     = $request->kepala;
        $page       = $request->page ?? 1; // Halaman default ke 1
        $perPage    = 7;

        $offset     = ($page - 1) * $perPage;
        $query = DB::select("
        SELECT
            id,
            nama_akun,
            level,
            saldo_normal,
            CASE 
            WHEN level = 4 THEN 
                CONCAT(SUBSTRING(nomor_akun, 1, LENGTH(nomor_akun) - 2), '-', SUBSTRING(nomor_akun, LENGTH(nomor_akun) - 1))
            WHEN level = 5 THEN 
                 CONCAT(SUBSTRING(nomor_akun, 1, 3), '-', 
                   SUBSTRING(nomor_akun, 4, 2), '-', 
                   SUBSTRING(nomor_akun, 6, 3))
            ELSE 
                nomor_akun 
        END AS nomor_akun
        FROM coas
        WHERE
            is_deleted IS NULL AND
            nomor_akun LIKE ? AND
            created_by = ?
        LIMIT ?
        OFFSET ?
    ", [$kepala . '%', $users, $perPage, $offset]);
            $total = DB::table('coas')
                ->where('is_deleted', null)
                ->where('nomor_akun', 'LIKE', $kepala . '%')
                ->where('created_by', $users)
                ->count();
             
        return response()->json([
            'data'          => $query,
            'total'         => $total,
            'current_page'  => (int) $page,
            'last_page'     => ceil($total / $perPage), // Hitung total halaman
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nomor_akun'    => 'required|regex:/^[0-9\-]+$/',
            'nama_akun'     => 'required|string',
        ]);
        
        $check = Coa::where('nomor_akun', $request->nomor_akun)
                ->where('created_by', Auth::user()->id)
                ->first();

        if($validator->fails()){
            Alert::error('Oops!', 'Validasi Error');
            return redirect()->back();
        }

        if($check){
            Alert::error('Oops!', 'Nomor Akun sudah ada');
            return redirect()->back();
        }

        $nomor_akun     = $request->nomor_akun;
        $nomor_akun_tanpa_tanda_hubung = str_replace('-', '', $nomor_akun);
        $real_akun = $nomor_akun_tanpa_tanda_hubung;
        // Menentukan level berdasarkan panjang nomor akun
        $jumlah_digit_nomor_akun = strlen($nomor_akun_tanpa_tanda_hubung);
        $level = 0;
        
        // Menentukan level berdasarkan jumlah digit nomor akun
        if ($jumlah_digit_nomor_akun == 1) {
            $level = 1;
        } elseif ($jumlah_digit_nomor_akun == 2) {
            $level = 2;
        } elseif ($jumlah_digit_nomor_akun == 3) {
            $level = 3;
        } elseif ($jumlah_digit_nomor_akun == 5) {
            $level = 4;
        } elseif ($jumlah_digit_nomor_akun == 8) {
            $level = 5;
        } else {
            Alert::error('Oops!', 'Format nomor akun tidak valid');
            return redirect()->back();
        }
        
        // Menentukan parent_id berdasarkan level
        $parent_id = null;
        if ($level == 2) {
            $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 1); // Parent Level 1
        } elseif ($level == 3) {
            $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 2); // Parent Level 2
        } elseif ($level == 4) {
            $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 3); // Parent Level 3
        } elseif ($level == 5) {
            $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 5); // Parent Level 4
        }

        $nama_akun      = $request->nama_akun;
        // Menentukan saldo_normal, golongan otomatis
        $awal = substr($nomor_akun_tanpa_tanda_hubung, 0, 1);
        switch ($awal) {
            case '1':
                $saldo_normal = 'debit';
                $golongan = 'Aset';
                break;
            case '2':
                $saldo_normal = 'credit';
                $golongan = 'Liabilitas';
                break;
            case '3':
                $saldo_normal = 'credit';
                $golongan = 'Ekuitas';
                break;
            case '4':
                $saldo_normal = 'credit';
                $golongan = 'Pendapatan';
                break;
            case '5':
                $saldo_normal = 'debit';
                $golongan = 'Beban';
                break;
            case '6':
                $saldo_normal = 'debit';
                $golongan = 'Beban Umum';
                break;
            case '7':
                $saldo_normal = 'credit';
                $golongan = 'Pendapatan Lainnya';
                break;
            case '8':
                $saldo_normal = 'debit';
                $golongan = 'Beban Lainnya';
                break;
            default:
                $saldo_normal = 'credit';
                $golongan = 'Beban Umum'; // Nilai default untuk golongan
                break;
        }
        
        // Verifikasi keberadaan parent akun
        if ($level > 1 && $parent_id) {
            $parent_level = $level - 1;
            $selCoa = Coa::where('nomor_akun', $parent_id)
                        ->where('level', $parent_level)
                        ->where('created_by', Auth::user()->id)
                        ->first();

            if (empty($selCoa)) {
                Alert::error('Oops!', 'Akun Level ' . $parent_level . ' tidak ditemukan untuk parent dengan nomor akun ' . $parent_id);
                return redirect()->back();
            }
        }

        $data = [
            'parent_id'    => $selCoa->id ?? null,
            'subchild'     => ($selCoa->subchild ?? 0) + 1,
            'nomor_akun'   => $real_akun,
            'nama_akun'    => $nama_akun,
            'level'        => $level,
            'saldo_normal' => $saldo_normal,
            'golongan'     => $golongan,
            'arus_kas'     => 'aktifitas_operasional',
            'saldo_awal_debit' => 0,
            'saldo_awal_credit' => 0,
            'saldo_berjalan_debit' => 0,
            'saldo_berjalan_credit' => 0,
            'created_at'   => now(),
            'created_by'   => Auth::user()->id,
        ];

        $save = Coa::create($data);
        if($save) {
            Alert::success('Sukses !', 'Berhasil membuat data');
            return redirect()->back();
        } else {
            Alert::error('Oops!', 'Gagal membuat data');
            return redirect()->back();
        }
    }

    // Metode untuk menentukan saldo_normal
    private function determineSaldoNormal($nomor_akun)
    {
        // Menentukan saldo_normal berdasarkan awalan nomor akun
        $awal = substr($nomor_akun, 0, 1);
        
        switch ($awal) {
            case '1':
            case '5':
                return 'debit';
            case '2':
            case '3':
            case '4':
                return 'credit';
            default:
                return 'credit'; // Nilai default untuk saldo_normal
        }
    }

    public function update(Request $request, Coa $coa)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'nomor_akun' => 'required|regex:/^[0-9\-]+$/',
            'nama_akun'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 400,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 400);
        }

        $nomor_akun = $coa->nomor_akun = $input['nomor_akun'];
        $nomor_akun_tanpa_tanda_hubung = str_replace('-', '', $nomor_akun);
        $real_akun = $nomor_akun_tanpa_tanda_hubung;
        $jumlah_digit_nomor_akun = strlen($nomor_akun_tanpa_tanda_hubung);
        $level = 0;

       // Menentukan level berdasarkan jumlah digit nomor akun
       if ($jumlah_digit_nomor_akun == 1) {
        $level = 1;
        } elseif ($jumlah_digit_nomor_akun == 2) {
            $level = 2;
        } elseif ($jumlah_digit_nomor_akun == 3) {
            $level = 3;
        } elseif ($jumlah_digit_nomor_akun == 5) {
            $level = 4;
        } elseif ($jumlah_digit_nomor_akun == 8) {
            $level = 5;
        } else {
            Alert::error('Oops!', 'Format nomor akun tidak valid');
            return redirect()->back();
        }

        // Menentukan parent_id berdasarkan level
        $parent_id = null;
        if ($level == 2) {
            $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 1); // Parent Level 1
        } elseif ($level == 3) {
            $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 2); // Parent Level 2
        } elseif ($level == 4) {
            $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 3); // Parent Level 3
        } elseif ($level == 5) {
            $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 5); // Parent Level 4
        }

        $nama_akun = $input['nama_akun'];

        // Menentukan saldo_normal, golongan otomatis
        $awal = substr($nomor_akun_tanpa_tanda_hubung, 0, 1);
        switch ($awal) {
            case '1':
                $saldo_normal = 'debit';
                $golongan = 'Aset';
                break;
            case '2':
                $saldo_normal = 'credit';
                $golongan = 'Liabilitas';
                break;
            case '3':
                $saldo_normal = 'credit';
                $golongan = 'Ekuitas';
                break;
            case '4':
                $saldo_normal = 'credit';
                $golongan = 'Pendapatan';
                break;
            case '5':
                $saldo_normal = 'debit';
                $golongan = 'Beban';
                break;
            case '6':
                $saldo_normal = 'debit';
                $golongan = 'Beban Umum';
                break;
            case '7':
                $saldo_normal = 'credit';
                $golongan = 'Pendapatan Lainnya';
                break;
            case '8':
                $saldo_normal = 'debit';
                $golongan = 'Beban Lainnya';
                break;
            default:
                $saldo_normal = 'credit';
                $golongan = 'Beban Umum'; // Nilai default untuk golongan
                break;
        }


        $selCoa = Coa::where('nomor_akun', $parent_id)
                      ->where('created_by', Auth::user()
                      ->id)->first();
        $data = [
            'parent_id'    => $selCoa->id ?? null,
            'nomor_akun'   => $real_akun,
            'nama_akun'    => $nama_akun,
            'level'        => $level,
            'golongan'     => $golongan,
            'saldo_normal' => $saldo_normal,
            'updated_at'   => now(),
            'updated_by'   => Auth::user()->id,
        ];

        $update = $coa->update($data);
        if ($update) {
            Alert::success('Sukses !', 'Berhasil mengubah data');
            return redirect()->back();
        } else {
            Alert::error('Oops!', 'Gagal mengubah data');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $coa = Coa::find($id);
        if (!$coa) {
            Alert::error('Oops!', 'Data tidak ditemukan');
            return redirect()->back();
        }

        $jurnal = JurnalDetail::where('coa_akun', $coa->nomor_akun)->first();
        if ($jurnal) {
            Alert::error('Oops!', 'Akun tidak dapat dihapus karena sudah digunakan');
            return redirect()->back();
        }

        $data = [
            'is_deleted'    => 1,
            'deleted_at'    => now(),
            'deleted_by'    => Auth::user()->id,
        ];

        $update = $coa->update($data);
        $coa->delete();

        if ($update) {
            Alert::success('Sukses !', 'Berhasil menghapus data');
            return redirect()->back();
        } else {
            Alert::error('Oops!', 'Gagal menghapus data');
            return redirect()->back();
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        $path = $request->file('file')->getRealPath();
        $excel = Excel::toArray(new CoaImport, $request->file('file'))[0];

        // da($excel);
        usort($excel, function ($a, $b) {
            return strlen($a['kode_akun']) <=> strlen($b['kode_akun']);
        });

        // dd($excel);
        $data = []; // Initialize the data array
        $existingAccounts = []; // Array to store existing accounts
        $duplicatesInExcel = []; // Array to store duplicate accounts in the Excel file

        foreach ($excel as $key => $value) {
            $nomor_akun     = $value['kode_akun'];
            $nomor_akun_tanpa_tanda_hubung = str_replace('-', '', $nomor_akun);
            $real_akun = $nomor_akun_tanpa_tanda_hubung;
            $jumlah_nomor_akun = strlen($nomor_akun_tanpa_tanda_hubung);
            $level = 0;

            // da($value);
            if ($jumlah_nomor_akun == 1) {
                $level = 1;
            } elseif ($jumlah_nomor_akun == 2) {
                $level = 2;
            } elseif ($jumlah_nomor_akun == 3) {
                $level = 3;
            } elseif ($jumlah_nomor_akun == 5) {
                $level = 4;
            } elseif ($jumlah_nomor_akun == 8) {
                $level = 5;
            } else {
                // da($value);
                Alert::error('Oops!', 'Format nomor akun tidak valid');
                return redirect()->back();
            }

            // Menentukan parent_id berdasarkan level
            // da($level);
            $parent_id = null;
            if ($level == 2) {
                $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 1); // Parent Level 1
            } elseif ($level == 3) {
                $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 2); // Parent Level 2
            } elseif ($level == 4) {
                $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 3); // Parent Level 3
            } elseif ($level == 5) {
                $parent_id = substr($nomor_akun_tanpa_tanda_hubung, 0, 5); // Parent Level 4
            }

            $nama_akun      = $value['nama_akun'];

            // Menentukan saldo_normal, golongan otomatis
            $awal = substr($nomor_akun_tanpa_tanda_hubung, 0, 1);
            switch ($awal) {
                case '1':
                    $saldo_normal = 'debit';
                    $golongan = 'Aset';
                    break;
                case '2':
                    $saldo_normal = 'credit';
                    $golongan = 'Liabilitas';
                    break;
                case '3':
                    $saldo_normal = 'credit';
                    $golongan = 'Ekuitas';
                    break;
                case '4':
                    $saldo_normal = 'credit';
                    $golongan = 'Pendapatan';
                    break;
                case '5':
                    $saldo_normal = 'debit';
                    $golongan = 'Beban';
                    break;
                case '6':
                    $saldo_normal = 'debit';
                    $golongan = 'Beban Umum';
                    break;
                case '7':
                    $saldo_normal = 'credit';
                    $golongan = 'Pendapatan Lainnya';
                    break;
                case '8':
                    $saldo_normal = 'debit';
                    $golongan = 'Beban Lainnya';
                    break;
                default:
                    $saldo_normal = 'credit';
                    $golongan = 'Beban Umum'; // Nilai default untuk golongan
                    break;
            }

            // da($parent_id);

            $selCoa = Coa::where('nomor_akun', $parent_id)
                        ->where('created_by', Auth::user()->id)
                        ->first();

            // Cek apakah data sudah ada
            $existingCoa = Coa::where('nomor_akun', $real_akun)
                              ->where('created_by', Auth::user()->id)
                              ->first();


            if ($existingCoa || in_array($real_akun, $existingAccounts)) {
                $duplicatesInExcel[] = $real_akun; // Simpan nomor akun duplikat
            } else {
                $existingAccounts[] = $real_akun; // Tambahkan ke daftar akun yang sudah ada
    
                $data[] = [
                    'parent_id'    => $selCoa->id ?? null,
                    'subchild'     => ($selCoa->subchild ?? 0) + 1,
                    'nomor_akun'   => $real_akun,
                    'nama_akun'    => $nama_akun,
                    'level'        => $level,
                    'saldo_normal' => $saldo_normal,
                    'golongan'     => $golongan,
                    'arus_kas'     => 'aktifitas_operasional',
                    'saldo_awal_debit' => 0,
                    'saldo_awal_credit' => 0,
                    'saldo_berjalan_debit' => 0,
                    'saldo_berjalan_credit' => 0,
                    'created_at'   => now(),
                    'created_by'   => Auth::user()->id,
                ];
            }
        }
    
        if (!empty($duplicatesInExcel)) {
            Alert::error('Oops!', 'Nomor akun berikut sudah ada: ' . implode(', ', $duplicatesInExcel));
            return redirect()->back();
        }
        if (!empty($data)) {
            $xa = Coa::insert($data);
            if ($xa) {
                $updateCoa = Coa::where('created_by', Auth::user()->id)->get();
                foreach ($updateCoa as $coa) {
                    if($coa->level == 5){
                        $pr = substr($coa->nomor_akun, 0, $coa->level);
                    }else{
                        $pr = substr($coa->nomor_akun, 0, $coa->level - 1);
                    }

                    $parent_id = $coa->level > 1 ? Coa::where('nomor_akun', $pr)
                    ->where('created_by', Auth::user()->id)
                    ->value('id') : null;

                    $coa->update([
                        'parent_id' => $parent_id,
                        'subchild' => Coa::where('parent_id', $coa->id)->where('created_by', Auth::user()->id)->count()
                    ]);
                }
                Alert::success('Sukses !', 'Data Coa berhasil di import');
                return redirect()->back();
            } else {
                Alert::error('Oops!', 'Data Coa gagal di import');
            }
        } else {
            Alert::warning('Oops!', 'Tidak ada data baru yang diimport');
            return redirect()->back();
        }
    }

    public function printCoa(){
        $data = Coa::where('created_by', Auth::user()->id)
                    ->where(function($query) {
                        $query->where('is_deleted', 0)
                            ->orWhereNull('is_deleted');
                    })
                    ->orderBy('nomor_akun')
                    ->get();


        $coaTree = $this->buildTree($data);
        $flatArray = [];
        $this->flattenTree($coaTree, $flatArray);

        // dd($flatArray);
        $pdf = PDF::loadView('report.printcoa', ['data' => $flatArray, 'bType' => 'download']);
        return $pdf->download('coa_'.auth()->user()->name.'.pdf');
    }

    public function previewCoa(){
        $data = Coa::where('created_by', Auth::user()->id)
                    ->where(function($query) {
                        $query->where('is_deleted', 0)
                            ->orWhereNull('is_deleted');
                    })
                    ->orderBy('nomor_akun')
                    ->get();



        $coaTree = $this->buildTree($data);
        // da($coaTree);
        $flatArray = [];
        $this->flattenTree($coaTree, $flatArray);

        // da($flatArray);
        return view('report.printcoa', ['data' => $flatArray, 'bType' => 'preview']);
    }

    

    private function buildTree($elements, $parentId = 0) {
        $branch = [];
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    private function flattenTree($tree, &$flatArray, $level = 1) {
        foreach ($tree as $node) {
            $node->level = $level;
            $flatArray[] = $node;
            if (isset($node->children)) {
                $this->flattenTree($node->children, $flatArray, $level + 1);
            }
        }
    } 

    public function export()
    {
        return Excel::download(new CoaExport(), 'coas.xlsx');
    }
}
