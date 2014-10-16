var arr = location.pathname.match(/\/(\w+)(\/)?/i)
var modules = arr && arr[1] || 'wc';

requirejs.config({
    "baseUrl": "/public/vendor",
    "paths": {
      "app": "../modules",
      'jquery' : 'jquery/jquery',
      'modernizr': 'modernizr/modernizr', 
      'zepto': 'zepto/zepto',
      'lodash': 'lodash/dist/lodash',
      'hammer': 'hammerjs/dist/jquery.hammer.min',
      'moment': 'moment/min/moment.min',
      'zh_cn': 'moment/lang/zh-cn',
      'backbone' : 'backbone/Backbone.v1.1.0',
      'underscore' : 'underscore/underscore',
      'bootstrap': 'bootstrap/dist/js/bootstrap',
      'handlebars': 'handlebars/handlebars.amd.min',
      'wysihtml5': 'bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.all.min',
      'datetimepicker': 'bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min',
      'helper': '../common/js/helper',
      'plugins': '../common/js/plugins',
      'tmpl': 'jquery-tmpl/jquery.tmpl.min',
      'Swiper': 'swiper/dist/idangerous.swiper-2.4-zkj',
      'data' : '../modules/wc/js/data',
      'interactionFramework' : '../modules/worldcup/js/interactionFramework',
      'content' : '../modules/worldcup/js/content',
      'side' : '../modules/worldcup/js/side',

      'EndlessSlider' : '../modules/' + modules + '/js/EndlessSlider',
      'TileController' : '../modules/' + modules + '/js/TileController',
      'App' : '../modules/' + modules + '/js/App',

      'modalLayer': '../common/js/modalLayer'
    },
    shim: {
      'underscore': {
        exports: '_'
      },
      'backbone': {
        deps: ["underscore", "jquery"],
        exports: "Backbone"
      },
      'bootstrap': ['jquery'],
      'tmpl': {
        deps: ['jquery'],
        exports: 'tmpl'
      },
      'Swiper': {
        deps: ['jquery'],
        exports: 'Swiper'
      },
      'hammer': {
        deps: ['jquery'],
        exports: 'hammer'
      },
      'wysihtml5': ['handlebars', 'bootstrap'],
      'datetimepicker': ['bootstrap']
    },
    // urlArgs: "bust=" + (new Date()).getTime()
});

// requirejs(['/public/modules/' + modules + '/js/main.js?v=' + (+new Date)]);
requirejs(['/public/modules/' + modules + '/js/main.js?v=']);