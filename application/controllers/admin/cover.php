<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require( APPPATH.'core/Admin_Controller.php' );

class Cover extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Cover_model', 'cover');
        $this->load_params();
    }

	public function show() {
        $data = $this->cover->all();
        $this->render('admin/cover_list', array('result' => $data));
	}

    public function add() {
        $post_data = $this->input->post();
        $img = $post_data['img'];
        $date = $post_data['date'];
        $data = $this->cover->add($img, $date);
    }

    public function update() {
        $post_data = $this->input->post();
        $id = $post_data['id'];
        $img = $post_data['img'];
        $data = $this->cover->update($id, $img);
    }
}
