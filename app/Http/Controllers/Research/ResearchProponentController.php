<?php

namespace App\Http\Controllers\Research;

use App\Http\Controllers\Controller;
use App\Http\Requests\Research\StoreResearchProponentRequest;
use App\Http\Requests\Research\UpdateResearchProponentRequest;
use App\Models\Research;
use App\Models\ResearchProponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResearchProponentController extends Controller
{
    public function store(StoreResearchProponentRequest $request, Research $research): RedirectResponse
    {
        $this->authorizeAccess($request, $research);

        $research->proponents()->create($request->safe()->except('organizational_unit_id'));

        return back()->with('status', 'Research proponent added successfully.');
    }

    public function update(UpdateResearchProponentRequest $request, Research $research, ResearchProponent $proponent): RedirectResponse
    {
        $this->authorizeProponentAccess($request, $research, $proponent);

        $proponent->update($request->safe()->except('organizational_unit_id'));

        return back()->with('status', 'Research proponent updated successfully.');
    }

    public function destroy(Request $request, Research $research, ResearchProponent $proponent): RedirectResponse
    {
        $this->authorizeProponentAccess($request, $research, $proponent);

        $proponent->delete();

        return back()->with('status', 'Research proponent removed successfully.');
    }

    private function authorizeProponentAccess(Request $request, Research $research, ResearchProponent $proponent): void
    {
        abort_unless((int) $proponent->research_id === (int) $research->id, 404);
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
