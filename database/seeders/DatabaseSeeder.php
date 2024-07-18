<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $userData = [

            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678'),
            'role' => 'ADMIN',
            'service_id' => null,

        ];
        User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'role' => $userData['role'],
            'permissions' => [
                'USER_READ',
                'USER_WRITE',
                'SERVICE_READ',
                'SERVICE_WRITE',
                'CATEGORY_READ',
                'CATEGORY_WRITE',
                'EXPENSE_READ',
                'EXPENSE_WRITE'
            ],
            'service_id' => null,
        ]);
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
