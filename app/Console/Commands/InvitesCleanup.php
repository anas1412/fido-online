<?php

namespace App\Console\Commands;

use App\Models\TenantInvite;
use Illuminate\Console\Command;

class InvitesCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invites:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes expired and unused tenant invite codes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted = TenantInvite::where('expires_at', '<=', now())
            ->whereNull('used_by')
            ->delete();

        $this->info("$deleted expired and unused invite codes deleted.");
    }
}
