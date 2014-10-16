<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function all() {
        return $this->db->get('player')->result_array();
    }

    public function one($player_id = 0) {
        $result = $this->db->select('p.*, c.tournament_id as relation_tournament_id, c.match_id as relation_match_id, c.team_id as relation_team_id')
        ->from('player as p')
        ->where('p.id', $player_id)
        ->join('cardcase as c', 'c.card_id = p.id', 'left')
        ->get()->result_array();
        $result[0]['relation_type'] = 1;
        return $result;
    }

    public function update($data) {
        $ret = parent::update($this->db, 'player', $data);
        return $ret;
    }

    public function create($data) {
        $ret = parent::create($this->db, 'player', $data, 'translated_name');
        return $ret;
    }

}