<!doctype html>
<html>

    <head>
        <?php $this->load->view('include/header'); ?>
        
        <script src="<?php echo PUBLIC_URL; ?>/js/plugins/datatables/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="<?php echo PUBLIC_URL;?>/css/plugins/select2/select2.css">
        <script src="<?php echo PUBLIC_URL;?>/js/plugins/select2/select2.min.js"></script>
        <?php $this->load->view('include/datatable_export'); ?>
    </head>

    <body>

        <div class="container-fluid" id="content">
            <div id="main">
                <div class="container-fluid">
                    <div class="page-header">
                        <div class="pull-left">
                            <h1>Generate Invoice</h1>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6">
                             <?php   if (validation_errors()) { ?>
                                <div class="alert alert-danger alert-dismissable margin-top20">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <?php echo validation_errors(); ?>
                                </div>
                            <?php } 
                             if ($this->session->flashdata('error')) { ?>
                             <div class="alert alert-danger alert-dismissable margin-top20">
                                
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <?php echo $this->session->flashdata('error');?>
                                </div>
                           
                            <?php }  ?>
                                
                        </div>
                    </div>
                     <div class="row">
                       
                          <div class="col-sm-6 text-right">
                             
                              <div class="box">
                                <div class="box ">
                                <div class="box-title">
                                    <h3>
                                        FROM
                                    </h3>
                                </div>
                                <div class="box-content">
                                    <div class="form-group">
                                        <div class="col-sm-12 text-left">
                                            <p >Company ABC LMT</p>
                                            <!--<p class=" col-sm-4">Company ABC LMT</p></br>-->
                                        </div>     
                                    </div> 
                                </div>
                                </div>
                            </div>
                         </div>  
                         <div class="col-sm-6 text-right">
                             
                              <div class="box">
                                <div class="box ">
                                <div class="box-title">
                                    <h3>
                                        TO
                                    </h3>
                                </div>
                                <div class="box-content">
                                    <form action="" method="POST" class='form-horizontal form-validate' id="customer_create">   
                                        
                                        
                                       
                                        <div class="form-group">
                                            <label for="name" class="control-label col-sm-4">Customer Name</label>
                                            <div class="col-sm-8">
                                                 <input type="text" name="name"  class="form-control" data-rule-required="true" data-rule-minlength="2">
                                            </div>
                                        </div> 
                                        <div class="form-group">
                                            <label for="mail" class="control-label col-sm-4">Address</label>
                                                 <div class="col-sm-8">
                                                     <textarea  name="address" class="form-control"></textarea>
                                            </div>
                                        </div> 
                                        <div class="form-group">
                                            <label for="mail" class="control-label col-sm-4">Phone</label>
                                                 <div class="col-sm-8">
                                                 <input type="text" name="phone"  class="form-control" data-rule-required="true" data-rule-minlength="10">
                                            </div>
                                        </div> 
                                        
                                        <div class="form-actions">
                                            <input type="submit" class="btn btn-primary" value="ADD">
                                        </div>
                                    </form>
                                </div>
                                </div>
                            </div>
                         </div>  
                     </div>
                    
                    
                    
                    <div class="row">
                      <div class="col-sm-10">

                                <div class="box box-bordered">
                                    <div class="box-title">
                                        <h3>
                                            <i class="fa fa-th-list"></i>
                                            Generate Invoice
                                        </h3>

                                    </div>
                                    <div class="box-content">
                                            
                                       
                                           
                                          <form action="" method="POST"  class='form-vertical  form-validate' id="final_internals">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <div>
                                                       
                                                        <h4 class="panel-title">
                                                            <a style="text-decoration:underline" href="#p<?php // echo $fee_sem_counter; ?>" data-toggle="collapse" data-parent="#action_box">
                                                           
                                                                
                                                                Items 
                                                            </a>
                                                            
                                                        &nbsp;&nbsp;
                                                       
                                                            </h4>
                                                    </div>

                                                    <!-- /.panel-title -->
                                                </div>
                                                <!-- /.panel-heading -->
