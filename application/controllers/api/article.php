<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/GetImageSize.php');

class Article extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Article_model', 'article');
        $this->load_params();
    }

    public function index() {

    }

    public function show() {
        if(!empty($this->params['article'])) {
            $this->output($this->article->one($this->params['article']));
        }
    }

    public function create() {
        $form_data = $this->input->post();
        unset($form_data['id']);
        $result = $this->article->create($form_data);
        $this->output($result);
    }

    public function update() {
        $form_data = $this->input->post();
        $result = $this->article->update($form_data);
        $this->output($result);
    }

    public function hide() {
        $id = (int) $this->input->post('id');
        $action = $this->input->post('action');
        if(!is_int($id) || !in_array($action, array('hide', 'show'))) $result = false;
        $result = $this->article->$action($id);
        $this->output($result);
    }

}