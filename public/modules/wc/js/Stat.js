window.WC = window.WC || {};

WC.Stat = 

(function () {
	var url = 'http://nsclick.baidu.com/u.gif?';

	var param = {};
	reset();

	function set(key, val){
		param[ key ] = val;
	}

	function getUrl(){
		var s = [];
		for(var i in param){
			s.push( i + '=' + param[i] );
		}
		return url + s.join('&');
	}

	function send(key, val){
		set(key, val);
		new Image().src = getUrl();
		reset();
	}

	function reset(){
		param = {
			'dv' : 2014,
			'pid' : 240
		};
	}

	return {
		send : send
	};

})();