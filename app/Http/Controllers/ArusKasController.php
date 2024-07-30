<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coa;

class ArusKasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('arus-kas.index');
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
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // Validate the input fields
        $request->validate([
            'value' => 'required',
        ]);

        return response()->json($request->all());

        // Update the Coa model where id matches
        // Coa::where('id', $id)->update([
        //     'aruskas' => $request->input('value'),
        // ]);

        // Return a JSON response
        // return response()->json(['message' => 'Berhasil Update Aruskas'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
