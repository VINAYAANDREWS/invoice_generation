<!doctype html>
<html>

    <head>
        <?php $this->load->view('include/test_header'); ?>	
        <?php $this->load->view('include/datatable_export'); ?>
    </head>

    <body>

        <div class="container-fluid" id="content">
            <div id="main">
                <div class="container-fluid">
                    <div class="page-header">
                        <div class="pull-left">
                            <h1>Add Customer</h1>
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
                            <?php } 
                             
                            if($this->session->flashdata('msg')){ ?>
                               <div class="alert alert-success alert-dismissable margin-top20">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                   <?php echo $this->session->flashdata('msg');?>
                                </div>
                            
                            
                           <?php }
                             
                              if ($this->session->flashdata('error') == 'error') { ?>
                                <div class="alert alert-danger alert-dismissable margin-top20">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                   <?php echo "Something went wrong";?>
                                </div>
                            <?php }  ?>
                                
                        </div>
                    </div>
                        
                    <div class="row">
                       
                        <div class="col-sm-6">
                            <div class="box">
                                <div class="box box-bordered">
                                <div class="box-title">
                                    <h3>
                                        Add Customer
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
                                            <label for="mail" class="control-label col-sm-4">Email</label>
                                                 <div class="col-sm-8">
                                                 <input type="text" name="email"  class="form-control" data-rule-required="true" data-rule-minlength="2">
                                            </div>
                                        </div> 
                                        <div class="form-group">
                                            <label for="mail" class="control-label col-sm-4">Phone</label>
                                                 <div class="col-sm-8">
                                                 <input type="text" name="phone"  class="form-control" data-rule-required="true" data-rule-minlength="10">
                                            </div>
                                        </div> 
                                         <div class="form-group">
                                            <label for="mail" class="control-label col-sm-4">Address</label>
                                                 <div class="col-sm-8">
                                                     <textarea  name="address" class="form-control"></textarea>
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
                    <?php if (isset($customers)){ ?>
                            <div class="box box-color box-bordered">
                                <div class="box-title">
                                    <h3 class="table_title">
                                        <i class="fa fa-table"></i>
                                        Customer
                                    </h3>
                                </div>
                                <div style="overflow-x:auto; overflow-y: hidden;" class="box-content nopadding">
                                    
                                    <table class="table table-hover table-nomargin table-bordered " id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>Sl.No</th>
                                                <th> Name</th>
                                                <th>Email</th>
                                                <th> Phone</th>
                                                <th>Address</th>
                                                
                                            </tr>
                                        </thead>
                                            <tbody>
                                                <?php 
                                                if ( $customers){
                                                    $i = 1;
                                                    foreach ($customers as $customer) { ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $customer['name']; ?></td>
                                                        <td><?php echo $customer['email']; ?></td>
                                                        <td><?php echo $customer['phone']; ?></td>
                                                        <td><?php echo $customer['address']; ?></td>
                                                       
                                                        <?php } ?>
                                                        
                                                    </tr>
                                                    <?php 
                                                    
                                                    $i++;
                                                    
                                                        }  ?>

                                            </tbody>
                                    </table>
                                    
                                </div>
                            </div>
                    <?php } ?>
                </div>
            </div>
            </div>

       
        
         <script>
           
            $(document).ready(function() {
               
//                
              

                
                
                
            });
   
        </script>
        <style>
            th, td { white-space: nowrap; }
            td a.btn { margin: 3px ; padding: 3px 5px; font-size: 10px;}
        </style>
    </body>
</html>
