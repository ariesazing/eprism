<?php

namespace App\Http\Controllers\Research;

use App\Http\Controllers\Controller;
use App\Http\Requests\Research\StoreResearchRequest;
use App\Http\Requests\Research\UpdateResearchRequest;
use App\Models\OrganizationalUnit;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\ResearchStatus;
use App\Services\ResearchCodeGenerator;
use App\Services\ResearchVersioningService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\View\View;

class ResearchController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Research::query()
            ->with([
                'leadProponent:id,first_name,last_name',
                'organizationalUnit:id,unit_name,unit_code',
                'category:id,category_name',
                'status:id,status_name',
            ])
            ->latest();

        if ($user?->role?->role_name !== 'Administrator') {
            $query->where('lead_proponent_id', $user?->id);
        }

        $researches = $query->paginate(15);

        return view('researches.index', [
            'researches' => $researches,
        ]);
    }

    public function create(Request $request): View
    {
        return view('researches.create', [
            'categories' => ResearchCategory::query()->orderBy('category_name')->get(['id', 'category_name']),
            'statuses' => ResearchStatus::query()->orderBy('status_name')->get(['id', 'status_name']),
            'organizationalUnits' => OrganizationalUnit::query()->orderBy('unit_name')->get(['id', 'unit_name', 'unit_code']),
            'positionTitles' => \App\Models\User::positionTitles(),
            'defaultOrganizationalUnitId' => $request->user()?->organizational_unit_id,
            'isAdmin' => $request->user()?->role?->role_name === 'Administrator',
        ]);
    }

    public function store(
        StoreResearchRequest $request,
        ResearchCodeGenerator $codeGenerator,
        ResearchVersioningService $researchVersioningService,
    ): RedirectResponse
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $storedDocumentPaths = [];
            $storedProponentPhotoPaths = [];

            try {
                DB::beginTransaction();

                $submittedStatusId = $this->resolveStatusId(
                    'Submitted',
                    'Research has been successfully submitted'
                );

                $research = Research::query()->create([
                    'research_code' => $codeGenerator->generate(),
                    'title' => $request->string('title')->toString(),
                    'lead_proponent_id' => $request->user()->id,
                    'organizational_unit_id' => (int) $request->integer('organizational_unit_id'),
                    'category_id' => (int) $request->integer('category_id'),
                    'status_id' => $submittedStatusId,
                    'submitted_at' => now(),
                    'approved_at' => null,
                    'archived_at' => null,
                ]);

                $uploaderUnit = OrganizationalUnit::query()->findOrFail(
                    (int) $request->integer('uploader_proponent_organizational_unit_id')
                );

                $uploaderPhotoFile = $request->file('uploader_proponent_photo');
                $uploaderPhotoData = $this->storeProponentPhoto($research->research_code, $uploaderPhotoFile);
                $storedProponentPhotoPaths[] = $uploaderPhotoData['photo_path'];

                $research->proponents()->create([
                    'first_name' => $request->string('uploader_proponent_first_name')->toString(),
                    'middle_name' => $request->string('uploader_proponent_middle_name')->toString(),
                    'last_name' => $request->string('uploader_proponent_last_name')->toString(),
                    'suffix' => $request->filled('uploader_proponent_suffix')
                        ? $request->string('uploader_proponent_suffix')->toString()
                        : null,
                    'position_title' => $request->string('uploader_proponent_position_title')->toString(),
                    'organizational_unit_name' => $uploaderUnit->unit_name,
                    'email' => $request->string('uploader_proponent_email')->toString(),
                    'contact_number' => $request->string('uploader_proponent_contact_number')->toString(),
                    'photo_path' => $uploaderPhotoData['photo_path'],
                    'photo_disk' => $uploaderPhotoData['photo_disk'],
                    'photo_filename' => $uploaderPhotoData['photo_filename'],
                ]);

                foreach ((array) $request->input('additional_proponents', []) as $index => $proponentInput) {
                    $additionalUnit = OrganizationalUnit::query()->findOrFail(
                        (int) data_get($proponentInput, 'organizational_unit_id')
                    );

                    $additionalPhotoFile = $request->file("additional_proponents.$index.photo");
                    $additionalPhotoData = $this->storeProponentPhoto($research->research_code, $additionalPhotoFile);
                    $storedProponentPhotoPaths[] = $additionalPhotoData['photo_path'];

                    $research->proponents()->create([
                        'first_name' => (string) data_get($proponentInput, 'first_name'),
                        'middle_name' => (string) data_get($proponentInput, 'middle_name'),
                        'last_name' => (string) data_get($proponentInput, 'last_name'),
                        'suffix' => data_get($proponentInput, 'suffix') ?: null,
                        'position_title' => (string) data_get($proponentInput, 'position_title'),
                        'organizational_unit_name' => $additionalUnit->unit_name,
                        'email' => (string) data_get($proponentInput, 'email'),
                        'contact_number' => (string) data_get($proponentInput, 'contact_number'),
                        'photo_path' => $additionalPhotoData['photo_path'],
                        'photo_disk' => $additionalPhotoData['photo_disk'],
                        'photo_filename' => $additionalPhotoData['photo_filename'],
                    ]);
                }

                $initialDocuments = [
                    'research_manuscript_file' => 'Research Manuscript',
                    'narrative_form_file' => 'Narrative Form Document',
                    'documentation_file' => 'Research Documentation',
                ];

                foreach ($initialDocuments as $fieldName => $documentType) {
                    $uploadedFile = $request->file($fieldName);
                    $storedFilename = Str::uuid().'.'.$uploadedFile->getClientOriginalExtension();
                    $directory = 'research_documents/'.$research->research_code;
                    $storedPath = $uploadedFile->storeAs($directory, $storedFilename, 'local');
                    $storedDocumentPaths[] = $storedPath;

                    $research->documents()->create([
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
                }

                $researchVersioningService->snapshotSubmission(
                    $research,
                    $request->user(),
                    'Proposal',
                    'Initial proposal submission.'
                );

                DB::commit();

                return redirect()->route('researches.show', $research)->with(
                    'status',
                    'Research submitted successfully.'
                );
            } catch (UniqueConstraintViolationException $exception) {
                DB::rollBack();

                if ($storedDocumentPaths !== []) {
                    Storage::disk('local')->delete($storedDocumentPaths);
                }

                if ($storedProponentPhotoPaths !== []) {
                    Storage::disk('local')->delete($storedProponentPhotoPaths);
                }

                if ($attempt === 2 || ! str_contains($exception->getMessage(), 'researches_research_code_unique')) {
                    throw $exception;
                }
            } catch (Throwable $exception) {
                DB::rollBack();

                if ($storedDocumentPaths !== []) {
                    Storage::disk('local')->delete($storedDocumentPaths);
                }

                if ($storedProponentPhotoPaths !== []) {
                    Storage::disk('local')->delete($storedProponentPhotoPaths);
                }

                throw $exception;
            }
        }

        abort(500, 'Unable to submit research at this time.');
    }

    public function submit(
        Request $request,
        Research $research,
        ResearchVersioningService $researchVersioningService,
    ): RedirectResponse
    {
        $this->authorizeAccess($request, $research);

        $errors = [];

        if (! $research->proponents()->exists()) {
            $errors['proponents'] = 'Add at least one research proponent before submitting.';
        } elseif ($research->proponents()->whereNull('photo_path')->exists()) {
            $errors['proponents'] = 'Each research proponent must have an uploaded photo before submitting.';
        }

        $requiredDocumentTypes = [
            'Research Manuscript',
            'Narrative Form Document',
            'Research Documentation',
        ];

        $existingDocumentTypes = $research->documents()
            ->whereIn('document_type', $requiredDocumentTypes)
            ->pluck('document_type')
            ->unique()
            ->values()
            ->all();

        $missingDocumentTypes = array_values(array_diff($requiredDocumentTypes, $existingDocumentTypes));
        if ($missingDocumentTypes !== []) {
            $errors['documents'] = 'Upload all required PDF documents before submitting: '.implode(', ', $missingDocumentTypes).'.';
        }

        if ($errors !== []) {
            return back()->withErrors($errors);
        }

        $submittedStatusId = $this->resolveStatusId(
            'Submitted',
            'Research has been successfully submitted'
        );

        $research->update([
            'status_id' => $submittedStatusId,
            'submitted_at' => $research->submitted_at ?? now(),
        ]);

        $researchVersioningService->snapshotSubmission(
            $research->fresh('documents'),
            $request->user(),
            'Proposal',
            'Submitted revision snapshot.'
        );

        return back()->with('status', 'Research submitted successfully.');
    }

    public function show(Request $request, Research $research): View
    {
        $this->authorizeAccess($request, $research);

        $research->load([
            'leadProponent:id,first_name,last_name,email',
            'organizationalUnit:id,unit_name,unit_code',
            'category:id,category_name',
            'status:id,status_name',
            'proponents',
            'documents.uploader:id,first_name,last_name',
        ]);

        return view('researches.show', [
            'research' => $research,
            'canManageStatus' => $request->user()?->role?->role_name === 'Administrator',
            'positionTitles' => \App\Models\User::positionTitles(),
            'organizationalUnits' => OrganizationalUnit::query()->orderBy('unit_name')->get(['id', 'unit_name', 'unit_code']),
            'categories' => ResearchCategory::query()->orderBy('category_name')->get(['id', 'category_name']),
            'statuses' => ResearchStatus::query()->orderBy('status_name')->get(['id', 'status_name']),
        ]);
    }

    public function update(UpdateResearchRequest $request, Research $research): RedirectResponse
    {
        $this->authorizeAccess($request, $research);

        $research->fill([
            'title' => $request->string('title')->toString(),
            'category_id' => (int) $request->integer('category_id'),
            'organizational_unit_id' => (int) $request->integer('organizational_unit_id'),
        ]);

        if ($request->user()?->role?->role_name === 'Administrator' && $request->filled('status_id')) {
            $statusId = (int) $request->integer('status_id');
            $research->status_id = $statusId;

            $statusName = ResearchStatus::query()->whereKey($statusId)->value('status_name');
            if ($statusName === 'Submitted' && $research->submitted_at === null) {
                $research->submitted_at = now();
            }
            if ($statusName === 'Approved') {
                $research->approved_at = now();
            }
            if ($statusName === 'Archived') {
                $research->archived_at = now();
            }
        }

        $research->save();

        return redirect()->route('researches.show', $research)->with('status', 'Research record updated successfully.');
    }

    public function destroy(Request $request, Research $research): RedirectResponse
    {
        $this->authorizeAccess($request, $research);

        foreach ($research->proponents as $proponent) {
            if ($proponent->photo_path !== null && $proponent->photo_disk !== null) {
                Storage::disk($proponent->photo_disk)->delete($proponent->photo_path);
            }
        }

        $research->delete();

        return redirect()->route('researches.index')->with('status', 'Research record archived from active list.');
    }

    /**
     * @return array{photo_path: string, photo_disk: string, photo_filename: string}
     */
    private function storeProponentPhoto(string $researchCode, UploadedFile $photo): array
    {
        $photoDisk = 'local';
        $photoFilename = Str::uuid().'.'.$photo->getClientOriginalExtension();
        $photoDirectory = 'research_proponents/'.$researchCode;
        $photoPath = $photo->storeAs($photoDirectory, $photoFilename, $photoDisk);

        return [
            'photo_path' => $photoPath,
            'photo_disk' => $photoDisk,
            'photo_filename' => $photoFilename,
        ];
    }

    private function authorizeAccess(Request $request, Research $research): void
    {
        $user = $request->user();

        if ($user?->role?->role_name === 'Administrator') {
            return;
        }

        abort_unless((int) $research->lead_proponent_id === (int) $user?->id, 403);
    }

    private function resolveStatusId(string $statusName, string $description): int
    {
        return (int) ResearchStatus::query()->firstOrCreate(
            ['status_name' => $statusName],
            ['description' => $description]
        )->id;
    }
}
