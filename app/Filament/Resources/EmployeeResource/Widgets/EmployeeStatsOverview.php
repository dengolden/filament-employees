<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\country;
use App\Models\employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $us = country::where('country_code', 'US')->withCount('employees')->first();
        $uk = country::where('country_code', 'UK')->withCount('employees')->first();

        return [
            Card::make('All Employees', employee::all()->count()),
            Card::make('UK Employees', $uk ? $uk->employees_count :0),
            Card::make('US Employees', $us ? $us->employees_count :0),
        ];
    }
}
