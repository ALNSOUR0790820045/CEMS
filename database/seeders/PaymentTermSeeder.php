<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentTerm;

class PaymentTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTerms = [
            [
                'name' => 'نقدي',
                'name_en' => 'Cash',
                'days' => 0,
                'description' => 'دفع فوري عند الاستلام',
                'is_active' => true,
            ],
            [
                'name' => 'آجل 7 أيام',
                'name_en' => 'Net 7',
                'days' => 7,
                'description' => 'الدفع خلال 7 أيام من تاريخ الفاتورة',
                'is_active' => true,
            ],
            [
                'name' => 'آجل 15 يوم',
                'name_en' => 'Net 15',
                'days' => 15,
                'description' => 'الدفع خلال 15 يوم من تاريخ الفاتورة',
                'is_active' => true,
            ],
            [
                'name' => 'آجل 30 يوم',
                'name_en' => 'Net 30',
                'days' => 30,
                'description' => 'الدفع خلال 30 يوم من تاريخ الفاتورة',
                'is_active' => true,
            ],
            [
                'name' => 'آجل 60 يوم',
                'name_en' => 'Net 60',
                'days' => 60,
                'description' => 'الدفع خلال 60 يوم من تاريخ الفاتورة',
                'is_active' => true,
            ],
            [
                'name' => 'آجل 90 يوم',
                'name_en' => 'Net 90',
                'days' => 90,
                'description' => 'الدفع خلال 90 يوم من تاريخ الفاتورة',
                'is_active' => true,
            ],
        ];

        foreach ($paymentTerms as $term) {
            PaymentTerm::create($term);
        }
    }
}

