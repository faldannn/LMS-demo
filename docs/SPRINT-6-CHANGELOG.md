# Sprint 6 — Changelog: Modul Quiz CBT

**Tanggal:** 26 Juni 2026  
**Tujuan:** Ujian berbasis komputer (CBT) dengan bank soal scalable, acak soal/jawaban, timer, auto submit, auto scoring PG, koreksi manual essay, rekap & export nilai.

---

## Fitur

| Fitur | Implementasi |
|-------|--------------|
| Bank Soal | `question_banks` per `class_subject`, CRUD soal terpisah dari ujian |
| Pilihan Ganda | `type: multiple_choice` + `question_options` |
| Essay | `type: essay` + koreksi manual |
| Acak Soal | `shuffle_questions` + snapshot `quiz_attempt_questions` |
| Acak Jawaban | `shuffle_answers` + `option_order` JSON per attempt |
| Timer | `expires_at` + endpoint `time-remaining` |
| Auto Submit | JS timer + `auto-submit` saat waktu habis |
| Auto Scoring PG | `QuizService::calculateScore()` batch query |
| Manual Scoring Essay | `QuizGradingController` + `gradeEssay()` |
| Rekap Nilai | `quizzes.results` + statistik |
| Export Nilai | `QuizResultsExport` → Excel |

---

## Arsitektur Database (Scalable)

```
question_banks (1) ──< questions (N) ──< question_options
                         │
                         └──< quiz_questions >── quizzes
                                                    │
quiz_attempts ──< quiz_attempt_questions (snapshot soal per siswa)
              └──< student_answers
```

### Mengapa scalable untuk ribuan soal?

1. **Soal di bank, bukan di ujian** — bank bisa memuat ribuan soal; ujian hanya mereferensikan subset.
2. **`quiz_attempt_questions`** — saat siswa mulai, hanya N ID soal yang disalin ke attempt (bukan load seluruh bank).
3. **Paginasi bank** — daftar soal 20/halaman + filter tipe/kesulitan/search.
4. **Paginasi attempt** — `questions_per_page` (default 1) memuat sedikit soal per request.
5. **Index DB** — `(question_bank_id, type, is_active)`, `(quiz_attempt_id, display_order)`.
6. **Scoring batch** — `calculateScore()` memuat jawaban benar sekali query `whereIn`, bukan N+1 per soal.

### Mode pemilihan soal (`selection_mode`)

| Mode | Perilaku |
|------|----------|
| `fixed` | Soal dipilih manual via pivot `quiz_questions` |
| `random` | Acak N soal dari bank saat attempt dimulai |
| `all_bank` | Semua soal aktif di bank |

---

## Migration Baru

| File | Isi |
|------|-----|
| `2026_06_29_100000_create_question_banks_table` | Tabel bank soal |
| `2026_06_29_100001_enhance_questions_for_question_bank` | `question_bank_id`, `difficulty`, `is_active`, migrasi data lama |
| `2026_06_29_100002_create_quiz_questions_table` | Pivot quiz ↔ soal |
| `2026_06_29_100003_enhance_quizzes_for_cbt` | Acak, timer, mode seleksi, `pass_score` |
| `2026_06_29_100004_create_quiz_attempt_questions_table` | Snapshot soal per attempt |
| `2026_06_29_100005_enhance_quiz_attempts_for_cbt` | `expires_at`, `needs_grading`, kolom essay grading |

---

## Service Layer

| Service | Fungsi |
|---------|--------|
| `QuestionBankService` | CRUD bank & soal, paginasi, upload gambar |
| `QuizAttemptService` | Start attempt, generate soal, save answer, auto submit |
| `QuizService` | Attach/detach soal, scoring, rekap, grade essay |

---

## Routes Baru

```
GET  class-subjects/{classSubject}/question-banks     → question-banks.index
POST class-subjects/{classSubject}/question-banks     → question-banks.store
GET  question-banks/{questionBank}                      → question-banks.show
POST question-banks/{questionBank}/questions            → questions.store
POST quizzes/{quiz}/attach-questions                    → quizzes.attach-questions
GET  quizzes/{quiz}/results/export                      → quizzes.results.export
GET  quizzes/{quiz}/grade/{attempt}                     → quizzes.grade
POST quiz-answers/{answer}/grade                        → quizzes.grade-essay
GET  student/quizzes/{quiz}/time-remaining              → student.quizzes.time-remaining
POST student/quizzes/{quiz}/auto-submit                 → student.quizzes.auto-submit
```

---

## Alur Guru

1. E-Learning → **Bank Soal** → buat bank → tambah soal PG/Essay
2. **Buat Ujian CBT** → pilih bank, mode soal, acak, durasi
3. Mode `fixed` → kelola ujian → centang soal dari bank
4. Publikasikan → **Rekap Nilai** → koreksi essay → **Export Excel**

## Alur Siswa

1. Kuis & Ujian → mulai → timer aktif
2. Jawaban auto-save via AJAX
3. Waktu habis → auto submit (jika diaktifkan)
4. Lihat hasil (jika `show_results` aktif)

---

## Testing

`tests/Feature/CbtQuizTest.php` — 4 skenario:
- Buat soal PG di bank
- Mode acak menghasilkan snapshot attempt
- Auto scoring pilihan ganda 100%
- Export rekap nilai Excel

```bash
php artisan test --filter=CbtQuizTest
php artisan test   # 42 tests
```

---

## File Utama

| Kategori | Path |
|----------|------|
| Models | `QuestionBank`, `QuizAttemptQuestion` |
| Controllers | `QuestionBankController`, `QuizGradingController` |
| Export | `app/Exports/QuizResultsExport.php` |
| Views | `question-banks/*`, `quizzes/grade.blade.php`, `student-quizzes/attempt.blade.php` |
| Policy | `QuestionBankPolicy` |
