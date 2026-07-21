<?php

namespace App\Http\Controllers\Research;

use App\Http\Controllers\Controller;
use App\Http\Requests\Research\StoreResearchVersionRequest;
use App\Models\Research;
use App\Models\ResearchVersion;
use App\Models\VersionFile;
use App\Services\ResearchVersioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResearchVersionController extends Controller
{
    public function store(
        StoreResearchVersionRequest $request,
        Research $research,
        ResearchVersioningService $researchVersioningService,
    ): RedirectResponse {
        $this->authorizeCreate($request, $research);

        $version = $researchVersioningService->createVersionFromUploadedFiles(
            $research,
            $request->user(),
            [
                [
                    'document_name' => 'Research Manuscript',
                    'file' => $request->file('research_manuscript_file'),
                ],
                [
                    'document_name' => 'Narrative Form Document',
                    'file' => $request->file('narrative_form_file'),
                ],
                [
                    'document_name' => 'Research Documentation',
                    'file' => $request->file('documentation_file'),
                ],
            ],
            'Revision',
            $request->filled('remarks') ? $request->string('remarks')->toString() : null,
        );

        return back()->with('status', 'Version V'.$version->version_number.' created and SRAM evaluation completed.');
    }

    public function download(
        Request $request,
        Research $research,
        ResearchVersion $version,
        VersionFile $file,
    ): BinaryFileResponse {
        $this->authorizeView($request, $research);

        abort_unless((int) $version->research_id === (int) $research->id, 404);
        abort_unless((int) $file->research_version_id === (int) $version->id, 404);

        return response()->download(
            Storage::disk('local')->path($file->file_path),
            $file->original_file_name,
        );
    }

    private function authorizeCreate(Request $request, Research $research): void
    {
        $user = $request->user();
        abort_unless((int) $research->lead_proponent_id === (int) $user?->id, 403);
    }

    private function authorizeView(Request $request, Research $research): void
    {
        $user = $request->user();
        $roleName = $user?->role?->role_name;

        if (in_array($roleName, ['Administrator', 'Reviewer'], true)) {
            return;
        }

        abort_unless((int) $research->lead_proponent_id === (int) $user?->id, 403);
    }
}
