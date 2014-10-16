window.WC = window.WC || {};

WC.EndlessSlider = 

(function ($, Swiper) {

	var EndlessSlider = function(config){
		this.swiper;
		this.slideTmpl = '<div></div>';
		this.maxLength = 3;
		this.docWidth = document.body.clientWidth;
		this.total = config.total || 0;
		this.firstActiveSlideDataIndex = config.firstActiveSlideDataIndex;
		this.config = config;
    
		this.init(config);
	};

	EndlessSlider.prototype = {
		init : function(config){
			var self = this;
			
			this.swiper = $(config.swiper).swiper({
				onTouchStart : function(swiper){
					self.addSlide(swiper);
				},
				onSlideChangeEnd : function(swiper, direction){
					self.config.onSlideChangeEnd && self.config.onSlideChangeEnd(swiper, direction);
				},
				resistance : '100%'
			});
		},

		addSlide : function(swiper){
			var self = this;
			var index = Number($(swiper.activeSlide()).attr('card-index'));
			if(swiper.activeIndex == 0 && index > 0) self.manageSlide('leftBound');
			if(swiper.activeIndex == swiper.slides.length - 1 && index < self.total - 1) self.manageSlide('rightBound');			
		},

		reInit : function(index){
			$(this.swiper.container).find('.endless-swiper-wrapper').html('')[0].style.cssText = '';

			var i = typeof index != 'undefined' ? index : this.firstActiveSlideDataIndex;
			i = Math.max(0, Math.min(i, this.total-1));

			this.addFirstSlide(i);
			i > 0 && this.prependSlide();
			i < this.total - 1 && this.appendSlide();

			i == 0 && this.swiper.swipeTo(0, 0, false);
		},

		manageSlide : function(position){
			position == 'leftBound' && this.prependSlide();
			position == 'rightBound' && this.appendSlide();

			if(this.swiper.slides.length > this.maxLength){
				if(position == 'leftBound'){
					this.popSlide();
				}else if(position == 'rightBound'){
					this.shiftSlide();
				}
			}
		},

		addFirstSlide : function(index){
			var slide = this.swiper.appendSlide(this.slideTmpl);
			slide.innerHTML = '';
			$(slide).attr('card-index', index);
			this.config.onSlideAdded && this.config.onSlideAdded(slide, index);
		},

		prependSlide : function(){
			var firstIndex = Number($(this.swiper.slides[0]).attr('card-index')) - 1;
			var slide = this.swiper.prependSlide(this.slideTmpl);
			slide.innerHTML = '';
			this.swiper.swipeTo(this.swiper.activeIndex + 1, 0, false);
			this.processSlide(slide, firstIndex);
		},

		appendSlide : function(){
			var slides = this.swiper.slides;
			var lastIndex = Number($(slides[slides.length-1]).attr('card-index')) + 1;
			var slide = this.swiper.appendSlide(this.slideTmpl);
			slide.innerHTML = '';
			this.processSlide(slide, lastIndex);
		},

		processSlide : function(slide, index){
			$(slide).attr('card-index', index);
			this.config.onSlideAdded && this.config.onSlideAdded(slide, index);
			this.swiper.positions.start = -this.docWidth;
		},

		shiftSlide : function(){
			this.swiper.removeSlide(0);
			this.swiper.swipeTo(this.swiper.activeIndex - 1, 0, false);
		},

		popSlide : function(){
			this.swiper.removeLastSlide();
		}
	};

	return EndlessSlider;
})($, Swiper);