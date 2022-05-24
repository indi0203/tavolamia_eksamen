<?php

class LWP_MenuCart extends ET_Builder_Module {

	public $slug       = 'lwp_menu_cart';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://www.learnhowwp.com/divi-menu-cart/',
		'author'     => 'Learnhowwp.com',
		'author_uri' => 'https://learnhowwp.com/',
	);

	public function init() {
		$this->name = esc_html__( 'Menu Cart', 'lwp-divi-module' );
		$this->main_css_element = '%%order_class%%';
	}

	public function get_fields() {
		return array(
			'icon_font_size' => array(
				'label'           => esc_html__( 'Icon Font Size', 'lwp-divi-module' ),
				'description'     => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'lwp-divi-module' ),
				'type'            => 'range',
				'option_category' => 'font_option',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'default'         => '14px',
				'default_unit'    => 'px',
				'default_on_front'=> '',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings' => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'  	=> true,
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'lwp-divi-module' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Here you can define a custom color for your icon.', 'lwp-divi-module' ),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon_settings',				
			),																					
		);
	}

	public function get_settings_modal_toggles() {

		return array(
			'advanced' => array(
				'toggles' => array(
					'icon_settings' 	=> esc_html__( 'Icon', 'lwp-divi-module' ),
				),
			),
		);

	}	

	public function get_advanced_fields_config() {
		return array(
			'fonts' => array(
				'body'   => array(
					'css'   => array(
						'main' => "{$this->main_css_element} a",
					),
					'label' => esc_html__( 'Cart Menu', 'lwp-divi-module' ),
				),
				'count'   => array(
					'css'   => array(
						'main' => "{$this->main_css_element} .lwp_menu_cart_count",
					),
					'label' => esc_html__( 'Count', 'lwp-divi-module' ),
				),				
				'price'   => array(
					'css'   => array(
						'main' => "{$this->main_css_element} .woocommerce-Price-amount",
					),
					'label' => esc_html__( 'Price', 'lwp-divi-module' ),
				),
			),
		);
	}	

	public function render( $attrs, $content = null, $render_slug ) {

		$options 				= get_option('lwp_menu_cart_options');
		
		$single_item_text 		= '';
		$multiple_item_text		= '';
		$show_icon				= '';
		$show_item_count		= '';
		$show_price				= '';

		$icon_color 					= esc_attr($this->props['icon_color']);

		$icon_font_size 				= esc_attr($this->props['icon_font_size']);		
		$icon_font_size_tablet 			= esc_attr($this->props['icon_font_size_tablet']);		
		$icon_font_size_phone 			= esc_attr($this->props['icon_font_size_phone']);		
		$icon_font_size_last_edited		= esc_attr($this->props['icon_font_size_last_edited']);
		
		if(false == $options){	//Show default content
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

		if(isset($icon_font_size)){

			$icon_font_size_responsive_active = et_pb_get_responsive_status( $icon_font_size_last_edited );

			$icon_font_size_values = array(
				'desktop' => $icon_font_size,
				'tablet'  => $icon_font_size_responsive_active ? $icon_font_size_tablet : '',
				'phone'   => $icon_font_size_responsive_active ? $icon_font_size_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $icon_font_size_values, '%%order_class%% .lwp_cart_icon,.et-db #et-boc .et-l %%order_class%% .lwp_cart_icon', 'font-size', $render_slug,'','range');
		}
		
		if( isset($icon_color) && !empty($icon_color) ){
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .lwp_cart_icon,.et-db #et-boc .et-l %%order_class%% .lwp_cart_icon',
				'declaration' => sprintf('color:%1s;',$icon_color),
			) );		
		}

		$output = sprintf('
			<a  class="lwp_cart_module" href="%1s" title="Cart">
				%2s <span class="lwp_menu_cart_count">%3s</span> %4s
			</a>
		',$carturl,$icon_output,$cartcount,$carttotal_output);

		return $output;
	}
}

new LWP_MenuCart;
