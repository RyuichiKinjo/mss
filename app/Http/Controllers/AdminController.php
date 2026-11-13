<?php

namespace App\Http\Controllers;

use App\Services\SheetStore;
use App\Services\SheetStoreException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    private $sheets;

    public function __construct(SheetStore $sheets)
    {
        $this->sheets = $sheets;
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:80'],
            'nameKana' => ['required', 'string', 'max:80'],
            'birthday' => ['required', 'date_format:Y-m-d'],
            'role' => ['required', 'string', 'max:80'],
            'profileUrl' => ['required', 'url', 'max:300'],
            'qualifications' => ['array', 'max:30'],
            'qualifications.*' => ['string', 'max:80'],
        ], [
            'name.required' => '氏名を入力してください',
            'nameKana.required' => 'フリガナを入力してください',
            'birthday.required' => '生年月日を入力してください',
            'birthday.date_format' => '生年月日は日付で入力してください',
            'role.required' => '職種を入力してください',
            'profileUrl.required' => 'プロフィールページを入力してください',
            'profileUrl.url' => 'プロフィールページは URL で入力してください',
        ]);
        if ($validator->fails()) {
            return $this->invalid($validator->errors()->first());
        }
        $data = $validator->validated();

        try {
            $this->sheets->saveProfile([
                'name' => trim($data['name']),
                'nameKana' => trim($data['nameKana']),
                'birthday' => $data['birthday'],
                'role' => trim($data['role']),
                'profileUrl' => trim($data['profileUrl']),
                'qualifications' => $this->sheets->cleanList($data['qualifications'] ?? []),
            ]);
        } catch (SheetStoreException $exception) {
            return $this->invalid($exception->getMessage());
        }

        return response()->json(['ok' => true]);
    }

    public function storeSkill(Request $request)
    {
        return $this->saveSkill($request, false);
    }

    public function updateSkill(Request $request)
    {
        return $this->saveSkill($request, true);
    }

    public function destroySkill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'side' => ['required', 'in:private,business'],
            'group' => ['required', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:80'],
        ]);
        if ($validator->fails()) {
            return $this->invalid('削除する技術を指定してください');
        }
        $data = $validator->validated();

        try {
            $this->sheets->deleteSkill($data['side'], $data['group'], $data['name']);
        } catch (SheetStoreException $exception) {
            return $this->invalid($exception->getMessage());
        }

        return response()->json(['ok' => true]);
    }

    public function storeProject(Request $request)
    {
        return $this->saveProject($request, true, '');
    }

    public function updateProject(Request $request, string $id)
    {
        return $this->saveProject($request, false, $id);
    }

    public function destroyProject(string $id)
    {
        try {
            $this->sheets->deleteProject($id);
        } catch (SheetStoreException $exception) {
            return $this->invalid($exception->getMessage());
        }

        return response()->json(['ok' => true]);
    }

    private function saveSkill(Request $request, bool $updating)
    {
        $rules = [
            'side' => ['required', 'in:private,business'],
            'group' => ['required', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:80'],
            'level' => ['nullable', 'string', 'max:40'],
            'area' => ['nullable', 'string', 'max:40'],
        ];
        if ($updating) {
            $rules['fromGroup'] = ['required', 'string', 'max:40'];
            $rules['fromName'] = ['required', 'string', 'max:80'];
        }
        $validator = Validator::make($request->all(), $rules, [
            'group.required' => 'グループを入力してください',
            'name.required' => '技術名を入力してください',
        ]);
        if ($validator->fails()) {
            return $this->invalid($validator->errors()->first());
        }
        $data = $validator->validated();
        $skill = ['name' => trim($data['name'])];
        if ($data['side'] === 'business') {
            $level = trim((string) ($data['level'] ?? ''));
            $area = trim((string) ($data['area'] ?? ''));
            if ($level !== '') {
                $skill['level'] = $level;
            }
            if ($area !== '') {
                $skill['area'] = $area;
            }
        }

        try {
            if ($updating) {
                $this->sheets->updateSkill($data['side'], $data['fromGroup'], $data['fromName'], trim($data['group']), $skill);
            } else {
                $this->sheets->addSkill($data['side'], trim($data['group']), $skill);
            }
        } catch (SheetStoreException $exception) {
            return $this->invalid($exception->getMessage());
        }

        return response()->json(['ok' => true]);
    }

    private function saveProject(Request $request, bool $creating, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:120'],
            'start' => ['required', 'date_format:Y-m'],
            'end' => ['nullable', 'date_format:Y-m'],
            'industry' => ['nullable', 'string', 'max:40'],
            'role' => ['nullable', 'string', 'max:80'],
            'teamSize' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:2000'],
            'phases' => ['array', 'max:30'],
            'phases.*' => ['string', 'max:40'],
            'languages' => ['array', 'max:30'],
            'languages.*' => ['string', 'max:80'],
            'frameworks' => ['array', 'max:30'],
            'frameworks.*' => ['string', 'max:80'],
            'databases' => ['array', 'max:30'],
            'databases.*' => ['string', 'max:80'],
            'environment' => ['array', 'max:30'],
            'environment.*' => ['string', 'max:80'],
            'tools' => ['array', 'max:30'],
            'tools.*' => ['string', 'max:80'],
        ], [
            'title.required' => 'プロジェクト名を入力してください',
            'start.required' => '開始年月を入力してください',
            'start.date_format' => '開始年月は年月で入力してください',
            'end.date_format' => '終了年月は年月で入力してください',
        ]);
        if ($validator->fails()) {
            return $this->invalid($validator->errors()->first());
        }
        $data = $validator->validated();
        $end = (string) ($data['end'] ?? '');
        if ($end !== '' && $end < $data['start']) {
            return $this->invalid('終了年月は開始年月以降にしてください');
        }

        $technologies = [];
        foreach (['languages', 'frameworks', 'databases', 'environment', 'tools'] as $key) {
            $values = $this->sheets->cleanList($data[$key] ?? []);
            if ($values !== []) {
                $technologies[$key] = $values;
            }
        }
        $project = [
            'id' => $creating ? $this->sheets->nextProjectId() : $id,
            'title' => trim($data['title']),
            'period' => $this->period($data['start'], $end),
            'industry' => trim((string) ($data['industry'] ?? '')),
            'role' => trim((string) ($data['role'] ?? '')),
            'teamSize' => trim((string) ($data['teamSize'] ?? '')),
            'phases' => $this->sheets->cleanList($data['phases'] ?? []),
            'technologies' => $technologies,
            'description' => trim((string) ($data['description'] ?? '')),
        ];
        foreach (['industry', 'role', 'teamSize', 'description'] as $key) {
            if ($project[$key] === '') {
                unset($project[$key]);
            }
        }

        try {
            $this->sheets->saveProject($project, $creating);
        } catch (SheetStoreException $exception) {
            return $this->invalid($exception->getMessage());
        }

        return response()->json(['ok' => true]);
    }

    private function period(string $start, string $end): string
    {
        $startLabel = str_replace('-', '/', $start);
        if ($end === '') {
            return $startLabel;
        }

        return $startLabel . ' - ' . str_replace('-', '/', $end);
    }

    private function invalid(string $message)
    {
        return response()->json(['message' => $message], 422);
    }
}
