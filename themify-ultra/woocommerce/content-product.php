<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
/*
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
$is_shop_catalog = (is_shop() || is_post_type_archive( 'product' ) || is_tax( get_object_taxonomies( 'product' ) )) && 'custom' !== themify_get( 'setting-product_shop_image_size' );
if($is_shop_catalog){
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open' );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close' );
}*/
?>

