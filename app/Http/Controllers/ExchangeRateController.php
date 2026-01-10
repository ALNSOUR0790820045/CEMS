<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExchangeRateController extends Controller
{
    /**
     * Display a listing of exchange rates
     */
    public function index(Request $request)
    {
        $query = ExchangeRate::with(['fromCurrency', 'toCurrency', 'creator']);

        // Filters
        if ($request->filled('from_currency_id')) {
            $query->where('from_currency_id', $request->from_currency_id);
        }

        if ($request->filled('to_currency_id')) {
            $query->where('to_currency_id', $request->to_currency_id);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $rates = $query->latest('date')->latest('id')->paginate(20);
        
        // Get currencies for filters
        $currencies = Currency::active()->get();

        return view('exchange-rates.index', compact('rates', 'currencies'));
    }

    /**
     * Show the form for creating a new exchange rate
     */
    public function create()
    {
        $currencies = Currency::active()->get();

        return view('exchange-rates.create', compact('currencies'));
    }

    /**
     * Store a newly created exchange rate
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_currency_id' => 'required|exists:currencies,id|different:to_currency_id',
            'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
            'rate' => 'required|numeric|min:0.000001',
            'date' => 'required|date',
            'source' => 'required|in:manual,api,bank',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        // Check if rate already exists for this date and currency pair
        $existing = ExchangeRate::where('from_currency_id', $validated['from_currency_id'])
            ->where('to_currency_id', $validated['to_currency_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existing) {
            // Update existing rate instead of creating new one
            $existing->update($validated);
            $rate = $existing;
            $message = 'تم تحديث سعر الصرف الموجود';
        } else {
            $rate = ExchangeRate::create($validated);
            $message = 'تم إضافة سعر الصرف بنجاح';
        }

        // Also update the currency's exchange_rate field
        $currency = Currency::find($validated['from_currency_id']);
        if ($currency) {
            $currency->update([
                'exchange_rate' => $validated['rate'],
                'last_updated' => $validated['date'],
            ]);
        }

        return redirect()->route('exchange-rates.index')
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified exchange rate
     */
    public function edit(ExchangeRate $exchangeRate)
    {
        $currencies = Currency::active()->get();

        return view('exchange-rates.edit', compact('exchangeRate', 'currencies'));
    }

    /**
     * Update the specified exchange rate
     */
    public function update(Request $request, ExchangeRate $exchangeRate)
    {
        $validated = $request->validate([
            'rate' => 'required|numeric|min:0.000001',
            'date' => 'required|date',
            'source' => 'required|in:manual,api,bank',
            'notes' => 'nullable|string',
        ]);

        $exchangeRate->update($validated);

        // Update the currency's exchange_rate field if this is the latest rate
        $latestRate = ExchangeRate::where('from_currency_id', $exchangeRate->from_currency_id)
            ->where('to_currency_id', $exchangeRate->to_currency_id)
            ->latest('date')
            ->first();

        if ($latestRate && $latestRate->id == $exchangeRate->id) {
            $currency = Currency::find($exchangeRate->from_currency_id);
            if ($currency) {
                $currency->update([
                    'exchange_rate' => $validated['rate'],
                    'last_updated' => $validated['date'],
                ]);
            }
        }

        return redirect()->route('exchange-rates.index')
            ->with('success', 'تم تحديث سعر الصرف بنجاح');
    }

    /**
     * Remove the specified exchange rate
     */
    public function destroy(ExchangeRate $exchangeRate)
    {
        $exchangeRate->delete();

        return redirect()->route('exchange-rates.index')
            ->with('success', 'تم حذف سعر الصرف بنجاح');
    }

    /**
     * Update rates for all currencies
     */
    public function updateRates(Request $request)
    {
        $validated = $request->validate([
            'rates' => 'required|array',
            'rates.*.currency_id' => 'required|exists:currencies,id',
            'rates.*.rate' => 'required|numeric|min:0.000001',
            'date' => 'required|date',
            'source' => 'required|in:manual,api,bank',
        ]);

        $date = $validated['date'];
        $source = $validated['source'];
        $baseCurrency = Currency::where('is_base', true)->first();

        $count = 0;
        foreach ($validated['rates'] as $rateData) {
            if ($rateData['currency_id'] == $baseCurrency->id) {
                continue; // Skip base currency
            }

            // Check if rate exists
            $existing = ExchangeRate::where('from_currency_id', $rateData['currency_id'])
                ->where('to_currency_id', $baseCurrency->id)
                ->where('date', $date)
                ->first();

            if ($existing) {
                $existing->update([
                    'rate' => $rateData['rate'],
                    'source' => $source,
                    'created_by' => Auth::id(),
                ]);
            } else {
                ExchangeRate::create([
                    'from_currency_id' => $rateData['currency_id'],
                    'to_currency_id' => $baseCurrency->id,
                    'rate' => $rateData['rate'],
                    'date' => $date,
                    'source' => $source,
                    'created_by' => Auth::id(),
                ]);
            }

            // Update currency's exchange_rate field
            $currency = Currency::find($rateData['currency_id']);
            if ($currency) {
                $currency->update([
                    'exchange_rate' => $rateData['rate'],
                    'last_updated' => $date,
                ]);
            }

            $count++;
        }

        return redirect()->route('exchange-rates.index')
            ->with('success', "تم تحديث $count سعر صرف بنجاح");
    }

    /**
     * Get current exchange rate for currency pair
     */
    public function getRate(Request $request)
    {
        $validated = $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id',
            'date' => 'nullable|date',
        ]);

        $date = $validated['date'] ?? now()->toDateString();

        try {
            $amount = 1;
            $converted = ExchangeRate::convert(
                $amount,
                $validated['from_currency_id'],
                $validated['to_currency_id'],
                $date
            );

            $rate = $converted / $amount;

            return response()->json([
                'success' => true,
                'rate' => $rate,
                'date' => $date,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Show bulk update form
     */
    public function bulkUpdate()
    {
        $currencies = Currency::active()->where('is_base', false)->get();
        $baseCurrency = Currency::where('is_base', true)->first();

        return view('exchange-rates.bulk-update', compact('currencies', 'baseCurrency'));
    }
}
