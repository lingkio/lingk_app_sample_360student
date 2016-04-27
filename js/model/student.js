var Student = Backbone.Model.extend({
	url: function(options){
		console.log(options);
	  return 'http://localhost:32795/360-Degree/api/public/student/:id';
	},
	parse: function(response) {
		return response;
	}
});



