<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InquiryController extends Controller
{
    private const BIRTH_DATE = '1982-08-05';
    private const BIRTHDAY = '1982年8月5日';
    private const PROFILE_PAGE_URL = 'https://example.com/profile';
    private const SUMMARY_FILENAME = 'MY_SKILL_SHEETS_summary.pdf';
    private const CAREER_FILENAME = 'MY_SKILL_SHEETS_career.pdf';

    /**
     * 照会画面。描画は public/js/inquiry.js が JSON から行う。
     */
    public function index()
    {
        $sheet = $this->sheetData();

        $profile = $sheet['career']['profile'] ?? [];

        return view('inquiry', [
            'career' => $sheet['career'],
            'skillSummary' => $sheet['skillSummary'],
            'birthdayLabel' => $this->birthdayLabel($profile['birthday'] ?? null),
            'admin' => session('admin') === true,
        ]);
    }

    /**
     * パスワードが一致したときだけ、セッションを管理モードにする。
     */
    public function unlock(Request $request)
    {
        $password = (string) $request->input('password', '');
        $hash = (string) config('admin.password_hash');
        $valid = $password !== ''
            && preg_match('/^\$2[aby]\$\d{2}\$/', $hash) === 1
            && Hash::check($password, $hash);

        if (!$valid) {
            return response()->json(['message' => 'パスワードが違います'], 422);
        }

        $request->session()->regenerate();
        $request->session()->put('admin', true);

        return response()->json(['ok' => true]);
    }

    /**
     * 管理モードを終了し、照会だけに戻す。
     */
    public function lock(Request $request)
    {
        $request->session()->forget('admin');
        $request->session()->regenerate();

        return response()->json(['ok' => true]);
    }

    /**
     * スキル要約 PDF。画面のキーワード絞り込みは反映しない。
     */
    public function summary()
    {
        $sheet = $this->sheetData();
        $profile = $sheet['career']['profile'] ?? [];
        $summary = $sheet['skillSummary'];
        $this->ensureFontDirectory();

        $pdf = Pdf::loadView('pdf.summary', [
            'fontPath' => $this->fontPath(),
            'name' => $profile['name'] ?? '',
            'nameKana' => $profile['nameKana'] ?? '',
            'birthday' => $this->birthdayLabel($profile['birthday'] ?? null),
            'role' => $profile['role'] ?? '',
            'profileUrl' => $this->profileUrl($profile),
            'qualifications' => $profile['qualifications'] ?? [],
            'privateLabel' => $summary['private']['label'] ?? 'プライベートでの使用技術',
            'privateGroups' => $this->groupsForPdf($summary['private']['groups'] ?? []),
            'businessLabel' => $summary['business']['label'] ?? '業務での使用技術',
            'businessGroups' => $this->groupsForPdf($summary['business']['groups'] ?? []),
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(self::SUMMARY_FILENAME);
    }

    /**
     * 経歴書 PDF。全案件を新しい順に出す。画面のキーワード絞り込みは反映しない。
     */
    public function career()
    {
        $sheet = $this->sheetData();
        $profile = $sheet['career']['profile'] ?? [];
        $this->ensureFontDirectory();

        $pdf = Pdf::loadView('pdf.career', [
            'fontPath' => $this->fontPath(),
            'name' => $profile['name'] ?? '',
            'nameKana' => $profile['nameKana'] ?? '',
            'birthday' => $this->birthdayLabel($profile['birthday'] ?? null),
            'role' => $profile['role'] ?? '',
            'profileUrl' => $this->profileUrl($profile),
            'qualifications' => $profile['qualifications'] ?? [],
            'projects' => $this->projectsForPdf($sheet['career']['projects'] ?? []),
        ]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = $pageNumber . ' / ' . $pageCount;
            $font = $fontMetrics->getFont('helvetica');
            $size = 8;
            $width = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($canvas->get_width() - $width) / 2;
            $y = $canvas->get_height() - 22;
            $canvas->text($x, $y, $text, $font, $size, [0.416, 0.384, 0.353]);
        });

        return $pdf->download(self::CAREER_FILENAME);
    }

    private function sheetData(): array
    {
        $career = json_decode(file_get_contents(resource_path('data/career.json')), true);
        $skillSummary = json_decode(file_get_contents(resource_path('data/skill-summary.json')), true);

        return [
            'career' => is_array($career) ? $career : [],
            'skillSummary' => is_array($skillSummary) ? $skillSummary : [],
        ];
    }

    private function groupsForPdf(array $groups): array
    {
        $prepared = [];
        foreach ($groups as $group) {
            $skills = [];
            foreach ($group['skills'] ?? [] as $skill) {
                $name = $skill['name'] ?? '';
                if (!empty($skill['area'])) {
                    $name .= '（' . $skill['area'] . '）';
                }
                $skills[] = [
                    'name' => $name,
                    'level' => $skill['level'] ?? '',
                ];
            }
            $prepared[] = [
                'label' => $group['label'] ?? '',
                'skills' => $skills,
            ];
        }

        return $prepared;
    }

    private function projectsForPdf(array $projects): array
    {
        $rows = [];
        foreach ($projects as $project) {
            $period = $this->formatPeriod((string) ($project['period'] ?? ''));
            $tech = $project['technologies'] ?? [];
            $lines = [];
            foreach ([
                '言語' => $tech['languages'] ?? [],
                'FW' => $tech['frameworks'] ?? [],
                'DB' => $tech['databases'] ?? [],
                '環境' => $tech['environment'] ?? [],
                'ツール' => $tech['tools'] ?? [],
            ] as $label => $items) {
                $values = $this->listItems($items);
                if ($values === []) {
                    continue;
                }
                $lines[] = ['label' => $label, 'values' => $values];
            }
            $rows[] = [
                'period' => $period,
                'industry' => $project['industry'] ?? '',
                'title' => $project['title'] ?? '',
                'description' => $project['description'] ?? '',
                'role' => $project['role'] ?? '',
                'team' => $project['teamSize'] ?? '',
                'phases' => $this->listItems($project['phases'] ?? []),
                'technologies' => $lines,
            ];
        }

        usort($rows, function (array $a, array $b) {
            if ($a['period']['startKey'] !== $b['period']['startKey']) {
                return $b['period']['startKey'] <=> $a['period']['startKey'];
            }

            return $b['period']['endKey'] <=> $a['period']['endKey'];
        });

        $numbered = [];
        foreach ($rows as $index => $row) {
            $row['no'] = $index + 1;
            $numbered[] = $row;
        }

        return $numbered;
    }

    private function formatPeriod(string $period): array
    {
        if (preg_match('/(\d{4})\/(\d{2})\s*-\s*(\d{4})\/(\d{2})/', $period, $matched)) {
            return [
                'label' => $matched[1] . '/' . $matched[2] . '〜' . $matched[3] . '/' . $matched[4],
                'startKey' => ((int) $matched[1] * 100) + (int) $matched[2],
                'endKey' => ((int) $matched[3] * 100) + (int) $matched[4],
            ];
        }

        if (preg_match('/(\d{4})\/(\d{2})/', $period, $matched)) {
            return [
                'label' => $matched[1] . '/' . $matched[2] . '〜継続',
                'startKey' => ((int) $matched[1] * 100) + (int) $matched[2],
                'endKey' => 0,
            ];
        }

        return ['label' => $period, 'startKey' => 0, 'endKey' => 0];
    }

    private function birthdayLabel(?string $date = null): string
    {
        $zone = new \DateTimeZone('Asia/Tokyo');
        $raw = $date ?: self::BIRTH_DATE;
        $born = \DateTimeImmutable::createFromFormat('!Y-m-d', $raw, $zone);
        if (!$born || $born->format('Y-m-d') !== $raw) {
            $born = new \DateTimeImmutable(self::BIRTH_DATE, $zone);
        }
        $age = $born->diff(new \DateTimeImmutable('now', $zone))->y;
        $label = ((int) $born->format('Y')) . '年' . ((int) $born->format('n')) . '月' . ((int) $born->format('j')) . '日';

        return $label . ' (' . $age . '歳)';
    }

    private function profileUrl(array $profile): string
    {
        $url = trim((string) ($profile['profileUrl'] ?? ''));

        return $url !== '' ? $url : self::PROFILE_PAGE_URL;
    }

    private function listItems($items): array
    {
        $values = [];
        foreach (is_array($items) ? $items : [] as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $values[] = $item;
            }
        }

        return $values;
    }

    private function ensureFontDirectory(): void
    {
        $fontDir = storage_path('fonts');
        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }
    }

    private function fontPath(): string
    {
        return str_replace('\\', '/', resource_path('fonts/ipaexg.ttf'));
    }
}
