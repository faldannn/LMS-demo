<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'registration_type')) {
                $table->string('registration_type', 20)->default('baru')->after('address');
            }
            if (!Schema::hasColumn('students', 'entry_date')) {
                $table->date('entry_date')->nullable()->after('registration_type');
            }
            if (!Schema::hasColumn('students', 'origin_school')) {
                $table->string('origin_school', 150)->nullable()->after('entry_date');
            }
            if (!Schema::hasColumn('students', 'student_status')) {
                $table->string('student_status', 30)->default('aktif')->after('status');
            }
            if (!Schema::hasColumn('students', 'exit_date')) {
                $table->date('exit_date')->nullable()->after('student_status');
            }
            if (!Schema::hasColumn('students', 'exit_reason')) {
                $table->string('exit_reason', 255)->nullable()->after('exit_date');
            }
            if (!Schema::hasColumn('students', 'destination_school')) {
                $table->string('destination_school', 150)->nullable()->after('exit_reason');
            }
        });

        if (!Schema::hasTable('student_academic_histories')) {
            Schema::create('student_academic_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
                $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
                $table->string('status', 30)->default('aktif'); // aktif, naik_kelas, tinggal_kelas, lulus, mutasi_keluar, drop_out
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'academic_year_id'], 'uq_student_academic_year');
                $table->index(['academic_year_id', 'class_id'], 'idx_ay_class');
                $table->index(['student_id', 'status'], 'idx_student_status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_academic_histories');

        Schema::table('students', function (Blueprint $table) {
            $cols = [
                'registration_type',
                'entry_date',
                'origin_school',
                'student_status',
                'exit_date',
                'exit_reason',
                'destination_school',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
