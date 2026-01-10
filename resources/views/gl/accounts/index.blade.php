@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 24px; font-weight: 600;">Chart of Accounts</h1>
        <a href="{{ route('gl.accounts.create') }}" 
           style="background: #0071e3; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">
            + New Account
        </a>
    </div>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 14px;">Account Type</label>
                <select name="account_type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="">All Types</option>
                    <option value="asset" {{ request('account_type') == 'asset' ? 'selected' : '' }}>Asset</option>
                    <option value="liability" {{ request('account_type') == 'liability' ? 'selected' : '' }}>Liability</option>
                    <option value="equity" {{ request('account_type') == 'equity' ? 'selected' : '' }}>Equity</option>
                    <option value="revenue" {{ request('account_type') == 'revenue' ? 'selected' : '' }}>Revenue</option>
                    <option value="expense" {{ request('account_type') == 'expense' ? 'selected' : '' }}>Expense</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 14px;">Status</label>
                <select name="is_active" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="">All</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 14px;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Code or Name..." 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="submit" style="padding: 8px 20px; background: #0071e3; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Account Tree Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Account Code</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Account Name</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Type</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Current Balance</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Status</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px; text-align: right; font-family: monospace; font-weight: 500;">
                        {{ $account->account_code }}
                    </td>
                    <td style="padding: 12px; text-align: right; padding-right: {{ ($account->account_level - 1) * 30 + 12 }}px;">
                        @if($account->account_level > 1)
                            <span style="color: #999;">└─</span>
                        @endif
                        {{ $account->account_name }}
                        @if(!$account->allow_posting)
                            <span style="color: #999; font-size: 12px;">(Header)</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        <span style="padding: 4px 8px; background: #e3f2fd; border-radius: 4px; font-size: 12px;">
                            {{ ucfirst($account->account_type) }}
                        </span>
                    </td>
                    <td style="padding: 12px; text-align: right; font-family: monospace;">
                        {{ number_format($account->current_balance, 2) }}
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        @if($account->is_active)
                            <span style="padding: 4px 8px; background: #4caf5020; color: #4caf50; border-radius: 4px; font-size: 12px;">Active</span>
                        @else
                            <span style="padding: 4px 8px; background: #66666620; color: #666; border-radius: 4px; font-size: 12px;">Inactive</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        <div style="display: flex; gap: 5px; justify-content: flex-end;">
                            <a href="{{ route('gl.accounts.show', $account) }}" 
                               style="padding: 6px 12px; background: #f0f0f0; border-radius: 6px; text-decoration: none; color: #333; font-size: 14px;">
                                View
                            </a>
                            <a href="{{ route('gl.accounts.ledger', $account) }}" 
                               style="padding: 6px 12px; background: #e3f2fd; border-radius: 6px; text-decoration: none; color: #0071e3; font-size: 14px;">
                                Ledger
                            </a>
                            <a href="{{ route('gl.accounts.edit', $account) }}" 
                               style="padding: 6px 12px; background: #fff3e0; border-radius: 6px; text-decoration: none; color: #ff9800; font-size: 14px;">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #999;">
                        No accounts found. Create your first GL account to get started.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Legend -->
    <div style="margin-top: 20px; padding: 15px; background: #f5f5f7; border-radius: 8px;">
        <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 10px;">Account Types:</h3>
        <div style="display: flex; gap: 20px; flex-wrap: wrap; font-size: 13px;">
            <div><strong>Asset:</strong> Cash, Bank, Inventory, Receivables</div>
            <div><strong>Liability:</strong> Payables, Loans, Accrued Expenses</div>
            <div><strong>Equity:</strong> Capital, Retained Earnings</div>
            <div><strong>Revenue:</strong> Sales, Service Income</div>
            <div><strong>Expense:</strong> Salaries, Rent, Utilities</div>
        </div>
    </div>
</div>
@endsection
