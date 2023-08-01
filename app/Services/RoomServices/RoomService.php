<?php

namespace App\Services\RoomServices;

use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;

class RoomService
{

    public function createRoom(array $data, Room $roomModel)
    {
        $data['code'] = $this->getNextRoomCode($roomModel);
        $data['status'] = 'deactivated';
        return $roomModel::create($this->RoomData($data));
    }

    public function editRoom(Room $location, array $data): void
    {
        $data['status'] = $data['status'];
        $location->update($this->RoomData($data));
    }


    public function deleteRoom(Room $location): void
    {
        $location->delete();
    }


    public function getRoomById(int $id, Room $roomModel)
    {
        return $roomModel::where('id', $id)->first();
    }

    public function getRoomByCode(string $code, Room $roomModel)
    {
        return $roomModel::where('code', $code)->first();
    }


    public function getRoom(Room $roomModel): Collection
    {
        return $roomModel::all();
    }

    public function RoomData($data): array
    {
        return [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'max_players' => $data['max_players'],
            'status' => $data['status'],
            'winners_prize' => $data['winners_prize'],
            'location_id' => $data['location_id'],
            'category_id' => $data['category_id'],
        ];
    }

    public function getNextRoomCode(Room $roomModel): string
    {
        $lastRecord = $roomModel->orderBy('id', 'DESC')->first();
        if (!$lastRecord) {
            return 'R0001';
        }
        $lastRef = $lastRecord->code;

        $currentNumber = intval(substr($lastRef, 4)) + 1;
        $nextNumber = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
        $newReference = substr($lastRef, 0, 4) . $nextNumber;

        return $newReference;
    }
}
