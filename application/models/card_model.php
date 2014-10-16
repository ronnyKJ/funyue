<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function all() {
        $type_list = array(
            '1' => 'player',
            '2' => 'text',
            '3' => 'media',
            '4' => 'question',
            '5' => 'article'
        );
        $types = array();
        // 查询出每个类型中的卡片 ID
        $sql_group_by_type = 'SELECT group_concat(card_id) AS `card_ids`,`type` FROM (SELECT * FROM `cardcase` WHERE `match_id` != 0 ORDER BY `match_id`,`order` LIMIT 30) AS cards GROUP BY `type`';
        $result = $this->db->query($sql_group_by_type)->result_array();
        // 将每种类型卡片的内容查询出来
        foreach($result as $key => $value) {
            $sql = 'SELECT * from `' . $type_list[$value['type']] . '` WHERE `id` IN (' . $value['card_ids'] . ')';
            if(isset($types[$value['type']])) {
                array_push($types[$value['type']], $this->db->query($sql)->result_array());
            } else {
                $types[$value['type']] = $this->db->query($sql)->result_array();
            }
        }
        // 将卡片内容与卡片关联
        $sql_cards = 'SELECT * FROM `cardcase` WHERE `match_id` != 0 ORDER BY `match_id`, `order` LIMIT 30';
        $cards = $this->db->query($sql_cards)->result_array();
        foreach($cards as $key => $value) {
            foreach($types[$value['type']] as $key1 => $value1) {
                if($value1['id'] == $value['card_id']) {
                    $cards[$key] = array_merge($value1, $value);
                }
            }
        }
        return $cards;
    }

}

