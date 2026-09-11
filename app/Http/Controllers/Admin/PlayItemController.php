<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderRequest;
use App\Http\Requests\Admin\StorePlayItemRequest;
use App\Models\PlayItem;
use App\Services\MediaService;
use App\Support\SortsModels;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PlayItemController extends Controller
{
    public function index(MediaService $media): View
    {
        return view('admin.play-items.index', [
            'items' => PlayItem::query()->orderBy('sort_order')->orderBy('id')->get(),
            'media' => $media,
        ]);
    }

    public function create(): View
    {
        return view('admin.play-items.form', ['item' => new PlayItem]);
    }

    public function store(StorePlayItemRequest $request, MediaService $media): RedirectResponse
    {
        PlayItem::query()->create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'video_url' => $request->validated('video_url'),
            'image_path' => $media->storeImage($request->file('image'), 'play'),
            'sort_order' => (int) PlayItem::query()->max('sort_order') + 1,
        ]);

        return redirect()->route('admin.play-items.index')->with('status', 'Ítem de Juega creado.');
    }

    public function edit(PlayItem $playItem, MediaService $media): View
    {
        return view('admin.play-items.form', [
            'item' => $playItem,
            'media' => $media,
        ]);
    }

    public function update(StorePlayItemRequest $request, PlayItem $playItem, MediaService $media): RedirectResponse
    {
        $playItem->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'video_url' => $request->validated('video_url'),
            'image_path' => $media->replaceImage($playItem->image_path, $request->file('image'), 'play'),
        ]);

        return redirect()->route('admin.play-items.index')->with('status', 'Ítem de Juega actualizado.');
    }

    public function destroy(PlayItem $playItem, MediaService $media): RedirectResponse
    {
        $media->delete($playItem->image_path);
        $playItem->delete();

        return redirect()->route('admin.play-items.index')->with('status', 'Ítem eliminado.');
    }

    public function reorder(ReorderRequest $request): JsonResponse
    {
        SortsModels::apply(PlayItem::class, $request->validated('ids'));

        return response()->json(['ok' => true]);
    }
}
