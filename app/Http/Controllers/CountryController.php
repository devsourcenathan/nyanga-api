<?php
// app/Http/Controllers/Nyanga/CountryController.php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    public function index()
    {
        return Country::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|unique:countries',
            'code' => 'required|unique:countries',
        ]);

        return Country::create($request->all());
    }

    public function show(Country $country)
    {
        return $country;
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'label' => 'required|unique:countries,label,' . $country->id,
            'code' => 'required|unique:countries,code,' . $country->id,
        ]);

        $country->update($request->all());

        return $country;
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return response()->json(null, 204);
    }
}
