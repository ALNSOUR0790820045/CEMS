@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 600; margin-bottom: 5px;">{{ $journalEntry->journal_number }}</h1>
            <p style="color: #666; font-size: 14px;">Created {{ $journalEntry->created_at->diffForHumans() }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($journalEntry->status == 'draft')
                <a href="{{ route('gl.journal-entries.edit', $journalEntry) }}" 
                   style="padding: 10px 20px; background: #0071e3; color: white; border-radius: 8px; text-decoration: none;">
                    Edit
                </a>
                <form method="POST" action="{{ route('gl.journal-entries.submit', $journalEntry) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="padding: 10px 20px; background: #4caf50; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        Submit for Approval
                    </button>
                </form>
            @endif
            
            @if($journalEntry->status == 'pending_approval')
                <form method="POST" action="{{ route('gl.journal-entries.approve', $journalEntry) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="padding: 10px 20px; background: #4caf50; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        Approve
                    </button>
                </form>
                <form method="POST" action="{{ route('gl.journal-entries.reject', $journalEntry) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="padding: 10px 20px; background: #f44336; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        Reject
                    </button>
                </form>
            @endif
            
            @if($journalEntry->status == 'approved')
                <form method="POST" action="{{ route('gl.journal-entries.post', $journalEntry) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="padding: 10px 20px; background: #2196f3; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        Post to Ledger
                    </button>
                </form>
            @endif
            
            @if($journalEntry->status == 'posted' && !$journalEntry->reversed_by_id)
                <form method="POST" action="{{ route('gl.journal-entries.reverse', $journalEntry) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="padding: 10px 20px; background: #9c27b0; color: white; border: none; border-radius: 8px; cursor: pointer;" 
                            onclick="return confirm('Are you sure you want to reverse this entry?')">
                        Reverse Entry
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Status & Info Card -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div>
                <label style="display: block; color: #666; font-size: 12px; margin-bottom: 4px;">Status</label>
                <div>
                    @php
                        $statusColors = [
                            'draft' => '#666',
                            'pending_approval' => '#ff9800',
                            'approved' => '#2196f3',
                            'posted' => '#4caf50',
                            'cancelled' => '#f44336',
                            'reversed' => '#9c27b0',
                        ];
                        $color = $statusColors[$journalEntry->status] ?? '#666';
                    @endphp
                    <span style="padding: 6px 12px; background: {{ $color }}20; color: {{ $color }}; border-radius: 6px; font-size: 14px; font-weight: 500;">
                        {{ ucfirst(str_replace('_', ' ', $journalEntry->status)) }}
                    </span>
                </div>
            </div>
            <div>
                <label style="display: block; color: #666; font-size: 12px; margin-bottom: 4px;">Entry Date</label>
                <div style="font-weight: 500;">{{ $journalEntry->entry_date->format('Y-m-d') }}</div>
            </div>
            <div>
                <label style="display: block; color: #666; font-size: 12px; margin-bottom: 4px;">Type</label>
                <div style="font-weight: 500;">{{ ucfirst(str_replace('_', ' ', $journalEntry->journal_type)) }}</div>
            </div>
            <div>
                <label style="display: block; color: #666; font-size: 12px; margin-bottom: 4px;">Currency</label>
                <div style="font-weight: 500;">{{ $journalEntry->currency->code }}</div>
            </div>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
            <label style="display: block; color: #666; font-size: 12px; margin-bottom: 4px;">Description</label>
            <div>{{ $journalEntry->description }}</div>
        </div>
    </div>

    <!-- Journal Lines -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 15px;">Journal Entry Lines</h2>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">#</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd;">GL Account</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd;">Description</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Debit</th>
                    <th style="padding: 12px; text-align: right; border-bottom: 1px solid #ddd;">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($journalEntry->lines as $line)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px; text-align: right;">{{ $line->line_number }}</td>
                    <td style="padding: 12px; text-align: left;">
                        {{ $line->glAccount->account_code }} - {{ $line->glAccount->account_name }}
                    </td>
                    <td style="padding: 12px; text-align: left;">{{ $line->description }}</td>
                    <td style="padding: 12px; text-align: right; font-family: monospace; {{ $line->debit_amount > 0 ? 'font-weight: 600;' : 'color: #ccc;' }}">
                        {{ number_format($line->debit_amount, 2) }}
                    </td>
                    <td style="padding: 12px; text-align: right; font-family: monospace; {{ $line->credit_amount > 0 ? 'font-weight: 600;' : 'color: #ccc;' }}">
                        {{ number_format($line->credit_amount, 2) }}
                    </td>
                </tr>
                @endforeach
                
                <!-- Totals -->
                <tr style="background: #f5f5f7; font-weight: 600;">
                    <td colspan="3" style="padding: 12px; text-align: right;">Total</td>
                    <td style="padding: 12px; text-align: right; font-family: monospace; border-top: 2px solid #ddd;">
                        {{ number_format($journalEntry->total_debit, 2) }}
                    </td>
                    <td style="padding: 12px; text-align: right; font-family: monospace; border-top: 2px solid #ddd;">
                        {{ number_format($journalEntry->total_credit, 2) }}
                    </td>
                </tr>
                
                <!-- Balance Check -->
                <tr>
                    <td colspan="3" style="padding: 12px; text-align: right;">
                        @if($journalEntry->is_balanced)
                            <span style="color: #4caf50;">✓ Balanced</span>
                        @else
                            <span style="color: #f44336;">✗ Not Balanced (Difference: {{ number_format(abs($journalEntry->difference), 2) }})</span>
                        @endif
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Audit Trail -->
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 15px;">Audit Trail</h2>
        
        <div style="display: grid; gap: 10px;">
            <div style="padding: 10px; background: #f5f5f7; border-radius: 6px;">
                <strong>Created by:</strong> {{ $journalEntry->createdBy->name }} 
                on {{ $journalEntry->created_at->format('Y-m-d H:i') }}
            </div>
            
            @if($journalEntry->approved_at)
            <div style="padding: 10px; background: #f5f5f7; border-radius: 6px;">
                <strong>Approved by:</strong> {{ $journalEntry->approvedBy->name }} 
                on {{ $journalEntry->approved_at->format('Y-m-d H:i') }}
            </div>
            @endif
            
            @if($journalEntry->posted_at)
            <div style="padding: 10px; background: #f5f5f7; border-radius: 6px;">
                <strong>Posted by:</strong> {{ $journalEntry->postedBy->name }} 
                on {{ $journalEntry->posted_at->format('Y-m-d H:i') }}
            </div>
            @endif
            
            @if($journalEntry->reversed_by_id)
            <div style="padding: 10px; background: #fff3e0; border-radius: 6px;">
                <strong>Reversed by:</strong> 
                <a href="{{ route('gl.journal-entries.show', $journalEntry->reversedBy) }}" style="color: #0071e3; text-decoration: none;">
                    {{ $journalEntry->reversedBy->journal_number }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
