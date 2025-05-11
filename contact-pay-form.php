<?php
/**
 * Plugin Name: Contact Form with Razorpay
 * Description: Contact form with Razorpay payment integration. Supports shortcode, widget, and Gutenberg.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

define('CPF_PLUGIN_URL', plugin_dir_url(__FILE__));

include_once(plugin_dir_path(__FILE__) . 'admin-settings.php');

function cpf_enqueue_scripts() {
    wp_enqueue_script('razorpay', 'https://checkout.razorpay.com/v1/checkout.js', [], null, true);
    wp_enqueue_script('cpf-script', CPF_PLUGIN_URL . 'form-script.js', ['jquery'], null, true);

    $options = get_option('cpf_settings');
    wp_localize_script('cpf-script', 'cpf_vars', [
        'key' => $options['razorpay_key'] ?? '',
        'amount' => (int)($options['payment_amount'] ?? 100) * 100
    ]);
}
add_action('wp_enqueue_scripts', 'cpf_enqueue_scripts');

function cpf_display_form() {
    ob_start(); ?>
    <form id="cpf-form">
        <input type="text" name="cpf_name" placeholder="Your Name" required><br>
        <input type="email" name="cpf_email" placeholder="Your Email" required><br>
        <textarea name="cpf_message" placeholder="Your Message" required></textarea><br>
        <button type="button" id="cpf-pay-button">Pay & Submit</button>
    </form>
    <div id="cpf-result"></div>
    <?php return ob_get_clean();
}
add_shortcode('contact_pay_form', 'cpf_display_form');

function cpf_form_widget() {
    register_widget('CPF_Form_Widget');
}
add_action('widgets_init', 'cpf_form_widget');

class CPF_Form_Widget extends WP_Widget {
    function __construct() {
        parent::__construct('cpf_form_widget', 'Contact Pay Form');
    }
    public function widget($args, $instance) {
        echo do_shortcode('[contact_pay_form]');
    }
}

function cpf_register_block() {
    wp_register_script('cpf-block', '', [], null, true);
    register_block_type('cpf/contact-pay-form', [
        'render_callback' => 'cpf_display_form'
    ]);
}
add_action('init', 'cpf_register_block');
