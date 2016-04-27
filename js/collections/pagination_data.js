'use strict';
define(['backbone','paginator'], function(Backbone,PageableCollection) {
    
	var Students = Backbone.PageableCollection.extend({

      url: 'http://localhost:32773/360-Degree/api/public/users',
    
      // Any `state` or `queryParam` you override in a subclass will be merged with
      // the defaults in `Backbone.PageableCollection` 's prototype.
      state: {
    
        // You can use 0-based or 1-based indices, the default is 1-based.
        // You can set to 0-based by setting ``firstPage`` to 0.
        firstPage: 0,
    
        // Set this to the initial page index if different from `firstPage`. Can
        // also be 0-based or 1-based.
        currentPage: 2,
    
        // Required under server-mode
        totalRecords: 200
      },
    
      // You can configure the mapping from a `Backbone.PageableCollection#state`
      // key to the query string parameters accepted by your server API.
      queryParams: {
    
        // `Backbone.PageableCollection#queryParams` converts to ruby's
        // will_paginate keys by default.
        currentPage: "current_page",
        pageSize: "page_size"
      }
    });
	return new Students();
});


