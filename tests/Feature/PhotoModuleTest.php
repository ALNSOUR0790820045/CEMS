<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Models\PhotoAlbum;
use App\Models\Photo;
use App\Models\PhotoComparison;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'email' => 'test@company.com',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->project = Project::create([
            'project_code' => 'PRJ-001',
            'name' => 'Test Project',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_create_photo_album(): void
    {
        $albumData = [
            'project_id' => $this->project->id,
            'name' => 'Test Album',
            'name_en' => 'Test Album EN',
            'description' => 'Test Description',
            'album_type' => 'progress',
            'created_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ];

        $album = PhotoAlbum::create($albumData);

        $this->assertDatabaseHas('photo_albums', [
            'name' => 'Test Album',
            'album_type' => 'progress',
        ]);

        $this->assertNotNull($album->album_number);
        $this->assertStringStartsWith('ALB-', $album->album_number);
    }

    public function test_can_create_photo(): void
    {
        Storage::fake('public');

        $album = PhotoAlbum::create([
            'project_id' => $this->project->id,
            'name' => 'Test Album',
            'album_type' => 'progress',
            'created_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $photoData = [
            'project_id' => $this->project->id,
            'album_id' => $album->id,
            'original_filename' => 'test.jpg',
            'file_path' => 'photos/test.jpg',
            'title' => 'Test Photo',
            'description' => 'Test Description',
            'category' => 'progress',
            'uploaded_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ];

        $photo = Photo::create($photoData);

        $this->assertDatabaseHas('photos', [
            'title' => 'Test Photo',
            'category' => 'progress',
        ]);

        $this->assertNotNull($photo->photo_number);
        $this->assertStringStartsWith('PHT-', $photo->photo_number);
    }

    public function test_can_create_photo_comparison(): void
    {
        $album = PhotoAlbum::create([
            'project_id' => $this->project->id,
            'name' => 'Test Album',
            'album_type' => 'progress',
            'created_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $beforePhoto = Photo::create([
            'project_id' => $this->project->id,
            'album_id' => $album->id,
            'original_filename' => 'before.jpg',
            'file_path' => 'photos/before.jpg',
            'title' => 'Before Photo',
            'category' => 'before',
            'uploaded_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $afterPhoto = Photo::create([
            'project_id' => $this->project->id,
            'album_id' => $album->id,
            'original_filename' => 'after.jpg',
            'file_path' => 'photos/after.jpg',
            'title' => 'After Photo',
            'category' => 'after',
            'uploaded_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $comparisonData = [
            'project_id' => $this->project->id,
            'name' => 'Before/After Comparison',
            'description' => 'Test Comparison',
            'before_photo_id' => $beforePhoto->id,
            'after_photo_id' => $afterPhoto->id,
            'created_by_id' => $this->user->id,
        ];

        $comparison = PhotoComparison::create($comparisonData);

        $this->assertDatabaseHas('photo_comparisons', [
            'name' => 'Before/After Comparison',
        ]);

        $this->assertNotNull($comparison->comparison_number);
        $this->assertStringStartsWith('CMP-', $comparison->comparison_number);
    }

    public function test_album_number_is_auto_generated(): void
    {
        $album1 = PhotoAlbum::create([
            'project_id' => $this->project->id,
            'name' => 'Album 1',
            'album_type' => 'progress',
            'created_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $album2 = PhotoAlbum::create([
            'project_id' => $this->project->id,
            'name' => 'Album 2',
            'album_type' => 'progress',
            'created_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $this->assertNotEquals($album1->album_number, $album2->album_number);
        $this->assertStringStartsWith('ALB-' . date('Y'), $album1->album_number);
        $this->assertStringStartsWith('ALB-' . date('Y'), $album2->album_number);
    }

    public function test_photo_relationships_work(): void
    {
        $album = PhotoAlbum::create([
            'project_id' => $this->project->id,
            'name' => 'Test Album',
            'album_type' => 'progress',
            'created_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $photo = Photo::create([
            'project_id' => $this->project->id,
            'album_id' => $album->id,
            'original_filename' => 'test.jpg',
            'file_path' => 'photos/test.jpg',
            'title' => 'Test Photo',
            'uploaded_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $this->assertEquals($album->id, $photo->album->id);
        $this->assertEquals($this->project->id, $photo->project->id);
        $this->assertEquals($this->user->id, $photo->uploadedBy->id);
    }
}
