<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_logged_userid() {
    // Get current CodeIgniter instance
    $CI = & get_instance();
    // We need to use $CI->session instead of $this->session
    $user_id = $CI->session->userdata('user_id');
    if (!isset($user_id)) {
        return false;
    } else {
        return $user_id;
    }
}

function is_logged_in() {
    // Get current CodeIgniter instance
    $CI = & get_instance();
    // We need to use $CI->session instead of $this->session
    $user = $CI->session->userdata('logged_in');

    $client_id = $CI->session->userdata('client_id');
    if ($client_id == $CI->config->item('client_id') && isset($user)) {
        return true;
    } else {
        return false;
    }
}
function is_user_logged_in($user_id) {
    // Get current CodeIgniter instance
    $CI = & get_instance();
    // We need to use $CI->session instead of $this->session
    $user = $CI->session->userdata('logged_in');

    $client_id = $CI->session->userdata('client_id');
    if ($client_id == $CI->config->item('client_id') && isset($user) && isset($user_id)) {
        return true;
    } else {
        return false;
    }
}

function  get_current_user_group_id() {
    $CI = & get_instance();
    $group_id = $CI->session->userdata('group_id');
    if (!isset($group_id)) {
        return false;
    } else {
        return $group_id;
    }
}

function check_user_session() {
    if (!is_logged_in()) {
        redirect('login');
    }
    

    $CI = & get_instance();

    $class_name = $CI->router->fetch_class();
    $method_name = $CI->router->fetch_method();
    
    $forced_password_change = $CI->session->userdata('forced_password_change');
    $email = $CI->session->userdata('isset_email');
    
    if (($class_name!='user' || $method_name!='logout') && ($class_name!='user' || $method_name!='settings') && $forced_password_change==1){
        redirect('user/user/settings');
    }
    
    if (($class_name!='user' || $method_name!='logout') && 
            ($class_name!='user' || $method_name!='settings') && 
            ($class_name!='user' || $method_name!='cancel_email_verification') && 
            ($class_name!='user' || $method_name!='verify_email') && $email==1){
        //$CI->session->set_flashdata('isset_email_msg','no_email_set');
        redirect('user/user/settings');
    }
    
    if ($class_name != 'user' && $method_name != 'index' && $CI->session->userdata('student_status') == 'roll_out') {
        redirect('dashboard');
    }

    check_permission();
}

function check_user_exist($user_id){
    $CI = & get_instance();
    $CI->load->model('model_users');
    $user = $CI->model_users->get_user($user_id);
    if($user){
        return TRUE;
    } else {
        return FALSE;
    }
}

function create_user_institute($user_id,$institute_id){
    
    $CI = &get_instance();
    $CI->load->model('model_users');
    
    $user_institute['user_id'] = $user_id;
    $user_institute['institute_id'] = $institute_id;
    
    $CI->model_users->insert_user_institute($user_institute);
    
}

function current_user_can($action_name) {
    
    $CI = & get_instance();    
    $group_id = $CI->session->userdata('group_id');
    if ($group_id == SUPER_ADMIN_USERGROUP_ID){
        $result = 1 ;
    } else {
        $user_id = $CI->session->userdata('user_id');
        $CI->load->model('model_users');
        $user_action = $CI->model_users->isset_user_permission($user_id);
        if ($user_action){
            $result = $CI->model_users->check_user_can_by_action_name_and_user_id($action_name,$user_id);
        } else {
            $CI->load->model('model_user_groups');
            $result = $CI->model_user_groups->check_user_can_by_action_name_and_group_id($action_name,$group_id);
        }               
    }
    if ($result==1){
        return TRUE;
    }else{
        return FALSE;
    }
    
}

function get_user_institutes($user_id) {
    $CI = & get_instance();
    $CI->load->model('model_institutes');
   
    $institutes = $CI->model_institutes->get_user_institutes($user_id);
   
    return $institutes;  
   
}

function create_user($user) {
    $CI = & get_instance();
    if (isset($user['password'])) {
        
        $user['password'] = password_hash($user['password'],PASSWORD_DEFAULT);
    }
    $user['created_at'] = current_date_time();
    if (!isset($user['status'])) {
        $user['status'] = 1;
    }
    $CI->load->model('model_users');
    $insert_id = $CI->model_users->create_user($user);
    return $insert_id;
}

