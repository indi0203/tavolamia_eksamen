<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );


// END ENQUEUE PARENT ACTION

function my_scripts_method() {
    wp_enqueue_script(
          'custom-script',
          get_stylesheet_directory_uri() . '/js/topbutton.js',
          array( 'jquery' )
    );
}

// tilføjer description under thumbnail

add_action( 'woocommerce_before_shop_loop_item_title', 'wc_add_long_description' );
function wc_add_long_description() {
	global $product;

	?>
        <div class="øverest" itemprop="description">
            <?php echo apply_filters( 'the_content', $product->get_description() ) ?>
        </div>
	<?php
}


// tilføjer divider billede på single product side

add_action('woocommerce_after_single_product_summary','mycustomfuncion',11);
function mycustomfuncion()
{
    echo '<div class="my_divider"><img src="http://indiamillward.dk/kea/tavolamia/wp-content/uploads/2022/05/divider-13.png" alt="" /></div>';
}