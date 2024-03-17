<?php
/*
Plugin Name: Kin Code Donation
Plugin URI: https://example.com/kin-code-donation
Description: Adds a button that slides in from the bottom-right corner when scrolling starts on each post.
Version: 1.0
Author: Your Name
Author URI: https://example.com
*/

function kin_code_donation_enqueue_scripts() {
    wp_enqueue_script( 'kin-code-donation-script', plugin_dir_url( __FILE__ ) . 'assets/kin-code-donation.js', array( 'jquery' ), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'kin_code_donation_enqueue_scripts' );

function kin_code_donation_enqueue_styles() {
    wp_enqueue_style( 'kin-code-donation-style', plugin_dir_url( __FILE__ ) . 'assets/kin-code-donation.css' );
}
add_action( 'wp_enqueue_scripts', 'kin_code_donation_enqueue_styles' );

function kin_code_donation_settings_page() {
    add_options_page(
        'Kin Code Donation Settings',
        'Kin Code Donation',
        'manage_options',
        'kin-code-donation',
        'kin_code_donation_settings_page_content'
    );
}
add_action( 'admin_menu', 'kin_code_donation_settings_page' );

function kin_code_donation_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Kin Code Donation Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'kin_code_donation_settings' );
            do_settings_sections( 'kin_code_donation_settings' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Amount</th>
                    <td><input type="text" name="kin_code_donation_amount" value="<?php echo esc_attr( get_option('kin_code_donation_amount', '0.05') ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Destination</th>
                    <td><input type="text" name="kin_code_donation_destination" value="<?php echo esc_attr( get_option('kin_code_donation_destination', 'E8otxw1CVX9bfyddKu3ZB3BVLa4VVF9J7CTPdnUwT9jR') ); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function kin_code_donation_settings_init() {
    register_setting( 'kin_code_donation_settings', 'kin_code_donation_amount' );
    register_setting( 'kin_code_donation_settings', 'kin_code_donation_destination' );
}
add_action( 'admin_init', 'kin_code_donation_settings_init' );

function kin_code_donation_localize_scripts() {
    $amount = get_option( 'kin_code_donation_amount', '0.05' );
    $destination = get_option( 'kin_code_donation_destination', 'E8otxw1CVX9bfyddKu3ZB3BVLa4VVF9J7CTPdnUwT9jR' );

    wp_localize_script(
        'kin-code-donation-script',
        'kin_code_donation_params',
        array(
            'amount' => $amount,
            'destination' => $destination,
        )
    );
}
add_action( 'wp_enqueue_scripts', 'kin_code_donation_localize_scripts' );