<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function echo_store_categories($institute,$select=false,$print=true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_store');
    $categories = $CI->model_store->get_store_categories($institute);
    foreach ($categories as $category) {
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

function echo_store_types($institute,$select=false,$print=true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_store');
    $types = $CI->model_store->get_store_types($institute);
    foreach ($types as $type) {
        if ($select == $type['id']) {
            $echo .= "<option selected value='$type[id]'>$type[type_name]</option>";
        } else {
            $echo .= "<option value='$type[id]'>$type[type_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_stores($institute,$select=false,$print=true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_store');
    $get_store['institute'] = $institute;
    $stores = $CI->model_store->get_stores($get_store);
    $store_count = count($stores);
    foreach ($stores as $store) {
        
         if ($select) {
            if ($select == $store['id']) {
                $echo .= "<option selected value='$store[id]'>$store[name]</option>";
            } else {
                $echo .= "<option value='$store[id]'>$store[name]</option>";
            }
        } else {
            if($store['main_store']==1 || $store_count==1) {
                $echo .= "<option selected value='$store[id]'>$store[name]</option>";
            } else {
                $echo .= "<option value='$store[id]'>$store[name]</option>";
            }
        }
       
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_institute_stores($institute,$select_store,$select=false,$print=true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_store');
    $get_store['institute'] = $institute;
    $stores = $CI->model_store->get_stores($get_store);
    $store_count = count($stores);
    foreach ($stores as $store) {
        if (($select == $store['id'] || $select_store=="false")) {
            $echo .= "<option selected value='$store[id]'>$store[name]</option>";
        } else {
            $echo .= "<option value='$store[id]'>$store[name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_stores_item_categories($institute,$select=false,$print=true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_store_item');
    $categories = $CI->model_store_item->get_store_item_categories($institute);
    foreach ($categories as $category) {
        if ($select == $category['id']) {
            $echo .= "<option selected value='$category[id]'>$category[name]</option>";
        } else {
            $echo .= "<option value='$category[id]'>$category[name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_units($institute,$select=false,$print=true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_store_item');
    $units = $CI->model_store_item->get_units($institute);
    foreach ($units as $unit) {
        if ($select == $unit['id']) {
            $echo .= "<option selected value='$unit[id]'>$unit[unit]</option>";
        } else {
            $echo .= "<option value='$unit[id]'>$unit[unit]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
function echo_supplier_types($institute,$select = FALSE,$print = TRUE) {
    
    $CI = & get_instance();
    $CI->load->model('model_supplier');
    $types = $CI->model_supplier->get_supplier_types($institute);
    $echo = "";
    foreach ($types as $type) {
        if ($select == $type['id']) {
            $echo .= "<option selected value='$type[id]'>$type[name]</option>";
        } else {
            $echo .= "<option value='$type[id]'>$type[name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_items($item_category,$select = FALSE,$asset = FALSE,$print = TRUE){
    
    $CI = & get_instance();
    $CI->load->model('model_store_item');
    if(isset($asset) && $asset==true) {
        $items = $CI->model_store_item->get_item_by_category($item_category,$asset);
    } else {
        $items = $CI->model_store_item->get_item_by_category($item_category);
    }
    $echo = "";
    foreach ($items as $item) {
        $item_info = "";
        $item_code = "";
        if($item['item_code']) {
                $item_code .= $item['item_code']." - ";
            }
        if($item['name']) {
                $item_info .= " - ".$item['name'];
            }if($item['model_no']) {
                $item_info .= " - ".$item["model_no"];
            }
        if ($select == $item['id']) {
            $echo .= "<option selected value='$item[id]' rel='$item[item_name]$item_info'>$item_code$item[item_name]$item_info</option>";
        } else {
            $echo .= "<option value='$item[id]' rel='$item[item_name]$item_info'>$item_code$item[item_name]$item_info</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_suppliers($institute,$supplier_type=FALSE,$select = FALSE,$print = TRUE){
    $CI = & get_instance();
    $CI->load->model('model_supplier');
    $echo = "";
    if ($institute) {
        if($supplier_type) {
            $suppliers = $CI->model_supplier->get_suppliers($institute,$supplier_type);
        }else {
            $suppliers = $CI->model_supplier->get_suppliers($institute);
        }
        foreach ($suppliers as $supplier) {
            if ($select == $supplier['id']) {
                $echo .= "<option selected value='$supplier[id]'>$supplier[supplier_name]</option>";
            } else {
                $echo .= "<option value='$supplier[id]'>$supplier[supplier_name]</option>";
            }
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_store_indents($institute,$store=false,$status=false,$select = FALSE,$print = TRUE) {
    $CI = & get_instance();
    $CI->load->model('model_indent');
    $CI->load->model('model_store');
    $indent['institute'] = $institute;
    
    $indent['status'] = $status;
    if($store) {
        $indent['store'] = $store;
    } else {
        $CI->load->model('model_store');
        $get_store['institute'] = $institute;
        $stores = $CI->model_store->get_stores($get_store);
        $store_count = count($stores);
        foreach ($stores as $store) {
            if($store['main_store']==1 || $store_count==1) {
                $indent['store'] = $store['id'];
            }
        }
    }
    $indents = $CI->model_indent->get_indents($indent);
    $echo = "";
    if(isset($indents) && $indents && $store) {
        foreach ($indents as $indent) {
            if ($select == $indent['id']) {
                $echo .= "<option selected value='$indent[id]'>$indent[indent_no]</option>";
            } else {
                $echo .= "<option value='$indent[id]'>$indent[indent_no]</option>";
            }
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_indent_items($indent,$select = FALSE,$print = TRUE) {
    $CI = & get_instance();
    $CI->load->model('model_indent');
    $indent_items = $CI->model_indent->get_indent_items_by_indent_id($indent);
    $echo = "";
    foreach ($indent_items as $item) {
        if ($select == $item['indent_item_id']) {
            $echo .= "<option selected value='$item[indent_item_id]'>$item[item_name]</option>";
        } else {
            $echo .= "<option value='$item[indent_item_id]'>$item[item_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_store_supplier_purchase_order($institute,$store,$supplier,$status=false,$select = FALSE,$print = TRUE) {
    $CI = & get_instance();
    $CI->load->model('model_purchase_order');
    $purchase['institute'] = $institute;
    $purchase['store'] = $store;
    $purchase['supplier'] = $supplier;
    $purchase['status'] = $status;
    $purchase_orders = $CI->model_purchase_order->get_purchase_orders($purchase);
    $echo = "";
    foreach ($purchase_orders as $p_o) {
        if ($select == $p_o['id']) {
            $echo .= "<option selected value='$p_o[id]'>$p_o[po_number]</option>";
        } else {
            $echo .= "<option value='$p_o[id]'>$p_o[po_number]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}


function echo_purchase_order_items($purchase_order,$select = FALSE,$print = TRUE) {
    $CI = & get_instance();
    $CI->load->model('model_purchase_order');
    $purchase_items = $CI->model_purchase_order->get_items_by_po_id($purchase_order);
    $echo = "";
    foreach ($purchase_items as $item) {
        if ($select == $item['po_item_id']) {
            $echo .= "<option selected value='$item[po_item_id]'>$item[item_name]</option>";
        } else {
            $echo .= "<option value='$item[po_item_id]'>$item[item_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_unpaid_purchase_bills($store,$supplier,$bill_id = FALSE,$print = TRUE) {
    $CI = & get_instance();
    $CI->load->model('model_purchase_bill');
    if(isset($bill_id) && $bill_id) {
        $purchase_bills = $CI->model_purchase_bill->get_purchase_bills_by_supplier_and_store($store,$supplier);
    } else {
        $purchase_bills = $CI->model_purchase_bill->get_unpaid_purchase_bills($store,$supplier);
    }
    $echo = "";
    foreach ($purchase_bills as $purchase_bill) {
        if ($bill_id == $purchase_bill['id']) {
            $echo .= "<option selected value='$purchase_bill[id]'>$purchase_bill[bill_no]</option>";
        } else {
            $echo .= "<option value='$purchase_bill[id]'>$purchase_bill[bill_no]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}


function get_suppliper_account_balance($institute,$store = FALSE,$supplier_type = FALSE,
        $supplier = FALSE,$type = FALSE,$sb_to_date = FALSE) {
    
    $CI = &get_instance();
    $supplier_acc['institute'] = $institute;
    $supplier_acc['store'] = $store;
    $supplier_acc['supplier_type'] = $supplier_type;
    $supplier_acc['supplier'] = $supplier;
    $supplier_acc['type'] = $type;
    $supplier_acc['to_date'] = $sb_to_date;
    
    $CI->load->model('model_supplier_payment');
    $supplier_account_info = $CI->model_supplier_payment->get_supplier_account_info($supplier_acc);
    
    $balance = 0;
    foreach ($supplier_account_info as $supplier_acc) {

        if ($supplier_acc['bill_amount']) {
            $balance -= $supplier_acc['bill_amount'];
        } else if ($supplier_acc['paid_amount']) {
            $balance += $supplier_acc['paid_amount'];
        }
    }
    
    return $balance;
}

function echo_manufacturer($institute,$select = FALSE,$print = TRUE) {
    $CI = & get_instance();
    $CI->load->model('model_store_item');
    $manufacturers = $CI->model_store_item->get_manufacturers($institute);
    $echo = "";
    foreach ($manufacturers as $manufacturer) {
        if ($select == $manufacturer['id']) {
            $echo .= "<option selected value='$manufacturer[id]'>$manufacturer[name]</option>";
        } else {
            $echo .= "<option value='$manufacturer[id]'>$manufacturer[name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_store_item_types($select = FALSE,$print = TRUE) {
    $CI = & get_instance();
    $CI->load->model('model_store_item');
    $item_types = $CI->model_store_item->get_item_types();
    $echo = "";
    foreach ($item_types as $type) {
        if ($select == $type['id']) {
            $echo .= "<option selected value='$type[id]'>$type[item_type]</option>";
        } else {
            $echo .= "<option value='$type[id]'>$type[item_type]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_sections($institute,$select = FALSE,$print = TRUE) {
    $CI = & get_instance();
    $CI->load->model('model_assets');
    $sections = $CI->model_assets->get_sections($institute);
    $echo = "";
    foreach ($sections as $section) {
        $parent_child_section = array();
        $parent_child_section = $CI->model_assets->get_parent_sections($section['id']);
        if(isset($parent_child_section)) {
            $parent_child_section = array_column($parent_child_section, 'section');
            $section_name = implode(" -> ",$parent_child_section);
        } else {
            $section_name = $section['section'];
        }
        
        
        if ($select == $section['id']) {
            $echo .= "<option selected value='$section[id]'>$section_name</option>";
        } else {
            $echo .= "<option value='$section[id]'>$section_name</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function generate_indent_number($indent_array) {
    $CI= & get_instance();
    
    $indent_rules = get_option(array( 'option' => 'indent_no_generation_rule'));
    $rules = json_decode($indent_rules,TRUE);
    $fin_year = financial_year($indent_array['date']);
    $prefix = $rules['prefix'].$fin_year;
    $str_length = isset($rules['id_length']) && $rules['id_length'] ? $rules['id_length'] : 0 ;
        
    $CI->load->model('model_indent');
    $row = $CI->model_indent->get_indent_maxOf_max_id($indent_array['institute_id'],$prefix);
    if($row) {
        $next_number = $row['max_id']+1;
    } else {
         $next_number = 1;   
    }
    $s_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
    $indent_number['indent_number']= $prefix."-".$s_number;
    $indent_number['prefix_id'] = $prefix;
    $indent_number['max_id'] = $next_number;
    return $indent_number;
}

function generate_po_number($po_array) {
    $CI= & get_instance();
    
    $indent_rules = get_option(array( 'option' => 'po_no_generation_rule'));
    $rules = json_decode($indent_rules,TRUE);
    $fin_year = financial_year($po_array['date']);
    $prefix = $rules['prefix'].$fin_year;
    $str_length = isset($rules['id_length']) && $rules['id_length'] ? $rules['id_length'] : 0 ;
        
    $CI->load->model('model_purchase_order');
    $row = $CI->model_purchase_order->get_po_maxOf_max_id($po_array['institute_id'],$prefix);
    if($row) {
        $next_number = $row['max_id']+1;
    } else {
         $next_number = 1;   
    }
    $s_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
    $po_number['po_number']= $prefix."-".$s_number;
    $po_number['prefix_id'] = $prefix;
    $po_number['max_id'] = $next_number;
    return $po_number;
}

function echo_institute_items($institute,$item_category=FALSE,$select = FALSE,$asset = FALSE,$print = TRUE){
    
    $CI = & get_instance();
    $CI->load->model('model_store_item');
    if(isset($asset) && $asset==true) {
        $items = $CI->model_store_item->get_item_by_institute_or_category($institute,$item_category,$asset);
    } elseif(isset($item_category) && $item_category){
        $items = $CI->model_store_item->get_item_by_institute_or_category($institute,$item_category);
    }else{
        $items = $CI->model_store_item->get_item_by_institute_or_category($institute);
    }
    $echo = "";
    foreach ($items as $item) {
        $item_info = "";
        $item_code = "";
        if($item['item_code']) {
            $item_code .= $item['item_code']." - ";
        }
        if($item['name']) {
                $item_info .= " - ".$item['name'];
            }if($item['model_no']) {
                $item_info .= " - ".$item["model_no"];
            }
        if ($select == $item['id']) {
            $echo .= "<option selected value='$item[id]' rel='$item[item_name]$item_info'>$item_code$item[item_name]$item_info</option>";
        } else {
            $echo .= "<option value='$item[id]' rel='$item[item_name]$item_info'>$item_code$item[item_name]$item_info</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

 function echo_generate_asset_serial_no($institute,$no_of_items) {
        $CI = & get_instance();
        
        if(isset($institute) && isset($no_of_items) && $no_of_items>0) {
            $CI->load->model('model_assets');
            $max_id = $CI->model_assets->get_max_serial_number($institute);
            $max_asset_id = isset($max_id['max_serial_no'])?($max_id['max_serial_no'] + 1):1;
            $serial_nos = array();
            $i=1;
            while ($i <= $no_of_items) {
                $serial_no = $max_asset_id;
                $check_serial_no = $CI->model_assets->check_serial_no_availability($institute,$serial_no);
                    
                    if($check_serial_no == TRUE) {
                        $serial_nos[] = $serial_no;
                        
                        $i++;
                    } 
                $max_asset_id = $max_asset_id+1;
            }
            
            
            $serial_no = implode(",",$serial_nos);
            echo $serial_no;
        }
        
    }
    
//    function echo_sections($institute,$select = FALSE,$print = TRUE) {
//        $CI = & get_instance();
//        $CI->load->model('model_assets');
//        $echo = "";
//        $sections = $CI->model_assets->get_sections($institute);
//        $parent_child_sections = buildtree($sections);
//         //var_dump($parent_child_sections);die;
//        foreach ($parent_child_sections as $key=>$value) {
//            $echo .= "<option value='$key'>$value[section]</option>";
//        }
//        echo $echo;
//    }
//
//function buildtree($sections, $parent_id = 0, $tree = array())
//{
//    foreach($sections as $idx => $row)
//    {
////        if($row['parent_id'] == $parent_id)
////        {
//            foreach($row as $k => $v)
//                $tree[$row['id']][$k] = $v;
//            unset($sections[$idx]);
//            $tree[$row['id']]['children'] = buildtree($sections, $row['id']);
//            
//       // }
//    }
//   
//    ksort($tree);
//    return $tree;
//}

function fetch_recursive($sections, $currentid, $parentfound = false, $cats = array())
{
    foreach($sections as $row)
    {
        if((!$parentfound && $row['id'] == $currentid) || $row['parent_id'] == $currentid)
        {
            $rowdata = array();
            foreach($row as $k => $v)
                $rowdata[$k] = $v;
            $cats[] = $rowdata;
            if($row['parent_id'] == $currentid)
                $cats = array_merge($cats, fetch_recursive($sections, $row['id'], true));
        }
    }
    return $cats;
}

function send_item_owner_sms($owner_sms){
   
    $CI = & get_instance();
    $CI->load->model('model_employee');
    $CI->load->model('model_students');
    $CI->load->model('model_message');
    $CI->load->model('model_institutes');
    $inventory_sms = get_option(array('option' => 'inventory_item_owner_sms',
        'institute_id' => $owner_sms['institute_id']));

    $sms_rules = json_decode($inventory_sms, TRUE);
    

    if (isset($sms_rules) && $sms_rules['inventory_sms']==1 && $sms_rules['template_id']) {
       $institute_info = $CI->model_institutes->get_institute_by_id($owner_sms['institute_id']);
       $institute_name = $institute_info['name'];
       $owners = $owner_sms['owners'];
       
       $search_words = array("{{NAME}}","{{ITEMNAME}}","{{INSTITUTENAME}}");
       $check_search_words_in_msg= get_string_contain_words_in_array($sms_rules['message'],$search_words);
//           
       
        foreach ($owners as $owner) {
            $inv_message = $sms_rules['message'];
            $phone_no = array();
            if($owner['owner_type']=='employee') {
                $employee = $CI->model_employee->get_employee($owner['owner_id']);
                $phone_no[] = $phone_number = $employee['mobile'];
                $name = $employee['full_name'];
                $code = $employee['employee_number'];
            } else if($owner['owner_type']=='student') {
                $student = $CI->model_students->get_student($owner['owner_id']);
                $phone_no[] = $phone_number = $student['student_phone_number'];
                $name = $student['full_name'];
                $code = $student['admission_no'];
            } else if($owner['owner_type']=='parent') {
                $parent = $CI->model_students->get_student($owner['owner_id']);
                $phone_no[] = $phone_number = $parent['parent_mobile'];
                $name = $parent['parent_name'];
                $code = $parent['admission_no'];
            }
            
            if(isset($phone_no) && $phone_no) {
                             
             if(in_array("{{NAME}}", $check_search_words_in_msg)){
                 $inv_message = str_replace('{{NAME}}',$name, $inv_message);
             }
              if(in_array("{{ITEMNAME}}", $check_search_words_in_msg)){
                 $inv_message = str_replace('{{ITEMNAME}}',$owner_sms['item_name'], $inv_message);
             }
             
             if(in_array("{{INSTITUTENAME}}", $check_search_words_in_msg)){
                 $inv_message = str_replace('{{INSTITUTENAME}}',$institute_name, $inv_message);
             }
             
              
            $message = $inv_message;

            // provider details
            $sms_provider = get_option(array('option' => 'sms_provider','institute_id' => $owner_sms['institute_id']));
            $sms_provider_details = json_decode($sms_provider,TRUE);


            $sms_options = array();
            if (isset($sms_provider_details['provider'])) {
                $sms_options['provider'] = $sms_provider_details['provider'];
            }
            if (isset($sms_provider_details['username'])) {
                $sms_options['username'] = $sms_provider_details['username'];
            }
            if (isset($sms_provider_details['password'])) {
                $sms_options['password'] = $sms_provider_details['password'];
            }
            if (isset($message_options['type'])) {
                $sms_options['type'] = $message_options['type'];
            } else {
                $sms_options['type'] = $sms_provider_details['type'];
            }
            if (isset($message_options['senderid'])) {
                $sms_options['senderid'] = $message_options['senderid'];
            } else {
                $sms_options['senderid'] = $sms_provider_details['senderid'];
            }

            $sms_result = send_sms($message,$phone_no,$sms_options,$sms_rules['template_id']);
            if ($sms_result) {
                $add_inventory_sms['sender_user_id'] = get_logged_userid();
                $add_inventory_sms['sender_institute_id'] = get_logged_user_institute_id();
                $add_inventory_sms['body'] = $message;
                $add_inventory_sms['sms'] = 1;
                $add_inventory_sms['date_time'] = current_date_time();
                if (isset($sms_result['transaction_id'])) {
                    $add_inventory_sms['sms_transaction_id'] = $sms_result['transaction_id'];
                }
                $add_inventory_sms['provider'] = $sms_result['provider'];
                $add_inventory_sms['system'] = 0;

                //receiver code
                $receiver_code_array = array();
                $receiver_code = "ind_".$owner['owner_type']."_".$code;
                $receiver_code_array[] = $receiver_code;
                
                $add_inventory_sms['receiver'] = json_encode($receiver_code_array);

                $CI->load->model('model_message');
                $message_id = $CI->model_message->create_message($add_inventory_sms);
                if (isset($sms_result['ind_wise_transaction_id'])) {
                    $transaction_id = $sms_result['ind_wise_transaction_id'];
                    $transaction_ids = explode(",", $transaction_id);
                }

                $sms_receivers = array();
                //receiver student 
                $sms_receivers[0]['message_id'] = $message_id;
                $sms_receivers[0]['receiver_id'] = $owner['owner_id'];
                $sms_receivers[0]['receiver_type'] = $owner['owner_type'];
                $sms_receivers[0]['phone_number'] = $phone_number;
                if (isset($sms_result['ind_wise_transaction_id'])) {
                    $sms_receivers[0]['sms_transaction_id'] = $transaction_ids[0];
                }
                

                $CI->model_message->create_message_receivers($sms_receivers);
            }
            
         }

       }
      
    }
    
}


function send_item_receiver_sms($receiver_sms){
   
    $CI = & get_instance();
    $CI->load->model('model_employee');
    $CI->load->model('model_students');
    $CI->load->model('model_message');
    $CI->load->model('model_institutes');
    $inventory_sms = get_option(array('option' => 'inventory_issue_item_sms',
        'institute_id' => $receiver_sms['institute_id']));

    $sms_rules = json_decode($inventory_sms, TRUE);

    if (isset($sms_rules) && $sms_rules['inventory_issue_item_sms']==1 && $sms_rules['template_id']) {
       $institute_info = $CI->model_institutes->get_institute_by_id($receiver_sms['institute_id']);
       $institute_name = $institute_info['name'];
       $receivers = $receiver_sms['receivers'];
       
       $search_words = array("{{NAME}}","{{ITEMNAME}}","{{INSTITUTENAME}}");
       $check_search_words_in_msg= get_string_contain_words_in_array($sms_rules['message'],$search_words);
//           
        foreach ($receivers as $receiver) {
            $inv_message = $sms_rules['message'];
            $phone_no = array();
            if($receiver['receiver_type']=='employee') {
                $employee = $CI->model_employee->get_employee($receiver['receiver_id']);
                $phone_no[] = $phone_number = $employee['mobile'];
                $name = $employee['full_name'];
                $code = $employee['employee_number'];
            } else if($receiver['receiver_type']=='student') {
                $student = $CI->model_students->get_student($receiver['receiver_id']);
                $phone_no[] = $phone_number = $student['student_phone_number'];
                $name = $student['full_name'];
                $code = $student['admission_no'];
            } else if($receiver['receiver_type']=='parent') {
                $parent = $CI->model_students->get_student($receiver['receiver_id']);
                $phone_no[] = $phone_number = $parent['parent_mobile'];
                $name = $parent['parent_name'];
                $code = $parent['admission_no'];
            }
            
            if(isset($phone_no) && $phone_no) {
                             
             if(in_array("{{NAME}}", $check_search_words_in_msg)){
                 $inv_message = str_replace('{{NAME}}',$name, $inv_message);
             }
              if(in_array("{{ITEMNAME}}", $check_search_words_in_msg)){
                 $inv_message = str_replace('{{ITEMNAME}}',$receiver_sms['item_name'], $inv_message);
             }
             if(in_array("{{INSTITUTENAME}}", $check_search_words_in_msg)){
                 $inv_message = str_replace('{{INSTITUTENAME}}',$institute_name, $inv_message);
             }
             
              
            $message = $inv_message;
            
            // provider details
            $sms_provider = get_option(array('option' => 'sms_provider','institute_id' => $receiver_sms['institute_id']));
            $sms_provider_details = json_decode($sms_provider,TRUE);


            $sms_options = array();
            if (isset($sms_provider_details['provider'])) {
                $sms_options['provider'] = $sms_provider_details['provider'];
            }
            if (isset($sms_provider_details['username'])) {
                $sms_options['username'] = $sms_provider_details['username'];
            }
            if (isset($sms_provider_details['password'])) {
                $sms_options['password'] = $sms_provider_details['password'];
            }
            if (isset($message_options['type'])) {
                $sms_options['type'] = $message_options['type'];
            } else {
                $sms_options['type'] = $sms_provider_details['type'];
            }
            if (isset($message_options['senderid'])) {
                $sms_options['senderid'] = $message_options['senderid'];
            } else {
                $sms_options['senderid'] = $sms_provider_details['senderid'];
            }

            $sms_result = send_sms($message,$phone_no,$sms_options,$sms_rules['template_id']);
            if ($sms_result) {
                $add_inventory_sms['sender_user_id'] = get_logged_userid();
                $add_inventory_sms['sender_institute_id'] = get_logged_user_institute_id();
                $add_inventory_sms['body'] = $message;
                $add_inventory_sms['sms'] = 1;
                $add_inventory_sms['date_time'] = current_date_time();
                if (isset($sms_result['transaction_id'])) {
                    $add_inventory_sms['sms_transaction_id'] = $sms_result['transaction_id'];
                }
                $add_inventory_sms['provider'] = $sms_result['provider'];
                $add_inventory_sms['system'] = 0;

                //receiver code
                $receiver_code_array = array();
                $receiver_code = "ind_".$receiver['receiver_type']."_".$code;
                $receiver_code_array[] = $receiver_code;
                
                $add_inventory_sms['receiver'] = json_encode($receiver_code_array);

                $CI->load->model('model_message');
                $message_id = $CI->model_message->create_message($add_inventory_sms);
                if (isset($sms_result['ind_wise_transaction_id'])) {
                    $transaction_id = $sms_result['ind_wise_transaction_id'];
                    $transaction_ids = explode(",", $transaction_id);
                }

                $sms_receivers = array();
                //receiver student 
                $sms_receivers[0]['message_id'] = $message_id;
                $sms_receivers[0]['receiver_id'] = $receiver['receiver_id'];
                $sms_receivers[0]['receiver_type'] = $receiver['receiver_type'];
                $sms_receivers[0]['phone_number'] = $phone_number;
                if (isset($sms_result['ind_wise_transaction_id'])) {
                    $sms_receivers[0]['sms_transaction_id'] = $transaction_ids[0];
                }
                

                $CI->model_message->create_message_receivers($sms_receivers);
            }
            
            }

       }
      
    }
    
}

function get_institute_main_store($institute_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_store');
    $store = $CI->model_store->get_main_store_by_institute_id($institute_id);
    $main_store = "";
    if(isset($store) && $store) {
        $main_store = $store['id'];
    }
    return $main_store;
}


