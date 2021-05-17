<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'parent-style' )
	);

	if ( is_rtl() ) {
		wp_enqueue_style( 'parent-rtl', get_template_directory_uri() . '/rtl.css' );
		wp_enqueue_style( 'child-rtl',
			get_stylesheet_directory_uri() . '/rtl.css',
			array( 'parent-rtl' )
		);
	}

	wp_enqueue_script( 'child-rigid-front',
		get_stylesheet_directory_uri() . '/js/rigid-front.js',
		array( 'rigid-front' ),
		false,
		true
	);
}

// CUSTOM CODE - by HENRIT

/** 
 * Translation files for child theme
 */
function child_theme_slug_setup() {
    load_child_theme_textdomain( 'rigid', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'child_theme_slug_setup' );



/** 
 * USER REGISTRATION
 */

// remove some fields
function dokan_custom_seller_registration_required_fields( $required_fields ) {
    unset ($required_fields ['fname']); 
    unset ($required_fields ['lname']);
    return $required_fields;
};
add_filter( 'dokan_seller_registration_required_fields', 'dokan_custom_seller_registration_required_fields' );

// user registration extra fields DB
function addMyCustomMeta( $user_id ) {    
    update_user_meta( $user_id, 'dokan_custom_kmkr_nr', $_POST['kmkr_nr'] ); 
    update_user_meta( $user_id, 'dokan_custom_rgstr_nr', $_POST['rgstr_nr'] ); 
    update_user_meta( $user_id, 'billing_company', $_POST['shopname'] ); 
    update_user_meta( $user_id, 'nickname', $_POST['shopname'] ); 
    update_user_meta( $user_id, 'billing_phone', $_POST['phone'] ); 
    update_user_meta( $user_id, 'reg_enq_message', $_POST['reg_enq_message'] ); 
    update_user_meta( $user_id, 'reg_enq_message_id', $_POST['reg_enq_message_id'] ); 
}
add_action('user_register', 'addMyCustomMeta');    


// Code for editing extra fields details in database:
function my_save_extra_profile_fields( $user_id ) {
    if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
    }
    update_usermeta( $user_id, 'dokan_custom_kmkr_nr', $_POST['kmkr_nr'] );
    update_usermeta( $user_id, 'dokan_custom_rgstr_nr', $_POST['rgstr_nr'] );
    update_usermeta( $user_id, 'reg_enq_message', $_POST['reg_enq_message'] );
    update_usermeta( $user_id, 'reg_enq_message_id', $_POST['reg_enq_message_id'] );
}

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );


// Code for adding extra fields in Edit User Section:
function extra_user_profile_fields( $user ) { ?>
    <?php 
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }
        $kmkr_nr  = get_user_meta( $user->ID, 'dokan_custom_kmkr_nr', true );
        $rgstr_nr  = get_user_meta( $user->ID, 'dokan_custom_rgstr_nr', true );
        $reg_enq  = get_user_meta( $user->ID, 'reg_enq_message', true );
        $reg_enq_id  = get_user_meta( $user->ID, 'reg_enq_message_id', true );
     ?>

    <h3><?php _e("Ettevõtte lisainformatsioon", "blank"); ?></h3>
    <table class="form-table">
         <tr>
            <th><?php esc_html_e( 'Registrikood', 'dokan-lite' ); ?></th>
            <td>
                <input type="text" name="rgstr_nr" class="regular-text" value="<?php echo esc_attr($rgstr_nr); ?>"/>
            </td>
         </tr>
         <tr>
            <th><?php esc_html_e( 'KMKR Number', 'dokan-lite' ); ?></th>
            <td>
                <input type="text" name="kmkr_nr" class="regular-text" value="<?php echo esc_attr($kmkr_nr); ?>"/>
            </td>
         </tr>
         <tr>
            <th><?php esc_html_e( 'Reg Enq Message', 'dokan-lite' ); ?></th>
            <td>
                <input type="text" name="reg_enq_message" class="regular-text" value="<?php echo esc_attr($reg_enq); ?>"/>
            </td>
         </tr>
         <tr>
            <th><?php esc_html_e( 'Reg Enq Product ID', 'dokan-lite' ); ?></th>
            <td>
                <input type="text" name="reg_enq_message_id" class="regular-text" value="<?php echo esc_attr($reg_enq_id); ?>"/>
            </td>
         </tr>
    </table>
<?php }

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );



/* 
* Remove become a seller from account upgrade page
*/
remove_action( 'woocommerce_after_my_account', array( Dokan_Pro::init(), 'dokan_account_migration_button' ) );

// Remove firstname and lastname requirement from my account page (see on vajalik ka peale UI)
add_filter('woocommerce_save_account_details_required_fields', 'ts_hide_first_name');
function ts_hide_first_name($required_fields)
{
  unset($required_fields["account_first_name"]);
  return $required_fields;
}

add_filter('woocommerce_save_account_details_required_fields', 'ts_hide_last_name');
function ts_hide_last_name($required_fields)
{
  unset($required_fields["account_last_name"]);
  return $required_fields;
}



