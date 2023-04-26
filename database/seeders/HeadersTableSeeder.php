<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Str;
use Date;

class HeadersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('headers')->insert([
        //     'id' => random_int(1000, 10000),
        //     'name' => Str::random(20),
        //     'initial' => Str::random(2),
        //     'sex' => Str::random(2),
        //     'birthday' => date('Y-m-d H:i:s'),
        //     'station' => Str::random(20),
        //     'cert' => Str::random(20),
        // ]);
    }
}
