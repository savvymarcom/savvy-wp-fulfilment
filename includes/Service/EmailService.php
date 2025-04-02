<?php

namespace SavvyWebFulfilment\Service;

use SavvyWebFulfilment\Admin\SavvyPluginConfig;
use WC_Order;

class EmailService
{

    private SavvyPluginConfig $savvyPluginConfig;

    public function __construct()
    {
        $this->savvyPluginConfig = new SavvyPluginConfig();
    }


    public function sendFulfilmentErrorEmail(WC_Order $order, string $error): void
    {

        $adminEmail = get_option('savvy_web_notification_email') ?: get_option('admin_email');
        
        $subject = 'WooCommerce Order #' . $order->get_id() . ' - Fulfilment Error';
        $link = admin_url("post.php?post={$order->get_id()}&action=edit");

        $logoUrl = $this->savvyPluginConfig->getSavvyBrandLogo();
        $pluginName = $this->savvyPluginConfig->getSavvyPluginName();
        $brandName = $this->savvyPluginConfig->getSavvyBrandName();

        if(!empty($logoUrl)) {
            $logo = '<img src="' . $logoUrl . '" alt="' . $pluginName . ' Logo" width="150" style="display: block;" />'; 
        }else{
            $logo = "<h2>{$pluginName}</h2>"; 
        }

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
                            {$logo}
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
                                        <p>An error occurred while trying to send WooCommerce Order #{$order->get_id()} to {$brandName} for fulfilment.</p>
                                        <p><strong>Error Message:</strong><br />{$error}</p>
                                        <p><strong>What to do next:</strong></p>
                                        <ol>
                                            <li>
                                                <strong>Check the order and its items</strong>
                                                <ul>
                                                    <li>Ensure all required fields are completed (e.g. shipping address, customer name, etc.)</li>
                                                    <li>Make sure all products being fulfilled by {$brandName} have valid SKUs and configuration</li>
                                                    <li>If you identify and fix the issue, return to the order screen and click the <strong>"Resend to Fulfilment"</strong> button.</li>
                                                </ul>
                                            </li>
                                            <li>
                                                <strong>If the error is not something you can fix</strong>
                                                <ul>
                                                    <li>The issue may be with the {$brandName} fulfilment system or your account setup.</li>
                                                    <li>In this case, please contact your {$brandName} account manager for assistance.</li>
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
                                        <p style="margin: 0;">&copy; {$pluginName}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        EOD;

        $headers = array('Content-Type: text/html; charset=UTF-8');
        wc_mail($adminEmail, $subject, $body, $headers);
    }


    public function sendFulfilmentStatusUpdateEmail($orderId, $status, $tracking, $carrier): void
    {
        $adminEmail = get_option('savvy_web_notification_email') ?: get_option('admin_email');
        $subject = "WooCommerce #{$orderId} marked as Fulfilled";
        $link = admin_url("post.php?post={$orderId}&action=edit");

        $logoUrl = $this->savvyPluginConfig->getSavvyBrandLogo();
        $pluginName = $this->savvyPluginConfig->getSavvyPluginName();
        $brandName = $this->savvyPluginConfig->getSavvyBrandName();

        if (!empty($logoUrl)) {
            $logo = '<img src="' . $logoUrl . '" alt="' . $pluginName . ' Logo" width="150" style="display: block;" />';
        }else{
            $logo = "<h2>{$pluginName}</h2>"; 
        }

        $body = <<<EOD
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>WooCommerce Order #{$orderId} marked as Fulfilled</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            </head>
            <body style="margin: 0; padding: 0; background-color: #f4f4f4;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: #ffffff;">
                    <tr>
                        <td align="center" style="padding: 40px 0 30px 0;">
                            {$logo}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
                                        <b>Order #{$orderId} Fulfilled by {$brandName}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                        <p>The following order has been marked as fulfilled by {$brandName}:</p>
                                        <p><strong>Order ID:</strong> #{$orderId}<br />
                                        <strong>Status:</strong> {$status}<br />
                                        <strong>Tracking Number:</strong> {$tracking}<br />
                                        <strong>Carrier:</strong> {$carrier}</p>
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
                                        <p style="margin: 0;">&copy; {$pluginName}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        EOD;

        $headers = array('Content-Type: text/html; charset=UTF-8');
        wc_mail($adminEmail, $subject, $body, $headers);
    }

}