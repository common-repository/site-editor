<?php
/**
 * Module Name: Background
 * Module URI: http://www.siteeditor.org/design-editor/background
 * Description: Background Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorBackground
 */
final class SedDesignEditorBackground{

    /**
     * Capability required to access background fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Background fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'background';

    /**
     * This group title
     *
     * @access public
     * @var array
     */
    public $title = '';

    /**
     * this group description
     *
     * @access public
     * @var array
     */
    public $description = '';

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_background';

    /**
     * SedDesignEditorBackground constructor.
     */
    public function __construct(){

        $this->title = __("Background" , "site-editor");

        $this->description = __("Add background To each dom element" , "site-editor");

        if( is_site_editor() ){

            add_action( "sed_app_register"          , array( $this , 'register_group' ) , -9999 );

            add_action( "sed_app_register"          , array( $this , 'register_options' ) );

        }

        add_action( "sed_after_init_manager"    , array( $this , 'register_components' ) , 100 , 1 );

    }

    /**
     * Register Controls && Fields
     *
     * @access public
     * @since 1.0.0
     */
    public function register_components(){

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-color-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-image-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-image-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-external-background-image-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-external-background-image-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-parallax-background-image-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-parallax-background-image-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-parallax-background-ratio-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-parallax-background-ratio-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-attachment-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-attachment-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-size-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-size-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-repeat-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-repeat-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-position-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-position-field.class.php'; 

    }

    /**
     * Register Default Background Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_group(){

        SED()->editor->manager->add_group( $this->option_group , array(
            'capability'        => $this->capability,
            'theme_supports'    => '',
            'title'             => $this->title ,
            'description'       => $this->description ,
            'type'              => 'default',
        ));

    }

    /**
     * Register Options For Background Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'background_color' => array(
                "type"              => "background-color" ,
                "label"             => __("Background Color", "site-editor"),
                "description"       => __("Add Background Color For Element", "site-editor"),
                'default_value'     => 'transparent' ,
            ),

            'background_image' => array(
                "type"              => "background-image" ,  
                "label"             => __("Background Image", "site-editor"),
                "description"       => __("Add Background Image For Element", "site-editor"),
                'default_value'     => '' ,
                "remove_action"     => true ,
            ),

            'external_background_image' => array(
                "type"              => "external-background-image" ,  
                "label"             => __("External Background Image", "site-editor"),
                "description"       => __("Add External Background Image For Element", "site-editor"),
                'default_value'     => '' ,
                "dependency"    => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "IN"
                        )
                    ),
                )
            ),

            'parallax_background_image' => array(
                "type"              => "parallax-background-image" ,  
                "label"             => __("Parallax Background Image", "site-editor"),
                "description"       => __("Add Parallax Background Image For Element", "site-editor"),
                'default_value'     => false ,
                "dependency"    => array(
                    'queries'  =>  array(
                        "relation"     =>  "OR" ,
                        array(
                            "key"       => "background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        ),
                        array(
                            "key"       => "external_background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        )
                    ),
                )
            ),

            'parallax_background_ratio' => array(
                "type"              => "parallax-background-ratio" ,  
                "label"             => __("Parallax Background Ratio", "site-editor"),
                "description"       => __("Add Parallax Background Ratio For Element", "site-editor"),
                'js_params'         => array(
                    'step'          => 0.01 ,
                    "min"           => 0 ,
                    "max"           => 1 ,
                ),
                'default_value'     => 0.5 ,
                "dependency"    => array(
                    'queries'  =>  array(
                        "relation"     =>  "AND" ,
                        array(
                            "key"       => "parallax_background_image" ,
                            "value"     => true ,
                            "compare"   => "==="
                        ),
                        array(
                            "relation"     =>  "OR" ,
                            array(
                                "key"       => "background_image" ,
                                "value"     => array( 0 , '' , 'none' , '0' ) ,
                                "compare"   => "NOT IN"
                            ),
                            array(
                                "key"       => "external_background_image" ,
                                "value"     => array( 0 , '' , 'none' , '0' ) ,
                                "compare"   => "NOT IN"
                            )
                        )
                    ),
                )
            ),

            'background_attachment' => array(
                "type"              => "background-attachment" ,  
                "label"             => __("Background Attachment", "site-editor"),
                "description"       => __("Add Background Attachment For Element", "site-editor"),
                'default_value'     => 'scroll' ,
                "dependency"    => array(
                    'queries'  =>  array(
                        "relation"     =>  "OR" ,
                        array(
                            "key"       => "background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        ),
                        array(
                            "key"       => "external_background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        )
                    ),
                )
            ), 

            'background_size' => array(
                "type"              => "background-size" ,  
                "label"             => __("Background Size", "site-editor"),
                "description"       => __("Add Background Size For Element", "site-editor"),
                'default_value'     => 'inherit' ,
                "dependency"    => array(
                    'queries'  =>  array(
                        "relation"     =>  "OR" ,
                        array(
                            "key"       => "background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        ),
                        array(
                            "key"       => "external_background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        )
                    ),
                )
            ),

            'background_repeat' => array(
                "type"              => "background-repeat" ,  
                "label"             => __("Background Repeat", "site-editor"),
                "description"       => __("Add Background Repeat For Element", "site-editor"),
                'default_value'     => 'inherit' ,
                "dependency"    => array(
                    'queries'  =>  array(
                        "relation"     =>  "OR" ,
                        array(
                            "key"       => "background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        ),
                        array(
                            "key"       => "external_background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        )
                    ),
                )
            ), 

            'background_position' => array(
                "type"              => "background-position" ,
                "label"             => __('Background Position', 'site-editor'),
                "description"       => __("Background Position", "site-editor"),
                'default_value'     => 'left top' ,
                'has_border_box'    =>   false ,
                "dependency"    => array(
                    'queries'  =>  array(
                        "relation"     =>  "OR" ,
                        array(
                            "key"       => "background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        ),
                        array(
                            "key"       => "external_background_image" ,
                            "value"     => array( 0 , '' , 'none' , '0' ) ,
                            "compare"   => "NOT IN"
                        )
                    ),
                )
            ), 

        );

        $fields = apply_filters( 'sed_background_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_background_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorBackground();
