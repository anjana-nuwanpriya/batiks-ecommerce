<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PhoneVerification;

class CleanupExpiredOTPs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired OTP codes from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedCount = PhoneVerification::cleanupExpired();

        $this->info("Cleaned up {$deletedCount} expired OTP records.");

        return Command::SUCCESS;
    }
}
