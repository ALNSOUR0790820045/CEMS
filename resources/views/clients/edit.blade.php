@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px; font-size: 2rem; font-weight: 700;">تعديل العميل: {{ $client->name }}</h1>
    
    <form method="POST" action="{{ route('clients.update', $client) }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">اسم العميل *</label>
                <input type="text" name="name" required value="{{ old('name', $client->name) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                @error('name')<span style="color: #c62828; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">اسم العميل (إنجليزي)</label>
                <input type="text" name="name_en" value="{{ old('name_en', $client->name_en) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">نوع العميل *</label>
                <select name="client_type" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر النوع</option>
                    <option value="government" {{ old('client_type', $client->client_type) == 'government' ? 'selected' : '' }}>حكومي</option>
                    <option value="semi_government" {{ old('client_type', $client->client_type) == 'semi_government' ? 'selected' : '' }}>شبه حكومي</option>
                    <option value="private" {{ old('client_type', $client->client_type) == 'private' ? 'selected' : '' }}>خاص</option>
                    <option value="individual" {{ old('client_type', $client->client_type) == 'individual' ? 'selected' : '' }}>فردي</option>
                </select>
                @error('client_type')<span style="color: #c62828; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                @error('email')<span style="color: #c62828; font-size: 0.85rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">الدولة</label>
                <select name="country_id" id="country_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر الدولة</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id', $client->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">المدينة</label>
                <select name="city_id" id="city_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر المدينة</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" data-country="{{ $city->country_id }}" {{ old('city_id', $client->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">السجل التجاري</label>
                <input type="text" name="commercial_registration" value="{{ old('commercial_registration', $client->commercial_registration) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">الرقم الضريبي</label>
            <input type="text" name="tax_number" value="{{ old('tax_number', $client->tax_number) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">العنوان</label>
            <textarea name="address" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">{{ old('address', $client->address) }}</textarea>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">ملاحظات</label>
            <textarea name="notes" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">{{ old('notes', $client->notes) }}</textarea>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <span style="font-weight: 600;">العميل نشط</span>
            </label>
        </div>
        
        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; font-size: 1rem;">تحديث</button>
            <a href="{{ route('clients.index') }}" style="padding: 14px 40px; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; display: inline-block;">إلغاء</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Filter cities by country
    document.getElementById('country_id').addEventListener('change', function() {
        const countryId = this.value;
        const citySelect = document.getElementById('city_id');
        const options = citySelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            if (option.dataset.country === countryId || countryId === '') {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        if (countryId) {
            citySelect.value = '';
        }
    });
</script>
@endpush
@endsection
