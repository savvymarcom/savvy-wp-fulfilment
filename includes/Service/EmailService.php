<?php

namespace SavvyWebFulfilment\Service;

use SavvyWebFulfilment\Admin\SavvyPluginConfig;
use WC_Order;

class EmailService
{

    private SavvyPluginConfig $savvyPluginConfig;
    private array $headers;
    private string $adminEmail;
    private string $logoUrl;
    private string $pluginName;
    private string $brandName;
    private string $logo;

    public function __construct()
    {
        $this->savvyPluginConfig = new SavvyPluginConfig();
        $this->init();
    }

    private function init()
    {
        $this->adminEmail = get_option('savvy_web_notification_email') ?: get_option('admin_email');

        $this->logoUrl = $this->savvyPluginConfig->getSavvyBrandLogo();
        $this->pluginName = $this->savvyPluginConfig->getSavvyPluginName();
        $this->brandName = $this->savvyPluginConfig->getSavvyBrandName();

        if(!empty($this->logoUrl)) {
            $this->logo = '<img src="' . $this->logoUrl . '" alt="' . $this->pluginName . ' Logo" width="150" style="display: block;" />'; 
        }else{
            $this->logo = "<h2>{$this->pluginName}</h2>"; 
        }

        $this->headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: Savvy Web Plugin <' . $this->adminEmail . '>',
            'Reply-To: ' . $this->adminEmail,
        ];
    }

    public function sendFulfilmentErrorEmail(WC_Order $order, string $error): void
    {
        
        $subject = 'WooCommerce Order #' . $order->get_id() . ' - Fulfilment Error';
        $link = admin_url("post.php?post={$order->get_id()}&action=edit");

        $body = <<<EOD
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>WooCommerce Order #{$order->get_id()} - Fulfilment Error</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            </head>
            <body style="margin: 0; padding: 0; background-color: #f4f4f4;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: #ffffff;">
                    <tr>
                        <td align="center" style="padding: 40px 0 30px 0;">
                            {$this->logo}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
                                        <b>Fulfilment Error for Order #{$order->get_id()}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                        <p>An error occurred while trying to send WooCommerce Order #{$order->get_id()} to {$this->brandName} for fulfilment.</p>
                                        <p><strong>Error Message:</strong><br />{$error}</p>
                                        <p><strong>What to do next:</strong></p>
                                        <ol>
                                            <li>
                                                <strong>Check the order and its items</strong>
                                                <ul>
                                                    <li>Ensure all required fields are completed (e.g. shipping address, customer name, etc.)</li>
                                                    <li>Make sure all products being fulfilled by {$this->brandName} have valid SKUs and configuration</li>
                                                    <li>If you identify and fix the issue, return to the order screen and click the <strong>"Resend to Fulfilment"</strong> button.</li>
                                                </ul>
                                            </li>
                                            <li>
                                                <strong>If the error is not something you can fix</strong>
                                                <ul>
                                                    <li>The issue may be with the {$this->brandName} fulfilment system or your account setup.</li>
                                                    <li>In this case, please contact your {$this->brandName} account manager for assistance.</li>
                                                </ul>
                                            </li>
                                        </ol>
                                        <p><a href="{$link}" style="color: #ffffff; background-color: #007bff; text-decoration: none; padding: 10px 20px; border-radius: 5px; display: inline-block;">View Order in Admin</a></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #eeeeee; padding: 30px 30px 30px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;">
                                        <p style="margin: 0;">&copy; {$this->pluginName}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        EOD;

 
        $success = wp_mail($this->adminEmail, $subject, $body, $this->headers);

        if (!$success) {
            error_log('[SavvyWebPlugin] ❌ wp_mail failed to send.');
        } else {
            error_log('[SavvyWebPlugin] ✅ Test email sent.');
        }
    }


    public function sendFulfilmentStatusUpdateEmail($orderId, $status, $tracking, $carrier): void
    {

        $subject = "WooCommerce #{$orderId} marked as Fulfilled";
        $link = admin_url("post.php?post={$orderId}&action=edit");

        $emailTitle = "WooCommerce Order #{$orderId} marked as Fulfilled";
        $emailHeading = "Order #{$orderId} Fulfilled by {$this->brandName}";

        $emailBody = "<p>The following order has been marked as fulfilled by {$this->brandName}:</p>
                        <p><strong>Order ID:</strong> #{$orderId}<br />
                        <strong>Status:</strong> {$status}<br />
                        <strong>Tracking Number:</strong> {$tracking}<br />
                        <strong>Carrier:</strong> {$carrier}</p>
                        <a href='{$link}' style='color: #ffffff; background-color: #462a7b; text-decoration: none; padding: 10px 20px; border-radius: 5px; display: inline-block;'>View Order in Admin</a></p>";

        $emailContent = $this->emailTemplate($emailTitle, $emailHeading, $emailBody);
        
        $success = wp_mail($this->adminEmail, $subject, $emailContent, $this->headers);

        if (!$success) {
            error_log('[SavvyWebPlugin] ❌ wp_mail failed to send.');
        } else {
            error_log('[SavvyWebPlugin] ✅ Test email sent.');
        }
    }

    private function emailTemplate($title, $heading, $body)
    {
        return <<<EOD
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>{$title}</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            </head>
            <body style="margin: 0; padding: 0; background-color: #f6f6f6;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: #ffffff;">
                    <tr>
                        <td align="center" style="padding: 40px 0 30px 0;">
                            Logo should go here...
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #462a7b; font-family: Arial, sans-serif; font-size: 24px;">
                                        <b>{$heading}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                        {$body}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #462a7b; padding: 30px 30px 30px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;">
                                        <p style="margin: 0;">&copy; {$this->pluginName}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        EOD;
    }

}