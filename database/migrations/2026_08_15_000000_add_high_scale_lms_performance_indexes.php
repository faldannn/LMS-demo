<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance')) {
            Schema::table('attendance', function (Blueprint $table) {
                // Check if index exists or safely add composite index
                $table->index(['date', 'class_id', 'session'], 'attendance_date_class_session_idx');
                $table->index('teacher_id', 'attendance_teacher_id_idx');
            });
        }

        if (Schema::hasTable('class_subject')) {
            Schema::table('class_subject', function (Blueprint $table) {
                $table->index(['class_id', 'academic_year_id'], 'class_subject_class_year_idx');
                $table->index(['teacher_id', 'academic_year_id'], 'class_subject_teacher_year_idx');
            });
        }

        if (Schema::hasTable('schedules')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->index(['class_id', 'day_of_week'], 'schedules_class_day_idx');
                $table->index(['teacher_id', 'day_of_week'], 'schedules_teacher_day_idx');
            });
        }

        if (Schema::hasTable('teacher_attendances')) {
            Schema::table('teacher_attendances', function (Blueprint $table) {
                $table->index(['date', 'teacher_id'], 'teacher_attendances_date_teacher_idx');
                $table->index(['date', 'status'], 'teacher_attendances_date_status_idx');
            });
        }

        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->index(['class_subject_id', 'is_published'], 'assignments_cs_published_idx');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['role', 'status'], 'users_role_status_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attendance')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->dropIndex('attendance_date_class_session_idx');
                $table->dropIndex('attendance_teacher_id_idx');
            });
        }

        if (Schema::hasTable('class_subject')) {
            Schema::table('class_subject', function (Blueprint $table) {
                $table->dropIndex('class_subject_class_year_idx');
                $table->dropIndex('class_subject_teacher_year_idx');
            });
        }

        if (Schema::hasTable('schedules')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->dropIndex('schedules_class_day_idx');
                $table->dropIndex('schedules_teacher_day_idx');
            });
        }

        if (Schema::hasTable('teacher_attendances')) {
            Schema::table('teacher_attendances', function (Blueprint $table) {
                $table->dropIndex('teacher_attendances_date_teacher_idx');
                $table->dropIndex('teacher_attendances_date_status_idx');
            });
        }

        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropIndex('assignments_cs_published_idx');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_role_status_idx');
            });
        }
    }
};
