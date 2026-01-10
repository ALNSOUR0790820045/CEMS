@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Employee Header -->
    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <div style="display: flex; gap: 30px; align-items: start;">
            <div>
                @if($employee->photo_path)
                    <img src="{{ asset('storage/' . $employee->photo_path) }}" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #0071e3;">
                @else
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; color: white;">
                        {{ mb_substr($employee->first_name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div style="flex: 1;">
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 10px;">{{ $employee->full_name }}</h1>
                <p style="color: #666; font-size: 1.1rem; margin-bottom: 5px;">{{ $employee->job_title }}</p>
                <p style="color: #999; margin-bottom: 15px;">{{ $employee->employee_code }}</p>
                
                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    @if($employee->employment_status == 'active')
                        <span style="background: #d4edda; color: #155724; padding: 6px 16px; border-radius: 16px; font-size: 0.9rem; font-weight: 600;">نشط</span>
                    @else
                        <span style="background: #f8d7da; color: #721c24; padding: 6px 16px; border-radius: 16px; font-size: 0.9rem; font-weight: 600;">{{ $employee->employment_status }}</span>
                    @endif
                    
                    <span style="background: #e7f3ff; color: #0066cc; padding: 6px 16px; border-radius: 16px; font-size: 0.9rem;">
                        {{ $employee->employee_type }}
                    </span>
                </div>

                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('employees.edit', $employee) }}" style="background: #0071e3; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">تعديل</a>
                    <a href="{{ route('employees.index') }}" style="background: #f8f9fa; color: #333; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">رجوع</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #0071e3;">
            <h4 style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">سنوات الخدمة</h4>
            <p style="font-size: 1.8rem; font-weight: 700; color: #0071e3;">{{ number_format($employee->years_of_service, 1) }}</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #28a745;">
            <h4 style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">أيام منذ التوظيف</h4>
            <p style="font-size: 1.8rem; font-weight: 700; color: #28a745;">{{ $employee->days_since_hire }}</p>
        </div>
        @if($employee->age)
        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #ffc107;">
            <h4 style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">العمر</h4>
            <p style="font-size: 1.8rem; font-weight: 700; color: #ffc107;">{{ $employee->age }} سنة</p>
        </div>
        @endif
    </div>

    <!-- Employee Details -->
    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">المعلومات الشخصية</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <p style="color: #666; margin-bottom: 5px;">الاسم الكامل</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->full_name }}</p>
            </div>
            @if($employee->national_id)
            <div>
                <p style="color: #666; margin-bottom: 5px;">رقم الهوية</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->national_id }}</p>
            </div>
            @endif
            <div>
                <p style="color: #666; margin-bottom: 5px;">الجنس</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
            </div>
            @if($employee->date_of_birth)
            <div>
                <p style="color: #666; margin-bottom: 5px;">تاريخ الميلاد</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->date_of_birth->format('Y-m-d') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Contact Information -->
    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">معلومات الاتصال</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <p style="color: #666; margin-bottom: 5px;">الجوال</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->mobile }}</p>
            </div>
            @if($employee->email)
            <div>
                <p style="color: #666; margin-bottom: 5px;">البريد الإلكتروني</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->email }}</p>
            </div>
            @endif
            @if($employee->country)
            <div>
                <p style="color: #666; margin-bottom: 5px;">الدولة</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->country->name }}</p>
            </div>
            @endif
            @if($employee->city)
            <div>
                <p style="color: #666; margin-bottom: 5px;">المدينة</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->city->name }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Employment Information -->
    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">معلومات التوظيف</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            @if($employee->department)
            <div>
                <p style="color: #666; margin-bottom: 5px;">القسم</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->department->name }}</p>
            </div>
            @endif
            @if($employee->position)
            <div>
                <p style="color: #666; margin-bottom: 5px;">الوظيفة</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->position->name }}</p>
            </div>
            @endif
            <div>
                <p style="color: #666; margin-bottom: 5px;">تاريخ التوظيف</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->hire_date->format('Y-m-d') }}</p>
            </div>
            <div>
                <p style="color: #666; margin-bottom: 5px;">نوع التوظيف</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->employee_type }}</p>
            </div>
            @if($employee->supervisor)
            <div>
                <p style="color: #666; margin-bottom: 5px;">المشرف</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->supervisor->full_name }}</p>
            </div>
            @endif
        </div>
    </div>

    @if($employee->basic_salary)
    <!-- Salary Information -->
    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">معلومات الراتب</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <p style="color: #666; margin-bottom: 5px;">الراتب الأساسي</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ number_format($employee->basic_salary, 2) }}</p>
            </div>
            @if($employee->currency)
            <div>
                <p style="color: #666; margin-bottom: 5px;">العملة</p>
                <p style="font-weight: 600; font-size: 1.1rem;">{{ $employee->currency->name }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Documents -->
    @if($employee->documents->count() > 0)
    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">المستندات</h2>
        
        <div style="display: grid; gap: 10px;">
            @foreach($employee->documents as $document)
                <div style="padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="font-weight: 600; margin-bottom: 5px;">{{ $document->document_name }}</p>
                        <p style="color: #666; font-size: 0.9rem;">{{ $document->document_type }}</p>
                    </div>
                    @if($document->expiry_date)
                        <span style="color: {{ $document->expiry_date->isPast() ? '#dc3545' : '#666' }}; font-size: 0.9rem;">
                            تنتهي: {{ $document->expiry_date->format('Y-m-d') }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
