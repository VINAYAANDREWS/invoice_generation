<?php
class Model_invoice extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    
    public function get_items(){
        $this->db->select('i.*');
        $this->db->from('test_items i');
        $query=$this->db->get();
        $result=$query->result_array();
        return $result;
    }
}