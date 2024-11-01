<?php
/*
* Module Name: Icons
* Module URI: http://www.siteeditor.org/modules/icons
* Description: Icons Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/
class PBIconsShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_icons",                                      //*require
                "title"       => __("Icons","site-editor"),                        //*require for toolbar
                "description" => __("Add Icons Bar To Page","site-editor"),
                "icon"        => "sedico-icons",                                     //*require for icon toolbar
                "module"      =>  "icons"                                           //*require
            ) // Args
		);

        add_action( 'wp_enqueue_scripts', array( $this , 'load_default_font_icon' ) );

        add_filter( 'sed_shortcode_has_preset' , array( $this , 'remove_preset_support' ) , 10 , 2 );
    }

    function remove_preset_support( $is_support , $shortcode ){

        if( $shortcode == "sed_icons" ){
            return false;
        }

        return $is_support;

    }

    //loaded FontAwesome allways
    function load_default_font_icon(){
        wp_enqueue_style('sed-FontAwesome' , SED_EXT_URL.'icon-library/fonts/FontAwesome/FontAwesome.css' , array() , "4.3");
    }

    function get_atts(){
        $atts = array(
              'icon'                  =>  'fa fa-camera-retro' ,
              'border_color'          =>  '#ccc' ,
              'border_size'           =>  2  , 
              'link'                  =>  '' ,
              'link_target'           =>  '_self' , 
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        extract($atts);


    }

    function less(){
        return array(
            array( 'icon-main-less' )
        );
    }

    function styles(){
        $styles = array();
        $fonts = get_option('sed_icons_fonts');

        if( is_array( $fonts ) && !empty( $fonts ) ){
            foreach( $fonts as $font => $info){
                array_push( $styles , array('sed-'.$font,$info['style']) );
            }
        }

        return $styles;
    }

    function shortcode_settings(){  

        $this->add_panel( 'icon_settings_panel_outer' , array(
            'title'                   =>  __('Icon Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' , 
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-icons' ,
            'field_spacing'           => 'sm'
        ) );

        $this->add_panel( 'icon_settings_panel' , array(
            'title'                   =>  __('Icon Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'parent_id'               => "icon_settings_panel_outer",
            'priority'                => 9 , 
        ) );

        $params = array(

            'icon' => array(
                "type"                => "icon" , 
                "label"               => __("Icon Field", "site-editor"),
                "description"         => __("This option allows you to set a icon for your module.", "site-editor"),
                'has_border_box'      => false ,
                'panel'               => 'icon_settings_panel',   
            ), 

            'font_size' => array(
                "type"                => "font-size" , 
                "after_field"         => "px",
                "label"               => __("Font Size", "site-editor"),
                "description"         => __("Add Font Size For Element", "site-editor") ,
                "category"            => 'style-editor' ,
                "selector"            => '.hi-icon' ,
                "default"             => '' , 
                'has_border_box'      => false , 
                'panel'               => 'icon_settings_panel'
            ),  

            'font_color' => array(
                "type"                => "font-color" , 
                "label"               => __("Font Color", "site-editor"),
                "description"         => __("Add Font Color For Element", "site-editor") ,
                "category"            => 'style-editor' ,
                "selector"            => '.hi-icon' ,
                "default"             => '#ccc' ,
                'has_border_box'      => false , 
                'panel'               => 'icon_settings_panel' 
            ),

            "link" => array(
                "type"                => "link" ,
                "label"               => __("Link Panel Settings", "site-editor"),
                "panel_type"          => "default" ,
                'parent_id'           => 'icon_settings_panel_outer',
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
            'icon' , '.hi-icon' ,
            array('background','gradient','border','border_radius' ,'padding', 'font') , __("Icon Container" , "site-editor") ) ,

            array(
            'icon-hover' , '.hi-icon:hover' ,
            array( 'background','gradient','border','border_radius', 'font') , __("Icon Hover" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $icons_menu = $context_menu->create_menu( "icons" , __("Icons","site-editor") , 'icons' , 'class' , 'element' , '', "sed_icons" , array(
                "change_icon"      => true ,
                "link_to"          => true ,
                "seperator"        => array(45 , 75)
            ) );
    }

}

new PBIconsShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"                 => "basic" ,
    "name"                  => "icons",
    "title"                 => __("Icons","site-editor"),
    "description"           => __("","site-editor"),
    "icon"                  => "sedico-icons",
    "shortcode"             => "sed_icons",
    "tpl_type"              => "underscore" ,
    "show_ui_in_toolbar"    =>  false ,
    "js_module"             => array( 'sed_icons_module_script', 'icons/js/icons-module.min.js', array('sed-frontend-editor'))
));



