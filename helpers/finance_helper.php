<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function generate_new_series_receipt_number($data){
    
    $CI = & get_instance();
    $ret=array();
  
    $new_series_receipt = $CI->config->item('new_series_receipt');
  
    if ($new_series_receipt == TRUE) {
       // condition in config
        $paid_fee_id = array();
        $i = 0;
        $fees = $data['fees'];
        $amount = $data['amount']; 
        foreach($fees as $fee){
                    
            if($amount[$i] != ''){
                
                $paid_fee_id[$i] = $fees[$i];
            }
            $i++;
        }
        
        $last_fee = in_array($data['last_fee'], $paid_fee_id);

        if ($last_fee){
            $ret['status']= TRUE;
            
            $CI->load->model('model_institutes');
            $institute = $CI->model_institutes->get_institute_by_student($data['student_id']);
            $institute_id = $institute['institute_id'];    
            $CI->db->select_max('max_id');
            $CI->db->from('student_fees_payment fp');
            $CI->db->join('batches b',"fp.batch_id = b.id and b.institute_id = $institute_id");
            $CI->db->where('fp.series_id',2);
            $query = $CI->db->get();
            $result = $query->row_array();    
            if ($result['max_id']){
                $next_number = $result['max_id'] + 1;
            } else {
                $next_number = 501;
            }
            $prefix = get_option(array( 'option' => 'new_receipt_prefix'));
            $ret['ins']  = $result;
            $ret['max_id'] = $next_number;
            $ret['receipt_no'] = $prefix."/".$next_number;
        }else{
            $ret['status']= FALSE;
        } 
              
    } else {
        $ret['status']= FALSE;
    }
  
    return $ret;
}

function get_fine($fine_id,$diff) {
                  
   
    $CI = & get_instance();
    $CI->load->model('model_fine');
//    $result = $CI->model_fine->get_fine($fine_id,$diff);
//    if ($result['per_day']) {            
//        $fine = $diff * $result['discount'];
//    } else {
//       $fine =  $result['discount'];
//    }
    
    $result = $CI->model_fine->get_fine_by_category_id($fine_id);
    
    $balance_days = $diff;
    $fine = 0;
    
    $first_loop = TRUE;
    
    foreach ($result as $res) {       
        
        if ($balance_days > 0) {
            
            
            if ($res['day_to']) {
                $interval = $res['day_to'] - $res['day_from'] + 1;
            } else {
                $interval = $balance_days;
//                if ($first_loop == TRUE && $res['day_from'] > 1){
//                    $interval = $res['day_from'] - $balance_days;
//                } else {
//                   $interval = $balance_days; 
//                }
            }
            
            $first_loop = FALSE;
            
            if ($res['per_day'] && $interval >= $balance_days) {
                
                $fine += ($balance_days*$res['discount']);
                
            } else if($res['per_day'] && $interval < $balance_days) {
                
                $fine += ($interval*$res['discount']);
            } else {
                $fine += $res['discount'];
            }
            
            $balance_days -= $interval;
            
        }
        
    }
    

    return $fine;

}

function generate_receipt_number($student_id,$payment_date) {
    
   
    $CI = & get_instance();
    
    $CI->load->model('model_institutes');
    $institute = $CI->model_institutes->get_institute_by_student($student_id);
    $institute_id = $institute['institute_id'];   
    
    $fin_year = financial_year($payment_date);
    
    $CI->load->model('model_finance');
    $result = $CI->model_finance->get_student_fees_payment_maxOf_max_id($institute_id,$fin_year);
    if ($result['max_id']){
        $next_number = $result['max_id'] + 1;
    } else {
        $next_number = 1;
    }
    
    $receipt_number = str_pad($next_number,4,"0",STR_PAD_LEFT);
    
    $number['max_id'] = $next_number;
    $number['receipt_no'] = $fin_year."-".$receipt_number;
    $number['series'] = $fin_year;
    return $number;
    
}


