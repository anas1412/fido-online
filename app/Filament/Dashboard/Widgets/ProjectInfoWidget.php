<?php

namespace App\Filament\Dashboard\Widgets;

use Filament\Widgets\Widget;

class ProjectInfoWidget extends Widget
{
    protected string $view = 'filament.dashboard.widgets.project-info-widget';

    protected static ?int $sort = 1;
    
    // Define a public property. Livewire automatically makes 
    // public properties available to the Blade view.
    public string $version;

    public function mount(): void
    {
        // Retrieve from the cached config
        $this->version = config('app.version', 'N/A');
    }

    // You no longer need getViewData(), but if you prefer it over 
    // public properties, you can keep it and return config('app.version') there.
}