<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Services\LocationServices\LocationService;

class LocationController extends Controller


{
    private Location $locationModel;
    private LocationService $locationService;

    public function __construct(LocationService $locationService, Location $locationModel)
    {
        $this->locationModel = $locationModel;
        $this->locationService = $locationService;
    }
    public function index()
    {
        return $this->locationModel::paginate(10);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {

        $location = $this->locationService->createLocation(
            $request->validated(),
            $this->locationModel,
        );

        return response($location);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $location = $this->locationService->getLocationById($id, $this->locationModel);
        if (!$location) {
            return response([
                "message" => "Not Found",
            ], 404);
        }
        return response($location);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, int $id)
    {
        $location = $this->locationService->getLocationById($id, $this->locationModel);
        if (!$location) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->locationService->editLocation(
            $location,
            $request->validated(),
        );
        return response($location);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $location = $this->locationService->getLocationById($id, $this->locationModel);
        if (!$location) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->locationService->deletelocation(
            $location,
        );
        return response([
            "message" => "success",
        ]);
    }
}
