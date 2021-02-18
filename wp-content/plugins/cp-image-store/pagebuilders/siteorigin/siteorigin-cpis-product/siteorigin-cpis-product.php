<?php
/*
Widget Name: Image Store Product
Description: Inserts a product's shortcode.
Documentation: https://wordpress.dwbooster.com/content-tools/image-store
*/

class SiteOrigin_ImageStore_Product extends SiteOrigin_Widget
{
	function __construct()
	{
		parent::__construct(
			'siteorigin-imagestore-product',
			__('Image Store Product', CPIS_TEXT_DOMAIN),
			array(
				'description' 	=> __('Inserts the Product shortcode', CPIS_TEXT_DOMAIN),
				'panels_groups' => array('cpis-image-store'),
				'help'        	=> 'https://wordpress.dwbooster.com/content-tools/image-store'
			),
			array(),
			array(
				'product' => array(
					'type' => 'number',
					'label' => __("Enter the product's id", CPIS_TEXT_DOMAIN)
				),
				'layout' => array(
					'type' 		=> 'select',
					'label' 	=> __("Select the product's layout", CPIS_TEXT_DOMAIN),
					'default' 	=> 'single',
					'options' 	=> array(
						'multiple'  => __("Short", CPIS_TEXT_DOMAIN),
						'single' => __("Completed", CPIS_TEXT_DOMAIN)
					)
				)
			),
			plugin_dir_path(__FILE__)
		);
	} // End __construct

	function get_template_name($instance)
	{
        return 'siteorigin-cpis-product-shortcode';
    } // End get_template_name

    function get_style_name($instance)
	{
        return '';
    } // End get_style_name

} // End Class SiteOrigin_ImageStore_Product

// Registering the widget
siteorigin_widget_register('siteorigin-imagestore-product', __FILE__, 'SiteOrigin_ImageStore_Product');