<?php

use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\ResearchDocument;
use App\Models\ResearchProponent;
use App\Models\ResearchStatus;
use App\Models\SubmissionType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('research submission requires research details, an initial proponent, and a pdf document together', function () {
    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();

    $response = $this->from(route('researches.create'))
        ->actingAs($user)
        ->post(route('researches.store'), [
            'title' => 'Effects of Example Validation Rules',
            'category_id' => $category->id,
            'organizational_unit_id' => $user->organizational_unit_id,
        ]);

    $response->assertRedirect(route('researches.create', absolute: false));
    $response->assertSessionHasErrors([
        'uploader_proponent_photo',
        'research_manuscript_file',
        'narrative_form_file',
        'documentation_file',
    ]);
});

test('research submission creates the research, initial proponent, and all required pdf documents together', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();

    $response = $this->actingAs($user)->post(route('researches.store'), [
        'title' => 'Effects of Example Validation Rules',
        'category_id' => $category->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'uploader_proponent_first_name' => 'John',
        'uploader_proponent_middle_name' => 'Q',
        'uploader_proponent_last_name' => 'Doe',
        'uploader_proponent_suffix' => null,
        'uploader_proponent_position_title' => 'Teacher I',
        'uploader_proponent_organizational_unit_id' => $user->organizational_unit_id,
        'uploader_proponent_email' => 'john.doe@example.com',
        'uploader_proponent_contact_number' => '09123456789',
        'uploader_proponent_photo' => UploadedFile::fake()->create('uploader.jpg', 64, 'image/jpeg'),
        'research_manuscript_file' => UploadedFile::fake()->create('manuscript.pdf', 64, 'application/pdf'),
        'narrative_form_file' => UploadedFile::fake()->create('narrative.pdf', 64, 'application/pdf'),
        'documentation_file' => UploadedFile::fake()->create('documentation.pdf', 64, 'application/pdf'),
    ]);

    $research = Research::query()->firstOrFail();

    $response->assertRedirect(route('researches.show', $research, absolute: false));
    expect($research->status?->status_name)->toBe('Submitted');
    expect($research->submitted_at)->not->toBeNull();
    expect($research->proponents()->count())->toBe(1);
    expect($research->documents()->count())->toBe(3);
    expect($research->versions()->count())->toBe(1);
    expect($research->versions()->where('is_current', true)->exists())->toBeTrue();
    expect($research->proponents()->first()?->email)->toBe('john.doe@example.com');
    expect($research->documents()->pluck('document_type')->all())
        ->toMatchArray(['Research Manuscript', 'Narrative Form Document', 'Research Documentation']);

    $version = $research->versions()->with('files', 'submissionType')->firstOrFail();

    expect($version->submissionType?->type_name)->toBe('Proposal');
    expect($version->files()->count())->toBe(3);
    expect($version->files()->pluck('document_name')->sort()->values()->all())
        ->toBe(['Narrative Form Document', 'Research Documentation', 'Research Manuscript']);
});

test('research creation skips soft deleted research codes', function () {
    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();
    $draftStatus = ResearchStatus::query()->create([
        'status_name' => 'Draft',
        'description' => 'Research draft is awaiting completion before final submission.',
    ]);

    Research::factory()->create([
        'research_code' => 'RSH-'.now()->format('Y').'-0001',
        'lead_proponent_id' => $user->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'category_id' => $category->id,
        'status_id' => $draftStatus->id,
    ]);

    $deletedResearch = Research::factory()->create([
        'research_code' => 'RSH-'.now()->format('Y').'-0002',
        'lead_proponent_id' => $user->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'category_id' => $category->id,
        'status_id' => $draftStatus->id,
    ]);

    $deletedResearch->delete();

    Storage::fake('local');

    $this->actingAs($user)->post(route('researches.store'), [
        'title' => 'Soft Deleted Code Reuse Guard',
        'category_id' => $category->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'uploader_proponent_first_name' => 'John',
        'uploader_proponent_middle_name' => 'Q',
        'uploader_proponent_last_name' => 'Doe',
        'uploader_proponent_suffix' => null,
        'uploader_proponent_position_title' => 'Teacher I',
        'uploader_proponent_organizational_unit_id' => $user->organizational_unit_id,
        'uploader_proponent_email' => 'john.doe@example.com',
        'uploader_proponent_contact_number' => '09123456789',
        'uploader_proponent_photo' => UploadedFile::fake()->create('uploader.jpg', 64, 'image/jpeg'),
        'research_manuscript_file' => UploadedFile::fake()->create('manuscript.pdf', 64, 'application/pdf'),
        'narrative_form_file' => UploadedFile::fake()->create('narrative.pdf', 64, 'application/pdf'),
        'documentation_file' => UploadedFile::fake()->create('documentation.pdf', 64, 'application/pdf'),
    ]);

    expect(Research::query()->latest('id')->firstOrFail()->research_code)
        ->toBe('RSH-'.now()->format('Y').'-0003');
});

