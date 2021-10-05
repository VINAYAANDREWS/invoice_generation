<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function echo_lms_institute_category($institute,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_learning_materials');
    $categories = $CI->model_learning_materials->get_categories_by_institute($institute);
    foreach ($categories as $category) {
        if($select == $category['id']) {
            $echo .= "<option selected value='$category[id]'>$category[name]</option>";
        } else {
            $echo .= "<option value='$category[id]'>$category[name]</option>";
        }
    }
    if ($print == true){
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_online_exams($subject,$student_id,$select = false,$print = true){
   
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_online_exams');
    $exams = $CI->model_online_exams->get_exam_titles_by_subject($subject,$student_id);
    
    foreach ($exams as $exam) {
        if($select == $exam['id']) {
            $echo .= "<option selected value='$exam[id]'>$exam[exam_title]</option>";
        } else {
            $echo .= "<option value='$exam[id]'>$exam[exam_title]</option>";
        }
    }
    if ($print == true){
        echo $echo;
    } else {
        return $echo;
    }
}

function remaining_time($exam_time,$time) {

        $CI = & get_instance();
        if($time){
          $start_time = strtotime($time);  
        }else{
          $start_time = $CI->session->userdata('start_time');
        }

//        if ($exam_time == false) {
//            $exam_time = $CI->config->item('exam_time');
//        }
        $total_second = time() - $start_time;
        return $remaing = $exam_time - $total_second;
    }
    
    function echo_online_exam_title($institute,$select=false,$print=true){
        
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_online_exams');
        $exams = $CI->model_online_exams->get_online_exam($institute);
       
        foreach ($exams as $exam) {
            if($select == $exam['id']) {
                $echo .= "<option selected value='$exam[id]'>$exam[exam_title] - $exam[batch_name]</option>";
            } else {
                $echo .= "<option value='$exam[id]'>$exam[exam_title] - $exam[batch_name]</option>";
            }
        }
        if ($print == true){
            echo $echo;
        } else {
            return $echo;
        }  
        
    }
    
    function determine_url_type($url) {


        $yt_rx = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';
        $has_match_youtube = preg_match($yt_rx, $url, $yt_matches);
        
        if($has_match_youtube) {
            $video_id = $yt_matches[5]; 
            $type = 'youtube';
        }
  
        else {
            $video_id = 0;
            $type = 'none';
        }


        $data['video_id'] = $video_id;
        $data['video_type'] = $type;

        return $data;
    
    }
    
    function get_learning_material_batch_code($batch,$batch_sem){
        
         $result = "";
         $CI = & get_instance();
         $CI->load->model('model_batches');
         $CI->load->model('model_institutes');
    
        
         
         $batch_details = $CI->model_batches->get_batch_details_by_id($batch);
         $institute_details = $CI->model_institutes->get_institute_by_id($batch_details['institute_id']);
         $academic_sem = $CI->session->userdata('academic_sem');
         $class_course_label = $CI->session->userdata('single_course_class');
         
           if(isset($batch_details)){
               if ($institute_details['adm_year_in_batch_name']) {
                   $batch_name = $batch_details['batch_name']."-".$batch_details['admission_year'];
               } else {
                   $batch_name = $batch_details['batch_name'];
               }
               $course_batch_code = $batch_details['course_code']."_"."$batch_name";
               $code = remove_special_characters($course_batch_code);
               $result['batch_name'] = $batch_name;
               $result['course_name'] = $batch_details['course_name'];
               $result['id']=$batch_details['id'];
               $result['batch_sem'] = $batch_sem;
               if($academic_sem == 'yes'){
                   $result['batch_class'] = $code."-sem".$batch_sem;
                   $result['title'] = $class_course_label." code: ".$batch_details['course_code']." | Batch: ".$batch_name."| Semester : ".$batch_sem;
                   $result['label_name'] = $batch_details['course_code']." - ".$batch_name." - sem ".$batch_sem;
               }
               else{
                   $result['batch_class'] = $code;
                   $result['title'] = $class_course_label." code: ".$batch_details['course_code']." | Batch: ".$batch_name;
                   $result['label_name'] = $batch_details['course_code']." - ".$batch_name;
               }
               return $result;
           }
    }
    
    
?>