function generate_receipt_number_new($student_id,$payment_date) {
    
   
    $CI = & get_instance();
    
    $CI->load->model('model_institutes');
    $institute = $CI->model_institutes->get_institute_by_student($student_id);
    $institute_id = $institute['institute_id'];  
    
    $fee_receipt_rules = get_option(array('option' => 'fee_receipt_generation_rule','institute_id'=>$institute_id));
    $rules = json_decode($fee_receipt_rules,TRUE);
    
    $rule = $rules['rule'];
    $search_words = array("{{RECEIPTPREFIXCODE}}","{{FINANCIALYEAR}}","{{ID}}");
    
    $rule_contain = get_string_contain_words_in_array($rule,$search_words);
    
    $prefix = '';
    
    if (in_array("{{RECEIPTPREFIXCODE}}", $rule_contain)) {
        $CI->load->model('model_institutes');
        $institute_details = $CI->model_institutes->get_institute_by_id($institute_id);
        $receipt_prefix_code = $institute_details['receipt_code'];
        $rule = str_replace('{{RECEIPTPREFIXCODE}}',$receipt_prefix_code, $rule);
        
    }
    
    if (in_array("{{FINANCIALYEAR}}", $rule_contain)) {
        
        $fin_year = financial_year($payment_date);
        
        $fin_year = isset($rules['financial_year_separator']) && $rules['financial_year_separator'] ? str_replace('/',$rules['financial_year_separator'],$fin_year) : $fin_year ;
        
        $fin_year = isset($rules['financial_year_length']) && $rules['financial_year_length'] == 2 ? substr($fin_year,0,2) : $fin_year ;
        
        $rule = str_replace('{{FINANCIALYEAR}}',$fin_year, $rule);
          
    }
    
    
    if (in_array("{{ID}}", $rule_contain)) {
        
        $str_length = isset($rules['id_length']) && $rules['id_length'] ? $rules['id_length'] : 0 ;
        
        $prefix = str_replace('{{ID}}','', $rule);
        
        $CI->load->model('model_finance');
        $row = $CI->model_finance->get_student_fees_payment_maxOf_max_id($institute_id,$prefix);
        if($row) {
            $next_number = $row['max_id']+1;
        } else {
             $next_number = 1;   
        }
        $s_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
        
        $rule = str_replace('{{ID}}',$s_number, $rule);
    }
        
    $number['max_id'] = $next_number;
    $number['receipt_no'] = $rule;
    $number['series'] = $prefix;
    return $number;
    
}


function get_last_fee_payment_date($student_id,$batch_id,$fee_id,$fee_sem){
    
    $CI = &get_instance();
    $CI->load->model('model_finance');
    $result = $CI->model_finance->get_last_fee_payment_date($student_id,$batch_id,$fee_id,$fee_sem);
    return $result['last_date'];
}

function get_opening_balance($institute,$to_date,$category=false,$head=false,$remark = false,$exact_remark = false){
    
    $CI = &get_instance();
    
    $CI->load->model('model_day_book');
    $opening_date = $CI->model_day_book->get_opening_date($head);
    
    $from_date = $opening_date['date'];
    
    
       
    $CI->load->model('model_finance_report');
    $expense = $CI->model_finance_report->day_book_with_fee_receipt($institute,
                                                    $category,$head,$from_date,$to_date,$remark,$exact_remark);
    
    $credit_total = 0;
    $debit_total = 0;
    $balance = 0;
    
    if ($expense) {        
        foreach ($expense as $expns) {

            $credit = 0;
            $debit = 0;

            if (!$head) {
                if ($expns['from_head_category'] == $category && $expns['to_head_category'] == $category
                        && $expns['from_institute'] == $institute && $expns['to_institute'] == $institute) {
                    $credit = $expns['amount'];
                    $debit = $expns['amount'];
                } elseif ($expns['from_institute'] == $institute && $expns['from_head_category'] == $category) {
                    $credit = 0;
                    $debit = $expns['amount'];
                } elseif ($expns['to_institute'] == $institute && $expns['to_head_category'] == $category) {
                    $credit = $expns['amount'];
                    $debit = 0;
                }
            } 
            elseif ($expns['from_head_id'] == $head && $expns['to_head_id'] == $head) {
                                                                
                    $credit = $expns['amount'];

                    $debit = $expns['amount'];
            } 
            elseif ($expns['from_head_id'] == $head) {
                       
                    $credit = 0;

                    $debit = $expns['amount'];
            } 
            elseif ($head && $expns['to_head_id'] == $head && $expns['status'] == 'refund') {
                     
                    $credit = 0;

                    $debit = abs($expns['amount']);
            } 
            elseif ($head && $expns['to_head_id'] == $head) {
                   
                    $credit = $expns['amount'];

                    $debit = 0;
            }

            if ($credit) {
                $credit_total += $credit;
            } elseif ($debit) {
                $debit_total += $debit;
            }
                           
        }
                
    }
    $balance = $credit_total - $debit_total;
    return $balance;

}