test('research cannot be submitted without proponents and documents', function () {
    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();
    $draftStatus = ResearchStatus::query()->create([
        'status_name' => 'Draft',
        'description' => 'Research draft is awaiting completion before final submission.',
    ]);

    $research = Research::factory()->create([
        'lead_proponent_id' => $user->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'category_id' => $category->id,
        'status_id' => $draftStatus->id,
        'submitted_at' => null,
    ]);

    $response = $this->from(route('researches.show', $research))
        ->actingAs($user)
        ->post(route('researches.submit', $research));

    $response->assertRedirect(route('researches.show', $research, absolute: false));
    $response->assertSessionHasErrors(['proponents', 'documents']);

    $research->refresh();
    expect($research->status?->status_name)->toBe('Draft');
    expect($research->submitted_at)->toBeNull();
});

test('research cannot be submitted when a proponent has no photo', function () {
    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();
    $draftStatus = ResearchStatus::query()->create([
        'status_name' => 'Draft',
        'description' => 'Research draft is awaiting completion before final submission.',
    ]);

    $research = Research::factory()->create([
        'lead_proponent_id' => $user->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'category_id' => $category->id,
        'status_id' => $draftStatus->id,
        'submitted_at' => null,
    ]);

    ResearchProponent::query()->create([
        'research_id' => $research->id,
        'first_name' => 'Jane',
        'middle_name' => 'Q',
        'last_name' => 'Researcher',
        'suffix' => null,
        'position_title' => 'Teacher I',
        'organizational_unit_name' => 'Default Organizational Unit',
        'email' => 'jane@example.com',
        'contact_number' => '09123456789',
    ]);

    ResearchDocument::query()->create([
        'research_id' => $research->id,
        'document_type' => 'Proposal Narrative Document',
        'original_filename' => 'proposal.pdf',
        'stored_filename' => 'stored-proposal.pdf',
        'file_path' => 'research_documents/'.$research->research_code.'/stored-proposal.pdf',
        'storage_disk' => 'local',
        'file_extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'uploaded_by' => $user->id,
        'uploaded_at' => now(),
    ]);

    $response = $this->from(route('researches.show', $research))
        ->actingAs($user)
        ->post(route('researches.submit', $research));

    $response->assertRedirect(route('researches.show', $research, absolute: false));
    $response->assertSessionHasErrors(['proponents']);

    $research->refresh();
    expect($research->status?->status_name)->toBe('Draft');
    expect($research->submitted_at)->toBeNull();
});

test('research document upload only accepts pdf files', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();
    $draftStatus = ResearchStatus::query()->create([
        'status_name' => 'Draft',
        'description' => 'Research draft is awaiting completion before final submission.',
    ]);

    $research = Research::factory()->create([
        'lead_proponent_id' => $user->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'category_id' => $category->id,
        'status_id' => $draftStatus->id,
    ]);

    $response = $this->from(route('researches.show', $research))
        ->actingAs($user)
        ->post(route('researches.documents.store', $research), [
            'document_class' => 'research_documentation',
            'file' => UploadedFile::fake()->create('proposal.docx', 64, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]);

    $response->assertRedirect(route('researches.show', $research, absolute: false));
    $response->assertSessionHasErrors('file');
});

