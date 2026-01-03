@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700;">إدارة المشاريع</h1>
        <a href="{{ route('projects.create') }}" style="background: #0071e3; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة مشروع جديد
        </a>
    </div>

    @if(session('success'))
    <div style="background: #d1f4e0; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #0c6b3f;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('projects.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <input type="text" name="search" placeholder="بحث بالكود، الاسم، أو الموقع..." value="{{ request('search') }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            
            <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="">كل الحالات</option>
                <option value="tendering" {{ request('status') == 'tendering' ? 'selected' : '' }}>عطاء</option>
                <option value="awarded" {{ request('status') == 'awarded' ? 'selected' : '' }}>مرسى</option>
                <option value="mobilization" {{ request('status') == 'mobilization' ? 'selected' : '' }}>حشد</option>
                <option value="execution" {{ request('status') == 'execution' ? 'selected' : '' }}>تنفيذ</option>
                <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>متوقف</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
            </select>
            
            <select name="client_id" style="padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="">كل العملاء</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                @endforeach
            </select>
            
            <select name="project_manager_id" style="padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="">كل مدراء المشاريع</option>
                @foreach($projectManagers as $manager)
                    <option value="{{ $manager->id }}" {{ request('project_manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                @endforeach
            </select>
            
            <button type="submit" style="background: #0071e3; color: white; padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">بحث</button>
            <a href="{{ route('projects.index') }}" style="padding: 10px 25px; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; text-align: center;">إعادة تعيين</a>
        </form>
    </div>

    <!-- Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 1200px;">
            <thead>
                <tr style="background: #f5f5f7; border-bottom: 2px solid #e5e5e7;">
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f; white-space: nowrap;">كود المشروع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">العميل</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">قيمة العقد</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">تاريخ البدء</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">تاريخ الانتهاء</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">مدير المشروع</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr style="border-bottom: 1px solid #f5f5f7;">
                    <td style="padding: 15px; color: #0071e3; font-weight: 600; white-space: nowrap;">{{ $project->project_code }}</td>
                    <td style="padding: 15px; font-weight: 600;">{{ $project->name }}</td>
                    <td style="padding: 15px;">{{ $project->client->name }}</td>
                    <td style="padding: 15px;">
                        @if($project->project_status == 'tendering')
                            <span style="background: #fff3e0; color: #f57c00; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; white-space: nowrap;">عطاء</span>
                        @elseif($project->project_status == 'awarded')
                            <span style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; white-space: nowrap;">مرسى</span>
                        @elseif($project->project_status == 'mobilization')
                            <span style="background: #f3e5f5; color: #7b1fa2; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; white-space: nowrap;">حشد</span>
                        @elseif($project->project_status == 'execution')
                            <span style="background: #e0f2f1; color: #00796b; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; white-space: nowrap;">تنفيذ</span>
                        @elseif($project->project_status == 'on_hold')
                            <span style="background: #ffebee; color: #c62828; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; white-space: nowrap;">متوقف</span>
                        @elseif($project->project_status == 'completed')
                            <span style="background: #d1f4e0; color: #0c6b3f; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; white-space: nowrap;">مكتمل</span>
                        @else
                            <span style="background: #f5f5f7; color: #666; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; white-space: nowrap;">مغلق</span>
                        @endif
                    </td>
                    <td style="padding: 15px; white-space: nowrap;">{{ number_format($project->contract_value, 2) }} {{ $project->currency->code }}</td>
                    <td style="padding: 15px; white-space: nowrap;">{{ $project->contract_start_date->format('Y-m-d') }}</td>
                    <td style="padding: 15px; white-space: nowrap;">{{ $project->contract_end_date->format('Y-m-d') }}</td>
                    <td style="padding: 15px;">{{ $project->projectManager->name }}</td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('projects.show', $project) }}" style="background: #e0f2f1; color: #00796b; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; white-space: nowrap;">عرض</a>
                            <a href="{{ route('projects.edit', $project) }}" style="background: #f5f5f7; color: #0071e3; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; white-space: nowrap;">تعديل</a>
                            <form method="POST" action="{{ route('projects.destroy', $project) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #ffebee; color: #c62828; padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600; font-family: 'Cairo', sans-serif; white-space: nowrap;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding: 40px; text-align: center; color: #999;">لا توجد مشاريع</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $projects->links() }}
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
