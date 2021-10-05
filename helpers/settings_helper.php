<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_user_courses($user_id){
    $CI = & get_instance();
    $CI->load->model('model_courses');
    $courses = $CI->model_courses->get_user_courses($user_id);
    return $courses; 
}

function check_institute_access($institute){

    $CI = & get_instance();
    $CI->load->model('model_institutes');
    $user_id = $CI->session->userdata('user_id');
    $institutes = $CI->model_institutes->get_user_institutes($user_id,$institute);

    if($institutes){
        return TRUE;
    } else {
        return FALSE;
    }
}

function echo_institutes($view = false,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_institutes');
    if ($view == 'assigned'){
        $user_id = $CI->session->userdata('user_id');
        $institutes = $CI->model_institutes->get_user_institutes($user_id);
    } else {
        $institutes = $CI->model_institutes->get_institutes();
    }
      
    foreach ($institutes as $institute) {
        if ($select == $institute['id']) {
            $echo .= "<option selected value='$institute[id]'>$institute[name]</option>";
        } else {
            $echo .= "<option value='$institute[id]'>$institute[name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

/**
 * 
 *  @param array(
 *              'institutes' => //user_institutes,
 *              'user_id'    => //user_id,
 *              'select_institute'   => //institute to select
 *              );
 * echo_user_institutes(array('institutes' => $institutes,'select_institute' => $institute));
 * 
 */
function echo_user_institutes($data_array){
    
    
    if (isset($data_array['institutes']) && $data_array['institutes']) {
        $institutes = $data_array['institutes'];
    } else {
        if (isset($data_array['user_id']) && $data_array['user_id']){
            $user_id = $data_array['user_id'];
        } else{
            $user_id = get_logged_userid();
        } 
        $institutes = get_user_institutes($user_id);
    }
    
    if (isset($data_array['select_institute']) && $data_array['select_institute']) {
        $select = $data_array['select_institute'];
    } else {
        $select = get_logged_user_institute_id();
    }
    
    $echo = '';
    
    foreach ($institutes as $inst) {
        if ($select == $inst['id']) {
            $echo .= "<option selected value='$inst[id]'>$inst[name]</option>";
        } else {
            $echo .= "<option value='$inst[id]'>$inst[name]</option>";
        }
    }
    echo $echo;
}

function echo_courses($institute = false, $select = false, $print = true,$course_category=false) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_courses');
    if($course_category){
     $courses = $CI->model_courses->get_courses_by_institute_id($institute,$course_category);   
    }else{
    $courses = $CI->model_courses->get_courses_by_institute_id($institute);
    }
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


function echo_active_batches($institute, $course, $select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_batches');
    $CI->load->model('model_institutes');
    
    $institute_details = $CI->model_institutes->get_institute_by_id($institute);
    
    $batches = $CI->model_batches->get_batches($institute,$course,'active');
    
    if ($institute_details['adm_year_in_batch_name']) { //batch name with adm year
        foreach ($batches as $batch) {
            $batch_name = $batch['admission_year']." - ".$batch['batch_name'];
            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch_name</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch_name</option>";
            }
        }
    } else {
        foreach ($batches as $batch) {
            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch[batch_name]</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch[batch_name]</option>";
            }
        }
    }
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_batches($institute = false,$course = false, $select = false, $print = true) {

    $echo = '';
    $CI = & get_instance();
    $CI->load->model('model_batches');
    $CI->load->model('model_institutes');
    
    $institute_details = $CI->model_institutes->get_institute_by_id($institute);
    
    $batches = $CI->model_batches->get_batches($institute,$course);
    
    if ($institute_details['adm_year_in_batch_name']) { //batch name with adm year
        foreach ($batches as $batch) {
            $batch_name = $batch['batch_name']." - ".$batch['admission_year'];
            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch_name</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch_name</option>";
            }
        }
    } else {
        foreach ($batches as $batch) {
            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch[batch_name]</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch[batch_name]</option>";
            }
        }
    }
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function get_institute_logo($institute_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_institutes');
    $institute_details = $CI->model_institutes->get_institute_by_id($institute_id);
    if ($institute_details['grayscale_logo'] && file_exists("./public/uploads/logos/grayscale_logos/$institute_details[grayscale_logo]")) {
        
        $logo = "uploads/logos/grayscale_logos/$institute_details[grayscale_logo]";
        
    } elseif ($institute_details['color_logo'] && file_exists("./public/uploads/logos/color_logos/$institute_details[color_logo]")) {
        
        $logo = "uploads/logos/color_logos/$institute_details[color_logo]";
        
    } else {
        
        $white_label = get_option(array('option' => 'white_label'));
        $wl_details = json_decode($white_label,TRUE);
        
        if (isset($wl_details['white_label']) && $wl_details['white_label'] == "TRUE" && 
                    isset($wl_details['logo']) && $wl_details['logo'] && file_exists("./public/uploads/img/$wl_details[logo]")){
                
            $logo = "uploads/img/$wl_details[logo]";
                
        } else {
            $logo = "img/logo.png";
        }
        
    }
    return $logo;    
}

function get_backgroud_image() {
    
        $CI = & get_instance(); 
        $background_imgage = $CI->session->userdata('background_image');
    
    return $background_imgage;    

}

function get_login_logo() {
    
    $CI = & get_instance();
    $login_log = $CI->session->userdata('logo');
    return $login_log;    

}

function check_mark_or_grade($batch,$exam_group=false){
        
    $CI = & get_instance();
    $CI->load->model('model_exam');
   
    $result = $CI->model_exam->check_mark_or_grade($batch,$exam_group);
    
    if($result['exam_group_result']){
        $data = $result['exam_group_result'];
    }
    else if($result['batch_result']){
        
        $data = $result['batch_result'];
    }
    else if($result['course_result']){
        
        $data = $result['course_result'];
    }
    else if ($result['institute_result']) {
        
         $data = $result['institute_result'];
    }
    else{
        $data = "normal";
    }
    if($data){
        return $data;
    } 
}

function echo_course_categories($institute = false, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_courses');
    $course_categories = $CI->model_courses->get_course_categories_by_institute_id($institute);
    foreach ($course_categories as $category) {
        if ($select == $category['id']) {
            $echo .= "<option selected value='$category[id]'>$category[category_name]</option>";
        } else {
            $echo .= "<option value='$category[id]'>$category[category_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_batches_by_year_old($institute,$year=false, $course=false, $select = false, $all_batches = FALSE,$print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_batches');
    $show_adm_year_in_batch = show_adm_year_in_batch_name($institute);
    if ($all_batches == TRUE) {
        $batches = $CI->model_batches->get_batch_by_admission_year($institute,$year,$course);
    } else {
        $batches = $CI->model_batches->get_batch_by_admission_year($institute,$year,$course,'active');
    }
    
    if ($course == FALSE) {
        $last_loaded_group='';
        $count ="";
        foreach ($batches as $batch) {
            $batch_name = $show_adm_year_in_batch == TRUE ? $batch['admission_year']."-".$batch['batch_name'] : $batch['batch_name'];
            if ($last_loaded_group != $batch['course_id']){
//                $group_count++;
                  if($count != 0){ 
                      $echo .= "</optgroup>";
                  }
                $echo .= "<optgroup label='$batch[course_name]'>";  
            }

            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch_name</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch_name</option>";
            }

            $last_loaded_group=$batch['course_id'];
            $count++;
        }

        $echo .= "</optgroup>";
    
    } else {
        foreach ($batches as $batch) {
            $batch_name = $show_adm_year_in_batch == TRUE ? $batch['admission_year']."-".$batch['batch_name'] : $batch['batch_name'];
                $batch['admission_year']."-".$batch['batch_name'];
            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch_name</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch_name</option>";
            }
        }
    }
       
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
function echo_batches_by_year($institute,$year=false, $course=false, $select = false, $all_batches = FALSE,$print = true,$category=FALSE) {
    
   
     $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_batches');
    $show_adm_year_in_batch = show_adm_year_in_batch_name($institute);
     if($category){
            if ($all_batches == TRUE) {
                         $batches = $CI->model_batches->get_batch_by_course_category($institute,$year,$course,'',$category);
                } else {
                    $batches = $CI->model_batches->get_batch_by_course_category($institute,$year,$course,'active',$category);
                }
    }else{

         if ($all_batches == TRUE) {
            $batches = $CI->model_batches->get_batch_by_admission_year($institute,$year,$course);
        } else {
            $batches = $CI->model_batches->get_batch_by_admission_year($institute,$year,$course,'active');
        }
    }
    
    if ($course == FALSE) {
        $last_loaded_group='';
        $count ="";
        foreach ($batches as $batch) {
            $batch_name = $show_adm_year_in_batch == TRUE ? $batch['admission_year']."-".$batch['batch_name'] : $batch['batch_name'];
            if ($last_loaded_group != $batch['course_id']){
//                $group_count++;
                  if($count != 0){ 
                      $echo .= "</optgroup>";
                  }
                $echo .= "<optgroup label='$batch[course_name]'>";  
            }

            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch_name</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch_name</option>";
            }

            $last_loaded_group=$batch['course_id'];
            $count++;
        }

        $echo .= "</optgroup>";
    
    } else {

        foreach ($batches as $batch) {
            $batch_name = $show_adm_year_in_batch == TRUE ? $batch['admission_year']."-".$batch['batch_name'] : $batch['batch_name'];
                $batch['admission_year']."-".$batch['batch_name'];
            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch_name</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch_name</option>";
            }
        }
    }

    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
   
}

