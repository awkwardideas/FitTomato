
/*
	Base.js, version 1.1a
	Copyright 2006-2010, Dean Edwards
	License: http://www.opensource.org/licenses/mit-license.php
*/

var Base = function() {
	// dummy
};

Base.extend = function(_instance, _static) { // subclass
	
	"use strict";
	
	var extend = Base.prototype.extend;
	
	// build the prototype
	Base._prototyping = true;
	var proto = new this();
	extend.call(proto, _instance);
  proto.base = function() {
    // call this method from any other method to invoke that method's ancestor
  };
	delete Base._prototyping;
	
	// create the wrapper for the constructor function
	//var constructor = proto.constructor.valueOf(); //-dean
	var constructor = proto.constructor;
	var klass = proto.constructor = function() {
		if (!Base._prototyping) {
			if (this._constructing || this.constructor == klass) { // instantiation
				this._constructing = true;
				constructor.apply(this, arguments);
				delete this._constructing;
			} else if (arguments[0] !== null) { // casting
				return (arguments[0].extend || extend).call(arguments[0], proto);
			}
		}
	};
	
	// build the class interface
	klass.ancestor = this;
	klass.extend = this.extend;
	klass.forEach = this.forEach;
	klass.implement = this.implement;
	klass.prototype = proto;
	klass.toString = this.toString;
	klass.valueOf = function(type) {
		//return (type == "object") ? klass : constructor; //-dean
		return (type == "object") ? klass : constructor.valueOf();
	};
	extend.call(klass, _static);
	// class initialisation
	if (typeof klass.init == "function") klass.init();
	return klass;
};

Base.prototype = {	
	extend: function(source, value) {
		if (arguments.length > 1) { // extending with a name/value pair
			var ancestor = this[source];
			if (ancestor && (typeof value == "function") && // overriding a method?
				// the valueOf() comparison is to avoid circular references
				(!ancestor.valueOf || ancestor.valueOf() != value.valueOf()) &&
				/\bbase\b/.test(value)) {
				// get the underlying method
				var method = value.valueOf();
				// override
				value = function() {
					var previous = this.base || Base.prototype.base;
					this.base = ancestor;
					var returnValue = method.apply(this, arguments);
					this.base = previous;
					return returnValue;
				};
				// point to the underlying method
				value.valueOf = function(type) {
					return (type == "object") ? value : method;
				};
				value.toString = Base.toString;
			}
			this[source] = value;
		} else if (source) { // extending with an object literal
			var extend = Base.prototype.extend;
			// if this object has a customised extend method then use it
			if (!Base._prototyping && typeof this != "function") {
				extend = this.extend || extend;
			}
			var proto = {toSource: null};
			// do the "toString" and other methods manually
			var hidden = ["constructor", "toString", "valueOf"];
			// if we are prototyping then include the constructor
			var i = Base._prototyping ? 0 : 1;
			while (key = hidden[i++]) {
				if (source[key] != proto[key]) {
					extend.call(this, key, source[key]);

				}
			}
			// copy each of the source object's properties to this object
			for (var key in source) {
				if (!proto[key]) extend.call(this, key, source[key]);
			}
		}
		return this;
	}
};

// initialise
Base = Base.extend({
	constructor: function() {
		this.extend(arguments[0]);
	}
}, {
	ancestor: Object,
	version: "1.1",
	
	forEach: function(object, block, context) {
		for (var key in object) {
			if (this.prototype[key] === undefined) {
				block.call(context, object[key], key, object);
			}
		}
	},
		
	implement: function() {
		for (var i = 0; i < arguments.length; i++) {
			if (typeof arguments[i] == "function") {
				// if it's a function, call it
				arguments[i](this.prototype);
			} else {
				// add the interface using the extend method
				this.prototype.extend(arguments[i]);
			}
		}
		return this;
	},
	
	toString: function() {
		return String(this.valueOf());
	}
});


/**
 * Fitbit.jQuery.js
 *
 * @author     Chad Haney
 * @copyright  2014 - Awkward Ideas, LLC
 * @licesnse   http://www.opensource.org/licenses/mit-license.php
 */
	
