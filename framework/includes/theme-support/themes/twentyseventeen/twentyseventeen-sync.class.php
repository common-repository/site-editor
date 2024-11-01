<?php
/**
 * Twenty seventeen Theme Sync class
 *
 * @package SiteEditor
 * @subpackage framework
 * @since 1.0.0
 */

/**
 * SiteEditor Twenty seventeen Theme Sync class.
 *
 * Sync Twenty seventeen WordPress theme with SiteEditor Framework
 *
 * @since 1.0.0
 */

class SiteEditorTwentyseventeenThemeSync{

    /**
     * @access protected
     * @var object instance of SiteEditorThemeSupport Class
     */
    protected $theme_support;

    /**
     * Theme General Dynamic Css Options
     *
     * @var string
     * @access public
     */
    public $dynamic_css_options = array();

    /**
     * is added dynamic css options ?
     *
     * @var string
     * @access public
     */
    public $is_added_dynamic_css_options = false;

    /**
     * instance of TwentyseventeenHeaderDesignOptions
     *
     * @var string
     * @access public
     */
    public $header_design_options;

    /**
     * instance of TwentyseventeenFooterDesignOptions
     *
     * @var string
     * @access public
     */
    public $footer_design_options;

    /**
     * SiteEditorTwentyseventeenThemeSync constructor.
     * @param $theme_support object instance of SiteEditorThemeSupport Class
     */
    public function __construct( $theme_support ) {

        $this->theme_support = $theme_support;
        
        add_action( "plugins_loaded" , array( $this , 'add_features' ) , 9000  );

        add_action( "plugins_loaded" , array( $this , 'add_components' ) , 9000  );

        add_action( "sed_static_module_register" , array( $this , 'register_static_modules' ) , 10 , 1 );

        add_filter( "sed_theme_options_panels_filter" , array( $this , 'register_theme_panels' ) , 100 );

        add_filter( "sed_theme_options_fields_filter" , array( $this , 'register_theme_fields' ) );

        add_action( 'sed_enqueue_scripts' , array( $this, 'add_js_plugin' ) );

        add_action( 'wp_enqueue_scripts' , array( $this, 'add_js_module' ) , 100 );

        add_filter( "sed_twentyseventeen_css_vars" , array( $this , "get_dynamic_css_vars" ) , 10 , 1 );

        //add_filter( 'template_include', array(&$this,'template_chooser') , 99 );

        //add_filter( 'sed_header_wrapping_template', array( $this , 'get_header' ) , 100 , 1 ); //locate_template , load_template , get_template_part

        //add_filter( 'sed_base_template_wrapping' , array( $this , 'sed_base_template_wrapping' ) , 100 , 2 );

        //add_filter( 'sed_color_schemes' , array( $this , 'color_schemes' ) );

        add_action( 'wp' , array( $this, 'remove_page_builder_settings' ) );

    }

    public function add_components(){

        require_once dirname( __FILE__ ) . '/twentyseventeen-dynamic-css.class.php';

        new SiteEditorTwentyseventeenDynamicCss();
        
        require_once dirname( __FILE__ ) . '/twentyseventeen-typography.class.php';
        
        new SiteEditorTwentyseventeenTypography();

        require_once dirname( __FILE__ ) . '/modules/header-design-options.php';

        $this->header_design_options = new TwentyseventeenHeaderDesignOptions();

        require_once dirname( __FILE__ ) . '/modules/footer-design-options.php';

        $this->footer_design_options = new TwentyseventeenFooterDesignOptions();

    }

    public function color_scheme_js_settings( $settings , $color_scheme ){

        $settings['type'] = get_theme_mod( 'sed_color_scheme_type' , 'skin' );

        $settings['currentSkin'] = get_theme_mod( 'sed_color_scheme_skin' , 'default' );

        $settings['currents'] = array();

        $settings['defaults'] = array();

        foreach ( $color_scheme->get_customize_color_settings() AS $field_id => $option ){

            if( ! isset( $option['setting_id'] ) )
                continue;

            $default = isset( $option['default'] ) ? $option['default'] : '';

            $settings['defaults'][$field_id] = $default;

            $settings['currents'][$field_id] = get_theme_mod( $option['setting_id'] , $default );

        }

        return $settings;

    }

    /**
     * Add Js for site editor
     */
    public function add_js_plugin(){

        wp_register_script("sed-twentyseventeen-plugin", SED_FRAMEWORK_URL . '/includes/theme-support/themes/twentyseventeen/assets/js/twentyseventeen-plugin.js' , array( 'siteeditor' ) , "1.0.0",1 );

        wp_enqueue_script( 'sed-twentyseventeen-plugin' );

    }

    /**
     * Add Js for site editor front end
     */
    public function add_js_module(){

        wp_enqueue_script( 'sed-twentyseventeen-module', SED_FRAMEWORK_URL . '/includes/theme-support/themes/twentyseventeen/assets/js/twentyseventeen-module.js', array( 'sed-frontend-editor' ) ,"1.0.0" , 1);

    }