function user_name_by_user_id($user_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_users');
    $user = $CI->model_users->get_user($user_id);
    
    if($user['user_type'] == 'super_admin'){
        
        $name = $user['username'];
        
    } elseif ($user['user_type'] == 'admin') {
        
        $details = $CI->model_users->get_administrator_by_user_id($user_id);
        $name = $details['full_name'];
        
    } elseif ($user['user_type'] == 'employee') {
        $CI->load->model('model_employee');
        $details = $CI->model_employee->get_employee($user_id,'user_id');
        $name = $details['full_name'];
    } elseif ($user['user_type'] == 'student') {
        $CI->load->model('model_students');
        $details = $CI->model_students->get_student($user_id,'user_id');
        $name = $details['full_name'];
    } elseif ($user['user_type'] == 'parent') {
        $CI->load->model('model_parents');
        $details = $CI->model_parents->get_parent($user_id,'user_id');
        $name = $details['full_name'];
    } 
    
    return $name;
    
}

function get_current_user_institute_name ($user_id) {
    
    $institutes = get_user_institutes($user_id);
    if (count($institutes) > 1 || !$institutes) {
        
        $institute_details = get_option(array( 'option' => 'institute_group_name'));
        $details = json_decode($institute_details,TRUE);
        if (isset($details['name'])){
            $user_institute = $details['name'];
        } 
        
    } else {
        $user_institute = $institutes[0]['name'];
    }
    
    return $user_institute;
}

function get_current_user_course_class_label($user_id,$user_type) {
    
    $CI = & get_instance();
    
    $CI->load->model('model_students');
    $client_type = get_option(array( 'option' => 'client_type'));
    switch ($client_type) {
        case 'k_12':
                   $label['s_course_class'] = 'Class';         
                   $label['p_course_class'] = 'Classes';
            break;
        case 'higher_education':
                   $label['s_course_class'] = 'Course';         
                   $label['p_course_class'] = 'Courses'; 
            
            break;
        case 'mixed' :
                    if ($user_type == 'student' || $user_type == 'parent') {
                        $student = $CI->model_students->get_student($user_id,'user_id');
                        $institute_id = $student['institute_id'];
                    }
                    else {
                        $user_institutes = get_user_institutes($user_id);
                        if (count($user_institutes) > 1) {
                            $label['s_course_class'] = 'Course';         
                            $label['p_course_class'] = 'Courses'; 
                        } else {
                            $institute_id = $user_institutes[0]['id'];
                        }
                    }
                    if (isset($institute_id) && $institute_id) {
                        $CI->load->model('model_institutes');
                        $institute_details = $CI->model_institutes->get_institute_by_id($institute_id);
                        $institute_type = $institute_details['institute_type'];
                        if ($institute_type == 'k_12'){
                            $label['s_course_class'] = 'Class';         
                            $label['p_course_class'] = 'Classes';
                        } elseif ($institute_type == 'higher_education') {
                            $label['s_course_class'] = 'Course';         
                            $label['p_course_class'] = 'Courses'; 
                        } else {
                            $label['s_course_class'] = 'Course';         
                            $label['p_course_class'] = 'Courses';
                        }
                    } else {
                        $label['s_course_class'] = 'Course';         
                        $label['p_course_class'] = 'Courses'; 
                    }
            break;
        default:
                $label['s_course_class'] = 'Course';         
                $label['p_course_class'] = 'Courses';    
    }
    
    return $label;
    
}

function get_current_user_fee_sem_settings($user_id,$user_type) {
    
    $CI = & get_instance();
    
    $CI->load->model('model_students');
    $client_fee_sem = get_option(array( 'option' => 'client_fee_sem'));
    switch ($client_fee_sem) {
        case 'yes':
                    $fee_sem = 'yes';
            break;
        case 'no':
                    $fee_sem = 'no';
            break;
        case 'mixed':
                   if ($user_type == 'student' || $user_type == 'parent') {
                        $student = $CI->model_students->get_student($user_id,'user_id');
                        $institute_id = $student['institute_id'];
                    }
                    else {
                        $user_institutes = get_user_institutes($user_id);
                        if (count($user_institutes) > 1) {
                            $fee_sem = "yes";
                        } else {
                            $institute_id = $user_institutes[0]['id'];
                        }
                    }
                    if (isset($institute_id) && $institute_id) {
                        $CI->load->model('model_institutes');
                        $institute_details = $CI->model_institutes->get_institute_by_id($institute_id);
                        $institute_fee_sem = $institute_details['fee_sem'];
                        if ($institute_fee_sem == 'yes'){
                            $fee_sem = "yes";
                        } elseif ($institute_fee_sem == 'no') {
                            $fee_sem = "no";
                        } else {
                            $fee_sem = "yes";
                        }
                    } else {
                        $fee_sem = "yes";
                    }
            break;
        default:
                $fee_sem = "yes";   
    }
    
    return $fee_sem;
}

