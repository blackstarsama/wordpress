<?php
/*
Widget Name: Image Store
Description: Inserts the Image Store shortcode.
Documentation: https://wordpress.dwbooster.com/content-tools/image-store
*/

class SiteOrigin_ImageStore extends SiteOrigin_Widget
{
	function __construct()
	{
		parent::__construct(
			'siteorigin-image-store',
			__('Image Store', CPIS_TEXT_DOMAIN),
			array(
				'description' 	=> __('Inserts the Image Store shortcode', CPIS_TEXT_DOMAIN),
				'panels_groups' => array('cpis-image-store'),
				'help'        	=> 'https://wordpress.dwbooster.com/content-tools/image-store'
			),
			array(),
			array(
				'shortcode' => array(
					'type' 		=> 'textarea',
					'label' 	=> __( 'Image Store Shortcode', CPIS_TEXT_DOMAIN ),
					'default'	=> '[codepeople-image-store]',
				)
			),
			plugin_dir_path(__FILE__)
		);
	} // End __construct

	function get_template_name($instance)
	{
		return 'siteorigin-cpis-shortcode';
    } // End get_template_name

    function get_style_name($instance)
	{
        return '';
    } // End get_style_name

} // End Class SiteOrigin_ImageStore

// Registering the widget
siteorigin_widget_register('siteorigin-image-store', __FILE__, 'SiteOrigin_ImageStore');