<?php

    $img = false;

    switch ( $image_source ) {
        case "attachment":
            $img = get_sed_attachment_image_html( $attachment_id , $default_image_size , $custom_image_size );
            break;
        case "external":
            $img = get_sed_external_image_html( $image_url , $external_image_size );
            break;

    }

    if ( ! $img ) {
        $img = array();
        $img['thumbnail'] = '<img class="sed-image-placeholder sed-image" src="' . sed_placeholder_img_src() . '" />';
    }
  
?>
<div  class="module module-text-icon text-icon-skin3 <?php echo $class; ?>" <?php echo $sed_attrs; ?>>
    <div class="text-icon-wrapper">  
        <?php echo $content; ?>
    	<div class="text-icon"><?php echo $img['thumbnail']; ?></div>  
    </div>
    <?php
        $selector = ( site_editor_app_on() || sed_loading_module_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
        ob_start();
    ?>

        <?php echo $selector; ?>.module-text-icon.text-icon-skin3 .text-icon img {
            min-width: <?php echo $image_width;?>;  
            width: <?php echo $image_width;?>;   
        }

    <?php
        $css = ob_get_clean();
        sed_module_dynamic_css( $css );
    ?>
</div>