<?php

namespace App\Services;

class SheetStore
{
    public function career(): array
    {
        return $this->read($this->careerPath());
    }

    public function skills(): array
    {
        return $this->read($this->skillPath());
    }

    public function saveProfile(array $profile): void
    {
        $career = $this->career();
        $current = is_array($career['profile'] ?? null) ? $career['profile'] : [];
        $career['profile'] = array_merge($current, $profile);
        $this->write($this->careerPath(), $career);
    }

    public function addSkill(string $side, string $groupLabel, array $skill): void
    {
        $summary = $this->skills();
        $this->assertSide($summary, $side);
        if ($this->hasSkill($summary[$side]['groups'], $skill['name'])) {
            throw new SheetStoreException('同じ技術名がすでにあります');
        }
        $this->appendSkill($summary[$side]['groups'], $groupLabel, $skill);
        $this->write($this->skillPath(), $summary);
    }

    public function updateSkill(string $side, string $fromGroup, string $fromName, string $toGroup, array $skill): void
    {
        $summary = $this->skills();
        $this->assertSide($summary, $side);
        $removed = $this->removeSkill($summary[$side]['groups'], $fromGroup, $fromName);
        if (!$removed) {
            throw new SheetStoreException('技術が見つかりません');
        }
        if ($this->hasSkill($summary[$side]['groups'], $skill['name'])) {
            throw new SheetStoreException('同じ技術名がすでにあります');
        }
        $this->appendSkill($summary[$side]['groups'], $toGroup, $skill);
        $this->write($this->skillPath(), $summary);
    }

    public function deleteSkill(string $side, string $groupLabel, string $name): void
    {
        $summary = $this->skills();
        $this->assertSide($summary, $side);
        if (!$this->removeSkill($summary[$side]['groups'], $groupLabel, $name)) {
            throw new SheetStoreException('技術が見つかりません');
        }
        $this->write($this->skillPath(), $summary);
    }

    public function saveProject(array $project, bool $creating): void
    {
        $career = $this->career();
        $projects = is_array($career['projects'] ?? null) ? $career['projects'] : [];
        if ($creating) {
            $projects[] = $project;
        } else {
            $found = false;
            foreach ($projects as $index => $current) {
                if (($current['id'] ?? '') === $project['id']) {
                    $projects[$index] = $project;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new SheetStoreException('経歴が見つかりません');
            }
        }
        $career['projects'] = array_values($projects);
        $summary = $this->skills();
        if ($this->absorbTechnologies($summary, $project['technologies'] ?? [])) {
            $this->write($this->skillPath(), $summary);
        }
        $this->write($this->careerPath(), $career);
    }

    public function deleteProject(string $id): void
    {
        $career = $this->career();
        $projects = is_array($career['projects'] ?? null) ? $career['projects'] : [];
        $next = [];
        foreach ($projects as $project) {
            if (($project['id'] ?? '') === $id) {
                continue;
            }
            $next[] = $project;
        }
        if (count($next) === count($projects)) {
            throw new SheetStoreException('経歴が見つかりません');
        }
        $career['projects'] = $next;
        $this->write($this->careerPath(), $career);
    }

    public function nextProjectId(): string
    {
        $max = 0;
        foreach ($this->career()['projects'] ?? [] as $project) {
            if (preg_match('/^proj-(\d+)$/', (string) ($project['id'] ?? ''), $matched)) {
                $max = max($max, (int) $matched[1]);
            }
        }

        return 'proj-' . str_pad((string) ($max + 1), 2, '0', STR_PAD_LEFT);
    }

    public function cleanList($items): array
    {
        $values = [];
        foreach (is_array($items) ? $items : [] as $item) {
            $item = trim((string) $item);
            if ($item !== '' && !in_array($item, $values, true)) {
                $values[] = $item;
            }
        }

        return $values;
    }

    private function absorbTechnologies(array &$summary, array $technologies): bool
    {
        $map = [
            'languages' => '言語',
            'frameworks' => 'フレームワーク',
            'databases' => 'データベース',
            'environment' => '環境',
            'tools' => 'ツール',
        ];
        if (!isset($summary['business']['groups']) || !is_array($summary['business']['groups'])) {
            $summary['business']['groups'] = [];
        }
        $added = false;
        $known = [];
        foreach ($summary['business']['groups'] as $group) {
            foreach ($group['skills'] ?? [] as $skill) {
                $name = trim((string) ($skill['name'] ?? ''));
                if ($name !== '') {
                    $known[$name] = true;
                }
            }
        }
        foreach ($map as $key => $label) {
            foreach ($this->cleanList($technologies[$key] ?? []) as $name) {
                if (isset($known[$name])) {
                    continue;
                }
                $this->appendSkill($summary['business']['groups'], $label, ['name' => $name]);
                $known[$name] = true;
                $added = true;
            }
        }

        return $added;
    }

    private function appendSkill(array &$groups, string $label, array $skill): void
    {
        foreach ($groups as $index => $group) {
            if (($group['label'] ?? '') === $label) {
                $groups[$index]['skills'][] = $skill;
                return;
            }
        }
        $groups[] = [
            'label' => $label,
            'skills' => [$skill],
        ];
    }

    private function removeSkill(array &$groups, string $label, string $name): bool
    {
        foreach ($groups as $index => $group) {
            if (($group['label'] ?? '') !== $label) {
                continue;
            }
            $skills = [];
            $removed = false;
            foreach ($group['skills'] ?? [] as $skill) {
                if (!$removed && ($skill['name'] ?? '') === $name) {
                    $removed = true;
                    continue;
                }
                $skills[] = $skill;
            }
            $groups[$index]['skills'] = $skills;
            if ($removed && $skills === []) {
                array_splice($groups, $index, 1);
            }

            return $removed;
        }

        return false;
    }

    private function hasSkill(array $groups, string $name): bool
    {
        foreach ($groups as $group) {
            foreach ($group['skills'] ?? [] as $skill) {
                if (($skill['name'] ?? '') === $name) {
                    return true;
                }
            }
        }

        return false;
    }

    private function assertSide(array $summary, string $side): void
    {
        if (!isset($summary[$side]['groups']) || !is_array($summary[$side]['groups'])) {
            throw new SheetStoreException('区分が見つかりません');
        }
    }

    private function read(string $path): array
    {
        $data = json_decode((string) file_get_contents($path), true);
        if (!is_array($data)) {
            throw new SheetStoreException('データを読めませんでした');
        }

        return $data;
    }

    private function write(string $path, array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new SheetStoreException('保存できませんでした');
        }
        $json = preg_replace_callback('/^(?: {4})+/m', function (array $match) {
            return str_repeat(' ', (int) (strlen($match[0]) / 2));
        }, $json);
        $handle = fopen($path, 'c+');
        if ($handle === false) {
            throw new SheetStoreException('保存できませんでした');
        }
        try {
            if (!flock($handle, LOCK_EX)) {
                throw new SheetStoreException('保存できませんでした');
            }
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, $json . "\n");
            fflush($handle);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private function careerPath(): string
    {
        return resource_path('data/career.json');
    }

    private function skillPath(): string
    {
        return resource_path('data/skill-summary.json');
    }
}
