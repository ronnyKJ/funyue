<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function all() {
        return $this->db->get('task')->result_array();
    }

    public function one($task_id = 0) {
        return $this->db->get_where('task', array('id' => $task_id))->result_array();
    }

    public function update($task) {
        $task = $this->process_data($task);
        return $this->db->update('task', $task, array('id' => $task['id']));
    }

    public function create($task) {
        $task = $this->process_data($task);
        return $this->db->insert('task', $task);
    }

}