@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 28px; font-weight: 700; color: #1d1d1f; margin: 0 0 8px 0;">عقد جديد</h1>
            <p style="color: #86868b; margin: 0;">إنشاء عقد جديد في النظام</p>
        </div>

        <form method="POST" action="{{ route('contracts.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Basic Information -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #1d1d1f; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 2px solid #e5e5ea;">
                    المعلومات الأساسية
                </h2>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            رمز العقد <span style="color: #ff3b30;">*</span>
                        </label>
                        <input type="text" name="contract_code" value="{{ $contractCode }}" readonly style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px; background: #f5f5f7;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            رقم العقد (العميل) <span style="color: #ff3b30;">*</span>
                        </label>
                        <input type="text" name="contract_number" value="{{ old('contract_number') }}" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        @error('contract_number')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            عنوان العقد <span style="color: #ff3b30;">*</span>
                        </label>
                        <input type="text" name="contract_title" value="{{ old('contract_title') }}" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        @error('contract_title')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            العميل <span style="color: #ff3b30;">*</span>
                        </label>
                        <select name="client_id" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                            <option value="">اختر العميل</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            نوع العقد <span style="color: #ff3b30;">*</span>
                        </label>
                        <select name="contract_type" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                            <option value="">اختر النوع</option>
                            <option value="lump_sum" {{ old('contract_type') == 'lump_sum' ? 'selected' : '' }}>مبلغ إجمالي</option>
                            <option value="unit_price" {{ old('contract_type') == 'unit_price' ? 'selected' : '' }}>سعر الوحدة</option>
                            <option value="cost_plus" {{ old('contract_type') == 'cost_plus' ? 'selected' : '' }}>التكلفة الإضافية</option>
                            <option value="design_build" {{ old('contract_type') == 'design_build' ? 'selected' : '' }}>التصميم والبناء</option>
                            <option value="epc" {{ old('contract_type') == 'epc' ? 'selected' : '' }}>EPC</option>
                            <option value="bot" {{ old('contract_type') == 'bot' ? 'selected' : '' }}>BOT</option>
                        </select>
                        @error('contract_type')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            فئة العقد <span style="color: #ff3b30;">*</span>
                        </label>
                        <select name="contract_category" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                            <option value="">اختر الفئة</option>
                            <option value="main_contract" {{ old('contract_category') == 'main_contract' ? 'selected' : '' }}>عقد رئيسي</option>
                            <option value="subcontract" {{ old('contract_category') == 'subcontract' ? 'selected' : '' }}>عقد فرعي</option>
                            <option value="supply" {{ old('contract_category') == 'supply' ? 'selected' : '' }}>توريد</option>
                            <option value="service" {{ old('contract_category') == 'service' ? 'selected' : '' }}>خدمات</option>
                        </select>
                        @error('contract_category')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Financial Terms -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #1d1d1f; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 2px solid #e5e5ea;">
                    الشروط المالية
                </h2>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            قيمة العقد <span style="color: #ff3b30;">*</span>
                        </label>
                        <input type="number" step="0.01" name="contract_value" value="{{ old('contract_value') }}" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        @error('contract_value')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            العملة <span style="color: #ff3b30;">*</span>
                        </label>
                        <select name="currency_id" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                            <option value="">اختر العملة</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->code }} - {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('currency_id')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            نسبة الاستبقاء (%)
                        </label>
                        <input type="number" step="0.01" name="retention_percentage" value="{{ old('retention_percentage', 5.00) }}" style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        @error('retention_percentage')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            نسبة الدفعة المقدمة (%)
                        </label>
                        <input type="number" step="0.01" name="advance_payment_percentage" value="{{ old('advance_payment_percentage') }}" style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        @error('advance_payment_percentage')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #1d1d1f; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 2px solid #e5e5ea;">
                    التواريخ والمدة
                </h2>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            تاريخ التوقيع <span style="color: #ff3b30;">*</span>
                        </label>
                        <input type="date" name="signing_date" value="{{ old('signing_date') }}" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        @error('signing_date')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            تاريخ البدء <span style="color: #ff3b30;">*</span>
                        </label>
                        <input type="date" name="commencement_date" value="{{ old('commencement_date') }}" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        @error('commencement_date')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            تاريخ الانتهاء <span style="color: #ff3b30;">*</span>
                        </label>
                        <input type="date" name="completion_date" value="{{ old('completion_date') }}" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        @error('completion_date')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            فترة ضمان العيوب (أيام)
                        </label>
                        <input type="number" name="defects_liability_period" value="{{ old('defects_liability_period', 365) }}" style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Management -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #1d1d1f; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 2px solid #e5e5ea;">
                    الإدارة
                </h2>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            مدير العقد <span style="color: #ff3b30;">*</span>
                        </label>
                        <select name="contract_manager_id" required style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                            <option value="">اختر مدير العقد</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('contract_manager_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('contract_manager_id')
                            <span style="color: #ff3b30; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            مدير المشروع
                        </label>
                        <select name="project_manager_id" style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                            <option value="">اختر مدير المشروع (اختياري)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('project_manager_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Scope & Conditions -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #1d1d1f; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 2px solid #e5e5ea;">
                    نطاق العمل والشروط
                </h2>

                <div style="display: grid; gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            نطاق العمل
                        </label>
                        <textarea name="scope_of_work" rows="4" style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px; resize: vertical;">{{ old('scope_of_work') }}</textarea>
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            شروط الدفع
                        </label>
                        <textarea name="payment_terms" rows="3" style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px; resize: vertical;">{{ old('payment_terms') }}</textarea>
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                            بنود الغرامات
                        </label>
                        <textarea name="penalty_clause" rows="3" style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px; resize: vertical;">{{ old('penalty_clause') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Attachment -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #1d1d1f; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 2px solid #e5e5ea;">
                    المرفقات
                </h2>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">
                        ملف العقد الموقع
                    </label>
                    <input type="file" name="attachment" accept=".pdf,.doc,.docx" style="width: 100%; padding: 12px 16px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                    <p style="font-size: 12px; color: #86868b; margin-top: 4px;">الملفات المسموحة: PDF, DOC, DOCX (حد أقصى 10MB)</p>
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 24px; border-top: 1px solid #e5e5ea;">
                <a href="{{ route('contracts.index') }}" style="background: #f5f5f7; color: #1d1d1f; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: 600;">
                    إلغاء
                </a>
                <button type="submit" style="background: linear-gradient(135deg, #0071e3, #0077ed); color: white; border: none; padding: 12px 28px; border-radius: 10px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);">
                    حفظ العقد
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