/**
 * Disable all payment gateways on the checkout page and replace the "Pay" button by "Place order"
 */
add_filter( 'woocommerce_cart_needs_payment', '__return_false' );


// New User Approve emails

/**
 * Modify the message sent to a user after being approved.
 * 
 * @param $message The default message.
 * @param $user The user who will receive the message.
 * @return string the updated message.
 */
function my_custom_email_message( $message, $user ) {
    $message = 'Teie konto lehel Indust on heakskiidetud. 
Palun kasutage sisselogimiseks eelmises kirjas olevaid andmeid.

Head kasutamist
Indust';
    
    return $message;
}

// add a new custom approval message
add_filter( 'new_user_approve_approve_user_message', 'my_custom_email_message', 10, 2 );


/**
* Add a new header
* Muudame saatja emaili
*/
function add_email_header_22( $headers ) {
    $headers[] = "From: \"Indust\" <info@indust.ee>\n";
    return $headers;
}
add_filter( 'new_user_approve_email_header', 'add_email_header_22' );


/**
* Admin approve email
*/
function add_email_admin( $admins ) {
    $admins[] = "henri@indust.ee";
    $admins[] = "sergio@indust.ee";
    return $admins;
}
add_filter( 'new_user_approve_email_admins', 'add_email_admin' );


/**
* DOKAN - remove Seller info tab on product page in dokan plugin
*/
add_filter( 'woocommerce_product_tabs', 'dokan_remove_seller_info_tab', 50 );
function dokan_remove_seller_info_tab( $array ) {
    if ( !is_user_logged_in() ) {
          unset( $array['seller'] );
    }
  return $array;
}


/**
 * Make SKU non unique
 */
add_filter( 'wc_product_has_unique_sku', '__return_false', PHP_INT_MAX );


// Redirect to account page after login
add_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2 ); 
function wc_custom_user_redirect($redirect){
    $redirect =	home_url('/konto/') ; 
    return $redirect;
}

// Remove some myaccount tabs
add_filter( 'woocommerce_account_menu_items', 'my_woocommerce_account_menu_items', 9999 );
function my_woocommerce_account_menu_items( $items ) {
    unset( $items['downloads'] );
    unset( $items['orders'] );
    unset( $items['edit-address'] );
    return $items;
}

// Remove some tabs from vendor dashboard
add_filter( 'dokan_get_dashboard_nav', 'my_dokan_get_dashboard_nav' );
function my_dokan_get_dashboard_nav( $items ) {
    unset( $items['orders'] );
    unset( $items['coupons'] ); // ei toota
    unset( $items['withdraw'] );
    //unset( $items['tool'] ); ei toota
    unset( $items['settings'] );
    return $items;
}


