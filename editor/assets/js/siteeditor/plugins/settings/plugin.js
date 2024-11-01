(function( exports, $ ){

    var api = sedApp.editor;
    
    //handels of all loaded scripts in siteeditor app
    api.sedAppLoadedScripts = api.sedAppLoadedScripts || [];

    api.designEditorTpls = api.designEditorTpls || {};

    api.SiteEditorDialogSettings = api.Class.extend({

        initialize: function( options ){

            $.extend( this, options || {} );

            /**
             * Load Settings From 'js' or 'html' Template Or Load with 'Ajax'
             * @type {string}
             */
            this.templateType = "html";

            /**
             * Settings Type : include 'module' , 'app'
             * @type {string}
             */
            this.settingsType = "module";

            this.dialogsContents = {};

            this.dialogsTitles = {};

            this.ajaxProcessing = {};

            this.ajaxResetTmpls = {};

            this.ajaxCachTmpls = {};

            this.ajaxCachControls = {};

            this.backgroundAjaxload = {};

            this.optionsGroup = "";

            this.needToRefreshGroups = [];

            this.loading = {};

            this.currentSettingsId = "";

            this.pageOptionsLoaded = [];

            this.dialogSelector = "#sed-dialog-settings";

            this.rootBackBtn = {};

            this._dialogInit();

            this.ready();
        },

        /**
         *
         * @private
         */
        _dialogInit : function(){

            var self = this ,
                selector = this.dialogSelector;

            $( selector ).dialog({
                "autoOpen"  : false,
                "modal"     : false,
                //draggable: false,
                resizable   : false,
                "width"     : 295,
                "height"    : 600 ,
                "position"  : {
                    "my"        : "right-20",
                    "at"        : "right" ,
                    "of"        : "#sed-site-preview"
                },
                open: function () {
                    self._switchTmpl();
                },
                close : function(){
                    api.previewer.send("isOpenDialogSettings" , false);
                    self._resetTmpl();
                }
            });

            this._initDialogMultiLevelBox( selector );
            this._initDialogScrollBar( selector );

            //init expanded panels & other accordion settings
            $( ".accordion-panel-settings" ).livequery(function(){
                if( _.isUndefined( $(this).data( "acInit" ) ) ||  $(this).data( "acInit" ) !== true ){
                    $(this).accordion({
                        active: false,
                        collapsible: true,
                        event: 'click',
                        heightStyle: 'content',
                        create : function( event, ui ) {
                            $(this).data("acInit" , true);

                            api.Events.trigger( "accordionPanelSettingsInit" , event, ui , $(this) );
                        }
                    });
                }
            });

        },

        /**
         * for override in extends classes
         */
        ready : function(){
            var self = this;

            //open new group by button in other group settings
            $( this.dialogSelector ).find( ".open-new-group-settings" ).livequery(function(){

                $(this).on( "click.openNewGroup" , function(){

                    self.rootBackBtn[$(this).data("settingsId")] = {
                        prevSettingsId      : self.currentSettingsId ,
                        prevSettingsType    : self.settingsType
                    };

                    if( $(this).data("settingsType") == "module" ){

                        var dialogData = $.extend( {} , $(this).data("sedDialog") || {} , {
                            shortcodeName : $(this).data("settingsId")
                        });

                        var modelId;
                        if( !_.isUndefined( $(this).data( "modelId" ) ) ){
                            modelId = $(this).data( "modelId" );
                        }else{
                            return ;
                        }

                        var sedDialog = {
                            selector    : $(this).data("settingsId") ,
                            forceOpen   : true ,
                            reset       : true ,
                            data        : dialogData ,
                            extra       : {
                                attrs       : api.sedShortcode.getAttrs( modelId , true )
                            }
                        };

                        api.currentTargetElementId = modelId;

                        api.previewer.send('current_element' , api.currentTargetElementId  );

                        api.appModulesSettings.openInitDialogSettings( sedDialog , true );

                    }else{

                        api.appSettings.openInitDialogSettings( $(this).data("settingsId") , true , true );

                    }

                });

            }, function() {
                // unbind the change event
                $(this).unbind('click.openNewGroup');
            });

            api.Events.bind("afterAppendSettingsTmpl" , function( dialog , settingsType , currentSettingsId ){

                if( ! _.isUndefined( self.rootBackBtn[currentSettingsId] ) ) {
                    
                    var html = '<span id="sed_root_back_settings_btn" class="icon-close-level-box"><i class="icon-chevron-left"></i></span>';

                    var backBtn = $(html).prependTo( $(self.dialogSelector).siblings(".ui-dialog-titlebar:first").find("[data-self-level-box='dialog-level-box-settings-" + currentSettingsId + "-container']") );

                    backBtn.data( "settingsId" , self.rootBackBtn[currentSettingsId].prevSettingsId );

                    backBtn.data( "settingsType" , self.rootBackBtn[currentSettingsId].prevSettingsType );

                    delete self.rootBackBtn[currentSettingsId];

                }

            });

            api.Events.bind("endInitAppendSettingsTmpl" , function( dialog , settingsType , currentSettingsId ){

                if( ! _.isUndefined( self.rootBackBtn[currentSettingsId] ) ) {

                    var html = '<span id="sed_root_back_settings_btn" class="icon-close-level-box"><i class="icon-chevron-left"></i></span>';

                    var backBtn = $(html).prependTo( $(self.dialogSelector).siblings(".ui-dialog-titlebar:first").find("[data-self-level-box='dialog-level-box-settings-" + currentSettingsId + "-container']") );

                    backBtn.data( "settingsId" , self.rootBackBtn[currentSettingsId].prevSettingsId );

                    backBtn.data( "settingsType" , self.rootBackBtn[currentSettingsId].prevSettingsType );

                    delete self.rootBackBtn[currentSettingsId];

                }

            });

            $("#sed_root_back_settings_btn").livequery(function(){

                $(this).click(function(){

                    if( $(this).data( "settingsType" ) == "module" ) {
                        api.previewer.send('go_back_to_main_module', api.currentTargetElementId);
                    }else{
                        api.appSettings.openInitDialogSettings( $(this).data("settingsId") , true , false );
                    }
                });

            }, function() {
                // unbind the change event
                $(this).unbind('click');
            });

            api.Events.bind( "moduleDragStartEvent" , function( moduleName ){

                var shortcodeName;
                $.each(api.shortcodes , function( name, shortcode){
                    if(shortcode.asModule && shortcode.moduleName == moduleName){
                        shortcodeName = name;
                        return false;
                    }
                });

                if( ! shortcodeName )
                    return ;

                if( _.isUndefined( api.settings.groups[shortcodeName] ) && _.isUndefined( self.backgroundAjaxload[shortcodeName] ) && _.isUndefined( self.dialogsContents[shortcodeName] ) ) {
                    self._sendRequest(shortcodeName, "module" , shortcodeName);

                    self.backgroundAjaxload[shortcodeName] = 1;
                }

            });

            /**
             * Only For Page Options in this version
             */
            api.Events.bind( "afterResetpageInfoSettings" , function(){

                if( !_.isEmpty( self.needToRefreshGroups ) ){

                    //console.log( "----------self.needToRefreshGroups------------" , self.needToRefreshGroups );

                    _.each( self.needToRefreshGroups , function( optionsGroup ){

                        var settingId = optionsGroup + "_" + api.settings.page.id ,
                            isOpen = $( self.dialogSelector ).dialog( "isOpen" ),
                            isLoaded = optionsGroup == "sed_page_options" && $.inArray( settingId , self.pageOptionsLoaded ) > -1;


                        //console.log( "----------self.backgroundAjaxload[settingId]------------" , self.backgroundAjaxload[settingId] );

                        //console.log( "----------self.dialogsContents[settingId]------------" , self.dialogsContents[settingId] );

                        //console.log( "----------needToRefreshGroups ## settingId------------" , settingId );

                        if( _.isUndefined( self.backgroundAjaxload[settingId] ) && _.isUndefined( self.dialogsContents[settingId] ) && !isLoaded ) {

                            if( isOpen && self.optionsGroup == optionsGroup ){

                                self._resetTmpl();
                                self.currentSettingsId = settingId;
                                self._addLoading();

                                self._sendRequest(settingId, "app" , optionsGroup);
                            }else{

                                self._sendRequest(settingId, "app" , optionsGroup);
                                self.backgroundAjaxload[settingId] = 1;

                            }

                        }else if( !_.isUndefined( self.dialogsContents[settingId] ) ){

                            if( isOpen && self.optionsGroup == optionsGroup ) {
                                self._resetTmpl();
                                self.currentSettingsId = settingId;
                                self._switchTmpl();
                            }
                        }

                    });

                }

                if( api.settings.page.type == "post" ) {
                    //Post Options
                    var baseOptionsGroup = "sed_post_options_",
                        settingId = baseOptionsGroup + api.settings.page.id,
                        optionsGroup = settingId,
                        isOpen = $(self.dialogSelector).dialog("isOpen");

                    if( _.isUndefined( api.settings.groups[optionsGroup] ) ) {

                        $(".sed_customize_post_settings_btn").data("settingId", settingId);

                        $(".sed-customize-post-settings").show();

                        if (_.isUndefined(self.backgroundAjaxload[settingId]) && _.isUndefined(self.dialogsContents[settingId])) {

                            if (isOpen && self.optionsGroup.indexOf(baseOptionsGroup) === 0) {

                                self._resetTmpl();
                                self.currentSettingsId = settingId;
                                self._addLoading();

                                self._sendRequest(settingId, "app", optionsGroup);
                            } else {

                                self._sendRequest(settingId, "app", optionsGroup);
                                self.backgroundAjaxload[settingId] = 1;

                            }

                        } else if (!_.isUndefined(self.dialogsContents[settingId])) {

                            if (isOpen && self.optionsGroup.indexOf(baseOptionsGroup) === 0) {
                                self._resetTmpl();
                                self.currentSettingsId = settingId;
                                self._switchTmpl();
                            }
                        }

                    }
                }else{

                    if( $(self.dialogSelector).dialog("isOpen") && self.optionsGroup.indexOf( "sed_post_options_" ) === 0 ) {
                        $(self.dialogSelector).dialog('close');
                    }

                    $(".sed-customize-post-settings").hide();
                }


            });

        },

        /**
         *
         * @param dialogSelector
         * @private
         */
        _initDialogMultiLevelBox : function( dialogSelector ){

            $( dialogSelector ).multiLevelBoxPlugin({
                titleBar: $( dialogSelector ).siblings(".ui-dialog-titlebar:first"),
                innerContainer : $( dialogSelector ).find(".dialog-level-box-settings-container"),
            });
            $( dialogSelector ).siblings(".ui-dialog-titlebar:first").find(".close-page-box").livequery(function(){
                $(this).click(function(e){
                    $( dialogSelector ).dialog( "close" );
                });
            });

        },

        /**
         *
         * @param dialogSelector
         * @private
         */
        _initDialogScrollBar : function( dialogSelector ){
            var self = this;

            $( dialogSelector ).find('[data-multi-level-box="true"]').livequery(function(){
                if( _.isUndefined( self.dialogsContents[self.currentSettingsId] ) ){
                    $(this).mCustomScrollbar({
                        //autoHideScrollbar:true ,
                        advanced:{
                            updateOnBrowserResize:true, /*update scrollbars on browser resize (for layouts based on percentages): boolean*/
                            updateOnContentResize:true,
                        },
                        scrollButtons:{
                            enable:true
                        },
                        callbacks:{
                            onOverflowY:function(){
                                $(this).find(".mCSB_container").addClass("mCSB_ctn_margin");
                            },
                            onTotalScrollOffset:120,
                            onOverflowYNone:function(){
                                $(this).find(".mCSB_container").removeClass("mCSB_ctn_margin");
                            }
                        }
                    });
                }
            });

        },

        /**
         *
         * @param reset
         * @private
         */
        _switchTmpl : function( reset ){
            var self = this ,
                selector = this.dialogSelector;

            reset = !_.isUndefined( reset ) ? reset : true;

            if( !_.isUndefined( self.dialogsContents[self.currentSettingsId] ) ){

                var $currentElDialog = self.dialogsContents[self.currentSettingsId].appendTo( $( selector ) ).fadeIn( "slow" );
                self.dialogsTitles[self.currentSettingsId].appendTo( $( selector ).siblings(".ui-dialog-titlebar:first") );

                api.Events.trigger( "afterAppendSettingsTmpl" , $currentElDialog , this.settingsType , this.currentSettingsId );

                if( reset === true ) { 
                    $(selector).data('sed.multiLevelBoxPlugin')._reset();
                }
            }else{

                if( this.templateType == "ajax" ) {
                    this._ajaxLoadSettings();
                }else if( this.templateType == "html" ){

                    var $currentElDialog = $( $("#sed-tmpl-dialog-settings-" + self.currentSettingsId ).html() ).appendTo( $( selector ) ).fadeIn( "slow" );

                    api.Events.trigger( "afterInitAppendSettingsTmpl" , $currentElDialog , this.settingsType , this.currentSettingsId );

                    $( selector ).data('sed.multiLevelBoxPlugin').options.innerContainer = $( selector ).find(".dialog-level-box-settings-container");
                    $( selector ).data('sed.multiLevelBoxPlugin')._render();

                    api.Events.trigger( "endInitAppendSettingsTmpl" , $currentElDialog , this.settingsType , this.currentSettingsId );

                }

            }
        },

        /**
         *
         * @private
         */
        _resetTmpl : function(){
            var self = this ,
                selector = this.dialogSelector;

            $("#sed_root_back_settings_btn").remove();

            if( !_.isUndefined( this.ajaxProcessing[self.currentSettingsId] ) ){
                this.ajaxResetTmpls[self.currentSettingsId] = 'yes';
                return ;
            }

            api.Events.trigger( "beforeResetSettingsTmpl" , self.currentSettingsId , this.settingsType );

            self.dialogsTitles[self.currentSettingsId] = $( selector ).siblings(".ui-dialog-titlebar:first").children(".multi-level-box-title").detach();
            self.dialogsContents[self.currentSettingsId] = $( selector ).children().hide().detach();

            api.Events.trigger( "afterResetSettingsTmpl" , self.currentSettingsId , this.settingsType );

        },

        /**
         *
         * @private
         */
        _ajaxLoadSettings : function( ) {
            var self = this ,
                selector = this.dialogSelector;

            if (!_.isUndefined(self.ajaxResetTmpls[self.currentSettingsId])) {
                delete self.ajaxResetTmpls[self.currentSettingsId];
                return;
            }
 
            if (!_.isUndefined(self.ajaxCachTmpls[self.currentSettingsId])) {

                var output = self.ajaxCachTmpls[self.currentSettingsId];

                var $currentElDialog = $(output).appendTo($(selector));

                api.Events.trigger("afterInitAppendSettingsTmpl", $currentElDialog, self.settingsType, self.currentSettingsId);

                $(selector).data('sed.multiLevelBoxPlugin').options.innerContainer = $(selector).find(".dialog-level-box-settings-container");
                $(selector).data('sed.multiLevelBoxPlugin')._render();

                delete self.ajaxCachTmpls[self.currentSettingsId];

                var controls = $.extend( true , {} , self.ajaxCachControls[self.currentSettingsId]);

                self.setControls( controls , self.currentSettingsId , self.settingsType );

                api.Events.trigger("endInitAppendSettingsTmpl", $currentElDialog, self.settingsType, self.currentSettingsId);

                delete self.ajaxCachControls[self.currentSettingsId];

                return;
            }

            this._addLoading();

            if (!_.isUndefined(this.backgroundAjaxload[this.currentSettingsId])) {
                delete this.backgroundAjaxload[this.currentSettingsId];
                return;
            }

            this._sendRequest( this.currentSettingsId , this.settingsType , this.optionsGroup );
        },

        _addLoading : function( ){

            if( _.isUndefined( this.loading[this.currentSettingsId] ) ) {
                var tpl = api.template("sed-ajax-loading"), html;

                html = tpl({type: "medium"}); // loadingType : "small" || "medium" || ""

                this.loading[this.currentSettingsId] = $(html).appendTo($(this.dialogSelector));//

                this.loading[this.currentSettingsId].show();
            }

        },

        _sendRequest : function( settingIdReq , settingsTypeReq , optionsGroup ) {

            var self = this,
                selector = this.dialogSelector;

            this.ajaxProcessing[settingIdReq] = 1;

            var data = {
                action          : 'sed_load_options',
                setting_id      : settingIdReq ,
                setting_type    : settingsTypeReq ,
                options_group   : optionsGroup ,
                nonce           : api.settings.nonce.options.load,
                sed_page_ajax   : 'sed_options_loader'
            };

            data = api.applyFilters( 'sedAjaxLoadOptionsDataFilter' , data );

            var ajaxOptionsRequest = api.wpAjax.send({

                type: "POST",
                //url: api.settings.url.ajax,
                data : data,
                success : function( responseData ){

                    var output = responseData.output ,
                        controls = responseData.controls,
                        relations = responseData.relations ,
                        settings = responseData.settings ,
                        panels = responseData.panels ,
                        settingId = responseData.settingId ,
                        settingType = responseData.settingType,
                        groups  = responseData.groups ,
                        designTemplate = responseData.designTemplate ,
                        partials = responseData.partials,
                        previewParams = responseData.previewParams;

                    delete self.ajaxProcessing[settingId];

                    if( !_.isUndefined( self.loading[settingId] ) ) {
                        self.loading[settingId].hide();
                        self.loading[settingId].remove();
                        delete self.loading[settingId];
                    }

                    self.setDependencies( relations , settingId );

                    self.setSettings( settings , settingId );

                    self.setGroups( groups , settingId );

                    self.setPanels( panels , settingId );

                    self.sedDesignTemplate( designTemplate , settingId );

                    self.setDynamicPartials( partials , settingId );

                    self.setPreviewParams( previewParams , settingId );

                    if( _.isUndefined( self.ajaxResetTmpls[settingId] ) && _.isUndefined( self.backgroundAjaxload[settingId] ) ) {

                        var $currentElDialog = $(output).appendTo($(selector));

                        api.Events.trigger( "afterInitAppendSettingsTmpl" , $currentElDialog , settingType , settingId );

                        $( selector ).data('sed.multiLevelBoxPlugin').options.innerContainer = $( selector ).find(".dialog-level-box-settings-container");
                        $( selector ).data('sed.multiLevelBoxPlugin')._render();

                        self.setControls( controls , settingId , settingType );

                        api.Events.trigger( "endInitAppendSettingsTmpl" , $currentElDialog , settingType , settingId );

                    }else{

                        self.ajaxCachTmpls[settingId] = output;

                        self.ajaxCachControls[settingId] = controls;

                        if( ! _.isUndefined( self.ajaxResetTmpls[settingId] ) )
                            delete self.ajaxResetTmpls[settingId] ;

                        if( ! _.isUndefined( self.backgroundAjaxload[settingId] ) )
                            delete self.backgroundAjaxload[settingId] ;

                    }


                },

                error : function( responseData ){

                    var settingId = responseData.settingId;

                    if( !_.isUndefined( self.loading[settingId] ) ) {
                        self.loading[settingId].hide();
                        self.loading[settingId].remove();
                        delete self.loading[settingId];
                    }

                    if( !_.isUndefined( self.ajaxResetTmpls[settingId] ) ) {
                        delete self.ajaxResetTmpls[settingId];
                    }

                    if( !_.isUndefined( self.backgroundAjaxload[settingId] ) ) {
                        delete self.backgroundAjaxload[settingId];
                    }

                    delete self.ajaxProcessing[settingId];

                    var template = api.template("sed-load-options-errors"), html;

                    html = template( { message: responseData.message } ); // loadingType : "small" || "medium" || ""

                    $(html).appendTo( $(self.dialogSelector) );

                    //for show dialog title
                    $( selector ).data('sed.multiLevelBoxPlugin').options.innerContainer = $( selector ).find(".dialog-level-box-settings-container");
                    $( selector ).data('sed.multiLevelBoxPlugin')._render();
                }


            });
                //container   : this.dialogSelector

        },

        setDependencies : function( relations , settingId ){

            if( !_.isUndefined( relations ) && !_.isEmpty( relations ) && _.isObject( relations ) ){
                var groupRelations = {};

                groupRelations[settingId] = relations;

                api.settingsRelations = $.extend( api.settingsRelations , groupRelations);

            }

        },

        sedDesignTemplate : function( designTemplate , settingId ){

            if( !_.isUndefined( designTemplate ) && !_.isEmpty( designTemplate ) ){

                api.designEditorTpls[settingId] = designTemplate; 

            }

        },

        setSettings : function( settings , settingId ){

            if( !_.isEmpty( settings ) ) {
                var createdSettings = {};

                _.each( settings , function (settingArgs, id) {

                    if ( ! api.has( id ) ) {

                        var setting = api.create(id, id, settingArgs.value, {
                            transport: settingArgs.transport || "refresh",
                            previewer: api.previewer,
                            stype: settingArgs.type || "general",
                            dirty: settingArgs.dirty
                        });

                        api.settings.settings[id] = settingArgs;

                        createdSettings[id] = settingArgs.value;

                    }

                });

                api.previewer.send( "settings" , createdSettings );

                $.each( settings , function ( id , settingArgs ){

                    if( $.inArray( id , _.keys( createdSettings ) ) == -1 )
                        return true; //continue

                    if ( settingArgs.dirty ) {
                        var setting = api(id);
                        setting.callbacks.fireWith(setting, [setting.get(), {}]);
                    }

                });

            }

        },

        setDynamicPartials : function( partials ){

            api.previewer.send( 'addDynamicPartials' , partials );

        },

        setPreviewParams : function( previewParams , settingId ){

            api.previewer.send( 'addDynamicPreviewParams' , previewParams );

        },

        setPanels : function( panels , settingId ){

            if( _.isUndefined( api.settingsPanels[settingId] ) ) {
                api.settingsPanels[settingId] = {};
            }

            _.each( panels , function( data , id ){
                api.settingsPanels[settingId][id] = data;
            });

        },

        /**
         * Create Controls after load & append in settings dialog
         * @todo create app settings controls just on time like module settings
         *
         * @param controls
         * @param settingId
         * @param settingType
         */
        setControls : function( controls , settingId , settingType ){

            if( !_.isEmpty( controls ) ){

                if( _.isUndefined( api.sedGroupControls[settingId] ) ) {
                    api.sedGroupControls[settingId] = [];
                }

                if( settingType == "module" ){
                    
                    _.each( controls , function ( data , id ) {
                        api.sedGroupControls[settingId].push( data );
                    });

                    if ( !_.isUndefined( api.appModulesSettings.sedDialog.data.panelId ) )
                        api.appModulesSettings.initSettings( api.appModulesSettings.sedDialog.data.panelId );
                    else
                        api.appModulesSettings.initSettings();

                } else {

                    _.each(controls, function (data, id) {

                        if ($.inArray(id, _.keys(api.settings.controls)) == -1) {

                            api.settings.controls[id] = data;

                            api.Events.trigger("renderSettingsControls", id, data);

                        }

                        api.sedGroupControls[settingId].push( data );

                    });

                    api.Events.trigger("after_app_settings_update", settingId );

                    api.Events.trigger("after_group_settings_update", settingId );
                }
            }

        },

        setGroups : function( groups , settingId ){
            var self = this;

            if( !_.isEmpty( groups ) ){

                _.each( groups , function( data , id ){

                    if( id == "sed_page_options" && $.inArray( settingId , self.pageOptionsLoaded ) == -1 ){

                        self.pageOptionsLoaded.push( settingId );

                    }

                    if( $.inArray( id , _.keys( api.settings.groups ) ) == -1  ){

                        api.settings.groups[id] = data;
                    }

                    if( data.pages_dependency && $.inArray( id , self.needToRefreshGroups ) == -1 ){
                        self.needToRefreshGroups.push( id );
                    }

                });

            }

        },

        openInitDialogSettings : function( settingId , forceOpen , reset , settingsType , templateType , optionsGroup ){
            var isOpen = $( this.dialogSelector ).dialog( "isOpen" );

            //needToUpdateSettings = !_.isUndefined( needToUpdateSettings) ? needToUpdateSettings : true ;
            
            forceOpen = !_.isUndefined( forceOpen) ? forceOpen : true ; 

            if( !isOpen && forceOpen === true ){

                this.currentSettingsId = settingId;
                //this.panelsNeedToUpdate = [];

                this.optionsGroup = optionsGroup;

                this.templateType = templateType;

                this.settingsType = settingsType;

                $( this.dialogSelector ).dialog( "open" );

                api.previewer.send("isOpenDialogSettings" , true);

            }else if( isOpen ){

                this._resetTmpl();

                this.optionsGroup = optionsGroup;

                this.templateType = templateType;

                this.settingsType = settingsType;

                this.currentSettingsId = settingId;

                //this.panelsNeedToUpdate = [];

                reset =  !_.isUndefined( reset ) ? reset : true;

                this._switchTmpl( reset );

            }else
                return ;
        }

    });

    api.AppModulesSettings = api.Class.extend({

        initialize: function( options ){

            $.extend( this, options || {} );

            this.currentDialogSelector = "none";

            this.sedDialog;
            //in this version only using in design panel(for back btn and update settings)
            this.forceUpdate = false;
            this.rowContainerSettingsData = {};
            //this.lastSedDialog;
            this.panelsNeedToUpdate = [];
            //this.lastPanelsNeedToUpdate = [];

            this.dialogSelector = "#sed-dialog-settings";

            this.ready();
        },

        ready : function(){
            var self = this;

            this.initDialogSettings();

            //when render open settings dialog
            api.previewer.bind( 'openDialogSettings' , function( data ) {
                self.openInitDialogSettings( data );
            });

            //when render open settings dialog
            api.previewer.bind( 'rowContainerSettingData' , function( data ) {
                self.rowContainerSettingsData = data;
            });


            //when render select one module or click on settings icon left side modules
            api.previewer.bind( 'currentModuleSelected' , function( dataElement ) {
                self.moduleSelect( dataElement );
            });

            //when change skin current element updated
            api.previewer.bind( 'changeCurrentElementBySkinChange', function( dataEl ) {
                self.updateByChangePattern( dataEl );
            });

            api.previewer.bind( 'shortcodeControlsUpdate' , function( data  ) {
                self.shortcodeControlsUpdate( data );
            });

            api.previewer.bind( "currentElementId" , function( id ) {
                api.currentTargetElementId = id;
            });

            api.previewer.bind( 'currentPostId' , function( id ) {
                api.currentPostId = id;
            });

            this.readySettingsType();

        },

        readySettingsType : function(){
            var self = this;

            api.Events.bind("animationSettingsType" , function(dataElement , extra){  //alert( dataElement.shortcodeName );

                $( self.dialogSelector ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_" + dataElement.shortcodeName + "_animation"  );

            });

            api.Events.bind("changeSkinSettingsType" , function(dataElement , extra){

                $( self.dialogSelector ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_" + dataElement.shortcodeName + "_skin" );

                api.Events.trigger( "loadSkinsDirectly" , dataElement.moduleName );

            });

            api.Events.bind("linkToSettingsType" , function(dataElement , extra){

                $( self.dialogSelector ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( dataElement.shortcodeName + "_link_to_panel_level_box" );

            });

            api.Events.bind("afterAppendSettingsTmpl" , function( dialog , settingsType , currentSettingsId ){

                if( settingsType == "module" ){

                    api.Events.trigger( "afterAppendModulesSettingsTmpl" , self , dialog );

                }

            });

            api.Events.bind("afterInitAppendSettingsTmpl" , function( dialog , settingsType , currentSettingsId ){

                if( settingsType == "module" ){ 

                    api.Events.trigger( "afterInitAppendModulesSettingsTmpl" , self , dialog );

                }

            });

            api.Events.bind("endInitAppendSettingsTmpl" , function( dialog , settingsType , currentSettingsId ){

                if( settingsType == "module" ){

                    if( self.sedDialog.data.shortcodeName == "sed_row" ){
                        var html = '<span id="row_back_settings_element" class="icon-close-level-box"><i class="icon-chevron-left"></i></span>';
                        $(self.dialogSelector).siblings(".ui-dialog-titlebar:first").find("[data-self-level-box='dialog-level-box-settings-sed_row-container']").prepend( $(html) );
                    }

                }

            });

            api.Events.bind("beforeResetSettingsTmpl" , function( currentSettingsId , settingsType ){

                if( settingsType == "module" ){

                    api.Events.trigger( "beforeResetDialogSettingsTmpl" , currentSettingsId );

                }

            });

        },

        initDialogSettings : function(){
            var self = this ,
                selector = self.dialogSelector;

            api.Events.bind( "beforerCreateSettingsControls" , function(id, data , extra){
                if( !_.isUndefined( data ) && !_.isUndefined( data.is_image_size ) ){
                    var $field_id = 'sed_pb_' + id;

                    var optionsStr = "" ;

                    if( !_.isUndefined( data.has_custom_size ) && data.has_custom_size ){
                        optionsStr += '<option value="" > ' + api.I18n.custom_size + ' </option>'
                    }
                    
                    _.each( api.addOnSettings.imageModule.sizes  , function( size , key ){
                        var sizeWidth = !_.isUndefined( size.width ) ? size.width : "" ,
                            sizeHeight = !_.isUndefined( size.height ) ? size.height : "" ;
                        optionsStr += '<option value="' + key + '" > ' + size.label + ' - ' + sizeWidth + " x " + sizeHeight + ' </option>';
                    });

                    $( "#" + $field_id ).html( optionsStr );
                }
            });

            $( selector ).find(".go-panel-element-update").livequery(function(){
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){ //go-accordion-panel

                        var panelId = $(this).data("panelId");

                        if( panelId ) {

                            if ($.inArray(panelId, self.panelsNeedToUpdate) == -1) {
                                self.initSettings(panelId);
                                self.panelsNeedToUpdate.push(panelId);
                            }

                        }

                    });
                }
            });

            $( selector ).find(".go-row-container-settings").livequery(function(){
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){ //go-accordion-panel
                        //self.lastSedDialog = self.sedDialog;
                        self.lastTargetElementId = _.clone( api.currentTargetElementId );
                        //self.lastPanelsNeedToUpdate = self.panelsNeedToUpdate;
                        api.currentTargetElementId = self.rowContainerSettingsData.rowId;
                        api.previewer.send('current_element' , api.currentTargetElementId  );
                        self.openInitDialogSettings( self.rowContainerSettingsData );
                    });
                }
            });

            $("#row_back_settings_element").livequery(function(){
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){ //go-accordion-panel
                        api.currentTargetElementId = self.lastTargetElementId;
                        //api.previewer.send('current_element' , api.currentTargetElementId  );
                        api.previewer.send( 'go_back_to_main_module' , api.currentTargetElementId  );
                        //self.panelsNeedToUpdate = self.lastPanelsNeedToUpdate;
                        //self.openInitDialogSettings( self.lastSedDialog , false );
                    });
                }
            });

            //for update after click on back btn
            $( self.dialogSelector ).siblings(".ui-dialog-titlebar:first").find('[data-self-level-box] >.icon-close-level-box').livequery(function(){
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){
                        if( !_.isUndefined( self.sedDialog ) && $(this).parent().data("selfLevelBox") == 'dialog_page_box_'+ self.sedDialog.data.shortcodeName +'_design_panel' && self.forceUpdate === true ){
                            self.initSettings();
                            self.forceUpdate = false;
                        }
                    });
                }
            });

            api.previewer.bind( 'dialogSettingsClose' , function( ) {

                var isOpen = $( self.dialogSelector ).dialog( "isOpen" );
                if( isOpen )
                    $( self.dialogSelector ).dialog( "close" );

            });

        },

        updateSettings: function(  id , data , shortcodeName , extra , needReturn ){

            if( $.inArray(id , _.keys( api.settings.controls ) ) == -1 ){

                api.settings.controls[id] = data;

                api.Events.trigger( "renderSettingsControls" , id, data , extra);

                var control = api.control.instance( id );

                $( control.container ).parents(".row_settings:first").show();

            }else {

                var control = api.control.instance( id );

                $( control.container ).parents(".row_settings:first").show();

                if( !_.isUndefined(extra.attrs) ) {
                    control.update(extra.attrs);
                }else {
                    control.update();
                }

            }

            if( !_.isUndefined(needReturn) && needReturn === true )
                return control;

        },

        postButtonIdUpdate : function( dataElement ){
            //add data-post-id to post edit buttons
            if( !_.isUndefined( dataElement.contextmenuPostId ) ){
                var postEditBtn = $( this.dialogSelector ).find(".sed_post_edit_button");

                if(postEditBtn.length > 0){
                    postEditBtn.data("postId" , dataElement.contextmenuPostId);
                }
            }
        },

        widgetButtonIdUpdate : function( dataElement ){

            //add data-widget-id-base to widget settings buttons
            if( !_.isUndefined( dataElement.contextmenuWidgetIdBase ) ){
                var widgetIdBase = dataElement.contextmenuWidgetIdBase ,
                    widgetTitle = $("#widget-tpl-" + widgetIdBase ).data( "widgetTitle" );

                //$( this.dialogSelector ).dialog( "option" , "title", widgetTitle );
                $( this.dialogSelector ).siblings(".ui-dialog-titlebar:first").find('[data-self-level-box="dialog-level-box-settings-sed_widget-container"] >.ui-dialog-title').text( widgetTitle );

                var widgetIdBaseBtn = $(this.dialogSelector).find(".sed_widget_button");

                if(widgetIdBaseBtn.length > 0){
                    widgetIdBaseBtn.data("widgetIdBase" , widgetIdBase);
                }
            }
        },

        /**
         * Get settings in root or 'inner_box' Or 'expanded'
         * @Todo : if 'default' panel in 'inner_box' Or 'expanded' , create and update in
         * -first time And not sync with update panel , and it's need to update with panel
         *
         * @param panelId
         * @returns {*}
         */
        getSettings : function( panelId ){
            var dataElement = this.sedDialog.data;

            if( !_.isUndefined( panelId ) && panelId && panelId != "root" ){

                return _.filter( api.sedGroupControls[dataElement.shortcodeName] , function( data ){

                    //remove widget instance from settings
                    if( !_.isUndefined( data.shortcode ) && data.shortcode == "sed_widget" && !_.isUndefined( data.attr_name ) && data.attr_name == "instance")
                        return false;

                    if( _.isUndefined( data.panel ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName] ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName][data.panel] ) )
                        return false;

                    var panel = api.settingsPanels[dataElement.shortcodeName][data.panel];

                    return panel.id == panelId;

                });

            }else{

                return _.filter( api.sedGroupControls[dataElement.shortcodeName] , function( data ){

                    //remove widget instance from settings
                    if( !_.isUndefined( data.shortcode ) && data.shortcode == "sed_widget" && !_.isUndefined( data.attr_name ) && data.attr_name == "instance")
                        return false;

                    if( _.isUndefined( data.panel ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName] ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName][data.panel] ) )
                        return true;

                    var panel = api.settingsPanels[dataElement.shortcodeName][data.panel];

                    return $.inArray( panel.type , [ 'inner_box' ] ) == -1; //, 'expanded'

                });
            }

        },

        initSettings : function( panelId ){

            if( _.isUndefined( this.sedDialog ) )
                return ;

            var self        = this ,
                dataElement = this.sedDialog.data ,
                extra       = $.extend({} , this.sedDialog.extra || {}) ,
                settings;

            if( _.isUndefined( panelId ) )
                settings = this.getSettings();
            else
                settings = this.getSettings( panelId );

            _.each( settings , function( data ) {
                self.updateSettings( data.control_id , data , dataElement.shortcodeName , extra);

            });

            if(this.forceUpdate === true)
                return ;

            this.postButtonIdUpdate( dataElement );
            this.widgetButtonIdUpdate( dataElement );

            if( !_.isUndefined( dataElement.settingsType ) ){
                api.Events.trigger( dataElement.settingsType + "SettingsType" , dataElement , extra );
            }

            api.Events.trigger(  "after_shortcode_update_setting" , dataElement.shortcodeName );

            api.Events.trigger(  "after_group_settings_update" , dataElement.shortcodeName );

            api.Events.trigger( dataElement.shortcodeName + "_dialog_settings" , extra.attrs || {} );
        },

        // , needToUpdateSettings
        openInitDialogSettings : function( sedDialog , forceOpen ){

            //needToUpdateSettings = !_.isUndefined( needToUpdateSettings) ? needToUpdateSettings : true ;

            this.sedDialog = sedDialog;

            this.currentSettingsId = sedDialog.selector;

            this.panelsNeedToUpdate = [];

            var reset =  !_.isUndefined( sedDialog.reset ) ? sedDialog.reset : true;

            var needToUpdate = !_.isUndefined( api.sedGroupControls[this.currentSettingsId] );

            api.sedDialogSettings.openInitDialogSettings( this.currentSettingsId , forceOpen , reset , "module" , "ajax" , this.currentSettingsId );//"html"

            /**
             * update settings if created current settings template already
             */
            if( needToUpdate ) {
                if (!_.isUndefined(sedDialog.data.panelId))
                    this.initSettings(sedDialog.data.panelId);
                else
                    this.initSettings();
            }

        },

        //when module select
        moduleSelect : function( sedDialog ){

            var forceOpen = !_.isUndefined( sedDialog.forceOpen ) && sedDialog.forceOpen === true;
            this.openInitDialogSettings( sedDialog , forceOpen );

        },

        //when skin change
        updateByChangePattern : function( dataEl ){ 
            var self = this;

      		_.each( api.sedGroupControls[dataEl.shortcode_name] , function( data ) {

                if( !_.isUndefined( data.attr_name ) ){

                    var id = dataEl.shortcode_name + "_" + data.attr_name; 

                    api.previewer.trigger( "currentElementId" ,  dataEl.elementId );

                    self.updateSettings( id, data , dataEl.shortcode_name , {attrs : dataEl.attrs});

                }

      		});

            api.Events.trigger(  "after_shortcode_update_setting" , dataEl.shortcode_name );

            api.Events.trigger( dataEl.shortcode_name + "_dialog_settings" , dataEl.attrs || {} );

        },

        //after media sync attachment for title & alt & description update value by default media info
        shortcodeControlsUpdate : function( data ){
            var self = this ,
                shortcode = data.shortcode ,
                attrs = data.attrs ,
                targetAttrs = data.targetAttrs || [] ;


      		_.each( api.sedGroupControls[shortcode] , function( data ) {
                if(!$.isArray(targetAttrs) || targetAttrs.length == 0  || $.inArray( data.attr_name , targetAttrs) != -1 ){

                    self.updateSettings( shortcode + "_" + data.attr_name , data , shortcode , { attrs : attrs});

                }
      		});
        }

    });


    $( function() {

        api.sedDialogSettings       = new api.SiteEditorDialogSettings({});

        api.appModulesSettings      = new api.AppModulesSettings({});

    });

})( sedApp, jQuery );