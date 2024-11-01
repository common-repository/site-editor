<?php
/*
Module Name:Footer
Module URI: http://www.siteeditor.org/modules/footer
Description: Module Footer For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
/*
if( !is_pb_module_active( "widget" ) || !is_pb_module_active( "social-bar" ) || !is_pb_module_active( "icons" ) || !is_pb_module_active( "title" ) ){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>Widget Module</b><br /> please first install and activate its ") );
    return ;
}
*/
class PBfooterShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_footer",                          //*require
                "title"       => __("Footer","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "sedico-footer",                         //*require for icon toolbar
                "module"      =>  "footer"                              //*require
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
        //'footer_style'   => "footer-dark-style"
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        extract($atts);

    }

    function shortcode_settings(){ 

        $params = array(
            /*'footer_style' => array(
                'type' => 'select',
                'label' => __('Style', 'site-editor'),
                'description' => '',// __('Action Click on this Image', 'site-editor'),
                'choices'   =>array(
                    'footer-dark-style'             => __('Style1', 'site-editor'),
                    'footer-light-style'            => __('Style2', 'site-editor'),
                ),
                'panel'    => 'image_settings_panel',
            ),*/

            'row_container' => array(
                'type'          => 'row_container',
                'label'         => __('Module Wrapper Settings', 'site-editor')
            ), 

            "animation"  =>  array(
                "type"                => "animation" ,
                "label"               => __("Animation Settings", "site-editor"),
                'button_style'        => 'menu' ,
                'has_border_box'      => false ,
                'icon'                => 'sedico-animation' ,
                'field_spacing'       => 'sm' ,
                'priority'            => 530 ,
            )
        );

        return $params;

    }


    function custom_style_settings(){  
        return array(

            array(    
            'footer-inner' , '.footer-inner' ,  
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Footer Container" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $footer_menu = $context_menu->create_menu( "footer" , __("Footer","site-editor") , 'footer' , 'class' , 'element' , ''  , "sed_footer" , array(
            "seperator"    => array(75),
            "change_skin"  =>  false ,
            "duplicate"    => false
        ));
    }

}

new PBfooterShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "theme" ,
    "name"        => "footer",
    "title"       => __("Footer","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "sedico-footer",
    "shortcode"   => "sed_footer",
    "tpl_type"    => "underscore" ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array(),
));



