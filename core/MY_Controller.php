<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{
   
    # Constructor
    function __construct (){
        parent::__construct();
        define("BASE_URL",base_url());
        define("SITE_URL",site_url());
        define("PUBLIC_URL",base_url('public'));
        $timezone = config_item('time_reference');
        if ( function_exists( 'date_default_timezone_set' ) ){
         date_default_timezone_set($timezone);
        }
        $this->initial_session_load();
    }
    
    protected function handle_null_active_record($data_array){
        foreach($data_array as $key => $value){
        if (!$value){
         unset($data_array[$key]);
        }
      }
      return $data_array;
    }
    
    protected function initial_session_load() {
        
        $initial_session = $this->session->userdata('initial_session');
        if ($initial_session != TRUE) {
            
            $white_label = get_option(array('option' => 'white_label'));
            $wl_details = json_decode($white_label,TRUE);
            
            
            //product title
            if (isset($wl_details['white_label']) && $wl_details['white_label'] == "TRUE" && 
                    isset($wl_details['product_title']) && $wl_details['product_title']){
                $product_title = $wl_details['product_title'];
            } else if(isset($wl_details['white_label']) && $wl_details['white_label'] == "TRUE" && 
                    isset($wl_details['company_name']) && $wl_details['company_name']) {
                $product_title = $wl_details['company_name'];
            } else {
                $product_title = "Bank";
            }
            
            //url
            if (isset($wl_details['white_label']) && $wl_details['white_label'] == "TRUE" && 
                    isset($wl_details['product_url']) && $wl_details['product_url']){
                $product_url = $wl_details['product_url'];
            } elseif (isset($wl_details['white_label']) && $wl_details['white_label'] == "TRUE" && 
                    !isset($wl_details['product_url'])) {
                $product_url = NULL;
            } else {
                $product_url = 'https://mini_bank.in/';
            }
            
           
            
            //logo small
            if (isset($wl_details['white_label']) && $wl_details['white_label'] == "TRUE" && 
                    isset($wl_details['logo_small']) && $wl_details['logo_small'] && file_exists("./public/uploads/img/$wl_details[logo_small]")){
                $logo_small = PUBLIC_URL ."/uploads/img/$wl_details[logo_small]";
            } 
            
            //favicon
            if (isset($wl_details['white_label']) && $wl_details['white_label'] == "TRUE" && 
                    isset($wl_details['favicon']) && $wl_details['favicon'] && file_exists("./public/uploads/img/$wl_details[favicon]")){
                $favicon = PUBLIC_URL ."/uploads/img/$wl_details[favicon]";
            } else {
                $favicon = PUBLIC_URL ."/img/favicon.png";
            }
            
            
            
            
          
            
         
             $initial_session_settings = array(
                   
                    'favicon' => $favicon,
                    'product_title' => $product_title,
                    
                    'product_url' => $product_url
                );
            $initial_session_settings['initial_session'] = TRUE;
            
            $this->session->set_userdata($initial_session_settings);
        }
        
    }
    
}
