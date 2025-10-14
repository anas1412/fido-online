<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Illuminate\Support\Str;

class EditTenantProfile extends BaseEditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Tenant Profile';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->live()
                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->readOnly(),
                TextInput::make('type')
                    ->disabled(), // Display tenant type, but don't allow editing
            ]);
    }

    protected function getRedirectUrl(): string
    {
        $this->tenant->refresh();
        return filament()->getUrl(tenant: $this->tenant);
    }
}