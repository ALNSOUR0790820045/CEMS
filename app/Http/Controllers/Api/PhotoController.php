<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\PhotoAlbum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Photo::with(['project', 'album', 'uploadedBy']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by album
        if ($request->has('album_id')) {
            $query->where('album_id', $request->album_id);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by approval status
        if ($request->has('approved')) {
            $query->where('approved', $request->approved === 'true' || $request->approved === '1');
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->is_featured === 'true' || $request->is_featured === '1');
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('photo_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $photos = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($photos);
    }

    /**
     * Get photos by project
     */
    public function byProject($projectId)
    {
        $photos = Photo::where('project_id', $projectId)
            ->where('approved', true)
            ->orderBy('taken_date', 'desc')
            ->paginate(20);

        return response()->json($photos);
    }

    /**
     * Get photos by album
     */
    public function byAlbum($albumId)
    {
        $photos = Photo::where('album_id', $albumId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($photos);
    }

    /**
     * Upload photos
     */
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'album_id' => 'nullable|exists:photo_albums,id',
            'photo' => 'required|image|max:10240', // 10MB
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|in:progress,quality,safety,defect,before,after,milestone,general',
            'location' => 'nullable|string',
            'work_area' => 'nullable|string',
            'activity_id' => 'nullable|exists:project_activities,id',
            'taken_date' => 'nullable|date',
            'gps_latitude' => 'nullable|numeric',
            'gps_longitude' => 'nullable|numeric',
        ]);

        $file = $request->file('photo');
        $originalFilename = $file->getClientOriginalName();
        
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos', $filename, 'public');

        // Create thumbnails
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        
        // Thumbnail (150x150)
        $thumbnail = $image->cover(150, 150);
        $thumbnailPath = 'photos/thumbnails/' . $filename;
        Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());
        
        // Medium (800x600)
        $medium = $image->scale(width: 800);
        $mediumPath = 'photos/medium/' . $filename;
        Storage::disk('public')->put($mediumPath, $medium->encode());

        // Extract EXIF data
        $exifData = @exif_read_data($file->getRealPath());
        
        $photoData = [
            'project_id' => $validated['project_id'],
            'album_id' => $validated['album_id'] ?? null,
            'original_filename' => $originalFilename,
            'file_path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'medium_path' => $mediumPath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'width' => $image->width(),
            'height' => $image->height(),
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? 'general',
            'location' => $validated['location'] ?? null,
            'work_area' => $validated['work_area'] ?? null,
            'activity_id' => $validated['activity_id'] ?? null,
            'taken_date' => $validated['taken_date'] ?? now()->toDateString(),
            'taken_time' => now()->toTimeString(),
            'gps_latitude' => $validated['gps_latitude'] ?? ($exifData['GPSLatitude'] ?? null),
            'gps_longitude' => $validated['gps_longitude'] ?? ($exifData['GPSLongitude'] ?? null),
            'camera_make' => $exifData['Make'] ?? null,
            'camera_model' => $exifData['Model'] ?? null,
            'orientation' => $exifData['Orientation'] ?? null,
            'uploaded_by_id' => Auth::id(),
            'taken_by_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
        ];

        $photo = Photo::create($photoData);

        // Update album photos count
        if ($photo->album_id) {
            $album = PhotoAlbum::find($photo->album_id);
            if ($album) {
                $album->increment('photos_count');
            }
        }

        return response()->json($photo, 201);
    }

    /**
     * Bulk upload photos
     */
    public function bulkUpload(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'album_id' => 'nullable|exists:photo_albums,id',
            'photos' => 'required|array',
            'photos.*' => 'image|max:10240',
        ]);

        $photos = [];
        foreach ($request->file('photos') as $file) {
            // Reuse single upload logic
            $req = new Request([
                'project_id' => $validated['project_id'],
                'album_id' => $validated['album_id'] ?? null,
            ]);
            $req->files->set('photo', $file);
            
            $response = $this->upload($req);
            $photos[] = json_decode($response->getContent(), true);
        }

        return response()->json(['photos' => $photos], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->upload($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $photo = Photo::with(['project', 'album', 'activity', 'uploadedBy', 'takenBy', 'annotations'])
            ->findOrFail($id);

        return response()->json($photo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|in:progress,quality,safety,defect,before,after,milestone,general',
            'location' => 'nullable|string',
            'work_area' => 'nullable|string',
            'activity_id' => 'nullable|exists:project_activities,id',
            'tags' => 'nullable|array',
            'is_private' => 'sometimes|boolean',
        ]);

        $photo->update($validated);

        return response()->json($photo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);
        
        // Delete files
        if ($photo->file_path) {
            Storage::disk('public')->delete($photo->file_path);
        }
        if ($photo->thumbnail_path) {
            Storage::disk('public')->delete($photo->thumbnail_path);
        }
        if ($photo->medium_path) {
            Storage::disk('public')->delete($photo->medium_path);
        }

        // Update album photos count
        if ($photo->album_id) {
            $album = PhotoAlbum::find($photo->album_id);
            if ($album) {
                $album->decrement('photos_count');
            }
        }

        $photo->delete();

        return response()->json(['message' => 'Photo deleted successfully'], 200);
    }

    /**
     * Approve photo
     */
    public function approve($id)
    {
        $photo = Photo::findOrFail($id);
        
        $photo->update([
            'approved' => true,
            'approved_by_id' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json($photo);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $photo = Photo::findOrFail($id);
        
        $photo->update([
            'is_featured' => !$photo->is_featured,
        ]);

        return response()->json($photo);
    }

    /**
     * Search photos
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $query = Photo::query();

        if (isset($validated['project_id'])) {
            $query->where('project_id', $validated['project_id']);
        }

        $photos = $query->where(function ($q) use ($validated) {
            $search = $validated['query'];
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%")
              ->orWhere('work_area', 'like', "%{$search}%");
        })->paginate(20);

        return response()->json($photos);
    }

    /**
     * Get photos by location
     */
    public function byLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric', // in meters
        ]);

        $radius = $validated['radius'] ?? 100;
        $lat = $validated['latitude'];
        $lng = $validated['longitude'];

        // Simple bounding box query
        $photos = Photo::whereNotNull('gps_latitude')
            ->whereNotNull('gps_longitude')
            ->whereBetween('gps_latitude', [$lat - 0.001 * $radius, $lat + 0.001 * $radius])
            ->whereBetween('gps_longitude', [$lng - 0.001 * $radius, $lng + 0.001 * $radius])
            ->paginate(20);

        return response()->json($photos);
    }

    /**
     * Get photos by date range
     */
    public function byDateRange(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $query = Photo::whereBetween('taken_date', [$validated['start_date'], $validated['end_date']]);

        if (isset($validated['project_id'])) {
            $query->where('project_id', $validated['project_id']);
        }

        $photos = $query->orderBy('taken_date', 'desc')->paginate(20);

        return response()->json($photos);
    }

    /**
     * Get photos by tag
     */
    public function byTag($tag)
    {
        $photos = Photo::whereJsonContains('tags', $tag)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($photos);
    }

    /**
     * Download photo
     */
    public function download($id)
    {
        $photo = Photo::findOrFail($id);
        
        $filePath = storage_path('app/public/' . $photo->file_path);
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->download($filePath, $photo->original_filename);
    }

    /**
     * Bulk download photos
     */
    public function bulkDownload(Request $request)
    {
        $validated = $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'exists:photos,id',
        ]);

        // This would typically create a ZIP file
        // For simplicity, returning the list of photos
        $photos = Photo::whereIn('id', $validated['photo_ids'])->get();

        return response()->json([
            'message' => 'Bulk download prepared',
            'photos' => $photos,
        ]);
    }
}
