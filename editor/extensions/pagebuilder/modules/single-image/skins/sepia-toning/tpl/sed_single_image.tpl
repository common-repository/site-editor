<#
    if( !lightbox_id ){
        lightbox_id = sed_model_id;
    }

    var api = sedApp.editor ;

    var imgAttrs = {
        "class" : "sed-img"
    };

    var CustomImgSize = ( image_source == "external" ) ? external_image_size : custom_image_size;

    var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , CustomImgSize , imgAttrs );

    if( image_source == "attachment" ){
        full_src = api.fn.getAttachmentImageFullSrc( attachment_id );
    }

    if( !full_src ){

        full_src = SEDNOPIC.url;

    }
#>

<div {{sed_attrs}} class="module module-single-image single-image-sepia-toning {{className}}">

    <# if(image_click == "expand_mode"){ #>
        <a class="img sepia" href="{{full_src}}" data-lightbox="{{lightbox_id}}" data-title="{{title}}" title="{{title}}">
        {{{sedImageHtml}}}
        </a>
    <# } #>

    <# if(image_click == "link_mode" || image_click == "link_expand_mode" ){ #>
        <a class="img sepia" href="{{link}}" target="{{link_target}}" title="{{title}}">
        {{{sedImageHtml}}}
        </a>
    <# } #>

    <# if(image_click == "default"){ #>
        <div class="img sepia">
        {{{sedImageHtml}}}
        </div>
    <# } #>

</div>

