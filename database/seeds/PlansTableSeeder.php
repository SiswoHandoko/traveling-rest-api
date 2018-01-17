<?php

use App\Model\Plan;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->truncate();
    	
        $data = array(
            [
            	'user_id' => 1,
            	'tp_id' => 1,
            	'guide_id' => 1,
            	'start_date' => '2018-01-10 08:00:00',
            	'end_date' => '2018-01-15 08:00:00',
            	'status' => 'hold'
            ]
        );
        
        foreach ($data as $value) {
            Plan::create($value);
        }
    }
}
