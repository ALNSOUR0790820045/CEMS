@extends('layouts.app')
@section('content')
<div style="padding: 40px;">
<h1>إضافة شركة جديدة</h1>
<form method="POST" action="{{ route('companies.store') }}">
@csrf
<input type="text" name="name" placeholder="اسم الشركة" required style="display: block;margin: 10px 0;padding:10px;width:300px;">
<input type="email" name="email" placeholder="البريد" style="display:block;margin:10px 0;padding:10px;width:300px;">
<select name="country" required style="display:block;margin:10px 0;padding:10px;width:300px;">
<option value="">اختر الدولة</option>
<option value="SA">السعودية</option>
</select>
<button type="submit" style="padding:10px 30px;background:#0071e3;color: white;border:none;cursor:pointer;">حفظ</button>
</form>
</div>
@endsection