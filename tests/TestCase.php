<?php

namespace Tests;

use App\Services\AccessLevel\Models\Role;
use App\Services\User\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    public function authenticatedUser(array $data = []): User
    {
        $user = $this->user($data);

        $this->actingAs($user);

        return $user;
    }
    public function user(array $data = []): User
    {
        return User::factory()->create($data);
    }
}
