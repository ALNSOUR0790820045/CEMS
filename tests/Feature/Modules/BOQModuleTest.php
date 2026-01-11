<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BOQModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test BOQ tables exist
     */
    public function test_boq_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('boq_items'));
        $this->assertTrue(Schema::hasTable('boq_headers'));
        $this->assertTrue(Schema::hasTable('boq_sections'));
        $this->assertTrue(Schema::hasTable('boq_item_resources'));
        $this->assertTrue(Schema::hasTable('boq_revisions'));
    }

    /**
     * Test boq_headers has correct columns
     */
    public function test_boq_headers_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('boq_headers', [
            'id',
            'boq_number',
            'name',
            'description',
            'boqable_type',
            'boqable_id',
            'type',
            'status',
            'version',
            'currency',
            'total_amount',
            'markup_percentage',
            'discount_percentage',
            'final_amount',
            'created_by',
            'approved_by',
            'approved_at',
            'notes',
            'created_at',
            'updated_at',
            'deleted_at',
        ]));
    }

    /**
     * Test boq_sections has correct columns
     */
    public function test_boq_sections_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('boq_sections', [
            'id',
            'boq_header_id',
            'code',
            'name',
            'name_en',
            'description',
            'sort_order',
            'total_amount',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test boq_items has correct columns
     */
    public function test_boq_items_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('boq_items', [
            'id',
            'project_id',
            'wbs_id',
            'item_code',
            'description',
            'unit',
            'quantity',
            'unit_price',
            'total_price',
            'sort_order',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test boq_item_resources has correct columns
     */
    public function test_boq_item_resources_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('boq_item_resources', [
            'id',
            'boq_item_id',
            'resource_type',
            'resource_id',
            'resource_name',
            'unit',
            'quantity_per_unit',
            'unit_cost',
            'total_cost',
            'wastage_percentage',
            'notes',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test BOQ foreign keys
     */
    public function test_boq_foreign_keys(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_sections', 'boq_header_id'));
        $this->assertTrue(Schema::hasColumn('boq_items', 'project_id'));
        $this->assertTrue(Schema::hasColumn('boq_items', 'wbs_id'));
        $this->assertTrue(Schema::hasColumn('boq_item_resources', 'boq_item_id'));
    }

    /**
     * Test BOQ unique constraints
     */
    public function test_boq_unique_constraints(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_headers', 'boq_number'));
        $this->assertTrue(Schema::hasColumn('boq_items', 'item_code'));
    }

    /**
     * Test BOQ polymorphic relationship fields
     */
    public function test_boq_headers_has_polymorphic_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_headers', 'boqable_type'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'boqable_id'));
    }

    /**
     * Test BOQ status and type enums
     */
    public function test_boq_headers_has_status_and_type(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_headers', 'status'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'type'));
    }

    /**
     * Test boq_item_resources has resource_type enum
     */
    public function test_boq_item_resources_has_resource_type(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_item_resources', 'resource_type'));
    }

    /**
     * Test BOQ soft deletes
     */
    public function test_boq_headers_has_soft_deletes(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_headers', 'deleted_at'));
    }
}