function get_current_user_academic_sem_settings($user_id,$user_type) {
    
    $CI = & get_instance();
    
    $CI->load->model('model_students');
    $client_academic_sem = get_option(array( 'option' => 'client_academic_sem'));
    switch ($client_academic_sem) {
        case 'yes':
                    $academic_sem = 'yes';
            break;
        case 'no':
                    $academic_sem = 'no';
            break;
        case 'mixed':
                   if ($user_type == 'student' || $user_type == 'parent') {
                        $student = $CI->model_students->get_student($user_id,'user_id');
                        $institute_id = $student['institute_id'];
                    }
                    else {
                        $user_institutes = get_user_institutes($user_id);
                        if (count($user_institutes) > 1) {
                            $academic_sem = "yes";
                        } else {
                            $institute_id = $user_institutes[0]['id'];
                        }
                    }
                    if (isset($institute_id) && $institute_id) {
                        $CI->load->model('model_institutes');
                        $institute_details = $CI->model_institutes->get_institute_by_id($institute_id);
                        $institute_academic_sem = $institute_details['academic_sem'];
                        if ($institute_academic_sem == 'yes'){
                            $academic_sem = "yes";
                        } elseif ($institute_academic_sem == 'no') {
                            $academic_sem = "no";
                        } else {
                            $academic_sem = "yes";
                        }
                    } else {
                        $academic_sem = "yes";
                    }
            break;
        default:
                $academic_sem = "yes";
    }
    
    return $academic_sem;
}

function get_current_user_fee_sem_label($user_id,$user_type) {
    
    $CI = & get_instance();
    
    $CI->load->model('model_students');
    $client_fee_sem_label = get_option(array( 'option' => 'client_fee_sem_label'));
    switch ($client_fee_sem_label) {
        case 'sem':
                    $fee_sem_label = 'Sem';
            break;
        case 'year':
                    $fee_sem_label = 'Year';
            break;
        case 'mixed':
                   if ($user_type == 'student' || $user_type == 'parent') {
                        $student = $CI->model_students->get_student($user_id,'user_id');
                        $institute_id = $student['institute_id'];
                    }
                    else {
                        $user_institutes = get_user_institutes($user_id);
                        if (count($user_institutes) > 1) {
                            $fee_sem_label = "Sem/Year";
                        } else {
                            $institute_id = $user_institutes[0]['id'];
                        }
                    }
                    if (isset($institute_id) && $institute_id) {
                        $CI->load->model('model_institutes');
                        $institute_details = $CI->model_institutes->get_institute_by_id($institute_id);
                        $institute_fee_sem_label = $institute_details['fee_sem_label'];
                        if ($institute_fee_sem_label == 'sem'){
                            $fee_sem_label = "Sem";
                        } elseif ($institute_fee_sem_label == 'year') {
                            $fee_sem_label = "Year";
                        } else {
                            $fee_sem_label = "Year";
                        }
                    } else {
                        $fee_sem_label = "Year";
                    }
            break;
        default:
                $fee_sem_label = "Year";    
    }
    
    return $fee_sem_label;
}


