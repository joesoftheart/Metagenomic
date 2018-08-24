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
        <div class="col-lg-12 ">
           
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            
 <ol class="breadcrumb">
    <li <?php if($controller_name == 'main'){ echo "class=active";} ?> >
            
         <?php if ($controller_name == 'main') { ?> Home         
         <?php } else { ?>     
                 <a href="<?php echo site_url('main') ?>">Home</a> 
         <?php } ?>    
    </li>
    <li <?php if($controller_name == 'projects'){ echo "class=active"; }?>>
         <?php if ($controller_name == 'projects') { ?>  Current projects
         <?php } else { ?>
            <a href="<?php echo site_url('projects/index/' . $current_project) ?>">
               Current project
            </a>
        <?php } ?>  / Qiime 
   </li>
</ol>
          
     </div>
 </div>


<div class="row">
    <div class="col-lg-12">
    <div class="uk-child-width-1-6\@xl" uk-grid>
    <div>
        <ul class="uk-tab-right" uk-switcher="animation: uk-animation-fade" uk-tab>
 
        <li class="uk-active" >
                <a class="uk-text-capitalize uk-text-bold" href="1" onclick="advance_mode(this);">Qiime  </a>
        </li>
        </ul>

        <ul class="uk-switcher">

        <!-- qiime  -->
         <li>
         <link href="<?php echo base_url();?>tooltip/smart_wizard_theme_arrows.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/loading.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/tooltip.css" rel="stylesheet" />
         <script src="<?php echo base_url();?>tooltip/tooltip.js" type="text/javascript"></script>
       

         <div class="sw-theme-arrows">
             <ul class="nav-tabs step-anchor" uk-switcher="animation: uk-animation-fade">
                 <li class="pre"><a href="#">Step 1<br/> Qiime Process </a></li>
                 <li class="pre2"><a href="#">Step 2<br/> Result & Graph </a></li>
              
                
             </ul>
         
         <ul class="uk-switcher uk-margin">
         <!--pre -->
         <li>

    <link href="<?php echo base_url();?>tooltip/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="<?php echo base_url();?>tooltip/bootstrap-toggle.min.js"></script>
         <!-- .panel-group -->
         <div class="panel-group" id="accordion"></div>
    
             <div class="Pre-test">

                 <!-- /.row -->
                <div class="row">
                <div class="col-lg-12">
                 <!-- .panel-heading -->
                 <div class="panel-body">
                 <div class="panel-group" id="accordion">

                 <div class="panel panel-info">
                    <div class="panel-heading">          
                        <h4 class="panel-title">
                            <a  data-toggle="collapse" data-parent="#accordion" href="#collapse13" >1. Generate Map file 
                            <i class="fa fa-question-circle-o"></i>        
                            </a>
                        </h4>
                    </div>
                    <div id="collapse13" class="panel-collapse collapse">
                         <div class="panel-body"> 
                         <div class="col-lg-12 uk-margin"></div>  
                         <div class="col-lg-12">
                            <button class="btn btn-success" id="btnAddCol">
                            add group
                            </button>
                            <button class="btn btn-danger" id="btnRemoveCol">
                            remove group
                            </button>
                         </div> 
                         <div class="col-lg-12 uk-margin"> </div> 

                         <!--<form>-->
                         <div class="col-lg-12 table-responsive">
                         <form name="myform" id="myform" method="post">
                      
                         <table id="blacklistgrid">
                         <tr id="Row1">
                            <td><input type="text" value="#SampleID" readonly="readonly"/></td>
                            <td><input type="text" value="PrimerSequence" readonly="readonly"/></td>
                             <td><input type="text" value="ReversePrimer" readonly="readonly"/></td>
                             <td><input type="text" value="groupA" readonly="readonly"/></td>
                        </tr>

                        <?php  for($i = 0; $i < count($sampleName);$i++) { ?>
        
                         <tr id="Row2">
                             <td>
                             <input type="text" value="<?=$sampleName[$i]?>" readonly="readonly"/>
                             </td>
                             <td>
                             <input type="text" value="" />
                             </td>
                             <td>
                             <input type="text" value="" />
                             </td>
                             <td>
                             <input type="text" value="" />
                             </td>
                         </tr>

                        <?php } ?>

                         </table>
                        </form>
                        <!--</form>-->
                        </div> 

                <div class="col-lg-12 uk-margin">
                     <button class="btn btn-info" onclick="getExcel()">
                     create file
                    </button> 
                </div>
                    <div class="col-lg-10"> </div>
                    <div class="col-lg-5 ">
                        <p id="text_map"> </p>
                     </div> 
                     <div class="col-lg-2 col-lg-pull-1"> 
                        <img id="img_map"> 
                    </div>

                
                 </div>
                 </div>
                 </div>
                 </div>

            
             <form name="Pre-form" method="post" action="#" enctype="multipart/form-data">

                 <input type="hidden" name="username" value="<?= $username ?>">
                 <input type="hidden" name="project" value="<?= $current_project ?>">
                 <input type="hidden" name="chkmap" id="chkmap" value="nomap">


                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse15"> 2. Select Group core diversity analysis 
                             <i class="fa fa-question-circle-o"></i>  
                            </a> 
                        </h4>
                    </div>
                    <div id="collapse15" class="panel-collapse collapse">
                        <div class="panel-body">
                          <p class="fa fa-cog col-lg-11"> Group core diversity analysis </p>
                            <div class="col-lg-3 ">
                            <div class="form-group">
                                 <select class="uk-select" name="core_group" id="core_group" >
                                 </select>
                            </div>
                            </div> 
                            <div class="col-lg-8"><p class="opt1" style="display: none;"> warning : This group can not use for the next statisical analysis in below option.</p></div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-info">
                    <div class="panel-heading">          
                        <h4 class="panel-title">
                            <a  data-toggle="collapse" data-parent="#accordion" href="#collapse16" >3. Beta diversity index 
                            <i class="fa fa-question-circle-o"></i>        
                            </a>
                         </h4>
                    </div>
                    <div id="collapse16" class="panel-collapse collapse">
                        <div class="panel-body">    
                            
                            <div class="col-lg-6"> 
                            <div class="form-group">
                                <label>Non-phylogenetic metrics</label>
                                <select class="uk-select" name="beta_diversity_index" id="bdi1" >
                                    <option value="none">None</option>
                                    <option value="abund_jaccard">abund_jaccard</option>
                                    <option value="binary_lennon">binary_lennon</option>
                                    <option value="binary_sorensen_dice">binary_sorensen_dice</option>
                                    <option value="bray_curtis">bray_curtis</option>
                                    <option value="morisita_horn">morisita_horn</option>
                                 </select>
                            </div>

                            <div class="form-group">
                                <label>Phylogenetic metrics</label>
                                <select class="uk-select" name="beta_diversity_index2" id="bdi2" >
                                    <option value="none">None</option>
                                    <option value="unweighted_unifrac">unweighted_unifrac</option>
                                    <option value="weighted_unifrac">weighted_unifrac</option>
                                 </select>
                            </div>                                      
                            </div>    
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse14"> Options 
                             <i class="fa fa-question-circle-o"></i>  
                            </a> 
                        </h4>
                    </div>
                     <div id="collapse14" class="panel-collapse collapse">
                        <div class="panel-body">

                            <fieldset class="optionset1" disabled>
                            <p class="fa fa-cog col-lg-11"> Permanova</p>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                <select class="uk-select" name="permanova" id="permanova">
                                 </select>
                                </div>
                            </div>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                <select class="uk-select" name="opt_permanova" >
                                    <option value="none">none</option>
                                    <option value="weight">weight</option>
                                    <option value="unweight">unweight</option>
                                </select>
                                </div>
                            </div>
                            <p class="fa fa-cog col-lg-11"> Anosim</p>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                    <select class="uk-select" name="anosim" id="anosim"> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                <select class="uk-select" name="opt_anosim" >
                                    <option value="none">none</option>
                                    <option value="weight">weight</option>
                                    <option value="unweight">unweight</option>
                                </select>
                                </div>
                            </div>
                            <p class="fa fa-cog col-lg-11"> Adonis</p>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                    <select class="uk-select" name="adonis" id="adonis">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                <select class="uk-select" name="opt_adonis" >
                                    <option value="none">none</option>
                                    <option value="weight">weight</option>
                                    <option value="unweight">unweight</option>
                                </select>
                                </div>
                            </div>
                            </fieldset>

                             <div class="col-lg-12 uk-margin">
                               <p class="fa fa-cog"> PICRUSt  and STAMP :</p>  
                                <input id="toggle-event2" type="checkbox" data-toggle="toggle" data-size="small" data-onstyle="success" data-offstyle="danger" >
                             </div> 

                             <div class="col-lg-10">
                             <fieldset class="optionset2" disabled>

                            <div class="col-lg-10 col-lg-push-1">
                            <div class="form-group">
                            <label class="col-lg-8 col-lg-pull-1 uk-margin">PICRUSt</label>
                            <div class="col-lg-12">
                             <label>• Default Please select level of KEGG pathway : </label>
                            <label class="radio-inline">
                                <input name="kegg"  value="L1" type="radio">1
                            </label>
                            <label class="radio-inline">
                            <input name="kegg"  value="L2" checked type="radio">2
                             </label>
                            </div>
                            </div>
                            </div>

                            <div class="col-lg-12 uk-margin"></div>

                            <div class="col-lg-10 col-lg-push-1">
                            <div class="form-group">
                            <label class="col-lg-8 col-lg-pull-1 uk-margin">STAMP</label>
                            </div>
        
                            <div class="col-lg-10 ">
                            <div class="form-group">      
                             <label>• Sample comparison : </label>

                            <select class="uk-select" name="sample_comparison" id="sample_name">
         
                            </select>
                             </div>
                            <p class="opt2" style="display: none;"> <font color="red">*Required</font></p>
                            </div>
         
                            <div class="col-lg-10 ">
                            <div class="form-group">                                   
                            <label>• Selected statistical test : </label>
                            <select class="uk-select" name="statistical_test">
                                <option value="0"></option>
                                <option value="Bootstrap">Bootstrap</option>
                                <option value="Chi-square">Chi-square test</option>
                                <option value="Chi-square2">Chi-square test(w/Yates' correction)</option>
                                <option value="Difference">Difference between proportions</option>
                                <option value="Fisher">Fisher 's exact test</option>
                                <option value="G‐test">G‐test</option>
                                <option value="G‐test2">G‐test (w/ Yates' correction)</option>
                                <option value="Hypergeometric">Hypergeometric</option>
                                <option value="Permutation">Permutation</option>
                            </select>
                            </div>
                            <p class="opt2" style="display: none;"> <font color="red">*Required</font></p>
                            </div>

                            <div class="col-lg-10 ">
                                <div class="form-group">
                                <label>• CI method : </label>
                                <select class="uk-select" name="ci_method">
                                    <option value="0"></option>
                                    <option value="DP1">DP: Newcombe‐Wilson</option>
                                    <option value="DP2">DP: Asymptotic</option>
                                    <option value="DP3">DP: Asymptotic-CC</option>
                                    <option value="OR1">OR: Haldane adjustment</option>
                                    <option value="RP1">RP: Asymptotic</option>
                                </select>
                                </div>
                                <p class="opt2" style="display: none;"> <font color="red">*Required</font></p>
                             </div>

                            <div class="col-lg-10 ">
                            <div class="form-group">
                                <label>• P‐value : </label>
                            <select class="uk-select" name="p_value">
                                <option value="0">None</option>
                                <option value="0.05">0.05</option>
                                <option value="0.01">0.01</option>
                            </select>
                            </div>
                             <p class="opt2" style="display: none;"> <font color="red">*Required</font></p>
                            </div>
                            </div>
                            </fieldset>
                            </div>

                         </div>
                         <!-- panel-body -->
                    </div>
                 </div>
                </div> 
               
                
                 <div class="col-lg-12 uk-margin"></div>
                 <div class="row">
                 <div class="col-lg-1"></div>
                 <div class="col-lg-6">
                <input id="sub-test" class="btn btn-primary" value="Run Preprocess">
                </div>
                </div>

            </form><!-- close row form -->


                

                 </div>  <!-- /.col-lg-12 -->
                 </div><!-- /.row -->
                
                 
             </div> <!-- Pre-test -->


             <div class="row">
                 <div class="col-lg-11 ">
                    <div class="Pre-show" style="display:none"> 
                    <div class="loader">
                          <p class="h1"> Qiime Process</p>
                          <span></span>
                          <span></span>
                          <span></span>
                    </div>
                    <div class="col-lg-5 col-lg-push-1 "> <b>Status : </b></div>
                    <div class="col-lg-5 col-lg-pull-3" id="test_run">Wait Queue</div>
                    
                     <div class="col-lg-11 col-lg-push-1 uk-margin">
                     <div class="progress progress-striped active">
                         <div id="bar_pre" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                               <div class="percent_pre">0%</div>
                         </div>
                     </div>
                      
                     </div> 
                     </div> 
                 </div> 
             </div>
             
    </li>
    <!--End pre -->


     <!--pre2-->
     <li>

         <div class="Pre-test2">
           
           

            <hr class="uk-divider-icon">
            <div class="panel-body">       
            <div class="row">
            <div class="col-lg-6 col-lg-offset-3">
       
            <div class="alert alert-info">
            <center>
             
            <a href="<?php echo site_url('qiime_report/graph_qiime');?>" target="_blank">
             <button type="button" class="btn btn-warning  btn-circle btn-xl" id="qiime_report">
                <i class="fa fa-file-image-o"></i>
              </button>
            </a>  
             <h4>Graph</h4>

            </center>
            </div>    
            </div>
            </div>
            </div>


             <hr class="uk-divider-icon">
            <div class="panel-body">       
            <div class="row">
            <div class="col-lg-6 col-lg-offset-3">
       
            <div class="alert alert-info">
            <center>


            <a href="<?php echo site_url('run_qiime/graph_qiime_full');?>" target="_blank">
              <button type="button" class="btn btn-success  btn-circle btn-xl">
                <i class="fa fa-file-image-o"></i>
              </button>
            </a>
            <h4>Graph Qime</h4>

                

            </center>
            </div>    
            </div>
            </div>
            </div>

             <hr class="uk-divider-icon">
             <div class="panel-body">
             <div class="row">
             <div class="col-lg-6 col-lg-offset-3">

             <div class="alert alert-info">
             <center>

                  <button type="button" class="btn btn-info btn-circle btn-xl" id="btn_reports">
                   <i class="fa fa-file-word-o"></i>
                 </button>
                 <h4>  Report  </h4>

             </center>
            
             </div>    
             </div>
             </div>
             </div>

         </div> <!-- Pre-test2 -->
     </li>
     <!--End pre2-->
     </ul> 
     <!-- class="uk-switcher uk-margin" -->

    </div>
    </li>
    <!-- qiime  -->

    </ul>
    <!-- end class="uk-switcher" -->  

    </div>
    </div>
    </div>
</div>
 <script>

    $('#core_group').change(function(){
        var data_name = document.getElementById('core_group').value;
        if(data_name != "none"){
           on_switch(data_name);
        }else{$(".optionset1").attr("disabled", true);}
        
    });

  
     var console_event2 = false;
     $('#toggle-event2').change(function(){
        console_event2 = $(this).prop('checked');
        if($(this).prop('checked')){
          $(".optionset2").removeAttr("disabled")
          $(".opt2").show();
        }else{
          $(".optionset2").attr("disabled", true);
          $(".opt2").hide();
        }
     });



    function getExcel() {

                        var user = "<?php echo $username ?>";
                        var project = "<?php echo $current_project ?>";
                        var excel = "",  check_val = "";

                        $("#blacklistgrid").find("tr").each(function () {
                            var sep = "";
                            $(this).find("input").each(function () {
                                excel += sep + $(this).val();
                                check_val += $(this).val() + sep;
                                sep = "\t";
                            });
                            excel += "\n";
                        });
                        var count = true;
                        var res = check_val.split("\t");
                        for (var i = 0; i < res.length - 1; i++) {
                             if (res[i] == "") {count = false; }
                         }
                         if (count == false) {alert("Please insert value");
                        } else {
                        $.ajax({
                            type: "post",
                            datatype: "json",
                            url: "<?php echo base_url('Run_qiime/genmap');?>/" + user + "/" + project,
                            data: {data_excel: excel},
                            success: function (data) {
                                var user_file = $.parseJSON(data);
                                alert("Create map " + user_file + " success");
                                check_map();
                            }, error: function (e) {
                                console.log(e.message);
                            }
                        });
                        }
     }

        function check_map(){
                         var user = "<?=$username ?>";
                         var project = "<?=$current_project ?>";
                         var data_arr = new Array(user,project);
                         var time = 2;
                         var interval = null;
                             interval = setInterval(function(){   
                             time--;
           
                        if(time === 0){
                        $.ajax({ 
                        type:"post",
                        datatype:"json",
                        url:"<?php echo base_url('Run_qiime/check_map_file');?>/"+user+"/"+project,
                        success:function(data){
                        var map = JSON.parse(data);
                             if(map == 'Noerror'){
                                 clearInterval(interval);
                                 getGroup(data_arr);  
                                 $('#text_map').text("No errors or warnings found in mapping file.");
                                 $('#img_map').attr("src","<?php echo $src;?>"); 
                                 document.getElementById('chkmap').value = map;
                             }
                             else{ time = 3;  } 
                        }
                    });
                }

             },1000);      
        }
</script>

  

<script type="text/javascript">  

$(document).ready(function (){ 

        var myform = $('#myform');
        var col_num = 4;
        var alphas = '';
        $('#btnAddCol').click(function () {
            myform.find('tr').each(function () {
                var trow = $(this);
                var numdel = col_num-3;
                alphas = String.fromCharCode(numdel + 65);
                if (trow.index() === 0) {
                    trow.append('<td><input type="text" readonly="readonly" value=group'+alphas+' /></td>');
                } else {
                    trow.append('<td><input type="text" /></td>');
                }
            });
            col_num += 1;
        });
        $('#btnRemoveCol').click(function (){
            var column_count = $('#blacklistgrid #Row1 td').length;
            if (column_count > 4) {
                $('table tr').find('td:eq(-1),th:eq(-1)').remove();
                col_num -= 1;
            }
        });



      /*  check status run */

       var status = "<?=$status?>";
       var step_run = "<?=$step_run?>";
       var id_job = "<?=$id_job?>";
       var current = "<?=$current?>";
       if(status != "null"){
          var send_data = new Array(id_job,current);
          if(step_run == "1"){
                alert("Preprocess"); 
                $('li.pre').attr('id','active');
                $(".Pre-test").hide();
                $(".Pre-show").show();
                $('#test_run').html('Checking Run Qiime');
                checkrun(send_data);

          }else if(step_run == "2"){
              alert("Result & Graph"); 
              $('li.pre').attr('id','done');
              //$('li.pre2').attr('id','active');
              $('.sw-theme-arrows > .nav-tabs > .pre').next('li').find('a').trigger('click'); 
          }
       }else{

           $('li.pre').attr('id','active');
       }  

     /* end check status run  */
       
     /* submit run process */  
        $("#sub-test").click(function () {               
                var username = document.forms["Pre-form"]["username"].value;
                var project  = document.forms["Pre-form"]["project"].value;
                var chkmap = document.forms["Pre-form"]["chkmap"].value;
                var permanova = document.forms["Pre-form"]["permanova"].value;
                var opt_permanova = document.forms["Pre-form"]["opt_permanova"].value;
                var anosim = document.forms["Pre-form"]["anosim"].value;
                var opt_anosim = document.forms["Pre-form"]["opt_anosim"].value;
                var adonis = document.forms["Pre-form"]["adonis"].value;
                var opt_adonis = document.forms["Pre-form"]["opt_adonis"].value;

                var core_group = document.forms["Pre-form"]["core_group"].value;

                var kegg = document.forms["Pre-form"]["kegg"].value;
                var sample_comparison = document.forms["Pre-form"]["sample_comparison"].value;
                var statistical_test =  document.forms["Pre-form"]["statistical_test"].value;
                var ci_method = document.forms["Pre-form"]["ci_method"].value;
                var p_value = document.forms["Pre-form"]["p_value"].value; 

                var beta_diversity_index = document.forms["Pre-form"]["beta_diversity_index"].value;
                var beta_diversity_index2 = document.forms["Pre-form"]["beta_diversity_index2"].value;
                 
                var array_data = new Array(username,project,chkmap,permanova,opt_permanova,anosim,opt_anosim,adonis,opt_adonis,core_group,kegg,sample_comparison,statistical_test,ci_method,p_value,beta_diversity_index,beta_diversity_index2);

                var open_opt = null;
                if(console_event2){
                    if(sample_comparison != "0" && statistical_test !="0" && ci_method != "0" &&   p_value !="0" ){ 
                            open_opt = true;
                    }else{
                            open_opt = false;
                    }
                }

                /* check parameter run   */
                 if(console_event2 && open_opt){
                    if(chkmap == 'Noerror'){
                         // alert("open");
                        $(".Pre-test").hide();
                        $(".Pre-show").show();
                        getvalue(array_data);
                    }      
                        
                 }else if(!console_event2){
                    if(chkmap == 'Noerror'){
                        // alert("close");
                        $(".Pre-test").hide();
                        $(".Pre-show").show();
                        getvalue(array_data);
                    }      
                 }else{
                        alert("Please select all stamp");
                 }
      
        });    
     /*  end submit run process   */   
 });


function getGroup(data_arr){

     var data_value = data_arr;
     $.ajax({
        type:"post",
        datatype:"json",
        url:"<?php echo base_url('Run_qiime/getgroup'); ?>",
        data:{data: data_value},
        success:function(data){
            var reData = $.parseJSON(data);
            if(reData[0] == "1"){
                var name_group = "";
                name_group += "<option value='none'>none</option>";
                for (var i=0; i < reData[1].length; i++){
                        name_group += "<option value="+reData[1][i]+">"+reData[1][i]+"</option>";    
                }
                $('#core_group').html(name_group);
                $('#permanova').html(name_group);
                $('#anosim').html(name_group);
                $('#adonis').html(name_group);  

                /*start div sample-name*/
                var samname = "";
                samname += "<option value=0> </option>";
                for (var i=0; i < reData[2].length; i++) {

                     samname += "<option value="+reData[2][i]+">"+reData[2][i]+"</option>";    
                }
                $('#sample_name').html(samname);
                 /*end div sample-name*/ 
            }                
        },
        error:function(e){
             console.log(e.message);
        }             
    });                       
}

function getvalue(array_data){
            var data_value = array_data;
            $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_qiime/run_qiime_process'); ?>",
                    data:{data_array: data_value},
                    success:function(data){
                      var data_job = $.parseJSON(data);
                      checkrun(data_job);
                    },
                    error:function(e){
                      console.log(e.message);
                    }
            });
            
}

