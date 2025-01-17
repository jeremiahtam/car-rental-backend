<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use Illuminate\Http\Request;

class CarsController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $fields = $request->validate([
      'page' => 'nullable|integer',
      'search' => 'nullable|string',
      'itemsPerPage' => 'required|integer',
    ]);

    $allCarsQuery = Cars::with([
      'car_images' => function ($query) {
        $query->select('id', 'car_id', 'image_name')->where('removed', 0);
      }
    ])->when($fields['search'] !== "", function ($query) use ($fields) {
      return $query->where('brand', 'like', "%" . $fields['search'] . "%");
    })->where('removed', 0)->orderBy('created_at', 'desc')->paginate($fields['itemsPerPage'], ['*'], 'page', $fields['page']);

    $division = $allCarsQuery->total() / $fields['itemsPerPage'];
    $totalPages = ceil($division);
    if ($division > $totalPages) {
      $totalPages = $totalPages++;
    }

    //Setup Serial Number
    $endNumber = $fields['itemsPerPage'] * $fields['page'];
    $startFrom = $endNumber - $fields['itemsPerPage'] + 1;

    $allCars = [];
    foreach ($allCarsQuery as $car) {
      /**  */
      $images = [];
      foreach ($car->carImages as $image) {
        $images[] = $image['image_name'];
      }

      $allCars[] = [
        'sn' => $startFrom++,
        'id' => $car['id'],
        'images' => $images,
        'brand' => $car['brand'],
        'model' => $car['model'],
        'slug' => $car['slug'],
        'costPerMeter' => $car['cost_per_meter'],
        'waitAmountPerHour' => $car['wait_amount_per_hour'],
        'airCondition' => $car['aircondition'],
        'gearType' => $car['gear_type'],
        'fuelType' => $car['fuel_type'],
        'seats' => $car['seats'],
        'airbags' => $car['airbags'],
        'bookedDates' => [],
        'date' => $car['created_at']->format('M j, Y'),
        'time' => $car['created_at']->format('h:i A'),
      ];
    }

    if (!$allCars) {
      return response([
        'success' => false,
        'message' => 'Could not find cars',
        'data' => $allCars
      ], 400);
    }
    return response([
      'success' => true,
      'message' => 'All cars successfully retrieved',
      'data' => $allCars,
      'pageInfo' => [
        'currentPage' => $allCarsQuery->currentPage(),
        'totalPages' => $totalPages,
      ]
    ], 201);

  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(request $request)
  {
    $fields = $request->validate([
      'brand' => 'required|string',
      'model' => 'required|string',
      'slug' => 'required|string',
      'aircondition' => 'boolean',
      'gear_type' => 'required|string',
      'fuel_type' => 'required|string',
      'seats' => 'required|integer',
      'cost_per_meter' => 'required|numeric',
      'wait_amount_per_hour' => 'required|numeric',
      'images' => 'array|max:3'
    ]);

    $createCar = Cars::create([
      'brand' => $fields['brand'],
      'model' => $fields['model'],
      'slug' => $fields['slug'],
      'aircondition' => $fields['aircondition'],
      'gear_type' => $fields['gearType'],
      'fuel_type' => $fields['fuelType'],
      'seats' => $fields['seats'],
      'cost_per_meter' => $fields['costPerMeter'],
      'wait_amount_per_hour' => $fields['waitAmountPerHour'],
      'removed' => 0,
    ]);
    $carId = $createCar->id;

    $carImagesController = new CarImagesController();

    foreach ($fields['images'] as $image) {
      $carImagesController->store(new Request([
        'carId' => $carId,
        'imageName' => $image['imageName'],
      ]));
    }

    if (!$createCar) {
      return response([
        'success' => false,
        'message' => 'Could not create car',
      ], 400);
    }
    return response([
      'success' => true,
      'message' => 'Successfully created a car',
      'data' => $createCar,
    ], 201);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update($id, Request $request)
  {
    $fields = $request->validate([
      'brand' => 'required|string',
      'model' => 'required|string',
      'slug' => 'required|string',
      'aircondition' => 'boolean',
      'gear_type' => 'required|string',
      'fuel_type' => 'required|string',
      'seats' => 'required|integer',
      'cost_per_meter' => 'required|numeric',
      'wait_amount_per_hour' => 'required|numeric',
      'images' => 'array|max:3'
    ]);

    $editCarQuery = Cars::where('id', $id)->where('removed', 0)
      ->update([
        'brand' => $fields['brand'],
        'model' => $fields['model'],
        'slug' => $fields['slug'],
        'aircondition' => $fields['aircondition'],
        'gear_type' => $fields['gearType'],
        'fuel_type' => $fields['fuelType'],
        'seats' => $fields['seats'],
        'cost_per_meter' => $fields['costPerMeter'],
        'wait_amount_per_hour' => $fields['waitAmountPerHour'],
      ]);

    /** Delete all of this images */
    $carImagesController = new CarImagesController();
    $carImagesController->delete($id);

    $carImagesController = new CarImagesController();

    foreach ($fields['images'] as $image) {
      $carImagesController->store(new Request([
        'carId' => $id,
        'imageName' => $image['imageName'],
      ]));
    }

    if (!$editCarQuery) {
      return response([
        'success' => false,
        'message' => 'Could not edit car',
      ], 400);
    }

    return response([
      'success' => true,
      'message' => 'Successfully edited',
      'data' => $editCarQuery,
    ], 201);
  }

  public function delete($carId)
  {
    $deleteImage = Cars::where('id', $carId)
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
}
