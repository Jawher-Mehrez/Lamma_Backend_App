<?php

namespace App\Http\Controllers;

use App\Models\RoomPlayer;
use App\Http\Requests\StoreRoomPlayerRequest;
use App\Http\Requests\UpdateRoomPlayerRequest;
use App\Models\Room;
use App\Models\User;
use App\Services\RoomPlayerServices\RoomPlayerService;

class RoomPlayerController extends Controller
{
    private RoomPlayer $roomPlayerModel;
    private RoomPlayerService $roomPlayerService;

    public function __construct(RoomPlayerService $roomPlayerService, RoomPlayer $roomPlayerModel)
    {
        $this->roomPlayerModel = $roomPlayerModel;
        $this->roomPlayerService = $roomPlayerService;
    }

    public function index()
    {
        return $this->roomPlayerModel::paginate(10);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomPlayerRequest $request)
    {
        $roomId = $request->validated()['room_id'];

        $roomPlayer = $this->roomPlayerService->createRoomPlayer(
            $request->validated(),
            $this->roomPlayerModel,
        );

        return response($roomPlayer);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $roomPlayer = $this->roomPlayerService->getRoomPlayer($id, $this->roomPlayerModel);
        if (!$roomPlayer) {
            return response([
                "message" => "Not Found",
            ], 404);
        }
        return response($roomPlayer);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomPlayerRequest $request, int $id)
    {
        $roomPlayer = $this->roomPlayerService->getRoomPlayer($id, $this->roomPlayerModel);
        if (!$roomPlayer) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->roomPlayerService->editRoomPlayer(
            $roomPlayer,
            $request->validated(),
        );
        return response($roomPlayer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $roomPlayer = $this->roomPlayerService->getRoomPlayer($id, $this->roomPlayerModel);
        if (!$roomPlayer) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->roomPlayerService->deleteRoomPlayer(
            $roomPlayer,
        );
        return response([
            "message" => "success",
        ]);
    }

    /**
     *  kickPlayer
     */
    public function kickPlayer(int $roomId, int $playerId)
    {
        $room = Room::find($roomId);
        $player = User::find($playerId);
        $roomPlayer = $this->roomPlayerModel::where('room_id', $room->id)->where('user_id', $player->id)->first();
        if (!$roomPlayer) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $roomPlayer->kicked = 1;
        $roomPlayer->save();

        return response([
            "message" => "success",
        ]);
    }
    public function leftPlayer(int $roomId, int $playerId)
    {
        $room = Room::find($roomId);
        $player = User::find($playerId);
        $roomPlayer = $this->roomPlayerModel::where('room_id', $room->id)->where('user_id', $player->id)->first();
        if (!$roomPlayer) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $roomPlayer->left = 1;
        $roomPlayer->save();

        return response([
            "message" => "success",
        ]);
    }

    public function joinPlayer(int $roomId, int $playerId)
    {
        $room = Room::find($roomId);
        $roomstatus = $room->status;
        $player = User::find($playerId);

        if ($roomstatus !== 'active') {
            return response([
                "message" => "Room not actives",
            ], 400);
        }

        $roomPlayer = $this->roomPlayerModel::where('room_id', $room->id)->where('user_id', $player->id)->first();

        if ($roomPlayer) {
            $isKicked = $roomPlayer->kicked;
            if ($isKicked) {
                return response([
                    "message" => "You cannot join this room",
                ], 400);
            }
            $isLeft = $roomPlayer->left;
            if ($isLeft) {
                $roomPlayer->left = 0;
                $roomPlayer->save();
                return response([
                    "message" => "success",
                ], 200);
            }
        }

        $this->roomPlayerService->createRoomPlayer([
            'user_id' => $playerId,
            'room_id' => $roomId,
            'score' => 0,
            'rank' => 0
        ], $this->roomPlayerModel);

        return response([
            "message" => "success",
        ]);
    }



    public function stats(int $playerId)
    {
        $player = User::find($playerId);

        $roomsPlayer = $this->roomPlayerModel::where('user_id', $player->id)->get();

        $wins = 0;
        $lose = 0;
        $winsPercent = 0;
        $losesPercent = 0;
        $playTime = 0;
        foreach ($roomsPlayer as $roomPlayer) {
            $rankPlayer = $roomPlayer->rank;
            if ($rankPlayer <= 2) {
                $wins = $wins + 1;
            }
            if ($rankPlayer > 2) {
                $lose = $lose + 1;
            }
            $playTime += $roomPlayer->play_time_by_hours;
        }
        if ($roomsPlayer->count() != 0) {
            $winsPercent = ($wins / $roomsPlayer->count()) * 100;
            $losesPercent = ($lose / $roomsPlayer->count()) * 100;
        }

        return response([
            'wins' => [
                'count' => $wins,
                'percentage' => $winsPercent
            ],
            'loss' => [
                'count' => $lose,
                'percentage' => $losesPercent
            ],
            'total_play_time' => $playTime
        ]);
    }

    public function history(int $playerId)
    {
        $player = User::find($playerId);

        $roomsPlayer = $this->roomPlayerModel::with('room')->where('user_id', $player->id)->get();
        return response($roomsPlayer);
    }
}
