<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('activities', 'language')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropColumn('language');
            });
        }

        if (Schema::hasColumn('news_items', 'language')) {
            Schema::table('news_items', function (Blueprint $table) {
                $table->dropColumn('language');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('activities', 'language')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->string('language', 8)->default('en')->after('body');
            });
        }

        if (! Schema::hasColumn('news_items', 'language')) {
            Schema::table('news_items', function (Blueprint $table) {
                $table->string('language', 8)->default('en')->after('body');
            });
        }
    }
};
