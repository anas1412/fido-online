<?php

namespace App\Filament\Dashboard\Resources\TenantInvites\Pages;

use App\Filament\Dashboard\Resources\TenantInvites\TenantInviteResource;
use App\Models\TenantInvite;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ListTenantInvites extends ListRecords
{
    protected static string $resource = TenantInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createInvite')
                ->label('Créer une nouvelle invitation')
                ->action(function () {
                    do {
                        $code = Str::random(10);
                    } while (TenantInvite::where('code', $code)->exists());

                    $tenantInvite = TenantInvite::create([
                        'code' => $code,
                        'expires_at' => now()->addWeeks(2),
                        'tenant_id' => filament()->getTenant()->id,
                        'created_by' => Auth::id(),
                    ]);

                    Notification::make()
                        ->title('Invitation créée avec succès!')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('index'));
                }),
        ];
    }
}
