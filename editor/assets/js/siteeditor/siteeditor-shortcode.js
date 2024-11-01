(function( exports, $ ) {

    var api = sedApp.editor;

    api.SiteEditorShortcode = api.Class.extend({

        findChildren: function( shortcodesModels , parent_id ){
            var self = this , allChildren = [];

            $.each( shortcodesModels , function(index , shortcode){
                if(shortcode.parent_id == parent_id){
                    allChildren.push(shortcode);
                    allChildren = $.merge( allChildren , self.findChildren( shortcodesModels , shortcode.id  ) );
                }
            });

            return allChildren;
        },

        findParentOf : function( shortcodesModels , parent_id ){
            var self = this , allChildren = [];

            $.each( shortcodesModels , function(index , shortcode){
                if(shortcode.parent_id == parent_id){
                    allChildren.push(shortcode);
                }
            });

            return allChildren;
        },

        index: function( id , shortcodesModels ){

            return _.findIndex( shortcodesModels , {id : id} );

        },

        findModelsById : function( shortcodesModels , id ){

            var parentModel = _.findWhere( shortcodesModels , {id : id} );

            if( _.isUndefined( parentModel ) || !parentModel )
                return [];

            var models = this.findChildren( shortcodesModels , id ) || [];
            models.unshift( parentModel );

            return models;
        },

        clone : function( shortcodesModels ){

            var cloneModels = _.map( shortcodesModels , function( model ){
                var copyModel = $.extend( true, {} , model );
                return copyModel;
            });

            return cloneModels;
        },

        getModelsType : function(){
            
            var $el = $("#website")[0].contentWindow.jQuery( '[sed_model_id="' + api.currentTargetElementId + '"]' );
            var parentC = $el.parents(".sed-pb-post-container:first");

            return parentC.data("contentType");
        },

        readyPattern : function( shortcodesModels ){
            
            var modulesShortcodesCopy = this.clone( shortcodesModels );

            modulesShortcodesCopy = this.modifyModelsIds( modulesShortcodesCopy );

            if( !_.isUndefined( modulesShortcodesCopy[0].theme_id ) )
                delete modulesShortcodesCopy[0].theme_id;

            if( !_.isUndefined( modulesShortcodesCopy[0].is_customize ) )
                delete modulesShortcodesCopy[0].is_customize;
            
            return modulesShortcodesCopy;
        },

        modifyModelsIds : function( modulesShortcodesCopy ){
            var self = this;
            this.Ids = {};

            modulesShortcodesCopy = _.map( modulesShortcodesCopy , function(shortcode){

                var id = api.pageBuilder.getNewId( );

                self.Ids[shortcode.id] = id;

                shortcode.id = id;
                shortcode.attrs.sed_model_id = id;

                return shortcode;
            });

            modulesShortcodesCopy = _.map( modulesShortcodesCopy , function(shortcode){

                if( !_.isUndefined( self.Ids[shortcode.parent_id] ) )
                    shortcode.parent_id = self.Ids[shortcode.parent_id];

                return shortcode;
            });

            return modulesShortcodesCopy;
        },

        /**
         * only using in front end editor
         * @param Model Id
         * @returns array , collection of shortcode models
         */
        getContentModel : function( modelId ){

            var element = $('body').hasClass("siteeditor-app") ? $( '[sed_model_id="' + modelId + '"]' ) : $("#website")[0].contentWindow.jQuery( '[sed_model_id="' + modelId + '"]' ),
                parentC = element.parents(".sed-pb-post-container:first"),
                type = parentC.data("contentType") ,
                postId = parentC.data("postId");

            if(type == "theme")
                var contentModel = api.pagesThemeContent[postId];
            else
                var contentModel = api.postsContent[postId];

            return contentModel;
        },

        replaceModel : function( oldModelId , newModel ){

            var contentModel = this.getContentModel( oldModelId ) ,
                oldIndex = this.index( oldModelId , contentModel ) ,
                oldModel = this.findModelsById( contentModel , oldModelId ),
                oldModelLength = oldModel.length;

            var args = $.merge([ oldIndex , oldModelLength ] , newModel);

            Array.prototype.splice.apply( contentModel , args );

        },

        getShortcode: function( modelId  ){

            var contentModel = this.getContentModel( modelId );

            var $thisShortcode = _.findWhere( contentModel , {id : modelId} );

            return $thisShortcode;
        },

        getAttrs : function( modelId , includeContent ){

            includeContent = !!( !_.isUndefined( includeContent ) && includeContent === true );

            var $thisShortcode = this.getShortcode( modelId );

            if( !$thisShortcode ){
                //api.log("for : " + id + " not found shortcode");
                return ;
            }else {

                if( includeContent === false )
                    return $thisShortcode.attrs;

                var contentModel = this.getContentModel( modelId );

                var shortcodeContent = _.findWhere( contentModel , { tag : "content" , parent_id : modelId } );

                if( ! shortcodeContent ){
                    return $thisShortcode.attrs;
                }

                var newAttrs = $.extend( true , {} , $thisShortcode.attrs );

                newAttrs.sed_shortcode_content = shortcodeContent.content;//api.rawurldecode() 

                return newAttrs;
            }

        },

        getPatternScripts : function( shortcodesModels ){

            var _scripts = [];

            _.each( shortcodesModels , function( shortcode ){

                if( !_.isUndefined( api.shortcodes[shortcode.tag] ) ) {

                    _scripts = $.merge(_scripts, api.shortcodesScripts[shortcode.tag] || []);

                }


            });

            return _scripts;

        },

        getPatternModulesScripts : function( shortcodesModels ){

            var _scripts = [];

            _.each( shortcodesModels , function( shortcode ){

                if( !_.isUndefined( api.shortcodes[shortcode.tag] ) ) {

                    var currShortcodeInfo = api.shortcodes[shortcode.tag];

                    if (!_.isUndefined(currShortcodeInfo.asModule) && currShortcodeInfo.asModule) {
                        if (!_.isUndefined(api.ModulesEditorJs[currShortcodeInfo.moduleName])) {
                            _scripts.push(api.ModulesEditorJs[currShortcodeInfo.moduleName]);
                        }
                    }

                }

            });

            return _scripts;

        },

        getPatternStyles : function( shortcodesModels ){

            var _styles = [];

            _.each( shortcodesModels , function( shortcode ){

                if( !_.isUndefined( api.shortcodes[shortcode.tag] ) ) {

                    _styles = $.merge(_styles, api.shortcodesStyles[shortcode.tag] || []);

                }

            });

            return _styles;

        },

        getPatternTransport : function( shortcodesModels ){

            var transport = 'default';

            $.each( shortcodesModels , function( index , shortcode ){

                if( !_.isUndefined( api.shortcodes[shortcode.tag] ) ) {

                    var _transport = api.pageBuilder.getModuleTransport( shortcode.tag  , "shortcode");

                    if( _transport == "ajax" ){
                        transport = "ajax";
                        return false;
                    }

                }

            });

            return transport;

        },

        getPatternAttachmentIds : function( shortcodesModels ){

            var attachmentIds = [];

            _.each( shortcodesModels , function( shortcode ){

                if( !_.isUndefined( api.shortcodes[shortcode.tag] ) && !_.isUndefined( shortcode.attrs ) && !_.isUndefined( api.shortcodes[shortcode.tag].mediaAtts ) && !_.isEmpty( api.shortcodes[shortcode.tag].mediaAtts ) ) {

                    _.each( api.shortcodes[shortcode.tag].mediaAtts , function( attr ){

                        if( !_.isUndefined( shortcode.attrs[attr] ) ){

                            var value = _.isString( shortcode.attrs[attr] ) && shortcode.attrs[attr].indexOf( "," ) > -1 ? shortcode.attrs[attr].split( "," ) : parseInt( $.trim( shortcode.attrs[attr] ) );

                            if( $.isArray( value ) ){

                                _.each( value , function( attachId ){

                                    attachId = parseInt( $.trim( attachId ) );

                                    attachmentIds.push( attachId );

                                });

                            }else if( value > 0 ){

                                attachmentIds.push( value );

                            }

                        }

                    });

                }

            });

            attachmentIds = _.uniq( attachmentIds );

            return attachmentIds;

        }


    });

    api.sedShortcode = new api.SiteEditorShortcode;

}(sedApp, jQuery));