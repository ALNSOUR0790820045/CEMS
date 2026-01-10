@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 24px; font-weight: 600;">Journal Entries</h1>
        <a href="{{ route('gl.journal-entries.create') }}" 
           style="background: #0071e3; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">
            + New Journal Entry
        </a>
    </div>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 14px;">Status</label>
                <select name="status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="">All</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 14px;">Type</label>
                <select name="journal_type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="">All</option>
                    <option value="general" {{ request('journal_type') == 'general' ? 'selected' : '' }}>General</option>
                    <option value="opening_balance" {{ request('journal_type') == 'opening_balance' ? 'selected' : '' }}>Opening Balance</option>
                    <option value="adjustment" {{ request('journal_type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 14px;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Journal #, Description..." 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="submit" style="padding: 8px 20px; background: #0071e3; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Journal #</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Date</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Type</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Description</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Total Debit</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Total Credit</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Status</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px; text-align: right;">
                        <a href="{{ route('gl.journal-entries.show', $entry) }}" 
                           style="color: #0071e3; text-decoration: none; font-weight: 500;">
                            {{ $entry->journal_number }}
                        </a>
                    </td>
                    <td style="padding: 12px; text-align: right;">{{ $entry->entry_date->format('Y-m-d') }}</td>
                    <td style="padding: 12px; text-align: right;">
                        <span style="padding: 4px 8px; background: #e3f2fd; border-radius: 4px; font-size: 12px;">
                            {{ ucfirst(str_replace('_', ' ', $entry->journal_type)) }}
                        </span>
                    </td>
                    <td style="padding: 12px; text-align: right;">{{ Str::limit($entry->description, 50) }}</td>
                    <td style="padding: 12px; text-align: right; font-family: monospace;">
                        {{ number_format($entry->total_debit, 2) }}
                    </td>
                    <td style="padding: 12px; text-align: right; font-family: monospace;">
                        {{ number_format($entry->total_credit, 2) }}
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        @php
                            $statusColors = [
                                'draft' => '#666',
                                'pending_approval' => '#ff9800',
                                'approved' => '#2196f3',
                                'posted' => '#4caf50',
                                'cancelled' => '#f44336',
                                'reversed' => '#9c27b0',
                            ];
                            $color = $statusColors[$entry->status] ?? '#666';
                        @endphp
                        <span style="padding: 4px 8px; background: {{ $color }}20; color: {{ $color }}; border-radius: 4px; font-size: 12px; font-weight: 500;">
                            {{ ucfirst(str_replace('_', ' ', $entry->status)) }}
                        </span>
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        <a href="{{ route('gl.journal-entries.show', $entry) }}" 
                           style="padding: 6px 12px; background: #f0f0f0; border-radius: 6px; text-decoration: none; color: #333; font-size: 14px;">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #999;">
                        No journal entries found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $entries->links() }}
    </div>
</div>
@endsection
