<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fetch extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Article_model', 'article');
        $this->load_params();

        $this->newsHost = 'http://m.baidu.com/';
        $this->newsIdLink = $this->newsHost . 'news?tn=bdapiworldcup&action=newslist&pn=1&rn=20&fr=funyue';
        $this->newsContent = $this->newsHost . 'news?tn=bdapibaiyue&t=recommendinfo&fr=funyue';

        $this->leboUrlMorning = 'http://leboapi.baidu.com/lebo/worldcup?type=morning';
        $this->leboUrlEvening = 'http://leboapi.baidu.com/lebo/worldcup?type=evening';
    }

    private function send( $url, $isPost, $data = array() ){
         
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        if( $isPost ){
            curl_setopt ( $ch, CURLOPT_POST, 1 );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query($data) );
        }
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;

    }

    public function index() {
        header("Content-Type: text/html;charset=utf-8");

        $info = json_decode( file_get_contents( $this->newsIdLink ), true );

        $idArr = array();
        foreach ($info['data']['news'] as $key => $news) {
            $idArr[] = $news['nid'];
        }

        $param = array(
                'mid' => 'fromfunyue',
                'nids' => implode(',', $idArr),
                'wf' => 1,
                'ver' => 2
            );

        $result = json_decode( $this->send( $this->newsContent, true, $param ), true );

        foreach ($result['data']['news'] as $key => $entry) {
            $this->insertNews( $entry );
        }

    }

    private function insertNews( $news ){
         
        $nid = $news['nid'];
        $type = 1; //快讯

        $selectSql = "select * from `bdnews` where nid = '$nid';";
        $result = $this->db->query( $selectSql )->result_array();

        if( count($result) > 0 ){
            echo $nid . ' existed<br>';
        }else{

            // 插入bdnews，防止重复
            $insertSql = "insert into `bdnews` (`nid`, `title`, `ori_time`, `created_time`) values (?, ?, ?, ?)";
            $param = array( $nid, $news['title'], $news['ts'], time());
            $this->db->query( $insertSql, $param );

            // 插入新闻表
            $data = array();
            $data['title'] = $news['title'];
            $data['source'] = $news['site'];
            $data['source_link'] = $news['display_url'];
            $data['cover'] = $this->getImageFromUrl( $news['imageurls'][0]['url'] );
            $data['type'] = $type;
            $data['media_mod'] = '';
            $data['score_mod'] = '';
            $data['event_mod'] = '';
            $data['share_img'] = '';
            $data['ad_img'] = 'http://m.baidu.com/static/search/ala/news-ad.png';
            $data['ad_link'] = 'http://app.news.baidu.com/install.htm?src=shijiebei';
            $data['from'] = 'news';
            $data['ext'] = '';
            $data['created_date'] = date('md');
            $data['top'] = 0;

            $data['content'] = '';
            foreach ($news['content'] as $key => $segment) {
                if( $segment['type'] == 'text' ){
                    $data['content'] .= '<p>' . $segment['data'] . '</p>';
                }
            }

            $result = $this->article->create( $data );

            echo $nid . ' added<br>';
        }

    }

    private function getImageFromUrl( $url ){
        $imgUrlAll = parse_url( $url );
        $arr = explode( '=', $imgUrlAll['path'] );
        $arr = explode( '&', $arr[1] );
        return $arr[0];
    }


    //*********** 上面是抓取新闻，下面是抓取乐播 *************

    public function fetchLebo(){
        $this->getByTime( $this->leboUrlMorning );
        $this->getByTime( $this->leboUrlEvening );
    }

    private function getByTime( $url ){
        $result = json_decode( $this->send( $url, false ), true );

        foreach ($result['data'] as $key => $entry) {
            $this->insertLebo( $entry );
        }
    }

    private function insertLebo( $entry ){

        $sid = $entry['song_id'];
        $type = 2; //乐播

        $selectSql = "select * from `bdlebo` where sid = '$sid';";
        $result = $this->db->query( $selectSql )->result_array();

        if( count($result) > 0 ){
            echo $sid . ' existed<br>';
        }else{

            $insertSql = "insert into `bdlebo` (`sid`, `song_name`, `ori_time`, `created_time`) values (?, ?, ?, ?)";
            $param = array( $sid, $entry['song_name'], $entry['add_time'], time() );
            $this->db->query( $insertSql, $param );

            $data = array();
            $data['title'] = $entry['song_name'];
            $data['source'] = '百度乐播';
            $data['source_link'] = 'http://lebo.baidu.com/';
            $data['cover'] = $entry['songpic'][0]['pic_url'];
            $data['type'] = $type;
            $data['media_mod'] = $entry['songfile'][0]['file_link'];
            $data['score_mod'] = '';
            $data['event_mod'] = '';
            $data['share_img'] = '';
            $data['ad_img'] = '';
            $data['ad_link'] = '';
            $data['from'] = 'lebo';
            $data['created_date'] = date('md');
            $data['content'] = '';
            $data['ext'] = $entry['songfile'][0]['song_duration'] . ',' . $entry['song_id'];
            $data['top'] = 0;

            $this->article->create( $data );
            echo $sid . ' added<br>';
        }
    }

}