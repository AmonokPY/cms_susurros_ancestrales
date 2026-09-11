<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderRequest;
use App\Http\Requests\Admin\StoreSponsorRequest;
use App\Models\Sponsor;
use App\Services\MediaService;
use App\Support\SortsModels;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(MediaService $media): View
    {
        return view('admin.sponsors.index', [
            'items' => Sponsor::query()->orderBy('sort_order')->orderBy('id')->get(),
            'media' => $media,
        ]);
    }

    public function create(): View
    {
        return view('admin.sponsors.form', ['item' => new Sponsor]);
    }

    public function store(StoreSponsorRequest $request, MediaService $media): RedirectResponse
    {
        Sponsor::query()->create([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'website_url' => $request->validated('website_url'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email'),
            'image_path' => $media->storeImage($request->file('image'), 'sponsors'),
            'sort_order' => (int) Sponsor::query()->max('sort_order') + 1,
        ]);

        return redirect()->route('admin.sponsors.index')->with('status', 'Patrocinador creado.');
    }

    public function edit(Sponsor $sponsor, MediaService $media): View
    {
        return view('admin.sponsors.form', [
            'item' => $sponsor,
            'media' => $media,
        ]);
    }

    public function update(StoreSponsorRequest $request, Sponsor $sponsor, MediaService $media): RedirectResponse
    {
        $sponsor->update([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'website_url' => $request->validated('website_url'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email'),
            'image_path' => $media->replaceImage($sponsor->image_path, $request->file('image'), 'sponsors'),
        ]);

        return redirect()->route('admin.sponsors.index')->with('status', 'Patrocinador actualizado.');
    }

    public function destroy(Sponsor $sponsor, MediaService $media): RedirectResponse
    {
        $media->delete($sponsor->image_path);
        $sponsor->delete();

        return redirect()->route('admin.sponsors.index')->with('status', 'Patrocinador eliminado.');
    }

    public function reorder(ReorderRequest $request): JsonResponse
    {
        SortsModels::apply(Sponsor::class, $request->validated('ids'));

        return response()->json(['ok' => true]);
    }
}
