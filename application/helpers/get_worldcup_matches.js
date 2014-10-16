var jsdom       = require('jsdom');
var fs          = require('fs');
var mysql       = require('mysql');
var _           = require('lodash');
var connection  = mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : '',
  database : 'pe'
});

var url  = 'http://zh.wikipedia.org/wiki/2014%E5%B9%B4%E4%B8%96%E7%95%8C%E7%9B%83%E8%B6%B3%E7%90%83%E8%B3%BD';
var file = 'data_worldcup_matches.html';

jsdom.env(file, function(error, window){
  var raw = window.document.getElementsByTagName('h4');
  var matchData = getGroupMatchInfo(raw);
  saveGroupMatchInfo(matchData);
});

var saveGroupMatchInfo = function(data) {
  connection.connect();
  var values = [];
  var groups = ['A','B','C','D','E','F','G','H'];
  // 整理数据
  connection.query('select id, name from team', function(error, rows, fields){
    if(error) throw error;
    data = _.sortBy(data, function(match){
      return match.order;
    });
    data.forEach(function(match){
      var hitHome = _.find(rows, function(row){
        return row.name == match.home;
      });
      var hitAway = _.find(rows, function(row){
        return row.name == match.away;
      });
      match.homeId = hitHome && hitHome.id || 0;
      match.awayId = hitAway && hitAway.id || 0;

      if(match.stadium == '潘塔纳尔体育场'
      || match.stadium == '亚马逊体育场') {
        match.utc = -4;
      } else {
        match.utc = -3;
      }

      match.group = 'Z';
      if(match.order <= 48) {
        match.round = Math.floor((match.order - 1 ) / 16) + 1;
        match.group = groups[Math.floor((match.homeId - 0.1) / 4)];
      } else if(match.order <= 56) {
        match.round = 4;
      } else if(match.order <= 60) {
        match.round = 5;
      } else if(match.order <= 62) {
        match.round = 6;
      } else if(match.order <= 64) {
        match.round = 7;
      }
      
      values.push('('+[
        match.order,
        1,
        '"世界杯"',
        '"' + match.group + '"',
        '"' + match.round + '"',
        '"' + match.home + '"',
        match.homeId,
        '"' + match.away + '"',
        match.awayId,
        match.time,
        '"' + match.stadium + '"',
        match.utc
      ].join(',')+')');
    });
    // 写入数据
    connection.query('insert into `match` (`id`, `tournament_id`, `tournament_name`, `group`, `round`, `home`, `home_id`, `away`, `away_id`, `time`, `stadium`, `utc`) values ' + values.join(',') + ';', function(error, rows){
      if(error) throw error;
      connection.end();
    });
  });
}

var getGroupMatchInfo = function(data) {

  var getAllMatchInfoInGroup = function(group) {
    var info = [];
    do {
      group = group.nextSibling;
      if(!group
        || group.nodeType !== 1
        || ~group.className.toLowerCase().indexOf('wikitable')
        || group.tagName.toLowerCase() != 'table')
        continue;
      info.push(getFields(group.querySelector('tr')));
    } while(group && (!group.tagName || ['h2','h3','h4'].indexOf(group.tagName.toLowerCase()) < 0))
    return info;
  }

  var getFields = function(row) {
    var field = row.firstChild;
    var TIME = 0;
    var HOME = 1;
    var ORDER = 2;
    var AWAY = 3;
    var STADIUM = 4;
    var fields = [];
    while(field) {
      if(field.nodeType === 1) {
        fields.push(field);
      }
      field = field.nextSibling;
    }
    var time = +(''+Date.parse(fields[TIME].innerHTML
                .replace(/\n|\r|\r\n|\n\r/ig, ' ')
                .replace(/<br \/>/ig, '')
                .replace(/年|月/ig, '-')
                .replace(/日/ig, '')))
    .substr(0,10);
    var home = fields[HOME].firstChild.firstChild.innerHTML || '';
    var away = fields[AWAY].firstChild.lastChild.innerHTML || '';
    var order = +fields[ORDER].innerHTML.match(/\d+/ig)[0];
    var stadium = fields[STADIUM].lastChild.previousSibling.innerHTML;
    return {
      time: time,
      home: home,
      away: away,
      order: order,
      stadium: stadium
    }
  }

  var i = 0;
  var matches = [];
  while(data[i]) {
    matches = matches.concat(getAllMatchInfoInGroup(data[i++]));
  }
  return matches;
}