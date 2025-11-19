<?php

namespace App\Filament\Dashboard\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;

class Login extends BaseLogin
{
    // This points to your split-screen content view
    protected string $view = 'filament.dashboard.pages.auth.login';

    // ✅ ADD THIS METHOD
    // This overrides the default "Card" layout with your custom "Blank" layout
    public function getLayout(): string
    {
        return 'filament.dashboard.layouts.auth';
    }

    public function mount(): void
    {
        parent::mount();
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([])->statePath('data');
    }
}