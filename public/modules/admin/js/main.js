define([
  'app/admin/js/_article',
  'app/admin/js/_cover'
  ], function(){

  var methodName = window.location.href.match(/admin\/(\w+)#?/)[1];
  var method = require('app/admin/js/_' + methodName)();

});