<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

function send_mail($customer_email, $subject_text, $title, $intro, $username, $apiKey, $outro){
    $logo_url = yai_url . 'assets/images/yooker_icon.png'; // Logo URL
    $message = '<!DOCTYPE html>';
    $message .= '<html lang="nl">';
    $message .= '<head>';
    $message .= '<meta charset="UTF-8">';
    $message .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $message .= '<title>' . $subject_text . '</title>';
    $message .= '<style>';
    $message .= 'body { font-family: "Lato", sans-serif; color: #333; margin: 0; padding: 0; }';
    $message .= '.email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 20px; border-radius: 8px; }';
    $message .= '.email-header { text-align: center;}';
    $message .= '.email-header img { max-width: 80px !important;}';
    $message .= '.email-body { padding: 20px; }';
    $message .= '.email-body p { margin: 10px 0; line-height: 1.6; }';
    $message .= '.credentials { background-color: #f0f4ff; border: 1px solid #d0d9f5; padding: 10px; margin: 10px 0; border-radius: 5px; }';
    $message .= '.email-footer { text-align: center; padding: 10px 0; font-size: 12px; color: #777; }';
    $message .= '</style>';
    $message .= '</head>';
    $message .= '<body style="background-color: #E2E2E2; padding: 20px;">'; 
    
    $message .= '<div class="email-container">';
    
    // Header with Logo
    $message .= '<div class="email-header">';
    $message .= '<img src="' . $logo_url . '" alt="Yooker Logo">';
    $message .= '</div>';
    
    // Body
    $message .= '<div class="email-body">';
    $message .= '<h3>' . $title . '</h3>'; // Title moved here
    $message .= '<p>' . $intro . '</p>';
    $message .= '<div class="credentials">';
    $message .= '<p><strong>' . $username . '</strong></p>';
    $message .= '<p><strong>' . $apiKey . '</strong></p>';
    $message .= '</div>';
    $message .= '<p>' . $outro . '</p>';
    $message .= '</div>';

    // Footer
    $message .= '<div class="email-footer">Yooker | <a href="https://yooker.nl">yooker.nl</a></div>';
    $message .= '</div>';
    $message .= '</body>';
    $message .= '</html>';
    
    // Set email headers
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    // Send the email
    $is_sent = wp_mail($customer_email, $subject_text, $message, $headers);
    
    return $is_sent ? true : false;
}
?>