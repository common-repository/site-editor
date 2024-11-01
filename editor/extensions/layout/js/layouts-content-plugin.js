/**
 * @plugin.js
 * @App Layout Plugin JS
 *
 * @License: http://www.siteeditor.org/license
 * @Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){

    var api = sedApp.editor;

    /**
     * A helper function for check exist layout or no?
     * @param layout
     * @returns {boolean}
     */
    api.fn.existLayout = function( layout ){

        /**
         * sed_layouts_settings is for layouts_manager control
         */
        var layoutsManagerControl = api.control.instance("sed_add_layout_layouts_manager"),
            layoutsSettings;

        if (!_.isUndefined(layoutsManagerControl)) {
            layoutsSettings = layoutsManagerControl.model;
        }else{
            layoutsSettings = api( 'sed_layouts_settings' )();
        }

        if( _.isEmpty( layoutsSettings ) || !_.isObject( layoutsSettings ) ){
            return false;
        }

        var layouts = _.keys( layoutsSettings );

        return $.inArray( layout , layouts ) > -1;

    };

    /**
     * Get Current Page Layout
     */
    api.fn.getPageLayout = function( ){

        var currentLayout = !_.isEmpty( api( api.currentPageLayoutSettingId )() ) ? api( api.currentPageLayoutSettingId )() : api.defaultPageLayout;

        if( !api.fn.existLayout( currentLayout ) ){

            return "default";

        }

        return currentLayout;

    };

    api.LayoutsRowsContent = api.Class.extend({

        initialize: function (params, options) {
            
            this.value = {};

            this.removedThemeRows = [];

            this.newLayoutsWithoutMainContent = [];

            $.extend(this, options || {});

            this.ready();
        },

        ready : function () {
            var self = this;

            //When remove a public row
            api.previewer.bind("sedRemoveRowFromLayouts", function (themeId) {

                api.Events.trigger( "beforeRemovedThemePublicRow" , themeId );

                /*var control = api.control.instance("main_layout_row_scope_control");
                if (!_.isUndefined(control) && typeof control.removeRowFromAllLayouts == "function") { alert("test...");
                    control.removeRowFromAllLayouts(themeId);
                }else{}*/

                self.removeRowFromAllLayouts( themeId );

                self.removeRowFromLayoutsContent( themeId );
                
            });

            api.Events.bind("sedAfterThemeContentReady", function () {
                self.updateLayoutsContent();
                self.createThemeContent();
            });

            api.Events.bind("sedAfterThemeContentUpdate", function () {
                self.updateLayoutsContent();
                self.createThemeContent();
            });

            api.previewer.bind( "sedOriginalCustomizedRows", function( data ){

                var layoutsContent = self.getClone();

                $.each( data.rows , function( themeId , shortcodes ){

                    if( _.isUndefined( layoutsContent[themeId] ) ){
                        layoutsContent[themeId] = api.sedShortcode.clone( shortcodes );
                    }

                });

                self.set( layoutsContent );

            });

            //when refresh previewer
            api.addFilter( "sedPreviewerQueryFilter" , function( query ){

                query.sed_layouts_content = JSON.stringify( self.get() );

                query.sed_removed_theme_rows = self.removedThemeRows;
                
                /**
                 * Set Main Content for new layouts
                 * @type {Array}
                 */
                query.sed_new_layouts_without_main_content = self.newLayoutsWithoutMainContent;

                return query;
            });

            //when save data
            api.addFilter( "sedSaveQueryFilter" , function( query ){

                query.sed_layouts_content = JSON.stringify( self.get() );

                query.sed_removed_theme_rows = self.removedThemeRows;

                /**
                 * Set Main Content for new layouts
                 * @type {Array}
                 */
                query.sed_new_layouts_without_main_content = self.newLayoutsWithoutMainContent;

                return query;
            });

        } ,

        set : function ( to ) { 

            var from = this.get();

            if( !_.isEqual( from , to  ) ){ 

                this.value = to;

                api.previewer.send( "syncSedLayoutsContent" , to );

                //console.log("-----------layoutsRowsContent--------------", to );

            }

        } ,

        get : function () {

            return this.value ;

        },

        //return copy from current value
        getClone : function () {
            return $.extend( true , {} , this.get() );
        },

        updateLayoutsContent: function () {

            var layoutsContent = this.getClone();

            _.each(api.pagesThemeContent[api.settings.page.id], function( shortcode ) {

                if (!_.isUndefined(shortcode.theme_id) && _.isUndefined(shortcode.is_customize)) {

                    var rowShortcodes = api.sedShortcode.findModelsById(api.pagesThemeContent[api.settings.page.id], shortcode.id);

                    layoutsContent[shortcode.theme_id] = api.sedShortcode.clone(rowShortcodes);

                }

            });

            this.set( layoutsContent );

        },


        createThemeContent: function () {
            var pagesThemeContent = [];

            //console.log("----------------pagesThemeContent NEW NEW -------------", api.pagesThemeContent[api.settings.page.id]);

            _.each(api.pagesThemeContent[api.settings.page.id], function( shortcode ) {
                if (_.isUndefined(shortcode) || !_.isObject(shortcode))
                    return true;

                if (shortcode.parent_id == "root" && ( _.isUndefined(shortcode.theme_id) || !_.isUndefined(shortcode.is_customize) )) {
                    var rowShortcodes = api.sedShortcode.findModelsById(api.pagesThemeContent[api.settings.page.id], shortcode.id),
                        rowShortcodesClone = api.sedShortcode.clone(rowShortcodes),
                        model = {
                            content: rowShortcodesClone
                        };

                    if (!_.isUndefined(shortcode.row_type)) {
                        model.row_type = _.clone(shortcode.row_type);
                    }

                    if (!_.isUndefined(shortcode.rel_theme_id)) {
                        model.rel_theme_id = _.clone(shortcode.rel_theme_id);
                    }

                    if (!_.isUndefined(shortcode.theme_id)) {
                        model.theme_id = _.clone(shortcode.theme_id);
                        model.is_customize = _.clone(shortcode.is_customize);
                    }

                    pagesThemeContent.push( model ); //$.param()//console.log( "------------model-------------" , model );
                }

            });

            //console.log("---------------- pagesThemeContent After Modify -------------", pagesThemeContent);

            pagesThemeContent = encodeURI( JSON.stringify( pagesThemeContent ) );

            var id = api.currentPageThemeContentSettingId;

            if( _.isUndefined( api.settings.settings[id] ) ) {

                var data = {
                    option_type     : ( api.settings.page.type == "post" ) ? "postmeta" : "option" ,
                    transport       : "postMessage"  ,
                    type            : "general" ,
                    value           : pagesThemeContent
                };

                api.create( id, id, data.value, {
                    transport: data.transport,
                    previewer: api.previewer,
                    stype    : data.type || "general"  //settings type :: general type not support object values
                } );

                api.settings.settings[id] = data;

            }else{

                api( id ).set( pagesThemeContent );

            }

        },

        removeRowFromLayoutsContent: function (themeId) {

            var layoutsContent = this.getClone();

            if( !_.isUndefined( layoutsContent[themeId] ) ) {

                delete layoutsContent[themeId];

                this.removedThemeRows.push( themeId );

            }

            this.set( layoutsContent );

            //console.log("-----------layoutsRowsContent AFTER REMOVE--------------", layoutsContent );

        },

        removeRowFromAllLayouts: function (themeId) {

            var models = $.extend( true , {} , api( 'sed_layouts_models' )() || {} );

            _.each( models , function (rows, layout) {

                models[layout] = _.filter( models[layout] , function (row) {
                    return row.theme_id != themeId;
                });

            });

            api( 'sed_layouts_models' ).set( $.extend( true , {} , models ) );

        }

    });


    $( function() {

        api.layoutsRowsContent = new api.LayoutsRowsContent({});

    });
    
})( sedApp, jQuery );