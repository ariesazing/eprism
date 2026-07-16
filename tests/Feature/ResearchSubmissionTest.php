<?php

use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\ResearchDocument;
use App\Models\ResearchProponent;
use App\Models\ResearchStatus;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('research creation starts as a draft', function () {
    $user = User::factory()->create();
    $category = ResearchCategory::factory()->create();

    $response = $this->actingAs($user)->post(route('researches.store'), [
        'title' => 'Effects of Example Validation Rules',
        'category_id' => $category->id,
        'organizational_unit_id' => $user->organizational_unit_id,
    ]);

    $research = Research::query()->firstOrFail();

    $response->assertRedirect(route('researches.show', $research, absolute: false));
    expect($research->status?->status_name)->toBe('Draft');
    expect($research->submitted_at)->toBeNull();
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
            'document_type' => 'Proposal',
            'file' => UploadedFile::fake()->create('proposal.docx', 64, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]);

    $response->assertRedirect(route('researches.show', $research, absolute: false));
    $response->assertSessionHasErrors('file');
});

test('research can be submitted once a proponent and pdf document exist', function () {
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
    ]);

    ResearchDocument::query()->create([
        'research_id' => $research->id,
        'document_type' => 'Proposal',
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

    $response = $this->actingAs($user)->post(route('researches.submit', $research));

    $response->assertRedirect();

    $research->refresh();
    expect($research->status?->status_name)->toBe('Submitted');
    expect($research->submitted_at)->not->toBeNull();
});