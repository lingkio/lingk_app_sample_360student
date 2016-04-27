'use strict';
define(['backbone', 'jquery', 'underscore', 'text!../../templates/results.html', 'collections/data'], 
function(Backbone, $, _,  ResultsTemplate, DataCollection) {
  var ResultsView = Backbone.View.extend({
    el: $('#search_result_container'),
    template: _.template(ResultsTemplate),
    initialize: function() {
        // this.dataCollection = DataCollection;
    },
    render: function(filters) {
      var scope = this;

      var filter_func = function(el){return true;}

      //todo:fixme
      this.dataCollection = DataCollection;

      this.$el.empty();
      if(filters){
        var by = filters.id_filter;
        if(by){
          filter_func = function(el){
             
            if(el.id==by){
                return el.id==by;    
            } 
            
            
          }
        }else{
          var by = filters.lastname_filter;
          if(by){
            filter_func = function(el){
              return el.last_name.indexOf(by)!=-1;
            }
          }

        }
      } 
      //endfixme

      scope.data = {students:[]};
      var items_per_page = 25;
      this.dataCollection.fetch({
        success: function(collection, response, options) {
          scope.dataCollection.each(function(student) {
            student = student.toJSON();

            if(filter_func(student)){
              scope.data.students.push(student);  
            }
            scope.data.totalCount = student.totalCount;   
          });
            
            var total_pages = Math.ceil( 1000 / items_per_page );
            scope.data.students = scope.data.students;
            scope.data.total_pages = total_pages;  
            scope.data.items_per_page = items_per_page;
            scope.data.id_filter_val = $('#id_filter').val();
            scope.$el.append(scope.template(scope.data));
        },
        error: function() {
          console.log('Error loading json');
        }
      });
      
      
      
      
    }
  });
  return new ResultsView();
});