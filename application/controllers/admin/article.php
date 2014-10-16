<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require( APPPATH.'core/Admin_Controller.php' );

class Article extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Article_model', 'article');
        $this->load->model('Cover_model', 'cover');
        $this->load->helper('url');
        $this->config->load('article_type');
        $this->config->load('env');
        $this->load_params();
        $this->typeMap = $this->config->item('article_type');
    }

	public function index() {
        redirect(base_url('/admin/article/type/show'), 'location', 301);
	}

    public function articleConfig() {
        header("Content-Type: text/html;charset=utf-8");
        $html = '';
        foreach ($this->typeMap as $key => $value) {
            $html .= $key . ' <span style="width:100px;display:inline-block;">' . $value[0] . '</span><span style="display:inline-block; width:30px; height:15px; background-color:' . $value[1] . ';"></span><br>'; 
        }
        echo $html;
    }

    public function preview() {
        $data1 = $this->article->preview($this->params['preview']);
        $article = json_encode( $data1[0] );

        $data2 = $this->cover->cover($data1[0]['created_date']);
        $cover = json_encode( $data2[0] );

        $this->load->view('preview', array( 'article' => $article, 'cover' => $cover ));
    }

    public function edit(){
        $data = $this->article->preview($this->params['edit']);
        $this->output($data);
    }

    // 查询列表
    public function type() {
        $type   = empty($this->params['type']) ? 'show' : $this->params['type'];
        $date   = empty($this->params['date']) ? date('md') : $this->params['date'];

        $hide   = $type == 'show' ? 0 : 1;
        $result = $this->article->all($date, $hide);

        foreach ($result as $key => $news) {
            $result[$key]['typeStr'] = $this->typeMap[ $news['type'] ][0];
            $result[$key]['typeColor'] = $this->typeMap[ $news['type'] ][1];
        }

        $data = array(
            'result' => $result,
            'date' => $date,
            'uri' => array(
                'hide' => str_replace('show', 'hide', uri_string()), 
                'show' => str_replace('hide', 'show', uri_string()))
        );
        array_walk($data['uri'], function(&$url){
            $url = base_url($url);
        });
        $this->render('admin/article_list', $data);
    }

}
