<?php
/**
 * SiteEditor Control: parallax-background-ratio.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorParallaxBackgroundRatioControl' ) ) {

    if( ! class_exists( 'SiteEditorSliderControl' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/controls/site-editor-slider-control.class.php';
    }

	/**
	 * Parallax Background Ratio control
	 *
	 * Class SiteEditorParallaxBackgroundRatioControl
	 */
	class SiteEditorParallaxBackgroundRatioControl extends SiteEditorSliderControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'parallax-background-ratio';

        /**
         * The control category.
         *
         * @access public
         * @var string
         */
        public $category = 'style-editor';

        /**
         * The control is style option ?
         *
         * @access public
         * @var string
         */
        public $is_style_setting = true;

        /**
         * The control js render type
         *
         * @access public
         * @var string
         */
        public $js_type = "slider";

        /**
         * Css Selector for apply style
         *
         * @access public
         * @var string
         */
        public $selector = "";

        /**
         * Css Style Property
         *
         * @access public
         * @var string
         */
        public $style_props = "parallax-background-ratio";



        /**
         * Get the data to export to the client via JSON.
         *
         * @since 1.0.0
         *
         * @return array Array of parameters passed to the JavaScript.
         */
        public function json() {

            $json_array = parent::json();
            $json_array['type'] = $this->js_type;

            if( !empty( $this->style_props ) )
                $json_array['style_props'] = $this->style_props;

            if( !empty( $this->selector ) )
                $json_array['selector'] = $this->selector;

            return $json_array;

        }

	}
}

sed_options()->register_control_type( 'parallax-background-ratio' , 'SiteEditorParallaxBackgroundRatioControl' );
