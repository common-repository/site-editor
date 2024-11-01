<?php
/**
 * SiteEditor Field: text-shadow.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorTextShadowField' ) ) {
    
    /**
     * Field overrides.
     */
    class SiteEditorTextShadowField extends SiteEditorField { 

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'text_shadow';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'text-shadow';

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

            $this->default = 'none';

        }

    }
}

sed_options()->register_field_type( 'text-shadow' , 'SiteEditorTextShadowField' );
