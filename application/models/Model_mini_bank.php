<?php 
class Model_mini_bank extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    function user_login($username,$password){ 
//        echo $username,$password;
        $user_data=array();
        $username = $this->db->escape($username);
        $query = $this->db->query("select id,password,username,user_type from test_users where username=$username");
        if($query->num_rows() == 1){ 
            $user=$query->row_array();
            $hash = $user['password'];
            $verify_password = password_verify($password,$hash);
                               
                    if( $verify_password === FALSE){
                       $user_data['error']='invalid'; 
                    }else{
                         $user_data['user']=$user;
                    }
        }else{
            $user_data['error']='invalid';
        }
           
        return $user_data;
        
      
    }
    
    function create_customer($insert) {
        $this->db->insert('test_users',$insert);
        return $this->db->insert_id();
    }
    function get_customers($type){
        $this->db->select('u.*');
        $this->db->from('test_users u');
        $this->db->where('user_type',$type);
        $query=$this->db->get();
        $result=$query->result_array();
        return $result;
    }
    
    
     function get_transactions(){
        $this->db->select('u.*');
        $this->db->from('test_users u');
        $this->db->join('customer_transaction c',"c.user_id = u.id");
        $this->db->join('transaction_additional_details t',"t.transaction_id = c.id",'left');
        $this->db->where('user_type',"customer");
        $query=$this->db->get();
        $result=$query->result_array();
        return $result;
    }
    
    
    
    
    
    }
    
    ?>