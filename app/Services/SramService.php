<?php

namespace App\Services;

use App\Models\ResearchVersion;
use App\Models\SramResult;
use App\Models\VersionFile;
use Illuminate\Support\Facades\Storage;

class SramService
{
    public function processVersion(ResearchVersion $version): SramResult
    {
        $version->loadMissing('files');

        $grammar = $this->runGrammarCheck($version);

        $overallScore = $grammar['score'];
        $overallResult = $grammar['result'];

        $recommendation = $overallResult === 'Passed'
            ? 'Version passed grammar checking. Ready for review.'
            : 'Version failed grammar checking. Please revise language issues and resubmit.';

        $result = $version->sramResult()->updateOrCreate(
            ['research_version_id' => $version->id],
            [
                'overall_score' => $overallScore,
                'overall_result' => $overallResult,
                'recommendation' => $recommendation,
                'evaluated_at' => now(),
            ]
        );

        $result->checks()->delete();
        $result->checks()->create([
            'check_type' => 'Grammar Checking',
            'score' => $grammar['score'],
            'result' => $grammar['result'],
            'remarks' => $grammar['remarks'],
        ]);

        return $result;
    }

    /**
     * @return array{score: float, result: string, remarks: string}
     */
    private function runGrammarCheck(ResearchVersion $version): array
    {
        $issues = 0;
        $extractableFiles = 0;

        /** @var VersionFile $file */
        foreach ($version->files as $file) {
            if (! Storage::disk('local')->exists($file->file_path)) {
                $issues += 2;

                continue;
            }

            $content = Storage::disk('local')->get($file->file_path);
            $sample = strtolower(substr($content, 0, 50000));
            $normalized = preg_replace('/[^a-z\s]/', ' ', $sample) ?? '';
            $wordCount = str_word_count($normalized);

            if ($wordCount >= 50) {
                $extractableFiles++;
            }

            foreach ([' teh ', ' dont ', ' doesnt ', ' alot ', ' recieve '] as $pattern) {
                $issues += substr_count(' '.$normalized.' ', $pattern);
            }
        }

        $score = max(0.0, 100.0 - ($issues * 8.0));

        if ($extractableFiles === 0) {
            $score = min($score, 74.0);
        }

        $passed = $score >= 75.0;

        return [
            'score' => $score,
            'result' => $passed ? 'Passed' : 'Failed',
            'remarks' => $passed
                ? 'No critical grammar issues detected in extracted file text.'
                : 'Grammar issues were detected or text extraction was insufficient for confident pass.',
        ];
    }
}
