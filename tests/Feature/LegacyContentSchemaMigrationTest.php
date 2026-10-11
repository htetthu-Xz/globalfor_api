<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyContentSchemaMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_obsolete_language_columns_are_removed_from_existing_content_tables(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('language', 8)->default('en');
        });

        Schema::table('news_items', function (Blueprint $table) {
            $table->string('language', 8)->default('en');
        });

        $this->assertTrue(Schema::hasColumn('activities', 'language'));
        $this->assertTrue(Schema::hasColumn('news_items', 'language'));

        $migration = require database_path('migrations/2026_10_10_000500_remove_language_fields_from_content_tables.php');
        $migration->up();

        $this->assertFalse(Schema::hasColumn('activities', 'language'));
        $this->assertFalse(Schema::hasColumn('news_items', 'language'));
    }
}
