<?php

use App\Model\TourismPlace;
use Illuminate\Database\Seeder;

class TourismPlacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tourism_places')->truncate();
    	
        $data = [
            [
            	'city_id' => 227,
            	'name' => 'Borobudur',
            	'description' => 'This is borobudur',
                'adult_price' => 10000,
                'child_price' => 9000,
                'infant_price' => 8000,
            	'tourist_price' => 15000,
            	'longitude' => 1,
            	'latitude' => -1,
            	'facilities' => 'toilet, mushola, wifi',
            	'status' => 'active'
            ],
            [
                'city_id' => 227,
                'name' => 'Prambanan',
                'description' => 'This is prambanan',
                'adult_price' => 10000,
                'child_price' => 9000,
                'infant_price' => 8000,
                'tourist_price' => 15000,
                'longitude' => 1,
                'latitude' => -1,
                'facilities' => 'toilet, mushola, wifi',
                'status' => 'active'
            ]
        ];
        
        TourismPlace::insert($data);
    }
}
