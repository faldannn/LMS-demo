<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('academic_years', 'description')) {
            Schema::table('academic_years', function (Blueprint $table) {
                $table->text('description')->nullable()->after('year');
            });
        }

        if (Schema::hasTable('semesters') && Schema::hasColumn('academic_years', 'semester')) {
            $rows = DB::table('academic_years')->orderBy('id')->get();
            $canonical = [];

            foreach ($rows as $row) {
                if (!isset($canonical[$row->year])) {
                    $canonical[$row->year] = $row->id;
                }

                DB::table('semesters')->updateOrInsert(
                    ['academic_year_id' => $canonical[$row->year], 'number' => $row->semester],
                    [
                        'start_date' => $row->start_date,
                        'end_date' => $row->end_date,
                        'is_active' => (bool) $row->is_active,
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => now(),
                    ]
                );

                if ($row->id !== $canonical[$row->year]) {
                    foreach (['classes', 'schedules', 'class_subject', 'report_cards'] as $table) {
                        if (Schema::hasTable($table) && Schema::hasColumn($table, 'academic_year_id')) {
                            DB::table($table)->where('academic_year_id', $row->id)
                                ->update(['academic_year_id' => $canonical[$row->year]]);
                        }
                    }
                    DB::table('academic_years')->where('id', $row->id)->delete();
                }
            }

            Schema::table('academic_years', function (Blueprint $table) {
                $table->dropColumn(['semester', 'start_date', 'end_date']);
            });
        }

        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'major_id')) {
                $table->foreignId('major_id')->nullable()->after('grade')
                    ->constrained('majors')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('classes', 'major')) {
            $majors = DB::table('classes')->whereNotNull('major')->distinct()->pluck('major');
            foreach ($majors as $name) {
                if (!$name) {
                    continue;
                }
                $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 10));
                DB::table('majors')->updateOrInsert(
                    ['code' => $code],
                    ['name' => $name, 'status' => true, 'created_at' => now(), 'updated_at' => now()]
                );
                $majorId = DB::table('majors')->where('code', $code)->value('id');
                DB::table('classes')->where('major', $name)->update(['major_id' => $majorId]);
            }

            Schema::table('classes', function (Blueprint $table) {
                $table->dropColumn('major');
            });
        }
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'major')) {
                $table->string('major', 50)->nullable()->after('grade');
            }
            if (Schema::hasColumn('classes', 'major_id')) {
                $table->dropConstrainedForeignId('major_id');
            }
        });

        Schema::table('academic_years', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_years', 'semester')) {
                $table->tinyInteger('semester')->default(1)->after('year');
                $table->date('start_date')->nullable()->after('semester');
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (Schema::hasColumn('academic_years', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
