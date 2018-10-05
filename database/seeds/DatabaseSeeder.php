<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bus_settings')->insert([
            'shop_rate' => 89.99,
            'gst_rate' => 0.05,
            'shop_supply_rate' => 5.99,
            'city' => 'Lethbridge',
            'province' => 'Alberta',
            'address' => '145 35 St. North',
            'postal_code' => 'T3T U8Y',
            'phone' => '403-942-0200'
        ]);    

        DB::table('users')->insert([
            "name"=> "Matt",
            "email"=> "matt@admin.ca",
            "password"=> bcrypt(env('AUTH_CRED')),
            "role"=> "admin",
            "hourly_wage"=> 28.50
        ]);

        DB::table('users')->insert([
            "name"=> "Timmy",
            "email"=> "timmy@email.com",
            "password"=> bcrypt("password"),
            "role"=> "tech",
            "hourly_wage"=> 28.50
        ]);

        DB::table('customers')->insert([
            "first" => "Antonio",
            "last" => "Romeo",
            "phone_one" => "545-545-4444",
            "phone_two" => ""
        ]);  

        DB::table('vehicles')->insert([
            "customer_id" => 1,
            "make" => "Honda",
            "model" => "Civic",
            "year" => 2006,
            "vin" => "HS45ETGDGD4",
            "plate_number" => "TYE874"
        ]);   

        DB::table('work_orders')->insert([
            "vehicle_id" => 1,
            "created_by" => "Matt",
            "customer_id" => 1
        ]);                       	
    }
}
