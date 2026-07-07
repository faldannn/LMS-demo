<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'youtube_id')) {
                $table->string('youtube_id', 20)->nullable()->after('url');
            }
            if (!Schema::hasColumn('materials', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('class_subject_id')->constrained('users')->nullOnDelete();
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE materials MODIFY type VARCHAR(20) NOT NULL DEFAULT 'pdf'");
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
            $rows = DB::table('materials')->get();
            Schema::drop('materials');
            Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_subject_id')->constrained('class_subject')->cascadeOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->string('type', 20)->default('pdf');
                $table->string('file_path', 500)->nullable();
                $table->string('url', 500)->nullable();
                $table->string('youtube_id', 20)->nullable();
                $table->string('file_name', 255)->nullable();
                $table->unsignedInteger('file_size')->nullable();
                $table->string('mime_type', 100)->nullable();
                $table->unsignedSmallInteger('order')->default(0);
                $table->string('topic', 100)->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                $table->index(['class_subject_id', 'topic']);
                $table->index(['class_subject_id', 'is_published']);
            });
            foreach ($rows as $row) {
                $data = (array) $row;
                $data['type'] = ($data['type'] ?? 'pdf') === 'document' ? 'pdf' : ($data['type'] ?? 'pdf');
                unset($data['id']);
                DB::table('materials')->insert($data);
            }
            DB::statement('PRAGMA foreign_keys=ON');
        } elseif (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::table('materials')->where('type', 'document')->update(['type' => 'pdf']);
        }
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'youtube_id')) {
                $table->dropColumn('youtube_id');
            }
            if (Schema::hasColumn('materials', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });
    }
};
