<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchemeType;
use Carbon\Carbon;

class SchemeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate the table to clear any existing data
        SchemeType::truncate();

        // Define the schemes to be added
        $schemes = [
            [
                'title' => 'Fixed Plan',
                'shortcode' => 'Fixed',
                'flexibility_duration' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Flexible Plan',
                'shortcode' => 'Flexible',
                'flexibility_duration' => 6,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Gold Plan',
                'shortcode' => 'Gold',
                'flexibility_duration' => 6,  // Assuming 6 months for the Gold Plan
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        // Insert all the scheme records in one go
        SchemeType::insert($schemes);
    }
}
