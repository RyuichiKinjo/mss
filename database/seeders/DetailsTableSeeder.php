<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Str;

class DetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('details')->insert([
        //     'id' => random_int(1000, 10000),
        //     'h_id' => random_int(1000, 10000),
        //     'start' => date('Y-m-d H:i:s'),
        //     'end' => date('Y-m-d H:i:s'),
        //     'industry' => Str::random(20),
        //     'system' => Str::random(20),
        //     'role' => Str::random(20),
        //     'phase' => Str::random(20),
        //     'overview' => Str::random(20),
        //     'lang' => Str::random(20),
        //     'db' => Str::random(20),
        //     'env' => Str::random(20),
        // ]);
    }
}
