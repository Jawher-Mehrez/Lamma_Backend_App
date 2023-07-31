<?php

namespace App\Services\LocationServices;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{

    public function createLocation(array $data, Location $locationModel): Location
    {
        return $locationModel::create($this->locationData($data));
    }


    public function editLocation(Location $location, array $data): void
    {
        $location->update($this->locationData($data));
    }


    public function deleteLocation(Location $location): void
    {
        $location->delete();
    }


    public function getLocationById(int $id, Location $locationModel)
    {
        return $locationModel::where('id', $id)->first();
    }

    public function getLocations(Location $locationModel): Collection
    {
        return $locationModel::all();
    }

    public function locationData($data): array
    {
        return [
            'name' => $data['name'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'user_id' => $data['user_id'],
        ];
    }
}
