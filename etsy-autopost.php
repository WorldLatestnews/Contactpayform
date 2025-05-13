<?php
/*
Plugin Name: Etsy AutoPost
Description: Automatically pulls and posts products from Etsy.
Version: 1.0
Author: Kamal Jeet Singh
*/

// Schedule event
register_activation_hook(__FILE__, 'etsy_autopost_schedule');
function etsy_autopost_schedule() {
    if (!wp_next_scheduled('etsy_daily_post_event')) {
        wp_schedule_event(time(), 'hourly', 'etsy_daily_post_event'); // Change to 'daily' if preferred
    }
}

// Unschedule event on deactivation
register_deactivation_hook(__FILE__, 'etsy_autopost_unschedule');
function etsy_autopost_unschedule() {
    wp_clear_scheduled_hook('etsy_daily_post_event');
}

// Hook function to scheduled event
add_action('etsy_daily_post_event', 'etsy_fetch_and_post');

function etsy_fetch_and_post() {
    $api_key = 'YOUR_ETSY_API_KEY';
    $shop_name = 'YOUR_SHOP_NAME';

    $response = wp_remote_get("https://openapi.etsy.com/v2/shops/{$shop_name}/listings/active?api_key={$api_key}&limit=5");

    if (is_wp_error($response)) return;

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (!empty($data['results'])) {
        foreach ($data['results'] as $item) {
            $title = sanitize_text_field($item['title']);
            $description = sanitize_textarea_field($item['description']);
            $url = esc_url($item['url']);
            $image = esc_url($item['Images'][0]['url_fullxfull'] ?? '');

            $existing = get_posts([
                'post_type' => 'post',
                'meta_key' => 'etsy_listing_id',
                'meta_value' => $item['listing_id']
            ]);
            if ($existing) continue;

            $post_id = wp_insert_post([
                'post_title'   => $title,
                'post_content' => "<p>{$description}</p><p><a href='{$url}' target='_blank'>View on Etsy</a></p><img src='{$image}' alt=''>",
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_category'=> [1]
            ]);

            if ($post_id) {
                add_post_meta($post_id, 'etsy_listing_id', $item['listing_id']);
            }
        }
    }
}
