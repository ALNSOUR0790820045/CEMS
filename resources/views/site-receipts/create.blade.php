@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 100%; margin: 0 auto; background: #f5f5f7;">
    <div style="max-width: 900px; margin: 0 auto;">
        <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 20px; text-align: center;">إنشاء استلام موقع جديد</h1>

        <!-- Progress Steps -->
        <div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="step-item active" data-step="1" style="flex: 1; text-align: center; cursor: pointer;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #0071e3; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">1</div>
                    <div style="font-size: 12px; color: #0071e3; font-weight: 600;">معلومات أساسية</div>
                </div>
                <div class="step-item" data-step="2" style="flex: 1; text-align: center; cursor: pointer;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">2</div>
                    <div style="font-size: 12px; color: #999;">GPS والموقع</div>
                </div>
                <div class="step-item" data-step="3" style="flex: 1; text-align: center; cursor: pointer;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">3</div>
                    <div style="font-size: 12px; color: #999;">المواد</div>
                </div>
                <div class="step-item" data-step="4" style="flex: 1; text-align: center; cursor: pointer;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">4</div>
                    <div style="font-size: 12px; color: #999;">المستندات</div>
                </div>
                <div class="step-item" data-step="5" style="flex: 1; text-align: center; cursor: pointer;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">5</div>
                    <div style="font-size: 12px; color: #999;">الصور</div>
                </div>
                <div class="step-item" data-step="6" style="flex: 1; text-align: center; cursor: pointer;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">6</div>
                    <div style="font-size: 12px; color: #999;">التوقيعات</div>
                </div>
                <div class="step-item" data-step="7" style="flex: 1; text-align: center; cursor: pointer;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">7</div>
                    <div style="font-size: 12px; color: #999;">المراجعة</div>
                </div>
            </div>
        </div>

        <form id="siteReceiptForm" method="POST" action="{{ route('site-receipts.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: Basic Information -->
            <div class="form-step active" data-step="1" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">الخطوة 1: معلومات أساسية</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                        <select name="project_id" id="project_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                            <option value="">اختر المشروع</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" data-lat="{{ $project->latitude }}" data-lng="{{ $project->longitude }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">المورد *</label>
                        <select name="supplier_id" id="supplier_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                            <option value="">اختر المورد</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">أمر الشراء (اختياري)</label>
                        <select name="purchase_order_id" id="purchase_order_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                            <option value="">بدون أمر شراء</option>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الاستلام *</label>
                        <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">وقت الاستلام *</label>
                        <input type="time" name="receipt_time" value="{{ date('H:i') }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم السيارة</label>
                        <input type="text" name="vehicle_number" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم السائق</label>
                        <input type="text" name="driver_name" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">هاتف السائق</label>
                        <input type="text" name="driver_phone" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    </div>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <button type="button" onclick="nextStep()" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        التالي ←
                    </button>
                </div>
            </div>

            <!-- Step 2: GPS Location -->
            <div class="form-step" data-step="2" style="display: none; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">الخطوة 2: التقاط الموقع (GPS)</h2>
                
                <input type="hidden" name="latitude" id="latitude" required>
                <input type="hidden" name="longitude" id="longitude" required>
                
                <div style="margin-bottom: 20px;">
                    <button type="button" onclick="captureGPS()" id="captureGPSBtn" style="background: #34c759; color: white; padding: 15px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; width: 100%; font-size: 16px;">
                        📍 التقاط الموقع الحالي
                    </button>
                </div>

                <div id="gpsStatus" style="padding: 15px; background: #f0f0f0; border-radius: 8px; margin-bottom: 20px; text-align: center; color: #666;">
                    انقر على الزر أعلاه لالتقاط موقع GPS
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم الموقع *</label>
                    <input type="text" name="location_name" id="location_name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;" placeholder="مثال: مشروع البناء - المدخل الرئيسي">
                </div>

                <div id="mapPreview" style="height: 300px; border-radius: 8px; background: #f0f0f0; margin-top: 20px; display: flex; align-items: center; justify-content: center; color: #999;">
                    سيتم عرض الموقع على الخريطة بعد التقاط GPS
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                    <button type="button" onclick="prevStep()" style="background: #f0f0f0; color: #666; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        → السابق
                    </button>
                    <button type="button" onclick="nextStep()" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        التالي ←
                    </button>
                </div>
            </div>

            <!-- Step 3: Materials/Items -->
            <div class="form-step" data-step="3" style="display: none; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">الخطوة 3: المواد المستلمة</h2>
                
                <div id="itemsContainer">
                    <!-- Items will be added here -->
                </div>

                <button type="button" onclick="addItem()" style="background: #f0f0f0; color: #0071e3; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; margin-top: 15px;">
                    + إضافة مادة
                </button>

                <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                    <button type="button" onclick="prevStep()" style="background: #f0f0f0; color: #666; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        → السابق
                    </button>
                    <button type="button" onclick="nextStep()" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        التالي ←
                    </button>
                </div>
            </div>

            <!-- Step 4: Documents -->
            <div class="form-step" data-step="4" style="display: none; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">الخطوة 4: المستندات المطلوبة (4 إلزامية)</h2>
                
                <div style="display: grid; gap: 20px;">
                    <div style="border: 2px dashed #ddd; border-radius: 8px; padding: 20px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600;">1. الفاتورة الأصلية *</label>
                        <input type="file" name="invoice_document" id="invoice_document" accept="image/*,application/pdf" required style="width: 100%; padding: 10px; font-family: 'Cairo', sans-serif;">
                        <small style="color: #666;">PDF أو صورة (حد أقصى 10 ميجا)</small>
                    </div>

                    <div style="border: 2px dashed #ddd; border-radius: 8px; padding: 20px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600;">2. مذكرة التسليم *</label>
                        <input type="file" name="delivery_note" id="delivery_note" accept="image/*,application/pdf" required style="width: 100%; padding: 10px; font-family: 'Cairo', sans-serif;">
                        <small style="color: #666;">PDF أو صورة (حد أقصى 10 ميجا)</small>
                    </div>

                    <div style="border: 2px dashed #ddd; border-radius: 8px; padding: 20px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600;">3. قائمة التعبئة *</label>
                        <input type="file" name="packing_list" id="packing_list" accept="image/*,application/pdf" required style="width: 100%; padding: 10px; font-family: 'Cairo', sans-serif;">
                        <small style="color: #666;">PDF أو صورة (حد أقصى 10 ميجا)</small>
                    </div>

                    <div style="border: 2px dashed #ddd; border-radius: 8px; padding: 20px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600;">4. شهادات الجودة * (يمكن رفع أكثر من ملف)</label>
                        <input type="file" name="quality_certificates[]" id="quality_certificates" accept="image/*,application/pdf" multiple required style="width: 100%; padding: 10px; font-family: 'Cairo', sans-serif;">
                        <small style="color: #666;">PDF أو صورة (حد أقصى 10 ميجا لكل ملف)</small>
                    </div>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                    <button type="button" onclick="prevStep()" style="background: #f0f0f0; color: #666; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        → السابق
                    </button>
                    <button type="button" onclick="nextStep()" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        التالي ←
                    </button>
                </div>
            </div>

            <!-- Step 5: Photos -->
            <div class="form-step" data-step="5" style="display: none; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">الخطوة 5: التصوير الفوري (اختياري)</h2>
                
                <div style="border: 2px dashed #ddd; border-radius: 8px; padding: 20px; text-align: center;">
                    <input type="file" name="photos[]" id="photos" accept="image/*" multiple capture="environment" style="width: 100%; padding: 10px; font-family: 'Cairo', sans-serif;">
                    <p style="margin-top: 10px; color: #666;">التقط صور للسيارة، المواد، التعبئة، أو أي أضرار</p>
                </div>

                <div id="photoPreview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-top: 20px;">
                    <!-- Photo previews will appear here -->
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                    <button type="button" onclick="prevStep()" style="background: #f0f0f0; color: #666; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        → السابق
                    </button>
                    <button type="button" onclick="nextStep()" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        التالي ←
                    </button>
                </div>
            </div>

            <!-- Step 6: Signatures -->
            <div class="form-step" data-step="6" style="display: none; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">الخطوة 6: التوقيعات الثلاثية (إلزامية)</h2>
                
                <div style="display: grid; gap: 25px;">
                    <!-- Engineer Signature -->
                    <div style="border: 2px solid #0071e3; border-radius: 8px; padding: 20px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px; color: #0071e3;">1. توقيع مهندس الموقع</h3>
                        <select name="engineer_id" id="engineer_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; font-family: 'Cairo', sans-serif;">
                            <option value="">اختر المهندس</option>
                            <option value="1">المهندس أحمد</option>
                        </select>
                        <canvas id="engineer_canvas" width="800" height="200" style="border: 1px solid #ddd; border-radius: 6px; width: 100%; cursor: crosshair; background: #fafafa;"></canvas>
                        <input type="hidden" name="engineer_signature" id="engineer_signature" required>
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <button type="button" onclick="clearSignature('engineer')" style="padding: 8px 16px; background: #ff3b30; color: white; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">مسح</button>
                        </div>
                        <textarea name="engineer_notes" placeholder="ملاحظات المهندس (اختياري)" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-top: 10px; font-family: 'Cairo', sans-serif; min-height: 60px;"></textarea>
                    </div>

                    <!-- Storekeeper Signature -->
                    <div style="border: 2px solid #34c759; border-radius: 8px; padding: 20px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px; color: #34c759;">2. توقيع أمين المخزن</h3>
                        <select name="storekeeper_id" id="storekeeper_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; font-family: 'Cairo', sans-serif;">
                            <option value="">اختر أمين المخزن</option>
                            <option value="1">محمد - أمين المخزن</option>
                        </select>
                        <canvas id="storekeeper_canvas" width="800" height="200" style="border: 1px solid #ddd; border-radius: 6px; width: 100%; cursor: crosshair; background: #fafafa;"></canvas>
                        <input type="hidden" name="storekeeper_signature" id="storekeeper_signature" required>
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <button type="button" onclick="clearSignature('storekeeper')" style="padding: 8px 16px; background: #ff3b30; color: white; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">مسح</button>
                        </div>
                        <textarea name="storekeeper_notes" placeholder="ملاحظات أمين المخزن (اختياري)" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-top: 10px; font-family: 'Cairo', sans-serif; min-height: 60px;"></textarea>
                    </div>

                    <!-- Driver Signature -->
                    <div style="border: 2px solid #ff9500; border-radius: 8px; padding: 20px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px; color: #ff9500;">3. توقيع السائق/المورد</h3>
                        <input type="text" name="driver_signature_name" id="driver_signature_name" required placeholder="اسم السائق/المورد" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; font-family: 'Cairo', sans-serif;">
                        <canvas id="driver_canvas" width="800" height="200" style="border: 1px solid #ddd; border-radius: 6px; width: 100%; cursor: crosshair; background: #fafafa;"></canvas>
                        <input type="hidden" name="driver_signature" id="driver_signature" required>
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <button type="button" onclick="clearSignature('driver')" style="padding: 8px 16px; background: #ff3b30; color: white; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">مسح</button>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                    <button type="button" onclick="prevStep()" style="background: #f0f0f0; color: #666; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        → السابق
                    </button>
                    <button type="button" onclick="nextStep()" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        التالي ←
                    </button>
                </div>
            </div>

            <!-- Step 7: Review -->
            <div class="form-step" data-step="7" style="display: none; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">الخطوة 7: المراجعة والإرسال</h2>
                
                <div style="background: #f5f5f7; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px;">التحقق من المتطلبات:</h3>
                    <div style="display: grid; gap: 10px;">
                        <div id="check_gps" style="padding: 10px; background: white; border-radius: 6px;">
                            <span style="font-size: 20px; margin-left: 10px;">⭕</span> GPS تم التقاطه
                        </div>
                        <div id="check_documents" style="padding: 10px; background: white; border-radius: 6px;">
                            <span style="font-size: 20px; margin-left: 10px;">⭕</span> 4 مستندات مرفوعة
                        </div>
                        <div id="check_signatures" style="padding: 10px; background: white; border-radius: 6px;">
                            <span style="font-size: 20px; margin-left: 10px;">⭕</span> 3 توقيعات مكتملة
                        </div>
                        <div id="check_items" style="padding: 10px; background: white; border-radius: 6px;">
                            <span style="font-size: 20px; margin-left: 10px;">⭕</span> بنود المواد مدخلة
                        </div>
                    </div>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات عامة (اختياري)</label>
                    <textarea name="general_notes" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; min-height: 100px;"></textarea>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                    <button type="button" onclick="prevStep()" style="background: #f0f0f0; color: #666; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        → السابق
                    </button>
                    <button type="submit" id="submitBtn" style="background: #34c759; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; font-size: 16px;">
                        ✅ إرسال وإنشاء GRN
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let currentStep = 1;
let itemCounter = 0;
const products = @json($products);

// Signature canvases
let engineerCanvas, storekeeperCanvas, driverCanvas;
let engineerCtx, storekeeperCtx, driverCtx;
let isDrawing = {engineer: false, storekeeper: false, driver: false};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize signature canvases
    initSignatureCanvas('engineer');
    initSignatureCanvas('storekeeper');
    initSignatureCanvas('driver');
    
    // Add first item by default
    addItem();
    
    // Photo preview
    document.getElementById('photos').addEventListener('change', handlePhotoPreview);
});

