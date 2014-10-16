<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stats_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->locale = array();
        $this->locale['zh_CN'] = array(
            'shot' => '射门', 
            'shot_on_target' => '射正',
            'shot_on_frame' => '射中门框',
            'fouls' => '犯规',
            'offside' => '越位',
            'corner' => '角球',
            'penalty' => '点球',
            'free_kick' => '任意球',
            'steal' => '抢断',
            'goal' => '进球',
            'pass' => '传球',
            'pass_through' => '直塞',
            'pass_success_rate' => '传球成功率',
            'possesion_rate' => '控球率',
            'yellow_card' => '黄牌',
            'red_card' => '红牌'
            );
    }

    protected function combine_match_info($match_info, $match_id) {
        $results = array('id'=>$match_id, 'stats' => array());
        if(count($match_info)) {
            $index_home = $match_info[0]['team_id'] == $match_info[0]['home_id'] ? 0 : 1;
            $index_away = $index_home == 0 ? 1 : 0;
            foreach ($this->locale['zh_CN'] as $subject => $subject_translation) {
                array_push($results['stats'], array(
                    'subject' => $subject_translation,
                    'home' => $match_info[$index_home][$subject],
                    'away' => $match_info[$index_away][$subject]
                ));
            }
        }
        return $results;
    }

    public function match_summary($match_id) {
        $results = $this->db
            ->select('stats_match_summary.*, match.home_id, match.away_id')
            ->from('stats_match_summary')
            ->join('match', 'stats_match_summary.match_id = match.id')
            ->where(array('stats_match_summary.match_id' => $match_id))
            ->get()->result_array();
        $results = $this->combine_match_info($results, $match_id);
        return $results;
    }

    public function match_timeline($match_id) {
        $results = $this->db
            ->get_where('stats_match_timeline', array('match_id'=>$match_id))
            ->result_array();
        return $results;
    }

    public function one($team_id = 0) {
        return $this->db->get_where('team', array('id' => $team_id))->result_array();
    }
}