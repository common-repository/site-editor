/**
 * plugin.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){

  var api = sedApp.editor;

    api.fn.pageBuilderModuleExist = function ( role ) {

        var selectorT = '[sed_role="' + role + '"]',
            moduleElement = $("#website")[0].contentWindow.jQuery( selectorT ); 

        return moduleElement.length;

    };

  $( function() {
      api.settings = window._sedAppEditorSettings;
      api.l10n = window._sedAppEditorControlsL10n;
      api.postsContent = api.postsContent || {};
      api.pagesThemeContent = api.pagesThemeContent || {};
      api.modules = {};
      api.childShortcode = {};
      api.moduleDragSync = false;

      api.shortcodes          = window._sedRegisteredShortcodesSettings ;    //console.log( "-------------api.shortcodes--------------" , api.shortcodes );
      api.shortcodesScripts   = window._sedRegisteredShortcodesScripts;
      api.shortcodesStyles    = window._sedRegisteredShortcodesStyles;
      api.modulesSettings     = window._sedAppEditorPageBuilderModules;
      api.defaultPatterns     = window._sedShortcodesDefaultPatterns ;   //console.log( "----api.defaultPatterns----" , api.defaultPatterns );
      //only javascript files using in editor and not loaded in front end
      api.ModulesEditorJs     = window._sedAppJsModulesForEditor;

      //api.modulesInfo         = window._sedAppPageBuilderModulesInfo;


                                 //api.log( api.settings );
      // Check if we can run the customizer.
      if ( ! api.settings )
      	return;

      // Redirect to the fallback preview if any incompatibilities are found.
      if ( ! $.support.postMessage || (  api.settings.isCrossDomain ) )  //! $.support.cors &&
      	return window.location = api.settings.url.fallback;

        /*
        send  api.shortcodes to iframe
        send  api.shortcodes to iframe
        send  api.shortcodes to iframe
        send  api.shortcodes to iframe
        */
        api.previewer.bind( 'previewerActive', function( ) {

            api.previewer.send( "sed_api_shortcodes" , api.shortcodes );

            api.previewer.send( "sed_api_shortcodes_scripts" , api.shortcodesScripts );

            api.previewer.send( "sed_api_shortcodes_styles" , api.shortcodesStyles );

            api.previewer.send( "sed_api_modules_settings" , api.modulesSettings );

            api.previewer.send( "sed_api_modules_editor_js" , api.ModulesEditorJs );
            
            if( api.currentModuleForceToSelect ){

                api.previewer.send( "sed_force_select_module" , api.currentModuleForceToSelect );

                api.currentModuleForceToSelect = "";

            }

        });

        api.previewer.bind( 'checkModuleDragSync', function( ) {

            if(api.moduleDragSync === true)
                $.SiteEditorDroppable.destroy('[data-type-row="draggable-element"]' , "website" );

            var _getAttrs = function(element){
                var attrs = {};
                $.each(element.attributes, function() {
                  if(this.specified) {
                    attrs[this.name] = this.value;
                  }
                });
                return attrs;
            };

            $.sedDroppable('[data-type-row="draggable-element"]',{
                iframe : "website",
                dragStart : function(event , element, helper){
                    //parent.jQuery("#iframe_cover").show();
                },
                stop : function(event , element, helper){
                    //parent.jQuery("#iframe_cover").hide();
                },
                drop : function(event, ui){

                    var attrs = _getAttrs( ui.helper[0] );

                    api.previewer.send( 'moduleDragHandler' , {
                        option :  "drop" ,
                        args   :  {
                            //event : event ,
                            ui    : {
                                offset : ui.offset
                            } ,
                            element : {
                                attrs :  attrs ,
                                data  :  ui.helper.data()
                            }
                        } ,
                        mode   : api.moduleDragSync
                    });
                }
            });

            if(api.moduleDragSync === true)
                return ;

            //$( ".sed-module-pb" ).sedDraggable.destroy();

             //draggable modules in modules tab and drop in page content
            $( ".sed-module-pb" ).sedDraggable({
                scrollSensitivity : 30,
                scrollSpeed : 30,
                dropInSortable: ".sed-pb-component,.sed-pb-main-component",
                items: ".sed-row-pb" ,    //children only
                cancelSortable : '[data-type-row="draggable-element"],[sed-disable-editing="yes"]',
                iframeSortable: "website",
                placeholder:"<div class='sed-state-highlight-row'></div>",
                dragStart : function(event , element, helper){
                    $("#iframe_cover").show();
                    api.previewer.send("moduleDragStart"); 

                    api.Events.trigger( "moduleDragStartEvent" , helper.attr("sed-module-name") );
                },
                stop : function(event , element, helper){
                    $("#iframe_cover").hide();
                },
                out : function(event, sortableArea , options ) {
                    var rows;

                    if( options.items !== false ){
                        rows = sortableArea.children(options.items).not('[data-type-row="draggable-element"]' );
                    }else{
                        rows = sortableArea.children().not('[data-type-row="draggable-element"]' );
                    }

                    if(rows.length == 0 && !sortableArea.hasClass("sed-pb-empty-sortable-area") ){
                         sortableArea.addClass("sed-pb-empty-sortable-area");
                    }
                },
                over : function(event, sortableArea , options ) {
                    if( sortableArea.hasClass("sed-pb-empty-sortable-area") ){
                        ////api.log("sortableArea");
                        sortableArea.removeClass("sed-pb-empty-sortable-area");
                    }
                },
                sortStop : function(event,ui) {

                    var attrs = _getAttrs( ui.helper[0] );

                    if( !_.isUndefined( ui.sortable.data("parentModule") ) ){
                        var modules = ui.sortable.data("modulesAccepted");
                        modules = modules.split(",");

                        modules = _.map( modules , function( module ){
                            return $.trim(module);
                        });

                        modules = _.filter( modules , function( module ){
                            return module;
                        });

                        if( $.inArray( attrs["sed-module-name"] , modules ) == -1 ){
                            alert( ui.sortable.data("modulesAcceptedError") );
                            api.previewer.send("moduleDragStop");
                            return ;
                        }
                    }

                    api.previewer.send( 'moduleDragHandler' , {
                        option :  "sortStop" ,
                        args   :  {
                            //event : event ,
                            ui    : {
                                direction : ui.direction
                            } ,
                            //item  : ui.item ,
                            element : {
                                attrs :  attrs ,
                                data  :  ui.helper.data()
                            }
                        } ,
                        mode   : api.moduleDragSync
                    });

                    api.previewer.send("moduleDragStop");
                }
            });

            if(api.moduleDragSync === false)
                api.moduleDragSync = true;

        });

      api.previewer.bind( 'page_custom_design_settings' , function( sedCss ){

          var settingId;

          if( api.settings.page.type == "post" ) {

              settingId = "postmeta[" + api.settings.currentPostType + "][" + api.settings.page.id + "][page_custom_design_settings]";


          }else{

              settingId = "sed_" + api.settings.page.id + "_settings[page_custom_design_settings]";

          }

          api( settingId ).set( sedCss );

      });

      api.previewer.bind( 'site_custom_design_settings' , function( sedCss ){

          api( 'site_custom_design_settings' ).set( sedCss );

      });

       api.previewer.bind("resetpageInfoSettings" , function( data ){

          if( _.isUndefined(data.pageId) || _.isUndefined(data.pageType)  )
              return ;

           api.settings.page.id = data.pageId;
           
           api.settings.page.type = data.pageType;

           api.settings.currentPostType = data.postType;

           api.Events.trigger( "afterResetpageInfoSettings" );

       });

       api.previewer.bind("currentPageInfo" , function( data ){
           api.currentPageInfo = data;

           if( api.currentPageInfo.isHome === true && api.currentPageInfo.isFrontPage === true ){
               $(".home-blog-control").parents("td:first").show();
               api.isHomeBlog = true;
           }else{
               $(".home-blog-control").parents("td:first").hide();
               api.isHomeBlog = false;
           }

           if( api.currentPageInfo.isHome === false && api.currentPageInfo.isFrontPage === true ){
               $(".home-page-control").parents("td:first").show();
               api.isHomePage = true;
           }else{
               $(".home-page-control").parents("td:first").hide();
               api.isHomePage = false;
           }

           if( api.currentPageInfo.isHome === true && api.currentPageInfo.isFrontPage === false ){
               $(".index-blog-control").parents("td:first").show();
               api.isIndexBlog = true;
           }else{
               $(".index-blog-control").parents("td:first").hide();
               api.isIndexBlog = false;
           }

           if( ( api.currentPageInfo.isHome === true && api.currentPageInfo.isFrontPage === true ) || ( api.currentPageInfo.isHome === false && api.currentPageInfo.isFrontPage === true ) ){
               $(".home-control").parents("td:first").show();
               api.isHome = true;
           }else{
               $(".home-control").parents("td:first").hide();
               api.isHome = false;
           }

       });

        api.previewer.bind( 'posts_content_ready', function( postsContent ) {

            if( !_.isUndefined( postsContent ) ) {

                var contents = $.extend( true , {} , postsContent );

                $.each( contents , function ( post_id, models ) { 
                    api.postsContent[post_id] = models;
                });
            }

        });

        api.previewer.bind( 'posts_content_update', function( postsContent ) {

            api.trigger("change");

            if( !_.isUndefined( postsContent ) ) {

                var contents = $.extend( true , {} , postsContent ); //console.log( "-------contents-------" , contents );

                $.each( contents , function ( post_id, models ) { 
                    api.postsContent[post_id] = models;
                });
            }
            
        });

      /*var _filterDataSettings = function( currentSettingId , settings ){

          var copySettings = $.extend( {} , true , settings );

          _.each( settings , function( args , id ){

              //remove Current theme_content setting id from here and add this later
              if( id == currentSettingId ){
                  delete copySettings[ id ];
              }

          });

          return copySettings;

      };

      api.addFilter( "GeneralOptionsDataSettings" , function( settings ){

          if( api.settings.page.type == "post" )
              return settings;

          alert( api.settings.page.type );

          var currentSettingId =  "sed_" + api.settings.page.id + "_settings[theme_content]";

          return _filterDataSettings( currentSettingId , settings );

      });

      api.addFilter( "postOptionsDataSettings" , function( settings , postType ){

          if( api.settings.page.type != "post" )
              return settings;

          //alert( postType );

          var currentSettingId =  "postmeta[" + postType + "][" + api.settings.page.id + "][theme_content]";

          return _filterDataSettings( currentSettingId , settings );

      });*/

      api.previewer.bind( 'syncPreLoadSettings', function( dataSettings ) {

          _.each( dataSettings , function( settingArgs, id ) {

              if ( ! api.has( id ) ) {
                  var setting = api.create( id, id, settingArgs.value, {
                      transport   : settingArgs.transport || "refresh",
                      previewer   : api.previewer,
                      stype       : "general" ,
                      dirty       : settingArgs.dirty
                  } );

                  api.settings.settings[id] = settingArgs;

                  if ( settingArgs.dirty ) {
                      setting.callbacks.fireWith( setting, [ setting.get(), {} ] );
                  }

              }

          } );

      });

        api.previewer.bind( 'pages_theme_content_ready', function( obj ) { //alert("test....");

            if( api.settings.page.type == "post" ) { //alert( obj.postType );

                api.currentPageThemeContentSettingId = "postmeta[" + obj.postType + "][" + api.settings.page.id + "][theme_content]";

                api.currentPageLayoutSettingId = "postmeta[" + obj.postType + "][" + api.settings.page.id + "][page_layout]";

                api.settings.currentPostType = obj.postType;

            }else{

                api.currentPageThemeContentSettingId = "sed_" + api.settings.page.id + "_settings[theme_content]";

                api.currentPageLayoutSettingId = "sed_" + api.settings.page.id + "_settings[page_layout]";

            }

            api.pagesThemeContent = obj.content;

            api.Events.trigger( "sedAfterThemeContentReady" );

        });

        api.previewer.bind( 'pages_theme_content_update', function( pagesContent ) {

            api.trigger("change");

            api.pagesThemeContent = pagesContent;

            api.Events.trigger( "sedAfterThemeContentUpdate" );

        });


        api.previewer.bind( 'set_editor_current_page', function( obj ) {

            if( !_.isUndefined( obj.changePage ) && obj.changePage )
                api.trigger("change");

            if( !_.isUndefined( obj.page ) )
                api.settings.page = obj.page;

        });

        api.previewer.bind( 'dataModulesSkinsCache', function( data ) {
            api.dataModulesSkinsCache = data || {};
        });

        api.previewer.bind( 'moduleSkinsTplCache' , function( skinsTpl ) {
            api.moduleSkinsTplCache = $.extend(true , api.moduleSkinsTplCache || {} , skinsTpl || {});
        });

        api.previewer.bind( 'previewerActive', function( ) {
            if( !_.isUndefined( api.dataModulesSkinsCache ) )
                api.previewer.send( "dataModuleSkins" , api.dataModulesSkinsCache );

            if( !_.isUndefined( api.moduleSkinsTplCache ) )
                api.previewer.send( "moduleSkinsTpl" , api.moduleSkinsTplCache );
        });

        api.previewer.bind( 'syncAttachmentsSettings', function( data ) {

            if( _.isUndefined( data ) )
                return ;
                                    //api.log( data );
            api.attachmentsSettings = data;

            _.each( data , function(index, attachment) {
                api.previewer.trigger( 'addAttachmentSizes' , {
                    sizes : attachment.sizes,
                    id : attachment.id
                });    
            });

            /*if( _.isUndefined( api.attachmentsSettings ) )
                api.attachmentsSettings = data;
            else{
                var attachIds = _.pluck( data , 'id');
                api.attachmentsSettings = _.map( api.attachmentsSettings , function( attachment ){
                    var index = $.inArray( attachment.id , attachIds);
                    if( index != -1 ){
                        var newAttachment = _.findWhere(data , {'id' : attachment.id});
                        attachIds.splice( index , 1);
                        return newAttachment;
                    }else
                        return attachment;

                });

            } */

        });


        /*api.previewer.bind( 'duplicateSettingsSync', function( sync ) {

            var sed_pb_modules = api( 'sed_pb_modules' )() ,
                 sed_pb_modules_ids = _.keys( sed_pb_modules ) ,
                 sed_page_customized = api.get(),
                 settingsIds = _.keys( api.settings.settings ),
                 styleEditorSettings = _.chain( settingsIds )
                .filter(function( id ){
                    if( api.settings.settings[id].type == "style-editor" )
                        return true;
                    else
                        return false;
                })
                .value() ,
                currentModels = ( sync.place == "theme" ) ? api.pagesThemeContent[sync.postId ] : api.postsContent[sync.postId ] ,
                rowShortcodes = {},
                shortcodeIds = _.keys( sync.ids );
                //cssSelectors = {};

            _.each( sed_pb_modules_ids , function( id ){
                if( $.inArray( id , shortcodeIds ) != -1 ){
                    if( _.isUndefined( rowShortcodes['sed_pb_modules'] ) )
                        rowShortcodes['sed_pb_modules'] = {};

                    rowShortcodes['sed_pb_modules'][sync.ids[id]] = $.extend( true, {} , sed_pb_modules[id] );
                }
            });

            /*var reg = "#(" + shortcodeIds.join("|") + ")\s+" ,
                patt = new RegExp( reg , 'i' );
                patt.test( selector ); */
            /*
            _.each( styleEditorSettings , function( id ){

                var values = sed_page_customized[id],
                    selectors = _.keys( values );

                _.each( selectors , function( selector ){
                    _.each( shortcodeIds , function( sid ){
                        var str = '[sed_model_id="' + sid + '"]';
                        if( str == selector || selector.indexOf( str + " " ) > -1 ){
                            if( _.isUndefined( rowShortcodes[id] ) )
                                rowShortcodes[id] = {};

                            var newSelector = selector.replace( sid , sync.ids[sid] );
                            //cssSelectors[selector] = newSelector;

                            if( _.isObject( sed_page_customized[id][selector] ) )
                                rowShortcodes[id][newSelector] = $.extend( true, {} , sed_page_customized[id][selector] );
                            else
                                rowShortcodes[id][newSelector] = sed_page_customized[id][selector];
                        }
                    });
                });

            });

            $.extend(  true, sed_page_customized , rowShortcodes );

            api.previewer.send( "duplicateSettingsSynced" , {
                modelsSettings :  rowShortcodes ,
                //cssSelectors   :  cssSelectors
            });

        });*/

       $(".site-editor-app-tools-button .sed-module-gideline").click(function(){
          if( $(this).hasClass("modules-guideline-on") ){
              $(this).removeClass("modules-guideline-on");
              api.previewer.send("modulesGuidelineOff");
          }else{
              $(this).addClass("modules-guideline-on");
              api.previewer.send("modulesGuidelineOn");
          }
      });

      api.megamenuDragArea = {};

      api.previewer.bind( "megamenuDragAreaMode" , function( obj ){
          api.megamenuDragArea = obj;

          if( obj.mode == "on" ){
              $("#app-preview-mode-btn").prop("disabled" , true);
          }else if( obj.mode == "off" ){
              $("#app-preview-mode-btn").prop("disabled" , false);
          }
      });

      api.previewer.bind( 'previewerActive', function( ) {
          if( !_.isUndefined( api.megamenuDragArea.mode ) && api.megamenuDragArea.mode == "on" ){ 
              api.previewer.send( "megamenuDragAreaActiveAfterRefresh" , api.megamenuDragArea );
          }
      });


      api.previewer.bind( 'pageWidgetsList', function( value ) {
          var setting = api.instance("page_widgets_list");
          setting.set( value );
      });


      /**
       * @TYPOGRAPHY
       * we sended all font families to site-iframe.js file for load & save google fonts & custom fonts
       * All Control With 'font-family' type have "style_props" property with 'font-family' value in control js data
       * after refresh control setting we send font family
       */

      api.Events.bind( "after_control_value_refresh_event" , function( control , val ){

          if( !_.isUndefined( control.params ) && !_.isUndefined( control.params.style_props ) && control.params.style_props === 'font-family' ){ 

              api.previewer.send( "changeFontFamilyControl" , val );

          }

      })
      
      

  });

})( sedApp, jQuery );