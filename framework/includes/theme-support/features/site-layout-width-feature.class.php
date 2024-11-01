<?php
/**
 * Site Width & Site Layout Feature Class.
 *
 * Handles all features of themes
 *
 * @package SiteEditor
 * @subpackage framework
 * @since 1.0.0
 */
class SiteEditorSiteLayoutWidthFeature extends SiteEditorThemeFeature{

    /**
     * @access public
     * @var string
     */
    public $id = 'site_layout_feature';

    /**
     * Page default Length use from "wide" or "boxed"
     *
     * @access public
     * @var string
     */
    public $default_page_length = 'wide';

    /**
     * Sheet default width use from valid css value
     *
     * @access public
     * @var string
     */
    public $default_sheet_width = '1100px';

    /**
     * Selector For target element it's can change page length and sheet width
     *
     * @access public
     * @var string
     */
    public $main_selector = 'body';

    /**
     * Selector For target element it's can change page length and sheet width
     *
     * @access public
     * @var string
     */
    public $selectors = array();

    /**
     * Initialize class
     *
     * @since 1.0.0
     * @access public
     */
    public function render_init() {

        add_action( "sed_before_dynamic_css_output" , array( $this , 'feature_output' ) );

    }

    /**
     * Add To Dynamic Css
     *
     * @since 1.0.0
     * @access public
     */
    public function feature_output(){
        global $sed_dynamic_css_string;

        $main_selector = $this->main_selector;

        $selectors   = empty( $this->selectors ) || !is_array( $this->selectors ) ? "" : implode("," , $this->selectors );

        $sheet_width = get_theme_mod( 'sheet_width' );

        $sheet_width = ( $sheet_width === false ) ? $this->default_sheet_width : $sheet_width; 

        $site_length = get_theme_mod( 'site_length' );

        $site_length = ( $site_length === false ) ? $this->default_page_length : $site_length;

        $page_length = sed_get_page_setting( 'page_length' );

        $page_length = ( $page_length === 'default' ) ? $site_length : $page_length;

        ob_start();
        ?>
        <?php
        if( $page_length == "boxed" ) {
            echo $main_selector; ?>,
            .sed-row-boxed{
                max-width : <?php echo $sheet_width; ?> !important;
                margin-left : auto !important;
                margin-right : auto !important;
            }
            <?php
        }else{
            echo $main_selector; ?>{
                max-width : 100% !important;
            }

            .sed-row-boxed{
                max-width : <?php echo $sheet_width; ?> !important;
            }

            <?php
        }

        echo $selectors; ?>{
            max-width : <?php echo $sheet_width; ?> !important;
            margin-left : auto !important;
            margin-right : auto !important;
        }
        <?php

        $css = ob_get_clean();
        $sed_dynamic_css_string .= $css;
    }

    /**
     * Get the data to export to the client via JSON.
     * Using data in preview
     *
     * @since 1.0.0
     * @access public
     * @return array Array of parameters passed to the JavaScript.
     */
    public function json() {

        $json_array = parent::json();

        $selectors = empty( $this->selectors ) || !is_array( $this->selectors ) ? array( ) : $this->selectors;

        $json_array['main_selector']            = $this->main_selector;
        $json_array['selectors']                = $selectors;
        $json_array['default_page_length']      = $this->default_page_length;
        $json_array['default_sheet_width']      = $this->default_sheet_width;

        return $json_array;
    }

}

$this->register_feature( 'site_layout_feature' , 'SiteEditorSiteLayoutWidthFeature' );