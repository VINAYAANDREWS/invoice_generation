<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_generation extends MY_Controller {

    function __construct() {
        parent::__construct();
         $this->load->model('model_invoice');
    }

 
    public function login() {

        $view_data = array();  
        if ($this->input->post()) {

            $username = $this->input->post('username');
            $password = $this->input->post('password');                                
            if ($username && $password) {

                $user_data = $this->model_mini_bank->user_login($username, $password);
                
                if (isset($user_data['error']) && $user_data['error']) {
                    $this->session->set_flashdata('msg', $user_data['error']);
                    
                    redirect('invoice_generation');
                } else if (isset($user_data['user']) && $user_data['user']) {
                    
                      $user_session_data = array(
                                         'user_id' => $user_data['user']['id'],
                                         'username' => $user_data['user']['username'],
                                         'user_type' => $user_data['user']['user_type']
                        );
                        $this->session->set_userdata($user_session_data);
                   redirect('invoice_generation/add_customers');
                }                                                
            }
        }
       $this->load->view('login_admin',$view_data);
    }
// login 
    
    
     
//   add/edit customer data
      
    public function add_customers() {
         $view_data = array();  
        
        $user_id = $this->session->userdata('user_id');
        
        $type='customer';
        $view_data['customers']=$this->model_mini_bank->get_customers($type); 
//                  
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('phone', 'Phone', 'required');
            $this->form_validation->set_message('required', 'You must provide a %s.');

            if ($this->form_validation->run() == TRUE) {
                $insert['user_type']='customer';
                  $insert['name']=$this->input->post('name');
                  $insert['email']=$this->input->post('email');
                  $insert['phone']= $this->input->post('phone');
                  $insert['address']= $this->input->post('address');
              
               
                      //insert to table
                $insert_data= $this->model_mini_bank->create_customer($insert); 
                 
                 if($insert_data){       
                    $this->session->set_flashdata('msg', 'Created Customer successfully.');
                 }else{
                     $this->session->set_flashdata('error', 'Something went wrong.');
                 } 
                  
                 redirect("invoice_generation/add_customers");
             }
             
        }

         $this->load->view('items',$view_data);
    }
// crate customer 

    
    
       
 public function index() {
        
       $view_data['items']=$this->model_invoice->get_items();
      
        
        if($this->input->post()){
            $view_data['name']=$institute=$this->input->post('name');
           
            
        }
        $this->load->view('generate_invoice',$view_data);
            
        }


}
