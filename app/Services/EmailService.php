<?php

namespace App\Services;

use Resend\Laravel\Facades\Resend;

class EmailService
{
    public function sendOtp(string $email, string $otp, string $type): void
    {
        $subject = match ($type) {
            'registration' => 'Verify Your Email - Registration OTP',
            'password_reset' => 'Password Reset OTP',
            'email_verification' => 'Email Verification OTP',
            default => 'Your OTP Code',
        };

        $message = match ($type) {
            'registration' => 'Welcome to Natakahii! Use the code below to complete your registration.',
            'password_reset' => 'We received a request to reset your password. Use the code below to proceed.',
            'email_verification' => 'Use the code below to verify your email address.',
            default => 'Here is your one-time verification code.',
        };

        $heading = match ($type) {
            'registration' => 'Complete Your Registration',
            'password_reset' => 'Reset Your Password',
            'email_verification' => 'Verify Your Email',
            default => 'Verification Code',
        };

        $html = $this->buildHtmlContent($heading, $message, $otp);

        Resend::emails()->send([
            'from' => config('mail.from.name').' <'.config('mail.from.address').'>',
            'to' => [$email],
            'subject' => $subject,
            'html' => $html,
        ]);
    }

    private function buildHtmlContent(string $heading, string $message, string $otp): string
    {
        $year = date('Y');
        $digits = str_split($otp);
        $otpCells = '';
        foreach ($digits as $digit) {
            $otpCells .= "<td style='width:42px;height:50px;text-align:center;font-size:26px;font-weight:700;color:#0f172a;background:#fff7ed;border:2px solid #f97316;border-radius:8px;font-family:Courier New,monospace;padding:0;'>{$digit}</td><td style='width:6px;padding:0;'></td>";
        }
        $otpHtml = "<table role='presentation' cellpadding='0' cellspacing='0' style='margin:0 auto;'><tr>{$otpCells}</tr></table>";

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$heading}</title>
        </head>
        <body style="margin:0;padding:0;background-color:#f1f5f9;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:40px 20px;">
                <tr>
                    <td align="center">
                        <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">

                            <!-- Header -->
                            <tr>
                                <td style="background:#0f172a;padding:28px 32px;border-radius:12px 12px 0 0;text-align:center;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="padding-bottom:8px;">
                                                <span style="display:inline-block;width:40px;height:40px;background:#f97316;border-radius:10px;line-height:40px;text-align:center;">
                                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white' width='22' height='22'%3E%3Cpath d='M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0020 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z'/%3E%3C/svg%3E" alt="" width="22" height="22" style="vertical-align:middle;" />
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                                <span style="font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">Nata<span style="color:#f97316;">kahii</span></span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Body -->
                            <tr>
                                <td style="background:#ffffff;padding:36px 32px 20px;">
                                    <h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#0f172a;text-align:center;">{$heading}</h1>
                                    <p style="margin:0 0 28px;font-size:15px;color:#475569;text-align:center;line-height:1.6;">{$message}</p>

                                    <!-- OTP Box -->
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="padding:24px 0;">
                                                <div style="display:inline-block;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;">
                                                    {$otpHtml}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Expiry Notice -->
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;">
                                        <tr>
                                            <td align="center" style="padding:12px 20px;background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;">
                                                <p style="margin:0;font-size:13px;color:#9a3412;font-weight:600;">&#9200; This code expires in 10 minutes</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Security Notice -->
                            <tr>
                                <td style="background:#ffffff;padding:0 32px 32px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e2e8f0;padding-top:20px;">
                                        <tr>
                                            <td>
                                                <p style="margin:0;font-size:13px;color:#94a3b8;line-height:1.6;">If you didn't request this code, you can safely ignore this email. Someone may have entered your email address by mistake.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style="background:#0f172a;padding:20px 32px;border-radius:0 0 12px 12px;text-align:center;">
                                    <p style="margin:0;font-size:13px;color:rgba(255,255,255,0.5);">&copy; {$year} Natakahii. All rights reserved.</p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;
    }
}
