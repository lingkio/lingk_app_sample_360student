'use strict';
require.config({
	paths: {
		routes: './routes',
		text: '../lib/text',
		jquery: '../lib/jquery',
		underscore: '../lib/lodash',
		backbone: '../lib/backbone',
		bootstrap: '../lib/bootstrap',
		mui: '../lib/mui.min',
		moment: '../lib/moment',
		
	}
});
require(['backbone', 'jquery', 'underscore','mui'], function() {
	require(['bootstrap'], function() {
		require(['router'], function(Router) {
			Router.initialize();
		});
	});
});