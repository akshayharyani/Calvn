<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('users')->insert([
          'name' => 'Akshay',
          'email' => 'akshay.haryani@gmail.com',
          'password' => bcrypt('akshay007'),
      ]);

      DB::table('users')->insert([
          'name' => 'Jon Doe',
          'email' => 'jon@gmail.com',
          'password' => bcrypt('007'),
      ]);

      DB::table('users')->insert([
          'name' => 'Bhavesh',
          'email' => 'bhavesh@gmail.com',
          'password' => bcrypt('007'),
      ]);
    }
}
