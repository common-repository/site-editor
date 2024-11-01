<?php
/**
 * SiteEditor Field: font-weight.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorFontWeightField' ) ) {

    if( ! class_exists( 'SiteEditorSelectField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-select-field.class.php';
    } 
    
    /**
     * Field overrides.
     */
    class SiteEditorFontWeightField extends SiteEditorSelectField { 

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'font_weight';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'font-weight';

        /**
         * Use 'refresh', 'postMessage'
         *
         * @access protected
         * @var string
         */
        public $transport = 'postMessage';

        /**
         * Sets the Default Value
         *
         * @access protected
         */
        protected function set_default() {

            // If a custom default has been defined,
            // then we don't need to proceed any further.
            if ( ! empty( $this->default ) ) {
                return;
            }

            $this->default = '';

        }
        
        /**
         * Sets the $choices.
         *
         * @access protected
         */
        protected function set_choices() {

            if ( is_array( $this->choices ) && !empty( $this->choices ) ) {
                return ;
            }

            $this->choices = array(
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
            );

        }

    }
}

sed_options()->register_field_type( 'font-weight' , 'SiteEditorFontWeightField' );