function get_day_book($institute,$from_date,$to_date,$category=false,$head=false){
    
    $CI = &get_instance();
    
    $CI->load->model('model_day_book');
       
    $CI->load->model('model_finance_report');
    $expense = $CI->model_finance_report->day_book_with_fee_receipt($institute,
                                                    $category,$head,$from_date,$to_date);
    
    $credit_total = 0;
    $debit_total = 0;
    $balance = 0;
    
    if ($expense) {        
        foreach ($expense as $expns) {

            $credit = 0;
            $debit = 0;

            if (!$head) {
                if ($expns['from_head_category'] == $category && $expns['to_head_category'] == $category
                        && $expns['from_institute'] == $institute && $expns['to_institute'] == $institute) {
                    $credit = $expns['amount'];
                    $debit = $expns['amount'];
                } elseif ($expns['from_institute'] == $institute && $expns['from_head_category'] == $category) {
                    $credit = 0;
                    $debit = $expns['amount'];
                } elseif ($expns['to_institute'] == $institute && $expns['to_head_category'] == $category) {
                    $credit = $expns['amount'];
                    $debit = 0;
                }
            } elseif ($expns['from_head_id'] == $head) {
                $credit = 0;
                $debit = $expns['amount'];
            } elseif ($head && $expns['to_head_id'] == $head) {
                $credit = $expns['amount'];
                $debit = 0;
            }

            if ($credit) {
                $credit_total += $credit;
            } elseif ($debit) {
                $debit_total += $debit;
            }
                           
        }
                
    }
    $balance = $credit_total - $debit_total;
    return $balance;

}