function initSignatureCanvas(type) {
    const canvas = document.getElementById(type + '_canvas');
    const ctx = canvas.getContext('2d');
    
    // Store references
    if (type === 'engineer') { engineerCanvas = canvas; engineerCtx = ctx; }
    else if (type === 'storekeeper') { storekeeperCanvas = canvas; storekeeperCtx = ctx; }
    else if (type === 'driver') { driverCanvas = canvas; driverCtx = ctx; }
    
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    
    canvas.addEventListener('mousedown', (e) => startDrawing(e, type));
    canvas.addEventListener('mousemove', (e) => draw(e, type));
    canvas.addEventListener('mouseup', () => stopDrawing(type));
    canvas.addEventListener('mouseout', () => stopDrawing(type));
    
    // Touch events for mobile
    canvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        startDrawing(e.touches[0], type);
    });
    canvas.addEventListener('touchmove', (e) => {
        e.preventDefault();
        draw(e.touches[0], type);
    });
    canvas.addEventListener('touchend', (e) => {
        e.preventDefault();
        stopDrawing(type);
    });
}

function startDrawing(e, type) {
    isDrawing[type] = true;
    const ctx = type === 'engineer' ? engineerCtx : (type === 'storekeeper' ? storekeeperCtx : driverCtx);
    const canvas = type === 'engineer' ? engineerCanvas : (type === 'storekeeper' ? storekeeperCanvas : driverCanvas);
    const rect = canvas.getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
}

