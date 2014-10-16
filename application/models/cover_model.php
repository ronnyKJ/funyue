<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cover_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // 根据日期查询日报
    public function date($date){
        $sql = 'select * from `article` where created_date = "'.$date.'" AND hide = 0';
        return $this->db->query($sql)->result_array();
    }

    // 根据日期查询封面
    public function cover($date){
        $sql = 'select * from cover where date = "' . $date . '" limit 1';
        return $this->db->query($sql)->result_array();
    }

    public function all() {
        $sql = 'select * from cover order by created_time DESC';
        return $this->db->query($sql)->result_array();
    }

    public function add($img, $date) {
        $sql = 'INSERT INTO `cover` (date, img, update_time, created_time) VALUES(?, ?, ?, ?)';
        $now = time();
        if(empty($date)) $date=date('md');
        $params = array(
                $date,
                $img,
                $now,
                $now
            );
        $this->db->query($sql, $params);
    }

    public function update($id, $img) {
        $sql = 'UPDATE `cover` SET img=? WHERE id = ?';
        $params = array(
                $img,
                $id
            );
        $this->db->query($sql, $params);
    }
}