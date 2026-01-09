@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">إدارة التبعيات</h1>
            <p style="color: #86868b;">إدارة العلاقات بين الأنشطة ({{ $dependencies->total() }} علاقة)</p>
        </div>
        <a href="{{ route('activities.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            رجوع للأنشطة
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <!-- Add Dependency Form -->
    <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px;">إضافة علاقة جديدة</h3>
        
        <form method="POST" action="{{ route('dependencies.store') }}" style="display: grid; grid-template-columns: 2fr 2fr 1fr 1fr 120px; gap: 15px; align-items: end;">
            @csrf
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">النشاط السابق *</label>
                <select name="predecessor_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر النشاط</option>
                    @foreach($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->activity_code }} - {{ $activity->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">النشاط اللاحق *</label>
                <select name="successor_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر النشاط</option>
                    @foreach($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->activity_code }} - {{ $activity->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">النوع *</label>
                <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="FS">FS</option>
                    <option value="SS">SS</option>
                    <option value="FF">FF</option>
                    <option value="SF">SF</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">التأخير (أيام)</label>
                <input type="number" name="lag_days" value="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>

            <button type="submit" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إضافة</button>
        </form>

        <div style="margin-top: 15px; padding: 15px; background: #f5f5f7; border-radius: 8px;">
            <div style="font-weight: 600; margin-bottom: 10px; color: #1d1d1f;">أنواع العلاقات:</div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; font-size: 0.85rem;">
                <div><strong>FS:</strong> Finish-to-Start</div>
                <div><strong>SS:</strong> Start-to-Start</div>
                <div><strong>FF:</strong> Finish-to-Finish</div>
                <div><strong>SF:</strong> Start-to-Finish</div>
            </div>
        </div>
    </div>

    <!-- Dependencies Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @if($dependencies->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">النشاط السابق</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f; width: 80px;">العلاقة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">النشاط اللاحق</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f; width: 100px;">التأخير</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f; width: 100px;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dependencies as $dependency)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            @if($dependency->predecessor->is_critical)
                            <i data-lucide="alert-circle" style="width: 16px; height: 16px; color: #ff3b30;"></i>
                            @endif
                            <div>
                                <div style="font-family: monospace; font-weight: 600; color: #0071e3; margin-bottom: 3px;">
                                    {{ $dependency->predecessor->activity_code }}
                                </div>
                                <div style="color: #1d1d1f;">{{ $dependency->predecessor->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="background: #0071e3; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                            {{ $dependency->type }}
                        </span>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            @if($dependency->successor->is_critical)
                            <i data-lucide="alert-circle" style="width: 16px; height: 16px; color: #ff3b30;"></i>
                            @endif
                            <div>
                                <div style="font-family: monospace; font-weight: 600; color: #34c759; margin-bottom: 3px;">
                                    {{ $dependency->successor->activity_code }}
                                </div>
                                <div style="color: #1d1d1f;">{{ $dependency->successor->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="font-weight: 600; color: {{ $dependency->lag_days > 0 ? '#ff3b30' : ($dependency->lag_days < 0 ? '#34c759' : '#86868b') }};">
                            {{ $dependency->lag_days > 0 ? '+' : '' }}{{ $dependency->lag_days }} يوم
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <form method="POST" action="{{ route('dependencies.destroy', $dependency) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه العلاقة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: #ff3b30; color: white; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem; font-family: 'Cairo', sans-serif;">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div style="padding: 20px; border-top: 1px solid #f0f0f0;">
            {{ $dependencies->links() }}
        </div>
        @else
        <div style="padding: 60px; text-align: center;">
            <i data-lucide="git-branch" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 20px;"></i>
            <h3 style="color: #86868b; margin-bottom: 10px;">لا توجد تبعيات</h3>
            <p style="color: #d2d2d7;">ابدأ بإضافة علاقات بين الأنشطة</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
