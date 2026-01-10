<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 1000, 100000);
        $taxAmount = $subtotal * 0.15; // 15% tax
        $discount = $this->faker->randomFloat(2, 0, $subtotal * 0.1);
        $total = $subtotal + $taxAmount - $discount;

        return [
            'project_id' => \App\Models\Project::factory(),
            'supplier_id' => \App\Models\Supplier::factory(),
            'po_number' => 'PO-' . $this->faker->unique()->numberBetween(1000, 9999),
            'order_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'expected_delivery_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'status' => $this->faker->randomElement(['draft', 'pending', 'approved', 'received']),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount' => $discount,
            'total_amount' => $total,
            'created_by' => 1,
        ];
    }
}
