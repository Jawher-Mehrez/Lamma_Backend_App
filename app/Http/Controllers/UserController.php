<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserServices\UserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    private User $userModel;
    private UserService $userService;

    public function __construct(UserService $userService, User $userModel)
    {
        $this->userModel = $userModel;
        $this->userService = $userService;
    }


    public function index()
    {
        return $this->userModel::paginate(10);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser(
            $request->validated(),
            $this->userModel,
        );

        return response($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = $this->userService->getUserById($id, $this->userModel);
        if (!$user) {
            return response([
                "message" => "Not Found",
            ], 404);
        }
        return response($user);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $user = $this->userService->getUserById($id, $this->userModel);
        if (!$user) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->userService->editUser(
            $user,
            $request->validated(),
        );
        return response($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = $this->userService->getUserById($id, $this->userModel);
        if (!$user) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->userService->deleteUser(
            $user,
        );
        return response([
            "message" => "success",
        ]);
    }
}
