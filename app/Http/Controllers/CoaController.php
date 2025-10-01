<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Exports\CoaExport;
use App\Imports\CoaImport;
use App\Http\Requests\CoaRequest;
use App\Services\CoaService;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Alert;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CoaController extends Controller
{
    protected $coaService;

    public function __construct(CoaService $coaService)
    {
        $this->coaService = $coaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('coas.index');
    }

    public function filterCoaLevel(Request $request): JsonResponse
    {
        try {
            $filters = [
                'level' => $request->level,
                'search' => $request->search,
                'page' => $request->page,
            ];

            $result = $this->coaService->getFilteredCoa($filters);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error filtering COA by level: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memfilter data'], 500);
        }
    }

    public function filterCoa(Request $request): JsonResponse
    {
        try {
            $filters = [
                'kepala' => $request->kepala,
                'search' => $request->search,
                'page' => $request->page,
            ];

            $result = $this->coaService->getFilteredCoa($filters);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error filtering COA: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memfilter data'], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CoaRequest $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            $data = $this->coaService->prepareCOAData($request->validated());
            // dd($data);
            $coa = Coa::create($data);

            DB::commit();

            Alert::success('Sukses!', 'Berhasil membuat data');
            return redirect()->back();
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            Alert::error('Oops!', $e->getMessage());
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating COA: ' . $e->getMessage());
            Alert::error('Oops!', $e->getMessage());
            return redirect()->back();
        }
    }

    public function update(CoaRequest $request, Coa $coa)
    {
        try {
            DB::beginTransaction();

            $data = $this->coaService->prepareCOAData($request->validated());

            // Remove creation-specific fields for update
            unset($data['created_at'], $data['tgl_dibuat'], $data['created_by'], $data['periode']);
            $data['updated_at'] = now();
            $data['updated_by'] = Auth::id();

            $coa->update($data);

            DB::commit();

            Alert::success('Sukses!', 'Berhasil mengubah data');
            return redirect()->back();
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            Alert::error('Oops!', $e->getMessage());
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating COA: ' . $e->getMessage());
            Alert::error('Oops!', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $coa = Coa::findOrFail($id);

            // Check if account is used in journal entries
            $jurnal = JurnalDetail::where('coa_akun', $coa->nomor_akun)->first();
            if ($jurnal) {
                Alert::error('Oops!', 'Akun tidak dapat dihapus karena sudah digunakan');
                return redirect()->back();
            }

            DB::beginTransaction();

            $coa->update([
                'is_deleted' => 1,
                'deleted_at' => now(),
                'deleted_by' => Auth::id(),
            ]);

            $coa->delete();

            DB::commit();

            Alert::success('Sukses!', 'Berhasil menghapus data');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting COA: ' . $e->getMessage());
            Alert::error('Oops!', 'Gagal menghapus data');
            return redirect()->back();
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        try {
            DB::beginTransaction();

            $excel = Excel::toArray(new CoaImport, $request->file('file'))[0];

            usort($excel, function ($a, $b) {
                return strlen($a['kode_akun']) <=> strlen($b['kode_akun']);
            });

            $data = [];
            $existingAccounts = [];
            $duplicatesInExcel = [];
            $periode = auth()->user()->periode;

            foreach ($excel as $value) {
                $cleanAccountNumber = str_replace('-', '', $value['kode_akun']);

                // Check for duplicates
                $existingCoa = Coa::where('nomor_akun', $cleanAccountNumber)
                    ->where('created_by', Auth::id())
                    ->first();

                if ($existingCoa || in_array($cleanAccountNumber, $existingAccounts)) {
                    $duplicatesInExcel[] = $cleanAccountNumber;
                    continue;
                }

                try {
                    $coaData = $this->coaService->prepareCOAData([
                        'nomor_akun' => $value['kode_akun'],
                        'nama_akun' => $value['nama_akun']
                    ]);

                    $data[] = $coaData;
                    $existingAccounts[] = $cleanAccountNumber;
                } catch (\InvalidArgumentException $e) {
                    Alert::error('Oops!', $e->getMessage());
                    return redirect()->back();
                }
            }

            if (!empty($duplicatesInExcel)) {
                Alert::error('Oops!', 'Nomor akun berikut sudah ada: ' . implode(', ', $duplicatesInExcel));
                return redirect()->back();
            }

            if (!empty($data)) {
                Coa::insert($data);
                $this->updateParentRelationships();

                DB::commit();
                Alert::success('Sukses!', 'Data COA berhasil diimport');
            } else {
                Alert::warning('Oops!', 'Tidak ada data baru yang diimport');
            }

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing COA: ' . $e->getMessage());
            Alert::error('Oops!', 'Data COA gagal diimport: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update parent relationships after bulk insert
     */
    private function updateParentRelationships(): void
    {
        $updateCoa = Coa::where('created_by', Auth::id())->get();

        foreach ($updateCoa as $coa) {
            $parentAccountNumber = $coa->level == 5
                ? substr($coa->nomor_akun, 0, $coa->level)
                : substr($coa->nomor_akun, 0, $coa->level - 1);

            $parentId = $coa->level > 1
                ? Coa::where('nomor_akun', $parentAccountNumber)
                    ->where('created_by', Auth::id())
                    ->value('id')
                : null;

            $coa->update([
                'parent_id' => $parentId,
                'subchild' => Coa::where('parent_id', $coa->id)
                    ->where('created_by', Auth::id())
                    ->count()
            ]);
        }
    }

    public function printCoa()
    {
        try {
            $data = $this->getCoaTreeData();
            $pdf = PDF::loadView('report.printcoa', ['data' => $data, 'bType' => 'download']);
            return $pdf->download('coa_' . auth()->user()->name . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error printing COA: ' . $e->getMessage());
            Alert::error('Oops!', 'Gagal mencetak data COA');
            return redirect()->back();
        }
    }

    public function previewCoa()
    {
        try {
            $data = $this->getCoaTreeData();
            return view('report.printcoa', ['data' => $data, 'bType' => 'preview']);
        } catch (\Exception $e) {
            Log::error('Error previewing COA: ' . $e->getMessage());
            Alert::error('Oops!', 'Gagal menampilkan preview COA');
            return redirect()->back();
        }
    }

    /**
     * Get COA data organized in tree structure
     */
    private function getCoaTreeData(): array
    {
        $data = Coa::where('created_by', Auth::id())
            ->where(function ($query) {
                $query->where('is_deleted', 0)
                    ->orWhereNull('is_deleted');
            })
            ->orderBy('nomor_akun')
            ->get();

        $coaTree = $this->buildTree($data);
        $flatArray = [];
        $this->flattenTree($coaTree, $flatArray);

        return $flatArray;
    }



    /**
     * Build hierarchical tree structure from flat data
     */
    private function buildTree($elements, $parentId = 0): array
    {
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

    /**
     * Flatten tree structure for display
     */
    private function flattenTree($tree, &$flatArray, $level = 1): void
    {
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
        try {
            return Excel::download(new CoaExport(), 'coas.xlsx');
        } catch (\Exception $e) {
            Log::error('Error exporting COA: ' . $e->getMessage());
            Alert::error('Oops!', 'Gagal mengekspor data COA');
            return redirect()->back();
        }
    }
}
