<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
} else {
    header("location: main/login");
} ?>




<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li class="active">Picrust Stamp</li>
            </ol>
            <h3 class="page-header">Picrust and Stamp</h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
          

          <div class="col-lg-12 uk-margin"></div>
        
     <?php echo form_open_multipart('Run_picrust_stamp/runqueue/'.$current_project);?>

         <div class="panel-body">
         <div class="panel-group" id="accordion">

             
        <div class="col-lg-8">
        <div class="form-group">
        <h4>PICRUST</h4>
         &nbsp;&nbsp; <label>• Default Please select level of KEGG pathway : </label>
        <label class="radio-inline">
            <input name="kegg"  value="1" type="radio">1
        </label>
        <label class="radio-inline">
             <input name="kegg"  value="2" checked type="radio">2
        </label>
        <label class="radio-inline">
              <input name="kegg"  value="3" type="radio">3
        </label>
        </div>
        </div>

       <div class="col-lg-12 uk-margin"></div>

        <div class="col-lg-6">
        <div class="form-group">
        <h4>STAMP</h4>
        </div>
        </div>  

        <div class="col-lg-7 col-lg-push-1">
        <div class="form-group">      
          <label>• Sample comparison : </label>
          <select class="uk-select" name="sample_comparison">
           <?php foreach ($samname as $val_sam) { ?>
           <option value="<?php echo $val_sam; ?>"> <?php echo $val_sam; ?></option>
           <?php } ?>
          </select>
        </div>
        </div>
         
        <div class="col-lg-7 col-lg-push-1">
        <div class="form-group">                                   
        <label>• Selected statistical test : </label>
            <select class="uk-select" name="statistical_test">
                <option value="Bootstrap">Bootstrap</option>
                <option value="Chi-square test">Chi-square test</option>
                <option value="Chi-square test(w/Yates' correction)">Chi-square test(w/Yates' correction)</option>
                <option value="Difference between proportions">Difference between proportions</option>
                <option selected value="Fisher 's exact test">Fisher 's exact test</option>
                <option value="G‐test">G‐test</option>
                <option value="G‐test (w/ Yates' correction)">G‐test (w/ Yates' correction)</option>
                <option value="Hypergeometric">Hypergeometric</option>
                <option value="Permutation">Permutation</option>
            </select>
        </div>
        </div>

        <div class="col-lg-7 col-lg-push-1">
        <div class="form-group">
            <label>• CI method : </label>
            <select class="uk-select" name="ci_method">
                 <option selected value="DP: Newcombe‐Wilson">DP: Newcombe‐Wilson</option>
                 <option value="DP: Asymptotic">DP: Asymptotic</option>
                 <option value="DP: Asymptotic‐CC">DP: Asymptotic‐CC</option>
                 <option value="OR: Haldane adjustment">OR: Haldane adjustment</option>
                 <option value="RP: Asymptotic">RP: Asymptotic</option>
            </select>
        </div>
        </div>

        <div class="col-lg-7 col-lg-push-1">
        <div class="form-group">
            <label>• P‐value : </label>
            <select class="uk-select" name="p_value">
                 <option value="None">None</option>
                 <option selected value="0.05">0.05</option>
                 <option value="0.01">0.01</option>
               
            </select>
        </div>
        </div>

        </div><!--class="panel-group" id="accordion"-->
        </div><!--class="panel-body"-->


        <div class="col-lg-10 col-lg-push-1 uk-margin">
            <button type="submit" class="btn btn-primary">
           	      Run Picrust Stamp
           	</button>
       </div>
          
      <?php echo form_close(); ?>

        </div>
    </div>
</div>



