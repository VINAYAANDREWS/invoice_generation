<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function echo_receiver_code($receiver, $institute,$course_category=false, $course = false, $batch = false,
                $batch_group = false, $group = false, $ind_id = false,$total_receivers = false, 
                $adm_year = false, $message_group = false,$cust_numbers = false){
    $CI = & get_instance();
    $CI->load->model('model_institutes');
    $CI->load->model('model_courses');
    $CI->load->model('model_batches');
    $CI->load->model('model_employee');
    $CI->load->model('model_students');
    $CI->load->model('model_users');
    $CI->load->model('model_parents');
    
    $institutes = $CI->model_institutes->get_institute_by_id($institute);
    if($course != 'all_course') {
        $courses = $CI->model_courses->get_course_details($course);
    }
    $batches = $CI->model_batches->get_batch_details_by_id($batch);
    $total_receivers = isset($total_receivers) && $total_receivers ? $total_receivers:0;
    
    ////receiver ->institute
    if($receiver == 'institute') {
        if($adm_year){
            $CI->load->model('model_batch_years');
            $batch_year = $CI->model_batch_years->get_batch_year($adm_year);
            if($batch_year) {
                $admission_year = "-".$batch_year['admission_year'];
                $year = $batch_year['admission_year'];
                $adm_year_id = "-".$adm_year;
            } else {
                $admission_year = '';  
                $year = '';
                $adm_year_id = '';
            }
        } else {
            $admission_year = '';  
            $year = '';
            $adm_year_id = '';
        }
        if($group == 'staff') {
            $code = 'em-inst';
            $employee['institute'] = $institute;
            $employee['status'] = 'active';
            $employees = $CI->model_employee->get_employee_list($employee);
            
            $result['receiver_count'] = count($employees);
            $result['title'] = " Employees : ". $employees[0]['inst_code']." | Total : ".$result['receiver_count'];
            $result['count'] = count($employees)+$total_receivers;
        } else if($group == 'student') {
            $code = 'st-inst';
            $student['institute'] = $institute;
            $student['status'] = 'active';
            $student['admission_year'] = $adm_year;
            $students = $CI->model_students->get_student_list($student);
            
            $result['receiver_count'] = count($students);
            $result['title'] = " Students : ". $students[0]['inst_code']." | Adm Year : ".$year." | Total : ".$result['receiver_count'];
            $result['count'] = count($students)+$total_receivers;
        }else if($group == 'parent') {
            $code = 'pa-inst';
            $parent['institute'] = $institute;
            $parent['status'] = 'active';
            $parent['admission_year'] = $adm_year;
            $parents = $CI->model_parents->get_parents($parent);
            
            $result['receiver_count'] = count($parents);
            $result['title'] = " Parents : ". $parents[0]['inst_code']." | Adm Year : ".$year." | Total : ".$result['receiver_count'];
            $result['count'] = count($parents)+$total_receivers;
        }else if($group == 'admin') {
            $code = 'ad-inst';
            $institute_id = $institute;
            $admins = $CI->model_users->get_admin("",$institute_id);
            
            $result['receiver_count'] = count($admins);
            $result['title'] = " Administrators : ". $admins[0]['inst_code']." | Total : ".$result['receiver_count'];
            $result['count'] = count($admins)+$total_receivers;
        }
        
        $result['code'] = $code.'-'.$institute.$adm_year_id;
        $value = $group.'_'.$institutes['code'].$admission_year;
        $result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
        $result['value'] = $value;
        
        $result_array[0] = $result;
    }
    
    ///receiver -> course
    if($receiver == 'course') {
        if($adm_year){
            $CI->load->model('model_batch_years');
            $batch_year = $CI->model_batch_years->get_batch_year($adm_year);
            if($batch_year) {
                $admission_year = "-".$batch_year['admission_year'];
                $year = $batch_year['admission_year'];
                $adm_year_id = "-".$adm_year;
            } else {
                $admission_year = '';  
                $year = '';
                $adm_year_id = '';
            }
        } else {
            $admission_year = '';  
            $year = '';
            $adm_year_id = '';
        }
        if($group == 'student') {
            $code = 'st-course';
            $student['institute'] = $institute;
            $student['status'] = 'active';
            $student['admission_year'] = $adm_year;
            
            if($course == 'all_course') {
                //get all course with students in this adm year --  
                $students = $CI->model_students->get_course_wise_student_count($student);
                foreach ($students as $stud_course) {
                    $c_result['receiver_count'] = $stud_course['student_count'];
                    $c_result['title'] = " Students : ". $stud_course['institute_code']." | Course Code : ".$stud_course['course_code']." | Adm Year : ".$year." | Total : ".$c_result['receiver_count'];
                    $total_receivers += $stud_course['student_count'];
                    $c_result['count'] = $total_receivers;
                    $c_result['code'] = $code.'-'.$institute.'-'.$course.$adm_year_id;
                    $value = $group.'_'.$stud_course['institute_code'].'_'.$stud_course['course_code'].$admission_year;
                    $c_result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
                    $c_result['value'] = $value;
                   
                    $result_array[] = $c_result;
                }
            } else {
                $student['course'] = $course;
                $students = $CI->model_students->get_student_list($student);
                $result['receiver_count'] = count($students);
                $result['title'] = " Students : ".$institutes['code']." | Course Code : ".$courses['code']." | Adm Year : ".$year." | Total : ".$result['receiver_count'];
                $result['count'] = count($students)+$total_receivers;
                $result['code'] = $code.'-'.$institute.'-'.$course.$adm_year_id;
                $value = $group.'_'.$institutes['code'].'_'.$courses['code'].$admission_year;
                $result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
                $result['value'] = $value;
                $result_array[0] = $result;
            }
            
        } else if($group == 'parent') {
            $code = 'pa-course';
            $parent['institute'] = $institute;
            $parent['status'] = 'active';
            $parent['admission_year'] = $adm_year;
            if($course == 'all_course') {
                
                $parents = $CI->model_parents->get_course_wise_parent_count($parent);
                foreach ($parents as $par_course) {
                    $c_result['receiver_count'] = $par_course['parent_count'];
                    $c_result['title'] = " Parents : ". $par_course['institute_code']." | Course Code : ".$par_course['course_code']." | Adm Year : ".$year." | Total : ".$c_result['receiver_count'];
                    $total_receivers += $par_course['parent_count'];
                    $c_result['count'] = $total_receivers;
                    $c_result['code'] = $code.'-'.$institute.'-'.$course.$adm_year_id;
                    $value = $group.'_'.$par_course['institute_code'].'_'.$par_course['course_code'].$admission_year;
                    $c_result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
                    $c_result['value'] = $value;
                   
                    $result_array[] = $c_result;
                }
                
            } else {
                $parent['course'] = $course;
                $parents = $CI->model_parents->get_parents($parent);
                $result['receiver_count'] = count($parents);
                $result['title'] = " Parents : ". $parents[0]['inst_code']." | Course Code : ".$parents[0]['course_code']." | Adm Year : ".$year." | Total : ".$result['receiver_count'];
                $result['count'] = count($parents)+$total_receivers;
                $result['code'] = $code.'-'.$institute.'-'.$course.$adm_year_id;
                $value = $group.'_'.$institutes['code'].'_'.$courses['code'].$admission_year;
                $result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
                $result['value'] = $value;
                
                $result_array[0] = $result;
            }
        }
    }
    
    if($receiver == 'course_category') {
        $CI->load->model('model_courses');
        $category_info = $CI->model_courses->get_course_category_by_id($course_category);
        if($adm_year){
            $CI->load->model('model_batch_years');
            $batch_year = $CI->model_batch_years->get_batch_year($adm_year);
            if($batch_year) {
                $admission_year = "-".$batch_year['admission_year'];
                $year = $batch_year['admission_year'];
                $adm_year_id = "-".$adm_year;
            } else {
                $admission_year = '';  
                $year = '';
                $adm_year_id = '';
            }
        } else {
            $admission_year = '';  
            $year = '';
            $adm_year_id = '';
        }
        if($group == 'student') {
            $code = 'st-course_category';
            $student['institute'] = $institute;
            $student['status'] = 'active';
            $student['admission_year'] = $adm_year;
            
            if($course_category) {
                $student['course_category'] = $course_category;
                $students = $CI->model_students->get_student_list($student);
                $result['receiver_count'] = count($students);
                $result['title'] = " Students : ".$institutes['code']." | Course Category : ".$category_info['category_name']." | Adm Year : ".$year." | Total : ".$result['receiver_count'];
                $result['count'] = count($students)+$total_receivers;
                $result['code'] = $code.'-'.$institute.'-'.$course_category.$adm_year_id;
                $value = $group.'_'.$institutes['code'].'_'.$category_info['category_name'].$admission_year;
                $result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
                $result['value'] = $value;
                $result_array[0] = $result;
            }
            
        } else if($group == 'parent') {
            $code = 'pa-course_category';
            $parent['institute'] = $institute;
            $parent['status'] = 'active';
            $parent['admission_year'] = $adm_year;
            if($course_category) {
                $parent['course_category'] = $course_category;
                $parents = $CI->model_parents->get_parents($parent);
                $result['receiver_count'] = count($parents);
                $result['title'] = " Parents : ". $institutes['code']." | Course Category : ".$category_info['category_name']." | Adm Year : ".$year." | Total : ".$result['receiver_count'];
                $result['count'] = count($parents)+$total_receivers;
                $result['code'] = $code.'-'.$institute.'-'.$course_category.$adm_year_id;
                $value = $group.'_'.$institutes['code'].'_'.$category_info['category_name'].$admission_year;
                $result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
                $result['value'] = $value;
                
                $result_array[0] = $result;
            }
        }
    }
    
    if($receiver == 'batch') {
        if($adm_year){
            $CI->load->model('model_batch_years');
            $batch_year = $CI->model_batch_years->get_batch_year($adm_year);
            if($batch_year) {
                $admission_year = "-".$batch_year['admission_year'];
                $year = $batch_year['admission_year'];
                $adm_year_id = "-".$adm_year;
            } else {
                $admission_year = '';  
                $year = '';
                $adm_year_id = '';
            }
        } else {
            $admission_year = '';  
            $year = '';
            $adm_year_id = '';
        }
        
        $b_group = "";
        if(isset($batch_group) && $batch_group) {
            $CI->load->model('model_batch_group');
            $batch_group_info = $CI->model_batch_group->get_batch_group_by_id($batch_group);
            if($batch_group_info['group_code']) {
                $group_code = $batch_group_info['group_code'];
                $b_group = "Batch Group Code : ".$group_code." | ";
                $group_id = "-".$batch_group_info['id'];
            } else {
                $b_group = "Batch Group : ".$batch_group_info['group_name']." | ";
                $group_id = "-".$batch_group_info['id'];
            }
        } else {
            $group_id = "";
        }
        
        if($group == 'student') {
            $code = 'st-batch';
            $student['institute'] = $institute;
            $student['status'] = 'active';
            $student['batch'] = $batch;
            $student['batch_group'] = $batch_group;
            $student['admission_year'] = $adm_year;
            if(isset($batch_group) && $batch_group) {
                $students = $CI->model_students->get_student_by_batch_group($student);
            } else {
                $students = $CI->model_students->get_student_list($student);
            }
            $result['receiver_count'] = count($students);
            $result['title'] = " Students : ". $students[0]['inst_code']." | Course Code : ".$students[0]['course_code']." | Batch Code : ".$students[0]['batch_code']." | ".$b_group."Adm Year : ".$year." | Total : ".$result['receiver_count'];
            $result['count'] = count($students)+$total_receivers;
        }else if($group == 'parent') {
            $code = 'pa-batch';
            $parent['institute'] = $institute;
            $parent['batch'] = $batch;
            $parent['batch_group'] = $batch_group;
            $parent['status'] = 'active';
            $parent['admission_year'] = $adm_year;
            if(isset($batch_group) && $batch_group) {
                $parents = $CI->model_parents->get_parents_by_batch_group($parent);
            } else {
                $parents = $CI->model_parents->get_parents($parent);
            }
            $result['receiver_count'] = count($parents);
            $result['title'] = " Parents : ". $parents[0]['inst_code']." | Course Code : ".$parents[0]['course_code']." | Batch Code : ".$parents[0]['batch_code']." | ".$b_group."Adm Year : ".$year." | Total : ".$result['receiver_count'];
            $result['count'] = count($parents)+$total_receivers;
        }
        
        $result['code'] = $code.'-'.$batch.$adm_year_id.$group_id;
        $batch_group_code = isset($batch_group) && $batch_group?"_".$batch_group_info['group_code']:"";
        $value = $group.'_'.$institutes['code'].'_'.$courses['code'].'_'.$batches['batch_code'].$batch_group_code.$admission_year;
        $result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
        $result['value'] = $value;
        
        $result_array[0] = $result;
    }
    
    if($receiver == 'ind_staff' || $receiver == 'ind_student' || $receiver == 'ind_parent' || $receiver == 'ind_admin') {
        
            switch ($receiver) {
                case 'ind_staff':
                    $employee_info = $CI->model_employee->get_employee($ind_id,'employee_number');
                    $result['title'] = " Institute : ". $employee_info['inst_code']." | Employee : ".$employee_info['full_name'];
                    break;
                case 'ind_student':
                    $student_info = $CI->model_students->get_student($ind_id,'adm_no',$institute);
                    $result['title'] = " Institute : ". $student_info['inst_code']." | Student : ".$student_info['full_name'];
                    break;
                case 'ind_parent':
                    $parent_info = $CI->model_students->get_student($ind_id,'adm_no',$institute);
                    $result['title'] = " Institute : ". $parent_info['inst_code']." | Parent of ".$parent_info['full_name'];
                    break;
                case 'ind_admin':
                    $user_info = $CI->model_users->get_user($ind_id,'user_name');
                    $admin_info = $CI->model_users->get_administrator_by_user_id($user_info['id']);
                    $result['title'] = " Institute : ". $admin_info['inst_code']." | Admin : ".$admin_info['full_name'];
                    break;
            
        }
        
        $result['code'] = $receiver.'-'.$ind_id;
        $value = $receiver.'_'.$ind_id;
        $result['receiver_count'] = 1;
        $result['count'] = $total_receivers+1;
        $result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
        $result['value'] = $value;
        
        $result_array[0] = $result;
    }
    
    /// receiver -> message group
    if ($receiver == 'message_group') {
        
        $CI->load->model('model_message_group');
        $message_group_members = $CI->model_message_group->get_message_group_members($institute,$message_group);
        $result['receiver_count'] = count($message_group_members);
        $result['count'] = count($message_group_members) + $total_receivers;
        $result['title'] = " Message Group : ". $message_group_members[0]['inst_code']." | ".$message_group_members[0]['group_name'];
        $result['code'] = $receiver.'-'.$message_group;
        $value = $receiver.'_'.$message_group;
        $result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
        $result['value'] = $value;
        
        $result_array[0] = $result;
    }
    
    /// receiver -> custom number
    if ($receiver == 'custom_number') {
        
      $cust_phone_numbers = preg_split("/[\r\n,]+/",$cust_numbers,-1,PREG_SPLIT_NO_EMPTY);
      
      $result['receiver_count'] = count($cust_phone_numbers);
      $result['count'] = count($cust_phone_numbers) + $total_receivers;
      $numbers = implode(", ", $cust_phone_numbers);
      $result['title'] = "Custom Numbers:".$numbers;
      $result['code'] = $receiver.'-'.$cust_numbers;
      $value = $receiver.'-'.$numbers;
      $result['class'] = remove_special_characters(str_replace(",", '', str_replace(" ", '', $value)));
      $result['value'] = $value;
      
      $result_array[0] = $result;
    }
    

    return $result_array;
}

