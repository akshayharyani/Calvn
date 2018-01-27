<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('cars')->insert([
          'user_id' => 1,
          'car_name' => 'Model X',
          'car_number' => 'MH 05 007',
      ]);

      DB::table('cars')->insert([
          'user_id' => 1,
          'car_name' => 'Model S',
          'car_number' => 'MH 02 007',
      ]);

      DB::table('cars')->insert([
          'user_id' => 2,
          'car_name' => 'Model A',
          'car_number' => 'MH 02 101',
      ]);

      DB::table('cars')->insert([
          'user_id' => 3,
          'car_name' => 'Model C',
          'car_number' => 'MH 02 199',
      ]);
    }
}
