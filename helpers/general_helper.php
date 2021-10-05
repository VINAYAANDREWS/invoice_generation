<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function current_date_time() {
    $CI = & get_instance();
    $CI->load->helper('date');
    return date('Y-m-d H:i:s', now());
}

function to_check_date_format($date){
    
    if ($date) {
    
        list($da, $month, $year) = explode("/", $date);
                     
        $num_padded_da = str_pad($da, 2, "0", STR_PAD_LEFT);
                
        $num_padded_month = str_pad($month, 2, "0", STR_PAD_LEFT);
                       
        $num_padded_year = str_pad($year, 4, "20", STR_PAD_LEFT);
       
        
        $update_date = $num_padded_year."/".$num_padded_month."/".$num_padded_da;
        
        $get_date = to_date_format($update_date);
        return $get_date;
    } else {
        return NULL;
    }
    
}

function to_date_format($date) {
    if ($date) {
        $date = str_replace('/', '-', $date);        
        return date('Y-m-d', strtotime($date));
    } else {
        return NULL;
    }
}

function to_print_date_format($date) {
    if ($date && $date != '0000-00-00') {
        return date('d/m/Y', strtotime($date));
    } else {
        return NULL;
    }
}

function financial_year($date = false) {
    
    if ($date){
        
        $time=strtotime($date);
        $m=date("n",$time);
        $y=date("y",$time);
        
    } else {
        $y = date('y');
        $m = date('n');
    }
    if ($m >= 4) {
        $fn = $y . '/' . ($y + 1);
    } else {
        $fn = ($y - 1) . '/' . $y;
    }
    return $fn;
}


function financial_start_end_dates($date = false) {
    
    if ($date){
        
        $time=strtotime($date);
        $m=date("n",$time);
        $y=date("Y",$time);
        
    } else {
        $m = date('n');
        $y = date('Y');
    }
    if ($m >= 4) {
        $start_year = $y;
        $end_year = $y + 1;
    } else {
        $start_year = $y - 1;
        $end_year = $y;
    }
    
    $fn['start_date'] = $start_year.'-'.'04-01';
    $fn['end_date'] = $end_year.'-'.'03-31';
    
    return $fn;
}

function reverse_tax($amount) {
    $tax = get_option(array( 'option' => 'fee_tax'));
    
    $reverse_tax = ($amount * 100) / (100 + $tax);
    $data['tax'] = round(($amount - $reverse_tax),2);
    $data['amount'] = $amount - $data['tax'];
    return $data;
}

function convert_digit_to_words($number, $print = true) {

    $no = intval($number);
    $point = round($number - $no, 2) * 100;
    if ($point == 100) {
        $no = $no + 1;
        $point = 0;
    }

    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            //$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $plural = (($counter = count($str)) && $number > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
        } else
            $str[] = null;
    }
    $str = array_reverse($str);
    $result = implode('', $str);
        //$points = ($point) ?
        //"." . $words[$point / 10] . " " . 
        //      $words[$point = $point % 10] : '';
    if ($point) {
        $points = convert_digit_to_words($point, false);
    }
    if ($print) {
        if ($point) {
            return $result . "rupees  " . $points . " paise";
        } else {
            return $result . "rupees";
        }
    } else {
        return $result;
    }
}

function days_diff($from_date,$to_date)  { 
   
    $datediff = strtotime($to_date) - strtotime($from_date);
    $diff = floor($datediff / (60 * 60 * 24));  
    return $diff;
}

function checkDateFormat($date) {
    
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
        if(checkdate(substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4)))
            return true;
        else
            return false;
    } else {
        return false;
    }
}

function moneyFormat($amount){
    setlocale(LC_MONETARY, 'en_IN');
    if(is_numeric($amount))
    $amount = money_format('%!i', $amount);
    return $amount;
}

function get_day_name($date){
    $weekday = date('l', strtotime($date)); 
    return $weekday; 
}

function get_application_config_array($item_name) {
    
    $CI = &get_instance();
    return $CI->config->item($item_name);
}

function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
   
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

function get_date_time_from_datetime($datetime) {
    if ($datetime) {
        
        $datetime = str_replace('-', '/', $datetime); 
        $date_time['date'] = date('d/m/Y', strtotime($datetime)); 
        $date_time['time'] = date('H:i', strtotime($datetime));
        return $date_time;
    } else {
        return NULL;
    }
}

