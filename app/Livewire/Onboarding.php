<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\TenantInvite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// 1. IMPORT THE CORRECT CLASSES
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

// 2. IMPLEMENT THE CORRECT INTERFACES
class Onboarding extends Component implements HasForms, HasActions
{
    // 3. USE THE CORRECT TRAITS
    use InteractsWithForms;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        // This is still required and correct
        $this->form->fill();
    }

    // 4. DEFINE THE FORM USING THE FORM OBJECT
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Create a New Company Workspace')
                    ->schema([
                        TextInput::make('tenant_name')
                            ->label('Company Name')
                            ->required(fn (string $operation) => $operation === 'createTenant'),
                        Select::make('tenant_type')
                            ->label('Business Type')
                            ->options([
                                'accounting' => 'Accounting',
                                'commercial' => 'Commercial',
                            ])
                            ->required(fn (string $operation) => $operation === 'createTenant'),
                    ]),
                
                Section::make('Or Join an Existing Workspace')
                    ->schema([
                        TextInput::make('invite_code')
                            ->label('Invite Code')
                            ->helperText('Ask your administrator for an invite code.')
                            ->required(fn (string $operation) => $operation === 'joinTenant'),
                    ]),
            ])
            ->statePath('data');
    }

    public function createTenantAction(): Action
    {
        return Action::make('createTenant')
            ->label('Create Tenant')
            ->action('createTenant');
    }

    public function joinTenantAction(): Action
    {
        return Action::make('joinTenant')
            ->label('Join with Code')
            ->color('gray')
            ->action('joinTenant');
    }

    public function createTenant(): void
    {
        $data = $this->form->getState(operation: 'createTenant');
        $user = Auth::user();

        $tenant = Tenant::create([
            'name' => $data['tenant_name'],
            'slug' => Str::slug($data['tenant_name']),
            'type' => $data['tenant_type'],
        ]);

        $user->tenant_id = $tenant->id;
        $user->save();

        redirect('/dashboard');
    }

    public function joinTenant(): void
    {
        $data = $this->form->getState(operation: 'joinTenant');
        $user = Auth::user();
        
        $invite = TenantInvite::where('code', $data['invite_code'])->first();

        if (! $invite) {
            $this->addError('data.invite_code', 'This invite code is not valid.');
            return;
        }

        if ($invite->used_by) {
            $this->addError('data.invite_code', 'This invite code has already been used.');
            return;
        }

        $user->tenant_id = $invite->tenant_id;
        $user->save();

        $invite->used_by = $user->id;
        $invite->save();

        redirect('/dashboard');
    }
    
    public function render()
    {
        return view('livewire.onboarding'); // Simplified for clarity
    }
}