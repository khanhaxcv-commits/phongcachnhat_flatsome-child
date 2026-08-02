<?php

/**
 * Contact Form 7 cleanup.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('wpcf7_autop_or_not', '__return_false');

/**
 * Silently skip Contact Form 7 mail for blocked phone numbers.
 *
 * The default field list supports the field name from the old project and
 * several common CF7 phone field names. Both the field names and blocklist
 * can be changed with filters without editing this module.
 */
add_filter('wpcf7_skip_mail', 'cf7_silent_block_spam_phone_numbers', 10, 2);

function cf7_silent_block_spam_phone_numbers($skip_mail, $contact_form)
{
    if (!class_exists('WPCF7_Submission')) {
        return $skip_mail;
    }

    $submission = WPCF7_Submission::get_instance();

    if (!$submission) {
        return $skip_mail;
    }

    $posted_data = $submission->get_posted_data();

    if (!is_array($posted_data)) {
        return $skip_mail;
    }

    $phone_field_names = apply_filters(
        'cf7_spam_phone_fields',
        array('tel-379', 'your-tel', 'phone', 'telephone', 'so-dien-thoai', 'sdt'),
        $contact_form
    );

    $phone = '';

    foreach ((array) $phone_field_names as $field_name) {
        if (
            isset($posted_data[$field_name])
            && is_scalar($posted_data[$field_name])
            && trim((string) $posted_data[$field_name]) !== ''
        ) {
            $phone = sanitize_text_field(wp_unslash((string) $posted_data[$field_name]));
            break;
        }
    }

    if ($phone === '') {
        return $skip_mail;
    }

    $normalize_phone = static function ($value) {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if (strpos($digits, '0084') === 0) {
            return '0' . substr($digits, 4);
        }

        if (strpos($digits, '84') === 0) {
            return '0' . substr($digits, 2);
        }

        return $digits;
    };

    $phone_compare = $normalize_phone($phone);
    $blocked_phones = apply_filters(
        'cf7_blocked_phone_numbers',
        array('0987654321'),
        $contact_form
    );

    $blocked_phones = array_map($normalize_phone, (array) $blocked_phones);

    if ($phone_compare !== '' && in_array($phone_compare, $blocked_phones, true)) {
        return true;
    }

    return $skip_mail;
}
