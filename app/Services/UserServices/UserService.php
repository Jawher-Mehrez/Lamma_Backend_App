<?php

namespace App\Services\UserServices;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{

    public function createUser(array $data, User $userModel): User
    {
        return $userModel::create($this->UserData($data));
    }


    public function editUser(User $user, array $data): void
    {
        $user->update($this->UserData($data));
    }


    public function deleteUser(User $user): void
    {
        $user->delete();
    }


    public function getUserById(int $id, User $userModel)
    {
        return $userModel::where('id', $id)->first();
    }

    public function getusers(User $userModel): Collection
    {
        return $userModel::all();
    }

    public function userData($data): array
    {
        return [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            // 'role' => $data['role'],
            'phone_number' => $data['phone_number'],


        ];
    }
}
