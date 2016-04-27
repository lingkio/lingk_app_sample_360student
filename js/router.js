'use strict';
define(['backbone', 'jquery', 'underscore', 'views/index', 'views/detail'], function(Backbone, $, _, IndexView, DetailView) {
	var Router = Backbone.Router.extend({
		routes: {
			'': 'indexAction',
			'student/:id': 'detailAction',
			'*params': 'defaultAction'
		},
		indexAction: function() {
		    
		    $('.content').remove();
		    
		      $('#header .mui-appbar').first().remove();    
		   
		    var indexView = new IndexView();
			indexView.render();
		},
		detailAction: function(studentId){
		    
			var detailView = new DetailView(studentId);
			detailView.render();
		},
		defaultAction: function(params) {
		},
		
	});
	var initialize = function() {

		var router = new Router();
		Backbone.emulateHTTP = true;
		Backbone.history.start();
		$(document).on('click', '.student_guid', function(event) {
			var href = $(this).attr('href');

			var protocol = this.protocol + '//';
			if (href.slice(protocol.length) !== protocol) {
				event.preventDefault();
				router.navigate(href, true);
				
               Backbone.history.navigate(href, true); 
			}
		});
	};
	return {
		initialize: initialize
	};
});