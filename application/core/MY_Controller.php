<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    protected $params;

    function __contruct() {
        parent::__construct();
    }

    function __destruct(){
        $this->db->close();
    }

    public function _remap($method, $args) {
        // Call before action
        $this->before();
        
        call_user_func_array(array($this, $method), $args);
        
        // Call after action
        $this->after();
    }
    
    /**
     * These shouldn't be accessible from outside the controller
    **/
    protected function before() { return; }
    protected function after() { return; }

    protected function output($array) {
        $output = array('data' => $array);
        header('Content-Type: application/json');
        echo json_encode($output);
    }

    protected function load_params() {
        if($this->uri->total_segments() == 3) {
            $length = 2;
        } else {
            $length = 3;
        }
        $this->params = $this->uri->uri_to_assoc($length);
    }

    protected function render($view, $data = array()) {
        if(!is_array($data)) {
            $data = array();
        }
        $this->load->view('header');
        $this->load->view($view, $data);
        $this->load->view('footer');
    }
}