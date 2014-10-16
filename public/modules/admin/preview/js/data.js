define(

'data',

['jquery'], 

function ($) {

	var config = {
		localStorageKey : 'funyue'
	};

	var numFormat = function(num){
		return num < 10 ? '0' + num: num ;
	};

	var timeFormat = function(time){
		var date = new Date(time);
		var y = date.getFullYear();
		var m = numFormat(date.getMonth()+1);
		var d = numFormat(date.getDate());

		return m + '' + d;
	};

	var ajax = function(url, success, data){
		$.ajax({
			type : 'POST',
			url : url,
			data : data || {},
			dataType : 'json',
			success : function(json){
				success(json);
			},
			error : function(){

			}
		});
	};

	var storage = {
		set : function(key, val){
			var obj = JSON.parse(localStorage[config.localStorageKey] || '{}');
			obj[key] = val;
			localStorage[config.localStorageKey] = JSON.stringify(obj);
		},
		get : function(key){
			var obj = JSON.parse(localStorage[config.localStorageKey] || '{}');
			return obj[key];
		}
	};

	var mix = function(from, to){
		for(var i in from){
			to[i] = from[i];

		}
		return to;
	};

	var deepQuery = function(path, data){
		var arr = path.split('.');
		var i = 0, tmp = data, l = arr.length;
		while(i < l){
			if(arr[i] in tmp){
				tmp = tmp[arr[i]];
				i++;
				if(i >= l){
					return tmp;
				}
			}else{
				return undefined;
			}
		}
	};

	return {
		config : config,
		numFormat : numFormat,
		timeFormat : timeFormat,
		ajax : ajax,
		storage : storage,
		mix : mix,
		deepQuery : deepQuery
	};

});