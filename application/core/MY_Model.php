<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    // function __contruct() {
    //     parent::__construct();
    // }

    protected function convert_value(&$value, $key) {
        // NOTICE: 在上下文为数字环境时，== 比较时 php 会将字符串变为数字再比较，而 === 则不会。http://stackoverflow.com/questions/672040/comparing-string-to-integer-giving-strange-feedback
        if(strval($value) == strval(intval($value))) {
            $value = intval($value);
        } else {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            $value = htmlentities($value, ENT_QUOTES, 'UTF-8');
        }
        if($key == 'id' || preg_match("/_id$/i", $key)) {
            $value = intval($value);
        }
    }

    protected function create($db, $table, $data, $uni_field) {
        $data = $this->process_data($data);
        $exists = $db->get_where($table, array($uni_field => $data['local'][$uni_field]))->result_array();
        if(count($exists) > 0) {
            $ret = array('status' => '-1', 'error' => '该记录已存在。');
            return $ret;
        }
        $result = $db->insert($table, $data['local']);
        if($result == true) {
            $item_id = $db->insert_id();
            $result = $db->insert('cardcase', array_merge(array('card_id' => $item_id), $data['foreign']));
            if($result == true) {
                $ret = array('status' => '0', 'message' => '');
            } else {
                $ret = array('status' => '-1', 'message' => $result);
            }
        } else {
            $ret = array('status' => '-1', 'message' => $result);
        }
        return $ret;
    }

    protected function update($db, $table, $data) {
        $data = $this->process_data($data);
        $item_id = $data['local']['id'];
        $result = $db->update($table, $data['local'], array('id' => $item_id));
        if($result == true) {
            $result = $db->get_where('cardcase', array('card_id' => $item_id))->result_array();
            if(count($result) > 0) {
                $result = $db->update('cardcase', $data['foreign'], array('card_id' => $item_id));
            } else {
                $result = $db->insert('cardcase', array_merge(array('card_id' => $item_id), $data['foreign']));
            }
            if($result == true) {
                $ret = array('status' => '0', 'message' => '');
            } else {
                $ret = array('status' => '-1', 'message' => $result);    
            }
        } else {
            $ret = array('status' => '-1', 'message' => $result);
        }
        return $ret;
    }

    protected function process_data($data) {
        $local = array();
        $foreign = array();
        foreach($data as $key => $value){
            if($key[0] == '_') {
                unset($data[$key]);
            } elseif(preg_match("/^relation/i", $key)) {
                $foreign[substr($key, 9)] = $value;
                unset($data[$key]);
            }
        }
        if(count($foreign)) {
            $data = array(
                'local' => $data,
                'foreign' => $foreign
            );    
        }
        array_walk_recursive($data, array($this, 'convert_value'));
        return $data;
    }

}