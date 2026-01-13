<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\SmsService;
use Illuminate\Console\Command;

class TestPendingOrdersSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:test-pending-sms {--phone= : Test phone number to send SMS}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the pending orders SMS notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Pending Orders SMS Notification System');
        $this->info('==============================================');

        // Check SMS service status
        $smsService = new SmsService();
        $serviceStatus = $smsService->getServiceStatus();

        $this->info('SMS Service Status:');
        $this->line('- Enabled: ' . ($serviceStatus['enabled'] ? 'Yes' : 'No'));
        $this->line('- Username Configured: ' . ($serviceStatus['username_configured'] ? 'Yes' : 'No'));
        $this->line('- Password Configured: ' . ($serviceStatus['password_configured'] ? 'Yes' : 'No'));
        $this->line('- Campaign Configured: ' . ($serviceStatus['campaign_configured'] ? 'Yes' : 'No'));
        $this->line('- Mask Configured: ' . ($serviceStatus['mask_configured'] ? 'Yes' : 'No'));
        $this->line('- Debug Mode: ' . ($serviceStatus['debug_mode'] ? 'Yes' : 'No'));
        $this->newLine();

        if (!$serviceStatus['enabled']) {
            $this->error('SMS service is not enabled. Please enable it in your .env file.');
            return 1;
        }

        // Get pending orders count
        $pendingOrdersCount = Order::where('payment_status', 'pending')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->count();

        $this->info("Pending Orders (Last 7 days): {$pendingOrdersCount}");
        $this->newLine();

        // Get admin phones
        $adminPhones = $this->getAdminPhones();
        $this->info('Admin Phone Numbers:');
        if (empty($adminPhones)) {
            $this->error('No admin phone numbers configured!');
            $this->line('Please configure admin notification phones in Site Settings.');
            return 1;
        }

        foreach ($adminPhones as $phone) {
            $this->line("- {$phone}");
        }
        $this->newLine();

        // Test phone option
        $testPhone = $this->option('phone');
        if ($testPhone) {
            if (!$this->isValidPhone($testPhone)) {
                $this->error("Invalid phone number: {$testPhone}");
                return 1;
            }

            $this->info("Sending test SMS to: {$testPhone}");
            $message = $this->buildTestMessage($pendingOrdersCount);

            if ($smsService->sendCustomSms($testPhone, $message)) {
                $this->info('✅ Test SMS sent successfully!');
            } else {
                $this->error('❌ Failed to send test SMS');
                return 1;
            }
        } else {
            // Show what would be sent
            $message = $this->buildTestMessage($pendingOrdersCount);
            $this->info('Message that would be sent:');
            $this->line("'{$message}'");
            $this->newLine();

            if ($this->confirm('Do you want to send test SMS to all admin phones?')) {
                $successCount = 0;
                foreach ($adminPhones as $phone) {
                    $this->line("Sending to {$phone}...");
                    if ($smsService->sendCustomSms($phone, $message)) {
                        $this->info("✅ Sent to {$phone}");
                        $successCount++;
                    } else {
                        $this->error("❌ Failed to send to {$phone}");
                    }
                }

                $this->newLine();
                $this->info("Test completed: {$successCount}/" . count($adminPhones) . " SMS sent successfully");
            }
        }

        return 0;
    }

    /**
     * Get admin phone numbers from settings
     */
    private function getAdminPhones(): array
    {
        $phones = [];

        // First priority: Check for dedicated admin notification phones
        $adminNotificationPhones = get_setting('admin_notification_phones');
        if ($adminNotificationPhones) {
            $phoneList = array_map('trim', explode(',', $adminNotificationPhones));
            foreach ($phoneList as $phone) {
                if (!empty($phone) && $this->isValidPhone($phone)) {
                    $phones[] = $phone;
                }
            }
        }

        // If no dedicated admin phones, fall back to general contact phones
        if (empty($phones)) {
            $primaryPhone = get_setting('phone');
            if ($primaryPhone && $this->isValidPhone($primaryPhone)) {
                $phones[] = $primaryPhone;
            }

            $secondaryPhone = get_setting('secondary_phone');
            if ($secondaryPhone && $this->isValidPhone($secondaryPhone)) {
                $phones[] = $secondaryPhone;
            }

            $whatsappPhone = get_setting('whatsapp');
            if ($whatsappPhone && $this->isValidPhone($whatsappPhone) && !in_array($whatsappPhone, $phones)) {
                $phones[] = $whatsappPhone;
            }
        }

        return array_unique($phones);
    }

    /**
     * Validate phone number
     */
    private function isValidPhone(string $phone): bool
    {
        return phoneNumberValidation($phone) !== false;
    }

    /**
     * Build test message
     */
    private function buildTestMessage(int $count): string
    {
        $date = now()->format('Y-m-d');
        $appName = config('app.name', 'Nature\'s Virtue');

        if ($count === 0) {
            return "TEST - Daily Report ({$date}): No pending orders in the last 7 days. Great job! - {$appName}";
        } elseif ($count === 1) {
            return "TEST - Daily Report ({$date}): 1 pending order requires attention. Please check admin panel. - {$appName}";
        } else {
            return "TEST - Daily Report ({$date}): {$count} pending orders require attention. Please check admin panel. - {$appName}";
        }
    }
}
