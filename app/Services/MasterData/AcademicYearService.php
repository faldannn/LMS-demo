<?php

namespace App\Services\MasterData;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Semester;
use App\Repositories\AcademicYearRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AcademicYearService extends BaseMasterDataService
{
    public function __construct(AcademicYearRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['is_active'])) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            return parent::create($data);
        });
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {
            if (!empty($data['is_active'])) {
                AcademicYear::query()->where('id', '!=', $model->id)->update(['is_active' => false]);
            }

            return parent::update($model, $data);
        });
    }

    public function delete(Model $model): bool
    {
        /** @var AcademicYear $model */
        if ($model->classes()->count() > 0) {
            throw new \RuntimeException('Tidak dapat menghapus tahun ajaran yang masih memiliki kelas.');
        }

        return parent::delete($model);
    }

    public function duplicateClasses(int $fromYearId, int $toYearId, bool $copyHomeroom = true): array
    {
        return DB::transaction(function () use ($fromYearId, $toYearId, $copyHomeroom) {
            $sourceClasses = Classes::where('academic_year_id', $fromYearId)->get();
            if ($sourceClasses->isEmpty()) {
                throw new \RuntimeException('Tahun ajaran sumber tidak memiliki data kelas untuk disalin.');
            }

            $existingClassNames = Classes::where('academic_year_id', $toYearId)->pluck('name')->all();

            $created = 0;
            $skipped = 0;

            foreach ($sourceClasses as $sc) {
                if (in_array($sc->name, $existingClassNames, true)) {
                    $skipped++;
                    continue;
                }

                Classes::create([
                    'name' => $sc->name,
                    'grade' => $sc->grade,
                    'major_id' => $sc->major_id,
                    'homeroom_teacher_id' => $copyHomeroom ? $sc->homeroom_teacher_id : null,
                    'academic_year_id' => $toYearId,
                    'room' => $sc->room,
                    'max_students' => $sc->max_students ?? 36,
                    'session' => $sc->session ?? 'pagi',
                ]);

                $existingClassNames[] = $sc->name;
                $created++;
            }

            return [
                'created' => $created,
                'skipped' => $skipped,
                'total_source' => $sourceClasses->count(),
            ];
        });
    }
}