<!--                                                <div id="p<?php // echo $fee_sem_counter; ?>" class="panel-collapse collapse <?php // if ($counter == 0) echo 'in'; ?>">-->
                                                <div class="panel-body ind_assignment" >
                                                        <table class="table table-hover table-nomargin table-bordered ">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sl.No</th>
                                                                    <th>Item Name</th>
                                                                    <th>Quantity</th>
                                                                    <th>Price</th>
                                                                    <th>Total</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>

                                                        

                                                        <tbody>
                                                            <?php $i=1; 
                                                            $total_score=0;
                                                                $total_convert_score = 0;?>
                                                            <tr>
                                                                <td><?php echo $i; ?></td>
                                                                <td>
                                                                        <select name="item_name[]" class="form-control"> 
                                                                        <option value="">Select</option>
                                                                            <?php foreach ($items as $item){ ?>

                                                                                <option value="<?php echo $item['id']; ?>"   ><?php if($item['item_name']){ echo $item['item_name']; } ?></option>
                                                                           <?php  } ?>
                                                                    </select>
                                                                </td>
                                                                 
                                                                 <td>
                                                                        <input type="number" name="quantity[]" class="form-control quantity" >
                                                                </td>
                                                                <td>
                                                                        <input type="text" name="price[]" class="form-control price" value="" >
                                                                </td>
                                                                 <td>
                                                                        <input type="text" name="total[]" class="form-control total_price" >
                                                                </td>
                                                                 <td>
                                                                    
                                                                   <a href="javascript:void(0)" class="btn add_assignment" rel="tooltip" title="" data-original-title="Add More"><i class="fa fa-plus"></i></a>
                                                                    
                                                                </td>
                                                            </tr>
                                                            
                                                            <tr class="assignmnet_total">
                                                                <td colspan="2" style="text-align: center">Total</td>  
                                                                <td><label class=""></label></td>
                                                                <td></td>
                                                                <td><label class="assign_total"</label></td>
                                                               
                                                                
                                                            </tr>
                                                           
                                                            <?php $i++;
                                                            ?>
                                                        </tbody>

                                                        <?php

                                                    ?>
                                                </table>
                                            </div>
                                               
                                        </div>
                                            
                                              <div class="form-actions pull-left">
                                                    <button type="submit" class="btn btn-primary">Save Rule</button>
                                                </div>
                                          </form>
                                           
                                    </div>
                                </div>  
                
                </div>
                    </div>
                    
                    
                   
                </div>
            </div>
            </div>

       
        
         <script>
           
            $(document).ready(function() {
                
                row_count=<?php if(isset($i)){ echo $i; } else{ echo "0"; }?>;
                $("#main").on("click",".add_assignment", function () {
                
                var new_row ="";
                new_row += "<tr>";
                new_row +="<td>"+row_count+"</td>";
                new_row += "<td>";
                new_row += "<select name=\"item_name[]\" class=\"form-control\">"; 
                new_row += "<option value=\"\">Select</option>";
                <?php if(isset($items)){ foreach ($items as $item){ ?>

                new_row +="<option value=\"<?php echo $item['id']; ?>\"><?php echo $item['item_name']; ?></option>";
                <?php  }}
                ?>
                new_row += "</select>";
                new_row += "</td>";
                new_row += "<td>";
                new_row += "<input type=\"number\" name=\"quantity[]\" class=\"quantity form-control\">";
                new_row += "</td>";
                new_row += "<td>";
                new_row += "<input type=\"text\" name=\"price[]\" class=\"price form-control\">";
                new_row += "</td>";
                new_row += "<td>";
                new_row += "<input type=\"text\" name=\"total[]\" class=\"total_price form-control\">";
                new_row += "</td>";
                new_row += "<td>";
                new_row+="<a href=\"javascript:void(0)\" class=\"btn add_assignment\" rel=\"tooltip\" title=\"\" data-original-title=\"Add More\"><i class=\"fa fa-plus\"></i></a>"
                new_row += "</td>";
                new_row += "</tr>";
              
               row_count++;
              
               $(new_row).insertBefore('.assignmnet_total');
             
            });
              
                 $(document).on('keyup change blur', '.total_price',function () {

                var sum = 0;
                $(".total_price").each(function () {
                   if($(this).val()){
                    sum += parseFloat($(this).val());
                }
                });
                   if(sum){
                       total_f=sum;
                   }else{
                      total_f=0;
                   }
                $(".assign_total").html(total_f);
         
            });
//             $(document).on('keyup change blur', '.quantity',function () {
//
//                var sum = 0;
//                $(".quantity").each(function (i.el) {
//                   if($(this).val()){
//                    sum += $(el).val();
//                }
//                alert(sum);
//                });
//                
                
               $(document).on('keyup change blur', '.price',function () {
//alert();
//var v=0;
                    $(".quantity").each(function(){
                        var val = $('.price').val();
//                       return( $(this).html(parseFloat(q) * parseFloat($(".price").val())));
                       $(this).text( parseInt($(this).text()) * parseInt(val) );
//                       alert(v);
                       
                       
                       
//                       var total = 0;
//$(".quantity").each(function() {
//    total+= parseInt(this.value, 10) * parseInt($(this).attr("price"), 10);
//    alert(total);
//    var arr1 = [5, 3, 6, 8];
//var arr2 = [3, 7, 2, 5];
//var finalArr = [];
//for (var i = 0; i < arr1.length; i++) {
//    finalArr[i] = arr1[i] * arr2[i];
//}
//console.log(finalArr);
//
//
//
//
//array3= array1.map(function(e,i){return {value : (e.value * array2[i].value)}; }) ; 


});
//                       total+= parseInt(this.value, 10) * parseInt($(this).attr("name"), 10);
                    });
               
//                   if(sum){
//                       total_f=sum;
//                   }else{
//                      total_f=0;
//                   }
//                $(".assign_total").html(total_f);
         
//              $(".quantity").each(function(i,el){
//        var current_qty = $(el).val();
//        alert (current_qty);
    });      
         
   
        </script>
        <style>
            th, td { white-space: nowrap; }
            td a.btn { margin: 3px ; padding: 3px 5px; font-size: 10px;}
        </style>
    </body>
</html>