function get_current_user_batch_year_label($user_id,$user_type) {
    
    $CI = & get_instance();
    
    $CI->load->model('model_students');
    $client_batch_year_label = get_option(array('option' => 'client_batch_year_label'));
    switch ($client_batch_year_label) {
        case 'academic_year':
                    $batch_year_label = 'Academic Year';
            break;
        case 'admission_year':
                    $batch_year_label = 'Admission Year';
            break;
        case 'mixed':
                   if ($user_type == 'student' || $user_type == 'parent') {
                        $student = $CI->model_students->get_student($user_id,'user_id');
                        $institute_id = $student['institute_id'];
                    }
                    else {
                        $user_institutes = get_user_institutes($user_id);
                        if (count($user_institutes) > 1) {
                            $batch_year_label = "Batch Year";
                        } else {
                            $institute_id = $user_institutes[0]['id'];
                        }
                    }
                    if (isset($institute_id) && $institute_id) {
                        $CI->load->model('model_institutes');
                        $institute_details = $CI->model_institutes->get_institute_by_id($institute_id);
                        $institute_batch_year_label = $institute_details['batch_year_label'];
                        if ($institute_batch_year_label){
                            $batch_year_label = $institute_batch_year_label;
                        } else {
                            //institute type school - academic year else admission year
                            
                            if ($institute_details['institute_type'] == 'k_12') {
                                $batch_year_label = "Academic Year";
                            } elseif ($institute_details['institute_type'] == 'higher_education') {
                                $batch_year_label = 'Admission Year';
                            } else {
                                $batch_year_label = "Batch year";
                            }
                        }
                    } else {
                        $batch_year_label = "Batch year";
                    }
            break;
        default:
                $batch_year_label = "Batch year";    
    }
    
    return $batch_year_label;
}

