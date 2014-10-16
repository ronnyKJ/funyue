(function (App, data) {
	var param = location.search ? location.search.match(/[0-9a-zA-Z]+/g).join('/') + '/' : '';

	var appConf = {
			newsUrl : newsUrl + param,
			startDate : startDate,
			openDate : openDate,//20140613
			shareImg : 'http://m.baidu.com/static/search/ala/share.png'
		};

	var app;

	var main = function() {
		latestDate.substr(0, 2)
		var latest = new Date(2014, latestDate.substr(0, 2)-1, latestDate.substr(2, 2));
		var m = data.numFormat(latest.getMonth()+1);
		var d = data.numFormat(latest.getDate());
		// this.navigate('news/' + (m+d) + '/cover', {trigger : true});
		news( m+d, 'cover' );
	};

	var news = function(date, id){
		if( Number(date) > Number(latestDate) ){
			date = latestDate;
			id = 'cover';

			this.navigate('news/' + date + '/cover');
		}

		appConf.typeIndex = 0;
		run();
		app.news(date, id);
	};

	var options = {};
	options.observers = [ 
		{
			route : '',
			callback : main
		},
		{
			route : 'news/:date/:id',
			callback : news
		}
	];

	appConf.router = new Router(options);
	
	var run = function(){
		if(!app){
			app = new App(appConf);
		}
	}
	var hash = location.hash;
	if( !(!hash || (/#news\/[0-9]{4}\/([0-9]+|(cover)|(calendar))/g).test( hash )) ){
		location.hash = '';
	}
	
	appConf.router.start();

	setTimeout(function(){
		var sp = $('.start-page').css('-webkit-transform', 'translate3d(-' + document.body.clientWidth + 'px,0,0)');
		setTimeout(function(){
			sp.remove();
		}, 510);
	}, 1000);


})(WC.App, WC.data);