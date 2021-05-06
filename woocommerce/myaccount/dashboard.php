<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<p>
	<?php
	printf(
		/* translators: 1: user display name 2: logout url */
		wp_kses( __( 'Tere %1$s ', 'woocommerce' ), $allowed_html ),
		'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
		esc_url( wc_logout_url() )
	);
	?>
</p>

<p>
	<?php
    $user = wp_get_current_user();
    if ( in_array( 'seller', (array) $user->roles ) ) {
        $dashboard_desc = __( '<p>Tänud, et registreerisite meie lehel. Teie konto on nüüd aktiivne ja tooted üles laetud.</p>

<p>Hetkel saavad ostjad Teiega kontakteeruda läbi tootelehel oleva päringu vormi, mille sisu saadetakse Teile e-mailile. Samuti on võimalik Teiega kontakteeruda Teie Poe lehel.</p>

<p>Oma tooteid näete kui vajutate allolevat nuppu TARNIJA TÖÖLAUD. Tarnija Töölaual saate muuta ka oma salasõna ja vaadata konto andmeid.</p>' );
    } else {
        /* translators: 1: Orders URL 2: Address URL 3: Account URL. */
	   $dashboard_desc = __( 'Täname registreerimast. Meie lehel saate kontakteeruda ametlike tarnijatega. Selleks vajutage soovitud toote lehel nuppu “Saada päring”.' );
    }

	printf(
		wp_kses( $dashboard_desc, $allowed_html ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-address' ) ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);
	?>
</p>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
