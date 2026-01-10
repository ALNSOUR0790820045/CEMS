<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ARInvoice extends Model
{
    use SoftDeletes;

    protected $table = 'a_r_invoices';

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'client_id',
        'project_id',
        'contract_id',
        'ipc_id',
        'currency_id',
        'exchange_rate',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'received_amount',
        'balance',
        'status',
        'payment_terms',
        'gl_account_id',
        'sent_at',
        'attachment_path',
        'notes',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'exchange_rate' => 'decimal:4',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }

            // Calculate total_amount
            $invoice->total_amount = $invoice->subtotal + $invoice->tax_amount - $invoice->discount_amount;

            // Calculate balance
            $invoice->balance = $invoice->total_amount - $invoice->received_amount;
        });

        static::updating(function ($invoice) {
            // Recalculate total_amount
            $invoice->total_amount = $invoice->subtotal + $invoice->tax_amount - $invoice->discount_amount;

            // Recalculate balance
            $invoice->balance = $invoice->total_amount - $invoice->received_amount;

            // Update status based on payment (but don't change cancelled invoices)
            if ($invoice->status !== 'cancelled') {
                if ($invoice->received_amount >= $invoice->total_amount) {
                    $invoice->status = 'paid';
                } elseif ($invoice->received_amount > 0) {
                    $invoice->status = 'partially_paid';
                } elseif ($invoice->due_date < now()) {
                    $invoice->status = 'overdue';
                }
            }
        });
    }

    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $lastInvoice = self::where('invoice_number', 'like', "ARI-{$year}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "ARI-{$year}-{$newNumber}";
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function ipc()
    {
        return $this->belongsTo(IPC::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function items()
    {
        return $this->hasMany(ARInvoiceItem::class);
    }

    public function receiptAllocations()
    {
        return $this->hasMany(ARReceiptAllocation::class);
    }
}