function echo_fee_category($institute,$select = false, $print = true) {

    $echo = "";
    $CI = & get_instance();

    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }
    
    $CI->load->model('model_fee_category');
    $fee_categories = $CI->model_fee_category->get_fee_categories($institute);
    foreach ($fee_categories as $fee_category) {
        if ($select && in_array($fee_category['id'], $select)) {
            $echo .= "<option selected value='$fee_category[id]'>$fee_category[category_name]</option>";
        } else {
            $echo .= "<option value='$fee_category[id]'>$fee_category[category_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_fee($institute,$fee_category = false, $select = false, $print = true) {

    $echo = "";
    $CI = & get_instance();

    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }
    
    $CI->load->model('model_fee');
    $fees = $CI->model_fee->get_fees($institute,$fee_category);
    foreach ($fees as $fee) {
        if (in_array($fee['id'], $select)) {
            $echo .= "<option selected value='$fee[id]'>$fee[fee_name]</option>";
        } else {
            $echo .= "<option value='$fee[id]'>$fee[fee_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_fine($institute,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_fine');
    $fines = $CI->model_fine->get_fine_categories($institute);
    foreach ($fines as $fine) {
        if ($select == $fine['id']) {
            $echo .= "<option selected value='$fine[id]'>$fine[fine_name]</option>";
        } else {
            $echo .= "<option value='$fine[id]'>$fine[fine_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_day_book_heads($institute,$category = false, $select = false, $print = true){
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_day_book');        
    $heads = $CI->model_day_book->get_day_book_heads($institute,$category);          
    foreach ($heads as $head) {
        if ($select == $head['id']) {
            $echo .= "<option selected value='$head[id]'>$head[head_name]</option>";
        } else {
            $echo .= "<option value='$head[id]'>$head[head_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_day_book_heads_show_expense_first($institute, $select = false, $print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_day_book');        
    $heads = $CI->model_day_book->get_day_book_heads_show_expense_first($institute);        
    
    $last_loaded_group = '';
    $group_count = 0;
    $count = 0;
    foreach ($heads as $head) {
        
        if ($last_loaded_group != $head['group_name']){
            $group_count++;
              if($count != 0){ 
                  $echo .= "</optgroup>";
              }
            $echo .= "<optgroup label='$head[group_name]'>";  
        }
       
        if ($select == $head['id']) {
            $echo .= "<option selected value='$head[id]'>$head[head_name]</option>";
        } else {
            $echo .= "<option value='$head[id]'>$head[head_name]</option>";
        }
        
        $last_loaded_group=$head['group_name'];
        $count++;
    }
    
    $echo .= "</optgroup>";
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_payees($institute,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_day_book');
    $CI->load->model('model_employee');
    $CI->load->model('model_supplier');
    
    $customers = $CI->model_day_book->get_day_book_customers($institute);
    $suppliers = $CI->model_supplier->get_suppliers($institute);
    $employees = $CI->model_employee->get_employees_by_institute($institute);
    
    if ($customers) {
        $echo .= "<optgroup label='Customers'>"; 
        foreach ($customers as $cust) {
            $cust_id = 'c/'.$cust['id'];
            if ($select == $cust_id) {
                $echo .= "<option selected value='$cust_id'>$cust[name]</option>";
            } else {
                $echo .= "<option value='$cust_id'>$cust[name]</option>";
            }
        }
        $echo .= "</optgroup>";
    }
    if ($suppliers) {
        $echo .= "<optgroup label='Suppliers'>"; 
        foreach ($suppliers as $supplier) {
            $sup_id = 's/'.$supplier['id'];
            if ($select == $sup_id) {
                $echo .= "<option selected value='$sup_id'>$supplier[supplier_name]</option>";
            } else {
                $echo .= "<option value='$sup_id'>$supplier[supplier_name]</option>";
            }
        }
        $echo .= "</optgroup>";
    }
    if ($employees){
        $echo .= "<optgroup label='Employee'>";
        foreach ($employees as $emp){
            $emp_id = 'e/'.$emp['employee_id'];
            if ($select == $emp_id) {
                $echo .= "<option selected value='$emp_id'>$emp[full_name]</option>";
            } else {
                $echo .= "<option value='$emp_id'>$emp[full_name]</option>";
            }
        }
        $echo .= "</optgroup>";
    }
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}


function echo_day_book_customers($institute, $select = false, $print = true) {
    
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_day_book');        
    $customers = $CI->model_day_book->get_day_book_customers($institute);          
    foreach ($customers as $cust) {
        if ($select == $cust['id']) {
            $echo .= "<option selected value='$cust[id]'>$cust[name]</option>";
        } else {
            $echo .= "<option value='$cust[id]'>$cust[name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
    
}

function echo_day_book_groups($categories = false,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_day_book');
    $groups = $CI->model_day_book->get_all_day_book_groups($categories);
    foreach ($groups as $group) {
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

/** group_category = array(); */
function echo_day_book_heads_by_group_category($institute,$group_category,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_day_book');
    $heads = $CI->model_day_book->get_all_day_book_heads_by_group_category($institute,$group_category);
    foreach ($heads as $head) {
        if ($select == $head['id']) {
            $echo .= "<option selected value='$head[id]'>$head[head_name] - $head[group_name]</option>";
        } else {
            $echo .= "<option value='$head[id]'>$head[head_name] - $head[group_name]</option>";
        }
    }
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

/** group_category = array(); */
function echo_payment_account_day_book_heads($institute_id,$group_category_id = FALSE,$select = false,$print = true){
    
    
    $echo = '';
    $CI = & get_instance();
    $CI->load->model('model_day_book');
    
//    if (!$group_category_id) {
//        
//        $group_category_id = array(CASH, BANK, INCOME, CURRENT_ASSETS, FIXED_ASSETS, 
//                                    CURRENT_LIABILITIES, NON_CURRENT_LIABILITIES, EQUITY,
//                                    OTHER_INCOME, EXPENSES, OTHER_EXPENSES);
//    }
//    
    
    $heads = $CI->model_day_book->get_day_book_heads_by_group_category($institute_id,$group_category_id);
    
    $last_loaded_group = '';
    $group_count = 0;
    $count = 0;
    
    foreach ($heads as $head) {
        
        if ($last_loaded_group != $head['group_name']){
            $group_count++;
              if($count != 0){ 
                  $echo .= "</optgroup>";
              }
            $echo .= "<optgroup label='$head[group_name]'>";  
        }
        
        if ($select == $head['id']) {
            $echo .= "<option selected value='$head[id]'>$head[head_name]</option>";
        } else {
            $echo .= "<option value='$head[id]'>$head[head_name]</option>";
        }
        
        $last_loaded_group=$head['group_name'];
        $count++;
    }
    $echo .= "</optgroup>";
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_day_book_expense_heads($institute_id,$select = false,$print = true) {
    
    $echo = '';
    $CI = & get_instance();
    
    $group_category_id = array(EXPENSES);
    
    $CI->load->model('model_day_book');
    $heads = $CI->model_day_book->get_day_book_heads_by_group_category($institute_id,$group_category_id);
    
    foreach ($heads as $head) {
        
        if ($select == $head['id']) {
            $echo .= "<option selected value='$head[id]'>$head[head_name]</option>";
        } else {
            $echo .= "<option value='$head[id]'>$head[head_name]</option>";
        }
    }
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_batch_fee_semesters($batch,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_batches');
    $batch_details = $CI->model_batches->get_batch_details_by_id($batch);
    $fee_sems = $batch_details['total_fee_semesters'];
    for($k=1 ; $k<=$fee_sems;$k++) { 
      
        if($select == $k){       
            $echo .= "<option selected value='$k'> $k </option>";
        } else {
            $echo .= "<option value='$k'> $k </option>";
        }
    } 
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_institute_max_fee_semesters($institute,$select = false,$print = true) {
    
    $echo = "";
    
    $max_fee_sem = get_option(array('option' => 'maximum_fee_year','institute_id' => $institute));
    for($k=1 ; $k<=$max_fee_sem;$k++) { 
      
        if($select == $k){       
            $echo .= "<option selected value='$k'> $k </option>";
        } else {
            $echo .= "<option value='$k'> $k </option>";
        }
    } 
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_fee_groups($institute,$select = false, $print = true) {

    $echo = "";
    $CI = & get_instance();

    $CI->load->model('model_fee_group');
    $fee_groups = $CI->model_fee_group->get_fee_groups($institute);
    foreach ($fee_groups as $fee_group) {
        if ($select == $fee_group['id']) {
            $echo .= "<option selected value='$fee_group[id]'>$fee_group[group_name]</option>";
        } else {
            $echo .= "<option value='$fee_group[id]'>$fee_group[group_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_user_fee_category($institute,$select = false, $print = true) {

    $echo = "";
    $CI = & get_instance();

    $CI->load->model('model_fee_category');
    
    $user_type = $CI->session->userdata('user_type');
    $show_all_fee_category = get_option(array('option' => 'show_all_fee_category_in_payment','institute_id' => $institute));
    if ($show_all_fee_category == 'no' && $user_type != "super_admin") {
        $user_id = $CI->session->userdata('user_id'); 
        $fee_categories = $CI->model_fee_category->get_user_fee_categories($institute,$user_id);
    }else {
        $fee_categories = $CI->model_fee_category->get_fee_categories($institute);            
    }
    
    foreach ($fee_categories as $fee_category) {
        if ($select == $fee_category['id']) {
            $echo .= "<option selected value='$fee_category[id]'>$fee_category[category_name]</option>";
        } else {
            $echo .= "<option value='$fee_category[id]'>$fee_category[category_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_student_fees($student_id,$batch_id,$fee_group_id,$fee_sem,$select_fee = FALSE,$print = TRUE) {
    
    $echo = "";
    $CI = & get_instance();

    $CI->load->model('model_student_fee');
    $CI->load->model('model_fee_group');
    $CI->load->model('model_batch_fee');
    
    $student_fee_isset = $CI->model_student_fee->check_student_fee_isset($student_id,$batch_id);
    if($student_fee_isset) {
        $student_fees = $CI->model_student_fee->get_fee_setted_to_student($student_id,$batch_id,$fee_sem);
    } elseif ($fee_group_id) {
        $batch_group_fee_isset = $CI->model_fee_group->check_batch_group_fee_isset($fee_group_id,$batch_id);
        if ($batch_group_fee_isset) {
            $student_fees = $CI->model_fee_group->get_fee_setted_to_batch_fee_group($fee_group_id,$batch_id,$fee_sem);
        } else {
            $student_fees = $CI->model_batch_fee->get_fee_setted_batch($batch_id,$fee_sem);
        }
        
    } else {
        $student_fees = $CI->model_batch_fee->get_fee_setted_batch($batch_id,$fee_sem);
    }
  //  var_dump($student_fees);
    
    foreach ($student_fees as $fee) {
        if ($select_fee == $fee['fee_id']) {
            $echo .= "<option selected value='$fee[fee_id]'>$fee[fee_name]</option>";
        } else {
            $echo .= "<option value='$fee[fee_id]'>$fee[fee_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_payment_gateways($select = FALSE,$print = TRUE) {
    $echo = "";
    $CI = & get_instance();

    $CI->load->model('model_payment_gateway');
    $gateways = $CI->model_payment_gateway->get_payment_gateways();
    
    
    foreach ($gateways as $gateway) {
        if ($select == $gateway['gateway_key']) {
            $echo .= "<option selected value='$gateway[gateway_key]'>$gateway[name]</option>";
        } else {
            $echo .= "<option value='$gateway[gateway_key]'>$gateway[name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function category_wise_student_balance_fee_to_paid_by_date($student_id,$batch_id,$date) {
    
    $CI = & get_instance();
    
    $CI->load->model('model_students');
    $CI->load->model('model_student_fee');
    $CI->load->model('model_fee_group');
    $CI->load->model('model_batch_fee');
    $CI->load->model('model_finance');
    
    $student = $CI->model_students->get_student($student_id);
    
    $student_fee_isset = $CI->model_student_fee->check_student_fee_isset($student_id,$batch_id);
                    
    if($student_fee_isset){
        
        $category_wise_fee = $CI->model_student_fee->get_category_wise_total_student_fee($student_id,$batch_id);
    } elseif ($student['fee_group_id']) {
        
        $batch_group_fee_isset = $CI->model_fee_group->check_batch_group_fee_isset($student['fee_group_id'],$batch_id);
        if ($batch_group_fee_isset) {
            
            $category_wise_fee = $CI->model_fee_group->get_category_wise_total_student_group_fee($student['fee_group_id'],$batch_id);
           

        } else {
            
            $category_wise_fee = $CI->model_batch_fee->get_category_wise_total_batch_fee($batch_id);
            
        }

    } else{

        $category_wise_fee = $CI->model_batch_fee->get_category_wise_total_batch_fee($batch_id);
        
    }
    
    //paid amount
    $fee_paid = $CI->model_finance->category_wise_student_paid_amount_by_date($student_id,$batch_id,$date);
    
    $cat_fee_details = array();
    
    foreach ($fee_paid as $fee_bal) {
        $cat_fee_details[$fee_bal['category_id']]['paid_amount'] = $fee_bal['amount'] + $fee_bal['tax'];
    }
    
    foreach ($category_wise_fee as $fee) {
        if (isset($fee['concession'])) {
            $cat_fee_details[$fee['category_id']]['fee_amount'] = $fee['fee_amount'] - $fee['concession'];
        } else {
            $cat_fee_details[$fee['category_id']]['fee_amount'] = $fee['fee_amount'];
        }
    }
    
    return $cat_fee_details;
    
}

function echo_fee_sem($institute,$adm_year = FALSE,$select = FALSE,$print = TRUE) {
    $echo = "";
    $CI = & get_instance();

    $CI->load->model('model_batch_years');
    $fee_sem = $CI->model_batch_years->get_max_fee_sem($institute,$adm_year);
    if ($fee_sem['max_fee_sem']) {
        $max_fee_sem = $fee_sem['max_fee_sem'];
    } else {
        $max_fee_sem = 1;
    }
    
    
    for ($j = 1; $j <= $max_fee_sem; $j++) {
     $echo .= '<option value="'.$j.'">'.$j.'</option>';
    } 
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_payment_methods($select = FALSE,$print = TRUE) {
    
    $echo = '';
    
    $payment_methods = config_item('payment_methods');
    
    foreach ($payment_methods as $key => $value) {
        if ($select == $key) {
            $echo .= "<option value='$key' selected>$value</option>";
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


function generate_voucher_number($institute_id,$date,$voucher_type) {
    
   
    $CI = & get_instance();

    $fin_year = financial_year($date);
    
    if ($voucher_type == 'expense'){
        $series = 'E'.$fin_year;
    } else if ($voucher_type == 'payment'){
        $series = 'P'.$fin_year;
    } else {
        $series = $fin_year;
    }
    
    
    $CI->load->model('model_day_book');
    $result = $CI->model_day_book->get_day_book_maxOf_max_id($institute_id,$series);
    if ($result['max_id']){
        $next_number = $result['max_id'] + 1;
    } else {
        $next_number = 1;
    }
    
    if ($voucher_type == 'expense'){
        $voucher_number = $fin_year.'-'.'E'.$next_number;
    } elseif ($voucher_type == 'payment') {
        $voucher_number = $fin_year.'-'.'P'.$next_number;
    }else {
        $voucher_number = $fin_year.'-'.$next_number;
    }
    
    $number['max_id'] = $next_number;
    $number['voucher_no'] = $voucher_number;
    $number['prefix'] = $series;
    return $number;
    
}


function get_bbps_convenience_fees($amount) {
    
   
    $CI = & get_instance();

    $amount = round($amount,1);

    $bbps_convenience_fee_ranges = config_item('bbps_convenience_fee_ranges');
    
    $tax = isset($bbps_convenience_fee_ranges["tax"]) ? $bbps_convenience_fee_ranges["tax"] : 0;
    
    foreach ($bbps_convenience_fee_ranges["ranges"] as $key => $value){
        
        $min_max_value = explode('-', $key);
        
        $min = $min_max_value[0];
        $max = $min_max_value[1];
        
        if(!$max && ($min <= $amount)){
            $bbps_convenience_fee = $value;
        } elseif ($max && ($min <= $amount) && ($amount <= $max)) {
            $bbps_convenience_fee = $value;
        }
        
    }

    $bbps_convenience_fee = isset($bbps_convenience_fee) && $bbps_convenience_fee ?
                                                   $bbps_convenience_fee : 0;             
    
    $bbps_convenience_fee_with_tax = $bbps_convenience_fee + (($bbps_convenience_fee*$tax)/100);
    
    return round($bbps_convenience_fee_with_tax,1);
}
