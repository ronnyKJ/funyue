<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Article_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // 根据日期查询日报
    public function date($date, $hide){
        if($hide == 0){//显示的
            $sql = 'UPDATE `article` SET `views`=`views`+1 WHERE `hide` = 0 AND `created_date` = ?';
            $params = array($date);
            $this->db->query($sql, $params);
        }
        
        $showStr = " " . $this->getShowCondition($hide, 'AND');

        $sql = 'select * from article where created_date = "' . $date . '"' . $showStr . ' order by top DESC, create_at DESC';

        return $this->db->query($sql)->result_array();
    }

    public function all($date, $hide) {
        $hide = $hide == 0 ? 0 : 1;
        $this->db
        ->from('article')
        ->where(array('created_date' => $date, 'hide' => $hide))
        ->order_by('top DESC, create_at DESC');

        $result = $this->db->get()->result_array();
        
        return $result;
    }

    // 根据日期查询封面
    // 0-显示 1-隐藏 2-两者
    public function getLatestDate($hide){
        $con = $this->getShowCondition($hide, 'where');
        $sql = 'select created_date from `article` ' . $con . ' order by created_date DESC limit 10';

        $result = $this->db->query($sql)->result_array();

        // 预览的时候直接显示最新一天
        if( $hide == 2 ){
            return $result[0]['created_date'];
        }

        $today = intval(date('md'));
        foreach ($result as $key => $entry) {
            $date = $entry['created_date'];
            if( intval($date) <= $today ){
                return $date;
            }
        }

        return '0610';//如果找不到有数据的日期，返回默认开始日期
    }

    private function getShowCondition($hide, $prefix){
        if($hide == 0){
            $showStr = $prefix . ' hide = 0'; // 返回显示的
        }elseif($hide == 1){
            $showStr = $prefix . ' hide = 1'; // 返回隐藏的
        }else{
            $showStr = ''; // 返回全部
        }

        return $showStr;
    }

    public function create($data) {
        $sql = 'INSERT INTO `article` (
            `title`,
            `source`,
            `source_link`,
            `content`,
            `create_at`,
            `views`,
            `hide`,
            `cover`,
            `created_date`,
            `created_time`,
            `up`,
            `down`,
            `type`,
            `media_mod`,
            `score_mod`,
            `event_mod`,
            `share_img`,
            `ad_img`,
            `ad_link`,
            `from`,
            `ext`,
            `top`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $created_date = $data['created_date'];
        $created = time();
        $params = array(
                $data['title'],
                $data['source'],
                $data['source_link'],
                $data['content'],
                $created,
                0,
                1,
                $data['cover'],
                $created_date,
                date('H:i', $created),
                0,
                0,
                $data['type'],
                $data['media_mod'],
                $data['score_mod'], 
                $data['event_mod'],
                $data['share_img'],
                $data['ad_img'],
                $data['ad_link'],
                $data['from'],
                $data['ext'],
                $data['top']
            );
        $this->db->query($sql, $params);

        $this->updateCoverTime($created, $created_date);
    }

    public function update($data) {
        $sql = 'UPDATE `article` SET 
            `title`=?,
            `source`=?,
            `source_link`=?,
            `content`=?,
            `cover`=?,
            `created_date`=?,
            `type`=?,
            `media_mod`=?,
            `score_mod`=?,
            `event_mod`=?,
            `share_img`=?,
            `ad_img`=?,
            `ad_link`=?,
            `from`=?,
            `ext`=?,
            `top`=?
            WHERE id = ?';

        $params = array(
                $data['title'],
                $data['source'],
                $data['source_link'],
                $data['content'],
                $data['cover'],
                $data['created_date'],
                $data['type'],
                $data['media_mod'],
                $data['score_mod'], 
                $data['event_mod'],
                $data['share_img'],
                $data['ad_img'],
                $data['ad_link'],
                $data['from'],
                $data['ext'],
                $data['top'],
                $data['id']
            );
        $this->db->query($sql, $params);

        $now = time();
        $date = date('md', $now);
        $this->updateCoverTime($now, $date);
    }

    private function updateCoverTime($update_time, $date){
        $sql = 'UPDATE `cover` SET update_time=? WHERE date = ?';
        $params = array(
                $update_time,
                $date
            );
        $this->db->query($sql, $params);
    }

    public function hide($id) {
        return $this->db->update('article', array('hide' => 1), array('id' => $id));
    }

    public function show($id) {
        return $this->db->update('article', array('hide' => 0), array('id' => $id));
    }

    public function one($article_id) {
        $sql = 'UPDATE `article` SET `views`=`views`+1 WHERE `id` = ?';
        $params = array($article_id);
        $this->db->query($sql, $params);

        $sql = 'SELECT * FROM `article` WHERE hide = 0 AND id = ?';
        $params = array($article_id);
        $result = $this->db->query($sql, $params)->result_array();
        return $result;
    }

    public function preview($article_id) {
        $sql = 'SELECT * FROM `article` WHERE id = ?';
        $params = array($article_id);
        $result = $this->db->query($sql, $params)->result_array();
        return $result;
    }

    public function up($id) {
        $sql = 'UPDATE `article` SET `up`=`up`+1 WHERE `id` = ?';
        $params = array($id);
        $this->db->query($sql, $params);
    }

    public function down($id) {
        $sql = 'UPDATE `article` SET `down`=`down`+1 WHERE `id` = ?';
        $params = array($id);
        $this->db->query($sql, $params);
    }

    public function recommend() {
        $dateStr = date('md');


        $result = $this->getRecommendByDate( $dateStr );

        if( count($result) == 0 ){
            $latestDate = $this->getLatestDate( 0 );
            $result = $this->getRecommendByDate( $latestDate );
        }

        return $result;
    }

    private function getRecommendByDate( $dateStr ){
        $countSQL = 'SELECT COUNT(DISTINCT(type)) AS count FROM article';
        $countResult = $this->db->query($countSQL)->result_array();
        $count = $countResult[0]['count'];//type

        
        $infoType = 1;//快讯类
        $all = array();

        $infoArr = $this->getLatestRecordByType( $infoType, $dateStr, $count-1 );
        
        if( count( $infoArr ) > 0 ){
            $final = array( $infoArr[0] );
        }


        
        for( $i = 2; $i < $count; $i++ ) {
            $set = $this->getLatestRecordByType( $i, $dateStr, 1 );
            if( count( $set ) == 0 ){
                if( isset( $infoArr[ $i-1 ] ) ){
                    $final[] = $infoArr[ $i-1 ];
                }
            }else{
                $final[] = $set[0];
            }
            
        }

        return $final;  
    }

    private function getLatestRecordByType( $type, $dateStr, $count ){
        $sql = 'SELECT id, title, created_date, type FROM `article` 
        WHERE created_date = ? AND hide = 0 AND type = ?
        order by top DESC, create_at DESC 
        LIMIT ?';

        $param = array(
                $dateStr,
                $type,
                $count
            );

        return $this->db->query($sql, $param)->result_array();
    }

    public function getCountFromDate($date){
        $sql = "SELECT created_date, COUNT(created_date) AS count FROM article WHERE hide = 0 AND created_date >= '$date' GROUP BY created_date;";
        return $this->db->query($sql)->result_array();
    }
}