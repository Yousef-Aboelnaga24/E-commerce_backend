<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAll()
    {
        return User::all();
    }

    public function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        return User::create($data);
    }

    public function update(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        $user->update($data);
        return $user;
    }

    public function delete(User $user)
    {
        return $user->delete();
    }
}
