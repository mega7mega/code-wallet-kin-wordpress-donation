<?php
/*
Plugin Name: Kin Code Donation
Plugin URI: https://example.com/kin-code-donation
Description: Adds a Code.com donation button to the end of each post, and makes it slide in when scrolling.
Version: 1.0
Author: Your Name
Author URI: https://example.com
*/

function kin_code_donation_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_style('kin-code-donation-style', plugin_dir_url(__FILE__) . 'assets/kin-code-donation.css');
}
add_action('wp_enqueue_scripts', 'kin_code_donation_enqueue_scripts');

function kin_code_donation_add_button($content) {
    if (is_single() && is_main_query()) {
        $amount = get_option('kin_code_donation_amount', '0.05');
        $destination = get_option('kin_code_donation_destination', 'E8otxw1CVX9bfyddKu3ZB3BVLa4VVF9J7CTPdnUwT9jR');
        // Fetch the custom text option
        $customText = esc_js(get_option('kin_code_donation_custom_text', 'Do you like my writing? Donate to my blog with Code Wallet'));

        $button_code = <<<EOT
<div id="kin-code-donation-container" style="background-color:white; display: block; z-index: 1000; margin-top: 20px;">
    <p style="margin-bottom: 5px; text-align: center;">$customText</p>
   
</div>
 <div id="button-container2"></div>
<script type="module">
    import code from 'https://js.getcode.com/v1';

    const { button } = code.elements.create('button', {
        currency: 'usd',
        amount: {$amount},
        destination: '{$destination}',
    });

    button.on('success', () => {
        alert('Thank you!');
    });

    button.mount('#button-container2');
</script>
EOT;

        $content .= $button_code;
    }
    return $content;
}

add_filter('the_content', 'kin_code_donation_add_button');


function kin_code_donation_settings_init() {
    register_setting('kin_code_donation_settings', 'kin_code_donation_amount');
    register_setting('kin_code_donation_settings', 'kin_code_donation_destination');
    // Register a new setting for the custom text
    register_setting('kin_code_donation_settings', 'kin_code_donation_custom_text');
}

add_action('admin_init', 'kin_code_donation_settings_init');

function kin_code_donation_settings_page() {
    add_options_page(
        'Kin Code Donation Settings',
        'Kin Code Donation',
        'manage_options',
        'kin-code-donation',
        'kin_code_donation_settings_page_content'
    );
}
add_action('admin_menu', 'kin_code_donation_settings_page');

function kin_code_donation_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Kin Code Donation Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('kin_code_donation_settings');
            do_settings_sections('kin_code_donation_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Amount</th>
                    <td><input type="text" name="kin_code_donation_amount" value="<?php echo esc_attr(get_option('kin_code_donation_amount', '0.05')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Destination</th>
                    <td><input type="text" name="kin_code_donation_destination" value="<?php echo esc_attr(get_option('kin_code_donation_destination')); ?>" /></td>
                </tr>
                <!-- Add input for custom text -->
                <tr valign="top">
                    <th scope="row">Custom Text</th>
                    <td><input type="text" name="kin_code_donation_custom_text" value="<?php echo esc_attr(get_option('kin_code_donation_custom_text', 'Do you like my writing? Donate to my blog with Code Wallet')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
