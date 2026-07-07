<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    if (Schema::hasTable('attendance_details')) {
      Schema::table('attendance_details', function (Blueprint $table) {
        $table->index(['student_id', 'status'], 'attendance_details_student_status_idx');
        $table->index('attendance_id', 'attendance_details_attendance_idx');
      });
    }

    if (Schema::hasTable('students')) {
      Schema::table('students', function (Blueprint $table) {
        $table->index(['class_id', 'status'], 'students_class_status_idx');
      });
    }

    if (Schema::hasTable('quiz_attempts')) {
      Schema::table('quiz_attempts', function (Blueprint $table) {
        $table->index(['student_id', 'created_at'], 'quiz_attempts_student_created_idx');
        $table->index(['quiz_id', 'status'], 'quiz_attempts_quiz_status_idx');
      });
    }

    if (Schema::hasTable('assignment_submissions')) {
      Schema::table('assignment_submissions', function (Blueprint $table) {
        $table->index(['student_id', 'submitted_at'], 'assignment_submissions_student_submitted_idx');
      });
    }

    if (Schema::hasTable('materials')) {
      Schema::table('materials', function (Blueprint $table) {
        $table->index(['class_subject_id', 'created_at'], 'materials_class_subject_created_idx');
      });
    }

    if (Schema::hasTable('self_attendance_logs')) {
      Schema::table('self_attendance_logs', function (Blueprint $table) {
        $table->index(['student_id', 'attendance_date'], 'self_attendance_student_date_idx');
      });
    }

    if (Schema::hasTable('sessions')) {
      Schema::table('sessions', function (Blueprint $table) {
        $table->index('last_activity', 'sessions_last_activity_idx');
      });
    }

    if (Schema::hasTable('report_cards')) {
      Schema::table('report_cards', function (Blueprint $table) {
        $table->index(['academic_year_id', 'semester'], 'report_cards_year_semester_idx');
      });
    }
  }

  public function down(): void
  {
    $drops = [
      'attendance_details' => ['attendance_details_student_status_idx', 'attendance_details_attendance_idx'],
      'students' => ['students_class_status_idx'],
      'quiz_attempts' => ['quiz_attempts_student_created_idx', 'quiz_attempts_quiz_status_idx'],
      'assignment_submissions' => ['assignment_submissions_student_submitted_idx'],
      'materials' => ['materials_class_subject_created_idx'],
      'self_attendance_logs' => ['self_attendance_student_date_idx'],
      'sessions' => ['sessions_last_activity_idx'],
      'report_cards' => ['report_cards_year_semester_idx'],
    ];

    foreach ($drops as $table => $indexes) {
      if (!Schema::hasTable($table)) {
        continue;
      }
      Schema::table($table, function (Blueprint $table) use ($indexes) {
        foreach ($indexes as $index) {
          $table->dropIndex($index);
        }
      });
    }
  }
};
