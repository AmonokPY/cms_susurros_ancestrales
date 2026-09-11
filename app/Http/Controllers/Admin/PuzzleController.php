<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderRequest;
use App\Http\Requests\Admin\StorePuzzleRequest;
use App\Models\Puzzle;
use App\Models\PuzzleSlugRedirect;
use App\Services\MediaService;
use App\Support\Html;
use App\Support\SortsModels;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PuzzleController extends Controller
{
    public function index(MediaService $media): View
    {
        return view('admin.puzzles.index', [
            'items' => Puzzle::query()->orderBy('sort_order')->orderBy('id')->get(),
            'media' => $media,
        ]);
    }

    public function create(): View
    {
        return view('admin.puzzles.form', ['item' => new Puzzle]);
    }

    public function store(StorePuzzleRequest $request, MediaService $media): RedirectResponse
    {
        Puzzle::query()->create($this->payload($request, $media, new Puzzle));

        return redirect()->route('admin.puzzles.index')->with('status', 'Puzzle creado.');
    }

    public function edit(Puzzle $puzzle, MediaService $media): View
    {
        return view('admin.puzzles.form', [
            'item' => $puzzle,
            'media' => $media,
        ]);
    }

    public function update(StorePuzzleRequest $request, Puzzle $puzzle, MediaService $media): RedirectResponse
    {
        $oldSlug = $puzzle->slug;
        $puzzle->update($this->payload($request, $media, $puzzle));

        if ($oldSlug !== $puzzle->slug) {
            PuzzleSlugRedirect::query()
                ->where('old_slug', $puzzle->slug)
                ->orWhere(function ($query) use ($puzzle, $oldSlug) {
                    $query->where('puzzle_id', $puzzle->id)->where('old_slug', $puzzle->slug);
                })
                ->delete();
            PuzzleSlugRedirect::query()->updateOrCreate(
                ['old_slug' => $oldSlug],
                ['puzzle_id' => $puzzle->id]
            );
        }

        return redirect()->route('admin.puzzles.index')->with('status', 'Puzzle actualizado.');
    }

    public function destroy(Puzzle $puzzle, MediaService $media): RedirectResponse
    {
        $media->delete($puzzle->cover_image_path);
        $media->delete($puzzle->extra_image_path);
        $media->delete($puzzle->audio_path);
        $puzzle->delete();

        return redirect()->route('admin.puzzles.index')->with('status', 'Puzzle eliminado.');
    }

    public function reorder(ReorderRequest $request): JsonResponse
    {
        SortsModels::apply(Puzzle::class, $request->validated('ids'));

        return response()->json(['ok' => true]);
    }

    private function payload(StorePuzzleRequest $request, MediaService $media, Puzzle $puzzle): array
    {
        $data = $request->safe()->except(['cover_image', 'extra_image', 'audio']);
        $data['description'] = Html::sanitize($data['description'] ?? null);
        $data['benefits'] = Html::sanitize($data['benefits'] ?? null);
        $data['sort_order'] = ($data['sort_order'] ?? '') === '' || $data['sort_order'] === null
            ? (int) Puzzle::query()->max('sort_order') + 1
            : (int) $data['sort_order'];
        $data['cover_image_path'] = $media->replaceImage(
            $puzzle->cover_image_path,
            $request->file('cover_image'),
            'puzzles'
        );
        $data['extra_image_path'] = $media->replaceImage(
            $puzzle->extra_image_path,
            $request->file('extra_image'),
            'puzzles'
        );
        $data['audio_path'] = $media->replaceAudio(
            $puzzle->audio_path,
            $request->file('audio')
        );

        return $data;
    }
}
