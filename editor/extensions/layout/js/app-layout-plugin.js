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

    api.LayoutsRowsContent = api.Class.extend({

        initialize: function (params, options) {
            var self = this;

            this.value = {};

            $.extend(this, options || {});

            this.ready();
        },

        ready : function () {
            var self = this;

            //When remove a public row
            api.previewer.bind("sedRemoveRowFromLayouts", function (themeId) {

                api.Events.trigger( "beforeRemovedThemePublicRow" , themeId );

                var control = api.control.instance("main_layout_row_scope_control");
                if (!_.isUndefined(control) && typeof control.removeRowFromAllLayouts == "function") {
                    control.removeRowFromAllLayouts(themeId);
                }

                self.removeRowFromLayoutsContent(themeId);
            });

            api.Events.bind("sedAfterThemeContentReady", function () {
                self.updateLayoutsContent();
                self.createThemeContent();
            });

            api.Events.bind("sedAfterThemeContentUpdate", function () {
                self.updateLayoutsContent();
                self.createThemeContent();
            });

            //when refresh previewer
            api.addFilter( "sedPreviewerQueryFilter" , function( query ){

                query.sed_layouts_content = JSON.stringify( self.get() );

                return query;
            });

            //when save data
            api.addFilter( "sedSaveQueryFilter" , function( query ){

                query.sed_layouts_content = JSON.stringify( self.get() );

                return query;
            });

        } ,

        set : function ( to ) {

            var from = this.get();

            if( !_.isEqual( from , to  ) ){

                this.value = to;

                api.previewer.send( "syncSedLayoutsContent" , to );

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
            var control = this;

            var layoutsContent = this.getClone();

            _.each(api.pagesThemeContent[api.settings.page.id], function (shortcode, index) {

                if (!_.isUndefined(shortcode.theme_id) && _.isUndefined(shortcode.is_customize)) {

                    var rowShortcodes = api.sedShortcode.findModelsById(api.pagesThemeContent[api.settings.page.id], shortcode.id);

                    layoutsContent[shortcode.theme_id] = api.sedShortcode.clone(rowShortcodes);

                }

            });

            this.set( layoutsContent );

            //console.log("-----------layoutsRowsContent--------------", layoutsContent );

        },


        createThemeContent: function () {
            var control = this,
                pagesThemeContent = [];

            //console.log("----------------pagesThemeContent NEW NEW -------------", api.pagesThemeContent[api.settings.page.id]);

            _.each(api.pagesThemeContent[api.settings.page.id], function (shortcode, index) {
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

            pagesThemeContent = encodeURI( JSON.stringify( pagesThemeContent ) );

            //console.log("---------------- pagesThemeContent After Modify -------------", pagesThemeContent);

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

            if( !_.isUndefined( layoutsContent[themeId] ) )
                delete layoutsContent[themeId];

            this.set( layoutsContent );

            //console.log("-----------layoutsRowsContent--------------", layoutsContent );

        }

    });

    api.RemovedRowsCollection = api.Class.extend({

        initialize: function (params, options) {
            var self = this;

            this.value = {};

            $.extend(this, options || {});

            this.ready();
        },

        ready: function () {

            var self = this;

            //Before remove a public row
            api.Events.bind( "beforeRemovedThemePublicRow" , function( themeId ){
                self.beforeDestroyPublicRow( themeId );
            });

            //Before Change Public To Private Scope
            api.Events.bind( "beforeChangeScopePublicToPrivate" , function( themeId ){
                self.beforeDestroyPublicRow( themeId );
            });

            //when checked special layout or checked all layouts in scope settings
            api.Events.bind( "beforeChangePublicRowLayout" , function( themeId , layout , isAdded ){

                if( ! isAdded ){
                    var nextModel = self.getNextPublicRow( leyout , themeId );

                    var prevModel = self.getPrevPublicRow( leyout , themeId );

                    self.refresh( themeId , leyout , prevModel , nextModel );
                }else{
                    self.removeModel( themeId , layout );
                }

            });

        },

        beforeDestroyPublicRow : function( themeId ){

            var leyouts = this.getLayoutsByThemeId( themeId ) ,
                self = this;

            if( !_.isEmpty( leyouts ) ){

                _.each( leyouts , function( leyout ){

                    var nextModel = self.getNextPublicRow( leyout , themeId );

                    var prevModel = self.getPrevPublicRow( leyout , themeId );

                    self.refresh( themeId , leyout , prevModel , nextModel );

                });

            }

        },

        refresh : function( themeId , leyout , prevModel , nextModel ){

            if( _.isUndefined( themeId ) || ! themeId )
                return ;

            var removedRowsModels = $.extend( true , {} , api('sed_layouts_removed_rows')() || {} );

            var afterRelThemeId =  ( _.isUndefined( nextModel ) || ! nextModel ) ? "" : nextModel.theme_id ;

            var afterRowType    =  ( _.isUndefined( nextModel ) || ! nextModel ) ? "end" : "before" ;

            var beforeRelThemeId =  ( _.isUndefined( prevModel ) || ! prevModel ) ? "" : prevModel.theme_id ;

            var beforeRowType    =  ( _.isUndefined( prevModel ) || ! prevModel ) ? "start" : "after" ;

            if( _.isUndefined( removedRowsModels[leyout] ) )
                removedRowsModels[leyout] = [];

            removedRowsModels[leyout].push({
                theme_id    :  themeId ,
                after       :  {
                    rel_theme_id    : afterRelThemeId  ,
                    row_type        : afterRowType
                } ,
                before      :  {
                    rel_theme_id    : beforeRelThemeId  ,
                    row_type        : beforeRowType
                }
            });

            api('sed_layouts_removed_rows').set( removedRowsModels );

        },

        removeModel: function ( themeId , layout ) {

            var removedRowsModels = $.extend( true , {} , api('sed_layouts_removed_rows')() || {} );

            if( _.isUndefined( removedRowsModels[layout] ) )
                return ;

            removedRowsModels[layout] = _.filter( removedRowsModels[layout] , function( model ) {
                return model.theme_id != themeId;
            });

            api('sed_layouts_removed_rows').set( removedRowsModels );

        },

        getLayoutsByThemeId: function ( themeId ) {

            var layouts = [];

            var layoutsModels = $.extend( true , {} , api('sed_layouts_models')() );

            $.each( layoutsModels , function (layout, rows) {
                $.each(rows, function (idx, options) {
                    if (options.theme_id == themeId)
                        layouts.push(layout);
                });
            });

            return layouts;

        },

        getNextPublicRow : function( layout , themeId ){

            var layoutsModels = $.extend( true , {} , api('sed_layouts_models')() );

            if( _.isUndefined( layoutsModels[layout] ) ){
                return false;
            }

            var models = _.sortBy( layoutsModels[layout] , 'order' );

            models = models.reverse();

            var currIndex = _.findIndex( models , { theme_id : themeId } );

            if( currIndex < ( models.length - 1 )  ){
                return models[ currIndex + 1 ];
            }else{
                return false;
            }

        },

        getPrevPublicRow : function( layout , themeId ){

            var layoutsModels = $.extend( true , {} , api('sed_layouts_models')() );

            if( _.isUndefined( layoutsModels[layout] ) ){
                return false;
            }

            var models = _.sortBy( layoutsModels[layout] , 'order' );

            models = models.reverse();

            var currIndex = _.findIndex( models , { theme_id : themeId } );

            if( currIndex > 0  ){
                return models[ currIndex - 1 ];
            }else{
                return false;
            }

        }

    });

        api.AppLayouts = api.Class.extend({

        initialize: function (params, options) {
            var self = this;

            $.extend(this, options || {});

            this.currentLayout;

            this.ready();
        },

        ready: function () {
            var self = this;

            var initLayoutScopeControl = false;

            api.Events.bind("afterInitAppendModulesSettingsTmpl", function (moduleSettingsObj, currentElDialog) {

                var currentElement = $("#website")[0].contentWindow.jQuery('[sed_model_id="' + api.currentTargetElementId + '"]'),
                    currentRow = currentElement.parents(".sed-pb-module-container:first").parent();

                var scopeEl = $($("#layouts-scope-settings-button-tpl").html()).prependTo(currentElDialog);

                if (!_.isUndefined(__layoutsScopeContent) && !_.isEmpty(__layoutsScopeContent)) {

                    var scopeSettingsEl = $( __layoutsScopeContent ).prependTo($("#dialog_page_box_main_layout_row_scope_control"));

                    scopeSettingsEl.after( $("#manage-layout-theme-rows-page-box-tpl").html() );


                } else {

                    var content = $("#layouts-scope-settings-content-tpl").html();

                    content += $("#manage-layout-theme-rows-page-box-tpl").html();

                    $( content ).prependTo($("#dialog_page_box_main_layout_row_scope_control"));

                }

                if (currentRow.parent().hasClass("sed-site-main-part")) {
                    scopeEl.show();
                } else {
                    scopeEl.hide();
                }

            });

            api.Events.bind("afterAppendModulesSettingsTmpl", function (moduleSettingsObj, currentElDialog) {
                var currentElement = $("#website")[0].contentWindow.jQuery('[sed_model_id="' + api.currentTargetElementId + '"]'),
                    currentRow = currentElement.parents(".sed-pb-module-container:first").parent();

                $(__layoutsScopeContent).prependTo($("#dialog_page_box_main_layout_row_scope_control"));
                var scopeEl = $("#sed-scope-settings-main_layout_row_scope_control").parents(".row_settings:first");

                if (currentRow.parent().hasClass("sed-site-main-part")) {
                    scopeEl.show();
                } else {
                    scopeEl.hide();
                }

            });

            var __layoutsScopeContent , __isOnceCreateScopeContent = true;
            //Create Scope Content only one time and keep with all events
            api.Events.bind("beforeResetDialogSettingsTmpl", function (settingsId) {
                if( __isOnceCreateScopeContent === true ) {
                    __layoutsScopeContent = $("#dialog_page_box_main_layout_row_scope_control").children().detach();
                    __isOnceCreateScopeContent = false;
                }
            });

            api.previewer.bind("updateCurrentLayoutRowsOrders", function (themeRows) {
                var control = api.control.instance("main_layout_row_scope_control");
                //console.log("---------------themeRows-----------------", themeRows);
                if (!_.isUndefined(control)) {
                    control.ordersRefresh(themeRows);
                }
            });

            api.previewer.bind("sedPagesLayoutsInfo", function (info) {
                api.defaultPageLayout = info.defaultPageLayout;
                api.currentLayoutGroup = info.currentLayoutGroup;

                self.currentLayout = !_.isEmpty(api('page_layout')()) ? api('page_layout')() : api.defaultPageLayout;

                var scopeControl = api.control.instance("main_layout_row_scope_control");

                if (!_.isUndefined(scopeControl)) {
                    scopeControl.currentLayout = !_.isEmpty(api('page_layout')()) ? api('page_layout')() : api.defaultPageLayout;
                }

                var layoutsManagerControl = api.control.instance("sed_add_layout_layouts_manager")

                if (!_.isUndefined(layoutsManagerControl)) {
                    layoutsManagerControl.currentLayout = !_.isEmpty(api('page_layout')()) ? api('page_layout')() : api.defaultPageLayout;
                }

            });


            api.previewer.bind("sedRemoveModuleElementsSync", function (moduleId) {
                self.removeModule(moduleId);
            });

            api.previewer.bind("ok_sedRemoveModulesConfirm", function () {
                api.previewer.send("sedRemoveModulesApply", $("#sed-confirm-message-dialog").data("moduleId"));
                $("#sed-confirm-message-dialog").removeData("moduleId")
            });

            api.previewer.bind("cancel_sedRemoveModulesConfirm", function () {
                $("#sed-confirm-message-dialog").removeData("moduleId")
            });

            api.previewer.bind("customThemeRowInfoChange", function () {
                if ($("#sed_theme_custom_row_type").length > 0) {
                    self.updateRowTypeSelectField();
                }
            });

            $(".sed_go_to_scope_settings").livequery(function () {

                $(this).on("click.openScopeSettings", function () {

                    var currentElement = $("#website")[0].contentWindow.jQuery('[sed_model_id="' + api.currentTargetElementId + '"]'),
                        currentRow = currentElement.parents(".sed-pb-module-container:first").parent();

                    var themeId = currentRow.data("themeId");

                    if (initLayoutScopeControl === false) {
                        api.Events.trigger("renderSettingsControls", 'main_layout_row_scope_control', api.settings.controls['main_layout_row_scope_control']);
                        initLayoutScopeControl = true;
                    }

                    var control = api.control.instance("main_layout_row_scope_control");

                    if (!_.isUndefined(themeId) && !_.isEmpty(themeId) && themeId) {
                        control.update(themeId);
                    } else {
                        control.update();
                    }

                    self.updateRowTypeSelectField();
                });

            }, function () {

                $(this).unbind("click.openScopeSettings");

            });

            $("#sed_theme_custom_row_type").livequery(function () {

                $(this).on("change", function () {
                    var val = $(this).val();
                    api.previewer.send("customThemeRowChangeType", val);
                });

            }, function () {

                $(this).unbind("change");

            });

            //when customize revert to hidden or normal public scope ::  current element updated
            api.previewer.bind('changeCurrentElementByCustomizeRevert', function (dataEl) {
                api.appModulesSettings.updateByChangePattern(dataEl);
            });

            //before change page layout
            api.Events.bind( 'beforeRefreshPreviewer' , function ( id ){

                var currGroupId = "sed_pages_layouts[" + api.currentLayoutGroup + "]";

                if( id == "page_layout" || currGroupId == id ) {
                    //self.beforeUpdatePageLayout();
                }

            });

            //Update Layouts for controls
            _.each( [ "afterAppendSettingsTmpl" , "endInitAppendSettingsTmpl" ] , function( _EvSettingsAppend ){

                api.Events.bind( _EvSettingsAppend , function( $dialog , settingsType , settingsId ){

                    if( $dialog.find(".sed_all_layouts_options_select").length > 0 ) {

                        _.each( api.sedGroupControls[settingsId] , function( data ){

                            if( $( "#sed-app-control-" + data.control_id ).find(".sed_all_layouts_options_select").length > 0 ) {

                                var template = api.template("sed-layouts-select-options"),
                                    content = template({layoutsSettings: api('sed_layouts_settings')()});

                                $("#sed-app-control-" + data.control_id).find(".sed_all_layouts_options_select").html( content );

                                var control = api.control.instance( data.control_id );

                                if ( !_.isUndefined( control ) ) {

                                    var currVal = control.currentValue;

                                    control.update( currVal );

                                }

                            }

                        });

                    }

                });

            });

            api.addFilter( 'sedPreviewerTransportFilter' , function( transport , id ){

                var currGroupId = "sed_pages_layouts[" + api.currentLayoutGroup + "]";

                if (_.isEmpty(api("page_layout")()) && id == currGroupId) {

                    transport = "refresh";

                }else if( id == "page_layout" ) {

                    var newLayout = !_.isEmpty(api('page_layout')()) ? api('page_layout')() : api( currGroupId )();

                    if( newLayout == self.currentLayout ) {

                        transport = "postMessage";

                    }

                }

                return transport;
            });

        },

        beforeUpdatePageLayout : function(){

            var currGroupId = "sed_pages_layouts[" + api.currentLayoutGroup + "]";

            var newLayout = !_.isEmpty(api('page_layout')()) ? api('page_layout')() : api( currGroupId )() ,
                curLayout = this.currentLayout;

            if( _.isUndefined( this.cacheChangeLayoutThemeContent ) )
                this.cacheChangeLayoutThemeContent = {};

            this.cacheChangeLayoutThemeContent[curLayout] = api.sedShortcode.clone( api( api.currentPageThemeContentSettingId )() );

            if( !_.isUndefined( this.cacheChangeLayoutThemeContent[newLayout] ) ) {
                api( api.currentPageThemeContentSettingId ).set(this.cacheChangeLayoutThemeContent[newLayout]);
            }

        },

        updateRowTypeSelectField: function () {

            var id = $("#website")[0].contentWindow.jQuery('[sed_model_id="' + api.currentTargetElementId + '"]').parents(".sed-pb-module-container:first").parent().attr("sed_model_id");

            var shortcode = _.findWhere(api.pagesThemeContent[api.settings.page.id], {id: id});

            if (!_.isUndefined(shortcode.theme_id)) {

                $("#sed_theme_custom_row_type_container").addClass("hide");

                return;

            } else {

                $("#sed_theme_custom_row_type_container").removeClass("hide");

                $("#sed_theme_custom_row_type > option").removeClass("hide");

                var rowType = shortcode.row_type,
                    relThemeId = shortcode.rel_theme_id;

                $("#sed_theme_custom_row_type").val(rowType);

            }

            var otherRowType = "before",
                hasBeforePublicRow = false,
                hasAfterPublicRow = false,
                themeRows = _.where(api.pagesThemeContent[api.settings.page.id], {parent_id: "root"}),
                num = 0,
                currentIndex;

            _.each(api.pagesThemeContent[api.settings.page.id], function (shortcode, index) {


                if (id != shortcode.id && shortcode.parent_id == "root" && !_.isUndefined(shortcode.theme_id)) {

                    if (otherRowType == "before") {
                        hasBeforePublicRow = true;
                    } else if (otherRowType == "after") {
                        hasAfterPublicRow = true;
                        return false;
                    }

                } else if (id == shortcode.id) {
                    otherRowType = "after";
                    currentIndex = num;
                }

                if (shortcode.parent_id == "root") {
                    num += 1;
                }

            });

            if (hasBeforePublicRow === true) {
                $('#sed_theme_custom_row_type > option[value="start"]').addClass("hide");
            } else {
                $('#sed_theme_custom_row_type > option[value="after"]').addClass("hide");
            }

            if (hasAfterPublicRow === true) {
                $('#sed_theme_custom_row_type > option[value="end"]').addClass("hide");
            } else {
                $('#sed_theme_custom_row_type > option[value="before"]').addClass("hide");
            }

            if (rowType == "before" && currentIndex > 0) {
                if (!_.isUndefined(themeRows[currentIndex - 1].rel_theme_id) && themeRows[currentIndex - 1].rel_theme_id == relThemeId) {
                    $('#sed_theme_custom_row_type > option[value="after"]').addClass("hide");
                    $('#sed_theme_custom_row_type > option[value="start"]').addClass("hide");
                }
            }

            if (rowType == "after" && themeRows.length > ( currentIndex + 1 )) {
                if (!_.isUndefined(themeRows[currentIndex + 1].rel_theme_id) && themeRows[currentIndex + 1].rel_theme_id == relThemeId) {
                    $('#sed_theme_custom_row_type > option[value="before"]').addClass("hide");
                    $('#sed_theme_custom_row_type > option[value="end"]').addClass("hide");
                }
            }

            if (rowType == "start" && themeRows.length > ( currentIndex + 1 )) {
                if (!_.isUndefined(themeRows[currentIndex + 1].rel_theme_id) && themeRows[currentIndex + 1].row_type == rowType) {
                    $('#sed_theme_custom_row_type > option[value="before"]').addClass("hide");
                }
            }

            if (rowType == "end" && currentIndex > 0) {
                if (!_.isUndefined(themeRows[currentIndex - 1].rel_theme_id) && themeRows[currentIndex - 1].row_type == rowType) {
                    $('#sed_theme_custom_row_type > option[value="after"]').addClass("hide");
                }
            }

        },

        removeModule: function (moduleId) {
            var control = this;

            $("#sed-confirm-message-dialog").dialog("open");

            $("#sed-confirm-message-dialog").data("confirmEventId", "sedRemoveModulesConfirm");

            $("#sed-confirm-message-dialog").data("moduleId", moduleId);

            $("#sed-confirm-message-dialog").html($("#sed-remove-module-confirm-tpl").html());

        },

        manageLayoutRows: function ( elm , themeId ) {

            $(api.sedDialogSettings.dialogSelector).data('sed.multiLevelBoxPlugin')._pageBoxNext( elm );

            //$( api.sedDialogSettings.dialogSelector ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_manage_layout_theme_rows"  );

            var layout = $( elm ).data("layout"),
                template = api.template("sed-layout-edit-rows"),
                models = api('sed_layouts_models')(),
                content = template({
                    layoutRows: models[layout],
                    noTitle: api.I18n.no_title,
                    currThemeId: themeId || ""
                });

            $("#dialog_page_box_manage_layout_theme_rows .sed-dialog-page-box-inner").html(content);

            $("#dialog_page_box_manage_layout_theme_rows").data("layout" , layout);
        }

    });

    //1.no public to public ----------- add theme_id to main shortcode model
    //2.public to no public ----------- remove theme_id from main shortcode model && remove related shortcodes from sed_layout_content
    //3.public to customize ------------ add is_customize to main shortcode model && not update in sed_layout_content
    //4.customize to public ------------ remove is_customize from main shortcode model && replce main shortcodes with customize shortcodes
    //5.hidden to customize ------------ remove is_hidden & add is_customize to main shortcode model && not update in sed_layout_content
    //6.customize to hidden ------------- remove is_customize & add is_hidden to main shortcode model && replce main shortcodes with customize shortcodes
    //7.hidden to public ------------ remove is_hidden from main shortcode model
    //8.public to hidden -------------  add is_hidden to main shortcode model

    /*
      confirm alert
      1. after convert public to private (customize or hidden or normal to private )
      2. after customize to hidden
      3. after customize to public
      4. after remove public row (customize or hidden or normal)
      5. after drag & drop public row to inner other modules
    */

    api.LayoutScopeControl = api.Control.extend({

        ready: function () {
            var control = this;

            this.lastThemeId = parseInt(api.instance('sed_last_theme_id').get());

            this.currentLayout = !_.isEmpty(api('page_layout')()) ? api('page_layout')() : api.defaultPageLayout;

            this.models = $.extend( true, {} , control.setting() );

            control.publicScopeEl = control.container.find('[name="sed_layout_scope_public"]');

            control.sedScopeLayoutEl = control.container.find('[name="sed_scope_layout"]');

            control.layoutPublicTypeEl = control.container.find('[name="sed_layout_public_type"]');

            control.allLayoutCheckedEl = control.container.find('.sed-all-sub-themes-check-box > input');

            control.editLayoutRowsEl = control.container.find('.edit-layout-rows');

            this.lastLayoutPublicType = "normal";

            this.themeId = "";

            this.confirmDialogEl = $("#sed-confirm-message-dialog");

            this.confirmEventIds = {
                "publicToPrivate"   : "changeScopePublicToPrivateConfirm",
                "customizeToPublic" : "changeScopeCustomizeToPublicConfirm",
                "customizeToHidden" : "changeScopeCustomizeToHiddenConfirm",
            };

            api.previewer.bind("ok_" + this.confirmEventIds.publicToPrivate, function () {
                control.changeScopePublicToPrivate();
            });

            api.previewer.bind("cancel_" + this.confirmEventIds.publicToPrivate, function () {
                $(control.selector).find('[name="sed_layout_scope_public"]').prop("checked", true);
            });

            api.previewer.bind("ok_" + this.confirmEventIds.customizeToPublic, function () {
                control.changeScopePublicTypes("normal", false);
            });

            api.previewer.bind("cancel_" + this.confirmEventIds.customizeToPublic, function () {
                control.updateRadioField( $(control.selector).find('[name="sed_layout_public_type"]') , "customize" );
            });

            api.previewer.bind("ok_" + this.confirmEventIds.customizeToHidden, function () {
                control.changeScopePublicTypes("hidden", false);
            });

            api.previewer.bind("cancel_" + this.confirmEventIds.customizeToHidden, function () {
                control.updateRadioField( $(control.selector).find('[name="sed_layout_public_type"]') , "customize" );
            });


            this.publicScopeEl.on("change", function () {

                if ($(this).prop('checked')) {
                    var container = $(this).parents("li:first");
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.public_scope );

                    //show public options like public type && all layout
                    container.find("ul.select-pubic-scope").removeClass("hide");

                    //"normal" public layout type is default value for public scope
                    control.updateRadioField(container.find('[name="sed_layout_public_type"]'), "normal");
                    control.lastLayoutPublicType = "normal";

                    //show all layout && select current page layout AS default value for layouts in "normal" public layout type
                    container.find("ul.select-layouts-custom").removeClass("hide");
                    control.updateMultiCheckboxField(container.find('[name="sed_scope_layout"]'), [control.currentLayout]);

                    $(control.selector).find('.select-layouts-custom .edit-layout-rows').addClass("hide");

                    $(control.selector).find('.select-layouts-custom .edit-layout-rows').filter(function(){
                        return $(this).data("layout") == control.currentLayout;
                    }).removeClass("hide");

                    //always disable current page layout for prevent user control , it's can not unchecked when current row has any public type scope
                    container.find('.sub-theme-item input[value="' + control.currentLayout + '"]').prop("disabled", true);

                    //create new theme id & add current row to public Layouts Model
                    control.themeId = control.generateThemeId();
                    control.addRowToModel(control.currentLayout);

                    //update Row Title From default module name
                    var shortcodeName = api.appModulesSettings.sedDialog.data.shortcodeName ,
                        title = api.shortcodes[shortcodeName].title;
                    control.updateRowTitle( control.currentLayout , control.themeId , title);

                    $("#sed_theme_custom_row_type_container").addClass("hide");

                    /*
                     * @Event
                     * @Name : sedLayoutChangeScope
                     * @args : @type
                     */
                    api.previewer.send('sedLayoutChangeScope', {
                        'type': 'privateToPublic',
                        'elementId': api.currentTargetElementId,
                        'themeId': control.themeId
                    });

                    control.refresh();
                } else {

                    if( control._get("main_row") )
                        return ;

                    control.confirmDialogEl.dialog("open");

                    control.confirmDialogEl.data("confirmEventId", control.confirmEventIds.publicToPrivate);

                    control.confirmDialogEl.html($("#change-public-to-private-confirm-tpl").html());

                }

            });

            this.layoutPublicTypeEl.on("change", function () {
                var type = $(this).val();
                control.changeScopePublicTypes(type);
            });

            this.sedScopeLayoutEl.livequery(function () {
                var $this = $(this)
                $this.on("change", function () {

                    api.Events.trigger( "beforeChangePublicRowLayout" , control.themeId , $(this).val() , $(this).prop('checked') );

                    if ($(this).prop('checked')) {

                        control.addRowToModel( $(this).val() , control._get( "order" ) );

                        var shortcodeName = api.appModulesSettings.sedDialog.data.shortcodeName ,
                            title = api.shortcodes[shortcodeName].title;
                        control.updateRowTitle($(this).val(), control.themeId, title);

                        if ($(control.selector).find('.select-layouts-custom input[name="sed_scope_layout"]').length == control.container.find("ul.select-layouts-custom .sub-theme-item input:checked").length) {
                            control.allLayoutCheckedEl.prop("checked", true);
                        }

                        $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").removeClass("hide");

                    } else {
                        control.removeRowFromModel($(this).val());
                        control.allLayoutCheckedEl.prop("checked", false);
                        $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").addClass("hide");
                    }

                    control.refresh();
                });
            }, function() {
                // unbind the change event
                $(this).unbind('change');
            });

            this.allLayoutCheckedEl.on("change", function () {
                var sedScopeLayoutEl = $(this).parents(".select-layouts-custom:first").find('[name="sed_scope_layout"]');

                if ($(this).prop('checked')) {
                    sedScopeLayoutEl.prop("checked", true);


                    sedScopeLayoutEl.each(function () {
                        var layout = $(this).val();

                        //if (layout != control.currentLayout)
                            $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").removeClass("hide");

                        if (!control.existThemeIdInLayout(layout)) {

                            api.Events.trigger( "beforeChangePublicRowLayout" , control.themeId , layout , true );

                            control.addRowToModel( layout , control._get( "order" ) );

                            var shortcodeName = api.appModulesSettings.sedDialog.data.shortcodeName ,
                                title = api.shortcodes[shortcodeName].title;

                            control.updateRowTitle( layout , control.themeId , title );

                        }
                    });

                } else {
                    sedScopeLayoutEl.prop("checked", false);

                    sedScopeLayoutEl.each(function () {
                        var layout = $(this).val();
                        $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").addClass("hide");

                        if (layout == control.currentLayout) {
                            $(this).prop("checked", true);
                            return;
                        }

                        if (control.existThemeIdInLayout(layout)) {
                            api.Events.trigger( "beforeChangePublicRowLayout" , control.themeId , layout , false );

                            control.removeRowFromModel(layout);
                        }
                    });

                }

                control.refresh();
            });

            this.editLayoutRowsEl.livequery(function () {
                $(this).on("click", function () {

                    api.appLayouts.manageLayoutRows( this , control.themeId );

                });
            }, function() {
                // unbind the change event
                $(this).unbind('click');
            });

            var RowsPageBoxSelector = "#dialog_page_box_manage_layout_theme_rows";

            $( RowsPageBoxSelector ).find(".layout-row-container").livequery(function () {
                $(this).sortable({
                    handle: ".sort.action" ,
                    // Keep track of the starting position
                    start: function (event, ui) {
                        ui.item.startPos = ui.item.index();
                    },

                    update: function (e, ui) {
                        var currValue = [],
                            order = 0,
                            themeRows = {};

                        $( RowsPageBoxSelector ).find(".layout-row-container > .sed-layout-row-box").each(function () {
                            var themeId = $(this).data("rowId");
                            themeRows[themeId] = {
                                order: order,
                            };
                            order++;
                        });

                        var layout = $( RowsPageBoxSelector ).data("layout");

                        control.ordersRefresh(themeRows, layout);

                        var endPos    = ui.item.index();
                        var startPos  = ui.item.startPos;

                        if( control.currentLayout == layout ){
                            api.previewer.send( "syncLayoutPublicRowsSort" , {
                                start   : startPos ,
                                end     : endPos
                            });
                        }

                    }
                }).disableSelection();
            }, function() {
                // unbind the change event
                //$(this).unbind('click');
            });

            var layoutRowItem = $( RowsPageBoxSelector ).find(".layout-row-container > .sed-layout-row-box");

            var _editRowTitle = function( $el ){
                $el.addClass("editing");
                $el.find(".layout-row-title-edit").focus();
            };

            var _updateRowTitle = function( $el , title ){

                if( !_titleValidation( title ) ){
                    _printAlert( api.I18n.invalid_layout_row_title );
                    return ;
                }

                $el.removeClass("editing");
                $el.find(".row-title-label").text( title );
                var layout = $( RowsPageBoxSelector ).data("layout");
                control.updateRowTitle( layout , $el.data("rowId") , title );
            };

            var _titleValidation = function( title ){
                var pattern = /^[A-Za-z0-9_\-\s]{2,35}$/;
                return pattern.test( title );
            };

            var _printAlert = function ( errortext ) {
                $( RowsPageBoxSelector ).find(".sed-layout-row-error-box p").html( errortext );
                $( RowsPageBoxSelector ).find(".sed-layout-row-error-box p").slideDown( 300 ).delay( 5000 ).fadeOut( 400 );
            };

            layoutRowItem.livequery(function(){

                var $el = $(this);

                $el.find('[data-action="edit"]').on("click" , function(){

                    _editRowTitle( $el ); //$(this).parents(".sed-layout-row-box:first")

                });

                $el.find('.row-title-label').on("click" , function(){

                    _editRowTitle( $el ); //$(this).parents(".sed-layout-row-box:first")

                });

                $el.find(".layout-row-title-edit").on("blur" , function(){

                    _updateRowTitle( $el , $(this).val() );

                });

                $el.find(".layout-row-title-edit").on("keypress" , function(e){

                    if (e.keyCode == 13)
                        _updateRowTitle( $el , $(this).val() );

                });

            });
        },


        changeScopePublicToPrivate: function () {
            var control = this;

            api.Events.trigger( "beforeChangeScopePublicToPrivate" , control.themeId );

            $(".sed-scope-mode-label .scope-mode").text( api.I18n.private_scope );

            var leyouts = this.getLayoutsByThemeId(control.themeId);

            _.each(leyouts, function (leyout) {
                control.removeRowFromModel(leyout);
            });

            control.container.find("ul.select-pubic-scope").addClass("hide");
            /*
             * @Event
             * @Name : sedLayoutChangeScope
             * @args : @type
             */
            api.previewer.send('sedLayoutChangeScope', {
                'type': 'publicToPrivate',
                'elementId': api.currentTargetElementId,
                'themeId': control.themeId
            });

            control.refresh();
        },

        changeScopePublicTypes: function (type, showConfirm) {
            var control = this;

            showConfirm = ( !_.isUndefined(showConfirm) ) ? showConfirm : true;
            switch (type) {
                case "normal":
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.public_scope );

                    if (control.lastLayoutPublicType == "customize" && showConfirm === true) {

                        control.confirmDialogEl.dialog("open");

                        control.confirmDialogEl.data("confirmEventId", control.confirmEventIds.customizeToPublic);

                        control.confirmDialogEl.html($("#change-customize-to-public-confirm-tpl").html());

                        return true;
                    }

                    control.container.find("ul.select-layouts-custom").removeClass("hide");

                    switch (control.lastLayoutPublicType) {
                        case "customize":
                            var usingDataMode = $(".select-customize-to-public-data-mode").find('[name="change-customize-to-public-mode"]:checked').val();
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'customizeToPublic',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId,
                                'usingDataMode': usingDataMode
                            });
                            break;
                        case "hidden":
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'hiddenToPublic',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId
                            });
                            break;
                    }

                    break;
                case "customize":
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.customize_scope );

                    control.updateExcludeRows("add");
                    control.container.find("ul.select-layouts-custom").addClass("hide");

                    switch (control.lastLayoutPublicType) {
                        case "normal":
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'publicToCustomize',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId
                            });
                            break;
                        case "hidden":
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'hiddenToCustomize',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId
                            });
                            break;
                    }

                    break;
                case "hidden":

                    if( control._get("main_row") )
                        return ;

                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.hidden_scope );

                    if (control.lastLayoutPublicType == "customize" && showConfirm === true) {

                        control.confirmDialogEl.dialog("open");

                        control.confirmDialogEl.data("confirmEventId", control.confirmEventIds.customizeToHidden);

                        control.confirmDialogEl.html($("#change-customize-to-hidden-confirm-tpl").html());

                        return true;
                    }

                    control.updateHiddenRows("add");
                    control.container.find("ul.select-layouts-custom").addClass("hide");

                    switch (control.lastLayoutPublicType) {
                        case "customize":
                            var usingDataMode = $(".select-customize-to-public-data-mode").find('[name="change-customize-to-public-mode"]:checked').val();
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'customizeToHidden',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId ,
                                'usingDataMode' : usingDataMode
                            });
                            break;
                        case "normal":
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'publicToHidden',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId
                            });
                            break;
                    }

                    break;
            }


            switch (control.lastLayoutPublicType) {
                case "customize":
                    control.updateExcludeRows("remove");
                    break;
                case "hidden":
                    control.updateHiddenRows("remove");
                    break;
            }

            control.refresh();
            control.lastLayoutPublicType = type;
        },

        //update radio fields & multi checkboxes field
        updateRadioField: function (element, to) {
            element.filter(function () {
                return this.value === to;
            }).prop('checked', true);
        },

        updateMultiCheckboxField: function (element, to) {
            if (_.isEmpty(to) || !_.isArray(to))
                return;

            element.filter(function () {
                return $.inArray(this.value, to) > -1;
            }).prop('checked', true);

            element.filter(function () {
                return $.inArray(this.value, to) == -1;
            }).prop('checked', false);

        },

        //refresh orders in Layouts row and current page row
        ordersRefresh: function (themeRows, layout) {
            var control = this;

            layout = ( !_.isUndefined(layout) && layout ) ? layout : control.currentLayout;

            if (!_.isEmpty(themeRows)) {

                control.models[layout] = _.map(control.models[layout], function (options) {

                    if ($.inArray(options.theme_id, _.keys(themeRows)) != -1) {
                        options.order = themeRows[options.theme_id].order;
                    }

                    return options;
                });

                control.refresh();
            }

        },

        getMultiCheckboxVal: function (element) {
            var val = [];

            element.filter(":checked").each(function () {
                val.push($(this).val());
            });

            return val;
        },

        refresh: function () {
            //console.log("refresh this.models ------------------------", this.models);

            var models = $.extend( true , {} , this.models );

            this.setting.set( models );
        },

        update: function (themeId) {
            var control = this;

            var layouts = !_.isUndefined(themeId) ? this.getLayoutsByThemeId(themeId) : [],
                publicScopeEl = $(control.selector).find('[name="sed_layout_scope_public"]'),
                layoutPublicTypeEl = $(control.selector).find('[name="sed_layout_public_type"]');

            //update layouts
            var template = api.template( "sed-all-layouts-checkbox-scope" ),
                content = template( { layoutsSettings : api('sed_layouts_settings')() } );

            $(control.selector).find('.select-layouts-custom > li.sub-theme-item').remove();
            $( content ).appendTo( $(control.selector).find('.select-layouts-custom') );

            //reset if disabled in main row
            publicScopeEl.prop("disabled", false);

            $(control.selector).find('.select-layouts-custom > li input[type="checkbox"]').prop("disabled", false);

            $(control.selector).find('[name="sed_layout_public_type"]').filter( function(){
                return $(this).prop("value") == "hidden";
            }).prop("disabled", false);


            if (_.isEmpty(layouts)) {
                $(".sed-scope-mode-label .scope-mode").text( api.I18n.private_scope );

                publicScopeEl.prop("checked", false);
                $(control.selector).find("ul.select-pubic-scope").addClass("hide");
                control.allLayoutCheckedEl.prop("checked", false);
                $(control.selector).find('.select-layouts-custom .edit-layout-rows').addClass("hide");

            } else if ($.inArray(this.currentLayout, layouts) == -1) {
                $(".sed-scope-mode-label .scope-mode").text( api.I18n.private_scope );

                publicScopeEl.prop("checked", false);
                $(control.selector).find("ul.select-pubic-scope").addClass("hide");
                control.allLayoutCheckedEl.prop("checked", false);
                $(control.selector).find('.select-layouts-custom .edit-layout-rows').addClass("hide");

                this.themeId = themeId;
                _.each(leyouts, function (leyout) {
                    control.removeRowFromModel(leyout);
                });

            } else {

                this.themeId = themeId;

                //disable layouts and hidden condition , if current module is main row
                if( this._get("main_row") ){

                    publicScopeEl.prop("disabled", true);

                    $(control.selector).find('.select-layouts-custom > li input[type="checkbox"]').prop("disabled", true);

                    $(control.selector).find('[name="sed_layout_public_type"]').filter( function(){
                        return $(this).prop("value") == "hidden";
                    }).prop("disabled", true);

                }

                publicScopeEl.prop("checked", true);
                $(control.selector).find("ul.select-pubic-scope").removeClass("hide");

                this.updateMultiCheckboxField( $(control.selector).find('[name="sed_scope_layout"]') , layouts); //alert( layouts );

                $(control.selector).find('.sub-theme-item input[value="' + control.currentLayout + '"]').prop("disabled", true);

                if (this.isCustomize()) {
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.customize_scope );
                    $(control.selector).find("ul.select-layouts-custom").addClass("hide");
                    this.updateRadioField(layoutPublicTypeEl, "customize");
                } else if (this.isHidden()) {
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.hidden_scope );
                    $(control.selector).find("ul.select-layouts-custom").addClass("hide");
                    this.updateRadioField(layoutPublicTypeEl, "hidden");
                } else {
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.public_scope );
                    $(control.selector).find("ul.select-layouts-custom").removeClass("hide");
                    this.updateRadioField(layoutPublicTypeEl, "normal");
                }

                $(control.selector).find('.select-layouts-custom input[name="sed_scope_layout"]').each(function () {
                    if ( $(this).prop('checked') ) { //&& $(this).val() != control.currentLayout
                        $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").removeClass("hide");
                    } else {
                        $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").addClass("hide");
                    }
                });

                if ($(control.selector).find('.select-layouts-custom input[name="sed_scope_layout"]').length == layouts.length) {
                    control.allLayoutCheckedEl.prop("checked", true);
                } else {
                    control.allLayoutCheckedEl.prop("checked", false);
                }

            }

        },

        getLayoutsByThemeId: function (themeId) {
            var control = this, layouts = [];

            $.each(this.models, function (layout, rows) {
                $.each(rows, function (idx, options) {
                    if (options.theme_id == themeId)
                        layouts.push(layout);
                });
            });

            return layouts;
        },

        isCustomize: function (themeId, layout) {
            var isCustom = false;

            themeId = ( _.isUndefined(themeId) || !themeId ) ? this.themeId : themeId;

            layout = ( _.isUndefined(layout) || !layout ) ? this.currentLayout : layout;

            _.each(this.models[layout], function (options) {
                if (options.theme_id == themeId) {
                    var index = $.inArray(api.currentPageInfo.id, options.exclude);
                    if (index > -1) {
                        isCustom = true;
                        return false;
                    }
                }
            });
            return isCustom;
        },

        /**
         * get row attributes
         * @param attr include : exclude , hidden , order , title , main_row
         * @param themeId
         * @param layout
         * @private
         */
        _get : function( attr , layout , themeId ){

            if( _.isUndefined( attr ) )
                return false;

            themeId = ( _.isUndefined(themeId) || !themeId ) ? this.themeId : themeId;

            layout = ( _.isUndefined(layout) || !layout ) ? this.currentLayout : layout; 

            if( attr == "themeId" )
                return themeId;

            var val;
            _.each(this.models[layout], function (options) {
                if ( options.theme_id == themeId && !_.isUndefined( options[attr] ) ) {
                    val = options[attr];
                }
            });

            if( _.isUndefined( val ) )
                return false;

            return val;
        },

        isHidden: function (themeId, layout) {
            var isHide = false;

            themeId = ( _.isUndefined(themeId) || !themeId ) ? this.themeId : themeId;

            layout = ( _.isUndefined(layout) || !layout ) ? this.currentLayout : layout;

            _.each(this.models[layout], function (options) {
                if (options.theme_id == themeId) {
                    var index = $.inArray(api.currentPageInfo.id, options.hidden);
                    if (index > -1) {
                        isHide = true;
                        return false;
                    }
                }
            });
            return isHide;
        },

        updateRowTitle: function (layout, themeId, title) {
            var control = this;

            this.models[layout] = _.map(this.models[layout], function (options) {
                if (options.theme_id == themeId) {
                    options.title = title;
                    return options;
                } else
                    return options;

            });

            control.refresh();
        },

        updateExcludeRows: function (type) {
            var control = this;

            this.models[control.currentLayout] = _.map(this.models[control.currentLayout], function (options) {
                if (options.theme_id == control.themeId) {
                    var index = $.inArray(api.currentPageInfo.id, options.exclude);

                    if (type == "add" && index == -1) {
                        options.exclude.push(api.currentPageInfo.id);
                    }

                    if (type == "remove" && index != -1) {
                        options.exclude.splice(index, 1);
                    }

                    return options;
                } else
                    return options;

            });

        },

        updateHiddenRows: function (type) {
            var control = this;

            this.models[control.currentLayout] = _.map(this.models[control.currentLayout], function (options) {
                if (options.theme_id == control.themeId) {
                    var index = $.inArray(api.currentPageInfo.id, options.hidden);

                    if (type == "add" && index == -1) {
                        options.hidden.push(api.currentPageInfo.id);
                    }

                    if (type == "remove" && index != -1) {
                        options.hidden.splice(index, 1);
                    }

                    return options;
                } else
                    return options;

            });

        },

        generateThemeId: function () {

            this.lastThemeId += 1;

            api('sed_last_theme_id').set(this.lastThemeId);

            return "theme_id_" + this.lastThemeId;

        },

        removeRowFromModel: function (leyout) {
            var control = this;
            this.models[leyout] = _.filter(this.models[leyout], function (row) {
                return row.theme_id != control.themeId;
            });

        },

        removeRowFromAllLayouts: function (themeId) {

            var control = this;

            _.each(this.models, function (rows, leyout) {
                control.models[leyout] = _.filter(control.models[leyout], function (row) {
                    return row.theme_id != themeId;
                });
            });

        },

        existThemeIdInLayout: function (leyout) {
            var control = this;
            if (_.isUndefined(this.models[leyout])) {
                return false;
            }

            var exist = false;

            _.each(this.models[leyout], function (layoutModel) {
                if (layoutModel.theme_id == control.themeId) {
                    exist = true;
                    return false;
                }
            });

            return exist;
        },

        addRowToModel: function (leyout , order) {

            if( this._get("main_row") )
                return;

            var control = this,
                options = {
                    order: order || 0,
                    theme_id: this.themeId,
                    exclude: [], // this row not show in pages with this ids
                    hidden: [],
                    title: ""
                };

            if (_.isUndefined(this.models[leyout]))
                this.models[leyout] = [];

            this.models[leyout].push(options);

        },
    });

    api.LayoutsManagerControl = api.Control.extend({

        ready: function () {
            var control = this;
            this.model = $.extend( true, {} , control.setting() );

            this.currentLayout = !_.isEmpty( api('page_layout')() ) ? api('page_layout')() : api.defaultPageLayout;

            this.view();
            this.updateView();

            api.previewer.bind( "ok_sedRemoveLayoutConfirm" , function () {
                control.removeLayout( $("#sed-confirm-message-dialog").data( "layout" ) , false );
                $("#sed-confirm-message-dialog").removeData( "layout" );
            });

            api.previewer.bind( "cancel_sedRemoveLayoutConfirm" , function () {
                $("#sed-confirm-message-dialog").removeData( "layout" );
            });

            $( api.sedDialogSettings.dialogSelector ).find(".sed_go_to_manage_layout_rows").livequery(function(){
                //if( _.isUndefined( self.panelsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){

                        api.appLayouts.manageLayoutRows( this );

                    });
                //}
            },function(){
                $(this).unbind("click");
            });

        },

        view : function(){
            var actionElement = this.container.find('[data-action]'),
                control = this;

            this.UI = {
                _Edit             : this.container.find('.sed-layout-edit') ,
                _EditInput        : this.container.find('.sed-layout-edit [name="edit-layout-title"]') ,
                _Add              : this.container.find('.sed-add-layout') ,
                _AddTitleInput    : this.container.find('.sed-add-layout [name="add-new-layout-title"]') ,
                _AddSlugInput     : this.container.find('.sed-add-layout [name="add-new-layout-slug"]') ,
                _ErrorBox         : this.container.find(".sed-layout-error-box p")
            };

            actionElement.livequery(function(){
                $(this).on("click" , function(){
                    var action = $(this).data("action");
                    switch ( action ){
                        case "save":
                            control.saveItem();
                            break;
                        case "save-close":
                            control.disableEditMode();
                            break;
                        case "edit":
                            control.currentLayoutEl = $(this).parents("li:first");
                            control.editItem( $(this).data("layoutTitle") , $(this).data("layout") );
                            break;
                        case "add":
                            control.addItem();
                            break;
                        case "delete":
                            control.removeLayout( $(this).data("layout") );
                            break;
                    }
                });
            }, function() {
                // unbind the change event
                $(this).unbind('click');
            });
        },

        refresh : function () {
            this.setting.set( this.model );
            this.updateView();
        },

        printAlert : function ( ) {
            this.UI._ErrorBox.html( this.errortext );
            this.UI._ErrorBox.slideDown( 300 ).delay( 5000 ).fadeOut( 400 );
        },

        updateView : function(){
            var template = api.template( "sed-layouts-manager" ),
                content = template( { layoutsSettings : this.model , currentLayout : this.currentLayout } );

            this.container.find(".sed-layout-lists > ul").html( content );
        },

        addItem : function ( title , slug ) {
            var title = this.UI._AddTitleInput.val(),
                slug = this.UI._AddSlugInput.val();

            if ( _.isEmpty( title ) ) {
                this.errortext = api.I18n.empty_layout_title;
                this.printAlert();
                return;
            }

            if ( !this.titleValidation( title ) ) {
                this.errortext = api.I18n.invalid_layout_title;
                this.printAlert();
                return;
            }

            if ( _.isEmpty( slug ) ) {
                this.errortext = api.I18n.empty_layout_slug;
                this.printAlert();
                return;
            }

            if ( !this.slugValidation( slug ) ) {
                this.errortext = api.I18n.invalid_layout_slug;
                this.printAlert();
                return;
            }

            if ( $.inArray( slug , _.keys( this.model ) ) == -1 ) {
                this.model[slug] = {
                    "title" : title
                };

                this.UI._AddTitleInput.val("");
                this.UI._AddSlugInput.val("");

                this.refresh();
                this.UI._ErrorBox.hide();
                
                //add main_row(content) model to sed_layouts_models
                var layoutModels = $.extend( true, {} , api('sed_layouts_models')() );

                var lastThemeId = parseInt( api('sed_last_theme_id')() );

                lastThemeId += 1;

                api('sed_last_theme_id').set( lastThemeId );

                var themeId = "theme_id_" + lastThemeId;

                layoutModels[slug] = [];

                layoutModels[slug].push({
                    order       : 0,
                    theme_id    : themeId,
                    exclude     : [], // this row not show in pages with this ids
                    hidden      : [],
                    title       : api.I18n.main_row_content ,
                    main_row    : true
                });

                var control = api.control.instance("main_layout_row_scope_control");

                if (!_.isUndefined(control)) {
                    control.models = layoutModels;
                }

                api('sed_layouts_models').set( layoutModels );

            } else {
                this.errortext = api.I18n.layout_already_exist;
                this.printAlert();
            }
        },

        removeLayout : function ( slug , confirm ) {
            confirm = _.isUndefined( confirm ) ? true : confirm;

            if( confirm === true ) {
                $("#sed-confirm-message-dialog").dialog("open");

                $("#sed-confirm-message-dialog").data("confirmEventId", "sedRemoveLayoutConfirm");

                $("#sed-confirm-message-dialog").data("layout", slug);

                $("#sed-confirm-message-dialog").html($("#sed-remove-layout-confirm-tpl").html());

                return false;
            }

            if( slug == "default" ){
                this.errortext = api.I18n.remove_default_layout;
                this.printAlert();
            }else if( slug == this.currentLayout ){
                this.errortext = api.I18n.remove_current_layout;
                this.printAlert();
            }else if( $.inArray( slug , _.keys( this.model ) ) > -1 ){
                //remove from sed_layouts_settings
                delete this.model[slug];
                this.refresh();

                //remove from sed_pages_layouts
                _.each( pageLayouts , function( layout , pagesGroup ){
                    if( layout == slug ){
                        api('sed_pages_layouts[' + pagesGroup + ']').set( "default" );
                    }
                });

                //remove from sed_layouts_models
                var layoutModels = $.extend( true, {} , api('sed_layouts_models')() );

                var themeIds = _.pluck( layoutModels[slug] , "theme_id" );

                $.each( layoutModels , function (layout, rows) {
                    if( layout != slug ) {
                        $.each(rows, function (idx, options) {
                            var index = $.inArray( options.theme_id , themeIds );
                            if ( index > -1 ){
                                themeIds.splice( index , 1);
                            }
                        });
                    }
                });

                if( !_.isUndefined( layoutModels[slug] ) ){
                    delete layoutModels[slug];

                    var control = api.control.instance("main_layout_row_scope_control");

                    if (!_.isUndefined(control)) {
                        control.models = layoutModels;
                    }

                    api('sed_layouts_models').set( layoutModels );
                }


                //remove theme row only in this layout from sed_layouts_content
                var layoutsContent = api.layoutsRowsContent.getClone();

                _.each( themeIds, function( themeId ){

                    if( !_.isUndefined( layoutsContent[themeId] ) )
                        delete layoutsContent[themeId];

                });

                api.layoutsRowsContent.set( layoutsContent );

            }else{
                this.errortext = api.I18n.layout_not_exist;
                this.printAlert();
            }
        },

        editItem : function ( title , slug ) {
            this.errortext = "";
            this.container.find(".sed-view-mode").removeClass("hide");

            this.UI._Edit.removeClass("hide");

            this.currentLayoutEl.find(".sed-view-mode").addClass("hide");

            this.UI._EditInput.val( title );
            this.UI._EditInput.data( "layout" , slug );

            this.UI._Edit.appendTo( this.currentLayoutEl.find(".sed-edit-mode") );
            this.UI._EditInput.focus();
        },

        saveItem : function ( ) {
            var title = this.UI._EditInput.val(),
                slug = this.UI._EditInput.data( "layout" );

            if ( _.isEmpty( title ) ) {
                this.errortext = api.I18n.empty_layout_title;
                this.printAlert();
                return;
            }

            if ( !this.titleValidation( title ) ) {
                this.errortext = api.I18n.invalid_layout_title;
                this.printAlert();
                return;
            }

            if ( $.inArray( slug , _.keys( this.model ) ) > -1 ) {
                this.model[slug] = {
                    "title" : title
                };
                this.refresh();
                this.UI._ErrorBox.hide();
            } else {
                this.errortext = api.I18n.invalid_layout;
                this.printAlert();
            }

        },

        titleValidation : function( title ){
            var pattern = /^[A-Za-z0-9_\-\s]{3,35}$/;
            return pattern.test( title );
        },


        slugValidation : function( slug ){
            var pattern = /^[A-Za-z0-9_\-]{3,20}$/;
            return pattern.test( slug );
        },

        disableEditMode : function () {
            this.currentLayoutEl.find(".sed-view-mode").removeClass("hide");
            this.UI._Edit.addClass("hide");
        }

    });

    api.controlConstructor = $.extend( api.controlConstructor, {
        layout_scope    : api.LayoutScopeControl ,
        layouts_manager : api.LayoutsManagerControl
    });


   $( function() {

       api.layoutsRowsContent = new api.LayoutsRowsContent({});

       api.appLayouts = new api.AppLayouts({});

       /*$( "#page_layout" ).click(function() {
           $( "#sed-dialog-sub-themes-settings" ).dialog( "open" );
       });

        $( "#sed-dialog-sub-themes-settings" ).dialog({
            autoOpen: false,
            width: 295,
            height: 295 ,
            resizable: false
        });*/

        var confirmActionType = "cancel";
        $("#sed-confirm-message-dialog").dialog({
            autoOpen      : false,
            modal         : true,
            width         : 350,  
            height        : 150 ,   //default is "auto"
            resizable     : false ,
            close         : function(){
                if( confirmActionType == "cancel" ){
                    var confirmEventId = $(this).data("confirmEventId");
                    api.previewer.trigger( "cancel_" + confirmEventId );
                    $( this ).html("");
                }else{
                    confirmActionType = "cancel";
                }
            },
            buttons: [
                {
                    text: api.I18n.ok_confirm,
                    click: function() {
                        confirmActionType = "ok";
                        $( this ).dialog( "close" );
                        var confirmEventId = $(this).data("confirmEventId");
                        api.previewer.trigger( "ok_" + confirmEventId );
                        $( this ).html("");
                    }
                },
                {
                    text:  api.I18n.cancel_confirm,
                    click: function () {
                        confirmActionType = "cancel";
                        $(this).dialog("close");
                    }
                }
            ]
        });

        /*$("#sed-app-control-main_layout_row_scope_control").find("ul.select-layouts-custom").livequery(function(){

            var template = api.template( "sed-all-layouts-checkbox-scope" ),
                content = template( { layoutsSettings : api('sed_layouts_settings')() } );

            $(this).find(">li.sub-theme-item").remove();
            $( content ).appendTo( $(this) );

        });*/

       api.previewer.bind('pageStaticContentInfo' , function( info ){
           api.pageStaticContentInfo = info;
       });

       /*
        $(".content").mCustomScrollbar({
        //autoHideScrollbar:true ,
        advanced:{
        updateOnBrowserResize:true, //update scrollbars on browser resize (for layouts based on percentages): boolean
           updateOnContentResize:true,
       },
       callbacks:{
        onOverflowY:function(){
            $(this).find(".mCSB_container").addClass("mCSB_ctn_margin");
        },

        onOverflowYNone:function(){
            $(this).find(".mCSB_container").removeClass("mCSB_ctn_margin");
        }
    }
});
        */

   });
})( sedApp, jQuery );