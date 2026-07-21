<?php

namespace App\Services;

use App\Models\Research;

class ResearchCodeGenerator
{
    public function generate(string $categoryName): string
    {
        $categoryPrefix = $this->resolveCategoryPrefix($categoryName);
        $year = now()->format('Y');
        $prefix = $categoryPrefix.'-'.$year.'-';

        $latestCode = Research::withTrashed()
            ->where('research_code', 'like', $prefix.'%')
            ->orderByDesc('research_code')
            ->value('research_code');

        $nextNumber = 1;

        if ($latestCode) {
            $suffix = substr($latestCode, -4);
            if (is_numeric($suffix)) {
                $nextNumber = ((int) $suffix) + 1;
            }
        }

        do {
            $generatedCode = $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (Research::withTrashed()->where('research_code', $generatedCode)->exists());

        return $generatedCode;
    }

    private function resolveCategoryPrefix(string $categoryName): string
    {
        return match (strtolower(trim($categoryName))) {
            'action research' => 'AR',
            'basic research' => 'BR',
            default => 'RSH',
        };
    }
}
