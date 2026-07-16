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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'defaultOrganizationalUnitId' => $request->user()?->organizational_unit_id,
            'isAdmin' => $request->user()?->role?->role_name === 'Administrator',
        ]);
    }

    public function store(StoreResearchRequest $request, ResearchCodeGenerator $codeGenerator): RedirectResponse
    {
        $draftStatusId = $this->resolveStatusId(
            'Draft',
            'Research draft is awaiting completion before final submission.'
        );

        $research = Research::query()->create([
            'research_code' => $codeGenerator->generate(),
            'title' => $request->string('title')->toString(),
            'lead_proponent_id' => $request->user()->id,
            'organizational_unit_id' => (int) $request->integer('organizational_unit_id'),
            'category_id' => (int) $request->integer('category_id'),
            'status_id' => $draftStatusId,
            'submitted_at' => null,
            'approved_at' => null,
            'archived_at' => null,
        ]);

        return redirect()->route('researches.show', $research)->with(
            'status',
            'Research draft created. Add at least one proponent and one PDF document to submit it.'
        );
    }

    public function submit(Request $request, Research $research): RedirectResponse
    {
        $this->authorizeAccess($request, $research);

        $errors = [];

        if (! $research->proponents()->exists()) {
            $errors['proponents'] = 'Add at least one research proponent before submitting.';
        }

        if (! $research->documents()->exists()) {
            $errors['documents'] = 'Upload at least one PDF research document before submitting.';
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
        ]);
    }

    public function edit(Request $request, Research $research): View
    {
        $this->authorizeAccess($request, $research);

        return view('researches.edit', [
            'research' => $research,
            'categories' => ResearchCategory::query()->orderBy('category_name')->get(['id', 'category_name']),
            'statuses' => ResearchStatus::query()->orderBy('status_name')->get(['id', 'status_name']),
            'organizationalUnits' => OrganizationalUnit::query()->orderBy('unit_name')->get(['id', 'unit_name', 'unit_code']),
            'isAdmin' => $request->user()?->role?->role_name === 'Administrator',
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

        $research->delete();

        return redirect()->route('researches.index')->with('status', 'Research record archived from active list.');
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
