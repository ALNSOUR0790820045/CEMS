@extends('layouts.app')

@section('title', 'Financial Reports Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Financial Reports</h1>
        <p class="text-gray-600">Generate comprehensive financial reports for your organization</p>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Trial Balance -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold ml-3">Trial Balance</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">View opening, debit, credit, and closing balances for all accounts</p>
            <a href="/api/reports/trial-balance" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Generate Report →</a>
        </div>

        <!-- Balance Sheet -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold ml-3">Balance Sheet</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Assets, Liabilities, and Equity statement with hierarchical display</p>
            <a href="/api/reports/balance-sheet" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Generate Report →</a>
        </div>

        <!-- Income Statement -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold ml-3">Income Statement</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Revenue, Expenses, and Net Income with comparative analysis</p>
            <a href="/api/reports/income-statement" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Generate Report →</a>
        </div>

        <!-- Cash Flow Statement -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold ml-3">Cash Flow Statement</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Operating, Investing, and Financing activities analysis</p>
            <a href="/api/reports/cash-flow" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Generate Report →</a>
        </div>

        <!-- AP Aging -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-red-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold ml-3">AP Aging</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Accounts Payable aging by vendor with aging buckets</p>
            <a href="/api/reports/ap-aging" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Generate Report →</a>
        </div>

        <!-- AR Aging -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold ml-3">AR Aging</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Accounts Receivable aging by customer with aging buckets</p>
            <a href="/api/reports/ar-aging" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Generate Report →</a>
        </div>

        <!-- Project Profitability -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-pink-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold ml-3">Project Profitability</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Revenue vs Cost analysis with Budget vs Actual comparison</p>
            <a href="/api/reports/project-profitability" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Generate Report →</a>
        </div>

        <!-- Budget vs Actual -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-teal-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold ml-3">Budget vs Actual</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Compare budgeted vs actual with variance analysis</p>
            <a href="/api/reports/budget-vs-actual" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Generate Report →</a>
        </div>

        <!-- Tax Report -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold ml-3">Tax Report (VAT)</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Sales tax collected vs Purchase tax paid analysis</p>
            <a href="/api/reports/tax-report" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Generate Report →</a>
        </div>
    </div>

    <!-- Recent Reports -->
    @if($recentReports->isNotEmpty())
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Reports</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Report Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Generated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Format</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentReports as $report)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $report->report_type)) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->generated_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ strtoupper($report->file_format) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="/api/report-history/{{ $report->id }}/download" class="text-blue-600 hover:text-blue-800">Download</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <a href="{{ route('reports.history') }}" class="text-blue-600 hover:text-blue-800 font-medium">View All Reports →</a>
        </div>
    </div>
    @endif
</div>
@endsection
