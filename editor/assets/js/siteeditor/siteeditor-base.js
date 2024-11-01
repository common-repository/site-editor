window.sedApp = window.sedApp || {};

(function( exports, $ ){
	var api, extend, ctor, inherits,
		slice = Array.prototype.slice;

	/* =====================================================================
	 * Micro-inheritance - thank you, backbone.js.
	 * ===================================================================== */

	extend = function( protoProps, classProps ) {
		var child = inherits( this, protoProps, classProps );
		child.extend = this.extend;
		return child;
	};

	// Shared empty constructor function to aid in prototype-chain creation.
	ctor = function() {};

	// Helper function to correctly set up the prototype chain, for subclasses.
	// Similar to `goog.inherits`, but uses a hash of prototype properties and
	// class properties to be extended.
	inherits = function( parent, protoProps, staticProps ) {
		var child;

		// The constructor function for the new subclass is either defined by you
		// (the "constructor" property in your `extend` definition), or defaulted
		// by us to simply call `super()`.
		if ( protoProps && protoProps.hasOwnProperty( 'constructor' ) ) {
			child = protoProps.constructor;
		} else {
			child = function() {
				// Storing the result `super()` before returning the value
				// prevents a bug in Opera where, if the constructor returns
				// a function, Opera will reject the return value in favor of
				// the original object. This causes all sorts of trouble.
				var result = parent.apply( this, arguments );
				return result;
			};
		}

		// Inherit class (static) properties from parent.
		$.extend( child, parent );

		// Set the prototype chain to inherit from `parent`, without calling
		// `parent`'s constructor function.
		ctor.prototype  = parent.prototype;
		child.prototype = new ctor();

		// Add prototype properties (instance properties) to the subclass,
		// if supplied.
		if ( protoProps )
			$.extend( child.prototype, protoProps );

		// Add static properties to the constructor function, if supplied.
		if ( staticProps )
			$.extend( child, staticProps );

		// Correctly set child's `prototype.constructor`.
		child.prototype.constructor = child;

		// Set a convenience property in case the parent's prototype is needed later.
		child.__super__ = parent.prototype;

		return child;
	};

	api = {};

	/* =====================================================================
	 * Base class.
	 * ===================================================================== */

	api.Class = function( applicator, argsArray, options ) {
		var magic, args = arguments;

		if ( applicator && argsArray && api.Class.applicator === applicator ) {
			args = argsArray;
			$.extend( this, options || {} );
		}

		magic = this;
		if ( this.instance ) {
			magic = function() {
				return magic.instance.apply( magic, arguments );
			};

			$.extend( magic, this );
		}

		magic.initialize.apply( magic, args );
		return magic;
	};

	api.Class.applicator = {};

	api.Class.prototype.initialize = function() {};

	/*
	 * Checks whether a given instance extended a constructor.
	 *
	 * The magic surrounding the instance parameter causes the instanceof
	 * keyword to return inaccurate results; it defaults to the function's
	 * prototype instead of the constructor chain. Hence this function.
	 */
	api.Class.prototype.extended = function( constructor ) {
		var proto = this;

		while ( typeof proto.constructor !== 'undefined' ) {
			if ( proto.constructor === constructor )
				return true;
			if ( typeof proto.constructor.__super__ === 'undefined' )
				return false;
			proto = proto.constructor.__super__;
		}
		return false;
	};

	api.Class.extend = extend;

	/* =====================================================================
	 * Events mixin.
	 * ===================================================================== */

	api.Events = {
		trigger: function( id ) {
			if ( this.topics && this.topics[ id ] )
				this.topics[ id ].fireWith( this, slice.call( arguments, 1 ) );
			return this;
		},

		bind: function( id ) {
			this.topics = this.topics || {};
			this.topics[ id ] = this.topics[ id ] || $.Callbacks();
			this.topics[ id ].add.apply( this.topics[ id ], slice.call( arguments, 1 ) );
			return this;
		},

		unbind: function( id ) {
			if ( this.topics && this.topics[ id ] )
				this.topics[ id ].remove.apply( this.topics[ id ], slice.call( arguments, 1 ) );
			return this;
		}
	};

	/* =====================================================================
	 * Observable values that support two-way binding.
	 * ===================================================================== */

	api.Value = api.Class.extend({
		initialize: function( initial, options ) {
			this._value = initial; // @todo: potentially change this to a this.set() call.
			this.callbacks = $.Callbacks();
            //this.history = [this._value];
            this._dirty = false;

			$.extend( this, options || {} );

			this.set = $.proxy( this.set, this );
		},

		/*
		 * Magic. Returns a function that will become the instance.
		 * Set to null to prevent the instance from extending a function.
		 */
		instance: function() {
			return arguments.length ? this.set.apply( this, arguments ) : this.get();
		},

		get: function() {
			return this._value;
		},

		set: function( to ) {
			var from = this._value;
            //this.history.push(to);
			to = this._setter.apply( this, arguments );
			to = this.validate( to );

			// Bail if the sanitized value is null or unchanged.
			if ( null === to  || (_.isEqual( from , to ) && $.inArray( this.stype , ["module" , "style-editor" , "force-refresh"] ) == -1 ) )    //  :: change by parsaatef
				return this;
    
			this._value = to;
            this._dirty = true;

			this.callbacks.fireWith( this, [ to, from ] );

			return this;
		},

		_setter: function( to ) {
			return to;
		},

		setter: function( callback ) {
			var from = this.get();
			this._setter = callback;
			// Temporarily clear value so setter can decide if it's valid.
			this._value = null;
			this.set( from );
			return this;
		},

		resetSetter: function() {
			this._setter = this.constructor.prototype._setter;
			this.set( this.get() );
			return this;
		},

		validate: function( value ) {
			return value;
		},

		bind: function() {
		  ////api.log( arguments )
			this.callbacks.add.apply( this.callbacks, arguments );
			return this;
		},

		unbind: function() {
			this.callbacks.remove.apply( this.callbacks, arguments );
			return this;
		},

		link: function() { // values*
			var set = this.set;

			$.each( arguments, function() {
				this.bind( set );
			});
			return this;
		},

		unlink: function() { // values*
			var set = this.set;
			$.each( arguments, function() {
				this.unbind( set );
			});
			return this;
		},

		sync: function() { // values*
			var that = this;

			$.each( arguments, function() {
				that.link( this );
				this.link( that );
			});
			return this;
		},

		unsync: function() { // values*
			var that = this;
			$.each( arguments, function() {
				that.unlink( this );
				this.unlink( that );
			});
			return this;
		}
	});

	/* =====================================================================
	 * A collection of observable values.
	 * ===================================================================== */

	api.Values = api.Class.extend({
		defaultConstructor: api.Value,

		initialize: function( options ) {
			$.extend( this, options || {} );

			this._value = {};
			this._deferreds = {};
		},

		instance: function( id ) {
			if ( arguments.length === 1 )
				return this.value( id );

			return this.when.apply( this, arguments );
		},

		value: function( id ) {
			return this._value[ id ];
		},

		has: function( id ) {
			return typeof this._value[ id ] !== 'undefined';
		},

		add: function( id, value ) {
			if ( this.has( id ) )
				return this.value( id );

			this._value[ id ] = value;
			value.parent = this;
			if ( value.extended( api.Value ) )
				value.bind( this._change );

			this.trigger( 'add', value );

			if ( this._deferreds[ id ] )
				this._deferreds[ id ].resolve();

			return this._value[ id ];
		},

		create: function( id ) {
			return this.add( id, new this.defaultConstructor( api.Class.applicator, slice.call( arguments, 1 ) ) );
		},

		each: function( callback, context ) {
			context = typeof context === 'undefined' ? this : context;

			$.each( this._value, function( key, obj ) {
				callback.call( context, obj, key );
			});
		},

		remove: function( id ) {
			var value;

			if ( this.has( id ) ) {
				value = this.value( id );
				this.trigger( 'remove', value );
				if ( value.extended( api.Value ) )
					value.unbind( this._change );
				delete value.parent;
			}

			delete this._value[ id ];
			delete this._deferreds[ id ];
		},

		/**
		 * Runs a callback once all requested values exist.
		 *
		 * when( ids*, [callback] );
		 *
		 * For example:
		 *     when( id1, id2, id3, function( value1, value2, value3 ) {} );
		 *
		 * @returns $.Deferred.promise();
		 */
		when: function() {
			var self = this,
				ids  = slice.call( arguments ),
				dfd  = $.Deferred();

			// If the last argument is a callback, bind it to .done()
			if ( $.isFunction( ids[ ids.length - 1 ] ) )
				dfd.done( ids.pop() );

			$.when.apply( $, $.map( ids, function( id ) {
				if ( self.has( id ) )
					return;

				return self._deferreds[ id ] = self._deferreds[ id ] || $.Deferred();
			})).done( function() {
				var values = $.map( ids, function( id ) {
						return self( id );
					});

				// If a value is missing, we've used at least one expired deferred.
				// Call Values.when again to generate a new deferred.
				if ( values.length !== ids.length ) {
					// ids.push( callback );
					self.when.apply( self, ids ).done( function() {
						dfd.resolveWith( self, values );
					});
					return;
				}

				dfd.resolveWith( self, values );
			});

			return dfd.promise();
		},

		_change: function() {
			this.parent.trigger( 'change', this );
		}
	});

	$.extend( api.Values.prototype, api.Events );

	/* =====================================================================
	 * An observable value that syncs with an element.
	 *
	 * Handles inputs, selects, and textareas by default.
	 * ===================================================================== */

	api.ensure = function( element ) {
		return typeof element == 'string' ? $( element ) : element;
	};

	api.Element = api.Value.extend({
		initialize: function( element, options ) {
			var self = this,
				synchronizer = api.Element.synchronizer.html,
				type, update, refresh;

			this.element = api.ensure( element );
			this.events = '';

			if ( this.element.is('input, select, textarea') ) {
				this.events += 'change';
				synchronizer = api.Element.synchronizer.val;

				if ( this.element.is('input') ) {
					type = this.element.prop('type');
					if ( api.Element.synchronizer[ type ] )
						synchronizer = api.Element.synchronizer[ type ];

					if ( 'text' === type || 'password' === type ) {
						this.events += ' keyup';
					} else if ( 'range' === type ) {
						this.events += ' input propertychange';
					}

				} else if ( this.element.is('textarea') ) {
					this.events += ' keyup';
				}
			}
                         ////api.log( this.events );
			api.Value.prototype.initialize.call( this, null, $.extend( options || {}, synchronizer ) );
			this._value = this.get();
                        // //api.log( this._value );
			update  = this.update;
			refresh = this.refresh;

			this.update = function( to ) {
				if ( to !== refresh.call( self ) )
					update.apply( this, arguments );
			};
			this.refresh = function() {
				self.set( refresh.call( self ) );
			};

			this.bind( this.update );
			this.element.bind( this.events, this.refresh );
		},

		find: function( selector ) {
			return $( selector, this.element );
		},

		refresh: function() {},

		update: function() {}
	});

	api.Element.synchronizer = {};

	$.each( [ 'html', 'val' ], function( i, method ) {
		api.Element.synchronizer[ method ] = {
			update: function( to ) {
				this.element[ method ]( to );
			},
			refresh: function() {
				return this.element[ method ]();
			}
		};
	});

	api.Element.synchronizer.checkbox = {
		update: function( to ) {
			this.element.prop( 'checked', to );
		},
		refresh: function() {
			return this.element.prop( 'checked' );
		}
	};

	api.Element.synchronizer.radio = {
		update: function( to ) {
			this.element.filter( function() {
				return this.value === to;
			}).prop( 'checked', true );
		},
		refresh: function() {
			return this.element.filter( ':checked' ).val();
		}
	};

	/* =====================================================================
	 * Messenger for postMessage.
	 * ===================================================================== */

	$.support.postMessage = !! window.postMessage;

	api.Messenger = api.Class.extend({
		add: function( key, initial, options ) {
			return this[ key ] = new api.Value( initial, options );
		},

		/**
		 * Initialize Messenger.
		 *
		 * @param  {object} params        Parameters to configure the messenger.
		 *         {string} .url          The URL to communicate with.
		 *         {window} .targetWindow The window instance to communicate with. Default window.parent.
		 *         {string} .channel      If provided, will send the channel with each message and only accept messages a matching channel.
		 * @param  {object} options       Extend any instance parameter or method with this object.
		 */
		initialize: function( params, options ) {
			// Target the parent frame by default, but only if a parent frame exists.
			var defaultTarget = window.parent == window ? null : window.parent;

			$.extend( this, options || {} );

			this.add( 'channel', params.channel );
			this.add( 'url', params.url || '' );
			//this.add( 'targetWindow', params.targetWindow || defaultTarget );
			this.add( 'origin', this.url() ).link( this.url ).setter( function( to ) {
				return to.replace( /([^:]+:\/\/[^\/]+).*/, '$1' );
			});

			// first add with no value
			this.add( 'targetWindow', null );
			// This avoids SecurityErrors when setting a window object in x-origin iframe'd scenarios.
			this.targetWindow.set = function( to ) {
				var from = this._value;

				to = this._setter.apply( this, arguments );
				to = this.validate( to );

				if ( null === to || from === to ) {
					return this;
				}

				this._value = to;
				this._dirty = true;

				this.callbacks.fireWith( this, [ to, from ] );

				return this;
			};
			// now set it
			this.targetWindow( params.targetWindow || defaultTarget );

			// Since we want jQuery to treat the receive function as unique
			// to this instance, we give the function a new guid.
			//
			// This will prevent every Messenger's receive function from being
			// unbound when calling $.off( 'message', this.receive );
			this.receive = $.proxy( this.receive, this );
			this.receive.guid = $.guid++;

			$( window ).on( 'message', this.receive );
		},

		destroy: function() {
			$( window ).off( 'message', this.receive );
		},

		receive: function( event ) {
			var message;

			event = event.originalEvent;

			if ( ! this.targetWindow() )
				return;

			// Check to make sure the origin is valid.
			if ( this.origin() && event.origin !== this.origin() )
				return;

			// Ensure we have a string that's JSON.parse-able
			if ( typeof event.data !== 'string' || event.data[0] !== '{' ) {
				return;
			}

			message = JSON.parse( event.data );

			// Check required message properties.
			if ( ! message || ! message.id || typeof message.data === 'undefined' )
				return;

			// Check if channel names match.
			if ( ( message.channel || this.channel() ) && this.channel() !== message.channel )
				return;

			this.trigger( message.id, message.data );
		},

		send: function( id, data ) {
			var message;

			data = typeof data === 'undefined' ? null : data;

			if ( ! this.url() || ! this.targetWindow() )
				return;

			message = { id: id, data: data };
			if ( this.channel() )
				message.channel = this.channel();

			this.targetWindow().postMessage( JSON.stringify( message ), this.origin() );
		}
	});

	// Add the Events mixin to api.Messenger.
	$.extend( api.Messenger.prototype, api.Events );

	/* =====================================================================
	 * Core editor object.
	 * ===================================================================== */

	api = $.extend( new api.Values(), api );
	api.get = function() {
		var result = {};

		this.each( function( obj, key ) {
			result[ key ] = obj.get();
		});

		return result;
	};


	api.Filters = api.Class.extend({

		initialize: function () {
			this.topics = {};
		},

		add : function( id, callback , priority ){

			if( _.isUndefined( this.topics[id] ) ){
				this.topics[id] = [];
			}

			this.topics[id].push({
				callback : callback ,
				priority  : priority || 10 , //1 , 2 , 3 , ...
				type	 : "add"
			});

		},

		remove : function( id, callback , priority ){

			if( _.isUndefined( this.topics[id] ) ){
				this.topics[id] = [];
			}

			this.topics[id].push({
				callback : callback ,
				priority  : priority || 10 ,
				type	 : "remove"
			});

		},

		render : function( id , args ){

			if( _.isUndefined( this.topics[id] ) ){
				if( args.length > 0 )
					return args[0];
				else
					return false;
			}

			var topics = _.sortBy( this.topics[id] , 'priority'),
				result;

			_.each( topics , $.proxy( function( obj ){

				var callback = obj.callback;
				result = obj.callback.apply( obj , args);

			} , this) );

			return result;

			/*var callbacks = $.Callbacks();

			_.each( topics , $.proxy( function( obj ){

				var callback = obj.callback;

				if( obj.type == "add" )
					callbacks.add( callback );
				else if( obj.type == "remove" )
					callbacks.remove( callback );

			} , this) );

			//callback && callback.apply( this, [ result1, result2 ]);

			return callbacks.fire( args );*/
		},

	});

	api.filters = new api.Filters();

	api.applyFilters = function( id ) {
		return api.filters.render(id, slice.call( arguments, 1 ) );
	};

	api.addFilter = function(id, callback, priority) {
		api.filters.add(id, callback , priority);
	};

	/*api.removeFilter = function( id, callback , priority ) {
		api.filters.remove( id, callback , priority );
	};*/


	api.log = function() {
        var args = arguments , Debaug = false;

        if( args.length == 0 || Debaug === false )
            return ;

        if(console && console.log){
            console.log.apply( console , args );
        }else{
            $.each( args , function( index , msg ){
                alert( msg );
            });
            alert( msg );
        }

    };

    api.currentSedElementId = '';
    api.fn = {};

    api.fn.ucfirst = function(str) {
        //  discuss at: http://phpjs.org/functions/ucfirst/
        str += '';
        var f = str.charAt(0).toUpperCase();

        return f + str.substr(1);
    };

	api.fn.urldecode = function(str){
		return decodeURIComponent((str+'').replace(/\+/g,'%20'));
	};

	api.fn.urlencode = function (str){
		str=(str+'').toString();
		return encodeURIComponent(str).replace(/!/g,'%21').replace(/'/g,'%27').replace(/\(/g,'%28').replace(/\)/g,'%29').replace(/\*/g,'%2A').replace(/%20/g,'+');
	};

	api.fn.rawurldecode = function(str){
		return decodeURIComponent(str+'');
	};

	api.fn.rawurlencode = function(str){
		str=(str+'').toString();
		return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A');
	};


	// Expose the API to the world.
	exports.editor = api;
})( sedApp, jQuery );
