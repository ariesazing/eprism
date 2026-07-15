<?php

namespace App\Console\Commands;

use App\Models\PreRegistrationVerification;
use Illuminate\Console\Command;

class PrunePreRegistrationVerifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pre-registration-verifications:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired pre-registration verification records.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deletedCount = PreRegistrationVerification::query()
            ->where('expires_at', '<=', now())
            ->delete();

        $this->info(sprintf('Deleted %d expired pre-registration verification record(s).', $deletedCount));

        return self::SUCCESS;
    }
}
