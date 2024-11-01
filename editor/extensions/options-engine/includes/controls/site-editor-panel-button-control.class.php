<?php
/**
 * SiteEditor Control: panel-button
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorPanelButtonControl' ) ) {

	/**
	 * Skin control
	 */
	class SiteEditorPanelButtonControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'panel-button';

		/**
		 * The control Button Style
		 *
		 * @access public
		 * @var string
		 */
		public $button_style = 'black';

        /**
         * The Title Of Skin Panel
         *
         * @access public
         * @var string
         */
        public $panel_title = '';

		/**
		 * The Title Of Skin Panel
		 *
		 * @access public
		 * @var string
		 */
		public $panel_content = '';

		/**
		 * Go to panel button icon
		 *
		 * @access public
		 * @var string
		 */
		public $icon = "";

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {

		}

		/**
		 * Renders the control wrapper and calls $this->render_content() for the internals.
		 *
		 * @since 3.4.0
		 */
		protected function render_content() {

			$atts           = $this->input_attrs();

			$atts_string    = $atts["atts"];

			$classes        = "sed-module-element-control sed-element-control sed-bp-input sed-bp-skin-input sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $level_box_id   = 'dialog_page_box_' . $pkey;

			$this->button_style = empty( $this->button_style ) ? "default" : $this->button_style;

            $classes .= " sed-btn-{$this->button_style}";

			?>

            <?php if(!empty($this->description)){ ?>
                <span class="field_desc flt-help sedico sedico-question sedico-lg " title="<?php echo esc_attr( $this->description );?>"></span>
            <?php } ?>

            <button type="button" data-related-level-box="<?php echo esc_attr( $level_box_id );?>" class="<?php echo esc_attr( $classes ); ?>"  name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" <?php echo $atts_string; ?>>

				<?php if( ! empty( $this->icon ) ){ ?>
					<span class="sedico sedico-lg <?php echo esc_attr( $this->icon );?>"></span>
				<?php } ?>

				<?php echo esc_html( $this->label );?>
                <span class="sedico sedico-chevron-right sed-arrow-right sedico-lg"></span>
            </button>

            <div id="<?php echo esc_attr( $level_box_id );?>" data-title="<?php echo esc_attr( $this->panel_title );?>" data-multi-level-box="true">
                <?php $this->panel_content() ;?>
            </div>

			<?php
		}

        /**
         * Panel Content For Override in extend classes
         *
         *
         * @access protected
         */
        protected function panel_content() {

            echo $this->panel_content;

        }

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 *
		 * @see SiteEditorOptionsControl::print_template()
		 *
		 * @access protected
		 */
		protected function content_template() {

		}
	}
}

$this->register_control_type( 'panel-button' , 'SiteEditorPanelButtonControl' );
