define('App', ['EndlessSlider', 'backbone', 'data', 'tmpl', 'hammer'], function (EndlessSlider, Backbone, data, tmpl, hammer) {
	
	/*** main ***/
	var App = function(conf){
		this.tileController;
		this.data = {};
		this.router = new Backbone.Router();
		this.docHeight = document.body.clientHeight;

		this.typeIndex = conf.typeIndex == undefined? 0: conf.typeIndex;
		
		this.conf = conf;
		this._countDown();//倒计时
		this._init(conf);
	};

	App.prototype = {	
		_init : function(conf){
			var self = this;

			this.newsESwiper = new EndlessSlider({
				swiper : '.horizon',
				firstActiveSlideDataIndex : 0,//这里要改*****************
				container : $('.container').eq(0),
				onSlideAdded : this._onSlideAdded.bind(this),
				onSlideChangeEnd : this._onSlideChangeEnd.bind(this)
			});	

			var $cover = this.cover = $('.cover-slider');
			this.coverSwiper = $cover.swiper({
				onSlideChangeEnd : function(swiper, direction){
					if(swiper.activeIndex == 1){
						setTimeout(function(){
							$cover.hide();
							self.router.navigate('news/' + self.currentDate + '/' + self.data[self.currentDate].seq2id[0]);
						}, 10);

					}
				},
				resistance : '100%'
			});

			this._bindAction();
			this._bindShare();
		},

		_initCover : function(){
			var coverData = this.data[this.currentDate].cover;
			
			if( this._isToday( coverData.date ) ){
				coverData.today = true;
			}

			data.mix(this.coverBaseData, coverData);

			var cover = $('.cover-slide').css('background-image', 'url(' + coverData.img + ')').html('');
			$('#cover-tpl').tmpl( coverData ).appendTo(cover);
		},

		_countDown : function(){
			this.coverBaseData = {};
			var delta = this.conf.openDate - new Date();
			var days = delta/24/3600000;

			var remain = days > 0 ? Math.ceil(days) : Math.floor(days);
			if( remain >= 0 ){
				this.coverBaseData.begin = false;
			}else{
				this.coverBaseData.begin = true;
			}
			remain = Math.abs(remain);
			this.coverBaseData.remain = (data.numFormat(remain)+"").split('');
		},

		_isToday : function(dateStr){
			return data.timeFormat(+new Date) == dateStr;
		},

		_onSlideAdded : function(slide, index){
			var jSlide = $(slide);
			var self = this;

			var result = self.data[ self.currentDate ];

			var id = result.seq2id[index];
			if(id == 'calendar'){// 日历
				this._addCalendarSlide(jSlide, self);
				slide.type = 'calendar';
			}else{
				if( slide == self.newsESwiper.swiper.activeSlide() ){
					self._setTaskProgress(self.currentDate, Number(self.currentId));
				}

				this._addNewsSlide(jSlide, self, result, id, index);
				slide.type = 'news';
			}

		},

		_addCalendarSlide : function(jSlide, self){

			if(self.calendarElement){
				jSlide.css('position', 'relative').html('').append(self.calendarElement);
				return;
			}

			self.calendarElement = $('#calendar-tpl').tmpl({});
			jSlide.css('position', 'relative').append(self.calendarElement);

			var html = self._initCalendar(self.conf.startDate);

			$('.calendar-list').html(html);

			var monthMark = $('.month-mark');
			var calendarMonth = $('.calendar-month');

			$('.calendar-swiper', jSlide[0]).swiper({
				mode:'vertical',
				scrollContainer: true,
				resistance: '100%',
				onTouchEnd : function(swiper){
					self._setMonth(monthMark, calendarMonth);
				}
			});

			$('.calendar-wrapper')[0].addEventListener('webkitTransitionEnd', function(e){
				self._setMonth(monthMark, calendarMonth);
			}, false);

			self._setMonth(monthMark, calendarMonth);
		},

		_addNewsSlide : function(jSlide, self, result, id, index){
			var entry = result.news[ id ];
			
			if(index == 0){
				jSlide.addClass('first-card');
			}

			entry.indexStr = index+1;
			// entry.content = $('<div />').html(entry.content).html();
			
			entry = data.mix(result.cover, entry);
			content = $('#news-article-tpl').tmpl(entry);
			$('.bottom', content).append($('#share-tpl').tmpl({ newsId : self.currentId }));

			jSlide.append(content);
			var slide = jSlide[0];
			$('.page .top', slide).height(this.docHeight);
			$('.news-page', slide).swiper({
				mode:'vertical',
				scrollContainer: true
			});
		},

		_setMonth : function(monthMark, calendarMonth){
			var top = -15000, tmp = 0, upMonth;

			monthMark.each(function(i, month){
				tmp = $(month).offset().top;
				if(tmp < 0 && tmp > top ){
					top = tmp;
					upMonth = month;
				}
			});

			if(!upMonth){
				upMonth = monthMark[0]
			}

			calendarMonth.html(upMonth.innerHTML);			
		},

		_onSlideChangeEnd : function(swiper, direction){
			var self = this;
			var activeSlide = swiper.activeSlide()
			var i = $(activeSlide).attr('card-index');

			var result = self.data[ self.currentDate ];
			self.currentId = result.seq2id[ i ];
			self.router.navigate('news/' + self.currentDate + '/' + self.currentId);

			// 记录进度
			if(activeSlide.type == 'news'){
				self._setTaskProgress(self.currentDate, Number(self.currentId));
			}
		},

		/*
		 * {
		 * 	 '0409' : {
		 *		d : [455,456], 当天的新闻id数组
		 *		t : 5, 总共条数
		 *		p : 60 进度60%，即r/t
		 *	 }
		 * }
		 */
		_setTaskProgress : function(date, id){
			var storage = data.storage;
			var obj = storage.get(date)||{};
			obj.d = (obj.d || []);
			if(obj.d.indexOf(id) < 0){
				obj.d.push(id);
			}

			var len = obj.d.length;
			obj.t = this.data[date].seq2id.length - 1;
			obj.p = Math.min( obj.t? parseInt(len/obj.t * 100) : 0, 100 );
			storage.set(date, obj);

			if(obj.p == 100){
				this._setTaskDone('t1', date);
			}

			this._setCalendarData('progress', date, obj.p);
		},

		_setTaskDone : function(type, date){
			var storage = data.storage;
			var obj = storage.get(date)||{};
			obj[type] = 1;
			storage.set(date, obj);

			this._setCalendarData(type, date, 'disabled');
		},

		_setCalendarData : function(type, date, data){
			if(type == 'progress'){
				$('li[date=' + date + '] .progress .num', this.calendarElement).html(data);
			}else{
				$('li[date=' + date + '] .' + type, this.calendarElement).removeClass(data);
			}
		},

		/*
		 * 功能 : 对使用的数据进行查询，如果存在则直接引用，不存在则通过ajax请求之后再引用
		 * path : 路径，如news.SUNSOU.2
		 * url : ajax请求的url
		 * sendData : ajax请求的参数
		 * callback : 数据返回并处理之后的回调，如果指定newPath，则以新的路径查询返回值作为参数，否则以原path查询
		 * saveData : [可选]ajax返回之后用于处理数据的回调
		 * newPath : [可选]参考callback
		 */
		_getAppData : function(params){//path, url, callback, saveData, newPath
			var path = params.path,
				url = params.url,
				sendData = params.sendData,
				callback = params.callback,
				saveData = params.saveData,
				newPath = params.newPath;

			var self = this;

			var result = data.deepQuery(path, this.data);
			if(!result){
				data.ajax(url, function(json){
					saveData && saveData(json);
					callback && callback(data.deepQuery(newPath||path, self.data));
				}, sendData);
			}else{
				var tmp = newPath ? data.deepQuery(newPath, self.data) : result;
				callback && callback(tmp);
			}
		},

		news : function(date, id){
			var self = this;
			this.currentDate = date;
			this.currentId = id;

			this._getAppData({
				path : date,
				url : this.conf.newsUrl + 'date/' + date + '/id/' + id,
				callback : function(news){
					var ns = self.newsESwiper;

					var result = self.data[date];
					ns.total = result.total;

					var index = result['id2seq'][id] || 0;
					self._initCover();

					if(id != 'cover'){
						self.coverSwiper.swipeTo(1);
					}

					ns.reInit( index );
				},
				saveData : function(json){
					data.mix(json.data, self.data);
				}
			});
		},

		_bindShare : function(){
			var self = this;

			$('body').hammer()
			.on('tap', '.weibo', function(){
				self._setTaskDone('t3', self.currentDate);

				var url = location.href;
				var news = self.data[self.currentDate].news[self.currentId];
			    var content = news.title;
				var pic = news.share_img;
				location.href = 'http://service.weibo.com/share/share.php?url='+ encodeURIComponent(url) + '&title=' + content + '&pic='+pic;
			})
			.on('tap', '.up, .down', function(){
				var id = $(this).attr('news-id');
				var act = $(this).hasClass('down')? 'down' : 'up';

				if( !$(this).hasClass('tap') ){
					$(this).addClass('tap');
					// var btn = $(this).removeClass('tap');
					// setTimeout(function(){ btn.addClass('tap'); });
				}

				data.ajax('/wc/' + act, function(json){
					self._setTaskDone('t2', self.currentDate);
				}, {id:id});
			});

		},

		_bindAction : function(){
			var self = this;
			var bodyHammer = $('body').hammer();

			var hash = '';
			bodyHammer
			.on('swiperight', '.first-card', function(){
				self.cover.show();
				self.coverSwiper.swipeTo(0);
				self.router.navigate('news/' + self.currentDate + '/cover');
			})
			.on('tap', '.start', function(){
				self.coverSwiper.swipeTo(1);
			})
			.on('tap', '.calendar', function(){
				self.router.navigate('news/' + self.currentDate + '/calendar', {trigger : true});
			})
			.on('tap', 'li.date', function(ev){
				ev.preventDefault();
				self.router.navigate('news/' + $(this).attr('date') + '/cover', {trigger : true});
			})
			;
		},

		_initCalendar : function(startDate){
			var m = Number(latestDate.substr(0, 2)) - 1;
			var d = Number(latestDate.substr(2, 4));
			var today = new Date(2014, m, d);

			today = new Date(today.getFullYear(), today.getMonth(), today.getDate());
			startDate.getMonth();
			today.getMonth();

			var tmp = new Date(startDate);
			var html = [];

			var tmpMonth = tmp.getMonth()+1;
			html.push($('#month-mark-tpl').tmpl({month:tmpMonth}).html());

			var endDate = new Date().setDate(today.getDate() + 3);
			var cls = '';

			tmp.setDate(tmp.getDate() - 1);
			var monthStr = dateStr = '';
			while( tmp.setDate(tmp.getDate() + 1) < endDate ){
				if(tmp.getDate() == 1){
					tmpMonth = tmp.getMonth()+1;
					html.push($('#month-mark-tpl').tmpl({month:tmpMonth}).html());
				}

				if(tmp.getMonth() == today.getMonth() && tmp.getDate() == today.getDate()){
					cls = 'today';
				}else{
					cls = 'before-date';
				}

				if(tmp <= today){
					monthStr = data.numFormat(tmp.getMonth() + 1);
					dateStr = data.numFormat(tmp.getDate());
					var date = data.timeFormat(tmp);
					var param = data.storage.get(date) || {};

					var t1_disabled = param.t1 == 1? '' : 'disabled';
					var t2_disabled = param.t2 == 1? '' : 'disabled';
					var t3_disabled = param.t3 == 1? '' : 'disabled';

					var obj = {
						date    : tmp.getDate(),
						dateStr : monthStr + '' + dateStr,
						cls     : cls,
						progress : param.p || 0,
						t1_disabled : t1_disabled,
						t2_disabled : t2_disabled,
						t3_disabled : t3_disabled
					};

					html.push( $('#common-date-tpl').tmpl(obj).html() );
				}

				if(tmp > today){
					html.push($('#future-date-tpl').tmpl( { date : tmp.getDate() } ).html());
				}

			}

			this.calendarListContent = html.join('');

			return this.calendarListContent;
		}
	};

	return App;
});