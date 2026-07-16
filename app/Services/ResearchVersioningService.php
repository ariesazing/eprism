<?php

namespace App\Services;

use App\Models\Research;
use App\Models\ResearchDocument;
use App\Models\ResearchVersion;
use App\Models\SubmissionType;
use App\Models\User;
use Illuminate\Support\Collection;

class ResearchVersioningService
{
    public function snapshotSubmission(
        Research $research,
        User $submittedBy,
        string $submissionTypeName = 'Proposal',
        ?string $remarks = null,
    ): ResearchVersion {
        $submissionType = SubmissionType::query()->firstOrCreate(
            ['type_name' => $submissionTypeName],
            ['description' => $submissionTypeName.' stage submission.']
        );

        $documents = $this->latestDocuments($research);
        $currentVersion = $research->versions()
            ->with('files')
            ->where('is_current', true)
            ->latest('id')
            ->first();

        if ($currentVersion && $this->matchesCurrentSnapshot($currentVersion, $documents)) {
            return $currentVersion;
        }

        $research->versions()->where('is_current', true)->update(['is_current' => false]);

        $version = $research->versions()->create([
            'submission_type_id' => $submissionType->id,
            'version_number' => $this->nextVersionNumber($research, $submissionType->id),
            'parent_version_id' => $currentVersion?->id,
            'status_id' => $research->status_id,
            'is_current' => true,
            'submitted_by' => $submittedBy->id,
            'submitted_at' => $research->submitted_at ?? now(),
            'remarks' => $remarks,
        ]);

        foreach ($documents as $document) {
            $version->files()->create([
                'document_name' => $document->document_type,
                'original_file_name' => $document->original_filename,
                'stored_file_name' => $this->snapshotStoredFilename($version, $document),
                'file_path' => $document->file_path,
                'file_type' => $document->mime_type !== '' ? $document->mime_type : $document->file_extension,
                'file_size' => $document->file_size,
                'uploaded_by' => $document->uploaded_by,
                'uploaded_at' => $document->uploaded_at ?? now(),
            ]);
        }

        return $version;
    }

    /**
     * @return Collection<int, ResearchDocument>
     */
    private function latestDocuments(Research $research): Collection
    {
        return $research->documents()
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->get()
            ->unique('document_type')
            ->values();
    }

    private function nextVersionNumber(Research $research, int $submissionTypeId): int
    {
        $latestVersionNumber = ResearchVersion::query()
            ->where('research_id', $research->id)
            ->where('submission_type_id', $submissionTypeId)
            ->max('version_number');

        return ((int) $latestVersionNumber) + 1;
    }

    /**
     * @param Collection<int, ResearchDocument> $documents
     */
    private function matchesCurrentSnapshot(ResearchVersion $currentVersion, Collection $documents): bool
    {
        $currentFiles = $currentVersion->files
            ->sortBy('document_name')
            ->map(fn ($file): array => [
                'document_name' => $file->document_name,
                'original_file_name' => $file->original_file_name,
                'file_path' => $file->file_path,
                'file_type' => $file->file_type,
                'file_size' => (int) $file->file_size,
                'uploaded_by' => (int) $file->uploaded_by,
            ])
            ->values()
            ->all();

        $documentSnapshot = $documents
            ->sortBy('document_type')
            ->map(fn (ResearchDocument $document): array => [
                'document_name' => $document->document_type,
                'original_file_name' => $document->original_filename,
                'file_path' => $document->file_path,
                'file_type' => $document->mime_type !== '' ? $document->mime_type : $document->file_extension,
                'file_size' => (int) $document->file_size,
                'uploaded_by' => (int) $document->uploaded_by,
            ])
            ->values()
            ->all();

        return $currentFiles === $documentSnapshot;
    }

    private function snapshotStoredFilename(ResearchVersion $version, ResearchDocument $document): string
    {
        return 'snapshot-'.$version->id.'-'.$document->stored_filename;
    }
}