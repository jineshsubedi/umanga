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

                        'mail.mailers.gmail.host' => $settings['gmail_host'] ?? env('GMAIL_MAILER_HOST', 'smtp.gmail.com'),
                        'mail.mailers.gmail.port' => $settings['gmail_port'] ?? env('GMAIL_MAILER_PORT', 465),
                        'mail.mailers.gmail.encryption' => $settings['gmail_encryption'] ?? env('GMAIL_MAILER_ENCRYPTION', 'ssl'),
                        'mail.mailers.gmail.username' => $settings['gmail_username'] ?? env('GMAIL_MAILER_USERNAME'),
                        'mail.mailers.gmail.password' => $settings['gmail_password'] ?? env('GMAIL_MAILER_PASSWORD'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Ignore during migrations or console commands where table might not exist
        }

        \Illuminate\Support\Facades\Mail::extend('smart', function (array $config = []) {
            return new \App\Mail\Transports\SmartTransport(
                \Illuminate\Support\Facades\Mail::mailer('smtp')->getSymfonyTransport(),
                \Illuminate\Support\Facades\Mail::mailer('gmail')->getSymfonyTransport()
            );
        });
    }
}
