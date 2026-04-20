<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function createUser(array $data): User
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_active' => $data['is_active'] ?? true,
        ];

        return $this->userRepository->create($payload);
    }
}
