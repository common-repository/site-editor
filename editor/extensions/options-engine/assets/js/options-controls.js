/**
 * SiteEditor Posts JS Plugin
 *
 * Copyright, 2016
 * Released under LGPL License.
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){

    var api = sedApp.editor;

    api.SliderControl = api.SiteEditorControls.extend({

        _ready: function() {

			var control = this ,
				slider = this.container.find('.sed-bp-form-slider-container');

			this.sliderInputValue = this.container.find(".slider-demo-value");

            this.sliderOptions =  {
                min     : 0 ,
                max     : 100 ,
                step    : 1 ,
                value   : 10 ,
                range   : "min"
            };

            this.slider = slider;

            if ( ! _.isUndefined( control.params.min ) ) {
                this.sliderOptions.min = _.isNaN( parseFloat( control.params.min ) ) ? 0 : parseFloat( control.params.min );
            }

            if ( ! _.isUndefined( control.params.max ) ) {
                this.sliderOptions.max = _.isNaN( parseFloat( control.params.max ) ) ? 100 : parseFloat( control.params.max );
            }

            if ( ! _.isUndefined( control.params.step ) ) {
                this.sliderOptions.step = _.isNaN( parseFloat( control.params.step ) ) ? 1 : parseFloat( control.params.step );
            }

            if( !_.isNull( control.defaultValue ) ) {
                this.sliderOptions.value = _.isNaN( parseFloat( control.defaultValue ) ) ? this.sliderOptions.min : parseFloat( control.defaultValue );
            }

            var _lazyRefresh = _.debounce(function( value ){
                control.refresh( value );
            }, 50);

            this.sliderOptions.slide = function( event, ui ) {
		        control.sliderInputValue.val( parseFloat( ui.value ) );
                _lazyRefresh( ui.value );
		    };

			slider.slider( this.sliderOptions );

        },

        _update: function( val ){

            val = ( _.isUndefined( val ) || _.isNaN( parseFloat( val ) ) ) ? this.sliderOptions.min : parseFloat( val );

            this.slider.slider( "value", val );

            this.sliderInputValue.val( val );

        }

    });


    api.DimensionControl = api.SiteEditorControls.extend({

        _ready: function() {

            var control = this;

            this.dimension = this.container.find('.sed-bp-dimension-input');

            // Save the value
            this.dimension.on( 'change keyup paste', function() {

                var value = $( this ).val();

                // Validate the value and show a warning if it's invalid.
                // We did this once when initializing the field, but we need to re-evaluate
                // every time the value changes.
                if ( false === control._validate( value ) ) {

                    control.container.find( '.sed-bp-form-dimension' ).addClass( 'invalid' );

                } else {

                    control.container.find( '.sed-bp-form-dimension' ).removeClass( 'invalid' );

                    control.refresh( value );

                }

            });

        },

        _validate: function ( value ) {

            var validUnits = ['rem', 'em', 'ex', '%', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ch', 'vh', 'vw', 'vmin', 'vmax'],
                numericValue,
                unit;

            // 0 is always a valid value
            if ( '0' === value ) {
                return true;
            }

            if( !_.isString( value ) ){
                return false;
            } 

            // If we're using calc() just return true.
            if ( 0 <= value.indexOf( 'calc(' ) && 0 <= value.indexOf( ')' ) ) {
                return true;
            }

            // Get the numeric value.
            numericValue = parseFloat( value );

            // Get the unit
            unit = value.replace( numericValue, '' );

            // Check the validity of the numeric value.
            if ( isNaN( numericValue ) ) {
                return false;
            }

            // Check the validity of the units.
            if ( -1 === $.inArray( unit, validUnits ) ) {
                return false;
            }

            return true;

        },

        _update: function( val ){

            val = ( _.isUndefined( val ) || ! $.trim( val ) ) ? 0 : val;

            this.dimension.val( val );

            // Validate the value and show a warning if it's invalid
            if ( false === this._validate( val ) ) {
                this.container.find( '.sed-bp-form-dimension' ).addClass( 'invalid' );
            } else {
                this.container.find( '.sed-bp-form-dimension' ).removeClass( 'invalid' );
            }

        }

    });


    api.DateControl = api.SiteEditorControls.extend({

        _ready: function() {

            var control = this ,
                dateInput = this.container.find('.sed-bp-form-datepicker-input');

            this.dateInput = dateInput;

            var datepickerOptions = {};

            if ( ! _.isUndefined( control.params.datepicker ) ) {
                datepickerOptions = control.params.datepicker;
            }

            dateInput.datepicker( datepickerOptions );

            // Save the changes
            dateInput.on( 'change keyup paste', function() {
                control.refresh( $( this ).val() );
            });

        },

        _update: function( val ){

            this.dateInput.val( val );

        }

    });


    api.MulticolorControl = api.SiteEditorControls.extend({

        _ready: function() {

            var control = this ,
                pickers  = this.container.find('.sed-colorpicker');

            this.model = _.isObject( control.defaultValue ) ? control.defaultValue : {};

            this.pickers = pickers;

            var colorPickerOptionBG = _.clone( colorPickerOption );

            //pickers.val( '' );

            if( !_.isUndefined( this.params.show_input ) )
                colorPickerOptionBG.showInput = this.params.show_input;

            var _refresh = function( color , key ){

                color = _.isNull( color ) ? "transparent" : color.toString();

                var newModel = $.extend( true , {} , control.currentValue );

                newModel[key] = color;

                //var newVal = $.extend( true , {} , control.model ); console.log( "-----newVal-----" , newVal );

                control.refresh( newModel );

            };

            var _lazyRefresh = _.debounce(function( color , key ){
                _refresh( color , key );
            }, 20);

            colorPickerOptionBG.change = function (color) {
                var key = $(this).data( "key" );
                _refresh( color , key );
            };

            colorPickerOptionBG.move = function (color) {
                var key = $(this).data( "key" );
                _lazyRefresh( color , key );
            };

            colorPickerOptionBG.dragstop = function (color) {
                var key = $(this).data( "key" );
                _refresh( color , key );
            };

            colorPickerOptionBG.hide = function(color) {
                var key = $(this).data( "key" );
                _refresh( color , key );
            };

            this.pickers.spectrum(colorPickerOptionBG);
        },

        _update: function( val ){
            var control = this;

            val = ( _.isObject( val ) ) ? val : {};

            $.each( val , function( key , color ){
                color = ( color == "transparent" ) ? "" : color;
                control.container.find('.sed-colorpicker[data-key="'+ key +'"]').spectrum("set", color);
            });

        }

    });


    api.SortableControl = api.SiteEditorControls.extend({

        _ready: function() {

			var control = this ,
				sortableUl = this.container.find('.sed-bp-form-sortable');

            this.sortableUl = sortableUl;

            if( _.isString( control.defaultValue ) ){
                control.defaultValue = control.defaultValue.split(",");
            }

            if( ! $.isArray( control.defaultValue ) ) {
                control.defaultValue = [];
            }

            this.models = {};

            var _refresh = function(){

                var currVal = [];

                control.container.find('.sed-bp-checkbox-input').filter(":checked").each(function(index , el){

                    currVal.push( $(this).val() );

                });

                control.refresh( currVal );

            };

            sortableUl.sortable({

                items: "li.sed-bp-form-sortable-item",

                update: function( event, ui ) {

                    _refresh( );

                    var currId;

                    if( control.isModuleControl ) {
                        currId = api.currentTargetElementId;
                    }else{
                        currId = control.id;
                    }

                    control._saveOrdersModels( currId );
                }

		    });  

			sortableUl.disableSelection();

            this.container.find('.sed-bp-checkbox-input').on("change", function(){

                _refresh( );

            });

            this._saveOrdersModels( control.id );

        },

        _saveOrdersModels : function( currId ){

            var control = this;

            this.models[currId] = [];

            this.container.find('.sed-bp-checkbox-input').each(function(index , el){

                control.models[currId].push( $(this).val() );

            });

        },

        _update: function( val ){
            var control = this,
                currValue = [],
                models;

            if( _.isString( val ) )
                currValue = val.split(",");
            else if( $.isArray( val ) )
                currValue = val;

            this.container.find('.sed-bp-checkbox-input').each(function(index , el){

                if( $.inArray( $(this).val() , currValue) > -1)
                    $(this).prop( 'checked', true );
                else
                    $(this).prop( 'checked', false );

            });

            if( this.isModuleControl && ! _.isUndefined( this.models[api.currentTargetElementId] ) ) {
                models = this.models[api.currentTargetElementId];
            }else{
                models = this.models[this.id];
            }

            _.each( models , function( val ){ 

                var $item = control.container.find('li.sed-bp-form-sortable-item[data-value="' + val + '"]');

                control.container.find('.sed-bp-form-sortable').append( $item );

            });

        }

    });

    var _escape = function ( text ) {
        text = text || '';
        text = text.replace( /&([^#])(?![a-z1-4]{1,8};)/gi, '&#038;$1' );
        return text.replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' ).replace( /'/g, '&#039;' );
    };

    api.CodeControl = api.SiteEditorControls.extend({

        _ready: function() {

			var control = this ,
				element = this.container.find('.sed-pb-codemirror-editor');

            this.code = element;

            this.isExpanded = false;

            this.codeOptions =  {
                value       : control.defaultValue ,
                styleActiveLine: true,
                lineNumbers: true,
                mode        : 'html' ,
                theme       : 'default' ,
                height      : '250px' ,
                selectionPointer: true
            };

            $.extend( this.codeOptions , control.params.code );

            this.canEncoded = false;

            if( !_.isUndefined(control.params.encoded) && control.params.encoded == "yes" ){

                this.canEncoded = true;

            }

            this._getCodeVal = function(){

                if( control.canEncoded === true ){

                    return api.fn.rawurlencode( control.editor.getValue() );

                }

                return control.editor.getValue();

            };

            // HTML mode requires a small hack because CodeMirror uses 'htmlmixed'.
            if ( 'html' === this.codeOptions.mode ) {
                this.codeOptions.mode = { name: 'htmlmixed' };
            }

			this.editor = CodeMirror.fromTextArea( element[0], this.codeOptions);

            setTimeout(function() {
                control.editor.refresh();
            },200);

            if( control.params.updateType == 'auto-change' ) {
                // On change make sure we infor the Customizer API
                this.editor.on('change', function () {
                    control.refresh( control._getCodeVal() );
                });
            }else{
                this.container.find(".sed-save-code-changes").on('click', function () {
                    control.refresh( control._getCodeVal() );
                });
            }

            if( $("#sed-current-code-editor-expanded").length == 0 ){
                $( '<input type="hidden" name="sed-current-code-editor-expanded" id="sed-current-code-editor-expanded" value="" />' ).appendTo("body");
            }

            this.container.find(".sed-resize-code-editor-toggle").on('click', function () {

                if( control.isExpanded === true ) {

                    control.toNormal();

                }else{

                    control.toExpand();

                }

            });

        },

        toNormal : function(){

            var control = this;

            control.editor.setSize( "100%" , control.codeOptions.height );

            $("#sed-dialog-settings").dialog("option", "width", 295);

            this.container.find(".sed-resize-code-editor-toggle").removeClass( "sed-expanded-toggle" );

            control.container.removeClass( "sed-code-editor-expanded" );

            $("#sed-current-code-editor-expanded").val( "" );

            $( "#sed-dialog-settings" ).removeClass( "sed-dialog-code-editor-expanded" );

            control.isExpanded = false;

        },

        toExpand : function(){

            var control = this;

            control.editor.setSize( "100%" , "470px" );

            $("#sed-dialog-settings").dialog("option", "width", 600);

            this.container.find(".sed-resize-code-editor-toggle").addClass( "sed-expanded-toggle" );

            control.container.addClass( "sed-code-editor-expanded" );

            $("#sed-current-code-editor-expanded").val( control.id );

            $( "#sed-dialog-settings" ).addClass( "sed-dialog-code-editor-expanded" );

            control.isExpanded = true;

        },

        _update: function( val ){

            if( this.canEncoded === true ){

                val = api.fn.rawurldecode( val );

            }

            this.editor.setValue( val );

        }

    });

    api.WpEditorControl = api.SiteEditorControls.extend({

        _ready: function() {

            var control = this ,
                editorToggleButton = this.container.find('.sed-open-wp-editor-btn');

            control.editorExpanded = new api.Value( false );

            editorToggleButton.on("click" , function(){

                var editor = tinyMCE.get( 'sed-wp-tinymce-text-editor' );

                control.editorExpanded.set( true );

                editor.focus();

            });

            var __refresh = function( value ) { 

                control.refresh( value );

                control.container.find('.sed-textarea-html-content').val( value );

            };

            var __onVisualEditorChange = function() {

                var value, editor;

                if ( control.editorSyncSuspended ) {
                    return;
                }

                editor = tinyMCE.get( 'sed-wp-tinymce-text-editor' );

                value = wp.editor.removep( editor.getContent() );

                control.editorSyncSuspended = true;

                __refresh( value );

                control.editorSyncSuspended = false;

            };

            var __onTextEditorChange = function() {

                if ( control.editorSyncSuspended ) {
                    return;
                }

                var content = switchEditors.wpautop( $( this ).val() );

                control.editorSyncSuspended = true;

                __refresh( content );

                control.editorSyncSuspended = false;

            };

            /**
             * Update the button text when the expanded state changes;
             * toggle editor visibility, and the binding of the editor
             * to the post setting.
             */
            control.editorExpanded.bind( function( expanded ) {

                var editor,
                    textarea = $( '#sed-wp-tinymce-text-editor' );

                editor = tinyMCE.get( 'sed-wp-tinymce-text-editor' );

                $( document.body ).toggleClass( 'sed-wp-text-editor-pane-open', expanded );

                if ( expanded ) {

                    var content = control.container.find('.sed-textarea-html-content').val();

                    if ( editor && ! editor.isHidden() ) {
                        editor.setContent( wp.editor.autop( content ) );
                    } else {
                        textarea.val( content );
                    }

                    editor.on( 'input change keyup', __onVisualEditorChange );

                    textarea.on( 'input', __onTextEditorChange );

                    var dialog = $("#sed-dialog-settings").parents(".ui-dialog:first");

                    $("#sed-wp-text-editor-pane").data( "currentControlId" , control.id );

                    var _docW = $(window).width() ,
                        offsetLeft  = dialog.offset().left ,
                        editorW = $("#sed-wp-text-editor-pane").outerWidth();

                    if( _docW - ( offsetLeft - 165 ) < editorW ){
                        offsetLeft = _docW - editorW;
                    }else{
                        offsetLeft = ( offsetLeft - 165 > 0 ) ? offsetLeft - 165 : 0;
                    }

                    $("#sed-wp-text-editor-pane").css({
                        "top"       : dialog.offset().top ,
                        "left"      : offsetLeft ,
                        "display"   : "block"
                    });

                } else {

                    editor.off( 'input change keyup', __onVisualEditorChange );

                    textarea.off( 'input', __onTextEditorChange );

                    // Cancel link and force a click event to exit fullscreen & kitchen sink mode.
                    editor.execCommand( 'wp_link_cancel' );

                    $( '.mce-active' ).click();

                    $("#sed-wp-text-editor-pane").hide();

                }
            });

        },

        _update: function( val ){

            val = ( !val ) ? '' : val;

            this.container.find('.sed-textarea-html-content').val( val );

        }

    });

    api.CustomFontsControl = api.SiteEditorControls.extend({

        _ready: function() {

            var control = this;

            this.mediaLibrary = null;

            this.uploadSelector = null;

            this.collection = _.isArray( control.defaultValue ) ? control.defaultValue : [];

            this.container.find('.sed-custom-fonts-accordion').accordion({
                active          : 0,
                collapsible     : true,
                event           : 'click',
                heightStyle     : 'content'
            });

            if( _.isEmpty( this.collection ) ){

                this.addNewFont();

            }

            this.container.find('.sed-new-custom-font-btn').on("click" , function(){

                control.addNewFont();

            });

            this.container.find('.upload-btn').livequery(function(){

                $(this).on( 'click' , function(e){

                    control.fontUpload( $(this) , e );

                });

            }, function() {
                // unbind the change event
                $(this).unbind('click');

            });

            this.container.find(".sed_custom_font_title").livequery(function(){

                $(this).on( 'keyup change' , function(){

                    $(this).parents(".sed-custom-font-fields:first").prev().find(".sed-font-heading").text( $(this).val() );

                    control.updateAttr( $(this).parents(".sed-custom-font-fields:first").data("fontId") , "font_title" , $(this).val() );

                });

            }, function() {
                // unbind the change event
                $(this).unbind('keyup change');
            });

            this.container.find(".sed_custom_font_family").livequery(function(){

                $(this).on( 'keyup change' , function(){

                    control.updateAttr( $(this).parents(".sed-custom-font-fields:first").data("fontId") , "font_family" , $(this).val() );

                });

            }, function() {
                // unbind the change event
                $(this).unbind('keyup change');

            });

            this.container.find(".sed-custom-font-accordion-delete").livequery(function(){

                $(this).on( 'click' , function(){

                    control._removeModel( $(this).parents(".sed-accordion-header:first").data("fontId") );

                    $(this).parents(".sed-accordion-header:first").next().remove();

                    $(this).parents(".sed-accordion-header:first").remove();

                    control.container.find('.sed-custom-fonts-accordion').accordion( "refresh" );

                });

            }, function() {
                // unbind the change event
                $(this).unbind('click');

            });

        } ,

        updateAttr : function( id , attr , value ){ //console.log( "----this.collection----" , this.collection );

            this.collection = _.map( this.collection , function( font ){

                if( font.id == id ){
                    font[attr] = value;
                }

                return font;

            });

            this._updateControl();

        },

        addNewFont : function(){

            var _model = {
                id              : this._uniqueId() ,
                font_title      : '' ,
                font_family     : '' ,
                font_woff       : '' ,
                font_ttf        : '' ,
                font_svg        : '' ,
                font_eot        : ''
            };

            var _template = api.template( 'sed-add-custom-font' ),
                html = _template( _model );

            this.container.find(".sed-custom-fonts-accordion").append( html );

            var _num = this.container.find('.sed-custom-font-fields').length - 1;

            this.container.find('.sed-custom-fonts-accordion').accordion( "option", "active", parseInt( _num ) );

            this.container.find('.sed-custom-fonts-accordion').accordion( "refresh" );

            this.collection.push( _model );

            this._updateControl();

        } ,
        
        _updateControl : function () {

            var newVal = $.extend( true , {} , this.collection );

            this.refresh( newVal );

        },

        _removeModel : function ( id ) {

            this.collection = _.filter( this.collection , function( font ){

                return font.id != id;

            });

            this._updateControl();

        },

        _uniqueId : function(){

            var _prefix = "sed_custom_font_";

            if( _.isEmpty( this.collection ) ){
                return _prefix + 1;
            }

            var _lastFont = _.max( this.collection , function( font ){
                return parseInt( font.id.replace( _prefix , "" ) );
            });

            var _newId = parseInt( _lastFont.id.replace( _prefix , "" ) ) + 1;

            return _prefix + _newId;

        },

        fontUpload : function( $this , event ){

            var control = this;

            event.preventDefault();

            this.uploadSelector = $this.parents(".row_settings:first");

            this._addFile( $this );

        },

        _addFile : function ( $this ) {

            var control = this,
                $el = $this;

            // If the media frame already exists, reopen it.
            if ( this.mediaLibrary ) {

                this.mediaLibrary.open();
                return ;

            } else {
                // Create the media frame.
                this.mediaLibrary = wp.media.frames.mediaLibrary =  wp.media({
                    // Set the title of the modal.
                    title: "Media Library",//$el.data('choose'),

                    // Customize the submit button.
                    button: {
                        // Set the text of the button.
                        text: "Update",//$el.data('update'),
                        // Tell the button not to close the modal, since we're
                        // going to refresh the page when the image is selected.
                        close: false
                    }
                });

                // When an image is selected, run a callback.
                this.mediaLibrary.on( 'select', function() {
                    // Grab the selected attachment.
                    var attachment = control.mediaLibrary.state().get('selection').first();

                    control.mediaLibrary.close();

                    //console.log( "-------------attachment---------------" , attachment );

                    var _fontType = control.uploadSelector.find('.upload-btn').data("fontType"),
                        _filename = attachment.attributes.filename,
                        _ext;

                    if( _fontType === "woff" ){

                        _ext = _filename.substring(_filename.length - 4);

                    }else{

                        _ext = _filename.substring(_filename.length - 3);

                    }

                    if( _ext != _fontType ){
                        alert( "Your selected file is invalid. please using from a ." +_fontType + " file" );
                        return ;
                    }

                    control.uploadSelector.find('.media-url-field').val( attachment.attributes.url );

                    control.updateAttr( control.uploadSelector.parents(".sed-custom-font-fields:first").data("fontId") , "font_" + _fontType , attachment.attributes.url );

                    /*selector.find('.upload-button').unbind().addClass('remove-file').removeClass('upload-button').val(optionsframework_l10n.remove);
                    /*selector.find('.of-background-properties').slideDown();
                    /*selector.find('.remove-image, .remove-file').on('click', function() {
                        optionsframework_remove_file( $(this).parents('.section') );
                    });*/
                });

            }

            // Finally, open the modal.
            this.mediaLibrary.open();
        }


    });

    /*api.PageOptionsScopeControl = api.SiteEditorControls.extend({

        _ready: function() {

            var control = this,
                element = control.container.find('.sed-module-element-control');

            this.element = element;

            this.element.on("change" , function(){

                var val = $(this).val() ,
                    pageId = $(this).data('pageId') ,
                    newVal = $.extend( true , {} , control.setting.get() );

                newVal[pageId] = val;

                control.refresh( newVal );

            });

        } ,

        _update: function( val ){ //alert( val );

            val = ( !val ) ? '' : val;

            this.element.filter( function() {
                return this.value === val;
            }).prop( 'checked', true );

        }

    });*/


    api.controlConstructor = $.extend( api.controlConstructor, {
        slider             : api.SliderControl,
        sortable           : api.SortableControl,
        date               : api.DateControl,
        dimension          : api.DimensionControl,
        code               : api.CodeControl,
        custom_font        : api.CustomFontsControl,
        "multi-color"      : api.MulticolorControl,
        "wp-editor"        : api.WpEditorControl
        //page_options_scope : api.PageOptionsScopeControl
    });



    $( function() {

        $("#sed-wp-text-editor-pane").draggable({
            handle: "#sed-wp-editor-dragbar" ,
            containment: "window" ,
            drag : function( event, ui ){
                $("#sed-dialog-settings").parents(".ui-dialog:first").css({
                    "top"   : ui.position.top ,
                    "left"  : ui.position.left + 165
                });
            }
        });

        api.Events.bind( "beforeResetSettingsTmpl" , function( settingsId , settingsType ){

            //if actived wp editor
            if( $( document.body ).hasClass( 'sed-wp-text-editor-pane-open' ) ){

                var cId = $("#sed-wp-text-editor-pane").data( "currentControlId" ) ,
                    control = api.control.instance( cId );

                control.editorExpanded.set( false );
            }

            if( $("#sed-current-code-editor-expanded").length > 0 && !_.isEmpty( $("#sed-current-code-editor-expanded").val() ) ){

                var ccId = $("#sed-current-code-editor-expanded").val(),
                    codeControl = api.control.instance( ccId );

                codeControl.toNormal();

            }

            var w = $( "#sed-dialog-settings" ).dialog( "option" , "width" );

            if( w > 295 )
                $( "#sed-dialog-settings" ).dialog( "option" , "width", 295 );

        });

        $("#sed-wp-text-editor-pane").find(".sed-close-wp-editor").on("click" , function(){

            var cId = $("#sed-wp-text-editor-pane").data( "currentControlId" ) ,
                control = api.control.instance( cId );

            control.editorExpanded.set( false );

        });

        //for select2 control
        $("select.sed-select2.sed-element-control").livequery(function(){
            $(this).select2({
                placeholder: 'Select an option' ,
                allowClear: true
            });
        });


        //update Custom Fonts for 'font-family' control types
        _.each( [ "afterAppendSettingsTmpl" , "endInitAppendSettingsTmpl" ] , function( _EvSettingsAppend ){

            api.Events.bind( _EvSettingsAppend , function( $dialog , settingsType , settingsId ){

                if( $dialog.find(".sed-control-font-family").length > 0 ) {

                    _.each( api.sedGroupControls[settingsId] , function( data ){

                        if( $( "#sed-app-control-" + data.control_id ).find(".sed-control-font-family").length > 0 ) {

                            var customFonts = api( 'sed_custom_fonts' )();

                            var _validCustomFonts = {};

                            _.each ( customFonts , function( model ){

                                if( !_.isEmpty( model.id ) && !_.isEmpty( model.font_title ) && !_.isEmpty( model.font_family ) && !_.isEmpty( model.font_woff ) && !_.isEmpty( model.font_ttf ) && !_.isEmpty( model.font_svg ) && !_.isEmpty( model.font_eot ) ){

                                    _validCustomFonts[model.font_family] = model.font_title;

                                }

                            });

                            var _currentCustomFontEl = $("#sed-app-control-" + data.control_id).find(".sed-control-font-family .custom_fonts option").filter(":selected");

                            var _currentCustomFont = _currentCustomFontEl.length > 0 ? _currentCustomFontEl.val() : false;

                            var _customFontsString = "";

                            $.each( _validCustomFonts , function( _key , _title ){

                                var _selected = _currentCustomFont === _key ? 'selected="selected"' : '';

                                _customFontsString += '<option value="' + _key + '" ' + _selected + '>' + _title + '</option>';

                            });
                            
                            $("#sed-app-control-" + data.control_id).find(".sed-control-font-family .custom_fonts").html( _customFontsString );

                            /*var control = api.control.instance( data.control_id );

                            if ( !_.isUndefined( control ) ) {

                                //control.currentValue = control.setting();

                                var currVal = control.currentValue;

                                control.update( currVal );

                            }*/

                        }

                    });

                }

            });

        });


    });


})( sedApp, jQuery );