(function($) {

	Fitbit = function(oAuthToken, oAuthVerifier, options){
		return new Fitbit.API(oAuthToken, oAuthVerifier, options);
	}
	
	/**
	 * The Base Fitbit class is used to extend all other Fitbit
	 * classes. It handles the callbacks and the basic setters/getters
	 *	
	 * @param 	object  An object of the default properties
	 * @param 	object  An object of properties to override the default	
	 */
	
	Fitbit.Base = Base.extend({
		
		/**
		 * Build Date
		 */
	
		buildDate: '2014-04-23',
		
		/**
		 * Version
		 */
		 
		version: '0.1',
		
		/**
		 * Sets the default options
		 *
		 * @param	object 	The default options
		 * @param	object 	The override options
		 */
		 
		constructor: function(_default, options) {
			if(typeof _default !== "object") {
				_default = {};
			}
			if(typeof options !== "object") {
				options = {};
			}
			this.setOptions($.extend(true, {}, _default, options));
		},
		
		/**
		 * Delegates the callback to the defined method
		 *
		 * @param	object 	The default options
		 * @param	object 	The override options
		 */
		 
		callback: function(method) {
		 	if(typeof method === "function") {
				var args = [];
								
				for(var x = 1; x <= arguments.length; x++) {
					if(arguments[x]) {
						args.push(arguments[x]);
					}
				}
				
				method.apply(this, args);
			}
		},
		
		/**
		 * Log a string into the console if it exists
		 *
		 * @param 	string 	The name of the option
		 * @return	mixed
		 */		
		 
		log: function(str) {
			if(window.console && console.log) {
				console.log(str);
			}
		},
		
		/**
		 * Get an single option value. Returns false if option does not exist
		 *
		 * @param 	string 	The name of the option
		 * @return	mixed
		 */		
		 
		getOption: function(index) {
			if(this[index]) {
				return this[index];
			}
			return false;
		},
		
		/**
		 * Get all options
		 *
		 * @return	bool
		 */		
		 
		getOptions: function() {
			return this;
		},
		
		/**
		 * Set a single option value
		 *
		 * @param 	string 	The name of the option
		 * @param 	mixed 	The value of the option
		 */		
		 
		setOption: function(index, value) {
			this[index] = value;
		},
		
		/**
		 * Set a multiple options by passing a JSON object
		 *
		 * @param 	object 	The object with the options
		 * @param 	mixed 	The value of the option
		 */		
		
		setOptions: function(options) {
			for(var key in options) {
	  			if(typeof options[key] !== "undefined") {
		  			this.setOption(key, options[key]);
		  		}
		  	}
		}
	});
	
	/**
	 * The Fitbit API class is used to make calls to the Fitbit API
	 *
	 * @param 	object  An object of properties to override the default	
	 */
	 Fitbit.API = Fitbit.Base.extend({
		
		/**
		 *  Fitbit oAuthToken
		 */
		oAuthToken:"",
		 
		/**
		 *  Fitbit oAuthVerifier
		 */
		oAuthVerifier:"",
		 
		/**
		 *  Fitbit UserID
		 */
		userID:"-",
		
		/**
		 *	Fitbit API URL
		 */
		apiBaseURL:"https://api.fitbit.com",
		
		/**
		 *	Fitbit Response Format  json or xml
		 */
		responseFormat:"json",
		
		/**
		 * Constructor
		 *
		 * @param   object  The wrapping jQuery object
		 * @param	object  oAuthToken
		 * @param	object 	oAuthVerifier
		 */
		 
		constructor: function(token, verifier, options) {
			
			this.oAuthToken	= token;
			this.oAuthVerifier  = verifier;
			this.base(options);		
			
		},
		
		/**
		 * Get Devices
		 */
		getDevices: function() {
			var resourceURL = "/1/user/"+this.userID+"/devices";
			return this.postRequest(resourceURL);
		},
		
		/**
		 * Get Alarms
		 */
		getAlarms: function(deviceID) {
			var resourceURL = "/1/user/"+this.userID+"/devices/tracker/"+deviceID+"/alarms";
			return this.postRequest(resourceURL);
		},
		
		/**
		 * Add Alarm
		 */
		addAlarm: function(deviceID, time, enabled, recurring, weekDays, label, snoozeLength, snoozeCount, vibe) {
			var resourceURL = "/1/user/"+this.userID+"/devices/tracker/"+deviceID+"/alarms";
			var parameters = new Object();
			
			//Time of the alarm (Required)
			parameters.time = time; //"19:00+04:00"
			
			//Enabled? (Required)
			if(typeof enabled !== "undefined"){
				parameters.enabled = enabled; //true or false
			}else{
				parameters.enabled = true;
			}
			
			//One time or recurring alarm (Required)
			if(typeof recurring !== "undefined"){
				parameters.recurring = recurring; //true or false
			}else{
				parameters.recurring = false; 
			}
			
			//The days alarm is active for recurring alarm only (Required)
			if(typeof weekDays !== "undefined"){
				parameters.weekDays = weekDays;
			}else{
				parameters.weekDays = ["MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY", "SUNDAY"]; //Array  ["MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY", "SUNDAY"]
			}
			
			//Label for the alarm
			if(typeof label !== "undefined"){
				parameters.label = label; //"String value"
			}
			
			//Minutes between alarms
			if(typeof snoozeLength !== "undefined"){
				parameters.snoozeLength = snoozeLength; //9
			}
			
			//Maximum snooze count
			if(typeof snoozeCount !== "undefined"){
				parameters.snoozeCount = snoozeCount; //2
			}
			
			//Vibe pattern
			if(typeof vibe !== "undefined"){
				parameters.vibe = vibe; //"DEFAULT"
			}
			
			return this.postRequest(resourceURL, parameters);
		},
		
		/**
		 * Update Alarm
		 */
		updateAlarm:  function(deviceID, alarmID, time, enabled, recurring, weekDays, label, snoozeLength, snoozeCount, vibe) {
			var resourceURL = "/1/user/"+this.userID+"/devices/tracker/"+deviceID+"/alarms/"+alarmID;
			var parameters = new Object();
			
			//Time of the alarm (Required)
			parameters.time = time; //"19:00+04:00"
			
			//Enabled? (Required)
			if(typeof enabled !== "undefined"){
				parameters.enabled = enabled; //true or false
			}else{
				parameters.enabled = true;
			}
			
			//One time or recurring alarm (Required)
			if(typeof recurring !== "undefined"){
				parameters.recurring = recurring; //true or false
			}else{
				parameters.recurring = false; 
			}
			
			//The days alarm is active for recurring alarm only (Required)
			if(typeof weekDays !== "undefined"){
				parameters.weekDays = weekDays;
			}else{
				parameters.weekDays = ["MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY", "SUNDAY"]; //Array  ["MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY", "SUNDAY"]
			}
			
			//Label for the alarm
			if(typeof label !== "undefined"){
				parameters.label = label; //"String value"
			}
			
			//Minutes between alarms
			if(typeof snoozeLength !== "undefined"){
				parameters.snoozeLength = snoozeLength; //9
			}
			
			//Maximum snooze count
			if(typeof snoozeCount !== "undefined"){
				parameters.snoozeCount = snoozeCount; //2
			}
			
			//Vibe pattern
			if(typeof vibe !== "undefined"){
				parameters.vibe = vibe; //"DEFAULT"
			}
			
			
			return this.postRequest(resourceURL, parameters);
		},
		
		/**
		 * Delete Alarm
		 */
		deleteAlarm: function(deviceID, alarmID) {
			var resourceURL = "/1/user/"+this.userID+"/devices/tracker/"+deviceID+"/alarms/"+alarmID;
			return this.deleteRequest(resourceURL);
		},
		
		postRequest: function(resourceURL, parameters){
			return this.executeRequest("POST", resourceURL, parameters);
		},
		
		getRequest: function(resourceURL, parameters){
			return this.executeRequest("GET", resourceURL, parameters);
		},
		
		deleteRequest:function(resourceURL, parameters){
			return this.executeRequest("DELETE", resourceURL, parameters);
		},
		
		executeRequest: function(type, resourceURL, parameters){
			var requestURL = this.apiBaseURL + resourceURL + "." + this.responseFormat;
			
			var request = $.ajax({
				type: type,
				url: requestURL,
				data: parameters,
				dataType: this.responseFormat
			});
			
			request.done(function(result) {
				return result;
			});
			
			request.fail(function( jqXHR, textStatus ) {
				this.log( "Request failed: " + textStatus );
				return false;
			});
		}
		
	 });
	 
	String.prototype.ucfirst = function() {
		return this.substr(0, 1).toUpperCase() + this.substr(1);
	};
	
	/**
	 * jQuery helper method
	 *
	 * @param  int     An integer used to start the clock (no. seconds)
	 * @param  object  An object of properties to override the default	
	 */
	 
	$.fn.Fitbit = function(oAuthToken, oAuthVerifier, options) {
		return new Fitbit(oAuthToken, oAuthVerifier, options);
	};
	
	/**
	 * jQuery helper method
	 *
	 * @param  int     An integer used to start the clock (no. seconds)
	 * @param  object  An object of properties to override the default	
	 */
	 
	$.fn.Fitbit = function(oAuthToken, oAuthVerifier, options) {
		return new Fitbit(oAuthToken, oAuthVerifier, options);
	};
	
}(jQuery));