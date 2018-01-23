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

        $data = [
            [
            	'username' => 'tommy',
            	'password' => Hash::make('qwerty'),
            	'realname' => 'Tommy',
            	'email' => 'admin@google.com',
                'web_token' => sha1(time()),
            	'status' => 'nonactive'
            ]
        ];

        Admin::insert($data);
    }
}
