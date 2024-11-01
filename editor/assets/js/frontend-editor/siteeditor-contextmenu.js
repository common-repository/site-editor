(function( exports, $ ) {

    var api = sedApp.editor ;
    api.currentModuleElement = api.currentModuleElement || "";

    api.SiteEditorContextmenu = api.Class.extend({
        initialize: function( id , options ){
            var contextmenu = this;

            this.params = {};
            $.extend( this, options || {} );

            contextmenu.ready();
        },

        ready: function() {},

        send: function( id, data ) {
            this.preview.send( id , data);
        }

    });

    //this class is @deprecated
    api.RowContextmenu = api.SiteEditorContextmenu.extend({
        ready : function( ){
            var contextmenu = this;
            //start contextmenu
            contextmenu.preview.bind( 'contextmenu', function( ctxtm ) {
                var $thisElement , settingsValid = ctxtm.settingsValid;

                //$(contextmenu.params.selector).livequery(function(){
                    $(contextmenu.params.selector).jeegoocontext(contextmenu.params.menu_id , {
                        onShow: function(e, context){

                            if(api.appPreview.mode == "on")
                                return false;

                            $thisElement = $(context);

                            
							var module_container = $thisElement.parents(".sed-pb-module-container:first");
								
							//contextmenu.preview.send("currentElementId" , '[sed_model_id="' + $thisElement.attr("sed_model_id") + '"]' );
							
                            var role = $thisElement.attr("sed-role") || "custom-row",
                                flength = $("[sed-role='footer']").length,
                                hlength = $("[sed-role='header']").length;

                            $(".element-settings" , '#main_row_contextmenu').attr("sed-dialog" , "dialog-settings-" + role);
                            if(!settingsValid[role] || settingsValid[role] === false)
                                $(".element-settings" , '#main_row_contextmenu').addClass("context-menu-item-disable");
                            else
                                $(".element-settings" , '#main_row_contextmenu').removeClass("context-menu-item-disable");

                            $(".row-type-item" , '#main_row_contextmenu').each(function(){
                                if(flength == 1 && role != "footer" && $(this).attr("sed-value") == "footer")
                                    $(this).addClass("context-menu-item-disable");
                                else if($(this).attr("sed-value") == "footer")
                                    $(this).removeClass("context-menu-item-disable");

                                if(hlength == 1 && role != "header" && $(this).attr("sed-value") == "header")
                                    $(this).addClass("context-menu-item-disable");
                                 else if($(this).attr("sed-value") == "header")
                                    $(this).removeClass("context-menu-item-disable");

                                if(role == $(this).attr("sed-value")){
                                    $(this).addClass("row-type-item-active");
                                }else{
                                    $(this).removeClass("row-type-item-active");
                                }
                            });

                        }
                    });
                //});

                //for row Type item in contextmenu
                $(".row-type-item").on("click", function(){
                    if(!$(this).hasClass("context-menu-item-disable")){
                        var value = $(this).attr("sed-value");
                        $thisElement.attr("sed-role" , value);
                        $(".row-type-item").removeClass("row-type-item-active");
                        $(this).addClass("row-type-item-active");

                        $(".element-settings" , $thisMenu).attr("sed-dialog" , "dialog-settings-" + value);

                        if(!settingsValid[value] || settingsValid[value] === false)
                            $(this).parents(".context-menu-root:first").find(".element-settings").addClass("context-menu-item-disable");
                        else
                            $(this).parents(".context-menu-root:first").find(".element-settings").removeClass("context-menu-item-disable");

                        contextmenu.send( 'change_role_row' , {id : $thisElement.attr("sed_model_id"), role : value});
                    }
                });

            });
        }
    });

 /*
 @-- data-contextmenu-post-id is required for all modules when have one post edit button in dialog settings
 @-- all data-contextmenu-{%s} send to contextmenus
 @-- all data-contextmenu-{%s} just only defined in html tag when html tag is module container and have module id ($id)
 @-- files ----
    siteeditor-contextmenu.min.js line 115 ::: add data-contextmenu-{%s} to any contextmenu item like
    settings
    for data-contextmenu-post-id :: in line 166 plugins/contextmenu/plugin.js
    add data-post-id to any post edit buttons
    using data-post-id in in line 166 plugins/posts/plugin.js  in line 109 when click post edit button
 */

    api.ElementContextmenu = api.SiteEditorContextmenu.extend({
        ready : function( ){
            var contextmenu = this ,
                mClass = "module_" + contextmenu.params.shortcode + "_contextmenu",
                selector = "." + mClass , //contextmenu.params.selector
                cClass = "module_" + contextmenu.params.shortcode + "_contextmenu_container",
                containerSelector = "." + cClass ,
                coClass = contextmenu.params.menu_id + "_cover" ,
                coverSelector = "." + coClass ;

            $( selector + "," + containerSelector + "," + coverSelector ).jeegoocontext(contextmenu.params.menu_id , {
                onShow: function(e, context){

                    var preventShow = false ;

                    /*$.each([ "setting_btn" ] , function( idx , clss ){
                        if( $(context).find( "." + clss ).is(e.target) )
                            preventShow = true;
                    });*/

                    if(api.appPreview.mode == "on" ||  preventShow === true )
                        return false;

                    context = contextmenu.getModuleContext( context );

                    if( $( context ).length > 0 ){
                        $(this).find( "li.contextmenu-item-container" ).each(function( index , element){
                            _.extend( $(this).data() , contextmenu.getDataContextMenu( context ) || {});
                        });
                    }


                    api.contextmenu.onShow(e, context , $(this));
                },
                onSelect: function(e, context){
                    context = contextmenu.getModuleContext( context );

                    api.contextmenu.onSelect(e, context , $(this));
                }
            });

           /* $( containerSelector + " .drag_pb_btn" ).jeegoocontext(contextmenu.params.menu_id , {

            });*/

        },

        getModuleContext : function( context ){
            var contextmenu = this ,
                mClass = "module_" + contextmenu.params.shortcode + "_contextmenu",
                selector = "." + mClass , //contextmenu.params.selector
                cClass = "module_" + contextmenu.params.shortcode + "_contextmenu_container",
                coClass = contextmenu.params.menu_id + "_cover";

            if( $(context).hasClass(cClass) )
                context = $(context).find(selector + ":first");
            else if( $(context).hasClass(coClass) )
                context = $(context).siblings(selector + ":first");

            return context;
        },

        getDataContextMenu : function( context ){
            var dataContextMenu = {};
            $.each( $(context).data() , function(key , value){
                var patt = /^contextmenu(.*)/g ;
                if( patt.test( key ) )
                    dataContextMenu[key] = value;
            });

            return dataContextMenu ;
        }

    });


    api.Contextmenu = api.Class.extend({
        initialize: function( params , options ){
            var contextmenu = this;

            this.params = params;
            $.extend( this, options || {} );

            contextmenu.ready();
        },

        ready: function() {},

        onShow : function( event , context , element ){

            $thisElement = $( context );
            api.selectPlugin.select( $( context ) );

            var arrangementItem = element.find(".modules-arrangement");

            var parent = $thisElement.parents('[sed-layout-role="pb-module"]:first');

            if(parent.attr("data-type-row") == "static-element")
                arrangementItem.addClass("contextmenu-hide-item");
            else if(parent.attr("data-type-row") == "draggable-element")
                arrangementItem.removeClass("contextmenu-hide-item");

            api.Events.trigger( "sedShowContextmenu" , event , context , element );

            //this.preview.send("currentElementId" , $thisElement.attr("sed_model_id"));

        },
        /****
        data-actions :
        1.open-dialog
        2.edit-style
        3.widget-open-dialog
        4.bringToFront
        5.bringForward
        6.sendBackward
        7.sendToBack
        8.delete
        9.lock
        10.copy
        11.paste
        12.undo
        13.redo
        14.openMediaLibrary
        ****/
        onSelect : function( event , context , element ){
            var action = element.data("action");
            if(action)
                api.Events.trigger( "ctxtAct_" + action , event , context , element );
        }

    });

        //end contextmenu
    api.contextmenuConstructor = {
        row : api.RowContextmenu ,
        element : api.ElementContextmenu
    };

    $( function() {
        var sedContextmenu ;
        api.settings = window._sedAppEditorSettings;

        $.each( api.contextMenuSettings , function( id, data ){
			var constructor = api.contextmenuConstructor[ data.type ] || api.SiteEditorContextmenu,
				contextmenu;

			contextmenu = new constructor( id, {
				params: data,
				preview : api.preview
			});
        });


        api.contextmenu = new api.Contextmenu( {}, {
            preview : api.preview
        });

        $.each( api.itemContextMenuSettings , function( id, item ){
            $( "#" + id ).data( "options", item.options );
        });

        /*$('.sed-handle-sort-row .setting_btn').livequery( function(){
            $(this).click(function(){
                var context = $(this).parents(".sed-row-pb.sed-pb-module-container:first").find(">.sed-pb-module-container .sed-pb-module-container:first");
                api.selectPlugin.select( context , true );
            });
        });*/


        $(".element-open-dialog").livequery(function(){
            $(this).on("click", function(){
                if(!$(this).hasClass("context-menu-item-disable")){

                    var id = $thisElement.attr("sed_model_id");//api.log( id );  //api.log( $(this).attr("sed-dialog-id") );
                    api.preview.send( 'element_open_dialog' , {
                        tmpl      :  $(this).attr("sed-dialog-tmpl-id"),
                        selector  :  $(this).attr("sed-dialog-selector") ,
                        data      :  $(this).data(),
                        id        :  $(this).attr("sed-dialog-id"),
                        options   :  $(this).data( "options" ),
                        extra     :  {
                            attrs : api.contentBuilder.getAttrs( id , true ) || {}
                        }
                    });

                    //api.styleCurrentSelector = "#" + id;

                    $(document).trigger( $(this).attr("sed-dialog-id") , [this]);
                    //api.preview.send( 'currentElementId' , $thisElement.attr("sed_model_id"));
                }
            });

            //if($thisElement)
                //$thisElement.contextMenu("hide");
        });

        api.Events.bind( "ctxtAct_openDialogSettings"  , function(event , context , element){

            var id = $thisElement.attr("sed_model_id");

            api.preview.send( 'openDialogSettings' , {
                selector        :  element.attr("sed-dialog-selector") ,
                data            :  element.data(),
                extra :  {
                    attrs : api.contentBuilder.getAttrs( id , true ) || {}
                },

            });

            api.pageBuilder.sendRowData( $thisElement );

        });

        $('[data-action="formElement"]').livequery(function(){
             $(this).on("click", function( e ){
                e.stopPropagation();
             });
        });

        api.Events.bind( "ctxtAct_duplicate"  , function(event , context , element){

            var id = $thisElement.attr("sed_model_id");    //api.contentBuilder.getAttrs( id )
                                  // element.attr("sed-dialog-selector")   element.data()
           // $thisElement

            api.duplicate( $thisElement );


        });

       /* api.Events.bind( "ctxtAct_formElement" , function( event , context , element ){
            return false;
        });*/

        /*$(".widget-element-open-dialog").on("click", function(){
            if(!$(this).hasClass("context-menu-item-disable")){

                var idBase = $thisElement.data('widgetIdBase') ,
                    tpl = $("#widget-tpl-" + idBase ) ,
                    phpClass = tpl.find('[name="php_class"]').val();

                if(!phpClass){
                    $(this).addClass('context-menu-item-disable');
                    return ;
                }

                api.preview.send( 'widget_element_open_dialog' ,  $thisElement.data() );

                //api.styleCurrentSelector = "#" + $thisElement.attr("id");

                //$(document).trigger( $(this).attr("sed-dialog-id") , [this]);
                //api.preview.send( 'currentElementId' , $thisElement.attr("id"));
            }
            //$thisElement.contextMenu("hide");
        });


        $(".element-edit-styles").on("click", function(){
            if(!$(this).hasClass("context-menu-item-disable")){
                api.preview.send( 'call_style_editor' , "#" + $thisElement.attr("id"));
                //api.styleCurrentSelector = "#" + $thisElement.attr("id");
                //api.preview.send( 'currentElementId' , $thisElement.attr("id"));
                api.currentModuleElement = $thisElement;
            }
            $(document).trigger("elementEditStyles" , [this]);
            //$thisElement.contextMenu("hide");
        });  */
                //$(".some-selector").contextMenu("hide");
        /*
         attachment_id
         image_url
        */

        var _openLibraryMediaGallery = function( element , options ){
            var post_id = api.pageBuilder.getPostId( element ) ,
            containerId = element.parents(".sed-pb-module-container:first").attr("sed_model_id"),
            shortcode_models = api.contentBuilder.findAllTreeChildrenShortcode( containerId , post_id ) ,
            media_attrs = options.media_attrs;

            shortcode_models = _.map( shortcode_models , function( shortcode ){
                if(shortcode.id == element.attr("sed_model_id")){
                    var shortcode_info = api.shortcodes[shortcode.tag];
                    shortcode.attrs = $.extend({} , shortcode_info.attrs , shortcode.attrs);
                }

                return shortcode;
            });

            //api.log( shortcode_models );
            api.preview.send( 'openMediaLibrary' , {
                moduleId            : element.attr("sed_model_id") ,
                options             : options,
                models              : shortcode_models,
                moduleContainerId   : containerId
            });
        };


        api.Events.bind( "ctxtAct_openMediaLibrary"  , function(event , context , element){
            var options = element.data("options");
            _openLibraryMediaGallery( $(context) , options );

        });

        api.preview.bind("openMediaLibraryEditGallery" , function( options ){
            _openLibraryMediaGallery( $( '[sed_model_id="' + api.currentSedElementId + '"]' ) , { 'media' : options } );
        });

        api.preview.bind("updateMediaListModule" , function( data ){
            ////api.log( data );
            var postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + data.moduleContainerId + '"]' ) ) ,
                shortcode = api.contentBuilder.getShortcode( data.moduleContainerId ) ,
                syncMediaAttachments = [];

            api.contentBuilder.deleteModuleTreeChildren( data.moduleContainerId , postId );
            api.contentBuilder.addShortcodesToParent( data.moduleContainerId , data.shortcodes , postId );

            var _modifyAddModelPattern = function(){

                api.contentBuilder.contentModel[postId] = _.map( api.contentBuilder.contentModel[postId] , function(shortcode){

                    if(shortcode.newModel === true){

                        var id = api.pageBuilder.getNewId( );

                        ////update post_id attr and other in sed_{{media}} shortcode
                        if( !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.sed_main_media) && shortcode.attrs.sed_main_media ){

                            syncMediaAttachments.push({
                                attachment    : $.extend(true, {}, shortcode.attrs.attachment_model) ,
                                targetElement : id ,
                                shortcode     : shortcode.tag
                            });

                             delete shortcode.attrs.attachment_model;

                        }

                        api.contentBuilder.contentModel[postId] = _.map( api.contentBuilder.contentModel[postId] , function(childShortcode){
                            if(shortcode.id === childShortcode.parent_id){
                                childShortcode.parent_id = id;
                            }
                            return childShortcode;
                        });

                        shortcode.id = id;
                        shortcode.attrs.sed_model_id = id;
                        delete shortcode.newModel;
                    }

                    return shortcode;
                });

            };

            _modifyAddModelPattern();

            //remove newModel from shortcodes models
            //api.contentBuilder.postsContent[postId] = _.omit(api.contentBuilder.postsContent[postId]  , "newModel" , "cid");
            //var newArr = _.map(arr, function(o) { return _.omit(o, 'c'); });

            _.each( syncMediaAttachments , function(data){
                api.preview.trigger( "syncMediaAttachments" , data );
            });

            api.contentBuilder.refreshModule( data.moduleId );

        });


    });

}(sedApp, jQuery));