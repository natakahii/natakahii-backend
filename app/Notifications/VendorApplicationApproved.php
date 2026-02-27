<?php

namespace App\Notifications;

use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Resend\Laravel\Notifications\Messages\ResendMessage;

class VendorApplicationApproved extends Notification
{
    use Queueable;

    public function __construct(
        private VendorApplication $application
    ) {}

    public function via($notifiable): array
    {
        return ['resend'];
    }

    public function toResend($notifiable): ResendMessage
    {
        $shopName = $this->application->business_name;
        $year = date('Y');
        $fromEmail = config('mail.from.address') ?? 'noreply@natakahii.com';
        $fromName = config('mail.from.name') ?? 'Natakahii';

        $htmlContent = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Vendor Application Approved</title>
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
                                                <span style="display:inline-block;width:40px;height:40px;background:#22c55e;border-radius:10px;line-height:40px;text-align:center;">
                                                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white' width='22' height='22'><path d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'/></svg>
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
                                    <h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#0f172a;text-align:center;">🎉 Congratulations!</h1>
                                    <p style="margin:0 0 28px;font-size:15px;color:#475569;text-align:center;line-height:1.6;">Your vendor application has been approved</p>

                                    <!-- Success Box -->
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="padding:24px 0;">
                                                <div style="display:inline-block;background:#f0fdf4;border:2px solid #22c55e;border-radius:12px;padding:20px 24px;width:100%;box-sizing:border-box;">
                                                    <p style="margin:0 0 12px;font-size:14px;color:#475569;"><strong>Business Name:</strong> {$shopName}</p>
                                                    <p style="margin:0;font-size:14px;color:#475569;"><strong>Status:</strong> <span style="color:#22c55e;font-weight:700;">✓ Approved</span></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Details -->
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;">
                                        <tr>
                                            <td style="padding:16px;background:#f8fafc;border-radius:8px;">
                                                <p style="margin:0 0 12px;font-size:14px;color:#0f172a;font-weight:600;">What's next?</p>
                                                <ul style="margin:0;padding-left:20px;font-size:14px;color:#475569;line-height:1.8;">
                                                    <li>Your shop is now live on Natakahii</li>
                                                    <li>Start adding your products to attract customers</li>
                                                    <li>Complete your shop profile for better visibility</li>
                                                    <li>Monitor orders and customer feedback</li>
                                                </ul>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- CTA Button -->
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;">
                                        <tr>
                                            <td align="center" style="padding:20px 0;">
                                                <a href="{$this->getShopDashboardLink()}" style="display:inline-block;background:#22c55e;color:#ffffff;text-decoration:none;font-weight:600;font-size:15px;padding:12px 32px;border-radius:8px;transition:background 0.2s ease;">Go to Your Shop</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Support Note -->
                            <tr>
                                <td style="background:#ffffff;padding:0 32px 32px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e2e8f0;padding-top:20px;">
                                        <tr>
                                            <td>
                                                <p style="margin:0;font-size:13px;color:#94a3b8;line-height:1.6;">If you have any questions, feel free to contact our support team. We're here to help you succeed!</p>
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

        return ResendMessage::create()
            ->from($fromEmail, $fromName)
            ->subject('🎉 Your Vendor Application Has Been Approved!')
            ->html($htmlContent);
    }

    private function getShopDashboardLink(): string
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        return "{$frontendUrl}/vendor/dashboard";
    }
}
