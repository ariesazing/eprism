<?php

namespace App\Http\Controllers\Research;

use App\Http\Controllers\Controller;
use App\Http\Requests\Research\StoreResearchDocumentRequest;
use App\Models\Research;
use App\Models\ResearchDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResearchDocumentController extends Controller
{
    public function store(StoreResearchDocumentRequest $request, Research $research): RedirectResponse
    {
        $this->authorizeAccess($request, $research);

        $documentType = match ($request->string('document_class')->toString()) {
            'research_manuscript' => 'Research Manuscript',
            'narrative_form_document' => 'Narrative Form Document',
            default => 'Research Documentation',
        };

        $uploadedFile = $request->file('file');
        $storedFilename = Str::uuid().'.'.$uploadedFile->getClientOriginalExtension();

        $directory = 'research_documents/'.$research->research_code;
        $storedPath = $uploadedFile->storeAs($directory, $storedFilename, 'local');

        $existingDocument = ResearchDocument::query()
            ->where('research_id', $research->id)
            ->where('document_type', $documentType)
            ->latest('uploaded_at')
            ->first();

        if ($existingDocument) {
            Storage::disk($existingDocument->storage_disk)->delete($existingDocument->file_path);

            $existingDocument->update([
                'original_filename' => $uploadedFile->getClientOriginalName(),
                'stored_filename' => $storedFilename,
                'file_path' => $storedPath,
                'storage_disk' => 'local',
                'file_extension' => strtolower((string) $uploadedFile->getClientOriginalExtension()),
                'mime_type' => (string) $uploadedFile->getClientMimeType(),
                'file_size' => $uploadedFile->getSize(),
                'uploaded_by' => $request->user()->id,
                'uploaded_at' => now(),
            ]);

            return back()->with('status', $documentType.' updated successfully.');
        }

        ResearchDocument::query()->create([
            'research_id' => $research->id,
            'document_type' => $documentType,
            'original_filename' => $uploadedFile->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'file_path' => $storedPath,
            'storage_disk' => 'local',
            'file_extension' => strtolower((string) $uploadedFile->getClientOriginalExtension()),
            'mime_type' => (string) $uploadedFile->getClientMimeType(),
            'file_size' => $uploadedFile->getSize(),
            'uploaded_by' => $request->user()->id,
            'uploaded_at' => now(),
        ]);

        return back()->with('status', $documentType.' uploaded successfully.');
    }

    public function download(Request $request, Research $research, ResearchDocument $document): BinaryFileResponse
    {
        $this->authorizeDocumentAccess($request, $research, $document);

        return response()->download(
            Storage::disk($document->storage_disk)->path($document->file_path),
            $document->original_filename
        );
    }

    public function destroy(Request $request, Research $research, ResearchDocument $document): RedirectResponse
    {
        $this->authorizeDocumentAccess($request, $research, $document);

        Storage::disk($document->storage_disk)->delete($document->file_path);
        $document->delete();

        return back()->with('status', 'Research document removed successfully.');
    }

    private function authorizeDocumentAccess(Request $request, Research $research, ResearchDocument $document): void
    {
        abort_unless((int) $document->research_id === (int) $research->id, 404);
        $this->authorizeAccess($request, $research);
    }

    private function authorizeAccess(Request $request, Research $research): void
    {
        $user = $request->user();

        if ($user?->role?->role_name === 'Administrator') {
            return;
        }

        abort_unless((int) $research->lead_proponent_id === (int) $user?->id, 403);
    }
}
