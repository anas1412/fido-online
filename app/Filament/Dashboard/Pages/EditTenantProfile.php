<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Filament\Schemas\Schema;
// Reuse the schema we just built for consistency!
use App\Filament\Resources\Tenants\Schemas\TenantForm; 
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditTenantProfile extends BaseEditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Paramètres de l\'entreprise';
    }

    public function form(Schema $schema): Schema
    {
        // We simply call the static configure method from TenantForm
        // This keeps your Admin Panel and User Dashboard perfectly synced.
        return TenantForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deleteTenant')
                ->label("Supprimer l'organisation")
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading("Supprimer l'organisation ?")
                ->modalSubheading("Attention : Cette action est irréversible. Toutes les factures et données seront effacées.")
                ->action(function () {
                    $this->tenant->delete();
                    Notification::make()->title('Organisation supprimée')->success()->send();
                    return redirect()->to('/dashboard');
                })
                ->visible(fn () => Auth::user()->tenants()->where('tenant_id', filament()->getTenant()->id)->wherePivot('is_owner', true)->exists()),
        ];
    }
}