<?php
/*
Plugin Name: Menu Cart Module Divi
Plugin URI:  https://www.learnhowwp.com/divi-menu-cart/
Description: This plugins adds a new module in the Divi Builder. The module allows you add cart icon with item count and price.
Version:     1.1
Author:      learnhowwp.com
Author URI:  http://learnhowwp.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: lwp-divi-module
Domain Path: /languages

Menu Cart Module Divi is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Menu Cart Module Divi is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Menu Cart Module Divi. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


if ( ! function_exists( 'lwpdm_initialize_menu_cart_extension' ) ):
/**
 * Creates the extension's main class instance.
 *
 * @since 1.0.0
 */
function lwpdm_initialize_menu_cart_extension() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/MenuCartModuleDivi.php';
}
add_action( 'divi_extensions_init', 'lwpdm_initialize_menu_cart_extension' );
endif;

if ( ! function_exists( 'lwpdm_cart_add_to_cart_fragment' ) ):
add_filter( 'woocommerce_add_to_cart_fragments', 'lwpdm_cart_add_to_cart_fragment' );

function lwpdm_cart_add_to_cart_fragment( $fragments ) {

    $single_item_text 		= '';
    $multiple_item_text		= '';
    $show_icon				= '';
    $show_item_count		= '';
    $show_price				= '';

    $options 				= get_option('lwp_menu_cart_options');
    
    if(false == $options){  //Show default content
        $show_icon				= 'on';
        $show_item_count		= 'on';
        $show_price				= 'on';
    }
    else{
        if( isset( $options['show_item_count'] ) && $options['show_item_count']=='on' )
            $show_item_count = 'on';

        if( isset( $options['show_icon'] ) && $options['show_icon']=='on' )
            $show_icon = 'on';

        if( isset( $options['show_price'] ) && $options['show_price']=='on' )
            $show_price = 'on';

        if( isset( $options['single_item_text'] ) )
            $single_item_text = esc_html( $options['single_item_text'] );

        if( isset( $options['multiple_item_text'] ) )
            $multiple_item_text = esc_html( $options['multiple_item_text'] ); 						
    }

    $cartcount 			='';
    $carttotal 			='';
    $carturl			='';
    $icon_output 		='';
    $carttotal_output 	='';

    if($show_icon==='on'){
        $icon_output = '<span class="image_wrap"><span class="lwp_cart_icon et-pb-icon">&#xe07a;</span></span>';			
    }	

    if( function_exists( 'WC' ) && $show_item_count=='on' ){
        
        $total = WC()->cart->get_cart_contents_count();

        if( !empty($single_item_text) && !empty($multiple_item_text) ){
            $single 			= $total.' '.$single_item_text;
            $multiple 			= $total.' '.$multiple_item_text;

            if($total == 1)
                $cartcount = $single;
            else
                $cartcount = $multiple;
        }

        else if( !empty($single_item_text) && $total == 1){
            $single 			= $total.' '.$single_item_text;
            $cartcount = $single;
        }

        else if( !empty($multiple_item_text) && ($total ==0 || $total > 1) ){
            $multiple 			= $total.' '.$multiple_item_text;				
            $cartcount = $multiple;
        }
        else{
            $cartcount = sprintf ( _n( '%d Item', '%d Items', WC()->cart->get_cart_contents_count(),'lwp-divi-module') , $total );
        }
    }

    if( function_exists( 'WC' ) && $show_price=='on' ){			
        $carttotal = WC()->cart->get_cart_total();
        if($show_item_count =='on')
            $carttotal_output = sprintf('<span class="lwp_menu_cart_sep"> -</span> %1s',$carttotal);
        else
            $carttotal_output = sprintf('%1s',$carttotal);
    }

    if( function_exists( 'wc_get_cart_url' ) )
        $carturl = wc_get_cart_url();

    $output = sprintf('
        <a  class="lwp_cart_module" href="%1s" title="Cart">
            %2s <span class="lwp_menu_cart_count">%3s</span> %4s
        </a>
    ',$carturl,$icon_output,$cartcount,$carttotal_output);

	global $woocommerce;
	
    ob_start();

    $allowed_html= array(
        'a' => array(
            'class' => array(),
            'href'  => array(),
            'title' => array(),
        ),
        'span' => array(
            'class' => array(),
        ),
        'bdi' => array()
    );

    echo wp_kses($output,$allowed_html);
	
    $fragments['a.lwp_cart_module'] = ob_get_clean();
	
    return $fragments;
}
endif;


if ( ! function_exists( 'lwpdm_menu_cart_options_page' ) ): 
//Menu
add_action('admin_menu', 'lwpdm_menu_cart_options_page');

function lwpdm_menu_cart_options_page() {
    add_menu_page(
        'Divi Menu Cart', 
        'Divi Menu Cart',
        'manage_options',
        'lwp_menu_cart',
        'lwpdm_menu_cart_options_page_output',
        'dashicons-cart'
    );
}
endif;

if ( ! function_exists( 'lwpdm_menu_cart_options_page_output' ) ): 
//Menu Page
function lwpdm_menu_cart_options_page_output() {
?>
<div>
    <h2>Divi Menu Cart</h2>
    <?php settings_errors(); ?>
    <form action="options.php" method="post">
    <?php settings_fields('lwp_menu_cart_options'); ?>
    <?php do_settings_sections('lwp_menu_cart'); ?>
    <input class="button-primary" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
</div>
<?php
}
endif;

if ( ! function_exists( 'lwpdm_menu_cart_admin_init' ) ): 
//Register Setting, Section and Fields
add_action('admin_init', 'lwpdm_menu_cart_admin_init');

function lwpdm_menu_cart_admin_init(){

register_setting( 'lwp_menu_cart_options', 'lwp_menu_cart_options', 'lwpdm_menu_cart_options_validate' );

add_settings_section('lwp_menu_cart_main_section', 'Main Settings', 'lwpdm_menu_cart_section_text', 'lwp_menu_cart');

add_settings_field('single_item_text', 'Single Item Text', 'lwpdm_menu_cart_single_item_text', 'lwp_menu_cart', 'lwp_menu_cart_main_section');

add_settings_field('multiple_item_text', 'Multiple Item Text', 'lwpdm_menu_cart_multiple_item_text', 'lwp_menu_cart', 'lwp_menu_cart_main_section');

add_settings_field('show_icon', 'Show Icon', 'lwpdm_menu_cart_show_icon', 'lwp_menu_cart', 'lwp_menu_cart_main_section');

add_settings_field('show_item_count', 'Show Item Count', 'lwpdm_menu_cart_show_item_count', 'lwp_menu_cart', 'lwp_menu_cart_main_section');

add_settings_field('show_price', 'Show Price', 'lwpdm_menu_cart_show_price', 'lwp_menu_cart', 'lwp_menu_cart_main_section');

}
endif;

if ( ! function_exists( 'lwpdm_menu_cart_section_text' ) ): 
//Description text for section
function lwpdm_menu_cart_section_text() {
    echo '<p>You can set the content for Divi Menu Cart here. The styles can be set inside the Module Settings Design tab.</p>';
}
endif;

if ( ! function_exists( 'lwpdm_menu_cart_single_item_text' ) ): 
//Output  of setting field
function lwpdm_menu_cart_single_item_text() {

    $options = get_option('lwp_menu_cart_options');
    echo "<input id='single_item_text' name='lwp_menu_cart_options[single_item_text]' type='text' placeholder='Item' value='";
    if( isset( $options['single_item_text'] ) )
        echo esc_attr( $options['single_item_text'] );
    echo "' />";    

}
endif;

if ( ! function_exists( 'lwpdm_menu_cart_multiple_item_text' ) ): 
function lwpdm_menu_cart_multiple_item_text() {

    $options = get_option('lwp_menu_cart_options');
    echo "<input id='multiple_item_text' name='lwp_menu_cart_options[multiple_item_text]' type='text' placeholder='Items' value='";
    if( isset( $options['multiple_item_text'] ) )
        echo esc_attr ( $options['multiple_item_text'] );
    echo "' />";    

}
endif;

if ( ! function_exists( 'lwpdm_menu_cart_show_item_count' ) ): 
function lwpdm_menu_cart_show_item_count() {

    $options = get_option('lwp_menu_cart_options');

    echo "<input id='show_item_count' name='lwp_menu_cart_options[show_item_count]' type='checkbox' ";
    if( isset($options['show_item_count']) )
        echo 'checked';
    echo " />";

}
endif;

if ( ! function_exists( 'lwpdm_menu_cart_show_icon' ) ):
function lwpdm_menu_cart_show_icon() {

    $options = get_option('lwp_menu_cart_options');

    echo "<input id='show_icon' name='lwp_menu_cart_options[show_icon]' type='checkbox' ";
    if( isset($options['show_icon']) )
        echo 'checked';
    echo " />";

}
endif;

if ( ! function_exists( 'lwpdm_menu_cart_show_price' ) ):
function lwpdm_menu_cart_show_price() {

    $options = get_option('lwp_menu_cart_options');

    echo "<input id='show_price' name='lwp_menu_cart_options[show_price]' type='checkbox' ";
    if( isset($options['show_price']) )
        echo 'checked';
    echo " />";

}
endif;

if ( ! function_exists( 'lwpdm_menu_cart_options_validate' ) ):
function lwpdm_menu_cart_options_validate($input) {

    $output = array();

    if(isset( $input['single_item_text'] ))
        $output['single_item_text'] = sanitize_text_field( $input['single_item_text'] );

    if(isset( $input['multiple_item_text'] ))    
        $output['multiple_item_text'] = sanitize_text_field( $input['multiple_item_text'] );

    if( isset( $input['show_item_count'] ) && $input['show_item_count']=='on' )    
        $output['show_item_count'] = sanitize_text_field( $input['show_item_count'] );
    
    if( isset( $input['show_icon'] ) && $input['show_icon']=='on' )    
        $output['show_icon'] = sanitize_text_field( $input['show_icon'] );
        
    if( isset( $input['show_price'] ) && $input['show_price']=='on' )    
        $output['show_price'] = sanitize_text_field( $input['show_price'] );        

    return $output;
}
endif;

/*
Rating
*/
if ( ! function_exists( 'lwpdm_menu_cart_activation_time' ) ):
    function lwpdm_menu_cart_activation_time(){

        $get_activation_time = strtotime("now");
        add_option('lwpdm_menu_cart_activation_time', $get_activation_time );

    }
    register_activation_hook( __FILE__, 'lwpdm_menu_cart_activation_time' );
endif;

if ( ! function_exists( 'lwpdm_menu_cart_check_installation_time' ) ):
    function lwpdm_menu_cart_check_installation_time() {   
        
        $install_date = get_option( 'lwpdm_menu_cart_activation_time' );
        $spare_me = get_option( 'lwpdm_menu_cart_spare_me' );
        $past_date = strtotime( '-7 days' );
     
        if ( $past_date >= $install_date && $spare_me==false) {
     
            add_action( 'admin_notices', 'lwpdm_menu_cart_rating_admin_notice' );
        }
    
    }
    add_action( 'admin_init', 'lwpdm_menu_cart_check_installation_time' );
endif;

if ( ! function_exists( 'lwpdm_menu_cart_rating_admin_notice' ) ):
    /*
    Display Admin Notice, asking for a review
    */
    function lwpdm_menu_cart_rating_admin_notice() {
        
            $dont_disturb = esc_url( get_admin_url() . '?lwpdm_menu_cart_spare_me=1' );
            $dont_show = esc_url( get_admin_url() . '?lwpdm_menu_cart_spare_me=1' );
            $plugin_info = 'Menu Cart Module Divi';       
            $reviewurl = esc_url( 'https://wordpress.org/support/plugin/menu-cart-divi/reviews/?filter=5' );
            
            printf(__('<div class="wrap notice notice-info">
                            <div style="margin:10px 0px;">
                                Hello! Seems like you are using <strong> %s </strong> plugin to build your Divi website - Thanks a lot! Could you please do us a BIG favor and give it a 5-star rating on WordPress? This would boost our motivation and help other users make a comfortable decision while choosing the plugin.
                            </div>	
                            <div class="button-group" style="margin:10px 0px;">
                                <a href="%s" class="button button-primary" target="_blank" style="margin-right:10px;">Ok,you deserve it</a>
                                <span class="dashicons dashicons-smiley"></span><a href="%s" class="button button-link" style="margin-right:10px; margin-left:3px;">I already did</a>
                                <a href="%s" class="button button-link"> Don\'t show this again.</a>							
                            </div>
                        </div>', 'lwp-divi-module'), $plugin_info, $reviewurl, $dont_disturb,$dont_show );
    }
endif;

if ( ! function_exists( 'lwpdm_menu_cart_spare_me' ) ):
    function lwpdm_menu_cart_spare_me(){ 
    
        if( isset( $_GET['lwpdm_menu_cart_spare_me'] ) && !empty( $_GET['lwpdm_menu_cart_spare_me'] ) ){
    
            $lwpdm_menu_cart_spare_me = $_GET['lwpdm_menu_cart_spare_me'];
    
            if( $lwpdm_menu_cart_spare_me == 1 ){
                add_option( 'lwpdm_menu_cart_spare_me' , TRUE );
            }
    
        }
    
    }
    add_action( 'admin_init', 'lwpdm_menu_cart_spare_me', 5 );
endif;