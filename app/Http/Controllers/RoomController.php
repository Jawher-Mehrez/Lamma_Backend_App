<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\RoomPlayer;
use App\Services\RoomServices\RoomService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    private Room $roomModel;
    private RoomService $roomService;

    public function __construct(RoomService $roomService, Room $roomModel)
    {
        $this->roomModel = $roomModel;
        $this->roomService = $roomService;
    }
    public function index(Request $request)
    {
        return $this->roomModel::where('status', 'active')->filter($request->all())->get();
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $room = $this->roomService->createRoom(
            $request->validated(),
            $this->roomModel,
        );
        return response($room);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $room = $this->roomService->getRoomById($id, $this->roomModel);
        if (!$room) {
            return response([
                "message" => "Not Found",
            ], 404);
        }
        return response($room);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, int $id)
    {
        $room = $this->roomService->getRoomById($id, $this->roomModel);
        if (!$room) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->roomService->editRoom(
            $room,
            $request->validated(),
        );
        if ($request->validated()['status'] === 'closed') {
            $notification = 'Room "' . $room->name . '" has been closed';
            $roomPlayers = RoomPlayer::where('room_id', $room->id)->get();
            foreach ($roomPlayers as $roomPlayer) {
                $this->roomService->notification($roomPlayer->player, $notification, 'room Closed');
            }
        }

        return response($room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $room = $this->roomService->getRoomById($id, $this->roomModel);
        if (!$room) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->roomService->deleteRoom(
            $room,
        );
        return response([
            "message" => "success",
        ]);
    }
}
