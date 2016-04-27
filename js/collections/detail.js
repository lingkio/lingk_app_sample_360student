'use strict';
define(['backbone'], function(Backbone) {
	var StudentCollection = Backbone.Collection.extend({
		url: function(options){
		    console.log(options);
		  return 'http://localhost:32795/360-Degree/api/public/student/';
		},
		parse: function(response) {
			return response;
		}
	});
	
	return new StudentCollection();
});

