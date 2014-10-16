requirejs.config({
    "baseUrl": "",
    "paths": {
      'jquery'     : 'public/vendor/jquery/jquery',
      'zepto'      : 'public/vendor/zepto/zepto',
      'hammer'     : 'public/vendor/hammerjs/dist/jquery.hammer.min',
      'backbone'   : 'public/vendor/backbone/Backbone.v1.1.0',
      'underscore' : 'public/vendor/underscore/underscore',
      'tmpl'       : 'public/vendor/jquery-tmpl/jquery.tmpl.min',
      'Swiper'     : 'public/vendor/swiper/dist/idangerous.swiper-2.4-zkj',


      'data'          : 'public/modules/wc/js/data',
      'EndlessSlider' : 'public/modules/wc/js/EndlessSlider',
      'App'           : 'public/modules/wc/js/App'
    },
    shim: {
      'underscore': {
        exports: '_'
      },
      'backbone': {
        deps: ["underscore", "jquery"],
        exports: "Backbone"
      },
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
      }
    },
    // urlArgs: "bust=" + (new Date()).getTime()
});

// requirejs(['/public/modules/' + module + '/js/main.js?v=' + (+new Date)]);
// requirejs(['/public/modules/' + module + '/js/main.js?v=']);
// requirejs(['/dist/lib.min.js?v=', '/dist/app.min.js?v=']);