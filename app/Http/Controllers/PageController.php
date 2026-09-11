<?php

namespace App\Http\Controllers;

use App\Models\PlayItem;
use App\Models\Puzzle;
use App\Models\PuzzleSlugRedirect;
use App\Models\SiteSetting;
use App\Models\Sponsor;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(MediaService $media): View
    {
        $settings = SiteSetting::current();

        $playItems = PlayItem::query()->orderBy('sort_order')->orderBy('id')->get();
        $sponsors = Sponsor::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('public.home', [
            'settings' => $settings,
            'playItems' => $playItems,
            'playCards' => $this->loopCards($playItems),
            'sponsors' => $sponsors,
            'sponsorCards' => $this->loopCards($sponsors),
            'media' => $media,
        ]);
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        return back()->with('status', 'Mensaje validado correctamente.');
    }

    public function puzzles(MediaService $media, ?string $slug = null): View|RedirectResponse
    {
        if ($slug) {
            $redirect = PuzzleSlugRedirect::query()->where('old_slug', $slug)->first();
            if ($redirect && $redirect->puzzle && $redirect->puzzle->slug !== $slug) {
                return redirect()->route('puzzles.show', $redirect->puzzle->slug, 301);
            }
        }

        $puzzles = Puzzle::query()->orderBy('sort_order')->orderBy('id')->get();
        $active = $slug ? $puzzles->firstWhere('slug', $slug) : null;

        if ($slug && ! $active) {
            abort(404);
        }

        return view('public.puzzles', [
            'settings' => SiteSetting::current(),
            'puzzles' => $puzzles,
            'activeSlug' => $active?->slug,
            'media' => $media,
        ]);
    }

    private function loopCards($items)
    {
        if ($items->isEmpty()) {
            return $items;
        }

        $loop = $items;
        while ($loop->count() < 10) {
            $loop = $loop->concat($items);
        }

        return $loop;
    }
}
