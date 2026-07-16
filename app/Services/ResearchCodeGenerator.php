<?php

namespace App\Services;

use App\Models\Research;

class ResearchCodeGenerator
{
    public function generate(): string
    {
        $year = now()->format('Y');
        $prefix = 'RSH-'.$year.'-';

        $latestCode = Research::query()
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

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
