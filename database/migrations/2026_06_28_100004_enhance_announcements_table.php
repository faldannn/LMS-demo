<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('attachment_path', 500)->nullable()->after('content');
            $table->string('attachment_name', 255)->nullable()->after('attachment_path');
            $table->unsignedInteger('attachment_size')->nullable()->after('attachment_name');
            $table->string('attachment_mime', 100)->nullable()->after('attachment_size');
            $table->boolean('is_published')->default(true)->after('created_by');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn([
                'attachment_path', 'attachment_name', 'attachment_size',
                'attachment_mime', 'is_published', 'published_at',
            ]);
        });
    }
};
