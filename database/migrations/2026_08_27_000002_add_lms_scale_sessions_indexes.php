<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            "CREATE INDEX sessions_last_activity_user_idx ON sessions (last_activity, user_id)",
            "CREATE INDEX attendance_details_att_student_idx ON attendance_details (attendance_id, student_id)",
            "CREATE INDEX materials_cs_published_idx ON materials (class_subject_id, is_published)",
            "CREATE INDEX comments_thread_created_idx ON comments (discussion_thread_id, created_at)",
            "CREATE INDEX discussion_threads_cs_created_idx ON discussion_threads (class_subject_id, created_at)",
        ];

        foreach ($indexes as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {
                // Table might not exist or index already created
            }
        }
    }

    public function down(): void
    {
    }
};
