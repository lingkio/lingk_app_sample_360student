'use strict';
define(['backbone', 'jquery', 'underscore', 'text!../../templates/index.html', 'views/header', 'views/footer', 'views/index_results'],
	function(Backbone, $, _, IndexTemplate, HeaderView, FooterView, ResultsView) {
		var IndexView = Backbone.View.extend({
			el: $('.container'),
			header: $('#header'),
			template: _.template(IndexTemplate),
			data: {},
			events: {
				'change #id_filter': 'searchById',
				'change #lastname_filter': 'searchByLastName',
			},
			initialize: function() {
				this.headerView = HeaderView;
				this.resultsView = ResultsView;
				this.footerView = FooterView;
			},
			render: function() {
				var scope = this;

				scope.$el.append(scope.template(scope.data));
				scope.headerView.$el = $('#header');
				scope.headerView.render();
				scope.footerView.$el = $('#footer');
				scope.footerView.render();
				scope.resultsView.$el = $('#search_result_container');
				scope.resultsView.render({
							id_filter:'',
							lastname_filter:'',
						});
			},
			searchById: function() {
			    
				$('#lastname_filter').val("");
				$('#search_result_container').find(".mui-content").hide();	
				this.resultsView.render({
							id_filter:$('#id_filter').val(),
							lastname_filter:'',
				});
			    
				
			},
			searchByLastName: function() {
				$('#id_filter').val("");
				$('#search_result_container').find(".mui-content").hide();	
				this.resultsView.render({
							id_filter:'',
							lastname_filter:$('#lastname_filter').val(),
				});
				$('.mui-content').hide();		
			},
		});
		return IndexView;
	});