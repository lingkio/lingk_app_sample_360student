'use strict';
define(['backbone', 'jquery', 'underscore', 'text!../../templates/detail.html', 'views/header', 'views/footer'],
  function(Backbone, $, _, DetailTemplate, HeaderView, FooterView) {
    var DetailView = Backbone.View.extend({
      el: $('.container'),
      header: $('#header'),
      template: _.template(DetailTemplate),
      data: {},
      events: {
        'click .logo': 'showAlert'
      },
      initialize: function(student_id) {
        
        this.student_id = student_id;
        this.headerView = HeaderView;
        this.footerView = FooterView;
      },
      render: function() {
        var student_api_id = this.student_id;
        var Student = Backbone.Model.extend({
          url: function(){
            return 'http://light-it-09.tk/api/public/student/'+student_api_id;
            //return 'http://localhost:32773/360-Degree/api/public/student/'+student_api_id;
          },
          parse: function(response) {
            return response;
          }
        });
        var student_info = new Student;
        student_info.set({scope:this});

        var data_stu = student_info.fetch({
          success: function(student) {

            var resp = student.toJSON();
            var scope = resp.scope;  
            scope.$el.empty();
            scope.data.seif = resp[0];

            var birth_Date = new Date(scope.data.seif.birthdate);
            
            var monthNames = [
              "January", "February", "March",
              "April", "May", "June", "July",
              "August", "September", "October",
              "November", "December"
            ];
            
            var date = new Date();
            var day = birth_Date.getDate();
            var monthIndex = date.getMonth();
            var year = birth_Date.getFullYear();
             var formated_date = monthNames[monthIndex]+', '+ day + ' ' + year +' ('+day+'/'+(monthIndex+1)+'/'+year+')';
            
            scope.data.seif.birthdate = formated_date;
            scope.data.telephones = resp[0].telephones.data;
            scope.data.addresses = resp[0].addresses.data;
            scope.data.emails = resp[0].emails.data;
            scope.data.enrollment = resp[0].enrollment;
            scope.data.programs = resp[0].program.data;
            scope.data.learner_action = resp[0].learner_action.data;
            scope.data.academic_history = resp[0].academic_history;
            
            for(var i = 0; i < scope.data.learner_action.length; i++){
                if(scope.data.learner_action[i].lastModified != undefined){
                    var date_modif = new Date(scope.data.learner_action[i].lastModified);
                    var day = date_modif.getDate();
                    if(parseInt(day, 10) < 10){
                        var cur_day = '0'+cur_day;
                    }else{
                        var cur_day = day;
                    }
                    var monthIndex = date_modif.getMonth()+ 1;
                    if(parseInt(monthIndex, 10) < 10){
                        var cur_month = '0'+monthIndex;
                    }else{
                        var cur_month = monthIndex;
                    }
                    var year = date_modif.getFullYear();
                    var last_modif_date = year+''+cur_month+''+cur_day;
                    scope.data.learner_action[i].lastModified = moment(last_modif_date, "YYYYMMDD").fromNow();    
                }
                
            } 

            scope.$el.append(scope.template(scope.data));
            scope.headerView.$el = $('#header');
            scope.headerView.render();
            scope.footerView.$el = $('#footer');
            scope.footerView.render();      
          },
          error: function() {
            return false;
          }
        });
      },
      showAlert: function() {
        console.log('Hell Yeah!');
      }
    });
    return DetailView;
  });