function echo_countries($select = false, $print = true) {
    
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_countries');
    $result = $CI->model_countries->get_countries();
    foreach ($result as $row) {
        if ($select == $row['id']) {
            $echo .= "<option selected value='$row[id]'>$row[name]</option>";
        } else {
            $echo .= "<option value='$row[id]'>$row[name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_states($country,$select = false,$print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_countries');
    $states = $CI->model_countries->get_states($country);

    foreach ($states as $state) {
        if($select == $state['id']){
            $echo .= "<option selected value='$state[id]'>$state[state_name]</option>";
        } else {
            $echo .= "<option value='$state[id]'>$state[state_name]</option>"; 
        }
    }  
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function get_date_plus_no_of_days_excluding_sunday($days,$date = false){
    
    $add_date = $date ? date('Y-m-d', strtotime($date. ' + '.$days.' days')) : date('Y-m-d', strtotime(' + '.$days.' days'));
    
    $name_of_day = date('D', strtotime($add_date));
    
    if ($name_of_day == "Sun") {
        
        $add_date = date('Y-m-d', strtotime($add_date. ' + 1 days'));
    } 
    
    return $add_date;
}

function echo_years($select_year = false) {
    
    $admission_year = get_option(array( 'option' => 'admission_year'));
    
    $admission_year_details = json_decode($admission_year, TRUE);
    
    $start_year = $admission_year_details['start_year'];
    
    $current_year = $admission_year_details['current_year'];
    
    if ($start_year) {
        $from_year = $start_year;
    } else {
        $from_year = date("Y",strtotime("-3 year"));
    }
    
    
    if ($current_year) {
        $next_year = $current_year + 3;
    } else {
        $next_year = date("Y",strtotime("+3 year"));
    }
    
    if ($select_year){
        $select = $select_year;
    } else if ($current_year) {
        $select = $current_year;
    } else {
        $select = date("Y");
    }
    
    $echo = '';
    foreach (range($from_year, $next_year) as $x) {
        
        if ($select && $x == $select) {
            $echo .= "<option value='$x' selected>$x</option>";
        } else {
            $echo .= "<option value='$x'>$x</option>";
        }
    }
    echo $echo;
}

function generateRandomString($length) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function remove_special_characters($string){
    
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '-', $string); // Removes special chars.
}

function to_db_date_format($date) {
    
    if ($date) {
        return date('Y-m-d', strtotime($date));
    } else {
        return NULL;
    }
    
}

//if ( ! function_exists( 'mime_content_type' ) ) {
//
//
//function mime_content_type( $filename ) {
//    $finfo = finfo_open( FILEINFO_MIME_TYPE );
//    $mime_type = finfo_file( $finfo, $filename );
//    finfo_close( $finfo );
//    return $mime_type;
//}
//}

if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

function change_date_format($date){
    
    if ($date) {
    
        list($da, $month, $year) = explode(".", $date);
                     
        $num_padded_da = str_pad($da, 2, "0", STR_PAD_LEFT);
        $num_padded_month = str_pad($month, 2, "0", STR_PAD_LEFT);
        $update_date = $year."/".$num_padded_month."/".$num_padded_da;
        
        $get_date = to_date_format($update_date);
        return $get_date;
    } else {
        return NULL;
    }
    
}

function number_to_words($num) {

    $ones = array(
        0 => "ZERO",
        1 => "ONE",
        2 => "TWO",
        3 => "THREE",
        4 => "FOUR",
        5 => "FIVE",
        6 => "SIX",
        7 => "SEVEN",
        8 => "EIGHT",
        9 => "NINE",
        10 => "TEN",
        11 => "ELEVEN",
        12 => "TWELVE",
        13 => "THIRTEEN",
        14 => "FOURTEEN",
        15 => "FIFTEEN",
        16 => "SIXTEEN",
        17 => "SEVENTEEN",
        18 => "EIGHTEEN",
        19 => "NINETEEN",
        "014" => "FOURTEEN"
    );
    $tens = array(
        0 => "ZERO",
        1 => "TEN",
        2 => "TWENTY",
        3 => "THIRTY",
        4 => "FORTY",
        5 => "FIFTY",
        6 => "SIXTY",
        7 => "SEVENTY",
        8 => "EIGHTY",
        9 => "NINETY"
    );
    $hundreds = array(
        "HUNDRED",
        "THOUSAND",
        "MILLION",
        "BILLION",
        "TRILLION",
        "QUARDRILLION"
    );
    $num = number_format($num, 2, ".", ",");
    $num_arr = explode(".", $num);
    $wholenum = $num_arr[0];
    $decnum = $num_arr[1];
    $whole_arr = array_reverse(explode(",", $wholenum));
    krsort($whole_arr, 1);
    $rettxt = "";
    foreach ($whole_arr as $key => $i) {

        while (substr($i, 0, 1) == "0")
            $i = substr($i, 1, 5);
        if ($i < 20) {
            /* echo "getting:".$i; */
            $rettxt .= $ones[$i];
        } elseif ($i < 100) {
            if (substr($i, 0, 1) != "0")
                $rettxt .= $tens[substr($i, 0, 1)];
            if (substr($i, 1, 1) != "0")
                $rettxt .= " " . $ones[substr($i, 1, 1)];
        }else {
            if (substr($i, 0, 1) != "0")
                $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
            if (substr($i, 1, 1) != "0")
                $rettxt .= " " . $tens[substr($i, 1, 1)];
            if (substr($i, 2, 1) != "0")
                $rettxt .= " " . $ones[substr($i, 2, 1)];
        }
        if ($key > 0) {
            $rettxt .= " " . $hundreds[$key] . " ";
        }
    }
    if ($decnum > 0) {
        $rettxt .= " and ";
        if ($decnum < 20) {
            $rettxt .= $ones[$decnum];
        } elseif ($decnum < 100) {
            $rettxt .= $tens[substr($decnum, 0, 1)];
            $rettxt .= " " . $ones[substr($decnum, 1, 1)];
        }
    }
    return $rettxt;
}

function date_to_words($date) {


    $date = explode('-', $date);
    
    if($date){
    $year = $date[0];
    $month = $date[1];
    $day = $date[2];
//    $Day = 3; 
//    $suffix= date("S", mktime(0, 0, 0, 0, $day, 0));
    $day = num_to_ordinal_word((int)$day);
    $year = number_to_words($year);
//    $monthNum = number_to_words($month);
    $monthNum = $month_name = date("F", mktime(0, 0, 0, $month, 10));

    return $day. ' ' . ucwords(strtolower($monthNum)) . ' ' . ucwords(strtolower($year));
    }else {
        return NULL;
    }
}
function num_to_ordinal_word($num)
{
        $first_word = array('eth','First','Second','Third','Fourth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Eleventh','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
        $second_word =array('','','Twenty','Thirty','Forty','Fifty');

        if($num <= 20){
                return $first_word[$num];
        }else{
        $first_num = substr($num,-1,1);
        $second_num = substr($num,-2,1);
        
        return $string = str_replace('y eth','ieth',$second_word[$second_num].' '.$first_word[$first_num]);
        }
}
	



