'use strict';
define(['backbone'], function(Backbone) {
	var DataCollection = Backbone.Collection.extend({
		//url: 'http://localhost:32773/360-Degree/api/public/users?limit=1001',
		url: 'http://light-it-09.tk/api/public/users?limit=1001',
		initialize: function() {},
		parse: function(response) {
			return response;
		}
	});
	return new DataCollection();
});
