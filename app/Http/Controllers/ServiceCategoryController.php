<?php
// app/Http/Controllers/ServiceCategoryController.php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        return ServiceCategory::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|unique:service_categories|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = ServiceCategory::create($request->all());

        return response()->json($category, 201);
    }

    public function show(ServiceCategory $category)
    {
        return $category;
    }

    public function update(Request $request, ServiceCategory $category)
    {
        $request->validate([
            'label' => 'required|string|unique:service_categories,label,' . $category->id . '|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($request->all());

        return response()->json($category);
    }

    public function destroy(ServiceCategory $category)
    {
        $category->delete();

        return response()->json(null, 204);
    }
}
