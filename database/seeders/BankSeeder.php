<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'code' => 'NCB',
                'name' => 'البنك الأهلي التجاري',
                'name_en' => 'National Commercial Bank',
                'swift_code' => 'NCBKSAJE',
                'phone' => '920001000',
                'email' => 'info@alahli.com',
                'is_active' => true,
            ],
            [
                'code' => 'SAMBA',
                'name' => 'سامبا',
                'name_en' => 'Samba Financial Group',
                'swift_code' => 'SAMBSARI',
                'phone' => '920008888',
                'email' => 'info@samba.com',
                'is_active' => true,
            ],
            [
                'code' => 'RIYAD',
                'name' => 'بنك الرياض',
                'name_en' => 'Riyad Bank',
                'swift_code' => 'RIBLSARI',
                'phone' => '920002470',
                'email' => 'info@riyadbank.com',
                'is_active' => true,
            ],
            [
                'code' => 'RAJHI',
                'name' => 'مصرف الراجحي',
                'name_en' => 'Al Rajhi Bank',
                'swift_code' => 'RJHISARI',
                'phone' => '920003344',
                'email' => 'info@alrajhibank.com.sa',
                'is_active' => true,
            ],
            [
                'code' => 'SABB',
                'name' => 'البنك السعودي البريطاني',
                'name_en' => 'Saudi British Bank',
                'swift_code' => 'SABBSARI',
                'phone' => '920004449',
                'email' => 'info@sabb.com',
                'is_active' => true,
            ],
        ];

        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
