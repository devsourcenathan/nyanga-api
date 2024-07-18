<?php
// app/Http/Controllers/TownController.php

namespace App\Http\Controllers;

use App\Models\Town;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TownController extends Controller
{
    public function index()
    {
        return Town::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|unique:towns|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $town = Town::create($request->all());

        return response()->json($town, 201);
    }

    public function show(Town $town)
    {
        return $town;
    }

    public function update(Request $request, Town $town)
    {
        $request->validate([
            'label' => 'required|string|unique:towns,label,' . $town->id . '|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $town->update($request->all());

        return response()->json($town);
    }

    public function destroy(Town $town)
    {
        $town->delete();

        return response()->json(null, 204);
    }
}
