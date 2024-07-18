<?php
// app/Http/Controllers/Nyanga/ProductCategoryController.php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    public function index()
    {
        return ProductCategory::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|unique:countries',
            'code' => 'required|unique:countries',
        ]);

        return ProductCategory::create($request->all());
    }

    public function show(ProductCategory $ProductCategory)
    {
        return $ProductCategory;
    }

    public function update(Request $request, ProductCategory $ProductCategory)
    {
        $request->validate([
            'label' => 'required|unique:countries,label,' . $ProductCategory->id,
            'code' => 'required|unique:countries,code,' . $ProductCategory->id,
        ]);

        $ProductCategory->update($request->all());

        return $ProductCategory;
    }

    public function destroy(ProductCategory $ProductCategory)
    {
        $ProductCategory->delete();

        return response()->json(null, 204);
    }
}