function echo_date_options($select = FALSE,$print = TRUE) {
    
    $date_option = config_item('date_option');
    
    $echo = "";
    
    foreach ($date_option as $key=>$value) {
        
        if ($select == $key) {
            $echo .= "<option selected value='$key'>$value</option>";
        } else {
            $echo .= "<option value='$key'>$value</option>";
        }
        
    }
    
    if ($print == TRUE) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_admission_years($institute_id,$select_year = false,$current = FALSE) {

      $CI = & get_instance();  
      
      $CI->load->model('model_batch_years');
      $admission_years = $CI->model_batch_years->get_batch_years($institute_id);
      
      $echo = '';
//      foreach ($admission_years as $adm_year) {
//          $echo .= '<option value="'.$adm_year['id'].'"'.($adm_year['id'] == $select_year ? ' selected="selected"' : '').'>'.$adm_year['admission_year'].'</option>';
//      }
      
      foreach ($admission_years as $adm_year) {
          if ($select_year && $select_year == $adm_year['id']) {
              $echo .= '<option value="'.$adm_year['id'].'" selected>'.$adm_year['admission_year'].'</option>';
          } else if ($current && $adm_year['current'] == 1){
              $echo .= '<option value="'.$adm_year['id'].'" selected>'.$adm_year['admission_year'].'</option>';
          } else {
              $echo .= '<option value="'.$adm_year['id'].'">'.$adm_year['admission_year'].'</option>';
          }
          
      }
      echo $echo;
}

function show_adm_year_in_batch_name($institute_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_institutes');
    $institute_details = $CI->model_institutes->get_institute_by_id($institute_id);
    if ($institute_details['adm_year_in_batch_name']) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function echo_course_by_category($category,$select = false, $print = true) {
    $CI = & get_instance();
    $CI->load->model('model_courses');
    $courses = $CI->model_courses->get_course_by_category($category);
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


function echo_university($select = false, $print = true) {
    $CI = & get_instance();
    $CI->load->model('model_courses');
    $universities = $CI->model_courses->get_university();
    foreach ($universities as $university) {
        if ($select == $university['id']) {
            $echo .= "<option selected value='$university[id]'>$university[university_board]</option>";
        } else {
            $echo .= "<option value='$university[id]'>$university[university_board]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_course_by_institute_category_university($institute,$category=false,$university=false,$select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_courses');
    $catgry = (isset($category) && $category)?$category:"";
    $univ = (isset($university) && $university)?$university:"";
    $courses = $CI->model_courses->get_course_list($institute,$catgry,$univ);
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



function echo_user_courses($institute, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_courses');
    $CI->load->model('model_department');
    $CI->load->model('model_batches');
    $CI->load->model('model_employee');
  
    if (current_user_can("all_course_permission")==TRUE) {
        $courses = $CI->model_courses->get_courses_by_institute_id($institute);
    }
    else{
    $employee_id = $CI->session->userdata('employee_id');
    $check_hod= $CI->model_department->check_hod_by_employee_id($employee_id);
   
    $check_tutor = $CI->model_batches->get_tutor_by_employee_id($institute,$employee_id);
    
    if(isset($check_hod) && $check_hod){
       
        $hod_courses = $CI->model_courses->get_hod_courses($employee_id);
        $courses= $hod_courses;   
        
        
    }
    else if (isset($check_tutor) && $check_tutor){
        $tutor_courses = $CI->model_courses->get_tutor_courses($employee_id);
       
       $courses = $tutor_courses;
    }
    else{
         
        $courses = $CI->model_employee->get_employee_courses_by_institute_employee($institute,$employee_id);
    
        
    }
    
//    var_dump($courses);
    
    }   

    
    foreach ($courses as $course) {
        
        
        
        if ($select == $course['course_id']) {
            $echo .= "<option selected value='$course[course_id]'>$course[course_name]</option>";
        } else {
            $echo .= "<option value='$course[course_id]'>$course[course_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_user_batch($institute,$course=false,$year=false,$select=false,$print=true){
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_courses');
    $CI->load->model('model_department');
    $CI->load->model('model_batches');
    $CI->load->model('model_employee');
    if (current_user_can("all_course_permission")==TRUE) {
        $batches = $CI->model_batches->get_batches($institute,$course,'active',false,$year);
    }
    else{
    $employee_id = $CI->session->userdata('employee_id');
    $check_hod= $CI->model_department->check_hod_by_employee_id($employee_id,$course);
//    var_dump($check_hod);
    $check_tutor = $CI->model_batches->get_tutor_by_employee_id($institute,$employee_id,$course);
   
    if(isset ($check_hod) && $check_hod){
       
       $batches = $CI->model_batches->get_hod_batches($employee_id,$course,false,$year);
       
       
    }
    else if (isset($check_tutor) && $check_tutor){
       $batches = $CI->model_batches->get_tutor_batches($employee_id,$course,false,$year);
//       $batches = $check_tutor;
    }
    
    else{
       $batches = $CI->model_employee->get_faculty_current_batches($employee_id,$course,false,$year);
    
   }
    
   }   
   
if(isset($batches)){
    foreach ($batches as $batch) {
        
        if ($select == $batch['batch_id']) {
            $echo .= "<option selected value='$batch[batch_id]'>$batch[batch_name]</option>";
        } else {
            $echo .= "<option value='$batch[batch_id]'>$batch[batch_name]</option>";
        }
    }
}
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_academic_user_type(){
    
    $academic_user_type = "";
    $CI = & get_instance();
    $CI->load->model('model_department');
    $CI->load->model('model_batches');
    $CI->load->model('model_employee');
    $employee_id = $CI->session->userdata('employee_id');
    $user = $CI->session->userdata('user_id');
    $user_type = $CI->session->userdata('user_type');
    
    if($user_type =="employee"){
    
    //check_hod
    $hod = $CI->model_department->check_hod_by_employee_id($employee_id);
    
    //check_tutor
    $tutor = $CI->model_batches->check_batch_tutor_isset($employee_id);
   
    $faculty = $CI->model_employee->get_faculty_current_batches($employee_id);
    
    if(isset($hod) && $hod){
        $academic_user_type = "hod";
    }else if(isset ($tutor) && $tutor){
        $academic_user_type = "tutor";
    }else if(isset ($faculty) && $faculty){
        $academic_user_type = "faculty";
    }
    }
    elseif ($user_type=="admin"||$user_type=="super_admin") {
        $academic_user_type = "admin";
    }
    return $academic_user_type;
    
}

function  echo_report_card_template($institute,$select=false,$print=true){
   
     $echo = '';
        $CI = & get_instance();
         $CI->load->model('model_internals');
         $templates = $CI->model_internals->get_report_card_templates($institute);
         if(isset($templates) && $templates){
            foreach($templates as $template){
                
            if (isset($select) && $select == $template['template_number']) {
                $echo .= "<option selected value='$template[template_number]'>$template[template_name]</option>";
            } else {
                $echo .= "<option value='$template[template_number]'>$template[template_name]</option>";
            }
                
            }
                
             
         }
         
         if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
}

function echo_child_batch($institute,$batch,$sem,$select=false,$print=true){
    $echo = '';
    $CI = & get_instance();
    $CI->load->model('model_child_batches'); 
    $child_batches = $CI->model_child_batches->get_child_batches_by_batch_id($institute,false,false,$batch,$sem);

    if(isset($child_batches) && $child_batches){
        echo "<option value=''>Select One</option>";
        foreach($child_batches as $child_batch){
            
            if ($child_batch['child_batch_id']==$select) {
                $echo .= "<option selected value='$child_batch[child_batch_id]'>$child_batch[child_batch_name]</option>";
            }
            else {
                $echo .= "<option value='$child_batch[child_batch_id]'>$child_batch[child_batch_name]</option>";
            }
        }
    }
    
    if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
}

function get_institute_type($institute_id){
    $CI = & get_instance();
   $CI->load->model('model_institutes');
    $institute_details = $CI->model_institutes->get_institute_type($institute_id);
    $institute_type = $institute_details['institute_type'];
    if ($institute_type == "higher_education") {
        $institute_type="higher_education";
    }else if($institute_type=="k_12"){
        $institute_type="k_12";
    }else{
        $client_type = get_option(array( 'option' => 'client_type'));
        $institute_type=$client_type;
    }
    return $institute_type; 
}

function get_current_batch_year($institute_id) {
    
    $CI = & get_instance();  
      
      $CI->load->model('model_batch_years');
      $admission_year = $CI->model_batch_years->get_current_batch_year($institute_id);
    
      return $admission_year['id'];
    
}

function check_date_lock($batch,$sem){
    
    $CI = & get_instance();
    
    $CI->load->model('model_batches');
    $date_range = $CI->model_batches->check_batch_date_range($batch,$sem);
    $date= array();
    if($date_range){
        
        if($date_range['sem_start_date'] && $date_range['sem_start_date']!="0000-00-00"){
            
            $date['start_date'] = $date_range['sem_start_date'];
            
        }else if($date_range['start_date'] && $date_range['start_date']!="0000-00-00"){
            
            $date['start_date'] = $date_range['start_date'];
        }else{
            $date['start_date'] ="";
        }
        
        if($date_range['sem_end_date'] && $date_range['sem_end_date']!="0000-00-00"){
            
            $date['end_date'] = $date_range['sem_end_date'];
            
        }else if($date_range['end_date'] && $date_range['end_date']!="0000-00-00"){
            
            $date['end_date'] = $date_range['end_date'];
        }else{
            $date['end_date'] ="";
        }
        
    }
    return $date;
}
function echo_event_categories($institute_id,$select_category= false) {

      $CI = & get_instance();  
      
      $CI->load->model('model_academic_calendar');
      $categories=  $CI ->model_academic_calendar->get_event_category_by_institute_id($institute_id);
      
      $echo = '';
      foreach ($categories as $category) {
          if ($select_category && $select_category == $category['id']) {
              $echo .= '<option value="'.$category['id'].'" selected>'.$category['category_name'].'</option>';
          } else {
              $echo .= '<option value="'.$category['id'].'">'.$category['category_name'].'</option>';
          }
          
      }
      echo $echo;
}

function get_institute_course_class_label($institute_id) {
    
    $CI = & get_instance(); 
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
    
    return $label;
}
function echo_course_specialization($batch= false,$select=false) {
$filter=array();
      $CI = & get_instance(); 
      if($batch){
            $filter['batch']=$batch;
      }
      $CI->load->model('model_course_specialization');
      $specializations=  $CI ->model_course_specialization->get_course_specialization($filter);
      
      $echo = '';
      foreach ($specializations as $spec) {
          if ($select && ($spec['id']==$select)) {
              $echo .= '<option value="'.$spec['id'].'" selected>'.$spec['specialization'].'</option>';
          } else {
              $echo .= '<option value="'.$spec['id'].'">'.$spec['specialization'].'</option>';
          }
          
      }
      echo $echo;
}

function echo_batch_group($batch_id,$select=false) {
      $CI = & get_instance();  
      $CI->load->model('model_batch_group');
      $groups=  $CI->model_batch_group->get_batch_group_by_batch_id($batch_id);
      
      $echo = '';
      foreach ($groups as $group) {
          if ($select && $select == $group['id']) {
              $echo .= '<option value="'.$group['id'].'" selected>'.$group['group_name'].'</option>';
          } else {
              $echo .= '<option value="'.$group['id'].'">'.$group['group_name'].'</option>';
          }
          
      }
      echo $echo;
}

function echo_institute_university_boards($institute = false, $select = false, $print = true) {
    $CI = & get_instance();
    $CI->load->model('model_courses');
    $university_boards = $CI->model_courses->get_university_board($institute);
    $echo = '';
    foreach ($university_boards as $board) {
        if ($select == $board['id']) {
            $echo .= "<option selected value='$board[id]'>$board[university_board]</option>";
        } else {
            $echo .= "<option value='$board[id]'>$board[university_board]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_course_batches($institute,$course,$year=false,$select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_batches');
    $show_adm_year_in_batch = show_adm_year_in_batch_name($institute);
    if(isset($course) && $course) {
        $batches = $CI->model_batches->get_batch_by_admission_year($institute,$year,$course,'active');
        foreach ($batches as $batch) {
            $batch_name = $show_adm_year_in_batch == TRUE ? $batch['admission_year']."-".$batch['batch_name'] : $batch['batch_name'];
                $batch['admission_year']."-".$batch['batch_name'];
            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch_name</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch_name</option>";
            }
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_certificate_names($institute,$select = false, $print = true) {
    $CI = & get_instance();
    $CI->load->model('model_certificate_generation');
    $array=array();
    $array['institute']=$institute;
    $certificate_names = $CI->model_certificate_generation->get_certificate_names($array);
    foreach ($certificate_names as $certificate_name) {
        if ($select == $certificate_name['id']) {
            $echo .= "<option selected value='$certificate_name[id]'>$certificate_name[certificate_name]</option>";
        } else {
            $echo .= "<option value='$certificate_name[id]'>$certificate_name[certificate_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
function echo_grievance_categories($institute_id,$select_category= false, $print = true) {

      $CI = & get_instance();  
      
      $CI->load->model('model_grievance');
      $categories=  $CI ->model_grievance->get_student_grievance_category($institute_id);
      
      $echo = '';
      foreach ($categories as $category) {
          if ($select_category && $select_category == $category['id']) {
              $echo .= '<option value="'.$category['id'].'" selected>'.$category['category'].'</option>';
          } else {
              $echo .= '<option value="'.$category['id'].'">'.$category['category'].'</option>';
          }
          
      }
       if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }

    
            }
