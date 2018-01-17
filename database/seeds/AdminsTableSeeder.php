<?php

use App\Model\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->truncate();

        $data = array(
            [
            	'username' => 'tommy',
            	'password' => Hash::make('tommy'),
            	'realname' => 'Tommy',
                'api_token' => sha1(time()),
            	'status' => 'nonactive'
            ]
        );

        foreach ($data as $value) {
            Admin::create($value);
        }
    }
}