function checkrun(job_val){
          
        $('#bar_pre').width(1+"%");
        var time = 30;
        var interval = null;
        interval = setInterval(function(){   
            time--;
            if(time === 0){
            $.ajax({ 
                type:"post",
                datatype:"json",
                url:"<?php echo base_url('Run_qiime/check_run_qiime'); ?>",
                data:{data_job: job_val },
                success:function(data){
                //console.log("data : " + JSON.parse(data));
                var data_up = $.parseJSON(data);
                   if(data_up[0] == "0"){
                            $('#test_run').html('Queue complete');
                            clearInterval(interval);

                             $('.sw-theme-arrows > .nav-tabs > .pre').next('li').find('a').trigger('click'); 
                             $(".Pre-show").hide();
                             $(".Pre-test").show();
                             
                             $('li.pre').attr('id','done');
                             //$('li.pre2').attr('id','active');

                   }else{
                         var show_data = data_up[0];
                         var show_num  = data_up[1];
                         $('#bar_pre').width(show_num+"%");
                         $('.percent_pre').html(show_num+"%");
                         $('#test_run').html(show_data);
                         time = 30;  
                   }  
                },
                error:function(e){
                      console.log(e.message);
                    }
            });
            }
        },1000);
}




function on_switch(data_name){
     var user = "<?php echo $username ?>";
     var project = "<?php echo $current_project ?>";
   
     $.ajax({ 
        type:"post",
        datatype:"json",
        url:"<?php echo site_url('Run_qiime/read_map_json/');?>"+user+"/"+project,
        data:{data_group: data_name},
        success:function(data){
                var data_sw = $.parseJSON(data);
                console.log(data_sw);
                if(data_sw == "on"){ 
                    $(".optionset1").removeAttr("disabled");
                    $(".opt1").hide();
                }else{
                    $(".optionset1").attr("disabled", true);
                    $(".opt1").show();
                 }
        },
        error:function(e){
            console.log(e.message);
        }
    });          
}





document.getElementById("btn_reports").onclick = function(){

    location.href= "<?php echo site_url();?>Qiime_report/index/<?=$current_project?>";
    // $.ajax({ 
    //       type:"post",
    //       datatype:"json",
    //       url:"<?php echo base_url('ckprorun'); ?>",
    //       data:{current:"<?=$current_project?>"},
    //          success:function(data){
    //             var chk = JSON.parse(data); 
    //             if(chk == "t"){
                             
    //             }
              
    //         }         
    //  });

};



</script> 


   