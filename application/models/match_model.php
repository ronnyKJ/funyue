<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Match_model extends CI_Model {

    protected $utc_server  = 8;

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    protected function format_data($match) {
        $match_time_server = $this->time_local_to_server($match['time'], $match['utc']);
        $match['time_local'] = intval($match['time']);
        $match['time']       = $match_time_server;
        return $match;
    }

    public function time_local_to_server($time, $utc) {
        $time_server = $time + ($this->utc_server - $utc) * 3600;
        return $time_server;
    }

    public function time_server_to_local($time, $utc) {
        $time_server = $time - ($this->utc_server - $utc) * 3600;
        return $time_server;
    }

    public function all($tournament_id) {
        $result = $this->db->query('select `match`.*, t1.english_abbr home_abbr, t2.english_abbr away_abbr from `match` left join `team` t1 on `match`.home_id = t1.id left join `team` t2 on `match`.away_id = t2.id')->result_array();
        // $result = $this->db->get_where('match', array('tournament_id' => $tournament_id))->result_array();
        $result = array_map(array($this, 'format_data'), $result);
        return $result;
    }

    public function one($match_id) {
        $result = $this->db->get_where('match', array('id' => $match_id))->result_array();
        $result = array_map(array($this, 'format_data'), $result);
        return $result;
    }

    public function all_by_team($team_id) {
        $this->db->from('match')->
            where( "home_id = $team_id OR away_id = $team_id" );
        $result = $this->db->get()->result_array();
        $result = array_map(array($this, 'format_data'), $result);
        return $result;
    }

    public function all_by_round($tournament_id, $round) {
        $result = $this->db->get_where('match', array('round' => $round, 'tournament_id' => $tournament_id))->result_array();
        $result = array_map(array($this, 'format_data'), $result);
        return $result;
    }

    public function all_by_date($date) {
        $one_day_in_seconds = 86400;
        $result = $this->db->get_where('match', array('time >' => $date, 'time <' => $date + $one_day_in_seconds))->result_array();
        $result = array_map(array($this, 'format_data'), $result);
        return $result;
    }

    public function support($match_id, $team) {
        $team = $team == 'home' ? 'home_support' : 'away_support';
        $sql = 'UPDATE `match` SET `'.$team.'`=`'.$team.'`+1 WHERE `id` = ?';
        $params = array($match_id);
        return $this->db->query($sql, $params);
    }
}