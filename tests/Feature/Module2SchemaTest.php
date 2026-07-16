<?php

use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\ResearchDocument;
use App\Models\ResearchProponent;
use App\Models\ResearchStatus;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('has all module 2 tables after migration', function (): void {
    expect(Schema::hasTable('research_categories'))->toBeTrue();
    expect(Schema::hasTable('research_statuses'))->toBeTrue();
    expect(Schema::hasTable('researches'))->toBeTrue();
    expect(Schema::hasTable('research_proponents'))->toBeTrue();
    expect(Schema::hasTable('research_documents'))->toBeTrue();
});

it('contains module 2 seed values after seeding', function (): void {
    Artisan::call('db:seed');

    expect(DB::table('research_statuses')->where('status_name', 'Draft')->exists())->toBeTrue();
    expect(DB::table('research_categories')->where('category_name', 'Action Research')->exists())->toBeTrue();
    expect(DB::table('research_categories')->where('category_name', 'Basic Research')->exists())->toBeTrue();

    expect(DB::table('research_statuses')->where('status_name', 'Submitted')->exists())->toBeTrue();
    expect(DB::table('research_statuses')->where('status_name', 'Under SRAM Assessment')->exists())->toBeTrue();
    expect(DB::table('research_statuses')->where('status_name', 'Under Review')->exists())->toBeTrue();
    expect(DB::table('research_statuses')->where('status_name', 'Revision Required')->exists())->toBeTrue();
    expect(DB::table('research_statuses')->where('status_name', 'Approved')->exists())->toBeTrue();
    expect(DB::table('research_statuses')->where('status_name', 'Archived')->exists())->toBeTrue();
});

it('enforces key module 2 foreign key behaviors', function (): void {
    Artisan::call('db:seed');

    $category = ResearchCategory::query()->where('category_name', 'Action Research')->firstOrFail();
    $status = ResearchStatus::query()->where('status_name', 'Submitted')->firstOrFail();
    $leadUser = User::factory()->create();
    $uploader = User::factory()->create();

    $research = Research::factory()->create([
        'category_id' => $category->id,
        'status_id' => $status->id,
        'lead_proponent_id' => $leadUser->id,
    ]);

    $proponent = ResearchProponent::factory()->create([
        'research_id' => $research->id,
    ]);

    $document = ResearchDocument::factory()->create([
        'research_id' => $research->id,
        'uploaded_by' => $uploader->id,
    ]);

    expect(fn (): bool => (bool) $category->delete())->toThrow(QueryException::class);
    expect(fn (): bool => (bool) $uploader->forceDelete())->toThrow(QueryException::class);

    $research->forceDelete();

    expect(DB::table('research_proponents')->where('id', $proponent->id)->exists())->toBeFalse();
    expect(DB::table('research_documents')->where('id', $document->id)->exists())->toBeFalse();
});
