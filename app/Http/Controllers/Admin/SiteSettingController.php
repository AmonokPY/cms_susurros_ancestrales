<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingRequest;
use App\Models\SiteSetting;
use App\Services\MediaService;
use App\Support\Html;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(MediaService $media): View
    {
        return view('admin.settings.edit', [
            'settings' => SiteSetting::current(),
            'media' => $media,
        ]);
    }

    public function update(UpdateSiteSettingRequest $request, MediaService $media): RedirectResponse
    {
        $settings = SiteSetting::current();
        $data = $request->safe()->except(['about_image', 'extra_socials']);

        $data['about_text'] = Html::sanitize($data['about_text'] ?? null);
        $data['contact_extra'] = Html::sanitize($data['contact_extra'] ?? null);
        $data['about_image_path'] = $media->replaceImage(
            $settings->about_image_path,
            $request->file('about_image'),
            'about'
        );

        $socials = [];
        foreach ($request->input('extra_socials', []) as $item) {
            if (! empty($item['name']) && ! empty($item['url'])) {
                $socials[] = [
                    'name' => $item['name'],
                    'url' => $item['url'],
                ];
            }
        }
        $data['extra_socials'] = $socials;

        $settings->update($data);

        return back()->with('status', 'Contenido de inicio actualizado.');
    }
}