test('research can be submitted once a proponent and all required pdf documents exist', function () {
    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();
    $draftStatus = ResearchStatus::query()->create([
        'status_name' => 'Draft',
        'description' => 'Research draft is awaiting completion before final submission.',
    ]);
    ResearchStatus::query()->create([
        'status_name' => 'Submitted',
        'description' => 'Research has been successfully submitted',
    ]);

    $research = Research::factory()->create([
        'lead_proponent_id' => $user->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'category_id' => $category->id,
        'status_id' => $draftStatus->id,
        'submitted_at' => null,
    ]);

    ResearchProponent::query()->create([
        'research_id' => $research->id,
        'first_name' => 'Jane',
        'middle_name' => 'Q',
        'last_name' => 'Researcher',
        'suffix' => null,
        'position_title' => 'Teacher I',
        'organizational_unit_name' => 'Default Organizational Unit',
        'email' => 'jane@example.com',
        'contact_number' => '09123456789',
        'photo_path' => 'research_proponents/'.$research->research_code.'/jane.jpg',
        'photo_disk' => 'local',
        'photo_filename' => 'jane.jpg',
    ]);

    ResearchDocument::query()->create([
        'research_id' => $research->id,
        'document_type' => 'Research Manuscript',
        'original_filename' => 'manuscript.pdf',
        'stored_filename' => 'stored-manuscript.pdf',
        'file_path' => 'research_documents/'.$research->research_code.'/stored-manuscript.pdf',
        'storage_disk' => 'local',
        'file_extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'uploaded_by' => $user->id,
        'uploaded_at' => now(),
    ]);

    ResearchDocument::query()->create([
        'research_id' => $research->id,
        'document_type' => 'Narrative Form Document',
        'original_filename' => 'narrative.pdf',
        'stored_filename' => 'stored-narrative.pdf',
        'file_path' => 'research_documents/'.$research->research_code.'/stored-narrative.pdf',
        'storage_disk' => 'local',
        'file_extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'uploaded_by' => $user->id,
        'uploaded_at' => now(),
    ]);

    ResearchDocument::query()->create([
        'research_id' => $research->id,
        'document_type' => 'Research Documentation',
        'original_filename' => 'documentation.pdf',
        'stored_filename' => 'stored-documentation.pdf',
        'file_path' => 'research_documents/'.$research->research_code.'/stored-documentation.pdf',
        'storage_disk' => 'local',
        'file_extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'uploaded_by' => $user->id,
        'uploaded_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('researches.submit', $research));

    $response->assertRedirect();

    $research->refresh();
    expect($research->status?->status_name)->toBe('Submitted');
    expect($research->submitted_at)->not->toBeNull();
    expect($research->versions()->count())->toBe(1);

    $version = $research->versions()->with('files', 'submissionType')->firstOrFail();

    expect($version->submissionType?->type_name)->toBe('Proposal');
    expect($version->files()->count())->toBe(3);
});

test('resubmitting after document changes creates a new research version snapshot', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();
    $submittedStatus = ResearchStatus::query()->firstOrCreate([
        'status_name' => 'Submitted',
    ], [
        'description' => 'Research has been successfully submitted',
    ]);
    SubmissionType::query()->firstOrCreate([
        'type_name' => 'Proposal',
    ], [
        'description' => 'Proposal stage submission.',
    ]);

    $research = Research::factory()->create([
        'lead_proponent_id' => $user->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'category_id' => $category->id,
        'status_id' => $submittedStatus->id,
        'submitted_at' => now()->subDay(),
    ]);

    ResearchProponent::query()->create([
        'research_id' => $research->id,
        'first_name' => 'Jane',
        'middle_name' => 'Q',
        'last_name' => 'Researcher',
        'suffix' => null,
        'position_title' => 'Teacher I',
        'organizational_unit_name' => 'Default Organizational Unit',
        'email' => 'jane@example.com',
        'contact_number' => '09123456789',
        'photo_path' => 'research_proponents/'.$research->research_code.'/jane.jpg',
        'photo_disk' => 'local',
        'photo_filename' => 'jane.jpg',
    ]);

    $manuscript = ResearchDocument::query()->create([
        'research_id' => $research->id,
        'document_type' => 'Research Manuscript',
        'original_filename' => 'manuscript-v1.pdf',
        'stored_filename' => 'stored-manuscript-v1.pdf',
        'file_path' => 'research_documents/'.$research->research_code.'/stored-manuscript-v1.pdf',
        'storage_disk' => 'local',
        'file_extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'uploaded_by' => $user->id,
        'uploaded_at' => now()->subDay(),
    ]);

    ResearchDocument::query()->create([
        'research_id' => $research->id,
        'document_type' => 'Narrative Form Document',
        'original_filename' => 'narrative-v1.pdf',
        'stored_filename' => 'stored-narrative-v1.pdf',
        'file_path' => 'research_documents/'.$research->research_code.'/stored-narrative-v1.pdf',
        'storage_disk' => 'local',
        'file_extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'uploaded_by' => $user->id,
        'uploaded_at' => now()->subDay(),
    ]);

    ResearchDocument::query()->create([
        'research_id' => $research->id,
        'document_type' => 'Research Documentation',
        'original_filename' => 'documentation-v1.pdf',
        'stored_filename' => 'stored-documentation-v1.pdf',
        'file_path' => 'research_documents/'.$research->research_code.'/stored-documentation-v1.pdf',
        'storage_disk' => 'local',
        'file_extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1024,
        'uploaded_by' => $user->id,
        'uploaded_at' => now()->subDay(),
    ]);

    $firstResponse = $this->actingAs($user)->post(route('researches.submit', $research));
    $firstResponse->assertRedirect();

    $research->refresh();

    $this->actingAs($user)->post(route('researches.documents.store', $research), [
        'document_class' => 'research_manuscript',
        'file' => UploadedFile::fake()->create('manuscript-v2.pdf', 64, 'application/pdf'),
    ])->assertRedirect();

    $secondResponse = $this->actingAs($user)->post(route('researches.submit', $research));
    $secondResponse->assertRedirect();

    expect($research->fresh()->versions()->count())->toBe(2);
    expect($research->fresh()->versions()->where('is_current', true)->count())->toBe(1);

    $latestVersion = $research->fresh()->versions()->with('files')->latest('id')->firstOrFail();

    expect($latestVersion->parent_version_id)->not->toBeNull();
    expect($latestVersion->files()->count())->toBe(3);
    expect($latestVersion->files()->where('document_name', 'Research Manuscript')->value('stored_file_name'))
        ->not->toBe($manuscript->stored_filename);
});

test('submitted research information and proponents can still be updated', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();
    $otherCategory = ResearchCategory::factory()->create();
    $submittedStatus = ResearchStatus::query()->create([
        'status_name' => 'Submitted',
        'description' => 'Research has been successfully submitted',
    ]);

    $research = Research::factory()->create([
        'lead_proponent_id' => $user->id,
        'organizational_unit_id' => $user->organizational_unit_id,
        'category_id' => $category->id,
        'status_id' => $submittedStatus->id,
        'submitted_at' => now(),
    ]);

    $proponent = ResearchProponent::query()->create([
        'research_id' => $research->id,
        'first_name' => 'Jane',
        'middle_name' => 'Q',
        'last_name' => 'Researcher',
        'suffix' => null,
        'position_title' => 'Teacher I',
        'organizational_unit_name' => 'Default Organizational Unit',
        'email' => 'jane@example.com',
        'contact_number' => '09123456789',
    ]);

    $updateResearchResponse = $this->actingAs($user)->put(route('researches.update', $research), [
        'title' => 'Updated Submitted Research',
        'category_id' => $otherCategory->id,
        'organizational_unit_id' => $user->organizational_unit_id,
    ]);

    $updateResearchResponse->assertRedirect(route('researches.show', $research, absolute: false));

    $updateProponentResponse = $this->actingAs($user)->put(route('researches.proponents.update', [$research, $proponent]), [
        'first_name' => 'Janet',
        'middle_name' => 'R',
        'last_name' => 'Researcher',
        'suffix' => null,
        'position_title' => 'Teacher II',
        'organizational_unit_id' => $user->organizational_unit_id,
        'email' => 'janet@example.com',
        'contact_number' => '09999999999',
        'photo' => UploadedFile::fake()->create('janet.jpg', 64, 'image/jpeg'),
    ]);

    $updateProponentResponse->assertRedirect();

    $research->refresh();
    $proponent->refresh();

    expect($research->title)->toBe('Updated Submitted Research');
    expect($research->category_id)->toBe($otherCategory->id);
    expect($proponent->first_name)->toBe('Janet');
    expect($proponent->middle_name)->toBe('R');
    expect($proponent->position_title)->toBe('Teacher II');
    expect($proponent->email)->toBe('janet@example.com');
});