function echo_notification(){
    $CI = & get_instance();
    $CI->load->model('model_message');
    $user_type = $CI->session->userdata('user_type');
        if($user_type == 'employee'){
            $id = $CI->session->userdata('employee_id');
        } elseif ($user_type == 'student') {
            $id = $CI->session->userdata('student_id');
        } elseif ($user_type == 'parent') {
            $id = $CI->session->userdata('parent_id');
        } elseif ($user_type == 'admin') {
            $id = $CI->session->userdata('admin_id');
        }
    if(isset($id) && $id && $user_type){
    $msg_count = $CI->model_message->get_unread_messages_count($id,$user_type);
    $result = '';
    
        $result .= '
                        
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-envelope"></i>
                                        <span class="label label-lightred">';
         if($msg_count == 0){
            $result .= '';
        } else {
            $result .= $msg_count;
        }
        $result .= '</span>
                                </a>
                                <ul class="dropdown-menu pull-right message-ul">';
        if($msg_count != 0){
            $get_messages = $CI->model_message->get_unread_messages($id,$user_type,5,0);
            foreach ($get_messages as $message) {
                $result .= ''
                        . '<li class="notification_inbox" rel="'.$message['message_id'].'">';
                if($user_type != 'student') {
                    $result .= '<a href="'.site_url("messages/message/view_messages#$message[message_id]").'">';
                } else {
                    $result .= '<a href="'.site_url("student/student_profile/view_messages#$message[message_id]").'">';   
                }       
                $result .= ' <div class="details">';
                if ($user_type != 'student' && $user_type != 'parent') {
                    $result .= '<div class="name">';
                    $name = user_name_by_user_id($message['sender_user_id']);
                    $result .=  $name;  
                    $result .= '</div>';
                }
                $result .= '<div class="message">
                                                        '.$message["body"].'
                                                </div>
                                        </div>
                                </a>
                            </li>
                            ';
            }
        } else {
             $result .= ' <li>
                                <a href="#">
                                        
                                        No notifications
                                </a>
                            </li>
                            ';
        }
        if (current_user_can('view_messages') == TRUE){ 
        $result .= '<li>
                        <a href="'.site_url("messages/message/view_messages").'" class="more-messages">Go to Inbox
                                <i class="fa fa-arrow-right"></i>
                        </a>
                    </li>';
                     }
         $result .= '</ul>
                        
                    ';
       
    
    echo $result;
    }
    
}

