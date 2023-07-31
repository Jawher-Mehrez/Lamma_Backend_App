<?php

namespace App\ModelFilters;

use App\Models\Location;
use EloquentFilter\ModelFilter;

class RoomFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function location($name)
    {
        $location = Location::where('name', 'LIKE', $name)->first();
        if ($location) {
            return $this->related('location', 'location_id', '=', $location->id);
        }
        return null;
    }

    public function name($name)
    {
        return $this->where('name', 'LIKE', '%' . $name . '%');
    }
}
