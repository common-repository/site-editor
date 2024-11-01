(function( exports, $ ) {

    var api = sedApp.editor ;

    api.currentModule = api.currentModule || "";

    api.isOpenDialogSettings = api.isOpenDialogSettings || false;

    api.SelectPlugin = api.Class.extend({

        initialize: function( params , options ){
            var self = this;

            $.extend( this, options || {} );

            this.ready();

            this.elementId = null;
        },

        ready : function(){
            var self = this;
                                                                               //.is(".drag_pb_btn")|| $(e.target).is(".drag_pb_btn")
            $( ".sed-pb-module-container" ).livequery(function(){
                $(this).on("click" , function(e){     // $(this).is(".drag_pb_btn")
                    //api.styleEditor.editorState == "on" ||
                    if( self.resizing === true || api.appPreview.mode == "on" || $(this).is("[sed-disable-editing='yes']") )
                        return ;

                    e.preventDefault();
                    e.stopPropagation();

                    //api.hideContextmenu(e);  //api.log( $(this) );
                    self.select( $(this) , $(e.target).hasClass("sed_setting_btn_cmp") );

                });
            });

            $('body').on('click', function (e) {

                if( ! $(this).hasClass("sed-pb-module-container") && $(this).parents(".sed-pb-module-container").length == 0 ) {

                    api.preview.send('dialogSettingsClose');
                    api.isOpenDialogSettings = false;
                    api.currentSedElementId = "";

                }

            });

        },

        select : function( element , forceOpen , reset , ofrceRefresh ){

            if(api.appPreview.mode == "on")
                return false;
                                  
            var self = this;

            forceOpen = _.isUndefined( forceOpen ) ? false : forceOpen;

            ofrceRefresh = _.isUndefined( ofrceRefresh ) ? false : ofrceRefresh;

            if( element.hasClass( 'sed-static-module' ) ){

                api.preview.send( "openAppSettings" , {
                    settingId : element.data( "staticModuleId" ) ,
                    forceOpen : forceOpen ,
                    reset     : reset
                });

                return ;
            }
                                ////api.log( element.hasClass("sed-pb-module") );
            //sync module parent with first child module
            //alert( element.hasClass("sed-bp-module") );
            if( element.hasClass("sed-bp-module") ){
                element = element.find(".sed-pb-module-container:first");
            }

            if( element.hasClass("sed-row-pb") ){
                element = element.find(">.sed-pb-module-container .sed-pb-module-container:first");
            }

            var elementId = element.attr("sed_model_id");

			var module_container = element.parents(".sed-pb-module-container:first");

            if(!elementId || ( api.currentSedElementId == elementId && api.isOpenDialogSettings === true && ofrceRefresh === false ) ){
            	//api.log("Error : id and module class Should exist in one container on line 28  : siteeditor/site-iframe/plugin/select.min.js" , elementId);
                return ;
            }

            var shortcode = api.contentBuilder.getShortcode( elementId );

            if(!shortcode){
            	//api.log("Error : this id : " + elementId + " not valid module id on line 37 : siteeditor/site-iframe/plugin/select.min.js" , shortcode);
                return ;
            }


            var shortcode_info = api.shortcodes[shortcode.tag];

            api.currentModule =  shortcode_info.moduleName;

            api.preview.send( 'currentElementId' , elementId);

            api.currentSedElementId = elementId;

            api.preview.send( 'currentPostId' , $( '[sed_model_id="' + elementId + '"]' ).parents(".sed-pb-post-container:first").data("postId") );

            //api.Events.trigger("openUpdateModuleSettings" , shortcode , elementId);

            api.pageBuilder.sendRowData( element );

            var dialogData = $.extend( {} , this.getDataContextMenu( element ) || {} , {
                shortcodeName : shortcode.tag
            });

            reset =  !_.isUndefined( reset ) ? reset : true; 

            api.preview.send( 'currentModuleSelected' , {
                selector  :  shortcode.tag ,    //"#sed-dialog-settings-" +
                forceOpen   : forceOpen ,
                reset : reset ,
                data : dialogData ,
                extra :  {
                    attrs : api.contentBuilder.getAttrs( elementId , true )
                }
            });

            /*
            api.preview.send( 'element_open_dialog' , {
                tmpl      :  "tmpl-dialog-settings-" + shortcode.name,
                selector  :  "#sed-dialog-settings" ,
                data      : {
                    shortcodeName   : shortcode.name ,
                    dialogTmplType  : "dynamic"
                },
                id    :  "sedDialogSettings" ,
                extra :  {
                    attrs : $.extend({} ,  api.contentBuilder.getAttrs( id ) || {} )
                },
                options   :  {
                    dialog_options   :  {
                        "autoOpen"  : false,
                        "modal"     : false,
                        "width"     : 295,
                        "height"    : 600 ,
                        "position"  : {
                            "my"    : "right-20",
                            "at"    : "right" ,
                            "of"    : "#sed-site-preview"
                        }
                    }
                },
            }); */

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


    $( function() {

        api.selectPlugin = new api.SelectPlugin();

    });

}(sedApp, jQuery));