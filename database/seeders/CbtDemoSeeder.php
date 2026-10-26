<?php

namespace Database\Seeders;

use App\Models\ClassSubject;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class CbtDemoSeeder extends Seeder
{
    public function run(): void
    {
        $classSubject = ClassSubject::first();
        if (!$classSubject) {
            return;
        }

        // 1. Paket Ujian CBT Matematika & Sains
        $quizMath = Quiz::updateOrCreate(
            ['title' => 'PAS Matematika & Sains Terpadu (CBT Demo)'],
            [
                'class_subject_id' => $classSubject->id,
                'description' => 'Ujian Akhir Semester dengan dukungan rumus Matematika, Fisika, dan Kimia KaTeX LaTeX. Siswa wajib memasukkan token ujian 5-menit yang diberikan pengawas.',
                'duration' => 60,
                'pass_score' => 75,
                'is_published' => true,
                'show_results' => true,
                'shuffle_questions' => false,
                'shuffle_answers' => false,
                'auto_submit_on_timeout' => true,
                'questions_per_page' => 1,
            ]
        );

        $this->seedMathQuestions($quizMath);

        // 2. Paket Ujian CBT Bahasa Arab & Studi Islam
        $quizArabic = Quiz::updateOrCreate(
            ['title' => 'Ujian Bahasa Arab & Studi Islam (CBT Demo)'],
            [
                'class_subject_id' => $classSubject->id,
                'description' => 'Ujian Bahasa Arab dengan dukungan teks Arab RTL (Right-to-Left) dan font Amiri. Masukkan token ujian sebelum pengerjaan.',
                'duration' => 45,
                'pass_score' => 75,
                'is_published' => true,
                'show_results' => true,
                'shuffle_questions' => false,
                'shuffle_answers' => false,
                'auto_submit_on_timeout' => true,
                'questions_per_page' => 1,
            ]
        );

        $this->seedArabicQuestions($quizArabic);
    }

    protected function seedMathQuestions(Quiz $quiz): void
    {
        if ($quiz->questions()->count() > 0) {
            return;
        }

        $questionsData = [
            [
                'text' => "Tentukan nilai limit fungsi aljabar berikut:\n$$\\lim_{x \\to 0} \\frac{\\sin(3x)}{x}$$",
                'type' => 'multiple_choice',
                'points' => 20,
                'options' => [
                    ['text' => '$0$', 'is_correct' => false],
                    ['text' => '$1$', 'is_correct' => false],
                    ['text' => '$3$', 'is_correct' => true],
                    ['text' => '$\\frac{1}{3}$', 'is_correct' => false],
                    ['text' => '$\\infty$', 'is_correct' => false],
                ],
            ],
            [
                'text' => "Akar-akar persamaan kuadrat \$2x^2 - 6x + 4 = 0\$ dapat dihitung dengan rumus abc:\n\$\$x = \\frac{-b \\pm \\sqrt{b^2 - 4ac}}{2a}\$\$\nBerapakah himpunan penyelesaian nilai \$x\$ tersebut?",
                'type' => 'multiple_choice',
                'points' => 20,
                'options' => [
                    ['text' => '$\\{1, 2\\}$', 'is_correct' => true],
                    ['text' => '$\\{2, 3\\}$', 'is_correct' => false],
                    ['text' => '$\\{-1, -2\\}$', 'is_correct' => false],
                    ['text' => '$\\{0, 2\\}$', 'is_correct' => false],
                    ['text' => '$\\{1, 4\\}$', 'is_correct' => false],
                ],
            ],
            [
                'text' => "Sebuah balok bermassa \$m = 5\\text{ kg}\$ ditarik dengan gaya \$F = 20\\text{ N}\$ di atas lantai licin tanpa gesekan. Menurut Hukum II Newton:\n\$\$\\Sigma F = m \\cdot a\$\$\nPercepatan (\$a\$) yang dialami balok tersebut adalah ...",
                'type' => 'multiple_choice',
                'points' => 20,
                'options' => [
                    ['text' => '$2\\text{ m/s}^2$', 'is_correct' => false],
                    ['text' => '$4\\text{ m/s}^2$', 'is_correct' => true],
                    ['text' => '$10\\text{ m/s}^2$', 'is_correct' => false],
                    ['text' => '$15\\text{ m/s}^2$', 'is_correct' => false],
                    ['text' => '$100\\text{ m/s}^2$', 'is_correct' => false],
                ],
            ],
            [
                'text' => "Reaksi pembakaran gas metana berlangsung menurut persamaan setara berikut:\n\$\$\\text{CH}_4 + 2\\text{O}_2 \\rightarrow \\text{CO}_2 + 2\\text{H}_2\\text{O}\$\$\nBerapa mol gas \$\\text{O}_2\$ yang diperlukan untuk membakar tepat \$1\\text{ mol}\$ gas \$\\text{CH}_4\$?",
                'type' => 'multiple_choice',
                'points' => 20,
                'options' => [
                    ['text' => '$1\\text{ mol}$', 'is_correct' => false],
                    ['text' => '$2\\text{ mol}$', 'is_correct' => true],
                    ['text' => '$3\\text{ mol}$', 'is_correct' => false],
                    ['text' => '$4\\text{ mol}$', 'is_correct' => false],
                    ['text' => '$0.5\\text{ mol}$', 'is_correct' => false],
                ],
            ],
            [
                'text' => "Hitunglah nilai integral tentu berikut:\n\$\$\\int_0^2 (3x^2 + 2x) dx\$\$\nTuliskan langkah pengerjaan dan hasil akhirnya!",
                'type' => 'essay',
                'points' => 20,
                'options' => [],
            ],
        ];

        foreach ($questionsData as $idx => $qData) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $qData['text'],
                'type' => $qData['type'],
                'difficulty' => 'medium',
                'points' => $qData['points'],
                'order_number' => $idx + 1,
                'is_active' => true,
            ]);

            foreach ($qData['options'] as $optIdx => $optData) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optData['text'],
                    'is_correct' => $optData['is_correct'],
                    'order_number' => $optIdx + 1,
                ]);
            }
        }
    }

    protected function seedArabicQuestions(Quiz $quiz): void
    {
        if ($quiz->questions()->count() > 0) {
            return;
        }

        $arabicData = [
            [
                'text' => "Perhatikan mahfudzot berikut:\nمَنْ سَارَ عَلَى الدَّرْبِ وَصَلَ\nApakah arti yang paling tepat dari kalimat hikmah di atas?",
                'type' => 'multiple_choice',
                'points' => 25,
                'options' => [
                    ['text' => 'Barangsiapa bersabar maka ia akan beruntung', 'is_correct' => false],
                    ['text' => 'Barangsiapa berjalan pada jalurnya maka sampailah ia', 'is_correct' => true],
                    ['text' => 'Waktu itu lebih berharga daripada emas', 'is_correct' => false],
                    ['text' => 'Sebaik-baik teman adalah buku', 'is_correct' => false],
                    ['text' => 'Ilmu itu bagaikan cahaya penerang', 'is_correct' => false],
                ],
            ],
            [
                'text' => "Lengkapilah kalimat rumpang berikut dengan kata ganti (dhamir) yang tepat:\nفَاطِمَةُ ... الدَّرْسَ فِي الْفَصْلِ",
                'type' => 'multiple_choice',
                'points' => 25,
                'options' => [
                    ['text' => 'يَكْتُبُ', 'is_correct' => false],
                    ['text' => 'تَكْتُبُ', 'is_correct' => true],
                    ['text' => 'كَتَبُوا', 'is_correct' => false],
                    ['text' => 'أَكْتُبُ', 'is_correct' => false],
                    ['text' => 'نَكْتُبُ', 'is_correct' => false],
                ],
            ],
            [
                'text' => "Kalimat berikut ini: الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ termasuk ke dalam jumlah ...",
                'type' => 'multiple_choice',
                'points' => 25,
                'options' => [
                    ['text' => 'Jumlah Ismiyyah (جُمْلَة اسْمِيَّة)', 'is_correct' => true],
                    ['text' => 'Jumlah Fi\'liyyah (جُمْلَة فِعْلِيَّة)', 'is_correct' => false],
                    ['text' => 'Jumlah Syarthiyyah', 'is_correct' => false],
                    ['text' => 'Jumlah Nida\'iyyah', 'is_correct' => false],
                    ['text' => 'Jumlah Istifhamiyyah', 'is_correct' => false],
                ],
            ],
            [
                'text' => "Terjemahkanlah kalimat bahasa Arab berikut ke dalam bahasa Indonesia yang baik dan benar:\nطَلَبُ الْعِلْمِ فَرِيضَةٌ عَلَى كُلِّ مُسْلِمٍ",
                'type' => 'essay',
                'points' => 25,
                'options' => [],
            ],
        ];

        foreach ($arabicData as $idx => $qData) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $qData['text'],
                'type' => $qData['type'],
                'difficulty' => 'medium',
                'points' => $qData['points'],
                'order_number' => $idx + 1,
                'is_active' => true,
            ]);

            foreach ($qData['options'] as $optIdx => $optData) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optData['text'],
                    'is_correct' => $optData['is_correct'],
                    'order_number' => $optIdx + 1,
                ]);
            }
        }
    }
}
