<?php

namespace App\Http\Controllers;

use App\Models\Drivers;
use Illuminate\Http\Request;

class DriversController extends Controller
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

    $allDriversQuery = Drivers::when($fields['search'] !== "", function ($query) use ($fields) {
      return $query->where('name', 'like', "%" . $fields['search'] . "%");
    })->where('removed', 0)->orderBy('created_at', 'desc')->paginate($fields['itemsPerPage'], ['*'], 'page', $fields['page']);

    $division = $allDriversQuery->total() / $fields['itemsPerPage'];
    $totalPages = ceil($division);
    if ($division > $totalPages) {
      $totalPages = $totalPages++;
    }

    //Setup Serial Number
    $endNumber = $fields['itemsPerPage'] * $fields['page'];
    $startFrom = $endNumber - $fields['itemsPerPage'] + 1;

    $allDrivers = [];
    foreach ($allDriversQuery as $driver) {
      $allDrivers[] = [
        'sn' => $startFrom++,
        'id' => $driver['id'],
        'name' => $driver['name'],
        'nin' => $driver['nin'],
        'status' => $driver['status'],
        'date' => $driver['created_at']->format('M j, Y'),
        'time' => $driver['created_at']->format('h:i A'),
      ];
    }

    if (!$allDrivers) {
      return response([
        'success' => false,
        'message' => 'Could not find drivers',
        'data' => $allDrivers
      ], 400);
    }
    return response([
      'success' => true,
      'message' => 'All drivers successfully retrieved',
      'data' => $allDrivers,
      'pageInfo' => [
        'currentPage' => $allDriversQuery->currentPage(),
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
      'name' => 'required|string',
      'nin' => 'required|string',
      'status' => 'required|string',
    ]);

    $createDriver = Drivers::create([
      'name' => $fields['name'],
      'nin' => $fields['nin'],
      'status' => $fields['status'],
      'removed' => 0,
    ]);

    if (!$createDriver) {
      return response([
        'success' => false,
        'message' => 'Could not create driver',
      ], 400);
    }
    return response([
      'success' => true,
      'message' => 'Successfully created a driver',
      'data' => $createDriver,
    ], 201);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update($id, Request $request)
  {
    $fields = $request->validate([
      'name' => 'required|string',
      'nin' => 'required|string',
      'status' => 'required|string',
    ]);

    $editDriverQuery = Drivers::where('id', $id)->where('removed', 0)
      ->update([
        'name' => $fields['name'],
        'nin' => $fields['nin'],
        'status' => $fields['status'],
      ]);

    if (!$editDriverQuery) {
      return response([
        'success' => false,
        'message' => 'Could not edit driver',
      ], 400);
    }

    return response([
      'success' => true,
      'message' => 'Successfully edited',
      'data' => $editDriverQuery,
    ], 201);
  }

  public function delete($driverId)
  {
    $deleteImage = Drivers::where('id', $driverId)
      ->where('removed', 0)
      ->update([
        'removed' => 1,
      ]);
    if (!$deleteImage) {
      return response([
        'success' => false,
        'message' => 'Could not delete driver image',
      ], 400);
    }
    return response([
      'success' => true,
      'message' => 'Successfully deleted driver image',
      'data' => $deleteImage,
    ], 201);
  }
}
