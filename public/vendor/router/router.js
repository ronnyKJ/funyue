/**
 * @fileOverview 基于正则的前端路由适配ui组件
 * @author mxc
 * @mail   maxingchi@baidu.com
 * @date   2013-1-10
 * @version 1.0
 **/

function Router(options) {
    this.fragment = '';
    this.observers = [];

    this._checkUrlInterval = null;
    this._hashChangeNotSupport = false;
    this._started = false;

    options.observers && this.addObservers(options.observers);
}

Router.INTERVAL = 50;

Router.prototype = {
    _isRegExp: function(reg) {
        return Object.prototype.toString.call(reg) == "[object RegExp]";
    },
    _routeToRegExp: function(route) {
        // Cached regular expressions for matching named param parts and splatted
        // parts of route strings.
        var optionalParam = /\((.*?)\)/g;
        var namedParam    = /:\w+/g;
        var splatParam    = /\*\w+/g;
        var escapeRegExp  = /[\-{}\[\]+?.,\\\^$|#\s]/g;

        route = route.replace(escapeRegExp, '\\$&')
                   .replace(optionalParam, '(?:$1)?')
                   .replace(namedParam, '([^\/]+)')
                   .replace(splatParam, '(.*?)');
        return new RegExp('^' + route + '$');
    },
    _extractParameters: function(route, fragment) {
        return route.exec(fragment).slice(1);
    },
    // Gets the true hash value. Cannot use location.hash directly due to bug
    // in Firefox where location.hash will always be decoded.
    _getHash: function() {
      var match = window.location.href.match(/#(.*)$/);
      return match ? match[1] : '';
    },
    _getBindCheckUrl: function() {
        var _this = this;
        return function(){
            _this._checkUrl();
        }
    },
    _init: function(options) {
        options = options || {};
        var bindCheck = this._getBindCheckUrl();
        if ('onhashchange' in window) {
            window.addEventListener("hashchange",bindCheck);
        }
        else {
            this._checkUrlInterval = setInterval(bindCheck, Router.INTERVAL);
            this._hashChangeNotSupport = true;
        }
        var fragment = this.fragment = this._getHash();
        if (!options.silent) return this._trigger(fragment);
    },
    _isUrlChanged: function(fragment) {
        return fragment !== this.fragment;
    },
    _checkUrl: function() {
        var fragment = this._getHash();
        if (this._isUrlChanged(fragment)) {
            this.fragment = fragment;
            this._trigger(fragment);
        }
    },
    _trigger: function(fragment) {
        var i, len;
        for (i = 0,len = this.observers.length;i < len;i++) {
            var observer = this.observers[i];
            if (observer.route.test(fragment)) {
                observer.callback(fragment);
                break;
            }
        }
    },
    _updateHash: function(fragment, replace) {
        if (replace) {
            var href = window.location.href.replace(/(javascript:|#).*$/, '');
            location.replace(href + '#' + fragment);
        } 
        else {
            // Some browsers require that `hash` contains a leading #.
            location.hash = '#' + fragment;
        }
    },
    start:function(){
        if (this._started) {
            throw Error("already started");
        }
        this._started = true;
        this._init();
    },
    addObserver: function(route, func) {
        var _this = this;

        if (!this._isRegExp(route)){
            route = this._routeToRegExp(route)
        };

        var callback = function(fragment) {
            var args = _this._extractParameters(route, fragment);
            func && func.apply(_this, args);
        };
        this.observers.push({
            route: route,
            callback: callback
        });
    },
    addObservers: function(routes) {
        for (var i = 0,len = routes.length;i < len;i++) {
            this.addObserver(routes[i].route,routes[i].callback);
        }
    },
    getRoute:function(){
        return this.fragment;
    },
    navigate: function(fragment, options) {
        options = options || {};
        if (options.trigger !== false) {
            options.trigger = true;
        }
        if (!this._isUrlChanged(fragment)) return;
        //here we must assign the value of this.fragment before this._updateHash in order to avoid executing the _trigger method when update hash
        this.fragment = fragment;
        this._updateHash(fragment, options.replace);

        if (options.trigger) {
            this._trigger(this.fragment);
        }
    }
}

// module.exports = Router;