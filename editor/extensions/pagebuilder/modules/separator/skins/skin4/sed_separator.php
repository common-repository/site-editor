<div <?php echo $sed_attrs; ?>  class="module module-separator separator-skin4 <?php echo $class;?>" >
    <div class="module-separator-inner">
        <div class="separator-inner">
          <div class="spr-container">
            <div class="<?php echo $border_style;?> spr-horizontal separator"></div>
          </div>
          <?php echo $content;?>
          <div class="spr-container">
            <div class="<?php echo $border_style;?> spr-horizontal separator"></div>
          </div>
        </div>
    </div>
    <?php
        $selector = ( site_editor_app_on() || sed_loading_module_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
        ob_start();
    ?>

        <?php echo $selector; ?>.module-separator .separator {
            border-color: <?php echo $separator_color;?>; 
        }
    
        <?php echo $selector; ?> .module-separator-inner {
          max-width: <?php echo $max_width;?>px; 
        }

        <?php echo $selector; ?> .separator.spr-horizontal  {
            border-width: <?php echo $separator_width;?>px 0 0 0; 
        }

        <?php echo $selector; ?> .separator.spr-horizontal.spr-double {
            border-width: <?php echo $separator_width;?>px 0 <?php echo $separator_width;?>px 0 ;
        }

    <?php
        $css = ob_get_clean();
        sed_module_dynamic_css( $css );
    ?>
</div>