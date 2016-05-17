<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Bio-Recovery' );
define( 'CHILD_THEME_URL', 'http://www.studiopress.com/' );
define( 'CHILD_THEME_VERSION', '2.2.2' );

//* Enqueue Google Fonts
add_action( 'wp_enqueue_scripts', 'bio_recovery_google_fonts' );
function bio_recovery_google_fonts() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Open+Sans:300,400,400italic,700', array(), CHILD_THEME_VERSION );
	
	// Add Mobile Button Script to Header Right Widget Navigation Menu
	wp_enqueue_script( 'header_nav_for_mobile', get_bloginfo( 'stylesheet_directory' ) . '/lib/js/header-mobile-nav.js', array('jquery'), '1.0.0' );

}

//Enqueue the Dashicons script
add_action( 'wp_enqueue_scripts', 'mm_enqueue_dashicons' );
function mm_enqueue_dashicons() {
	wp_enqueue_style( 'mm-dashicons-style', get_stylesheet_directory_uri(), array('dashicons'), '1.0' );
}



//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

//* Add Accessibility support
add_theme_support( 'genesis-accessibility', array( 'headings', 'drop-down-menu',  'search-form', 'skip-links', 'rems' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Add support for WooCommerce
add_theme_support( 'genesis-connect-woocommerce' );


// Display 24 products per page
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 24;' ), 20 );

// Product Page title
add_filter( 'woocommerce_page_title', 'custom_woocommerce_page_title');
function custom_woocommerce_page_title( $page_title ) {
  if( $page_title == 'Shop' ) {
    return 'Bio-Recovery Products';
  }
}

// Register Utility Bar Widget Areas
genesis_register_sidebar( array(
	'id'			=> 'utility-bar-left',
	'name'			=> __( 'Utility Bar Left', 'bio-recovery' ),
	'description'	=> __( 'This is the left utility bar above the header.', 'bio-recovery' ),
) );

genesis_register_sidebar( array(
	'id'			=> 'utility-bar-right',
	'name'			=> __( 'Utility Bar Right', 'bio-recovery' ),
	'description'	=> __( 'This is the right utility bar above the header.', 'bio-recovery' ),
) );

add_action( 'genesis_before_header', 'utility_bar' );
function utility_bar() {
	echo '<div class="utility-bar"><div class="wrap">';

	genesis_widget_area( 'utility-bar-left', array(
		'before'	=> '<div class="utility-bar-left">',
		'after' 	=> '</div>',
	) );


	genesis_widget_area( 'utility-bar-right', array(
		'before'	=> '<div class="utility-bar-right">',
		'after' 	=> '</div>',
	) );

	echo '</div></div>';
}

//* Remove the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );

// Register after post widget area
genesis_register_sidebar( array(
	'id'            => 'home-cta',
	'name'          => __( 'Home CTA', 'bio-recovery' ),
	'description'   => __( 'Advertising the Main products', 'bio-recovery' ),
) );

add_action( 'genesis_after_header', 'home_cta_widget' );
function home_cta_widget() {
	if ( is_front_page() ) {
	echo '<div class="home-cta"><div class="wrap">';

	genesis_widget_area( 'home-cta', array(
		'before' 	=> '<div class="widget-area">',
		'after'  	=> '</div>',
	) );

	echo '</div></div>';
	}
}

//* Add search bar
add_filter( 'wp_nav_menu_items', 'product_search_secondary_nav_menu', 10, 2 );
function product_search_secondary_nav_menu( $menu, stdClass $args ){
        
       
        if ( 'secondary' != $args->theme_location )
        	return $menu;
        
               if( genesis_get_option( 'nav_extras' ) )
                return $menu;

        add_filter( 'genesis_search_form', 'custom_products_search', 10, 4 );
        
        $menu .= sprintf( '<li class="custom-search">%s</li>', __( genesis_search_form( $echo ) ) );

        remove_filter( 'genesis_search_form', 'custom_products_search', 10 );
        
        return $menu;
}

function custom_products_search( $form, $search_text, $button_text, $label ) {

	$form  = sprintf( '<form method="get" class="search-form" action="%s" role="search">%s<input type="search" name="s" placeholder="Product Search" /><input type="submit" value="&#xf179;" /><input type="hidden" name="post_type" value="product"></form>', home_url( '/' ), $label, $search_text, esc_attr( $button_text ) );

	return $form;
}

// Customize search form label - BLANK
add_filter( 'genesis_search_form_label', 'mm_search_form_label' );
function mm_search_form_label ( $text ) {
	return esc_attr( ' ' );
}

// Enable Random Sorting
add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );
function custom_woocommerce_get_catalog_ordering_args( $args ) {
  $orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	if ( 'random_list' == $orderby_value ) {
		$args['orderby'] = 'rand';
		$args['order'] = '';
		$args['meta_key'] = '';
	}
	return $args;
}
add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );
function custom_woocommerce_catalog_orderby( $sortby ) {
	$sortby['random_list'] = 'Random';
	return $sortby;
}

// Custom Default Product Image
add_action( 'init', 'custom_fix_thumbnail' );
 
function custom_fix_thumbnail() {
  add_filter('woocommerce_placeholder_img_src', 'custom_woocommerce_placeholder_img_src');
   
	function custom_woocommerce_placeholder_img_src( $src ) {
	$upload_dir = wp_upload_dir();
	$uploads = untrailingslashit( $upload_dir['baseurl'] );
	$src = $uploads . '/2016/04/bio-product.png';
	 
	return $src;
	}
}
// Add save percent next to sale item prices.
add_filter( 'woocommerce_sale_price_html', 'woocommerce_custom_sales_price', 10, 2 );
function woocommerce_custom_sales_price( $price, $product ) {
	$percentage = round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 );
	return $price . sprintf( __(' Now %s Off', 'woocommerce' ), $percentage . '%' );
}

//* Shortcodes in Sidebar Text Widget
add_filter('widget_text', 'do_shortcode');

