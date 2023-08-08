<?php

namespace App\Http\Controllers;

use App\Models\RoomPlayer;
use App\Http\Requests\StoreRoomPlayerRequest;
use App\Http\Requests\UpdateRoomPlayerRequest;
use App\Models\Room;
use App\Models\User;
use App\Services\RoomPlayerServices\RoomPlayerService;
use App\Services\RoomServices\RoomService;
use App\Services\UserServices\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomPlayerController extends Controller
{
    private RoomPlayer $roomPlayerModel;
    private RoomPlayerService $roomPlayerService;

    private Room $roomModel;
    private RoomService $roomService;

    private User $userModel;
    private UserService $userService;


    public function __construct(
        RoomPlayerService $roomPlayerService,
        RoomPlayer $roomPlayerModel,
        RoomService $roomService,
        Room $roomModel,
        User $userModel,
        UserService $userService,
    ) {
        $this->roomPlayerModel = $roomPlayerModel;
        $this->roomPlayerService = $roomPlayerService;
        $this->roomModel = $roomModel;
        $this->roomService = $roomService;
        $this->userModel = $userModel;
        $this->userService = $userService;
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
        $notification = 'You have been kicked from this room "' . $room->name . '"';
        $this->roomService->notification($player, $notification, 'kick');

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
        $notification = 'You left from this room "' . $room->name . '"';
        $this->roomService->notification($player, $notification, 'leave');

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

        $roomPlayers = $this->roomPlayerModel::where('room_id', $room->id)->get();

        $roomPlayer = $this->roomPlayerModel::where('room_id', $room->id)->where('user_id', $player->id)->first();

        if (!$roomPlayer) {
            if ($room->max_players === $roomPlayers->count()) {
                return response([
                    "message" => "Room is full",
                ], 400);
            }
            $roomPlayer = $this->roomPlayerService->createRoomPlayer(
                [
                    'score' => 0,
                    'rank' => 0,
                    'user_id' => $playerId,
                    'room_id' => $room->id,
                ],
                $this->roomPlayerModel,
            );
            return response($room);
        }
        if (!$roomPlayer->kicked) {
            $roomPlayer->left = 0;
            $roomPlayer->save();
            return response($room);
        }

        return response([
            'message' => 'Player ' . $playerId . ' is kicked from the room'
        ], 400);
    }


    public function joinPlayerByRoomCode(Request $request, int $playerId)
    {
        $rules = [
            'room_code' => 'required|string|exists:rooms,code',
        ];
        $messages = [
            'room_code.required' => 'The room code is required.',
            'room_code.string' => 'The room code must be a string.',
            'room_code.exists' => 'The provided room code does not exist.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $room = $this->roomService->getRoomByCode($request->all()['room_code'], $this->roomModel);
        if (!$room) {
            return response([
                'message' => 'Room does not exists with the code ' . $request->all()['room_code'],
            ], 400);
        }
        $player = $this->userService->getUserById($playerId, $this->userModel);
        if (!$player) {
            return response([
                'message' => 'Player does not exists with the ID ' . $playerId,
            ], 400);
        }

        $roomPlayer = $this->roomPlayerModel::where('room_id', $room->id)
            ->where('user_id', $player->id)->first();

        if (!$roomPlayer) {
            $roomPlayer = $this->roomPlayerService->createRoomPlayer(
                [
                    'score' => 0,
                    'rank' => 0,
                    'user_id' => $playerId,
                    'room_id' => $room->id,
                ],
                $this->roomPlayerModel,
            );
            return response($room);
        }
        if (!$roomPlayer->kicked) {
            $roomPlayer->left = 0;
            $roomPlayer->save();
            return response($room);
        }

        return response([
            'message' => 'Player ' . $playerId . ' is kicked from the room'
        ], 400);
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
