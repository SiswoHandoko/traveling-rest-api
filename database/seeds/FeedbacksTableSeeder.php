<?php

use App\Model\Feedback;
use Illuminate\Database\Seeder;

class FeedbacksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('feedbacks')->truncate();

        $data = [
            [
            	'name' => 'Asep Mulyadi',
                'description' => 'This app is very good!',
                'status' => 'active'
            ]
        ];

        Feedback::insert($data);
    }
}
