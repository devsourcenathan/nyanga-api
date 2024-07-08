<?php
// app/Http/Controllers/ServiceController.php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;


class ServiceController extends Controller
{
    public function index()
    {
        return Service::all();
    }

    /**
     * Store a new service.
     *
     * @bodyParam name string required The name of the service.
     * @bodyParam description string The description of the service.
     *
     * @response {
     *   "message": "Service created successfully.",
     *   "service": {
     *     "id": 1,
     *     "name": "Service Name",
     *     "description": "Service Description",
     *     "created_at": "2022-01-01T00:00:00.000000Z",
     *     "updated_at": "2022-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 500 {
     *   "message": "Error creating service."
     * }
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required',
            'description' => 'nullable',
            'time' => 'required|integer',
            'amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            // 'images' => 'nullable|array',
            'category_id' => 'required|exists:service_categories,id',
            'user_id' => 'required|exists:users,id',
        ]);

        // $uploadedImages = [];
        // if ($request->hasFile('images')) {
        //     $uploadedFile = $request->file('images');
        //     $path = $uploadedFile->store('public/images'); // Stocke l'image dans storage/app/public/images
        //     $uploadedImages[] = Storage::url($path); // Stocke le chemin d'accès public de l'image
        // }


        $service = Service::create([
            'label' => $request->input('label'),
            'description' => $request->input('description'),
            'time' => $request->input('time'),
            'amount' => $request->input('amount'),
            'discount' => $request->input('discount'),
            // 'images' => $uploadedImages,
            'category_id' => $request->input('category_id'),
            'user_id' => $request->input('user_id'),
        ]);
        return response()->json($service, Response::HTTP_CREATED);
    }

    public function show(Service $service)
    {
        return $service;
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'label' => 'required',
            'description' => 'nullable',
            'time' => 'required|integer',
            'amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'images' => 'nullable|array',
            'category_id' => 'required|exists:service_categories,id',
            'user_id' => 'required|exists:users,id',
        ]);


        $service->update($request->all());

        return response()->json($service, Response::HTTP_OK);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
