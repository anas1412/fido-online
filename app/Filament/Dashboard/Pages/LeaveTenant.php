<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class LeaveTenant extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = null;

    public static function leaveAction(): Action
    {
        return Action::make('leaveTenant')
            ->label(fn () => 'Quitter ' . (filament()->getTenant()?->name ?? 'l\'organisation'))
            ->icon('heroicon-o-arrow-left-on-rectangle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(fn () => 'Quitter ' . (filament()->getTenant()?->name ?? 'l\'organisation') . ' ?')
            ->modalSubheading('Cette action retirera votre accès à cette organisation.')
            ->modalButton('Oui, quitter')
            ->action(function () {
                $user = Auth::user();
                $tenant = filament()->getTenant();

                if (! $user || ! $tenant) {
                    Notification::make()
                        ->title('Erreur')
                        ->body('Impossible de quitter l\'organisation.')
                        ->danger()
                        ->send();

                    return redirect(filament()->getPanel('dashboard')->getUrl());
                }

                $user->tenants()->detach($tenant->id);

                Notification::make()
                    ->title('Succès')
                    ->body("Vous avez quitté l'organisation {$tenant->name}.")
                    ->success()
                    ->send();

                return redirect(filament()->getPanel('dashboard')->getUrl());
            });
    }
}
