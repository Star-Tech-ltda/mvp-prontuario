<?php

namespace App\Filament\Widgets;

use App\Models\Banner;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class BannerWidget extends Widget
{
    protected static string $view = 'filament.widgets.banner-widget';

    public function render(): View
    {
        $banner = Banner::latest()->first();

        return view(static::$view, compact('banner'));
    }



}