function draw(e, type) {
    if (!isDrawing[type]) return;
    const ctx = type === 'engineer' ? engineerCtx : (type === 'storekeeper' ? storekeeperCtx : driverCtx);
    const canvas = type === 'engineer' ? engineerCanvas : (type === 'storekeeper' ? storekeeperCanvas : driverCanvas);
    const rect = canvas.getBoundingClientRect();
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
}

function stopDrawing(type) {
    if (isDrawing[type]) {
        isDrawing[type] = false;
        const canvas = type === 'engineer' ? engineerCanvas : (type === 'storekeeper' ? storekeeperCanvas : driverCanvas);
        document.getElementById(type + '_signature').value = canvas.toDataURL();
    }
}

function clearSignature(type) {
    const canvas = type === 'engineer' ? engineerCanvas : (type === 'storekeeper' ? storekeeperCanvas : driverCanvas);
    const ctx = type === 'engineer' ? engineerCtx : (type === 'storekeeper' ? storekeeperCtx : driverCtx);
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById(type + '_signature').value = '';
}

function captureGPS() {
    const btn = document.getElementById('captureGPSBtn');
    const status = document.getElementById('gpsStatus');
    
    btn.disabled = true;
    btn.textContent = '⏳ جاري التقاط الموقع...';
    status.style.background = '#fff3cd';
    status.textContent = 'جاري التقاط الموقع...';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                
                btn.style.background = '#34c759';
                btn.textContent = '✅ تم التقاط الموقع بنجاح';
                status.style.background = '#d4edda';
                status.innerHTML = `<strong>✅ تم التقاط الموقع بنجاح!</strong><br>
                    خط العرض: ${position.coords.latitude.toFixed(6)}<br>
                    خط الطول: ${position.coords.longitude.toFixed(6)}`;
                
                // Update location name if empty
                const locationName = document.getElementById('location_name');
                if (!locationName.value) {
                    const projectSelect = document.getElementById('project_id');
                    const selectedProject = projectSelect.options[projectSelect.selectedIndex];
                    if (selectedProject.value) {
                        locationName.value = selectedProject.text + ' - الموقع';
                    }
                }
            },
            function(error) {
                btn.disabled = false;
                btn.style.background = '#ff3b30';
                btn.textContent = '❌ فشل التقاط الموقع';
                status.style.background = '#f8d7da';
                status.textContent = 'خطأ: ' + error.message;
            }
        );
    } else {
        btn.disabled = false;
        btn.style.background = '#ff3b30';
        btn.textContent = '❌ GPS غير مدعوم';
        status.style.background = '#f8d7da';
        status.textContent = 'المتصفح لا يدعم GPS';
    }
}

