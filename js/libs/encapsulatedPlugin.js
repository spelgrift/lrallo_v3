var jQuery = require('jquery');
/**
 * Plugin generator plugin. Doesn't allow for chaining directly, but preserves public
 * methods and namespaces, and supports multiple elements.
 * 
 * @copyright Jamie Talbot 2010 (http://jamietalbot.com)
 * Licensed as per jQuery (http://jquery.org/license).  Keep my name and you're all good :)
 *
 * Example Usage:
 *
 * Step 1: Define your plugin interface
 * ------------------------------------
 *
 * MyPluginDefinition = function() {
 *	
 *	var _privateMember;
 *	
 *	function _privateMethod() {}
 *
 *	return {
 *		setup: function() {
 *			// Initialisation Code
 *		},
 *		
 *		publicMethodWithArguments: function(name) {
 *			console.log(name);
 *		},
 *
 * 		publicMethod: function() {}
 *	}
 * }
 *
 * Step 2: Register your plugin
 * ----------------------------
 *
 * $.fn.myplugin = function(options) {
 *	return $.fn.encapsulatedPlugin('myplugin', MyPluginDefinition, this, options);
 * };
 * 
 * Step 3: Profit
 * --------------
 *
 * $('#foo').myplugin().publicMethod();
 * $('#bar').myplugin().publicMethodWithArguments('Jamie');
 *
 */
(function($) {

	$.fn.encapsulatedPlugin = function(plugin, definition, objects, options) {

		// Creates a function that calls the function of the same name on each member of the supplied set.
		function makeIteratorFunction(f, set) {
			return function() {
				for ( var i = 0; i < set.length; i++) {
					set[i][f].apply(set[i][f], arguments);
				}
			};
		}

		var result = [];
		objects.each(function() {
			var element = $(this);

			if (!element.data(plugin)) {
				// Initialise
				var instance = new definition(this, options);
				if (instance.setup) {
					// If there is a setup function supplied, call it.
					instance.setup();
				}

				// Store the new functions in a validation data object.
				element.data(plugin, instance);
			}
			result.push(element.data(plugin));
		});

		// We now have a set of plugin instances.
		result = $(result);

		// Take the public functions from the definition and make them available across the set.
		var template = result[0];
		if (template) {
			for ( var i in template) {
				if (typeof (template[i]) == 'function') {
					result[i] = makeIteratorFunction(i, result);
				}
			}
		}

		// Finally mix-in a convenient reference back to the objects, to allow for chaining.
		result.$ = objects;

		return result;
	};
})(jQuery);
