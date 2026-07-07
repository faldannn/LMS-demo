<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;

class ReportFilter
{
    public function __construct(
        public ?int $academicYearId = null,
        public ?int $semesterId = null,
        public ?int $classId = null,
        public ?int $subjectId = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $filter = new self(
            academicYearId: $request->integer('academic_year_id') ?: null,
            semesterId: $request->integer('semester_id') ?: null,
            classId: $request->integer('class_id') ?: null,
            subjectId: $request->integer('subject_id') ?: null,
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
        );

        $filter->resolveDateRange();

        return $filter;
    }

    public function toQueryArray(): array
    {
        return [
            'academic_year_id' => $this->academicYearId,
            'semester_id' => $this->semesterId,
            'class_id' => $this->classId,
            'subject_id' => $this->subjectId,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];
    }

    public function resolveDateRange(): void
    {
        if ($this->dateFrom && $this->dateTo) {
            return;
        }

        if ($this->semesterId) {
            $semester = Semester::find($this->semesterId);
            if ($semester) {
                $this->dateFrom ??= $semester->start_date?->toDateString();
                $this->dateTo ??= $semester->end_date?->toDateString();
                $this->academicYearId ??= $semester->academic_year_id;

                return;
            }
        }

        if ($this->academicYearId && !$this->dateFrom) {
            $semesters = Semester::where('academic_year_id', $this->academicYearId)->get();
            if ($semesters->isNotEmpty()) {
                $this->dateFrom ??= $semesters->min(fn ($s) => $s->start_date?->toDateString());
                $this->dateTo ??= $semesters->max(fn ($s) => $s->end_date?->toDateString());
            }
        }

        $this->dateFrom ??= now()->startOfMonth()->toDateString();
        $this->dateTo ??= now()->toDateString();
    }

    public function semesterLabel(): ?string
    {
        if (!$this->semesterId) {
            return null;
        }

        return Semester::find($this->semesterId)?->full_label;
    }

    public function periodLabel(): string
    {
        $parts = array_filter([
            AcademicYear::find($this->academicYearId)?->year,
            $this->semesterLabel(),
            Classes::find($this->classId)?->name ? 'Kelas ' . Classes::find($this->classId)->name : null,
            Subject::find($this->subjectId)?->name,
            $this->dateFrom && $this->dateTo
                ? date('d/m/Y', strtotime($this->dateFrom)) . ' — ' . date('d/m/Y', strtotime($this->dateTo))
                : null,
        ]);

        return implode(' · ', $parts) ?: 'Semua periode';
    }

    public function mapSemesterToReportCard(): ?string
    {
        if (!$this->semesterId) {
            return null;
        }

        $number = Semester::find($this->semesterId)?->number;

        return match ($number) {
            1 => 'ganjil',
            2 => 'genap',
            default => null,
        };
    }
}
