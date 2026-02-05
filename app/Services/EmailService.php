<?php

namespace App\Services;

use Resend\Laravel\Facades\Resend;

class EmailService
{
    public function sendOtp(string $email, string $otp, string $type): void
    {
        $subject = match($type) {
            'registration' => 'Verify Your Email - Registration OTP',
            'password_reset' => 'Password Reset OTP',
            'email_verification' => 'Email Verification OTP',
            default => 'Your OTP Code',
        };

        $message = match($type) {
            'registration' => 'Welcome! Use this OTP to complete your registration:',
            'password_reset' => 'Use this OTP to reset your password:',
            'email_verification' => 'Use this OTP to verify your email:',
            default => 'Your OTP code is:',
        };

        $html = $this->buildHtmlContent($message, $otp);

        Resend::emails()->send([
            'from' => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to' => [$email],
            'subject' => $subject,
            'html' => $html,
        ]);
    }

    private function buildHtmlContent(string $message, string $otp): string
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                    .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
                    .otp-box { background: white; border: 2px solid #4F46E5; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
                    .otp-code { font-size: 32px; font-weight: bold; color: #4F46E5; letter-spacing: 8px; }
                    .footer { text-align: center; margin-top: 20px; color: #6b7280; font-size: 14px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Natakahii</h1>
                    </div>
                    <div class='content'>
                        <p>{$message}</p>
                        <div class='otp-box'>
                            <div class='otp-code'>{$otp}</div>
                        </div>
                        <p><strong>This OTP will expire in 10 minutes.</strong></p>
                        <p>If you didn't request this code, please ignore this email.</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " Natakahii. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
}