function echo_template_names($institute, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_message_template');
    $message_template = $CI->model_message_template->get_message_template($institute);
 
    foreach ($message_template as $msg_temp) {
        if($msg_temp['template_id']){
        if ($msg_temp['id']== $select) {
            $echo.= "<option selected value='$msg_temp[id]'>$msg_temp[template_name]</option>";
        } else {
            $echo.= "<option value='$msg_temp[id]'>$msg_temp[template_name]</option>";
        }
    } 
    }
    if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
}

function echo_message_groups($institute,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_message_group');
    $message_groups = $CI->model_message_group->get_message_groups($institute);
    foreach ($message_groups as $group) {
        if ($select == $group['id']) {
            $echo .= "<option selected value='$group[id]'>$group[group_name]</option>";
        } else {
            $echo .= "<option value='$group[id]'>$group[group_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_institute_courses($institute = false, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_courses');
    $courses = $CI->model_courses->get_courses_by_institute_id($institute);
    $echo .= '<option value="all_course">All Course</option>';
    foreach ($courses as $course) {
        if ($select == $course['id']) {
            $echo .= "<option selected value='$course[id]'>$course[course_name]</option>";
        } else {
            $echo .= "<option value='$course[id]'>$course[course_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function get_message($institute_id,$receiver_id,$receiver_type,$message,$message_var = false) {
    $CI = & get_instance();
    // check if message contain codes
    $search_codes = array("{{INSTITUTENAME}}","{{COURSENAME}}","{{BATCHNAME}}",
                    "{{NAME}}","{{ADMNO}}","{{USERNAME}}","{{STUDENTNAME}}","{{STUDENTUSERNAME}}",
                    "{{ADMYEAR}}");
    
    $check_search_words_in_msg= get_string_contain_words_in_array($message,$search_codes);
    $msg_content = "";
    if(isset($receiver_type) && $receiver_type && isset($receiver_id) && $receiver_id ) {
          
        if($receiver_type == 'student') {
            $CI->load->model('model_students');
            $member_details = $CI->model_students->get_student_information($receiver_id,'id',"",$institute_id);
            $adm_no = $member_details['admission_no'];
            $course_name = $member_details['course_name'];
            $batch_name = $member_details['batch_name'];
            $username = $member_details['username'];
            $name = $member_details['full_name'];
            $student_name = $member_details['full_name'];
            $student_username = $member_details['username'];
            $adm_year = $member_details['admission_year'];
        } else if($receiver_type == 'employee') {
            $CI->load->model('model_employee');
            $member_details = $CI->model_employee->get_employee($receiver_id);
            $username = $member_details['username'];
            $name = $member_details['full_name'];
        } else if($receiver_type == 'parent') {
            $CI->load->model('model_parents');
            $CI->load->model('model_students');
            $student_info = $CI->model_students->get_student_information($receiver_id,'id',"",$institute_id);
            $member_details = $CI->model_parents->get_parent($student_info['parent_id'],'id');
            $username = $member_details['username'];
            $name = isset($student_info['parent_name']) && $student_info['parent_name'] ? $student_info['parent_name'] : $student_info['father_name'];
            $adm_no = $student_info['admission_no'];
            $course_name = $student_info['course_name'];
            $batch_name = $student_info['batch_name'];
            $student_name = $student_info['full_name'];
            $student_username = $student_info['username'];
            $adm_year = $student_info['admission_year'];
        } else if($receiver_type == 'admin') {
            $CI->load->model('model_users');
            $member_details = $CI->model_users->get_administrator_by_user_id($receiver_id,'id');
            $username = $member_details['username'];
            $name = $member_details['full_name'];
        }  else if($receiver_type == 'message_group') {
            $CI->load->model('model_message_group');
            $member_details = $CI->model_message_group->get_message_group_member_by_id($receiver_id);
            $name = $member_details['member_name'];
        }  
        
     $flag = TRUE;  
  
    if(in_array("{{INSTITUTENAME}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($member_details['institute_name']) && $member_details['institute_name']) {
            $message = str_replace('{{INSTITUTENAME}}',$member_details['institute_name'], $message);
          } else {
              $flag = FALSE;
          }
      }
     if(in_array("{{NAME}}", $check_search_words_in_msg) && $flag == TRUE){
         if(isset($name) && $name) {
            $message = str_replace('{{NAME}}',$name, $message);
          } else {
              $flag = FALSE;
          }
      }
      if(in_array("{{COURSENAME}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($course_name) && $course_name) {
            $message = str_replace('{{COURSENAME}}',$course_name, $message);
          } else {
              $flag = FALSE;
          }
      }
      if(in_array("{{BATCHNAME}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($batch_name) && $batch_name) {
            $message = str_replace('{{BATCHNAME}}',$batch_name, $message);
          } else {
              $flag = FALSE;
          }
      }
      if(in_array("{{ADMNO}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($adm_no) && $adm_no) {
            $message = str_replace('{{ADMNO}}',$adm_no, $message);
          } else {
              $flag = FALSE;
          }
      }
      if(in_array("{{USERNAME}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($username) && $username) {
            $message = str_replace('{{USERNAME}}',$username, $message);
          } else {
              $flag = FALSE;
          }
      }
      if(in_array("{{STUDENTNAME}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($student_name) && $student_name) {
            $message = str_replace('{{STUDENTNAME}}',$student_name, $message);
          } else {
              $flag = FALSE;
          }
      }
      if(in_array("{{STUDENTUSERNAME}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($student_username) && $student_username) {
            $message = str_replace('{{STUDENTUSERNAME}}',$student_username, $message);
          } else {
              $flag = FALSE;
          }
      }
      if(in_array("{{ADMYEAR}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($adm_year) && $adm_year) {
            $message = str_replace('{{ADMYEAR}}',$adm_year, $message);
          } else {
              $flag = FALSE;
          }
      }
            
          
      }
      
      $msg_content = isset($message) && $message && isset($flag) && $flag == TRUE?$message:"";
      return $msg_content;  
}


function dynamic_message_content($message,$message_var) {
    
    $CI = & get_instance();
    // check if message contain codes
    $search_codes = array("{{var1}}","{{var2}}","{{var3}}","{{var4}}");
    
    $check_search_words_in_msg= get_string_contain_words_in_array($message,$search_codes);
    $msg_content = "";
    $flag = TRUE;
    if(in_array("{{var1}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($message_var['var1']) && $message_var['var1']) {
            $message = str_replace('{{var1}}',$message_var['var1'], $message);
          } else {
              $flag = FALSE;
          }
      }
      
      if(in_array("{{var2}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($message_var['var2']) && $message_var['var2']) {
            $message = str_replace('{{var2}}',$message_var['var2'], $message);
          } else {
              $flag = FALSE;
          }
      }
      
      if(in_array("{{var3}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($message_var['var3']) && $message_var['var3']) {
            $message = str_replace('{{var3}}',$message_var['var3'], $message);
          } else {
              $flag = FALSE;
          }
      }
      
      if(in_array("{{var4}}", $check_search_words_in_msg) && $flag == TRUE){
          if(isset($message_var['var4']) && $message_var['var4']) {
            $message = str_replace('{{var4}}',$message_var['var4'], $message);
          } else {
              $flag = FALSE;
          }
      }
      
      $msg_content = isset($message) && $message && isset($flag) && $flag == TRUE?$message:"";
      return $msg_content; 
}