<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('coas.index');
    }

    public function list()
    {
        $query = Coa::whereNull('is_deleted')->get();
    
        if ($query->count() == 0) {
            return response()->json(null);
        }
    
        $no = 0;
        $result = ['data' => []];
        foreach ($query as $key => $value) {
            $no++;
            $result['data'][] = [
                $no,
                $value->nomor_akun,
                $value->nama_akun,
                $value->level,
                $value->saldo_normal,
                '<button style="background-color: #34d49c;" class="text-white py-1 px-2 rounded update" style="border-radius: 12px;" data-id="' . $value->id . '">Update</button>
                <button style="background-color: #f87171;" class="text-white py-1 px-2 rounded delete" style="border-radius: 12px;" data-id="' . $value->id . '">Delete</button>'
            ];
        }
        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Coa $coa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coa $coa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coa $coa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coa $coa)
    {
        //
    }
}
