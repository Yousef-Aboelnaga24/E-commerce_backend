<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;

class UserService
{
    public function getAll()
    {
        return User::withSum('orders as total_spent', 'total_price')->get();
    }

    public function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        return User::create($data);

        event(new Registered($user));

        return $user;
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
