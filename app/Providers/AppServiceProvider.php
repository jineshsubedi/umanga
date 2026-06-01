<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

                if (!empty($settings['mail_host'])) {
                    config([
                        'mail.mailers.smtp.host' => $settings['mail_host'],
                        'mail.mailers.smtp.port' => $settings['mail_port'] ?? env('MAIL_PORT', 587),
                        'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? env('MAIL_ENCRYPTION', 'tls'),
                        'mail.mailers.smtp.username' => $settings['mail_username'] ?? env('MAIL_USERNAME'),
                        'mail.mailers.smtp.password' => $settings['mail_password'] ?? env('MAIL_PASSWORD'),
                        'mail.from.address' => $settings['mail_from_address'] ?? env('MAIL_FROM_ADDRESS'),
                        'mail.from.name' => $settings['mail_from_name'] ?? env('MAIL_FROM_NAME'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Ignore during migrations or console commands where table might not exist
        }
    }
}
