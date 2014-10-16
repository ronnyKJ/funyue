require(['App', 'backbone', 'data'], function (App, Backbone, data) {
	var param = location.search ? location.search.match(/[0-9a-zA-Z]+/g).join('/') + '/' : '';

	var appConf = {
			newsUrl : '/admin/preview/news/null/' + param, 
			startDate : new Date(2014, 3, 9),
			openDate : new Date(2014, 5, 13)
		};

	var app;

	var AppRouter = Backbone.Router.extend({ 
	    routes : {
	        '' : 'main',
	        'news/:date/:id' : 'news',
	        '*error' : 'error'
	    },  
	    main : function() {
	    	latestDate.substr(0, 2)
	    	var latest = new Date(2014, latestDate.substr(0, 2)-1, latestDate.substr(2, 2));
			var m = data.numFormat(latest.getMonth()+1);
			var d = data.numFormat(latest.getDate());
	    	this.navigate('news/' + (m+d) + '/cover', {trigger : true});
	    },
	    news : function(date, id){
	    	if( Number(date) > Number(latestDate) ){
	    		date = latestDate;
	    		id = 'cover';

	    		this.navigate('news/' + date + '/cover');
	    	}

	    	appConf.typeIndex = 0;
	    	run();
	    	app.news(date, id);
	    },
	    error : function(error){  
	          
	    }
	});

	var run = function(){
    	if(!app){
    		app = new App(appConf);
    	}
	}
	  
	var router = new AppRouter();  
	Backbone.history.start();
});