<?php

namespace App\Http\Controllers;

use App\Models\CarImages;
use Illuminate\Http\Request;

class CarImagesController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(request $request)
  {


  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $fields = $request->validate([
      'carId' => 'required|integer|numeric',
      'imageName' => 'required|integer|numeric',
    ]);
    $createCarImageQuery = CarImages::create([
      'car_id' => $fields['carId'],
      'image_name' => $fields['imageName'],
    ]);
    if (!$createCarImageQuery) {
      return response([
        'success' => false,
        'message' => 'Could not create car image',
      ], 400);
    }
    return response([
      'success' => true,
      'message' => 'Successfully created car image',
      'data' => $createCarImageQuery,
    ], 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(CarImages $carImages)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(CarImages $carImages)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, CarImages $carImages)
  {
    //
  }

  public function delete($carId)
  {
    $deleteImage = CarImages::where('car_id', $carId)
      ->where('removed', 0)
      ->update([
        'removed' => 1,
      ]);
    if (!$deleteImage) {
      return response([
        'success' => false,
        'message' => 'Could not delete car image',
      ], 400);
    }
    return response([
      'success' => true,
      'message' => 'Successfully deleted car image',
      'data' => $deleteImage,
    ], 201);

  }
  /**
   * Remove the specified resource from storage.
   */
  public function destroy(CarImages $carImages)
  {
    //
  }
}
