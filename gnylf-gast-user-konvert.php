<?php
/*
Plugin Name: Siamsnus :: Guest Conversion
Description: Ett plugin för att konvertera gästkonton till riktiga konton vid inloggning
Version: 0.01 Alpha
Author: Gnylf Gnuder
Author URI: https://www.siamsnus.com
License: Are u effing kidding me?
*/

/**
 * Funktion för att autentisera användare vid inloggning.
 *
 * @param integer $user Användar-ID.
 * @param string  $username Användarnamn.
 * @param string  $password Lösenord.
 */
function gnylf_login_authenticate($user, $username, $password)
{
    // Om det är en administratör, avbryt.
    if (is_admin()) {
        return;
    }

    // Om användaren redan existerar, returnera den.
    if ($user instanceof WP_User) {
        return $user;
    }

    // Om användaren lämnat användarnamn eller lösenord tomt, visa felmeddelande.
    if (empty($username) || empty($password)) {
        if (is_wp_error($user)) {
            return $user;
        }

        $error = new WP_Error();

        if (empty($username)) {
            $error->add('empty_username', __('<strong>FEL</strong>: Användarnamnsfältet är tomt.'));
        }

        if (empty($password)) {
            $error->add('empty_password', __('<strong>FEL</strong>: Lösenordsfältet är tomt.'));
        }

        return $error;
    }
    // Kontrollera om det är en e-postadress eller användarnamn
    $check_type = strpos($username, '@') !== false;

    // Om det är en e-postadress, kör validering och returnera användarnamn.
    if (!$check_type) {

        // Kontrollera lösenordet
        if (wp_check_password($password, $user->user_pass)) {

            // Om lösenordet stämmer, returnera en giltig inloggning.
            return $user;

        } else {

            // Om lösenordet är fel, returnera ett felmeddelande.
            return new WP_Error('login', 'Inloggningen misslyckades. Kontrollera igen eller försök med din e-postadress.');

        }
    } else {
        // Hämta alla ordrar för den angivna e-postadressen
        $args = array(
            'customer' => $username,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $orders = wc_get_orders($args);
        $count = count($orders);

        // Om det finns ordrar, skapa ett nytt konto för användaren
        if ($count > 0) {

            $random_password = wp_generate_password(16);
            $email = $orders[0]->get_billing_email();
            $create_user = wp_create_user($username, $random_password, $email);
            $new_user = get_user_by('login', $username);
            $user_id = $new_user->ID;

            // Uppdatera användaren med detaljer, roll, förnamn och efternamn.
            $userdata = array(
                'ID' => $user_id,
                'first_name' => $orders[0]->get_billing_first_name(),
                'last_name' => $orders[0]->get_billing_last_name(),
                'role' => 'customer',
            );

            $user_id = wp_update_user($userdata);

            // Uppdatera användarbehörigheter för "kund" användarroll.
            update_user_meta($user_id, 'wp_capabilities', array('customer' => true));

            // Spara all WooCommerce-faktureringsdata till användaren.
            update_user_meta($user_id, 'billing_first_name', $orders[0]->get_billing_first_name());
            update_user_meta($user_id, 'billing_last_name', $orders[0]->get_billing_last_name());
            update_user_meta($user_id, 'billing_address_1', $orders[0]->get_billing_address_1());
            update_user_meta($user_id, 'billing_address_2', $orders[0]->get_billing_address_2());
            update_user_meta($user_id, 'billing_city', $orders[0]->get_billing_city());
            update_user_meta($user_id, 'billing_state', $orders[0]->get_billing_state());
            update_user_meta($user_id, 'billing_postcode', $orders[0]->get_billing_postcode());
            update_user_meta($user_id, 'billing_country', $orders[0]->get_billing_country());
            update_user_meta($user_id, 'billing_email', $orders[0]->get_billing_email());
            update_user_meta($user_id, 'billing_phone', $orders[0]->get_billing_phone());

                        // Spara all WooCommerce-leveransdata till användaren.
            update_user_meta($user_id, 'shipping_first_name', $orders[0]->get_shipping_first_name());
            update_user_meta($user_id, 'shipping_last_name', $orders[0]->get_shipping_last_name());
            update_user_meta($user_id, 'shipping_address_1', $orders[0]->get_shipping_address_1());
            update_user_meta($user_id, 'shipping_address_2', $orders[0]->get_shipping_address_2());
            update_user_meta($user_id, 'shipping_city', $orders[0]->get_shipping_city());
            update_user_meta($user_id, 'shipping_state', $orders[0]->get_shipping_state());
            update_user_meta($user_id, 'shipping_postcode', $orders[0]->get_shipping_postcode());
            update_user_meta($user_id, 'shipping_country', $orders[0]->get_shipping_country());

            // Spara tidigare ordrar till den nyligen genererade användaren.
            wc_update_new_customer_past_orders($user_id);

            // Visa felmeddelande och länk för att återställa lösenordet.
            return new WP_Error('login', sprintf(__('<strong>Login Failed. For security reasons we require you to update your password</strong><br/>'), '') . ' <a class="woocommerce-Button button " href="' . wp_lostpassword_url() . '">' . __('Reset your password here') . '</a>');

            return $user;
        }
    };
}
add_filter('authenticate', 'gnylf_login_authenticate', 30, 3);

/**
 * Länka tidigare ordrar vid registrering.
 *
 * @param integer $customer_id Användar-ID.
 * @param array $new_customer_data Kunddata som array.
 * @param string $password_generated Det genererade lösenordet.
 */
function gnylf_link_past_orders_on_registration($customer_id, $new_customer_data, $password_generated) {

    $username = $new_customer_data['user_email'];
    $args = array(
        'customer' => $username,
        'orderby' => 'date',
        'order' => 'DESC',
    );

    $orders = wc_get_orders($args);

    $count = count($orders);

    // Om det finns ordrar, länka dem till det nya kundkontot.
    if ($count > 0) {

        wc_update_new_customer_past_orders($customer_id);

    }
}
add_action('woocommerce_created_customer', 'gnylf_link_past_orders_on_registration', 10, 3);

