<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?php echo base_url();?>bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
   
  </head>
  <body class="hold-transition login-page">
    <div class="login-box">
        <?php if($this->session->flashdata('msg')!='') { ?>
        <div class="alert alert-<?php echo $this->session->flashdata('msg_type'); ?> alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <h4>Alert!</h4>
            <?php echo $this->session->flashdata('msg'); ?>
        </div>
        <?php } ?>
      
      <div class="login-box-body">
        <p class="login-box-msg">Sign in </p>
        <form action="" method="post" id="myform">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" placeholder="User Name" name="username"id="username" required="" />
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Password" name="password"id="password" required="" />
          </div>
          <div class="row">
            <div class="col-xs-12">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Log In</button>
            </div><!-- /.col -->
          </div>
        </form> 

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="<?php echo base_url();?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="<?php echo base_url();?>bootstrap/js/bootstrap.min.js"></script>
  
  </body>
</html>
