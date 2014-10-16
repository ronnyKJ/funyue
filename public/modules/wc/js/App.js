window.WC = window.WC || {};

WC.App = 

(function (EndlessSlider, data) {
	
	function formatTime( time ){
		var nf = data.numFormat;
		return nf(parseInt(time/60)) + ':' + nf(parseInt( time%60 ));
	}

	function lebostat(sid, act, len){
		var os = '', ua = navigator.userAgent;
		if( ua.indexOf('iPhone') > 0 ){
			os = 'iphone';
		}else if( ua.indexOf('Android') > 0 ){
			os = 'android';
		}else if( ua.indexOf('iPad') > 0 ){
			os = 'ipad';
		}else{
			os = 'other';
		}
		new Image().src = 'http://nsclick.baidu.com/v.gif?timestamp=' + (+new Date) + '&pid=323&ref=leeboo&from=funyue&sid=' + sid + '&type=' + act + '&s2e=' + len + '&os=' + os;
	}

	function lebostat2(sid){
		new Image().src = 'http://leboapi.baidu.com/lebo/activity?item_id=' + sid + '&item_type=1&activity_type=2&from=funyue&type=activityPost&version=1.0.0&callback=jsonp2';
	}

	/*** main ***/
	var App = function(conf){
		this.tileController;
		this.data = {};
		this.router = conf.router;
		this.docHeight = document.body.clientHeight;
		window.player = this.player = new _mu.Player({
				mode: 'list',
				baseDir: 'http://m.baidu.com/static/search/ala/',
				emptyMP3: 'http://m.baidu.com/static/search/ala/fy-empty.mp3',
				engines: [{constructor : 'AudioCore'}]
			});
		this.audioStat = {};
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

			this._bindAction();
			this._bindShare();
			this._initAudio();

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
			}else if(id == 'cover'){
				jSlide.addClass('cover');
				self._addCover(jSlide);
				slide.type = 'cover';
			}else{
				this._addNewsSlide(jSlide, self, result, id, index);
				slide.type = 'news';
			}

		},

		_addCover : function(jSlide){
			var coverData = this.data[this.currentDate].cover;
			
			coverData.isToday = coverData && this._isToday( coverData.date );
			data.mix(this.coverBaseData, coverData);

			jSlide.css('background-image', 'url(' + coverData.img + ')').html('');
			coverData.isCover = true;
			$('#cover-tpl').tmpl( coverData ).appendTo(jSlide[0]);
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

			var calendarSwiper = $('.calendar-swiper', jSlide[0]).swiper({
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

			// 日历拉到最下方
			var wh = calendarSwiper.wrapper.clientHeight, dh = this.docHeight;
			var yScroll = dh >= wh ? 0 : 0 - wh + dh;
			calendarSwiper.setWrapperTranslate(0, yScroll, 0);
			var monthMark = $('.month-mark'), calendarMonth = $('.calendar-month');
			self._setMonth(monthMark, calendarMonth);
		},

		_addNewsSlide : function(jSlide, self, result, id, index){
			var entry = result.news[ id ];
			var slide = jSlide[0];
			slide.entry = entry;
			jSlide.addClass('card-slide');
			if(index == 0){
				jSlide.addClass('first-card');
			}

			// lebo
			if(entry.from == 'lebo'){
				slide.isLebo = true;
				var arr = entry.ext.split(',');
				slide.audioDuration = Number(arr[0]||0);
				slide.songid = arr[1] || 0;
			}

			entry.indexStr = index;
			// entry.content = $('<div />').html(entry.content).html();
			
			var tmpNewsId = entry.id;
			entry = data.mix(result.cover, entry);
			entry.id = tmpNewsId;
			entry.isCover = false;
			entry.isToday = this._isToday();
			if(!entry.source_link) entry.source_link = 'javascript:;';

			content = $('#news-article-tpl').tmpl(entry);
			$('.bottom', content).append($('#share-tpl').tmpl({ newsId : self.currentId }));

			jSlide.append(content);
			$('.page .top', slide).height(this.docHeight);
			$('.news-page', slide).swiper({
				mode:'vertical',
				scrollContainer: true,
				onTouchEnd : function(swiper){
					var opacity = swiper.positions.current < 0 ? 0 : 1;
					$('.tip', swiper.activeSlide()).animate({'opacity': opacity}, 300);
				}
			});

		},

		_initAudio : function(){
			var player = this.player;
			var self = this;

			player.on("timeupdate", function() {
				var pos = player.curPos(),
					dur = player.duration();

				var sid = self.audioStat.curSongid || 0;

				if(!self.audioStat.playend && dur > 0 && dur - pos < 2){
					lebostat(sid, 'playend', parseInt(pos*1000));
					self.audioStat.playend = true;
				}

				self._showProgress();
			}, true)
			.on("player:stop", function() {
				$('.mucontrol').removeClass('pause').addClass('play');
			}, true);


			$('body').hammer()
			.on('tap', '.muplayer', function(ev){
				ev.preventDefault();
				var control = $('.mucontrol', this);
				var isPlay = control.hasClass('play');
				
				if( isPlay ){				
					player.play();
					control.removeClass('play').addClass('pause');
					//playstart统计
					if(!self.audioStat.started){
						lebostat2( self.audioStat.curSongid );
						self.audioStat.started = true;
					}
				}else{
					player.pause();
					control.removeClass('pause').addClass('play');
					lebostat(self.audioStat.curSongid, 'playend', parseInt(player.curPos()*1000));
				}
			});


		},

		_showProgress : function(){
			var slide = this.newsESwiper.swiper.activeSlide();
			var pos = player.curPos(),
		    	duration = player.duration()||slide.audioDuration||0;
			$(".muplayer .progress", slide).html( '<span class="red">'+formatTime( pos ) + '<span><span class="gray">/' + formatTime( duration ) + '</span>' );
		},

		_playAudio : function(slide){
			this.audioStat.curSongid = slide.songid || 0;
			this.audioStat.started = false;
			this.audioStat.playend = false;
			var src = $('.lebo-src', slide).val().split(',');
			this.player.reset();
			var self = this;
			setTimeout(function(){
				self.player.add( src );
				self._showProgress();
			}, 0);
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
				upMonth = monthMark[0];
			}

			calendarMonth.html(upMonth.innerHTML);			
		},

		_onSlideChangeEnd : function(swiper, direction){
			var self = this;
			var activeSlide = swiper.activeSlide()
			var i = $(activeSlide).attr('card-index');

			var result = self.data[ self.currentDate ];
			self.currentId = result.seq2id[ i ];
			self.router.navigate('news/' + self.currentDate + '/' + self.currentId, {trigger : false});

			// 这一段是统计，在一个乐播已经播放的页面滑走，发统计
			var prevSlide = swiper.slides[swiper.previousIndex];
			if( prevSlide.isLebo && prevSlide.songid && this.player.curPos() > 0.5 ){
				lebostat(prevSlide.songid, 'playend', parseInt(this.player.curPos()*1000));
			}

			if(activeSlide.isLebo){
				this._playAudio(activeSlide);
			}else{
				this.player.reset();	
			}
			_hmt.push(['_trackEvent', 'slidechange', 'end', self.currentDate, self.currentId]);
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
					_hmt.push(['_trackPageview', url]);
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
					if( !news.cover && news.news.length == 0 ){
						self.router.navigate('news/' + latestDate + '/cover', {trigger : true});
						return;
					}

					var ns = self.newsESwiper;

					var result = self.data[date];
					ns.total = result.total;

					var index = result['id2seq'][id] || 0;

					ns.reInit( index );
					self._afterNewsInit();

				},
				saveData : function(json){
					data.mix(json.data, self.data);
				}
			});
		},

		_afterNewsInit : function(){
			var as = this.newsESwiper.swiper.activeSlide();
			if( as.isLebo ){
				this._playAudio(as);
			}		
		},

		_getCurrentNews : function(){
			return this.data[ this.currentDate ].news[ this.currentId ];
		},

		_bindShare : function(){
			var self = this;

			$('body').hammer()
			.on('tap', '.weibo', function(){

				var url = 'http://' + location.host + '/?from=weibo' + location.hash;
				var news = $(this).parents('.card-slide')[0].entry||{};
			    var content = (news.title||'') + '%23FUN阅%23 %23世界杯%23 @世界杯围观小分队';
				var pic = news.share_img || self.conf.shareImg;
				setTimeout(function(){
					location.href = 'http://service.weibo.com/share/share.php?url='+ encodeURIComponent(url) + '&title=' + content + '&pic='+pic;
				}, 0);
			})
			.on('tap', '.up, .down', function(){
				var $this = $(this);
				var id = $this.attr('news-id');
				var act = $this.hasClass('down')? 'down' : 'up';

				if( !$this.hasClass('disabled') && !$this.hasClass('tap') ){
					$this.addClass('tap');

					if( $this.hasClass('up') ){
						$('.share .down').addClass('disabled');
					}
					if( $this.hasClass('down') ){
						$('.share .up').addClass('disabled');
					}
				}

				data.ajax('/wc/' + act, function(json){
					
				}, { id : id });
			});

		},

		_bindAction : function(){
			var self = this;

			$('body').hammer()
			.on('swiperight', '.first-card', function(){
				self.router.navigate('news/' + self.currentDate + '/cover', {trigger : false});
			})
			.on('tap', '.ad', function(){
				
			})
			.on('tap', '.calendar', function(){
				_hmt.push(['_trackEvent', 'calendar_icon', 'tap']);
				self.router.navigate('news/' + self.currentDate + '/calendar', {trigger : true});
			})
			.on('tap', 'li.date', function(ev){
				ev.preventDefault();
				var date = $(this).attr('date');
				self.router.navigate('news/' + date + '/cover', {trigger : true});
				_hmt.push(['_trackEvent', 'calendar', 'tap', date]);
			})
			;
		},

		_initCalendar : function(startDate){
			var m = Number(latestDate.substr(0, 2)) - 1;
			var d = Number(latestDate.substr(2, 4));
			var today = new Date(2014, m, d);
			var nf = data.numFormat;

			today = new Date(today.getFullYear(), today.getMonth(), today.getDate());
			startDate.getMonth();
			today.getMonth();

			var tmp = new Date(startDate);
			var html = [];

			var tmpMonth = tmp.getMonth()+1;
			html.push($('#month-mark-tpl').tmpl({month:tmpMonth}).html());

			var endDate = new Date(today).setDate(today.getDate() + 3);
			var cls = '';

			tmp.setDate(tmp.getDate() - 1);
			var monthStr = dateStr = mdStr = '';
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
					monthStr = nf(tmp.getMonth() + 1);
					dateStr = nf(tmp.getDate());

					mdStr = monthStr + '' + dateStr;

					var obj = {
						date    : dateStr,
						dateStr : mdStr,
						cls     : cls,
						count   : Number( countInfo[ mdStr ] ) || 0
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

})(WC.EndlessSlider, WC.data);