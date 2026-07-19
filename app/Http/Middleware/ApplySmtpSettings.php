<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ApplySmtpSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        if (tenancy()->initialized) {
            $smtp = \App\Models\Tenant\SmtpSetting::where('is_active', true)->first();

            if ($smtp) {
                Config::set('mail.mailer', $smtp->mail_mailer);
                Config::set('mail.mailers.smtp.host', $smtp->mail_host);
                Config::set('mail.mailers.smtp.port', $smtp->mail_port);
                Config::set('mail.mailers.smtp.username', $smtp->mail_username);
                Config::set('mail.mailers.smtp.password', $smtp->mail_password);
                Config::set('mail.mailers.smtp.encryption', $smtp->mail_encryption);
                Config::set('mail.from.address', $smtp->mail_from_address);
                Config::set('mail.from.name', $smtp->mail_from_name);
            }
        }

        return $next($request);
    }
}
