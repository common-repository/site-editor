<?php
/*
* Module Name: API Test Module
* Module URI: http://www.siteeditor.org/modules/api-test-module
* Description: API Test Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

class PBAPITestModule extends PBShortcodeClass{

	function __construct() {
		parent::__construct( array(
                "name"        => "sed_api_test",                               //*require
                "title"       => __("API Test Module","site-editor"),                 //*require for toolbar
                "description" => __("Add API Test Module To Page","site-editor"),
                "icon"        => "sedico-api-test-module",                              //*require for icon toolbar
                "module"      =>  "api-test-module"         //*require
            ) // Args
		);

	}

    function get_atts(){
        $atts = array(
            "text_field_attr"    =>  "Test Value" ,
            "tel_field_attr"    =>  "" ,
            "pass_field_attr"    =>  "" ,
            "search_field_attr"    =>  "" ,
            "url_field_attr"    =>  "" ,
            "email_field_attr"    =>  "" ,
            "date_field_attr"    =>  "07/05/2016" ,
            "dimension_field_attr"    =>  "" ,
            "textarea_field_attr"    =>  "Test text in textarea" ,
            "single_select_field_attr"   =>  "" ,
            "multi_select_field_attr"   =>  "options1_key" ,
            "og_single_select_field_attr"   =>  "" ,
            "og_multi_select_field_attr"   =>  "" ,
            "checkbox_field_attr"   =>  true ,
            "multi_check_field_attr"   =>  "options2_key,options4_key" ,
            "toggle_field_id"   =>  true ,
            "sortable_field_id"   => 'options3_key,options5_key',
            "switch_field_id"   =>  true ,
            "radio_field_attr"   =>  "options3_key" ,
            "radio_buttonset_field_id"   =>  "options3_key" ,
            "radio_image_field_id"   =>  "options3_key" ,
            "color_field_attr"   =>  "transparent" ,
            "style_color"   =>  "transparent" ,
            "style_bg_color"   =>  "transparent" ,
            //"multi_color_field_id"   =>  "" ,
            "image_field_attr"   =>  "" ,
            "image_size_field_attr"   =>  "thumbnail" ,
            "video_field_attr"   =>  "" ,
            "audio_field_attr"   =>  "" ,
            "file_field_attr"   =>  "" ,
            "spinner_field_attr"   =>  0 ,
            "spinner1_with_lock_attr"   =>  0 ,
            "spinner2_with_lock_attr"   =>  0 ,
            "spinner3_with_lock_attr"   =>  0 ,
            "spinner_lock_attr"   =>  true ,
            "range_field_attr"   =>  19 ,
            "icon_field_attr"   =>  "" ,
            "multi_icon_field_attr"   =>  "" ,
            "custom_attr"   =>  "" ,
            "multi_image_field_attr"   =>  "" ,
            "link"          => "" ,
            "link_target"   => "_self" ,
            "length"        => "boxed" ,
            "image_source"  => "attachment" ,
            "image_url"     => '' ,
            "attachment_id" => 0  ,
            "default_image_size"  => "thumbnail" ,
            "custom_image_size"   => "" ,
            "external_image_size" => "" ,
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

        extract( $atts );

        if( $image_field_attr > 0 ){
            if( get_post( $image_field_attr ) )
                $this->set_media( $image_field_attr );
        }

        if( $video_field_attr > 0 ){
            if( get_post( $video_field_attr ) )
                $this->set_media( $video_field_attr );
        }

        if( $audio_field_attr > 0 ){
            if( get_post( $audio_field_attr ) )
                $this->set_media( $audio_field_attr );
        }

        if( $file_field_attr > 0 ){
            if( get_post( $file_field_attr ) )
                $this->set_media( $file_field_attr );
        }

        if( $attachment_id > 0 ){
            if( get_post( $attachment_id ) )
                $this->set_media( $attachment_id );
        }

        $attachments = is_string( $multi_image_field_attr ) ? explode( "," , $multi_image_field_attr ) : $multi_image_field_attr;

        $attachments = is_array( $attachments ) ? $attachments : array();
        
        foreach( $attachments AS $attach_id ){

            if( $attach_id > 0 ){
                if( get_post( $attach_id ) )
                    $this->set_media( $attach_id );
            }
            
        }

        if($length == "boxed")
            $length_class = "sed-row-boxed";
        else
            $length_class = "sed-row-wide";

        $this->set_vars( array(
            "gallery"     => $attachments ,
            "length_class"     => $length_class
        ));
        
    }

    public function get_media_atts(){

        return array(
            'image_field_attr' , 'video_field_attr' , 'audio_field_attr' , 'file_field_attr' , 'attachment_id' , 'multi_image_field_attr'
        );

    }

    function shortcode_settings(){

        $this->add_panel( 'text_box_panel_parent' ,  array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Text Box Settings Panel', 'textdomain'),
            'description'       => __('Text Box Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'text_settings_panel' ,  array(
            'priority'          => 9,
            'type'              => 'default',
            'title'             => __('Text Box Settings', 'textdomain'),
            'description'       => __('Text Box Settings', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "text_box_panel_parent",
            'atts'              => array() ,
        ) );

        /*
        $params['param_name'] = array(
            "type"              => "param_type"  , //field type and control type if field type supported in controls types
            "label"             => "param_label" , //field title(label)
            "description"       => "param_description" ,
            "atts"              => "param_field_attributes" ,
            "dependency"        => "param_dependencies" ,
            "panel"             => "param_panel_id" ,
            "setting_id"        => "param_settings_type" ,
            "js_type"           => "param_js_type" ,
            "js_params"         => "param_control_parameter" ,
            "is_attr"           => "param_is_shortcode_attribute" ,  //only support in module settings. if (param_name === shortcode_attribute ) is_attr = true;
            "attr_name"         => "param_shortcode_attribute_name" , //only support in module settings. should is_attr = true , if (param_name === shortcode_attribute ) attr_name = param_name;
            "category"          => "param_control_category" , // in modules as default control_category is 'module-settings' , values include  'module-settings' || 'style-editor' || other custom categories
            "value"             => "param_value" , //in module settings as default , value equal to shortcode attribute value
            "priority"          => "param_priority" ,
            "custom_template"   => "param_html" , //only support if type === custom
            "choices"           => "param_options" , //types support : select , multi-check , multi-select,
            "groups"            => "param_select_groups" , //only for select and multi-select type
        );
        */
  
        $params = array();

        $params['text_field_attr'] = array(
            "type"          => "text" ,
            "label"         => __("Text Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("Enter Your text", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel",
            'has_border_box'    => false
        );

        $params['tel_field_attr'] = array(
            "type"          => "tel" ,
            "label"         => __("Tel Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("E.g +989190765018", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel",
            'has_border_box'    => false
        );

        $params['pass_field_attr'] = array(
            "type"          => "password" ,
            "label"         => __("Password Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("password", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel",
            'has_border_box'    => false
        );

        $params['search_field_attr'] = array(
            "type"          => "search" ,
            "label"         => __("Search Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("Keyword ...", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel",
            'has_border_box'    => false
        );

        $params['url_field_attr'] = array(
            "type"          => "url" ,
            "label"         => __("Url Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("E.g www.siteeditor.org", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel",
            'has_border_box'    => false
        );

        $params['email_field_attr'] = array(
            "type"          => "email" ,
            "label"         => __("Email Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("E.g info@siteeditor.org", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel",
            'has_border_box'    => false
        );

        $params['date_field_attr'] = array(
            "type"          => "date" ,
            "label"         => __("Date Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            'js_params'     => array(
                //"showAnim"          =>  "bounce" ,
                "showButtonPanel"   =>   true ,
                "changeMonth"       =>   true ,
                "changeYear"        =>   true ,
            ),
            "panel"         => "text_settings_panel",
            'has_border_box'    => false
        );

        $params['dimension_field_attr'] = array(
            "type"          => "dimension" ,
            "label"         => __("Dimension Control", "site-editor"),
            "description"          => __("10px, 10%, 10em,...", "site-editor"),
            "placeholder"   => __("10px, 10%, 10em,...", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel",
            'has_border_box'    => false
        );

        $params['textarea_field_attr'] = array(
            "type"          => "textarea" ,
            "label"         => __("Textarea Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "placeholder"   => __("Enter Your paragraph", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "text_settings_panel",
            'has_border_box'    => false
        );


        $this->add_panel( 'select_settings_panel' , array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Select Settings Panel', 'textdomain'),
            'description'       => __('Select Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) );


        $params['single_select_field_attr'] = array(
            "type"          => "select" ,
            "label"         => __("Single Select Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "choices"       =>  array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "select_settings_panel" ,
        );

        $params['multi_select_field_attr'] = array(
            "type"          => "multi-select" ,
            "label"         => __(" Select Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "choices"       =>  array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "select_settings_panel" ,
        );

        $params['og_single_select_field_attr'] = array(
            "type"          => "select" ,
            "label"         => __("optgroup Single Select Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "optgroup"      => true ,
            "groups"        => array(
                "group1_key"    =>   "group1_title"  ,
                "group2_key"    =>   "group2_title"  ,
            ),
            "choices"       =>  array(
                "group1_key"    =>  array(
                    "options1_key"      =>    "options1_value" ,
                    "options2_key"      =>    "options2_value" ,
                ) ,

                "group2_key"    => array(
                    "options3_key"      =>    "options3_value" ,
                    "options4_key"      =>    "options4_value" ,
                )
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "select_settings_panel" ,
        );

        $params['og_multi_select_field_attr'] = array(
            "type"          => "multi-select" ,
            "label"         => __("optgroup multi Select Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "optgroup"      => true ,
            "groups"        => array(
                "group1_key"    =>   "group1_title"  ,
                "group2_key"    =>   "group2_title"  ,
            ),
            "choices"       =>  array(
                "group1_key"    =>  array(
                    "options1_key"      =>    "options1_value" ,
                    "options2_key"      =>    "options2_value" ,
                ) ,

                "group2_key"    => array(
                    "options3_key"      =>    "options3_value" ,
                    "options4_key"      =>    "options4_value" ,
                )
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "select_settings_panel" ,
        );

        $this->add_panel( 'check_box_panel_parent' ,  array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Check Box Settings Panel', 'textdomain'),
            'description'       => __('Check Box Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'check_box_settings_panel' ,  array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Check Box Settings', 'textdomain'),
            'description'       => __('Check Box Settings', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "check_box_panel_parent",
            'atts'              => array() ,
        ) );


        $params['checkbox_field_attr'] = array(
            "type"          => "checkbox" ,
            "label"         => __("Checkbox Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "check_box_settings_panel" ,
        );

        $params['multi_check_field_attr'] = array(
            "type"          => "multi-check" ,
            "label"         => __("Multi Checkbox Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "choices"       =>  array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "check_box_settings_panel" ,
        );  

        $params['toggle_field_id'] = array(
            'label'             => __('Toggle Control', 'site-editor'),
            'type'              => 'toggle',
            //'priority'          => 28,
            "panel"         => "check_box_settings_panel" ,
        );

        /*$params['sortable_field_id'] = array(
            'label'             => __('Sortable Control', 'site-editor'),
            'type'              => 'sortable',
            'choices'           => array(
                "options1_key"      =>    "One" ,
                "options2_key"      =>    "Two" ,
                "options3_key"      =>    "Three" ,
                "options4_key"      =>    "Four" ,
                "options5_key"      =>    "Five" ,
            ),
            "panel"         => "check_box_settings_panel" ,
        );*/

        $params['switch_field_id'] = array(
            'label'             => __('Switch Control', 'site-editor'),
            'type'              => 'switch',
            //'priority'          => 30,
            'choices'           => array(
                "on"       =>    "ON" ,
                "off"      =>    "OFF" ,
            ),
            "panel"         => "check_box_settings_panel" ,
        );

        $this->add_panel( 'radio_panel_parent' ,  array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Radio Settings Panel', 'textdomain'),
            'description'       => __('Radio Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'radio_settings_panel' ,  array(
            'priority'          => 9,
            'type'              => 'expanded',
            'title'             => __('Radio Settings', 'textdomain'),
            'description'       => __('Radio Settings', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "radio_panel_parent", 
            'atts'              => array() ,
        ) );

        $params['radio_field_attr'] = array(
            "type"          => "radio" ,
            "label"         => __("Radio Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "choices"       =>  array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            'has_border_box'    => false ,
            "panel"         => "radio_settings_panel" ,
        );

        $params['radio_buttonset_field_id'] = array(
            "type"          => "radio-buttonset" ,
            "label"         => __("Radio Buttonset control", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "choices"       =>  array(
                "options1_key"      =>    "One" ,
                "options2_key"      =>    "Two" ,
                "options3_key"      =>    "Three" ,
                "options4_key"      =>    "Four" , 
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            'has_border_box'    => false ,
            "panel"         => "radio_settings_panel" ,
        );

        $params['radio_image_field_id'] = array(
            "type"          => "radio-image" ,
            "label"         => __("Radio Image control", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "choices"       =>  array(
                "options1_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
                "options2_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
                "options3_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
                "options4_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            'has_border_box'    => false ,
            "panel"         => "radio_settings_panel" ,
        );


        $this->add_panel( 'color_settings_panel' ,  array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Color Settings Panel', 'textdomain'),
            'description'       => __('Color Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) );


        $params['color_field_attr'] = array(
            "type"          => "color" ,
            "label"         => __("Color Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "color_settings_panel" ,
        );

        $params['style_color'] = array(
            "type"              => "color" ,
            "label"             => __("Style Editor Color Field", "site-editor"),
            "description"              => "",
            "default"           => "transparent" ,
            'js_params'     =>  array(
                'selector' =>  '.style-color-test' ,
                'style_props'       =>  "color" ,
            ),
            'category'  => "style-editor" ,
            'setting_id'     =>  "font_color",
            "panel"         => "color_settings_panel" ,
        );

        $params['style_bg_color'] = array(
            "type"              => "color" ,
            "label"             => __("background Color", "site-editor"),
            "description"              => "",
            "default"           => "transparent" ,
            'js_params'     =>  array(
                'selector' =>  'sed_current' ,
                'style_props'       =>  "background-color" ,
            ),
            'category'  => "style-editor" ,
            'setting_id'     =>  "background_color",
            "panel"         => "color_settings_panel" ,
        );

        //not support object Attribute for shortcodes
        /*$params['multi_color_field_id'] = array(
            "type"          => "multi-color" ,
            "label"         => __("Multicolor control", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            'choices'     => array(
                'link'    => 'Color',
                'hover'   => 'Hover',
                'active'  => 'Active',
            ),
            'default'     => array(
                'link'    => '#0088cc',
                'hover'   => '#00aaff',
                'active'  => '#00ffff',
            ),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "color_settings_panel" ,
        );*/

        $this->add_panel( 'media_settings_panel' ,  array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Media Settings Panel', 'textdomain'),
            'description'       => __('Media Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) );

        $params['change_image_panel'] = array(
            "priority"          => 9 ,
            "panel_type"        => 'inner_box' ,
            "type"              => "sed_image" ,
            "label"             => __("Select Image Panel", "site-editor"),
            "description"       => __("Image Panel Description", "site-editor"),
            'parent_id'         => "media_settings_panel",
            'controls'          => array(
                'image_source'          =>  'image_source' ,
                'image_url'             =>  'image_url' ,
                'attachment_id'         =>  'attachment_id' ,
                'default_image_size'    =>  'default_image_size' ,
                'custom_image_size'     =>  'custom_image_size' ,
                'external_image_size'   =>  'external_image_size'
            ),
            //in modules default value automatic === default attr value and not need to set
            /*'values'        => array(
                'image_source'          => $atts['image_source'] ,
                'image_url'             => $atts['image_url'] ,
                'attachment_id'         => $atts['attachment_id'] ,
                'default_image_size'    => $atts['default_image_size'] ,
                'custom_image_size'     => $atts['custom_image_size'] ,
                'external_image_size'   => $atts['external_image_size']
            )*/
        );

        $params['image_field_attr'] = array(
            "type"          => "image" ,
            "label"         => __("Single Image Field", "site-editor"),
            "description"   => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            'remove_action' => true ,
            "js_params"     => array(
                "rel_size_control"          => $this->control_prefix . "_image_size_field_attr"
            ),
            "panel"         => "media_settings_panel" ,
        );

        $params['image_size_field_attr'] = array(
            "type"          => "image-size" ,
            "label"         => __("Image Size Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "panel"         => "media_settings_panel" ,
            'dependency' => array(
                'queries'  =>  array(
                    array(
                        "key"       => "image_field_attr" ,
                        "value"     => array( "" , 0 ) ,
                        "compare"   => "NOT IN"
                    )
                )
            )
        );

        $params['multi_image_field_attr'] = array(
            "type"          => "multi-image" ,
            "label"         => __("Select Images Field", "site-editor"),
            "description"          => __("This option allows you to set a icon for your module.", "site-editor"),
            "panel"         => "media_settings_panel" ,
        );

        $params['video_field_attr'] = array(
            'type'              => 'video',
            'label'             => __('Video Field (MP4)', 'site-editor'),
            'description'       => __('the Video OGV Upload option allows you to upload a .OGV format of your video file. .OGV files are optional. You can choose a video with this format from the library by clicking on the button in this section.','site-editor'),
            "js_params"     => array(
                "subtypes"          => array( "mp4" )
            ),
            "panel"         => "media_settings_panel" ,
        );

        $params['audio_field_attr'] = array(
            'type'              => 'audio',
            'label'             => __('Audio Field', 'site-editor'),
            'description'       => __('This feature lets you upload a Mp3 audio; for compatibility it is required to upload files with this format.','site-editor'),
            "js_params"     => array(
                "subtypes"          => array( "mp3" )
            ),
            "panel"         => "media_settings_panel" ,
        );

        $params['file_field_attr'] = array(
            'type'              => 'file',
            'label'             => __('File Field', 'site-editor'),
            'description'       => __('Poll File For Download','site-editor'),
            "selcted_type"      => 'single',
            "js_params"     => array(
                //"subtypes"          => array( "zip" , "rar" , "pdf" ) ,
                "lib_title"         => __( "Media Library" , "site-editor" ),
                "btn_title"         => __( "Select File" , "site-editor" ),
                "support_types"     => array( "archive" , "document" )  //"archive" , "document" , "spreadsheet" , "interactive" , "text" , "audio" , "video" , "image" || "all" ----- only is array
            ),
            "panel"         => "media_settings_panel" ,
        );


        $this->add_panel( 'number_settings_panel' , array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Number Settings Panel', 'textdomain'),
            'description'       => __('Number Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) ); 


        $params['spinner_field_attr'] = array(
            "type"          => "number" ,
            "label"         => __("Spinner Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test" ,
            ),
            "panel"         => "number_settings_panel"
        );

        $params['spinner1_with_lock_attr'] = array(
            "type"          => "number" ,
            "label"         => __("Spinner1 with lock", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            'lock_id'       => 'spinner_lock_attr',
            "panel"         => "number_settings_panel"
        );

        $params['spinner2_with_lock_attr'] = array(
            "type"          => "number" ,
            "label"         => __("Spinner2 with lock", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            'lock_id'       => 'spinner_lock_attr',
            "panel"         => "number_settings_panel"
        );

        $params['spinner3_with_lock_attr'] = array(
            "type"          => "number" ,
            "label"         => __("Spinner3 with lock", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            'lock_id'       => 'spinner_lock_attr',
            "panel"         => "number_settings_panel"
        );

        $params['spinner_lock_attr'] = array(
            "type"          => "lock" ,
            "label"         => __("Spinner Lock Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "panel"         => "number_settings_panel"
        );

        $params['range_field_attr'] = array(
            "type"          => "slider" ,
            "label"         => __("Range Field", "site-editor"),
            "description"          => __("This option allows you to set a title for your image.", "site-editor"),
            "atts"          => array(
                "class"        =>    "custom-class1 custom-class2" ,
                "data-custom"  =>    "test"
            ),
            'js_params'     => array(
                "min"          =>    0 ,
                "max"          =>    100 ,
            ),
            "panel"         => "number_settings_panel",
        );

        $this->add_panel( 'icon_settings_panel' , array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Icon Settings Panel', 'textdomain'),
            'description'       => __('Icon Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) );  

        $params['icon_field_attr'] = array(
            "type"          => "icon" ,
            "label"         => __("Icon Field", "site-editor"),
            "description"          => __("This option allows you to set a icon for your module.", "site-editor"),
            'remove_action'    => true ,
            "panel"         => "icon_settings_panel"
        );
      
        $params['icon_color'] = array(
            "type"              => "color" ,
            "label"             => __("Icon Color Field", "site-editor"),
            "description"              => "",
            "default"           => "#000000" ,
            'js_params'     =>  array(
                'selector'          =>  '.my-icon-single' ,
                'style_props'       =>  "color" ,
            ),
            'category'      => "style-editor" ,
            'setting_id'     =>  "font_color",
            "panel"         => "icon_settings_panel"
        );

        $params['icon_size'] = array(
            "type"              => "number" ,
            "label"             => __("Icon Size Field", "site-editor"),
            "description"              => "",
            "default"           => 16 ,
            'js_params'     =>  array(
                'selector'          =>  '.my-icon-single' ,
                'style_props'       =>  "font-size" ,
            ),
            'category'  => "style-editor" ,
            'setting_id'     =>  "font_size",
            "panel"         => "icon_settings_panel"
        );

        $params['multi_icon_field_attr'] = array(
            "type"          => "multi-icon" ,
            "label"         => __("Select Icons Field", "site-editor"),
            "description"          => __("This option allows you to set a icon for your module.", "site-editor"),
            "panel"         => "icon_settings_panel"
        );


        $params['icon_color_group'] = array(
            "type"              => "color" ,
            "label"             => __("Icon Color Group Field", "site-editor"),
            "description"              => "",
            "default"           => "#000000" ,
            'js_params'     =>  array(
                'selector' =>  '.icon-group-single' ,
                'style_props'       =>  "color" ,
            ),
            'category'  => "style-editor" ,
            'setting_id'     =>  "font_color",
            "panel"         => "icon_settings_panel"
        );

        $params['icon_size_group'] = array(
            "type"              => "number" ,
            "label"             => __("Icon Size Group Field", "site-editor"),
            "description"              => "",
            "default"           => 16 ,
            'js_params'     =>  array(
                'selector'          =>  '.icon-group-single' ,
                'style_props'       =>  "font-size" ,
            ),
            'category'  => "style-editor" ,
            'setting_id'     =>  "font_size",
            "panel"         => "icon_settings_panel"
        );

        /*
         * @Code Editor Panel && Settings
         * In Modules Should Using "sed_shortcode_content" only for code key because the
         * code Should save as a shortcode content and it can not save as attribute
         * So you can using the code field only one time for each module

        $this->add_panel( 'code_editor_settings_panel' , array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Code Settings Panel', 'textdomain'),
            'description'       => __('Code Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) );

        //html_code_field_id
        $params['sed_shortcode_content'] = array(
            'type'              => 'code',
            'label'             => __('Edit HTML Code', 'translation_domain'),
            'priority'          => 10,
            'default'           => "",
            'js_params' => array(
                "mode" => "html",
            ),
            'panel'             =>  'code_editor_settings_panel' ,
        );

        $params['js_code_field_id'] = array(
            'type'              => 'code',
            'label'             => __('Javascript Code', 'translation_domain'),
            'priority'          => 10,
            'default'           => "",
            'js_params' => array(
                "mode" => "javascript",
            ),
            'panel'             =>  'code_editor_settings_panel' ,
        );

        $params['css_code_field_id'] = array(
            'type'              => 'code',
            'label'             => __('Custom Css', 'translation_domain'),
            'priority'          => 10,
            'default'           => "",
            'js_params' => array(
                "mode" => "css",
            ),
            'panel'             =>  'code_editor_settings_panel' ,
        );

        $params['wp_editor_field_id'] = array(
            'label'             => __('Edit Text Block', 'translation_domain'),
            'type'              => 'wp-editor',
            'priority'          => 10,
            'default'           => "",
            //panel or group
            'panel'             =>  'code_editor_settings_panel' ,
        ); */


        $params['animation'] = array(
            "type"          => "animation" ,
            "label"         => __("Animation Settings", "site-editor"),
            'dependency' => array(
                'queries'  =>  array(
                    array(
                        "key"       => "length" ,
                        "value"     => 'boxed' ,
                        "compare"   => "=="
                    )
                )
            )
        );

        $params['skin'] = array(
            "type"          => "skin" ,
            "label"         => __("Change skin Control", "site-editor"),
        );


        /*$params['group_skin'] = array(
            'type'       =>  "group_skin" ,
            'default'    =>  "default",
            'sub_module' =>  "image",
            'group'      =>  "image_thumb",
            'label'      =>  __('Images Change Skin', 'site-editor'),
            'js_params' =>  array(
                "support"  =>  array(
                    "type"     =>  "exclude" ,
                    "fields"   =>  array(
                        "tape-style"

                     )
                )
            ),
        );*/

        $params['row_container'] = array(
            "type"          => "row_container" ,
            "label"         => __("Row Container Settings", "site-editor")
        );


        $params['spacing'] = array(
            "type"          => "spacing" ,
            "label"         => __("Spacing", "site-editor"),
            "default"       => "30 30 30 30"
        );


        $params['align'] = array(
            "type"          => "align" ,
            "label"         => __("Align", "site-editor"),
            "default"       => "center"
        );


        $params['length'] = array(
            "type"          => "length" ,
            "label"         => __("Length", "site-editor"),
            "default"       => "wide"
        );


        $params['link'] = array(
            "type"          => "link" ,
            "label"         => __("Link Panel Settings", "site-editor"),
            "description"   => __("Link Panel Description", "site-editor"),
            "priority"      => 22 ,
            "panel_type"    => 'inner_box' ,
            "controls"      => array(
                "link"          => "link" ,
                "link_target"   => "link_target"
            ),
            //in modules default value automatic === default attr value and not need to set
            /*"values"        => array(
                "link"          => 'E.g www.siteeditor.org' ,
                "link_target"   => '_self'
            ),*/
            "panel_dependency"    => array(
                'controls'  =>  array(
                    "control"  => "length" ,
                    "value"    => "wide" ,
                    "type"     => "include" ,
                    "is_panel" => true
                )
            )
        );


        $this->add_panel( 'custom_settings_panel' , array(
            'priority'          => 9,
            'type'              => 'inner_box',
            'title'             => __('Custom Settings Panel', 'textdomain'),
            'description'       => __('Custom Settings Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'btn_style'         => "black",
            'parent_id'         => "root",
            'atts'              => array() ,
        ) ); 

        //$atts = $this->default_atts();
        
        $dropdown_control = "sed_api_test_custom_attr";
        ob_start();
        ?>
            <div class="dropdown" id="sed-app-control-<?php echo $dropdown_control ;?>">

                  <div class="dropdown-content sed-dropdown content">
                      <div>
                        <ul>
                            <li>
                            <a class="heading-item" href="#"><?php echo __("Custom Control" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class="box-items">
                                <li class="dropdown-item-selector selected-item" data-value="value1" >value1<a  href="#"></a></li>
                                <li class="dropdown-item-selector" data-value="value2" ><a href="#">value2</a></li>
                                <li class="dropdown-item-selector" data-value="value3" ><a  href="#">value3</a></li>
                                <li class="dropdown-item-selector" data-value="value4" ><a  href="#">value4</a></li>
                                <li class="dropdown-item-selector" data-value="value5" ><a  href="#">value5</a></li>
                                <li class="dropdown-item-selector" data-value="value6" ><a  href="#">value6</a></li>
                                <li class="dropdown-item-selector" data-value="value7" ><a  href="#">value7</a></li>
                                <li class="dropdown-item-selector" data-value="value8" ><a  href="#">value8</a></li>
                                <li class="dropdown-item-selector" data-value="value9" ><a  href="#">value9</a></li>
                                <li class="dropdown-item-selector" data-value="value10" ><a  href="#">value10</a></li>
                                <li class="clr"></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                  </div>

          </div>
        <?php
        $dropdown_html = ob_get_contents();
        ob_end_clean();

        $params['custom_attr'] = array(
            'type'              =>  'custom',
            'js_type'           =>  'dropdown',
            'has_border_box'    =>   true ,
            'custom_template'   =>  $dropdown_html ,
            'js_params'     =>  array(
                'options_selector'    => '.dropdown-item-selector',
                'selected_class'      => 'selected-item'
            ),
            'panel'             => 'custom_settings_panel'
        );

        $this->add_panel( 'design_editor_panel' , array(
            'priority'          => 100000,
            'type'              => 'inner_box',
            'title'             => __('Design Editor Panel', 'textdomain'),
            'description'       => __('Design Editor Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "root",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'design_editor_inner_panel' , array(
            'priority'          => 10,
            'type'              => 'default',
            'title'             => __('Design Editor Panel', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "design_editor_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'background_design_panel' , array(
            'priority'          => 10,
            'type'              => 'inner_box',
            'title'             => __('Background', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'border_design_panel' , array(
            'priority'          => 11,
            'type'              => 'inner_box',
            'title'             => __('Border', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'border_style_design_panel' , array(
            'priority'          => 11,
            'type'              => 'inner_box',
            'title'             => __('Border Style', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "border_design_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'border_color_design_panel' , array(
            'priority'          => 12,
            'type'              => 'inner_box',
            'title'             => __('Border Color', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "border_design_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'border_width_design_panel' , array(
            'priority'          => 13,
            'type'              => 'inner_box',
            'title'             => __('Border Width', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "border_design_panel",
            'atts'              => array() , 
        ) );

        $this->add_panel( 'border_radius_design_panel' , array(
            'priority'          => 12,
            'type'              => 'inner_box',
            'title'             => __('Border Radius', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'padding_design_panel' , array(
            'priority'          => 13,
            'type'              => 'inner_box',
            'title'             => __('Padding', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'margin_design_panel' , array(
            'priority'          => 14,
            'type'              => 'inner_box',
            'title'             => __('Margin', 'textdomain'),
            'capability'        => 'edit_theme_options' ,
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'line_height_design_panel' , array(
            'priority'          => 15,
            'type'              => 'inner_box',
            'title'             => __('Line Height', 'textdomain'),
            'capability'        => 'edit_theme_options' , 
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'trancparency_design_panel' , array(
            'priority'          => 16,
            'type'              => 'inner_box',
            'title'             => __('Trancparency', 'textdomain'),
            'capability'        => 'edit_theme_options' , 
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'position_design_panel' , array(
            'priority'          => 17,
            'type'              => 'inner_box',
            'title'             => __('Positio', 'textdomain'),
            'capability'        => 'edit_theme_options' , 
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'text_align_design_panel' , array(
            'priority'          => 18,
            'type'              => 'inner_box',
            'title'             => __('Text Align', 'textdomain'),
            'capability'        => 'edit_theme_options' , 
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'font_design_panel' , array(
            'priority'          => 19,
            'type'              => 'inner_box',
            'title'             => __('Font', 'textdomain'),
            'capability'        => 'edit_theme_options' , 
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'text_shadow_design_panel' , array(
            'priority'          => 20,
            'type'              => 'inner_box',
            'title'             => __('Text Shadow', 'textdomain'),
            'capability'        => 'edit_theme_options' , 
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'gradient_design_panel' , array(
            'priority'          => 21,
            'type'              => 'inner_box',
            'title'             => __('Gradient', 'textdomain'),
            'capability'        => 'edit_theme_options' , 
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $this->add_panel( 'shadow_design_panel' , array(
            'priority'          => 22,
            'type'              => 'inner_box',
            'title'             => __('Box Shadow', 'textdomain'),
            'capability'        => 'edit_theme_options' , 
            'parent_id'         => "design_editor_inner_panel",
            'atts'              => array() ,
        ) );

        $params['background_color'] = array(
            "type"              => "background-color" ,
            "label"             => __("Background Color", "site-editor"),
            "description"       => __("Add Background Color For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '#F6F6F6' ,
            'panel'             => 'background_design_panel'
        );

        $params['background_image'] = array(
            "type"              => "background-image" , 
            "label"             => __("Background Image", "site-editor"),
            "description"       => __("Add Background Image For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "remove_action"     => true , 
            "default"           => '' ,
            'panel'             => 'background_design_panel'
        );        

        $params['external_background_image'] = array(
            "type"              => "external-background-image" , 
            "label"             => __("External Background Image", "site-editor"),
            "description"       => __("Add External Background Image For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' ,
            'panel'             => 'background_design_panel'
        );    

        $params['parallax_background_image'] = array( 
            "type"              => "parallax-background-image" , 
            "label"             => __("Parallax Background Image", "site-editor"),
            "description"       => __("Add Parallax Background Image For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' ,
            'panel'             => 'background_design_panel'
        );   

        $params['parallax_background_ratio'] = array( 
            "type"              => "parallax-background-ratio" , 
            "label"             => __("Parallax Background Ratio", "site-editor"),
            "description"       => __("Add Parallax Background Ratio For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' , 
            "js_params"     =>  array(
                "step"          => 0.01 ,
                "min"           => 0 ,
                "max"           => 1 , 
            ),
            "default"           => 0.5 , 
            'panel'             => 'background_design_panel'
        ); 

        $params['background_attachment'] = array( 
            "type"              => "background-attachment" , 
            "label"             => __("Background Attachment", "site-editor"),
            "description"       => __("Add Background Attachment For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => 'scroll' , 
            'panel'             => 'background_design_panel'
        ); 

        $params['background_size'] = array( 
            "type"              => "background-size" , 
            "label"             => __("Background Size", "site-editor"),
            "description"       => __("Add Background Size For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' , 
            'panel'             => 'background_design_panel'
        ); 

        $params['background_repeat'] = array( 
            "type"              => "background-repeat" , 
            "label"             => __("Background Repeat", "site-editor"),
            "description"       => __("Add Background Repeat For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' , 
            'panel'             => 'background_design_panel'
        ); 

        $params['background_position'] = array(
            "type"              => "background-position" , 
            "label"             => __("Background Position", "site-editor"),
            "description"       => __("Add Background Position For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'has_border_box'    =>   true ,
            "default"           => '' ,
            'panel'             => 'background_design_panel'  
        );
 

        $params['border_top_style'] = array( 
            "type"              => "border-style" ,
            "label"             => __('Border Top Style', 'site-editor'),
            "description"       => __("Module Border Top Style", "site-editor"), 
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'top',
            'has_border_box'    =>   false ,
            "default"           => '' ,
            'panel'             => 'border_style_design_panel'
        ); 

        $params['border_right_style'] = array( 
            "type"              => "border-style" ,
            "label"             => __('Border Right Style', 'site-editor'),
            "description"       => __("Module Border Right Style", "site-editor"),  
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'right',
            'has_border_box'    =>   false ,
            "default"           => '' ,
            'panel'             => 'border_style_design_panel'
        );   


        $params['border_bottom_style'] = array( 
            "type"              => "border-style" ,
            "label"             => __('Border Bottom Style', 'site-editor'),
            "description"       => __("Module Border Bottom Style", "site-editor"),  
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'bottom',
            'has_border_box'    =>   false ,
            "default"           => '' ,
            'panel'             => 'border_style_design_panel'
        );   


        $params['border_left_style'] = array( 
            "type"              => "border-style" ,
            "label"             => __('Border Left Style', 'site-editor'),
            "description"       => __("Module Border Left Style", "site-editor"),  
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'left',
            'has_border_box'    =>   false ,
            "default"           => '' ,
            'panel'             => 'border_style_design_panel'
        );  
 

        $params['border_top_width'] = array( 
            "type"              => "border-width" ,
            "label"             => __('Border Top Width', 'site-editor'),
            "description"       => __("Module Border Top Width", "site-editor"), 
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'top',
            "default"           => '' ,
            'panel'             => 'border_width_design_panel'
        ); 

        $params['border_right_width'] = array( 
            "type"              => "border-width" ,
            "label"             => __('Border Right Width', 'site-editor'),
            "description"       => __("Module Border Right Width", "site-editor"),  
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'right',
            "default"           => '' ,
            'panel'             => 'border_width_design_panel'
        );   


        $params['border_bottom_width'] = array( 
            "type"              => "border-width" ,
            "label"             => __('Border Bottom Width', 'site-editor'),
            "description"       => __("Module Border Bottom Width", "site-editor"),  
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'bottom',
            "default"           => '' ,
            'panel'             => 'border_width_design_panel'
        );   


        $params['border_left_width'] = array( 
            "type"              => "border-width" ,
            "label"             => __('Border Left Width', 'site-editor'),
            "description"       => __("Module Border Left Width", "site-editor"),  
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'left',
            "default"           => '' ,
            'panel'             => 'border_width_design_panel'
        );   


        $params['border_top_color'] = array( 
            "type"              => "border-color" , 
            "label"             => __('Border Top Color', 'site-editor'),
            "description"       => __("Module Border Top Color", "site-editor"), 
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'top',
            "default"           => '' ,
            'panel'             => 'border_color_design_panel'
        ); 

        $params['border_right_color'] = array( 
            "type"              => "border-color" ,
            "label"             => __('Border Right Color', 'site-editor'),
            "description"       => __("Module Border Right Color", "site-editor"),  
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'right',
            "default"           => '' ,
            'panel'             => 'border_color_design_panel'
        );   


        $params['border_bottom_color'] = array( 
            "type"              => "border-color" ,
            "label"             => __('Border Bottom Color', 'site-editor'),
            "description"       => __("Module Border Bottom Color", "site-editor"),  
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'bottom',
            "default"           => '' ,
            'panel'             => 'border_color_design_panel'
        );    


        $params['border_left_color'] = array( 
            "type"              => "border-color" ,
            "label"             => __('Border Left Color', 'site-editor'),
            "description"       => __("Module Border Left Color", "site-editor"),  
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'left',
            "default"           => '' ,
            'panel'             => 'border_color_design_panel'
        );     

        $params['border_radius_tl'] = array( 
            "type"              => "border-radius" , 
            "label"             =>  ( is_rtl() ) ? __('Top right corner', 'site-editor') : __('Top left corner', 'site-editor') ,
            "description"       => __("Add corner For Element", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'tl',
            'lock_id'           => 'border_radius_lock',
            "default"           => '' , 
            'panel'             => 'border_radius_design_panel' 
        ); 

        $params['border_radius_tr'] = array( 
            "type"              => "border-radius" , 
            "label"             =>  ( is_rtl() ) ? __('Top left corner', 'site-editor') : __('Top right corner', 'site-editor') ,
            "description"       => __("Add corner For Element", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'tr',
            'lock_id'           => 'border_radius_lock',
            "default"           => '' , 
            'panel'             => 'border_radius_design_panel'
        ); 

        $params['border_radius_br'] = array( 
            "type"              => "border-radius" , 
            "label"             =>  ( is_rtl() ) ? __('Bottom left corner', 'site-editor') : __('Bottom right corner', 'site-editor') ,
            "description"       => __("Add corner For Element", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'br',
            'lock_id'           => 'border_radius_lock',
            "default"           => '' , 
            'panel'             => 'border_radius_design_panel'
        ); 

        $params['border_radius_bl'] = array( 
            "type"              => "border-radius" , 
            "label"             =>  ( is_rtl() ) ? __('Bottom right corner', 'site-editor') : __('Bottom left corner', 'site-editor') ,
            "description"       => __("Add corner For Element", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'bl',
            'lock_id'           => 'border_radius_lock',
            "default"           => '' , 
            'panel'             => 'border_radius_design_panel'
        ); 

        $params['border_radius_lock'] = array( 
            "type"              => "property-lock" , 
            "label"             => __('lock Corners Together', 'site-editor'), 
            "description"       => __("Add corner For Element", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'setting_id'        => 'border_radius_lock' , 
            'default'           => true ,
            'panel'             => 'border_radius_design_panel'
        );   

        $params['padding_top'] = array( 
            "type"              => "padding" , 
            "label"             => __('Padding Top', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'top',
            'lock_id'           => 'padding_lock',     
            "default"             => '' , 
            'panel'             => 'padding_design_panel'
        ); 

        $params['padding_right'] = array( 
            "type"              => "padding" , 
            "label"             => ( is_rtl() ) ? __('Padding Left', 'site-editor') : __('Padding Right', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'right',
            'lock_id'           => 'padding_lock',  
            "default"             => '' , 
            'panel'             => 'padding_design_panel'
        );

        $params['padding_bottom'] = array( 
            "type"              => "padding" , 
            "label"             => __('Padding Bottom', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'bottom',
            'lock_id'           => 'padding_lock',      
            "default"             => '' , 
            'panel'             => 'padding_design_panel'
        );

        $params['padding_left'] = array( 
            "type"              => "padding" , 
            "label"             => ( is_rtl() ) ? __('Padding Right', 'site-editor') : __('Padding Left', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'left',
            'lock_id'           => 'padding_lock',     
            "default"             => '' , 
            'panel'             => 'padding_design_panel'
        );

        $params['padding_lock'] = array( 
            "type"              => "property-lock" , 
            "label"             => __('lock Spacings Together', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'setting_id'        => 'padding_lock',
            'default'           => true , 
            'panel'             => 'padding_design_panel'
        );

        $params['margin_top'] = array( 
            "type"              => "margin" , 
            "label"             => __('Margin Top', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'top',
            'lock_id'           => 'margin_lock',    
            "default"             => '' , 
            'panel'             => 'margin_design_panel'
        ); 

        $params['margin_right'] = array( 
            "type"              => "margin" , 
            "label"             => ( is_rtl() ) ? __('Margin Left', 'site-editor') : __('Margin Right', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'right',
            'lock_id'           => 'margin_lock',     
            "default"             => '' , 
            'panel'             => 'margin_design_panel'
        );

        $params['margin_bottom'] = array( 
            "type"              => "margin" , 
            "label"             => __('Margin Bottom', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'bottom',
            'lock_id'           => 'margin_lock',      
            "default"             => '' , 
            'panel'             => 'margin_design_panel'
        );

        $params['margin_left'] = array( 
            "type"              => "margin" , 
            "label"             => ( is_rtl() ) ? __('Margin Right', 'site-editor') : __('Margin Left', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'prop_side'         => 'left',
            'lock_id'           => 'margin_lock',    
            "default"             => '' , 
            'panel'             => 'margin_design_panel'
        );

        $params['margin_lock'] = array(  
            "type"              => "property-lock" ,
            "label"             => __('lock Spacings Together', 'site-editor'),
            "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'setting_id'        => 'margin_lock',
            'default'           => true ,   
            'panel'             => 'margin_design_panel'
        );


        $params['line_height'] = array(
            "type"              => "line-height" , 
            "label"             => __("line height", "site-editor"),
            "description"       => __("Add Line Height For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' ,
            'panel'             => 'line_height_design_panel'
        );


        $params['trancparency'] = array(  
            "type"              => "trancparency" , 
            "label"             => __("Trancparency", "site-editor"),
            "description"       => __("Add Trancparency For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"             => '' ,
            'panel'             => 'trancparency_design_panel'
        );


        $params['position'] = array(
            "type"              => "position" , 
            "label"             => __("Position", "site-editor"),
            "description"       => __("Add Position For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "choices" =>array(
                'relative'     => __('relative', 'site-editor'),
                'absolute'     => __('absolute ', 'site-editor'),
                'fixed'     => __('fixed', 'site-editor'),
                'static'     => __('static ', 'site-editor') 
            ), 
            "default"             => '' ,
            'panel'             => 'position_design_panel'
        );


        $params['text_align'] = array(
            "type"              => "text-align" , 
            "label"             => __("Text Align", "site-editor"),
            "description"       => __("Add Text Align For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'choices' =>array(
                'left'      => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
                'center'    => __('Center', 'site-editor'),
                'right'     => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                'justify'   => __('justify', 'site-editor'),
            ), 
            "default"             => '' ,
            'panel'             => 'text_align_design_panel'
        );

        $this->controls['font'] = array();
       
        $params['font_family'] = array(
            "type"              => "font-family" ,
            "label"             => __('Font Family', 'site-editor'),  
            "description"       => __("Add Font Family For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            /*"optgroup"          => true ,
            "groups"            => array(
                "custom_fonts"     => __("Custom Fonts" , "site-editor") ,
                "standard_fonts"   => __("Standard Fonts" , "site-editor") ,
                "google_fonts"     => __("Google Fonts" , "site-editor") ,
            ),*/
            "default"           => '' ,
            'panel'             => 'font_design_panel'
        );        

        $params['font_size'] = array(
            "type"              => "font-size" , 
            "label"             => __("Font Size", "site-editor"),
            "description"       => __("Add Font Size For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' ,
            'panel'             => 'font_design_panel'
        );

        $params['font_color'] = array(
            "type"              => "font-color" , 
            "label"             => __("Font Color", "site-editor"),
            "description"       => __("Add Font Color For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' ,
            'panel'             => 'font_design_panel'
        );


        $params['font_weight'] = array(
            "type"              => "font-weight" , 
            "label"             => __("Font Weight", "site-editor"),
            "description"       => __("Add Font Weight For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' ,
            'panel'             => 'font_design_panel'
        );


        $params['font_style'] = array(
            "type"              => "font-style" ,
            "label"             => __('Font Style', 'site-editor'),  
            "description"       => __("Add Font Style For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' ,
            'panel'             => 'font_design_panel'
        );


        $params['text_decoration'] = array(
            "type"              => "text-decoration" , 
            "label"             => __("Text Decoration", "site-editor"),
            "description"       => __("Add Text Decoration For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' ,
            'panel'             => 'font_design_panel'
        );  

        $params['text_shadow_color'] = array(
            "type"              => "text-shadow-color" , 
            "label"             => __("Text Shadow Color", "site-editor"),
            "description"       => __("Add Text Shadow Color For Element", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,  
            "default"           => '' ,
            'panel'             => 'text_shadow_design_panel'
        );

        $params['text_shadow'] = array(
            "type"              => "text-shadow" , 
            "label"             => __("Text Shadow", "site-editor"),
            "description"       => __("Add Text Shadow For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'has_border_box'    =>   true ,
            "default"           => '' ,
            'panel'             => 'text_shadow_design_panel'
        );

        $params['gradient'] = array(
            "type"              => "gradient" , 
            "label"             => __("Gradient", "site-editor"),
            "description"       => __("Add Gradient For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            'has_border_box'    =>   true ,
            "default"           => '' ,
            'panel'             => 'gradient_design_panel'
        );

        $params['shadow_color'] = array(
            "type"              => "shadow-color" , 
            "label"             => __("Text Shadow Color", "site-editor"),
            "description"       => __("Add Shadow Color For Element", "site-editor"),
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,  
            "default"           => '' ,
            'panel'             => 'shadow_design_panel'
        );

        $params['shadow'] = array(
            "type"              => "shadow" , 
            "label"             => __("Shadow", "site-editor"),
            "description"       => __("Add Shadow For Element", "site-editor") ,
            "category"          => 'style-editor' ,
            "selector"          => 'sed_current' ,
            "default"           => '' ,
            'panel'             => 'shadow_design_panel' 
        );


        return $params;
    }

    /*
    * @New dependency (relations) Api
      $params['animation'] = array(
        "type"          => "animation" ,
        "label"         => __("Animation Settings", "site-editor"),
        "dependency"    => array(
            'controls'  =>  array(
                "control"  => "length" ,
                "value"    => "boxed" ,
                "type"     => "include"
            ),
        )
      );
      "animation"  =>  array(
          "type"          => "animation" ,
          "label"         => __("Animation Settings", "site-editor"),
          "dependency"    => array(
              'controls'  =>  array(
                  "control"  => "length" ,
                  "value"    => "boxed" ,
                  "type"     => "include"
              ),
          )
      ),

    */
    function custom_style_settings(){
        return array(

            array(
            'h3' , 'h3' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' , 'position' , 'trancparency' ) , __("H3" , "site-editor") ) ,
            array(
            'h4' , 'h4' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' , 'position' , 'trancparency' ) , __("H4" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $api_test_menu = $context_menu->create_menu("api-test-module" , __("API Test Module","site-editor") , 'api-test-module' , 'class' , 'element' , '' , "sed_api_test" , array() );
    }
}

new PBAPITestModule();
global $sed_pb_app;

/**
* Register module with siteeditor.
*/
$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "api-test-module",
    "title"       => __("API Test Module","site-editor"),
    "description" => __("Add Full Customize API Test Module","site-editor"),
    "icon"        => "sedico-api-test-module",
    "type_icon"   => "font",
    "shortcode"   => "sed_api_test",
    "tpl_type"    => "underscore"
    //"sub_modules"   => array('title', 'paragraph', 'image', 'icons' , 'separator'),
    //"js_module"   => array( 'sed_api_test _module_script', 'api-test-module /js/sed-api-test-module.min.js', array('sed-frontend-editor') )
));
