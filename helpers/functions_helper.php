<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function check_permission() {
    
    $CI = & get_instance();
    $group_id = $CI->session->userdata('group_id');
    $user_id = $CI->session->userdata('user_id');
    $CI->load->model('model_users');
    $user_action = $CI->model_users->isset_user_permission($user_id);
    $class_name = $CI->router->fetch_class();
    $method_name = $CI->router->fetch_method();
    $ajax_flag = FALSE;
    $ajax_class = array('ajax','ajax_transport','ajax_finance','ajax_student','ajax_employee','ajax_modal','ajax_widget','ajax_view',
        'ajax_academic_calendar','ajax_select','ajax_payment_gateway','ajax_payroll',
        'ajax_library','ajax_time_table','ajax_academics','ajax_inventory',
        'ajax_student_leave','ajax_lms','ajax_crm','ajax_certificate','ajax_student_profile');
    if(in_array($class_name, $ajax_class)){
        $ajax_flag=TRUE;
    }

    if ($ajax_flag == FALSE && $group_id != 1) {

        $CI->load->model('model_access_permission');
        if ($user_action){
            $row = $CI->model_access_permission->check_user_access_permission($user_id,$class_name,$method_name);
        } else {
            $row = $CI->model_access_permission->check_user_group_access_permission($group_id,$class_name,$method_name);
        }
        if (!$row) {
            exit("<h2>Access Denied</h2>");
        }
    }
    
    
    if ($group_id == STUDENT_USERGROUP_ID || $group_id == PARENT_USERGROUP_ID) {
        $menu_key="student";
    } else if ($group_id == COMPANY_USERGROUP_ID) {
        $menu_key="company";
    } else {
        $menu_key="primary";
    }
        
    $MENU_ITEMS=menu_items($menu_key);
    $CI->site_data = array('MENU_ITEMS' => $MENU_ITEMS);
}

function widget_access($widget_name) {
    
    $CI = & get_instance();
    
    if($CI->session->userdata('user_type')=='super_admin'){
    return TRUE;    
    }
    
    $CI->load->model('model_widget');
    $CI->load->model('model_users');
    $user_id = get_logged_userid();
    $isset_user_widget = $CI->model_users->isset_user_widget_access($user_id);
    if ($isset_user_widget) {
        $result = $CI->model_widget->check_user_widget_permission($user_id,$widget_name);
    } else {
        $group_id = get_current_user_group_id(); 
        $result = $CI->model_widget->check_user_group_widget_permission($group_id,$widget_name);
    }
    
    
    if ($result == 1){
        return TRUE;
    } else {
        return FALSE;
    }
}

function get_widgets($group_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_widget');
    $widgets = $CI->model_widget->get_user_group_widget_by_group_id($group_id); 
    return $widgets;
   
}

function get_user_widget($group_id,$user_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_widget');
    $CI->load->model('model_users');
    
    $isset_user_widget = $CI->model_users->isset_user_widget_access($user_id);
    if ($isset_user_widget) {

        $widgets = $CI->model_widget->get_user_widgets($user_id);
        
    } else {

        $widgets = $CI->model_widget->get_all_user_group_widgets($user_id,$group_id);

    } 
    if(!$widgets && $CI->session->userdata('user_type')=='super_admin'){
    $widgets = $CI->model_widget->get_super_admin_widgets(); 
    }
    return $widgets;
}

function show_widget($widget_name){
    
   if (widget_access($widget_name) == TRUE){
    $CI = & get_instance(); 
    $CI->load->view("include/widget/$widget_name");
    }    
  
}

function get_option($array) {
    
    $CI = & get_instance();  
    $CI->load->model('model_option');
    $defaults = array(
        
        'option'           => '',
        'institute_id'     => false,
        'exact_institute'   => false
        
    );
    
    $option = array_merge($defaults, $array);
    $data = array();
    
    if ($option['option'] && $option['institute_id'] && $option['exact_institute']) {
        
        $data = $CI->model_option->get_option($option['option'],$option['institute_id']);
    }
    elseif ($option['option'] && $option['institute_id'] && !$option['exact_institute']) {
        
        $data = $CI->model_option->get_option($option['option'],$option['institute_id']);
        if (!$data) {
            
            $data = $CI->model_option->get_option($option['option']);
        }
    } elseif ($option['option'] && !$option['institute_id'] && !$option['exact_institute']) {
            
            $data = $CI->model_option->get_option($option['option']);
    }
    
    if ($data) {
        return $data['value'];
    } else {
        return NULL;
    }
  
}
               
