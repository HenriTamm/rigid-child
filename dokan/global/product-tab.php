<?php
/**
 * Dokan Seller Single product tab Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<h2><?php esc_html_e( 'Vendor Information', 'dokan-lite' ); ?></h2>

<ul class="list-unstyled">
    <?php do_action( 'dokan_product_seller_tab_start', $author, $store_info ); ?>

    <li class="seller-name">
        <span>
            <?php esc_html_e( 'Vendor:', 'dokan-lite' ); ?>
        </span>

        <span class="details">
            <?php printf( '<a href="%s">%s</a>', esc_url( dokan_get_store_url( $author->ID ) ), esc_attr( $author->display_name ) ); ?>
        </span>
    </li>

    <?php do_action( 'dokan_product_seller_tab_end', $author, $store_info ); ?>
</ul>
