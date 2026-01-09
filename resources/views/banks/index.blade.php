@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">البنوك</h1>
        <a href="{{ route('banks.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">إضافة بنك</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الكود</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الاسم بالإنجليزية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الهاتف</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">البريد</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الحالة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banks as $bank)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px;">{{ $bank->code }}</td>
                        <td style="padding: 15px; font-weight: 600;">{{ $bank->name }}</td>
                        <td style="padding: 15px;">{{ $bank->name_en ?? '-' }}</td>
                        <td style="padding: 15px;">{{ $bank->phone ?? '-' }}</td>
                        <td style="padding: 15px;">{{ $bank->email ?? '-' }}</td>
                        <td style="padding: 15px;">
                            <span style="background: {{ $bank->is_active ? '#d4edda' : '#f8d7da' }}; color: {{ $bank->is_active ? '#155724' : '#721c24' }}; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                {{ $bank->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('banks.edit', $bank) }}" style="color: #0071e3; text-decoration: none;">تعديل</a>
                                <form method="POST" action="{{ route('banks.destroy', $bank) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer;">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #666;">لا توجد بنوك</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
