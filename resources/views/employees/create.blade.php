@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px; font-size: 2rem; font-weight: 700;">إضافة موظف جديد</h1>
    
    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        
        <!-- Personal Information -->
        <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">المعلومات الشخصية</h3>
        
        <input type="hidden" name="employee_code" value="{{ $employeeCode }}">
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم الموظف (تلقائي)</label>
            <input type="text" value="{{ $employeeCode }}" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f8f9fa; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم الأول *</label>
                <input type="text" name="first_name" required value="{{ old('first_name') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('first_name')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم الأوسط</label>
                <input type="text" name="middle_name" value="{{ old('middle_name') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم الأخير *</label>
                <input type="text" name="last_name" required value="{{ old('last_name') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('last_name')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم الهوية الوطنية</label>
                <input type="text" name="national_id" value="{{ old('national_id') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('national_id')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم جواز السفر</label>
                <input type="text" name="passport_number" value="{{ old('passport_number') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('passport_number')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الجنس *</label>
                <select name="gender" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر الجنس</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                </select>
                @error('gender')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الميلاد</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <!-- Contact Information -->
        <h3 style="margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">معلومات الاتصال</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الجوال *</label>
                <input type="text" name="mobile" required value="{{ old('mobile') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('mobile')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('email')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        <!-- Employment Information -->
        <h3 style="margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">معلومات التوظيف</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">القسم *</label>
                <select name="department_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر القسم</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوظيفة *</label>
                <select name="position_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر الوظيفة</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                </select>
                @error('position_id')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المسمى الوظيفي *</label>
            <input type="text" name="job_title" required value="{{ old('job_title') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            @error('job_title')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع التوظيف *</label>
                <select name="employee_type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="permanent" {{ old('employee_type') == 'permanent' ? 'selected' : '' }}>دائم</option>
                    <option value="contract" {{ old('employee_type') == 'contract' ? 'selected' : '' }}>عقد</option>
                    <option value="temporary" {{ old('employee_type') == 'temporary' ? 'selected' : '' }}>مؤقت</option>
                    <option value="consultant" {{ old('employee_type') == 'consultant' ? 'selected' : '' }}>استشاري</option>
                    <option value="daily_worker" {{ old('employee_type') == 'daily_worker' ? 'selected' : '' }}>عامل يومي</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ التوظيف *</label>
                <input type="date" name="hire_date" required value="{{ old('hire_date') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('hire_date')<span style="color: red; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        <!-- Salary Information -->
        <h3 style="margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">معلومات الراتب</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الراتب الأساسي</label>
                <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العملة</label>
                <select name="currency_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر العملة</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label>
                <input type="checkbox" name="is_active" value="1" checked>
                الموظف نشط
            </label>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ</button>
            <a href="{{ route('employees.index') }}" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