function menu_items($menu_key){
        $CI = & get_instance();
        
        $user_id = $CI->session->userdata('user_id');
        $CI->load->model('model_users');
        $user_action = $CI->model_users->isset_user_permission($user_id);
        $group_id = $CI->session->userdata('group_id');        
        $CI->load->model('model_access_permission');
        if ($user_action){
            $item_data = $CI->model_access_permission->get_user_menu_elements($user_id,$menu_key);
        } else {
            $item_data = $CI->model_access_permission->get_user_group_menu_elements($group_id,$menu_key);
        }
        
        $menus = array(
	'items' => array(),
	'parents' => array()
        );

        foreach($item_data as $item){ 
        $menus['items'][$item['id']] = $item;
        $menus['parent'][$item['id']]=$item['parent_id'];
        $menus['order'][$item['id']]=$item['menu_order'];
        if($item['group_action_id'] || $item['menu_show']=='show' || $group_id == SUPER_ADMIN_USERGROUP_ID){
	
	$menus['parents'][$item['parent_id']][] = $item['id'];
        
        $menus['child'][$item['id']] = $item['parent_id'];
        }
        }
        
        $menus=_parent_enable($menus);
       
        return $menus;
     
        
}

function menu($menus,$active_menu,$location){
        
       
     if($location=='left'){
        return _left_menu(0, $menus,$active_menu,'first-ul'); 
     }
     else if($location=='top'){
        return _top_menu(0, $menus,$active_menu,'first-ul'); 
     }
     
        
}

function _sort_menu($items,$menus){
   
    foreach ($items as $itemId) {
            $order[]=$menus['order'][$itemId];
            $item_order[]=$itemId;
            
    }
    array_multisort($order, $item_order);
     
     return $item_order;
 }
 
function _parent_enable($menus){
        $flag=0;
        foreach($menus['child'] as $new_item){
           if(!isset($menus['child'][$new_item]) && isset($menus['items'][$new_item]) && $menus['items'][$new_item]) { 
           $flag=1;
           $menus['parents'][$menus['parent'][$new_item]][] = $new_item;
           $menus['child'][$new_item] = $menus['parent'][$new_item];
           }
           
        }
        if($flag==1){
           $menus= _parent_enable($menus);
           }
           return $menus;
 }
 
