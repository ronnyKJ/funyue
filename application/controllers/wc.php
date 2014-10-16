<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WC extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Team_model', 'team');
        $this->load->model('Article_model', 'article');
        $this->load->model('Cover_model', 'cover');
        $this->config->load('article_type');
        $this->load_params();
        $this->typeMap = $this->config->item('article_type');
    }

	public function index(){
        $latestDate = $this->article->getLatestDate(0);
        $countArr = $this->article->getCountFromDate('0610');

        $arrInfo = array();
        foreach ($countArr as $key => $value) {
            $arrInfo[$value['created_date']] = $value['count'];
        }

        $data = array(
                'startDate' => 'new Date(2014, 5, 10)',
                'openDate' => 'new Date(2014, 5, 13)',//20140613
                'latestDate' => $latestDate,
                'static_url' => 'http://m.baidu.com/static/search/ala/',
                'countInfo' => $arrInfo
            );
		$this->load->view('wc', $data);
	}

	public function news(){
		$date = $this->params['date'];

        $cardResults = array();
        $articles = $this->article->date($date, 0);

        $seq2id = array();
        $id2seq = array();
        $news = array();


        $id2seq['cover'] = 0;
        $i = 1;

        $seq2id[] = 'cover';
        foreach ($articles as $key => $article) {
        	$seq2id[] = $article['id'];
        	$id2seq[ $article['id'] ] = $i++;
        	$news[ $article['id'] ] = $article;
            
            $news[ $article['id'] ]['typeStr'] = $this->typeMap[ $article['type'] ][0];
            $news[ $article['id'] ]['typeColor'] = $this->typeMap[ $article['type'] ][1];
        }

        $seq2id[] = 'calendar';
    	$id2seq[ 'calendar' ] = $i;

        $dateStr = substr($date, 0, 2) . '月' . substr($date, 2, 4) .'日';

        $covers = $this->cover->cover($date);

        if( count( $covers ) > 0 ){
            $cover = $covers[0];
            $cover['dateStr'] = $dateStr;
            $cover['updateTime'] = date('H:i', $cover['update_time']);
            $out['cover'] = $cover;
        }

		$out['news'] = $news;
		$out['total'] = count($articles) + 2;
		$out['seq2id'] = $seq2id;
		$out['id2seq'] = $id2seq;
		
		$result[$date] = $out;
		$this->output($result);
	}

    public function up(){
        $data = $this->input->post();
        $id = $data['id'];
        $result = $this->article->up($id);
        $this->output($result);
    }

    public function down(){
        $data = $this->input->post();
        $id = $data['id'];
        $result = $this->article->down($id);
        $this->output($result);
    }

    public function getXML(){
        header("Content-type: text/xml");

        $result = $this->article->recommend();
        $list = array();
        foreach( $result as $key => $entry ){
            $type = $this->typeMap[ $entry['type'] ];
            $url = 'http://funyue.duapp.com/?from=wise#news/' . $entry['created_date'] . '/' . $entry['id'];
            $list[] = '<list title="[' . $type[0] . ']'. $entry['title'] .'" url="' . $url . '"/>';
        }
        
        $listXML = implode( '', $list );
        echo $this->renderXML( $listXML );
    }

    private function renderXML( $list ){
        return '<?xml version="1.0" encoding="UTF-8"?>
        <DOCUMENT>'
        . $this->getItem('世界杯', $list)
        . $this->getItem('世界杯日报', $list)
        . $this->getItem('世界杯消息', $list)
        . $this->getItem('世界杯新闻', $list)
        . $this->getItem('世界杯资讯', $list)
        . $this->getItem('世界杯最新消息', $list)
        . $this->getItem('funyue世界杯', $list)
        . $this->getItem('翻阅世界杯', $list)
        .'</DOCUMENT>';
    }

    private function getItem( $key, $list ){
        return '<item>
            <key>'.$key.'</key>
            <display>
                <url>http://funyue.duapp.com/</url>
                <title>世界杯日报</title>
                <daily>' 
                . $list .
                '</daily>
            </display>
        </item>';
    }
}

?>