    /**
     * Register Site Default Panels
     */
    public function register_theme_panels( $panels )
    {

        $panels['front_page_settings'] = array(
            'title'                 =>  __('Front Page Options',"site-editor")  , 
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 30 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-current-post-customize' ,
            'field_spacing'         => 'sm',
            'dependency'    => array(
                'queries'          =>  array(
                    array(
                        'key'       => 'is_front_page' ,
                        'type'      => 'page_condition'
                    ) ,
                )
            )
        );

        $panels['blog_settings'] = array(
            'title'                 =>  __('Blog Options',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 30 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-current-post-customize' ,
            'field_spacing'         => 'sm'
        );

        $panels['blog_single_posts_settings'] = array(
            'title'                 =>  __('Single Post Settings',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 30 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'parent_id'             => 'blog_settings',
            'icon'                  => 'sedico-current-post-customize' ,
            'field_spacing'         => 'sm'
        );

        $panels['blog_archive_settings'] = array(
            'title'                 =>  __('Archive Settings',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 20 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'parent_id'             => 'blog_settings',
            'icon'                  => 'sedico-current-post-customize' ,
            'field_spacing'         => 'sm'
        );

        $panels['pages_settings'] = array(
            'title'                 =>  __('Pages Options',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 30 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-current-post-customize' ,
            'field_spacing'         => 'sm' ,
            'dependency'    => array(
                'queries'          =>  array(
                    array(
                        'key'       => 'is_page' ,
                        'type'      => 'page_condition'
                    ) ,
                )
            )
        ); 

        $panels['404_page_settings'] = array(
            'title'                 =>  __('404 Page Options',"site-editor")  , 
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 30 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-current-post-customize' ,
            'field_spacing'         => 'sm',
            'dependency'    => array(
                'queries'          =>  array(
                    array(
                        'key'       => 'is_404' ,
                        'type'      => 'page_condition'
                    ) ,
                )
            )
        );

        $panels['search_results_page_settings'] = array(
            'title'                 =>  __('Search Results Page Options',"site-editor")  , 
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 30 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-current-post-customize' ,
            'field_spacing'         => 'sm',
            'dependency'    => array(
                'queries'          =>  array(
                    array(
                        'key'       => 'is_search' ,
                        'type'      => 'page_condition'
                    ) ,
                )
            )
        );

        $panels['theme_general_styling'] =  array( 
            'type'              => 'inner_box',
            'title'             => __('Theme General Styling', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "root" ,
            'priority'          => 70 ,
        );

        $panels['general_custom_styling'] =  array(
            'type'              => 'inner_box',
            'title'             => __('General Custom Edit Style', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "theme_general_styling" ,
            'priority'          => 10 ,
        );

        $panels['forms_custom_styling_outer'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Forms Custom Edit Style', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "theme_general_styling" ,
            'priority'          => 10 ,
        );

        $panels['forms_custom_styling'] =  array(
            'type'              => 'default',
            'title'             => __('Forms Custom Edit Style', 'site-editor'),
            'parent_id'         => "forms_custom_styling_outer" ,
            'priority'          => 10 ,
        );

        $panels['buttons_custom_styling_outer'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Buttons Custom Edit Style', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "theme_general_styling" ,
            'priority'          => 10 ,
        );

        $panels['buttons_custom_styling'] =  array(
            'type'              => 'default',
            'title'             => __('Buttons', 'site-editor'),
            'parent_id'         => "buttons_custom_styling_outer" ,
            'priority'          => 10 ,
        );

        $panels['widgets_custom_styling_outer'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Widgets Custom Edit Style', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "theme_general_styling" ,
            'priority'          => 10 ,
        );

        $panels['widgets_custom_styling'] =  array(  
            'type'              => 'default',
            'title'             => __('Widgets', 'site-editor'),
            'parent_id'         => "widgets_custom_styling_outer" ,
            'priority'          => 10 ,  
        );

        $panels['dark_bg_widgets_custom_styling_outer'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Widgets for Background Dark', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "theme_general_styling" ,
            'priority'          => 10 ,
        );

        $panels['dark_bg_widgets_custom_styling'] =  array(  
            'type'              => 'default',
            'title'             => __('Widgets', 'site-editor'),
            'parent_id'         => "dark_bg_widgets_custom_styling_outer" ,
            'priority'          => 10 ,  
        );

        $panels['dark_bg_widgets_forms_custom_styling'] =  array(  
            'type'              => 'default',
            'title'             => __('forms', 'site-editor'),
            'parent_id'         => "dark_bg_widgets_custom_styling_outer" ,
            'priority'          => 10 ,  
        );

        $panels['media_custom_styling'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Media Custom Edit Style', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "theme_general_styling" ,
            'priority'          => 20 ,
        );

 
        /**
         * Remove Page Builder Settings Panel From Twentyseventeen Theme
         *
         */

        unset($panels['page_builder_settings']);

        return $panels;
    }


    /**
     * Register Theme Fields
     */
    public function register_theme_fields( $fields )
    {

        if( $this->is_added_dynamic_css_options === false ){

            $this->register_dynamic_css_options();

            $this->is_added_dynamic_css_options = true;

        }

        /**
         * Header Settings
         */
        $fields['header_settings'] = array(
            "type"              => "button",
            "label"             => __('Header Settings',"site-editor"),
            'atts'              => array(
                'data-settings-type'    => "app" ,
                'data-settings-id'      => "twenty_seventeen_header" ,
                'class'                 => "open-new-group-settings" ,
            ),
            'priority'          => 10 ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-header' ,
            'style'             => 'menu' ,
            'field_spacing'     => 'sm'
        );

        /**
         * Page Options
         */
        $fields['page_content_layout'] = array(
            'setting_id'        => 'page_layout',
            'label'             => __('Page Content Layout', 'site-editor'),
            'description'       => __( 'When the two column layout is assigned, the page title is in one column and content is in the other.' , 'site-editor' ),
            'type'              => 'radio-buttonset',
            'default'           => 'two-column',
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "one-column"        =>    __('One Column', 'site-editor'),
                "two-column"        =>    __('Two Column', 'site-editor'),
            ) ,
            'panel'             => 'pages_settings',
        );

        $fields['show_pages_title'] = array(
            'setting_id'        => 'sed_show_pages_title',
            'label'             => __('Show pages title', 'site-editor'),
            'type'              => 'switch',
            'default'           => true,
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "on"            =>    "Show" ,
                "off"           =>    "Hide" ,
            ) ,
            'panel'             =>  'pages_settings',
        );

        $fields['disable_page_featured_image_header'] = array(
            'setting_id'        => 'sed_disable_page_featured_image_header',
            'label'             => __('Disable Featured Image Header', 'site-editor'),
            'type'              => 'switch',
            'default'           => false,
            'choices'           => array(
                "on"                    =>    "Yes" ,
                "off"                   =>    "No" ,
            ) ,
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'panel'             => 'pages_settings',
        );


        /**
         * Home Page Options
         */
        $fields['show_front_page_titles'] = array(
            'setting_id'        => 'sed_show_front_page_titles',
            'label'             => __('Show front page titles', 'site-editor'),
            'type'              => 'switch',
            'default'           => true,
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "on"            =>    "Show" ,
                "off"           =>    "Hide" ,
            ) ,
            'panel'             =>  'front_page_settings',
        );

        /**
         * Filter number of front page sections in Twenty Seventeen.
         *
         * @since Twenty Seventeen 1.0
         *
         * @param $num_sections integer
         */
        $num_sections = apply_filters( 'twentyseventeen_front_page_sections', 4 );

        // Create a setting and control for each of the sections available in the theme.
        for ( $i = 1; $i < ( 1 + $num_sections ); $i++ ) {

            $fields['content_panel_' . $i] = array(
                'setting_id'        => 'panel_' . $i,
                'label'             => sprintf( __( 'Front Page Section %d Content', 'twentyseventeen' ), $i ),
                'description'       => __( 'Select pages to feature in this area from the dropdown. Add an image to a section by setting a featured image in the page editor. Empty section will not be displayed.', 'twentyseventeen' ),
                'type'              => 'dropdown-pages',
                'default'           => false,
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'sanitize_callback' => 'absint',
                'panel'             => 'front_page_settings',
                'partial_refresh'   => array(
                    'selector'            => '#panel' . $i,
                    'render_callback'     => 'twse_front_page_section',
                    'container_inclusive' => true,
                )
            );

        }

        /**
         * Footer Settings
         */
        $fields['footer_settings'] = array(
            "type"              => "button",
            "label"             => __('Footer Settings',"site-editor"),
            'atts'              => array(
                'data-settings-type'    => "app" ,
                'data-settings-id'      => "twenty_seventeen_footer" ,
                'class'                 => "open-new-group-settings" ,
            ),
            'priority'          => 12 ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-footer' ,
            'style'             => 'menu' ,
            'field_spacing'     => 'sm'
        );

        /**
         * 404 Page Settings
         */
        $fields['404_content_layout'] = array(
            'setting_id'        => 'sed_404_content_layout',
            'label'             => __('404 Content Layout', 'site-editor'),
            'description'       => __( 'When the two column layout is assigned, the page title is in one column and content is in the other.' , 'site-editor' ),
            'type'              => 'radio-buttonset',
            'default'           => 'two-column',
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "one-column"        =>    __('One Column', 'site-editor'),
                "two-column"        =>    __('Two Column', 'site-editor'),
            ) ,
            'panel'             => '404_page_settings',
        );

        $fields['show_404_page_title'] = array(
            'setting_id'        => 'sed_show_404_page_title',
            'label'             => __('Show 404 page title', 'site-editor'),
            'type'              => 'switch',
            'default'           => true,
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "on"            =>    "Show" ,
                "off"           =>    "Hide" ,
            ) ,
            'panel'             =>  '404_page_settings',
        );

        $fields['404_page_title'] = array(
            'setting_id'        => 'sed_404_page_title',
            'label'             => __('404 Page Title', 'site-editor'),
            'description'       => __( '404 Page Title' , 'site-editor' ),
            'type'              => 'text',
            'default'           => __( 'Oops! That page can&rsquo;t be found.', 'site-editor' ),
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'panel'             => '404_page_settings',
            'dependency' => array(
                'queries'  =>  array(
                    array(
                        "key"       => "show_404_page_title" ,
                        "value"     => '1' ,
                        "compare"   => "=="
                    )
                )
            )
        );

        /**
         * Search Result Settings
         */
        $fields['disable_search_results_sidebar'] = array(
            'setting_id'        => 'sed_disable_search_results_sidebar',
            'label'             => __('Disable search results sidebar', 'site-editor'),
            'type'              => 'switch',
            'default'           => false,
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "on"       =>    "Yes" ,
                "off"      =>    "No" ,
            ) ,
            'panel'             =>  'search_results_page_settings',
        );

        /**
         * Blog settings
         */
        $fields['disable_blog_sidebar'] = array(
            'setting_id'        => 'sed_disable_blog_sidebar',
            'label'             => __('Disable Blog Sidebar', 'site-editor'),
            'type'              => 'switch',
            'default'           => false,
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "on"       =>    "Yes" ,
                "off"      =>    "No" ,
            ) ,
            'panel'             =>  'blog_settings',
        );

