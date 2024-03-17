<?php
/**
 * Plugin Name: Kin Code Wallet Donation
 * Plugin URI: https://github.com/mega7mega/code-wallet-kin-wordpress-donation/
 * Description: Adds a Code.com donation button to the end of each post, and makes it slide in when scrolling.
 * Version: 1.1
 * Author: James Steele
 * Author URI: https://www.x.com/ceoduno
 * License: The Unlicense
 * License URI: https://unlicense.org/
**/

function kin_code_donation_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_style('kin-code-donation-style', plugin_dir_url(__FILE__) . 'assets/kin-code-donation.css');
}
add_action('wp_enqueue_scripts', 'kin_code_donation_enqueue_scripts');

function kin_code_donation_add_button($content) {
    if (is_single() && is_main_query()) {
        $amount = get_option('kin_code_donation_amount', '0.05');
        $destination = get_option('kin_code_donation_destination', 'E8otxw1CVX9bfyddKu3ZB3BVLa4VVF9J7CTPdnUwT9jR');
        $customText = esc_js(get_option('kin_code_donation_custom_text', 'Do you like my writing? Donate to my blog with Code Wallet'));
        $customCss = get_option('kin_code_donation_custom_css', '');

        $button_code = <<<EOT
<div id="kin-code-donation-container">
    <p>$customText</p>
	<div id="button-container2"></div>
</div>
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
        $customCssOutput = $customCss ? "<style>{$customCss}</style>" : '';
        $content .= $customCssOutput . $button_code;
    }
    return $content;
}
add_filter('the_content', 'kin_code_donation_add_button');

function kin_code_donation_settings_init() {
    // Check if the custom CSS option already exists, if not, add a default style
    if (false === get_option('kin_code_donation_custom_css')) {
        $default_css = "
#kin-code-donation-container {
    padding: 15px;
    border: 1px solid #0073aa; /* Border color */
    border-radius: 10px; /* Rounded corners */
    background: linear-gradient(145deg, #6db9ef, #7ce08a); /* Gradient background */
    color: #ffffff; /* Text color */
    text-align: center;
    margin-top: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional: Adds a shadow for depth */
}
#kin-code-donation-container p {
    margin: 0px 0px 12px 0px !important; /* Adjusts the margin for the paragraph */
}
";
        add_option('kin_code_donation_custom_css', $default_css);
    }

    register_setting('kin_code_donation_settings', 'kin_code_donation_amount');
    register_setting('kin_code_donation_settings', 'kin_code_donation_destination');
    register_setting('kin_code_donation_settings', 'kin_code_donation_custom_text');
    register_setting('kin_code_donation_settings', 'kin_code_donation_custom_css');
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
                    <th scope="row">Amount (Maximum 1.00)</th>
                    <td>$<input type="text" name="kin_code_donation_amount" value="<?php echo esc_attr(get_option('kin_code_donation_amount', '0.05')); ?>" size=5/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Destination (Code Wallet Deposit Address) <a href="https://www.getcode.com/" target=getcode>getcode.com</a></th>
                    <td><input type="text" name="kin_code_donation_destination" value="<?php echo esc_attr(get_option('kin_code_donation_destination')); ?>" size=100/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Custom Text for support</th>
                    <td><input type="text" name="kin_code_donation_custom_text" value="<?php echo esc_attr(get_option('kin_code_donation_custom_text', 'Enjoyed my blog? Show your support by donating via Code Wallet. Every bit helps!')); ?>"  size=100/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Custom CSS</th>
                    <td>
                        <textarea name="kin_code_donation_custom_css" rows="10" cols="50" class="large-text code"><?php echo esc_textarea(get_option('kin_code_donation_custom_css')); ?></textarea>
                        <p class="description">Add custom CSS here to style the donation button container, e.g., background, border, etc.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
