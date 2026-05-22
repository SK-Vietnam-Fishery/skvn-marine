<?php
/**
 * Media helpers for SKVN Marine.
 *
 * Auto-generate image ALT text from attachment title on upload.
 *
 * Rules:
 * - Only applies to image attachments.
 * - Only fills ALT if ALT is empty.
 * - Does not overwrite editor-provided ALT text.
 * - Does not auto-generate captions by default.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('add_attachment', 'skvn_marine_auto_set_image_alt_from_title');

/**
 * Auto-fill image ALT from attachment title when ALT is empty.
 *
 * @param int $attachment_id Attachment ID.
 * @return void
 */
function skvn_marine_auto_set_image_alt_from_title($attachment_id) {
    if (!wp_attachment_is_image($attachment_id)) {
        return;
    }

    $title = get_the_title($attachment_id);

    if (empty($title)) {
        return;
    }

    // Normalize dash variants and common HTML entities.
    $title = str_replace(
        ['–', '—', '&#8211;', '&#8212;'],
        '-',
        $title
    );

    $title = sanitize_text_field($title);

    if (empty($title)) {
        return;
    }

    $existing_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

    if (empty($existing_alt)) {
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $title);
    }
}
