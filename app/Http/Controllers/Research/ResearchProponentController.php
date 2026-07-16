<?php

namespace App\Http\Controllers\Research;

use App\Http\Controllers\Controller;
use App\Http\Requests\Research\StoreResearchProponentRequest;
use App\Http\Requests\Research\UpdateResearchProponentRequest;
use App\Models\Research;
use App\Models\ResearchProponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResearchProponentController extends Controller
{
    public function store(StoreResearchProponentRequest $request, Research $research): RedirectResponse
    {
        $this->authorizeAccess($request, $research);

        $photo = $request->file('photo');
        $photoFilename = Str::uuid().'.'.$photo->getClientOriginalExtension();
        $photoDirectory = 'research_proponents/'.$research->research_code;
        $photoDisk = 'local';
        $photoPath = $photo->storeAs($photoDirectory, $photoFilename, $photoDisk);

        $research->proponents()->create([
            ...$request->safe()->except('organizational_unit_id', 'photo'),
            'photo_path' => $photoPath,
            'photo_disk' => $photoDisk,
            'photo_filename' => $photoFilename,
        ]);

        return back()->with('status', 'Research proponent added successfully.');
    }

    public function update(UpdateResearchProponentRequest $request, Research $research, ResearchProponent $proponent): RedirectResponse
    {
        $this->authorizeProponentAccess($request, $research, $proponent);

        $payload = $request->safe()->except('organizational_unit_id', 'photo');

        if ($request->hasFile('photo')) {
            if ($proponent->photo_path !== null && $proponent->photo_disk !== null) {
                Storage::disk($proponent->photo_disk)->delete($proponent->photo_path);
            }

            $photo = $request->file('photo');
            $photoFilename = Str::uuid().'.'.$photo->getClientOriginalExtension();
            $photoDirectory = 'research_proponents/'.$research->research_code;
            $photoDisk = 'local';
            $photoPath = $photo->storeAs($photoDirectory, $photoFilename, $photoDisk);

            $payload = [
                ...$payload,
                'photo_path' => $photoPath,
                'photo_disk' => $photoDisk,
                'photo_filename' => $photoFilename,
            ];
        }

        $proponent->update($payload);

        return back()->with('status', 'Research proponent updated successfully.');
    }

    public function destroy(Request $request, Research $research, ResearchProponent $proponent): RedirectResponse
    {
        $this->authorizeProponentAccess($request, $research, $proponent);

        if ($proponent->photo_path !== null && $proponent->photo_disk !== null) {
            Storage::disk($proponent->photo_disk)->delete($proponent->photo_path);
        }

        $proponent->delete();

        return back()->with('status', 'Research proponent removed successfully.');
    }

    public function photo(Request $request, Research $research, ResearchProponent $proponent): BinaryFileResponse
    {
        $this->authorizeProponentAccess($request, $research, $proponent);

        abort_unless($proponent->photo_path !== null && $proponent->photo_disk !== null, 404);

        return response()->file(Storage::disk($proponent->photo_disk)->path($proponent->photo_path));
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
