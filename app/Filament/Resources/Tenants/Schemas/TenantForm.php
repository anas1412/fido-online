<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->label('Identifiant unique')
                    ->required(),
                Select::make('type')
                    ->options(['accounting' => 'Accounting', 'commercial' => 'Commercial'])
                    ->required(),
            ]);
    }
}
