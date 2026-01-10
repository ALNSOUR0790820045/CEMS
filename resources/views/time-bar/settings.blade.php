@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px;">إعدادات Time Bar</h1>

        <form method="POST" action="{{ route('time-bar.settings.update') }}" style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            @csrf
            @method('PUT')

            <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">فترات التنبيه</h2>

            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">فترة الإشعار الافتراضية (بالأيام)</label>
                <input type="number" name="default_notice_period" min="1" max="90" value="{{ $settings->default_notice_period ?? 28 }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">التنبيه الأول (أيام متبقية)</label>
                    <input type="number" name="first_warning_days" min="1" value="{{ $settings->first_warning_days ?? 21 }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">التنبيه الثاني (أيام متبقية)</label>
                    <input type="number" name="second_warning_days" min="1" value="{{ $settings->second_warning_days ?? 14 }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">تنبيه عاجل</label>
                    <input type="number" name="urgent_warning_days" min="1" value="{{ $settings->urgent_warning_days ?? 7 }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">تنبيه حرج</label>
                    <input type="number" name="critical_warning_days" min="1" value="{{ $settings->critical_warning_days ?? 3 }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">تنبيه أخير</label>
                    <input type="number" name="final_warning_days" min="1" value="{{ $settings->final_warning_days ?? 1 }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>

            <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 20px; padding-top: 20px; border-top: 1px solid #f5f5f7;">إعدادات الإشعارات</h2>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="email_notifications" value="1" {{ ($settings->email_notifications ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px;">
                    <span style="font-weight: 600;">تفعيل إشعارات البريد الإلكتروني</span>
                </label>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="sms_notifications" value="1" {{ ($settings->sms_notifications ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px;">
                    <span style="font-weight: 600;">تفعيل إشعارات الرسائل النصية</span>
                </label>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="escalation_enabled" value="1" {{ ($settings->escalation_enabled ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px;">
                    <span style="font-weight: 600;">تفعيل التصعيد التلقائي</span>
                </label>
            </div>

            <button type="submit" style="background: #0071e3; color: white; padding: 14px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; font-size: 1rem;">
                حفظ الإعدادات
            </button>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
