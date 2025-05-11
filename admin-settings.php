<?php
if (!defined('ABSPATH')) exit;

function cpf_settings_menu() {
    add_options_page('Contact Pay Settings', 'Contact Pay Settings', 'manage_options', 'cpf-settings', 'cpf_settings_page');
}
add_action('admin_menu', 'cpf_settings_menu');

function cpf_settings_page() {
    ?>
    <div class="wrap">
        <h2>Contact Form with Razorpay - Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('cpf_settings_group');
            do_settings_sections('cpf-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function cpf_register_settings() {
    register_setting('cpf_settings_group', 'cpf_settings');

    add_settings_section('cpf_main_section', 'Main Settings', null, 'cpf-settings');

    add_settings_field('razorpay_key', 'Razorpay Key', 'cpf_key_callback', 'cpf-settings', 'cpf_main_section');
    add_settings_field('payment_amount', 'Payment Amount (INR)', 'cpf_amount_callback', 'cpf-settings', 'cpf_main_section');
}
add_action('admin_init', 'cpf_register_settings');

function cpf_key_callback() {
    $options = get_option('cpf_settings');
    echo '<input type="text" name="cpf_settings[razorpay_key]" value="' . esc_attr($options['razorpay_key'] ?? '') . '" />';
}

function cpf_amount_callback() {
    $options = get_option('cpf_settings');
    echo '<input type="number" name="cpf_settings[payment_amount]" value="' . esc_attr($options['payment_amount'] ?? '100') . '" />';
}
