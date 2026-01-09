@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1600px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">معرض الصور الشامل</h1>

    <!-- Filters -->
    <form method="GET" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">المشروع</label>
                <select name="project_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">كل المشاريع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">الفئة</label>
                <select name="category" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">كل الفئات</option>
                    <option value="progress" {{ request('category') == 'progress' ? 'selected' : '' }}>تقدم العمل</option>
                    <option value="problem" {{ request('category') == 'problem' ? 'selected' : '' }}>مشكلة</option>
                    <option value="safety" {{ request('category') == 'safety' ? 'selected' : '' }}>سلامة</option>
                    <option value="quality" {{ request('category') == 'quality' ? 'selected' : '' }}>جودة</option>
                    <option value="material" {{ request('category') == 'material' ? 'selected' : '' }}>مواد</option>
                    <option value="equipment" {{ request('category') == 'equipment' ? 'selected' : '' }}>معدات</option>
                    <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>عام</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">النشاط</label>
                <select name="activity_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">كل الأنشطة</option>
                    @foreach($activities as $activity)
                        <option value="{{ $activity->id }}" {{ request('activity_id') == $activity->id ? 'selected' : '' }}>
                            {{ $activity->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" 
                        style="background: #0071e3; color: white; padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif;">
                    بحث
                </button>
                <a href="{{ route('daily-reports.photos') }}" 
                   style="background: #f5f5f7; color: #666; padding: 8px 20px; border-radius: 5px; text-decoration: none; display: inline-block;">
                    إعادة تعيين
                </a>
            </div>
        </div>
    </form>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #0071e3;">
            <div style="color: #666; font-size: 0.9rem;">إجمالي الصور</div>
            <div style="font-size: 2rem; font-weight: 700; color: #0071e3;">{{ $photos->total() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #28a745;">
            <div style="color: #666; font-size: 0.9rem;">صور مع GPS</div>
            <div style="font-size: 2rem; font-weight: 700; color: #28a745;">
                {{ $photos->where('latitude', '!=', null)->count() }}
            </div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #ffc107;">
            <div style="color: #666; font-size: 0.9rem;">صور موثقة</div>
            <div style="font-size: 2rem; font-weight: 700; color: #ffc107;">
                {{ $photos->where('verified', true)->count() }}
            </div>
        </div>
    </div>

    <!-- Photos Grid -->
    <div style="background: white; padding: 25px; border-radius: 10px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            @forelse($photos as $photo)
                <div style="border: 1px solid #ddd; border-radius: 10px; overflow: hidden; cursor: pointer; transition: transform 0.2s;" 
                     onmouseover="this.style.transform='scale(1.02)'" 
                     onmouseout="this.style.transform='scale(1)'"
                     onclick="openPhotoModal({{ json_encode($photo) }})">
                    <img src="{{ Storage::url($photo->photo_path) }}" alt="{{ $photo->photo_title }}" 
                         style="width: 100%; height: 250px; object-fit: cover;">
                    <div style="padding: 15px;">
                        <div style="font-weight: 600; margin-bottom: 8px;">{{ $photo->photo_title ?? 'صورة' }}</div>
                        
                        @if($photo->description)
                            <div style="color: #666; font-size: 0.85rem; margin-bottom: 8px;">
                                {{ Str::limit($photo->description, 60) }}
                            </div>
                        @endif

                        <div style="display: flex; gap: 8px; margin-bottom: 8px; font-size: 0.85rem;">
                            <span style="background: #e8f4fd; color: #0071e3; padding: 3px 8px; border-radius: 12px;">
                                {{ $photo->category }}
                            </span>
                            @if($photo->latitude && $photo->longitude)
                                <span style="background: #d4edda; color: #155724; padding: 3px 8px; border-radius: 12px;" title="GPS: {{ $photo->latitude }}, {{ $photo->longitude }}">
                                    📍 GPS
                                </span>
                            @endif
                            @if($photo->verified)
                                <span style="background: #d1ecf1; color: #0c5460; padding: 3px 8px; border-radius: 12px;" title="Hash: {{ substr($photo->hash, 0, 8) }}...">
                                    ✓ Verified
                                </span>
                            @endif
                        </div>

                        <div style="color: #999; font-size: 0.75rem; margin-bottom: 5px;">
                            {{ $photo->captured_at->format('Y-m-d H:i') }}
                        </div>

                        <div style="color: #666; font-size: 0.8rem;">
                            {{ $photo->dailyReport->project->name }}
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; padding: 60px; text-align: center; color: #999;">
                    لا توجد صور
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($photos->hasPages())
            <div style="margin-top: 30px; display: flex; justify-content: center;">
                {{ $photos->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Photo Modal -->
<div id="photoModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.95); z-index: 9999; padding: 40px;" onclick="closePhotoModal()">
    <div style="position: relative; height: 100%; display: grid; grid-template-columns: 1fr 400px; gap: 20px;" onclick="event.stopPropagation()">
        <button onclick="closePhotoModal()" style="position: absolute; top: -30px; left: 10px; background: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 1.5rem; z-index: 10;">×</button>
        
        <!-- Image Section -->
        <div style="display: flex; align-items: center; justify-content: center;">
            <img id="modalImage" src="" alt="" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 10px;">
        </div>

        <!-- Info Section -->
        <div style="background: white; padding: 30px; border-radius: 10px; overflow-y: auto;">
            <h3 id="modalTitle" style="margin-bottom: 20px;"></h3>
            
            <div id="modalDescription" style="color: #666; line-height: 1.6; margin-bottom: 20px;"></div>

            <div style="border-top: 1px solid #f0f0f0; padding-top: 20px; margin-top: 20px;">
                <h4 style="margin-bottom: 15px; color: #666; font-size: 0.9rem;">معلومات الصورة</h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <div style="color: #999; font-size: 0.85rem;">الفئة</div>
                        <div id="modalCategory" style="font-weight: 600;"></div>
                    </div>
                    <div>
                        <div style="color: #999; font-size: 0.85rem;">تاريخ الالتقاط</div>
                        <div id="modalCaptured" style="font-weight: 600;"></div>
                    </div>
                    <div>
                        <div style="color: #999; font-size: 0.85rem;">الموقع الجغرافي (GPS)</div>
                        <div id="modalGPS" style="font-weight: 600;"></div>
                    </div>
                    <div>
                        <div style="color: #999; font-size: 0.85rem;">Hash (Blockchain)</div>
                        <div id="modalHash" style="font-family: monospace; font-size: 0.75rem; word-break: break-all;"></div>
                    </div>
                    <div>
                        <div style="color: #999; font-size: 0.85rem;">تم الرفع بواسطة</div>
                        <div id="modalUploader" style="font-weight: 600;"></div>
                    </div>
                    <div>
                        <div style="color: #999; font-size: 0.85rem;">المشروع</div>
                        <div id="modalProject" style="font-weight: 600;"></div>
                    </div>
                    <div>
                        <div style="color: #999; font-size: 0.85rem;">رقم التقرير</div>
                        <div id="modalReport" style="font-weight: 600;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    function openPhotoModal(photo) {
        document.getElementById('photoModal').style.display = 'block';
        document.getElementById('modalImage').src = '/storage/' + photo.photo_path;
        document.getElementById('modalTitle').textContent = photo.photo_title || 'صورة';
        document.getElementById('modalDescription').textContent = photo.description || 'لا يوجد وصف';
        document.getElementById('modalCategory').textContent = photo.category;
        document.getElementById('modalCaptured').textContent = new Date(photo.captured_at).toLocaleString('ar-SA');
        
        if (photo.latitude && photo.longitude) {
            document.getElementById('modalGPS').innerHTML = `
                <a href="https://maps.google.com/?q=${photo.latitude},${photo.longitude}" target="_blank" 
                   style="color: #0071e3; text-decoration: none;">
                    ${photo.latitude}, ${photo.longitude} 📍
                </a>
            `;
        } else {
            document.getElementById('modalGPS').textContent = 'غير متوفر';
        }
        
        document.getElementById('modalHash').textContent = photo.hash;
        document.getElementById('modalUploader').textContent = photo.uploaded_by.name;
        document.getElementById('modalProject').textContent = photo.daily_report.project.name;
        document.getElementById('modalReport').textContent = photo.daily_report.report_number;
        
        document.body.style.overflow = 'hidden';
    }

    function closePhotoModal() {
        document.getElementById('photoModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
</script>
@endpush
@endsection
