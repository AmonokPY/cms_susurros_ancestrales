<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlayItem;
use App\Models\Puzzle;
use App\Models\Sponsor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'playCount' => PlayItem::query()->count(),
            'sponsorCount' => Sponsor::query()->count(),
            'puzzleCount' => Puzzle::query()->count(),
        ]);
    }
}
