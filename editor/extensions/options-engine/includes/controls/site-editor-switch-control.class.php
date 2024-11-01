<?php
/**
 * SiteEditor Control: switch.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorSwitchControl' ) ) {

	/**
	 * Switch control
	 */
	class SiteEditorSwitchControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'switch';

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

			$classes        = "sed-module-element-control sed-element-control sed-bp-input sed-bp-switch-input sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			if( ! class_exists( 'SiteEditorCheckboxField' ) ){
				require_once dirname( dirname( __FILE__ ) ) . DS . 'fields' . DS . 'site-editor-checkbox-field.class.php';
			}

            $value = SiteEditorCheckboxField::sanitize( $value );

			?>


			<label class=""><?php echo esc_html( $this->label );?></label>
			<?php if(!empty($this->description)){ ?> 
			    <span class="field_desc flt-help sedico sedico-question sedico-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
			<?php } ?>
			<div class="sed-bp-form-switch"> 
			    <?php $checked = ( "1" == $value ) ? 'checked="checked"' : ''; ?>
				<input  type="checkbox" class="<?php echo esc_attr( $classes ); ?>" value="true" name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" <?php echo $checked;?> <?php echo $atts_string;?> />
				<label class="switch-label" for="<?php echo esc_attr( $sed_field_id );?>">
					<span class="switch-on"><?php echo $this->choices['on']; ?> </span>
					<span class="switch-off"><?php echo  $this->choices['off']; ?> </span>
				</label>
			</div>	  

			<?php
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

$this->register_control_type( 'switch' , 'SiteEditorSwitchControl' );
