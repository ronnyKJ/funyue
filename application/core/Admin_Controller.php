<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends MY_Controller {

    function __contruct() {
        parent::__construct();
        
        $this->load_params();
    }

    protected function before() {
        session_start();
        // $day = date('d');
        $str = 'code';

        // var_dump($this->params);

        if( !(array_key_exists('isLogin', $_SESSION) && $_SESSION['isLogin'] == 1) ){

            $yes = date('Ymd');

            if( empty($this->params[ $str ]) || $this->params[ $str ]!=$yes){
                header("Content-Type: text/html;charset=utf-8"); 
                die('请登录');
            }else{
                $_SESSION['isLogin'] = 1;
            }

        }
    }
}