function addItem() {
    itemCounter++;
    const container = document.getElementById('itemsContainer');
    const itemHtml = `
        <div class="item-row" data-item="${itemCounter}" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 10px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">المادة *</label>
                    <select name="items[${itemCounter}][product_id]" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المادة</option>
                        ${products.map(p => `<option value="${p.id}" data-unit="${p.unit}">${p.name}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">الكمية *</label>
                    <input type="number" name="items[${itemCounter}][received_quantity]" step="0.001" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">الوحدة *</label>
                    <input type="text" name="items[${itemCounter}][unit]" value="قطعة" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">الحالة</label>
                    <select name="items[${itemCounter}][condition]" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="good">سليم</option>
                        <option value="damaged">تالف</option>
                        <option value="defective">معيب</option>
                        <option value="partial">جزئي</option>
                    </select>
                </div>
                <div>
                    <button type="button" onclick="removeItem(${itemCounter})" style="padding: 10px 15px; background: #ff3b30; color: white; border: none; border-radius: 6px; cursor: pointer;">حذف</button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function removeItem(id) {
    const item = document.querySelector(`[data-item="${id}"]`);
    if (item) item.remove();
}

function handlePhotoPreview(e) {
    const preview = document.getElementById('photoPreview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML += `
                <div style="position: relative; border-radius: 8px; overflow: hidden;">
                    <img src="${e.target.result}" style="width: 100%; height: 150px; object-fit: cover;">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 5px; font-size: 12px;">صورة ${index + 1}</div>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    });
}

function nextStep() {
    // Validate current step
    if (!validateStep(currentStep)) {
        return;
    }
    
    const currentStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    currentStepEl.style.display = 'none';
    
    currentStep++;
    
    const nextStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    nextStepEl.style.display = 'block';
    
    updateStepIndicators();
    window.scrollTo(0, 0);
}

function prevStep() {
    const currentStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    currentStepEl.style.display = 'none';
    
    currentStep--;
    
    const prevStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    prevStepEl.style.display = 'block';
    
    updateStepIndicators();
    window.scrollTo(0, 0);
}

function updateStepIndicators() {
    document.querySelectorAll('.step-item').forEach(item => {
        const step = parseInt(item.dataset.step);
        const circle = item.querySelector('div:first-child');
        const text = item.querySelector('div:last-child');
        
        if (step === currentStep) {
            item.classList.add('active');
            circle.style.background = '#0071e3';
            text.style.color = '#0071e3';
        } else if (step < currentStep) {
            circle.style.background = '#34c759';
            text.style.color = '#34c759';
        } else {
            item.classList.remove('active');
            circle.style.background = '#ddd';
            text.style.color = '#999';
        }
    });
    
    // Update review checks on step 7
    if (currentStep === 7) {
        updateReviewChecks();
    }
}

function validateStep(step) {
    if (step === 1) {
        if (!document.getElementById('project_id').value) {
            alert('يرجى اختيار المشروع');
            return false;
        }
        if (!document.getElementById('supplier_id').value) {
            alert('يرجى اختيار المورد');
            return false;
        }
    } else if (step === 2) {
        if (!document.getElementById('latitude').value) {
            alert('يرجى التقاط موقع GPS');
            return false;
        }
        if (!document.getElementById('location_name').value) {
            alert('يرجى إدخال اسم الموقع');
            return false;
        }
    } else if (step === 3) {
        const items = document.querySelectorAll('.item-row');
        if (items.length === 0) {
            alert('يرجى إضافة مادة واحدة على الأقل');
            return false;
        }
    } else if (step === 4) {
        if (!document.getElementById('invoice_document').files.length) {
            alert('يرجى رفع الفاتورة الأصلية');
            return false;
        }
        if (!document.getElementById('delivery_note').files.length) {
            alert('يرجى رفع مذكرة التسليم');
            return false;
        }
        if (!document.getElementById('packing_list').files.length) {
            alert('يرجى رفع قائمة التعبئة');
            return false;
        }
        if (!document.getElementById('quality_certificates').files.length) {
            alert('يرجى رفع شهادات الجودة');
            return false;
        }
    } else if (step === 6) {
        if (!document.getElementById('engineer_signature').value) {
            alert('يرجى إضافة توقيع مهندس الموقع');
            return false;
        }
        if (!document.getElementById('storekeeper_signature').value) {
            alert('يرجى إضافة توقيع أمين المخزن');
            return false;
        }
        if (!document.getElementById('driver_signature').value) {
            alert('يرجى إضافة توقيع السائق');
            return false;
        }
        if (!document.getElementById('driver_signature_name').value) {
            alert('يرجى إدخال اسم السائق/المورد');
            return false;
        }
    }
    
    return true;
}

function updateReviewChecks() {
    // GPS Check
    if (document.getElementById('latitude').value) {
        document.getElementById('check_gps').innerHTML = '<span style="font-size: 20px; margin-left: 10px;">✅</span> GPS تم التقاطه';
        document.getElementById('check_gps').style.background = '#d4edda';
    }
    
    // Documents Check
    const docsUploaded = 
        document.getElementById('invoice_document').files.length > 0 &&
        document.getElementById('delivery_note').files.length > 0 &&
        document.getElementById('packing_list').files.length > 0 &&
        document.getElementById('quality_certificates').files.length > 0;
    
    if (docsUploaded) {
        document.getElementById('check_documents').innerHTML = '<span style="font-size: 20px; margin-left: 10px;">✅</span> 4 مستندات مرفوعة';
        document.getElementById('check_documents').style.background = '#d4edda';
    }
    
    // Signatures Check
    const signaturesComplete = 
        document.getElementById('engineer_signature').value &&
        document.getElementById('storekeeper_signature').value &&
        document.getElementById('driver_signature').value;
    
    if (signaturesComplete) {
        document.getElementById('check_signatures').innerHTML = '<span style="font-size: 20px; margin-left: 10px;">✅</span> 3 توقيعات مكتملة';
        document.getElementById('check_signatures').style.background = '#d4edda';
    }
    
    // Items Check
    const items = document.querySelectorAll('.item-row');
    if (items.length > 0) {
        document.getElementById('check_items').innerHTML = `<span style="font-size: 20px; margin-left: 10px;">✅</span> ${items.length} بنود مدخلة`;
        document.getElementById('check_items').style.background = '#d4edda';
    }
}
</script>

<style>
@media (max-width: 768px) {
    .form-step > div {
        grid-template-columns: 1fr !important;
    }
    
    .item-row > div {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
