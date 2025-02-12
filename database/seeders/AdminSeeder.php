<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createAdmin();
    }


    protected function createAdmin()
    {
        $user = User::create([
            'email' => 'admin@admin.site',
            'password' => 'password',
            'first_name' => 'admin',
            'last_name' => 'admin'
        ]);
        $user->addRole(RoleEnum::Admin);
    }
}