function  _top_menu($parent, $menu,$active_menu,$ul=false) {
    if($ul=='first-ul'){
   $html = "<ul style='display:none' class='main-nav'>";
    }
    else{
        $html = "<ul class='dropdown-menu'>";
    }
   if (isset($menu['parents'][$parent])) {
       
      $menu['parents'][$parent]=_sort_menu($menu['parents'][$parent],$menu);
      
       foreach ($menu['parents'][$parent] as $itemId) {
            if($menu['items'][$itemId]['url']){
                   $menu_link=site_url($menu['items'][$itemId]['url']);   
             }
             else{
                      $menu_link="javascript:void(0)";
             }
             if($active_menu==$menu['items'][$itemId]['active_menu']){
                  $class='active';
             }
             else{
                  $class='';
             }
          if(!isset($menu['parents'][$itemId])) {
              
               $html .="<li class='$class'>";
                  
               $html .=" <a  href='".$menu_link."'>
                    <span>".$menu['items'][$itemId]['name']."</span>
                </a></li>";
            
          }
          if(isset($menu['parents'][$itemId])) {
             
               if($ul!='first-ul'){
                 $html .="<li class='dropdown-submenu'>";
              }
              else{
              $html .="<li class='$class'>";
                  }
                 
               $html .=" <a data-toggle='dropdown' class='dropdown-toggle' href='".$menu_link."'>
                    <span>".$menu['items'][$itemId]['name']."</span>";
               if($menu['items'][$itemId]['parent_id']==0){
                       $html .= "<span class='caret'></span>";
               }
                $html .= "</a>";
             
             $html .= _top_menu($itemId, $menu,$active_menu);
             $html .= "</li>";
          }
       }
       $html .= "</ul>";
   }
   return $html;
}
function  _left_menu($parent, $menu,$active_menu,$ul=false) {
    if($ul=='first-ul'){
   $html = "<ul class='subnav-menu'>";
    }
    else{
        $html = "<ul>";
    }
   if (isset($menu['parents'][$parent])) {
       
      $menu['parents'][$parent]=_sort_menu($menu['parents'][$parent],$menu);
      
       foreach ($menu['parents'][$parent] as $itemId) {
            if($menu['items'][$itemId]['url']){
                   $menu_link=site_url($menu['items'][$itemId]['url']);   
             }
             else{
                      $menu_link="javascript:void(0)";
             }
             
             if($active_menu==$menu['items'][$itemId]['active_menu']){
                  $class='active';
             }
             else{
                  $class='';
             }
             
          if(!isset($menu['parents'][$itemId])) {
              
               $html .="<li class='$class'>";
                   if($ul=='first-ul'){
               $html .=" <h3><a class='".$menu['items'][$itemId]['name']."'   href='".$menu_link."'>
                    ".$menu['items'][$itemId]['name']."
                </a></h3>";
                   } else {
                       $html .=" <a class='".$menu['items'][$itemId]['name']."'   href='".$menu_link."'>
                    ".$menu['items'][$itemId]['name']."
                </a>";
                   }
                    $html .="</li>";
            
          }
          if(isset($menu['parents'][$itemId])) {
             
               
              $html .="<li class='$class'>";
                  
                 
               if($ul=='first-ul'){
               $html .=" <h3><a class='".$menu['items'][$itemId]['name']."'   href='".$menu_link."'>
                    ".$menu['items'][$itemId]['name']."
                </a></h3>";
                   } else {
                       $html .=" <a class='".$menu['items'][$itemId]['name']."'   href='".$menu_link."'>
                    ".$menu['items'][$itemId]['name']."
                </a>";
                   }
             
             $html .= _left_menu($itemId, $menu,$active_menu);
             $html .= "</li>";
          }
       }
       $html .= "</ul>";
   }
   return $html;
}
function  _left_menu__($parent, $menu,$active_menu,$ul=false) {
    if($ul=='first-ul'){
   $html = "<ul class='subnav-menu'>";
    }
    else{
        $html = "<ul class='dropdown-menu'>";
    }
   if (isset($menu['parents'][$parent])) {
       
      $menu['parents'][$parent]=_sort_menu($menu['parents'][$parent],$menu);
      
       foreach ($menu['parents'][$parent] as $itemId) {
            if($menu['items'][$itemId]['url']){
                   $menu_link=site_url($menu['items'][$itemId]['url']);   
             }
             else{
                      $menu_link="#";
             }
             
             if($active_menu==$menu['items'][$itemId]['active_menu']){
                  $class='active';
             }
             else{
                  $class='';
             }
             
          if(!isset($menu['parents'][$itemId])) {
              
               $html .="<li class='$class'>";
                  
               $html .=" <a class='".$menu['items'][$itemId]['name']."'   href='".$menu_link."'>
                    ".$menu['items'][$itemId]['name']."
                </a></li>";
            
          }
          if(isset($menu['parents'][$itemId])) {
             
               if($ul!='first-ul'){
                 $html .="<li class='dropdown-submenu'>";
              }
              else{
              $html .="<li class='dropdown $class'>";
                  }
                 
               $html .=" <a class='".$menu['items'][$itemId]['name']." dropdown-toggle' data-toggle='dropdown'  href='".$menu_link."'>
                    ".$menu['items'][$itemId]['name'];
               if($menu['items'][$itemId]['parent_id']==0){
                       
               }
                $html .= "</a>";
             
             $html .= _left_menu($itemId, $menu,$active_menu);
             $html .= "</li>";
          }
       }
       $html .= "</ul>";
   }
   return $html;
}

function check_uploaded_image_size($image_size,$default = false) {
    
    if (!$default) {
        $default= config_item('upload_img_config');
    }
    if (isset($default['max_width']) && $default['max_width'] < $image_size['0'] || $default['max_height'] < $image_size['1']) {
        return FALSE;
    } else {
        
        return TRUE;
    }
}

function get_page_title($public = FALSE,$default_title = FALSE){
    
    $CI = & get_instance();
    $title = '';
    $title .= $CI->session->userdata('product_title');
    if ($public) {
        $client_name = $CI->session->userdata('client_name');
        $d_title = $default_title ? " | ".$default_title : '';
        $title .= " - ".$client_name.$d_title;
    } else {
    
        $CI->load->model('model_action_label');
        $class_name = $CI->router->fetch_class();
        $method_name = $CI->router->fetch_method();
        $label = $CI->model_action_label->get_action_label($class_name,$method_name);

        if ($label) {
            $head = ucfirst($label['label']);
        } else {
            $institute_details = get_option(array('option' => 'institute_group_name'));
            $details = json_decode($institute_details, TRUE);
            if (isset($details['name'])) {
                $head = $details['name'] ? $details['name'] : '';
            }
        }
        $d_title = $head ? " - ".$head : '';
        $title .= $d_title;
    } 
    return $title;
    
}

