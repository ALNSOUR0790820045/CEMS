<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Document;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test document creation.
     */
    public function test_can_create_document(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $this->actingAs($user, 'sanctum');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/documents', [
            'document_name' => 'Test Document',
            'document_type' => 'contract',
            'category' => 'Legal',
            'description' => 'A test document',
            'tags' => ['test', 'contract'],
            'is_confidential' => false,
            'file' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'document' => [
                    'id',
                    'document_number',
                    'document_name',
                    'document_type',
                    'category',
                    'file_path',
                ],
            ]);

        $this->assertDatabaseHas('documents', [
            'document_name' => 'Test Document',
            'document_type' => 'contract',
        ]);
    }

    /**
     * Test document listing.
     */
    public function test_can_list_documents(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        Document::factory()->count(3)->create([
            'company_id' => $company->id,
            'uploaded_by_id' => $user->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/documents');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'document_number',
                        'document_name',
                        'document_type',
                    ],
                ],
            ]);
    }

    /**
     * Test document search.
     */
    public function test_can_search_documents(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        Document::factory()->create([
            'document_name' => 'Important Contract',
            'company_id' => $company->id,
            'uploaded_by_id' => $user->id,
        ]);

        Document::factory()->create([
            'document_name' => 'Regular Document',
            'company_id' => $company->id,
            'uploaded_by_id' => $user->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/documents/search?q=Important');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }
}