        $fields['disable_header_post_meta'] = array(
            'setting_id'        => 'sed_disable_header_post_meta',
            'label'             => __('Disable Header Post Meta', 'site-editor'),
            'type'              => 'switch',
            'default'           => false,
            'choices'           => array(
                "on"       =>    "Yes" ,
                "off"      =>    "No" ,
            ) ,
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'panel'             =>  'blog_settings',
        );

        $fields['disable_footer_post_meta'] = array(
            'setting_id'        => 'sed_disable_footer_post_meta',
            'label'             => __('Disable Footer Post Meta', 'site-editor'),
            'type'              => 'switch',
            'default'           => false,
            'choices'           => array(
                "on"       =>    "Yes" ,
                "off"      =>    "No" ,
            ) ,
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'panel'             =>  'blog_settings',
        );

        /**
         * Single Post settings
         */

        $fields['show_single_post_title'] = array(
            'setting_id'        => 'sed_show_single_post_title',
            'label'             => __('Show Post Title', 'site-editor'),
            'type'              => 'switch',
            'default'           => true,
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "on"            =>    "Yes" ,
                "off"           =>    "No"  ,
            ) ,
            'panel'             =>  'blog_single_posts_settings',
        );

        $fields['disable_single_featured_image_header'] = array(
            'setting_id'        => 'sed_disable_single_featured_image_header',
            'label'             => __('Disable Featured Image Header', 'site-editor'),
            'type'              => 'switch',
            'default'           => false,
            'choices'           => array(
                "on"                    =>    "Yes" ,
                "off"                   =>    "No" ,
            ) ,
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'panel'             => 'blog_single_posts_settings',
        );

        $fields['disable_single_post_comments'] = array(
            'setting_id'        => 'sed_disable_single_post_comments',
            'label'             => __('Disable Comments on single post', 'site-editor'),
            'type'              => 'switch',
            'default'           => false,
            'choices'           => array(
                "on"       =>    "Yes" ,
                "off"      =>    "No" ,
            ) ,
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'panel'             =>  'blog_single_posts_settings',
        );

        $fields['disable_post_nav'] = array(
            'setting_id'        => 'sed_disable_post_nav',
            'label'             => __('Disable Post Navigation', 'site-editor'),
            'type'              => 'switch',
            'default'           => false,
            'choices'           => array(
                "on"       =>    "Yes" ,
                "off"      =>    "No" ,
            ) ,
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'panel'             =>  'blog_single_posts_settings',
        );

        /**
         * Blog && Archive settings
         */
        $archive_partial = $this->get_partial_refresh();

        $fields['show_blog_archive_title'] = array(
            'setting_id'        => 'sed_show_blog_archive_title',
            'label'             => __('Show Archive Title', 'site-editor'),
            'type'              => 'switch',
            'default'           => true,
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "on"            =>    "Show" ,
                "off"           =>    "Hide" ,
            ) ,
            'panel'             =>  'blog_archive_settings',
        );

        $fields['show_blog_archive_description'] = array(
            'setting_id'        => 'sed_show_blog_archive_description',
            'label'             => __('Show Archive Description', 'site-editor'),
            'type'              => 'switch',
            'default'           => true,
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "on"            =>    "Show" ,
                "off"           =>    "Hide" ,
            ) ,
            'panel'             =>  'blog_archive_settings',
        );

        $fields['show_archive_featured_image'] = array(
            'setting_id'        => 'sed_show_archive_featured_image',
            'label'             => __('Show Archive Featured Images', 'site-editor'),
            'type'              => 'switch',
            'default'           => true,
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "on"            =>    "Show" ,
                "off"           =>    "Hide" ,
            ) ,
            'panel'             =>  'blog_archive_settings',
        );

        $fields["blog_content_display"] = array(
            'setting_id'        =>  "sed_blog_content_display",
            "type"              => "radio-buttonset",
            "label"             => __("Blog Content Display","site-editor"),
            "description"       => __('This feature allows you to select if you want whole content of a post be loaded or only Excerpt and a summary of the post be displayed.',"site-editor"),
            'choices'   => array(
                "excerpt"           =>__("Excerpt","site-editor"),
                "content"           =>__("Full Content","site-editor"),
            ),
            "default"           => 'content',
            'option_type'       => 'theme_mod' ,
            'transport'         => 'postMessage' ,
            'partial_refresh'   => $archive_partial ,
            'panel'             =>  'blog_archive_settings',
        );

        $fields["excerpt_length"] = array(
            'setting_id'        =>  "sed_blog_excerpt_length",
            "type"              => 'number',
            "label"             => __("Excerpt Length","site-editor"),
            "description"       => __('This feature allows you to specify the number of Excerpt characters in a post. In other words it enables you to define the number of your post summary’s characters.',"site-editor"),
            "default"           => 250 ,
            'option_type'       => 'theme_mod' ,
            "js_params"         =>  array(
                "min"  =>  10 ,
            ),
            'transport'         => 'postMessage' ,
            'partial_refresh'   => $archive_partial ,
            'panel'             =>  'blog_archive_settings',
            "dependency"    => array(
                'queries'  =>  array(
                    array(
                        "key"           => "blog_content_display" ,
                        "value"         => "excerpt" ,
                        "compare"       => "==="
                    )
                ),
            )
        );

        $fields["excerpt_strip_html"] = array(
            'setting_id'        =>  "sed_blog_excerpt_strip_html",
            "type"              => "switch",
            "label"             => __("Strip HTML from Excerpt","site-editor"),
            "description"       => __('This feature allows to Html and Excerpt codes be overlooked for you.',"site-editor"),
            "default"           => false,
            'choices'           => array(
                "on"                =>    __( "Yes","site-editor") ,
                "off"               =>    __( "No","site-editor") ,
            ) ,
            'option_type'       => 'theme_mod' ,
            'transport'         => 'postMessage' ,
            'partial_refresh'   => $archive_partial ,
            'panel'             =>  'blog_archive_settings',
            "dependency"    => array(
                'queries'  =>  array(
                    array(
                        "key"           => "blog_content_display" ,
                        "value"         => "excerpt" ,
                        "compare"       => "==="
                    )
                ),
            )
        );

        /**
         * Remove Page Builder Settings Fields From Twentyseventeen Theme
         *
         */
        unset($fields['pb_rows_width']);

        unset($fields['pb_rows_padding']);

        $fields = array_merge( $fields , $this->dynamic_css_options );

        return $fields;

    }

    protected function get_partial_refresh(){

        return array(
            'selector'            => '#main.site-main',
            'render_callback'     => array( $this, '_render_archive_content' ),
            'container_inclusive' => false,
        );

    }

    public function _render_archive_content(){

        ob_start();

        if ( have_posts() ) :

            /* Start the Loop */
            while ( have_posts() ) : the_post();

                /*
                 * Include the Post-Format-specific template for the content.
                 * If you want to override this in a child theme, then include a file
                 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                 */
                get_template_part( 'template-parts/post/content', get_post_format() );

            endwhile;

            the_posts_pagination( array(
                'prev_text' => twentyseventeen_get_svg( array( 'icon' => 'arrow-left' ) ) . '<span class="screen-reader-text">' . __( 'Previous page', 'twentyseventeen' ) . '</span>',
                'next_text' => '<span class="screen-reader-text">' . __( 'Next page', 'twentyseventeen' ) . '</span>' . twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ),
                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentyseventeen' ) . ' </span>',
            ) );

        else :

            get_template_part( 'template-parts/post/content', 'none' );

        endif;

        $content = ob_get_contents();

        ob_end_clean();

        return $content;

    }

    public static function the_blog_content(){

        if ( is_single() ) {

            the_content();

        }else{

            $blog_content_display = get_theme_mod( 'sed_blog_content_display' , 'content' );

            $excerpt_length = get_theme_mod( 'sed_blog_excerpt_length' , 250 );

            $strip_html = (bool)get_theme_mod( 'sed_blog_excerpt_strip_html' , '0' );

            switch ( $blog_content_display ){

                case "content" :

                    the_content( sprintf(
                        __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ),
                        get_the_title()
                    ) );

                    break;

                case "excerpt" :

                    $content_post = apply_filters('the_excerpt', get_the_excerpt());

                    # FILTER EXCERPT LENGTH
                    if( strlen( $content_post ) > $excerpt_length )
                        $content_post = mb_substr( $content_post , 0 , $excerpt_length - 3 ) . '...';

                    if( $strip_html )
                        $content_post = strip_tags( $content_post );

                    echo $content_post;

                    break;

            }

        }

    }

    public function sed_base_template_wrapping( $template ){

        $template = dirname( __FILE__ ) . "/sed-base.php";

        return $template;

    }

    public function template_chooser( $template ) {

        $overridden_template = locate_template( 'header.php' );

        var_dump( $overridden_template );

        var_dump( $template );

        return dirname( __FILE__ ) . "/front-page.php";

    }

    public static function get_header() {

        ob_start();

        get_header();

        $header = ob_get_clean();

        $header = str_replace( '<head>', sprintf( '<head>%1$s %2$s %1$s', "\n", '<!-- Built With SiteEditor | http://www.siteeditor.org -->' ), $header );

        echo $header;

    }

    /**
     * Add several SiteEditor theme framework features.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_features(){

        sed_add_theme_support( "site_layout_feature" , array(
            "default_page_length"   =>  'wide' ,
            "default_sheet_width"   =>  '1000px' ,
            //'selectors'             =>  ['.wrap'] ,
            'main_selector'         =>  '#page'
        ) );

        sed_add_theme_support( 'sed_custom_background' , array(
            "default_color "        =>  '#ffffff' ,
            'selector'              =>  'body'
        ) );

    }

    /**
     * Register Static Modules
     *
     * @since 1.0.0
     * @access public
     */
    public function register_static_modules( $manager ){

        require_once dirname( __FILE__ ) . "/modules/header.php";

        $header_module = new TwentyseventeenHeaderStaticModule( $manager , 'twenty_seventeen_header' , array(
                'title'                 => __("Twentyseventeen Header" , "site-editor") ,
                'description'           => __("Twentyseventeen Header Module" , "site-editor")
            )
        );

        $header_module->css_options = $this->header_design_options;

        $manager->add_static_module( $header_module );

        require_once dirname( __FILE__ ) . "/modules/footer.php";

        $footer_module = new TwentyseventeenFooterStaticModule( $manager , 'twenty_seventeen_footer' , array(
                'title'         => __("Twentyseventeen Footer" , "site-editor") ,
                'description'   => __("Twentyseventeen Footer Module" , "site-editor") ,
            )
        );

        $footer_module->css_options = $this->footer_design_options;

        $manager->add_static_module( $footer_module );

    }

    /**
     * Register Static Modules
     *
     * @since 1.0.0
     * @access public
     */
    public function is_page( $module ){

        return is_page();

    }

    /**
     * Theme General Dynamic Css Variables
     * Add New variable to dynamic css
     *
     * @param $vars
     * @return array
     */
    public function get_dynamic_css_vars( $vars ){

        if( $this->is_added_dynamic_css_options === false ){

            $this->register_dynamic_css_options();

            $this->is_added_dynamic_css_options = true;

        }

        $new_vars = array();

        foreach ( $this->dynamic_css_options As $field_id => $option ){

            if( ! isset( $option['setting_id'] ) )
                continue;

            $new_vars[$field_id] = array(
                'settingId'             =>  $option['setting_id'] ,
                'default'               =>  ! isset( $option['default'] ) ? '' : $option['default']
            );

        }

        return array_merge( $vars , $new_vars );

    }

    public function register_dynamic_css_options( ){

        $this->dynamic_css_options = array(

            'border_radius' => array(
                'setting_id'        => 'sed_border_radius',
                'type'              => 'dimension',
                'label'             => __('Border Radius', 'site-editor'),
                "description"       => __("Border Radius for theme", "site-editor") ,
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'general_custom_styling' ,
            ),

            'link_hover_underline' => array(
                'setting_id'        => 'sed_link_hover_underline',
                'label'             => __('Link Hover Underline', 'site-editor'),
                'type'              => 'switch',
                'default'           => true, 
                'transport'         => 'postMessage' ,
                'choices'           => array(
                    "on"            =>    "Show" ,
                    "off"           =>    "Hide" ,
                ) ,
                'panel'             =>  'general_custom_styling',
            ),



            'reset_default_spacing' => array(
                'setting_id'        => 'sed_reset_default_spacing',  
                'label'             => __('Reset Default Spacing', 'site-editor'),
                'type'              => 'switch',
                'default'           => false, 
                'transport'         => 'postMessage' ,
                'choices'           => array(
                    "on"            =>    "Yes" ,
                    "off"           =>    "No" ,
                ) ,
                'panel'             =>  'general_custom_styling',
            ),




            /*'pages_and_front_page_title' => array(
                'setting_id'        => 'sed_pages_and_front_page_title',
                'label'             => __('Pages and Front Page Title', 'site-editor'),
                'type'              => 'radio-buttonset',
                'default'           => 'block',
                'transport'         => 'postMessage' ,
                'choices'           => array(
                    "none"          =>    "Hide" ,
                    "block"         =>    "Show" ,
                ) ,
                'panel'             =>  'general_custom_styling',
                //'has_border_box'    => false,
            ),*/
        

        /*--------------------------------------------------------------
        6.0 Forms
        --------------------------------------------------------------*/

            'form_control_padding' => array(
                'setting_id'        => 'sed_form_control_padding',
                'type'              => 'text',
                'label'             => __('Padding', 'site-editor'),
                'default'           => '0.7em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_border_width' => array(
                'setting_id'        => 'sed_form_control_border_width',
                'type'              => 'text',
                'label'             => __('Border Width', 'site-editor'),
                'default'           => '1px',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_border_radius' => array(
                'setting_id'        => 'sed_form_control_border_radius',
                'type'              => 'dimension',
                'label'             => __('Border Radius', 'site-editor'),
                'default'           => '3px',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ), 

            'form_control_bg' => array(
                'setting_id'        => 'sed_form_control_bg',
                'type'              => 'color', 
                'label'             => __('Background Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_color' => array(
                'setting_id'        => 'sed_form_control_color',
                'type'              => 'color', 
                'label'             => __('Text Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ), 

            'placeholder_color' => array(
                'setting_id'        => 'sed_placeholder_color',
                'type'              => 'color', 
                'label'             => __('Placeholder Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_border' => array(
                'setting_id'        => 'sed_form_control_border',
                'type'              => 'color', 
                'label'             => __('Border Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_box_shadow' => array(
                'setting_id'        => 'sed_form_control_box_shadow',
                'type'              => 'text', 
                'label'             => __('Shadow', 'site-editor'),
                'default'           => 'none',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),  

            'form_control_active_bg' => array(
                'setting_id'        => 'sed_form_control_active_bg',
                'type'              => 'color', 
                'label'             => __('Active Background Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_active_color' => array(
                'setting_id'        => 'sed_form_control_active_color',
                'type'              => 'color', 
                'label'             => __('Active Text Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ),

            'active_placeholder_color' => array(
                'setting_id'        => 'sed_active_placeholder_color',
                'type'              => 'color', 
                'label'             => __('Active Placeholder Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' , 
            ), 

            'form_control_active_border' => array(
                'setting_id'        => 'sed_form_control_active_border',
                'type'              => 'color', 
                'label'             => __('Active Border Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_active_box_shadow' => array(
                'setting_id'        => 'sed_form_control_active_box_shadow',
                'type'              => 'text', 
                'label'             => __('Active Shadow', 'site-editor'),
                'default'           => 'none',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'forms_custom_styling' ,
            ),   


        /*--------------------------------------------------------------
        6.0 Forms -> Button
        --------------------------------------------------------------*/    

            'button_padding' => array(
                'setting_id'        => 'sed_button_padding',
                'type'              => 'text', 
                'label'             => __('Padding', 'site-editor'),
                'default'           => '1em 2em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),        

            'button_border_width' => array(
                'setting_id'        => 'sed_button_border_width',
                'type'              => 'dimension',
                'label'             => __('Border Width', 'site-editor'),
                'default'           => '0px',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'button_border_radius' => array( 
                'setting_id'        => 'sed_button_border_radius',
                'type'              => 'dimension',
                'label'             => __('Border Radius', 'site-editor'),
                'default'           => '2px',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),


            'button_font_weight' => array(
                'setting_id'        => 'sed_button_font_weight',
                "type"              => "select" ,
                "label"             => __('Font Weight', 'site-editor'),
                'default'           => 800,
                'choices'           => array(
                    'normal'        => __('normal', 'site-editor'),
                    'bold'          => __('bold', 'site-editor') ,
                    'bolder'        => __('bolder', 'site-editor'),
                    'lighter'       => __('lighter', 'site-editor') ,
                    100             => 100,
                    200             => 200 ,
                    300             => 300,
                    400             => 400 ,
                    500             => 500,
                    600             => 600 ,
                    700             => 700,
                    800             => 800 ,
                    900             => 900 ,
                ) ,
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'button_text_transform' => array(
                'setting_id'        => 'sed_button_text_transform', 
                'type'              => 'select' ,
                'label'             => __('Text Transform', 'site-editor'),
                'default'           => 'none',
                'choices'           => array(
                    'capitalize'    => __('capitalize', 'site-editor'),
                    'lowercase'     => __('lowercase', 'site-editor'),
                    'uppercase'     => __('uppercase', 'site-editor'),
                    'none'          => __('none', 'site-editor'),
                ) ,
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'buttons_custom_styling' ,
            ),


            'button_bg' => array(
                'setting_id'        => 'sed_button_bg',
                'type'              => 'color', 
                'label'             => __('Background Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'button_border' => array(
                'setting_id'        => 'sed_button_border',
                'type'              => 'color', 
                'label'             => __('Border Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'button_color' => array(
                'setting_id'        => 'sed_button_color',
                'type'              => 'color', 
                'label'             => __('Text Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'buttons_custom_styling' ,
            ),       

            'button_active_bg' => array(
                'setting_id'        => 'sed_button_active_bg',
                'type'              => 'color',  
                'label'             => __('Active Background Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'button_active_border' => array(
                'setting_id'        => 'sed_button_active_border',
                'type'              => 'color', 
                'label'             => __('Active Border Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'button_active_color' => array(
                'setting_id'        => 'sed_button_active_color',
                'type'              => 'color', 
                'label'             => __('Active Text Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'buttons_custom_styling' ,
            ), 

            'secondary_button_bg' => array(
                'setting_id'        => 'sed_secondary_button_bg',
                'type'              => 'color', 
                'label'             => __('Secondary Background Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'secondary_button_border' => array(
                'setting_id'        => 'sed_secondary_button_border',
                'type'              => 'color', 
                'label'             => __('Secondary Border Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'secondary_button_color' => array(
                'setting_id'        => 'sed_secondary_button_color',
                'type'              => 'color', 
                'label'             => __('Secondary Text Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'buttons_custom_styling' ,
            ),       

            'secondary_button_active_bg' => array(
                'setting_id'        => 'sed_secondary_button_active_bg',
                'type'              => 'color',  
                'label'             => __('Secondary Active Background Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'secondary_button_active_border' => array(
                'setting_id'        => 'sed_secondary_button_active_border',
                'type'              => 'color', 
                'label'             => __('Secondary Active Border Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),

            'secondary_button_active_color' => array( 
                'setting_id'        => 'sed_secondary_button_active_color',
                'type'              => 'color', 
                'label'             => __('Secondary Active Text Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'buttons_custom_styling' ,
            ),  



        /*--------------------------------------------------------------
        15.0 Widgets
        --------------------------------------------------------------*/ 


            'widget_color' => array(
                'setting_id'        => 'sed_widget_color',
                'type'              => 'color', 
                'label'             => __('Text Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'widgets_custom_styling' ,
            ),   

            'secondary_widget_color' => array(
                'setting_id'        => 'sed_secondary_widget_color',
                'type'              => 'color', 
                'label'             => __('Secondary Text Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'widgets_custom_styling' ,
            ),   

            'widget_border_color' => array(
                'setting_id'        => 'sed_widget_border_color',
                'type'              => 'color', 
                'label'             => __('Border Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'widgets_custom_styling' ,
            ),  

            'widget_border_width' => array(
                'setting_id'        => 'sed_widget_border_width',
                'type'              => 'dimension',
                'label'             => __('Border Width', 'site-editor'),
                'default'           => '1px',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'widgets_custom_styling' ,
            ),    

            'widget_border_style' => array(
                'setting_id'        => 'sed_widget_border_style',
                "type"              => "select" ,
                "label"             => __('Border Style', 'site-editor'),
                'default'           => "solid",
                'choices'           => array(
                    'none'          => __('none', 'site-editor'),
                    'dotted'        => __('dotted', 'site-editor'),
                    'dashed'        => __('dashed', 'site-editor'),
                    'solid'         => __('solid', 'site-editor'),
                    'double'        => __('double', 'site-editor'),
                    'groove'        => __('groove', 'site-editor'),
                    'ridge'         => __('ridge', 'site-editor'),
                    'inset'         => __('inset', 'site-editor'),
                    'outset'        => __('outset', 'site-editor'),
                ) ,
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'widgets_custom_styling' ,
            ),

            'widget_border_radius' => array( 
                'setting_id'        => 'sed_widget_border_radius',
                'type'              => 'dimension',
                'label'             => __('Border Radius', 'site-editor'),
                'default'           => '0px',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'widgets_custom_styling' ,
            ),

            'widget_lists_padding' => array(
                'setting_id'        => 'sed_widget_padding',
                'type'              => 'text', 
                'label'             => __('Lists Padding', 'site-editor'),
                'default'           => '0.5em 0',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'widgets_custom_styling' ,
            ),  

            'widget_title_color' => array(
                'setting_id'        => 'sed_widget_title_color',
                'type'              => 'color', 
                'label'             => __('Title Color', 'site-editor'),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'widgets_custom_styling' ,
            ),   

            'widget_title_font_size' => array(
                'setting_id'        => 'sed_widget_title_font_size',
                'type'              => 'dimension',
                'label'             => __('Title Font Size', 'site-editor'),
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'widgets_custom_styling' ,
            ),

            'widget_title_font_weight' => array(
                'setting_id'        => 'sed_widget_title_font_weight',
                "type"              => "select" ,
                "label"             => __('Title Font Weight', 'site-editor'),
                'default'           => 800,
                'choices'           => array(
                    'normal'        => __('normal', 'site-editor'),
                    'bold'          => __('bold', 'site-editor') ,
                    'bolder'        => __('bolder', 'site-editor'),
                    'lighter'       => __('lighter', 'site-editor') ,
                    100             => 100,
                    200             => 200 ,
                    300             => 300,
                    400             => 400 ,
                    500             => 500,
                    600             => 600 ,
                    700             => 700,
                    800             => 800 ,
                    900             => 900 ,
                ) ,
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'widgets_custom_styling' ,
            ),

            'widget_title_text_transform' => array(
                'setting_id'        => 'sed_widget_title_text_transform', 
                'type'              => 'select' ,
                'label'             => __('Title Text Transform', 'site-editor'),
                'default'           => 'uppercase',
                'choices'           => array(
                    'capitalize'    => __('capitalize', 'site-editor'),
                    'lowercase'     => __('lowercase', 'site-editor'),
                    'uppercase'     => __('uppercase', 'site-editor'),
                    'none'          => __('none', 'site-editor'),
                ) ,
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'widgets_custom_styling' ,
            ),     


        /*--------------------------------------------------------------
        15.0 Widgets for Background Dark
        --------------------------------------------------------------*/ 


            'dark_bg_widget_color' => array( 
                'setting_id'        => 'sed_dark_bg_widget_color',
                'type'              => 'color', 
                'label'             => __('Text Color', 'site-editor'),
                'default'           => 'rgba(255,255,255,0.9)',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_custom_styling' ,
            ),   

            'dark_bg_secondary_widget_color' => array(
                'setting_id'        => 'sed_dark_bg_secondary_widget_color',
                'type'              => 'color', 
                'label'             => __('Secondary Text Color', 'site-editor'),
                'default'           => 'rgba(255,255,255,0.7)',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_custom_styling' ,
            ),    

            'dark_bg_widget_border_color' => array(
                'setting_id'        => 'sed_dark_bg_widget_border_color',
                'type'              => 'color', 
                'label'             => __('Border Color', 'site-editor'),
                'default'           => 'rgba(255,255,255,0.2)',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_custom_styling' ,
            ),  

            'dark_bg_widget_title_color' => array(
                'setting_id'        => 'sed_dark_bg_widget_title_color',
                'type'              => 'color', 
                'label'             => __('Title Color', 'site-editor'),
                'default'           => '#fff',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_custom_styling' ,
            ),   

            'dark_bg_form_control_bg' => array(
                'setting_id'        => 'sed_dark_bg_form_control_bg',
                'type'              => 'color', 
                'label'             => __('Background Color', 'site-editor'),
                'default'           => 'rgba(255,255,255,0.08)',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_forms_custom_styling' ,
            ),

            'dark_bg_form_control_color' => array(
                'setting_id'        => 'sed_dark_bg_form_control_color',
                'type'              => 'color', 
                'label'             => __('Text Color', 'site-editor'),
                'default'           => '#fff',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_forms_custom_styling' ,
            ), 

            'dark_bg_placeholder_color' => array(
                'setting_id'        => 'sed_dark_bg_placeholder_color',
                'type'              => 'color', 
                'label'             => __('Placeholder Color', 'site-editor'),
                'default'           => 'rgba(255,255,255,0.7)',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_forms_custom_styling' ,
            ),

            'dark_bg_form_control_border' => array(
                'setting_id'        => 'sed_dark_bg_form_control_border',
                'type'              => 'color', 
                'label'             => __('Border Color', 'site-editor'),
                'default'           => 'rgba(255,255,255,0.2)',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_forms_custom_styling' ,
            ),

            'dark_bg_form_control_active_bg' => array(
                'setting_id'        => 'sed_dark_bg_form_control_active_bg',
                'type'              => 'color', 
                'label'             => __('Active Background Color', 'site-editor'),
                'default'           => 'rgba(255,255,255,0.08)',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_forms_custom_styling' ,
            ),

            'dark_bg_form_control_active_border' => array(
                'setting_id'        => 'sed_dark_bg_form_control_active_border',
                'type'              => 'color', 
                'label'             => __('Active Border Color', 'site-editor'),
                'default'           => 'rgba(255,255,255,0.5)',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'has_border_box'    => false,
                'panel'             => 'dark_bg_widgets_forms_custom_styling' ,
            ),

        /*--------------------------------------------------------------
        16.0 Media
        --------------------------------------------------------------*/


            'playlist_item_active_bg' => array(
                'setting_id'        => 'sed_playlist_item_active_bg',
                'type'              => 'color',  
                'label'             => __('Playlist Item Active Background Color', 'site-editor'),
                "description"       => __("Playlist Item Active Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'media_custom_styling' ,
            ),    

            'playlist_item_active_color' => array(
                'setting_id'        => 'sed_playlist_item_active_color',
                'type'              => 'color', 
                'label'             => __('Playlist Item Active Text Color', 'site-editor'),
                "description"       => __("Playlist Item Active Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'media_custom_styling' ,
            ),


        /*--------------------------------------------------------------
        21.0 Layout
        --------------------------------------------------------------*/            

            'home_content_padding_top' => array(
                'setting_id'        => 'sed_home_content_padding_top',
                'type'              => 'dimension',
                'label'             => __('Home Content Padding Top', 'site-editor'),
                'default'           => '6em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'front_page_settings' ,
            ),      

            'rps_home_content_padding_top' => array(
                'setting_id'        => 'sed_rps_home_content_padding_top',
                'type'              => 'dimension',
                'label'             => __('Home Content Responsive Padding Top', 'site-editor'),
                'default'           => '3.5em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'front_page_settings' ,
            ),

            'home_content_padding_bottom' => array(
                'setting_id'        => 'sed_home_content_padding_bottom',
                'type'              => 'dimension',
                'label'             => __('Home Content Padding Bottom', 'site-editor'),
                'default'           => '4.5em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'front_page_settings' ,
            ),

            'rps_home_content_padding_bottom' => array(
                'setting_id'        => 'sed_rps_home_content_padding_bottom',
                'type'              => 'dimension', 
                'label'             => __('Home Content Responsive Padding Bottom', 'site-editor'),
                'default'           => '2em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'front_page_settings' ,
            ),

            'site_content_padding_top' => array(
                'setting_id'        => 'sed_site_content_padding_top',
                'type'              => 'dimension',
                'label'             => __('Site Content Padding Top', 'site-editor'),
                'default'           => '5.5em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'pages_settings' ,
            ),

            'rps_site_content_padding_top' => array(
                'setting_id'        => 'sed_rps_site_content_padding_top',
                'type'              => 'dimension',
                'label'             => __('Site Content Responsive Padding Top', 'site-editor'),
                'default'           => '2.5em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'pages_settings' ,
            ),

            'page_content_padding_bottom' => array(
                'setting_id'        => 'sed_page_content_padding_bottom',
                'type'              => 'dimension',
                'label'             => __('Page Content Padding Bottom', 'site-editor'),
                'default'           => '3.25em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'pages_settings' ,
            ),

            'rps_page_content_padding_bottom' => array(
                'setting_id'        => 'sed_rps_page_content_padding_bottom',
                'type'              => 'dimension',
                'label'             => __('Page Content Responsive Padding Bottom', 'site-editor'),
                'default'           => '1.5em', 
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'pages_settings' ,
            ),

            'page404_content_padding_bottom' => array(
                'setting_id'        => 'sed_page404_content_padding_bottom',
                'type'              => 'dimension',
                'label'             => __('404 Page Content Padding Bottom', 'site-editor'),
                'default'           => '9em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => '404_page_settings' ,
            ),

            'rps_page404_content_padding_bottom' => array( 
                'setting_id'        => 'sed_rps_page404_content_padding_bottom',
                'type'              => 'dimension',
                'label'             => __('404 Page Content Responsive Padding Bottom', 'site-editor'),
                'default'           => '4em',  
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => '404_page_settings' ,
            ),

        /*--------------------------------------------------------------
        Wrap
        --------------------------------------------------------------*/            

            'wrap_padding_left_right' => array(
                'setting_id'        => 'sed_wrap_padding_left_right',
                'type'              => 'dimension',
                'label'             => __('Wrap Padding Left & Right', 'site-editor'),
                'default'           => '3em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'general_settings' ,
            ),        

            'rps_wrap_padding_left_right' => array(
                'setting_id'        => 'sed_rps_wrap_padding_left_right',
                'type'              => 'dimension',
                'label'             => __('Wrap Responsive Padding Left & Right', 'site-editor'),
                'default'           => '2em',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'general_settings' ,
            ),      


        );

    }

    /**
     * Remove Page Builder Settings Dynamic Css From Twentyseventeen Theme
     *
     */
    public function remove_page_builder_settings(){

        remove_action( "sed_before_dynamic_css_output"       , array( SED()->framework , 'pb_css_output' ) );

    }

}



