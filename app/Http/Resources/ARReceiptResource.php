<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ARReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'receipt_date' => $this->receipt_date->format('Y-m-d'),
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
            ],
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'currency' => [
                'id' => $this->currency->id,
                'code' => $this->currency->code,
                'symbol' => $this->currency->symbol,
            ],
            'exchange_rate' => $this->exchange_rate,
            'bank_account' => $this->when($this->bankAccount, function () {
                return [
                    'id' => $this->bankAccount->id,
                    'account_name' => $this->bankAccount->account_name,
                ];
            }),
            'check_number' => $this->check_number,
            'reference_number' => $this->reference_number,
            'status' => $this->status,
            'notes' => $this->notes,
            'unallocated_amount' => $this->unallocated_amount,
            'allocations' => $this->whenLoaded('allocations', function () {
                return $this->allocations->map(function ($allocation) {
                    return [
                        'id' => $allocation->id,
                        'invoice_number' => optional($allocation->arInvoice)->invoice_number,
                        'allocated_amount' => $allocation->allocated_amount,
                    ];
                });
            }),
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
