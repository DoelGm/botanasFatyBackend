<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class packagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::all();
        return response()->json($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'type' => 'required|in:wholesale,promotion,special',
            'category_specific_id' => 'nullable|exists:categories,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'package_stock' => 'nullable|integer|min:0',
            'active' => 'boolean',
            'imgs' => 'nullable|array',
            'imgs.*' => 'nullable|url'
        ]);

        $package = Package::create($validatedData);
        return response()->json($package, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = Package::findOrFail($id);
        return response()->json($package);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $package = Package::findOrFail($id);

        $validatedData = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'name' => 'sometimes|required|string|max:100',
            'quantity' => 'sometimes|required|integer|min:1',
            'total_price' => 'sometimes|required|numeric|min:0',
            'type' => 'sometimes|required|in:wholesale,promotion,special',
            'category_specific_id' => 'nullable|exists:categories,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'package_stock' => 'nullable|integer|min:0',
            'active' => 'boolean',
            'imgs' => 'nullable|array',
            'imgs.*' => 'nullable|url'
        ]);

        $package->update($validatedData);
        return response()->json($package);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return response()->json(null, 204);
    }
}
