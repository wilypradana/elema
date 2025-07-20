<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Panduan Aplikasi',false)
                ->icon('heroicon-o-book-open')
                ->url(route('panduan-admin'), true)
                ->color('primary')
        ];
    }

}