// JQuery!
function my_theme_scripts() {
    wp_enqueue_script( 'my_jquery_script', get_stylesheet_directory_uri() . '/js/my_jquery_script.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'my_theme_scripts' );


// custom single product stuff
add_action( 'woocommerce_single_product_summary', 'custom_after_product_summary', 30 );
function custom_after_product_summary() {
    echo '<div class="rigid-product-popup-link">
    <a>Kuvatud hind on letihind. Hulgihinna, personaalse pakkumise ja täpsemate tingimuste läbirääkimiseks võtke tarnijaga ühendust.</a>
    </div>
    <div>
    <a href="#tab-title-seller_enquiry_form" class="smooth-goto button">Võta ühendust tarnijaga</a>
    </div>';
}

// remove woocommerce add to cart buttons
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart');
remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
add_filter( 'woocommerce_is_purchasable', '__return_false' );



/** 
 * Hide specific attributes from the Additional Information tab on single
 * WooCommerce product pages.
 *
 * @param WC_Product_Attribute[] $attributes Array of WC_Product_Attribute objects keyed with attribute slugs.
 * @param WC_Product $product
 *
 * @return WC_Product_Attribute[]
 */
function mycode_hide_attributes_from_additional_info_tabs( $attributes, $product ) {

	/**
	 * Array of attributes to hide from the Additional Information
	 * tab on single WooCommerce product pages.
	 */
	$hidden_attributes = [
		'pa_tarnija',
	];

	foreach ( $hidden_attributes as $hidden_attribute ) {

		if ( ! isset( $attributes[ $hidden_attribute ] ) ) {
			continue;
		}

		$attribute = $attributes[ $hidden_attribute ];

		$attribute->set_visible( false );
	}

	return $attributes;
}

add_filter( 'woocommerce_product_get_attributes', 'mycode_hide_attributes_from_additional_info_tabs', 20, 2 );



/**
 * Privaatsuspoliitika checkbox
 * @snippet       Add Privacy Policy Checkbox @ WooCommerce My Account Registration Form
 * @how-to        Get CustomizeWoo.com FREE
 * @sourcecode    https://businessbloomer.com/?p=74128
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.5.1
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
 
add_action( 'woocommerce_register_form', 'bbloomer_add_registration_privacy_policy', 11 );
   
function bbloomer_add_registration_privacy_policy() {
 
    woocommerce_form_field( 'privacy_policy_reg', array(
       'type'          => 'checkbox',
       'class'         => array('form-row privacy'),
       'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
       'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
       'required'      => true,
       'label'         => 'Olen lugenud ja nõustun <a target="_blank" rel="noopener noreferrer" href="/terms-of-service">kasutustingimustega</a>',
    ));
  
}
  
// Show error if user does not tick
add_filter( 'woocommerce_registration_errors', 'bbloomer_validate_privacy_registration', 10, 3 );
  
function bbloomer_validate_privacy_registration( $errors, $username, $email ) {
if ( ! is_checkout() ) {
    if ( ! (int) isset( $_POST['privacy_policy_reg'] ) ) {
        $errors->add( 'privacy_policy_reg_error', __( 'Registreerumiseks peate nõustuma kasutustingimustega', 'woocommerce' ) );
    }
}
return $errors;
}


// hide filters from shop page
add_filter( 'sidebars_widgets', 'remove_about_me_widget' );

function remove_about_me_widget( $sidebars_widgets ) {
    //error_log( 'in sidebars' );
    //error_log(print_r($sidebars_widgets,true));
    if( is_admin() ) { 
        return $sidebars_widgets; 
    }
    if( is_shop() ) {
        $shop_array = array(0 => 'woocommerce_product_categories-4');
        $sidebars_widgets['shop'] = $shop_array;
    }
    return $sidebars_widgets;
}


/**
 * @snippet    WooCommerce User Registration Shortcode For Product
 */
   
add_shortcode( 'wc_custom_reg_form', 'separate_registration_form' );
    
function separate_registration_form() {
   if ( is_admin() ) return;
   if ( is_user_logged_in() ) return;
   ob_start();
 
   // NOTE: The following <FORM></FORM> is taken from: woocommerce\templates\myaccount\form-login.php
   // When you update the WooCommerce plugin, you may need to adjust the below accordingly
 
   do_action( 'woocommerce_before_customer_login_form' );
 
   ?>

        <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

            <?php do_action( 'woocommerce_register_form_start' ); ?>

            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                </p>

            <?php endif; ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
            </p>

            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
                </p>

            <?php else : ?>

                <p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

            <?php endif; ?>

            <?php do_action( 'woocommerce_register_form' ); ?>

            <p class="woocommerce-form-row form-row">
                <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
            </p>

            <?php do_action( 'woocommerce_register_form_end' ); ?>

        </form>
 
   <?php
     
   return ob_get_clean();
}


/**
 * @snippet    WooCommerce User Registration Shortcode For Product Enquiry
 */
   
add_shortcode( 'wc_custom_reg_form_enquiry', 'separate_registration_form_enquiry' );
    
function separate_registration_form_enquiry() {
   if ( is_admin() ) return;
   if ( is_user_logged_in() ) return;
   ob_start();
   global $product;

 
   // NOTE: The following <FORM></FORM> is taken from: woocommerce\templates\myaccount\form-login.php
   // When you update the WooCommerce plugin, you may need to adjust the below accordingly
   do_action( 'woocommerce_before_customer_login_form' );
 
   ?>
         <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

            <?php do_action( 'woocommerce_register_form_start' ); ?>

            <div class="form-group">
            <textarea class="form-control" id="reg_enq_message" name="reg_enq_message" placeholder="<?php esc_html_e( 'Details about your enquiry...', 'dokan' ); ?>" rows="5" 
            value="<?php if ( ! empty( $postdata['reg_enq_message'] ) ) echo esc_attr($postdata['reg_enq_message']); ?>" required></textarea>
            </div>

            <input type="hidden" name="reg_enq_message_id" value="<?php echo esc_attr( $product->get_id() ); ?>">

            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                </p>

            <?php endif; ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
            </p>

            <?php do_action( 'woocommerce_register_form' ); ?>

            <p class="woocommerce-form-row form-row">
                <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Saada päring ja registreeru tasuta', 'woocommerce' ); ?></button>
            </p>

            <?php do_action( 'woocommerce_register_form_end' ); ?>

        </form>
 
   <?php
     
   return ob_get_clean();
}

// user menu logic 
function my_wp_nav_menu_args( $args ) {
    if( is_user_logged_in() && isset( $args['theme_location'] ) && $args['theme_location'] == 'primary' ) { 
        $args['menu'] = 'logged-in';
    }
    return $args;
}
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );


// Register custom widget - show only current category subcategories
add_action( 'widgets_init', 'override_woocommerce_widgets', 15 );

function override_woocommerce_widgets() {
  if ( class_exists( 'WC_Widget_Product_Categories' ) ) {
    unregister_widget( 'WC_Widget_Product_Categories' );

    include_once( 'widgets/class-custom-wc-widget-product-categories.php' );

    register_widget( 'Custom_WC_Widget_Product_Categories' );
  }

}


