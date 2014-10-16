<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function all($tournament_id) {
        return $this->db->get_where('team', array('tournament_id' => $tournament_id))->result_array();
    }

    public function one($team_id = 0) {
        return $this->db->get_where('team', array('id' => $team_id))->result_array();
    }

    public function all_by_group($tournament_id) {
        $results = $this->all($tournament_id);
        $groups = array();
        foreach ($results as $team) {
            if(!array_key_exists($team['group'], $groups)) {
                $groups[$team['group']] = array();
            }
            $team['gd'] = $team['goals'] - $team['goaled'];//净胜球
            array_push($groups[$team['group']], $team);
        }
        $results = array();
        while(list($group, $order) = each($groups)) {
            array_push($results, array('group'=>$group, 'order'=>$order));
        }
        return $results;
    }

    public function all_by_round($tournament_id = 0, $round = 0) {
        $sql = 'SELECT * FROM `team` WHERE `id` IN (SELECT `away_id` FROM `match` WHERE `round` = ? AND `tournament_id` = ? UNION SELECT `home_id` FROM `match` WHERE `round` = ? AND `tournament_id` = ?)';
        $params  = array($round, $tournament_id, $round, $tournament_id);
        $results = $this->db->query($sql, $params)->result_array();
        return $results;
    }
}