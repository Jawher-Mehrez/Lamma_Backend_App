<?php

namespace App\Services\RoomPlayerServices;

use App\Models\RoomPlayer;
use Illuminate\Database\Eloquent\Collection;

class RoomPlayerService
{

    public function createRoomPlayer(array $data, RoomPlayer $roomPlayerModel): RoomPlayer
    {
        return $roomPlayerModel
            ::create($this->roomPlayerData($data));
    }


    public function editRoomPlayer(RoomPlayer $roomPlayer, array $data): void
    {
        $roomPlayer->update($this->roomPlayerData($data));
    }


    public function deleteRoomPlayer(RoomPlayer $roomPlayer): void
    {
        $roomPlayer->delete();
    }


    public function getRoomPlayer(int $id, RoomPlayer $roomPlayerModel)
    {
        return $roomPlayerModel
            ::where('id', $id)->first();
    }

    public function getroom_players(RoomPlayer $roomPlayerModel): Collection
    {
        return $roomPlayerModel
            ::all();
    }

    public function roomPlayerData($data): array
    {
        return [
            'score' => $data['score'],
            'rank' => $data['rank'],
            'user_id' => $data['user_id'],
            'room_id' => $data['room_id'],
        ];
    }
}