/** action_log(action_name,action_type,table_name,table_key,module_name); */
function action_log($action_name,$action_type,$table_name,$id=false,$module=false){
  
    $CI = & get_instance();
    $CI->load->model('model_action_log');
    $CI->load->model('model_users');
   
    if($module){
       $insert['module_name']  = $module;
      
    }
    else{
         $class_name = $CI->router->fetch_class();
         $method_name = $CI->router->fetch_method();
         $module_name = $CI->model_action_log->get_module_name($class_name,$method_name);  
         $insert['module_name'] = $module_name['module_name'];

    }
    
//    var_dump($class_name);
//    var_dump($method_name);
//    var_dump($module_name);
    $insert['action_type'] = $action_type;
    
    $insert['table_name'] = $table_name;
    $insert['table_key'] = $id;
    $insert['user_id'] = $CI->session->userdata('user_id');
    $user_name = $CI->model_users->get_user($insert['user_id']);
    $insert['username'] = $user_name['username'];
    $insert['action_name'] = $action_name;
    $insert['date_time'] = current_date_time();
    $CI->model_action_log->create_action_log($insert);
    
}

function get_receiver_code($receiver, $institute, $course = false, $batch = false,
                $emp_category=false,$emp_department=false,$user_id=false){
    $CI = & get_instance();
    $CI->load->model('model_institutes');
    $CI->load->model('model_courses');
    $CI->load->model('model_batches');
    $CI->load->model('model_employee');
    $CI->load->model('model_students');
    $CI->load->model('model_users');
    $CI->load->model('model_parents');
    $institutes = $CI->model_institutes->get_institute_by_id($institute);
    $courses = $CI->model_courses->get_course_details($course);
    $batches = $CI->model_batches->get_batch_details_by_id($batch);
    $value="";
    
    if($receiver == 'category') {
        
        $code = 'em-inst';
        $employee['institute'] = $institute;
        $employee['category'] = $emp_category;
        $employee['status'] = 'active';
       
        $employees = $CI->model_employee->get_employee_list($employee);
        $category = $CI->model_employee->get_employee_categories_by_id($emp_category);
        if(isset($institutes) && $institutes) {
            $result['title'] = " Employees : ". $institutes['code'];
        }
        
        $result['code'] = $code.'-category-'.$emp_category;
        
        
        if(isset($category) && $category && isset($institutes) && $institutes) {
            $value = "emp".'_'.$institutes['code']."_".$category['category_name'];
        }
    }
    
    if($receiver == 'department') {
        $CI->load->model('model_department');
        $code = 'em-inst';
        $employee['institute'] = $institute;
        $employee['department_id'] = $emp_department;
        $employee['status'] = 'active';
        $employees = $CI->model_employee->get_employee_list($employee);
        $department = $CI->model_department->get_department_by_id($emp_department);
        if(isset($institutes) && $institutes) {
            $result['title'] = " Employees : ". $institutes['code'];
        }
        $result['code'] = $code.'-'.'department-'.$emp_department;
        if(isset($department) && $department && isset($institutes) && $institutes) {
            $value = "emp".'_'.$institutes['code']."_".$department['department'];
        }
    }
    
    if($receiver == 'batch') {
       
            $code = 'st-inst';
            $student['institute'] = $institute;
            $student['batch'] = $batch;
            $student['status'] = 'active';
            if ($institutes['adm_year_in_batch_name']) {
                
                $batch_code = $batches['batch_code']."-".$batches['admission_year'];
                
                $result['title'] = " Students : ". $institutes['code']." | Course Code : ".$batches['course_code']." | Batch Code : ".$batch_code;
                $result['code'] = $code.'-batch-'.$batch;
                $value = "student".'_'.$institutes['code'].'_'.$batches['course_code'].'_'.$batch_code;
            } else {
                $result['title'] = " Students : ". $institutes['code']." | Course Code : ".$batches['course_code']." | Batch Code : ".$batches['batch_code'];
                $result['code'] = $code.'-batch-'.$batch;
                $value = "student".'_'.$institutes['code'].'_'.$batches['course_code'].'_'.$batches['batch_code'];
            }
            
        }
        if($receiver == 'user') {
//        
        $code = 'user-inst';
        $user['institute'] = $institute;
        $user['user_id'] = $user_id;
        $user['status'] = 'active';
        $users= $CI->model_users->get_user($user_id, 'id');
        if(isset($institutes) && $institutes) {
            $result['title'] = " User : ". $institutes['code'];
        }
 
        $result['code'] = $code.'-user-'.$user_id;
 
        if(isset($users) && $users && isset($institutes) && $institutes) {
            $value = "user".'_'.$institutes['code']."_".$users['username'];
        }
    }
     
    $result['value'] = remove_special_characters($value);
    return $result;
}

function date_sort($a, $b) {
    return strtotime($a) - strtotime($b);
}
