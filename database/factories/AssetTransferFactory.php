<?php

namespace Database\Factories;

use App\Models\AssetTransfer;
use App\Models\FixedAsset;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetTransferFactory extends Factory
{
    protected $model = AssetTransfer::class;

    public function definition(): array
    {
        return [
            'fixed_asset_id' => FixedAsset::factory(),
            'transfer_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'reason' => fake()->sentence(),
            'status' => 'pending',
            'requested_by_id' => User::factory(),
            'company_id' => Company::factory(),
        ];
    }
}
