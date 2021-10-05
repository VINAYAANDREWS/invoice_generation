<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function generate_receipt_number1(){
    
    $CI= & get_instance();
    $prefix="R";
    
    $CI->db->select_max('max_id');
    $query=$CI->db->get('student_fees_payment');
    $row=$query->row_array();
    if($row)
    {
        $next_number=$row['max_id']+1;
    }
    else
     {
         $next_number=1;   
     }
     $s_number=str_pad($next_number,4,"0",STR_PAD_LEFT);
     $receipt['receipt_no']=$prefix.$s_number;
     $receipt['receipt_prefix'] = $prefix;
     $receipt['max_id'] = $next_number;
     return $receipt;
     
}

function generate_receipt_number($student_id){
   
    $CI = & get_instance();
    
    $CI->load->model('model_institutes');
    $institute = $CI->model_institutes->get_institute_by_student($student_id);
    $institute_id = $institute['institute_id']; 
    $CI->db->select_max('max_id');
    $CI->db->from('student_fees_payment fp');
    $CI->db->join('batches b',"fp.batch_id = b.id");
    $CI->db->join('courses c',"b.course_id = c.id and c.institute_id = $institute_id ");
    //$CI->db->where('fp.series_id',1);
    $query = $CI->db->get();
    $result = $query->row_array();
    if ($result['max_id']){
        $next_number = $result['max_id'] + 1;
    } else {
        $next_number = 1;
    }
    
    $number['max_id'] = $next_number;
    $number['receipt_no'] = $next_number;
    return $number;
    
}