function echo_user_group($institute,$type = false, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_user_groups');
    $user_groups = $CI->model_user_groups->get_user_groups($institute,$type);
    
    foreach ($user_groups as $row) {
        if ($select == $row['id']) {
            $echo .= "<option selected value='$row[id]'>$row[group_name]</option>";
        } else {
            $echo .= "<option  value='$row[id]'>$row[group_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function generate_user_name($prefix,$user_type,$str_length) {

    
    $CI= & get_instance();
    
    $CI->load->model('model_users');
    $max_id = $CI->model_users->get_maxOf_max_id_by_prefix_and_user_type($prefix,$user_type);
    if($max_id) {
        $next_number = $max_id['max_id']+1;
    }
    else {
        $next_number = 1;   
    }
    $s_number=str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
    $user['user'] = $prefix.$s_number;
    $user['prefix'] = $prefix;
    $user['max_id'] = $next_number;
    return $user;
}

function get_logged_user_institute_id() {
    
    $CI = & get_instance();
    
    $user_id = $CI->session->userdata('user_id');
                
    $user_type = $CI->session->userdata('user_type');
    
    if ($user_type == 'admin') {
        $CI->load->model('model_users');
        $user_details = $CI->model_users->get_administrator_by_user_id($user_id);
        $sender_institute_id = $user_details['institute_id'];
    } else if ($user_type == 'employee') {
        $CI->load->model('model_employee');
        $user_details = $CI->model_employee->get_employee($user_id, 'user_id');
        $sender_institute_id = $user_details['institute_id'];
    } else if ($user_type == 'student') {
        $CI->load->model('model_students');
        $user_details = $CI->model_students->get_student($user_id, 'user_id');
        $sender_institute_id = $user_details['institute_id'];
    } elseif ($user_type == 'super_admin') {
        $CI->load->model('model_institutes');
        $institutes = $CI->model_institutes->get_user_institutes($user_id);
        $sender_institute_id = $institutes[0]['id'];
    } else {
        $CI->load->model('model_institutes');
        $institutes = $CI->model_institutes->get_user_institutes($user_id);
        $sender_institute_id = $institutes[0]['id'];
    }
    
    return $sender_institute_id;
}

function get_current_user_institute_type($user_id,$user_type) {
    
    $CI = & get_instance();
    
    $CI->load->model('model_students');
    $client_type = get_option(array( 'option' => 'client_type'));
    switch ($client_type) {
        case 'k_12':
                   $institute_type = 'k_12';         
                   
            break;
        case 'higher_education':
                   $institute_type = 'higher_education';     
            break;
        case 'mixed' :
                    if ($user_type == 'student' || $user_type == 'parent') {
                        $student = $CI->model_students->get_student($user_id,'user_id');
                        $institute_id = $student['institute_id'];
                    }
                    else {
                        $user_institutes = get_user_institutes($user_id);
                        if (count($user_institutes) > 1) {
                            $institute_type = 'higher_education';
//                            $label['s_course_class'] = 'Course';         
//                            $label['p_course_class'] = 'Courses'; 
                        } else {
                            $institute_id = $user_institutes[0]['id'];
                        }
                    }
                    if (isset($institute_id) && $institute_id) {
                        $CI->load->model('model_institutes');
                        $institute_details = $CI->model_institutes->get_institute_by_id($institute_id);
                        $institute_type = $institute_details['institute_type'];
                        if ($institute_type == 'k_12'){
                            $institute_type = 'k_12'; 
                        } elseif ($institute_type == 'higher_education') {
                           $institute_type = 'higher_education';  
                        } else {
                            $institute_type = 'higher_education'; 
                        }
                    } else {
                        $institute_type = 'higher_education'; 
                    }
            break;
        default:
                $institute_type = 'higher_education';     
    }
    
    return $institute_type;
    
}

function echo_user_by_institute($institute, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_users');
    $CI->load->model('model_time_table');
  
    
    $administrators = $CI->model_users->get_admin(false,$institute);
    $employees = $CI->model_users->get_employee_by_institute_id($institute);
   
    
    $users = array_merge($administrators,$employees);
//  /  var_dump($users);
    foreach ($users as $row) {
        if ($select == $row['id']) {
            $echo .= "<option selected value='$row[id]'>$row[full_name]." - ".$row[username]</option>";
        } else {
            
            $echo .= "<option  value='$row[id]'>$row[full_name] - $row[username]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function get_user_institute_id($user_id,$user_type) {
    
    $CI = & get_instance();
    
    if ($user_type == 'admin') {
        $CI->load->model('model_users');
        $user_details = $CI->model_users->get_administrator_by_user_id($user_id);
        $sender_institute_id = $user_details['institute_id'];
    } else if ($user_type == 'employee') {
        $CI->load->model('model_employee');
        $user_details = $CI->model_employee->get_employee($user_id, 'user_id');
        $sender_institute_id = $user_details['institute_id'];
    } else if ($user_type == 'student') {
        $CI->load->model('model_students');
        $user_details = $CI->model_students->get_student($user_id, 'user_id');
        $sender_institute_id = $user_details['institute_id'];
    } elseif ($user_type == 'super_admin') {
        $CI->load->model('model_institutes');
        $institutes = $CI->model_institutes->get_user_institutes($user_id);
        $sender_institute_id = $institutes[0]['id'];
    }
//    else if($user_type == 'parent'){
//        
//        $CI->load->model('model_parents');
//        $institute = $CI->model_parents->get_student_by_parent($user_id);
//        
//        $sender_institute_id = $institute['institute_id'];
//    }
    
    
    else {
        $CI->load->model('model_institutes');
        $institutes = $CI->model_institutes->get_user_institutes($user_id);
        
        $sender_institute_id = $institutes[0]['id'];
    }
    
    return $sender_institute_id;
}


function get_user_by_email($email){
    
        $CI = & get_instance();
        
        $CI->load->model('model_users');
        $super_admin = $CI->model_users->get_super_admin_by_email($email);
        
        if ($super_admin) {
            $user_details = $super_admin;
        } else {
            $admin_details = $CI->model_users->get_administrator_by_email($email);

            if ($admin_details) {
                $user_details = $admin_details;
            } else {
                $CI->load->model('model_employee');
                $employee_details = $CI->model_employee->get_employee_by_email($email);
                if ($employee_details) {
                    $user_details = $employee_details;
                } else {
                    $CI->load->model('model_students');
                    $student_details = $CI->model_students->get_student_by_email($email);
                    if ($student_details) {
                        $user_details = $student_details;
                    } else {
                        $CI->load->model('model_parents');
                        $parent_details = $CI->model_parents->get_parent_by_email($email);
                        if ($parent_details) {
                            $user_details = $parent_details;
                        } else {
                            $user_details = '';
                        }
                    }
                }
            }
        
        }
        
        return $user_details;
    
}

function intermediate_login_redirect($user_id,$redirect){
        $CI = & get_instance();
        $CI->session->sess_destroy();
        $CI->load->model('model_users');
        $CI->load->helper('string');
        $update_user['intermediate_login_key'] = random_string('alnum', 30);
        $update_user['login_key_expired'] = date("Y-m-d H:i:s", strtotime('2 minutes'));
        $CI->model_users->update_users($update_user, $user_id);
        $user = $CI->model_users->get_user($user_id);
        $redirect=urlencode($redirect);
        $url = site_url("intermediate_login?user_name=$user[username]&intermediate_key=$user[intermediate_login_key]&redirect=$redirect");
        redirect($url);
    
}