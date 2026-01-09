<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ARInvoiceResource extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'invoice_date' => $this->invoice_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
            ],
            'project' => $this->when($this->project, function () {
                return [
                    'id' => $this->project->id,
                    'project_name' => $this->project->project_name,
                ];
            }),
            'contract' => $this->when($this->contract, function () {
                return [
                    'id' => $this->contract->id,
                    'contract_name' => $this->contract->contract_name,
                ];
            }),
            'ipc' => $this->when($this->ipc, function () {
                return [
                    'id' => $this->ipc->id,
                    'ipc_number' => $this->ipc->ipc_number,
                ];
            }),
            'currency' => [
                'id' => $this->currency->id,
                'code' => $this->currency->code,
                'symbol' => $this->currency->symbol,
            ],
            'exchange_rate' => $this->exchange_rate,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'received_amount' => $this->received_amount,
            'balance' => $this->balance,
            'status' => $this->status,
            'payment_terms' => $this->payment_terms,
            'sent_at' => $this->sent_at?->format('Y-m-d H:i:s'),
            'attachment_path' => $this->attachment_path,
            'notes' => $this->notes,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'amount' => $item->amount,
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
