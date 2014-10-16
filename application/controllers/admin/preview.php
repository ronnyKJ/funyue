<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require( APPPATH.'core/Admin_Controller.php' );

class Preview extends MY_Controller {

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
        $code = $this->params['index'];
        if( !( $code == md5(date('YmdH')) ) ){
            die('md');
        }

        $latestDate = $this->article->getLatestDate(2);
        $countArr = $this->article->getCountFromDate('0610');

        $arrInfo = array();
        foreach ($countArr as $key => $value) {
            $arrInfo[$value['created_date']] = $value['count'];
        }

        $data = array(
                'mod' => 'preview',
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
        $articles = $this->article->date($date, 2);

        $seq2id = array();
        $id2seq = array();
        $news = array();
        $i = 0;
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

        $cover = $covers[0];
        $cover['dateStr'] = $dateStr;

		$out['news'] = $news;
		$out['total'] = count($articles) + 1;
		$out['seq2id'] = $seq2id;
		$out['id2seq'] = $id2seq;
		$out['cover'] = $cover;

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
}

?>