<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $json = \Illuminate\Support\Facades\File::get("database/data/states.json");
        $states = json_decode($json);
        collect($states)->map(function($state){
            State::updateOrCreate([
                'name' => $state->name,
                'code' => $state->code,
                'country_id' => $state->country_id
            ]);
        });
    }
}
