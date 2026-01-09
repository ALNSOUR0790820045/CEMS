@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1d1d1f;
        margin: 0;
    }
    
    .compare-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        text-align: center;
    }
    
    .coming-soon {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 20px;
    }
    
    .message {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 10px;
    }
    
    .submessage {
        font-size: 1rem;
        color: #86868b;
    }
</style>

<div class="page-header">
    <h1 class="page-title">مقارنة نسخ جدول الكميات</h1>
</div>

<div class="compare-container">
    <div class="coming-soon">🔄</div>
    <h2 class="message">قريباً</h2>
    <p class="submessage">ستتمكن قريباً من مقارنة نسخ مختلفة من جدول الكميات</p>
</div>

@endsection
