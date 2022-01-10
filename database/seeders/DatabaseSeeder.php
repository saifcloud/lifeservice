<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Hash;

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
        \App\Models\Admin::create([
            'name'=>'Admin',
            'email'=>'admin@admin.com',
            'password'=>Hash::make(123456),
            'status'=>1
        ]);
    }
}
