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


        <!-- mothur + qiime  -->
         <li>
         <link href="<?php echo base_url();?>tooltip/smart_wizard_theme_arrows.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/loading.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/tooltip.css" rel="stylesheet" />
         <script src="<?php echo base_url();?>tooltip/tooltip.js" type="text/javascript"></script>
       

         <div class="sw-theme-arrows">
             <ul class="nav-tabs step-anchor" uk-switcher="animation: uk-animation-fade">
                 <li class="pre"><a href="#">Step 1<br />Qiime Preprocess </a></li>
                 <li class="pre2"><a href="#">Step 2<br />Qiime  Pick OTUs </a></li>
                 <li class="pre3"><a href="#">Step 3<br />..</a></li>
                 <li><a href="#">Step 4<br />..</a></li>
             </ul>
         
         <ul class="uk-switcher uk-margin">


         <!--Preprocess && Prepare in taxonomy -->
         <li>

         <!-- .panel-group -->
         <div class="panel-group" id="accordion"></div>
    
             <div class="Pre-test">
             <form name="Pre-form" method="post" action="#" enctype="multipart/form-data">

                 <input type="hidden" name="username" value="<?= $username ?>">
                 <input type="hidden" name="project" value="<?= $current_project ?>">
                 <!-- /.row -->
                <div class="row">

                <div class="col-lg-12">


                 <!-- .panel-heading -->
                 <div class="panel-body">
                 <div class="panel-group" id="accordion">

                        <div class="panel panel-info">
                         <div class="panel-heading">          
                             <h4 class="panel-title">
                                 <a  data-toggle="collapse" data-parent="#accordion" href="#collapse1" >1. ..  
                                 <i class="fa fa-question-circle-o" ></i>       
                                 </a>
                             </h4>
                         </div>
                         <div id="collapse1" class="panel-collapse collapse">

                             <div class="panel-body">       
                                 <label class="col-lg-10"> .. </label>
                                 <div class="col-lg-10">

                                
                                 </div>
                             </div>
                         </div>
                         </div>
                         <div class="panel panel-default">
                             <div class="panel-heading">
                                 <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2"> 2. ...
                                     <i class="fa fa-question-circle-o"></i>
                                     </a> 
                                 </h4>
                             </div>
                             <div id="collapse2" class="panel-collapse collapse">
                                <div class="panel-body">
                              
                           
                                    
                                </div>
                             </div>
                         </div>
                         <div class="panel panel-info">
                         <div class="panel-heading">
                             <h4 class="panel-title">
                                 <a data-toggle="collapse" data-parent="#accordion" href="#collapse3"> 3. ..
                                 <i class="fa fa-question-circle-o" ></i>
                                 </a>
                             </h4>
                         </div>
                         <div id="collapse3" class="panel-collapse collapse">
                             <div class="panel-body">
                               
                                                        
                             </div>
                         </div>
                         </div>
                         <div class="panel panel-default">
                         <div class="panel-heading">
                              <h4 class="panel-title">
                                 <a data-toggle="collapse" data-parent="#accordion" href="#collapse4"> 4. ..
                                 <i class="fa fa-question-circle-o"></i>
                                 </a> 
                         </h4>
                         </div>
                         <div id="collapse4" class="panel-collapse collapse">
                         <div class="panel-body">
                             
                            
                         </div>
                         </div>
                         </div>
                         <div class="panel panel-info">
                         <div class="panel-heading">
                             <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse5"> 5. ..
                                <i class="fa fa-question-circle-o" ></i>
                                 </a> 
                             </h4>
                         </div>
                         <div id="collapse5" class="panel-collapse collapse">
                         <div class="panel-body">
                            
                             
                            
                         </div>
                         </div>
                         </div>

                         </div>
                        </div>
                 </div>  <!-- /.col-lg-11 -->
                 </div><!-- /.row -->

    
                     <div class="col-lg-12 uk-margin"></div>
                     <div class="row">
                     <div class="col-lg-1"></div>
                     <div class="col-lg-6">
                      <input id="sub-test" class="btn btn-primary" value="Run Preprocess">  
                     </div>
                     </div>

                  
                                                

             </form><!-- close row form -->
             </div> <!-- Pre-test -->
             <div class="row">
                 <div class="col-lg-11 ">
                    <div class="Pre-show" style="display:none"> 
                      
                      <div class="loader">
                          <p class="h1"> Qiime Preprocess</p>
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
    <!--End Preprocess && Prepare in taxonomy -->


     <!--Prepare phylotype -->
     <li>

        <div class="Pre-test2">
           <form name="Pre-form2" method="post" action="#" enctype="multipart/form-data">

            <input type="hidden" name="username" value="<?= $username ?>">
            <input type="hidden" name="project" value="<?= $current_project ?>">
           <!-- /.row -->
             <div class="row">
             <div class="col-lg-11">

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
                            <p class="fa fa-gears col-lg-11"> Click button generate map file</p>
                            <div class="col-lg-2">                      
                                 <div class="col-lg-10">
                                    <a href="<?php echo site_url();?>Run_mothur_qiime/excel_map" target="_blank"><input type="button" class="btn btn-outline btn-info" value="generate map file" id="check_map" disabled="disabled" ></a>
                                   <!--  disabled="disabled"  -->
                                 </div>
                            </div>
                            <div class="col-lg-11 uk-margin">
                                <div class="col-lg-10">
                                     <input type="hidden" id="p_map" name="f_map" value="nomap" >
                                    <p class="fa fa-file-text-o" id="text_map"> No map file.</p>
                                     <img id="img_map" >
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
                         </div>
                    </div>
                 </div>
                 <div class="col-lg-12 uk-margin"></div>
                    <div class="row">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-4">
                       <!--  disabled -->
                    <input id="sub-test2" class="btn btn-primary disabled" value="Run Pick OTUs">
                     </div>
                    </div>
                 <div class="col-lg-12 uk-margin"></div>
                 </div>
                 </div>        

             </div>  <!-- /.col-lg-11 -->
             </div><!-- /.row -->
         </form><!-- close row form -->
        </div> <!-- Pre-test2 -->

        <div class="row">
                 <div class="col-lg-11">
                    <div class="Pre-show2" style="display:none"> 
                      <div class="loader">
                          <p class="h1">Qiime Pick OTUs</p>
                          <span></span>
                          <span></span>
                          <span></span>
                      </div>
                     <div class="col-lg-5 col-lg-push-1 "> <b>Status : </b></div>
                     <div class="col-lg-5 col-lg-pull-3" id="test_run2">Wait Queue</div>
                     <div class="col-lg-11 col-lg-push-1 uk-margin">
                     <div class="progress progress-striped active">
                         <div id="bar_pre2" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                         <div class="percent_pre2">0%</div>
                     </div>
                     </div>
                     </div>
                     </div> 
                </div> 
         </div>

     </li>
     <!--End Prepare phylotype analysis-->


    <!-- Analysis -->                              
    <li>

        <div class="Pre-test3">
            <!-- /.row -->
             <div class="row">
             <div class="col-lg-11">  tab3  </div>  <!-- /.col-lg-11 -->       
             </div><!-- /.row -->
        </div> <!-- Pre-test3 -->
    </li> 
    <!-- End Analysis -->

    <!-- Result && Graph -->
     <li> 
          <!-- /.row -->
             <div class="row">
             <div class="col-lg-11">  tab4  </div>  <!-- /.col-lg-11 -->       
             </div><!-- /.row -->
     </li>
    <!-- End Result && Graph -->

     </ul> 
     <!-- class="uk-switcher uk-margin" -->

    </div>
    </li>
    <!--  mothur + qiime  -->

    </ul>
    <!-- end class="uk-switcher" -->  

    </div>
    </div>
    </div>
</div>


  

<script type="text/javascript">  







$(document).ready(function (){ 

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
                $('#test_run').html('Ckecking Run Preprocess');
                checkrun(send_data);

          }else if(step_run == "2"){
              alert("Pick OTUs"); 
              $('li.pre').attr('id','done');
              $('li.pre2').attr('id','active');
              $(".Pre-test2").hide();
              $(".Pre-show2").show();
              $('#test_run2').html('Ckecking Run Pick OTUs');
              $('.sw-theme-arrows > .nav-tabs > .pre').next('li').find('a').trigger('click'); 
              checkrun2(send_data);
          }
       }else{

           $('li.pre').attr('id','active');
       }  
       
       
       
        $("#sub-test").click(function () {
               
                  var username = document.forms["Pre-form"]["username"].value;
                  var project  = document.forms["Pre-form"]["project"].value;
                  var maximum_ambiguous = document.forms["Pre-form"]["maximum_ambiguous"].value;
                  var maximum_homopolymer = document.forms["Pre-form"]["maximum_homopolymer"].value;
                  var minimum_reads_length = document.forms["Pre-form"]["minimum_reads_length"].value;
                  var maximum_reads_length = document.forms["Pre-form"]["maximum_reads_length"].value;

                  var alignment = document.forms["Pre-form"]["alignment"].value;
                  var option_silva = document.forms["Pre-form"]["option_silva"].value;

                  var customer = document.forms["Pre-form"]["customer"].value;
                  var diffs  = document.forms["Pre-form"]["diffs"].value;
                  var classify = document.forms["Pre-form"]["classify"].value;
                  var cutoff = document.forms["Pre-form"]["cutoff"].value;
                  var optionsRadios = document.forms["Pre-form"]["optionsRadios"].value;
                  var taxon = document.forms["Pre-form"]["taxon"].value;

                  if(alignment == 'silva'){
                     alignment = option_silva;
                  }
                  
                  var array_data = new Array(username,project,maximum_ambiguous,maximum_homopolymer,minimum_reads_length,maximum_reads_length,alignment,customer,diffs,classify,cutoff,optionsRadios,taxon);
                 
                  if(maximum_ambiguous != "" && maximum_homopolymer != "" && minimum_reads_length != "" && maximum_reads_length != "" && alignment != ""){

                       // $(".Pre-test").hide();
                       // $(".Pre-show").show();
                        //getvalue(array_data);
                   }    
        });

        $("#sub-test2").click(function () {
               
                var username = document.forms["Pre-form2"]["username"].value;
                var project  = document.forms["Pre-form2"]["project"].value;
                var f_map = document.forms["Pre-form2"]["f_map"].value;
                var permanova = document.forms["Pre-form2"]["permanova"].value;
                var anosim = document.forms["Pre-form2"]["anosim"].value;
                var adonis = document.forms["Pre-form2"]["adonis"].value;
                var opt_permanova = document.forms["Pre-form2"]["opt_permanova"].value;
                var opt_anosim = document.forms["Pre-form2"]["opt_anosim"].value;
                var opt_adonis = document.forms["Pre-form2"]["opt_adonis"].value;

                var array_data = new Array(username,project,f_map,permanova,anosim,adonis,opt_permanova,opt_anosim,opt_adonis);
                if(f_map == 'Noerror'){

                     $(".Pre-test2").hide();
                     $(".Pre-show2").show();
                     getvalue2(array_data); 
                }
                
         });


    $("#check_map").click(function () { 
        var user = "<?=$username ?>";
        var project = "<?=$current_project ?>";
        var $data_arr = new Array(user,project);
        var time = 10;
        var interval = null;
            interval = setInterval(function(){   
            time--;
            $('#img_map').attr("src","<?php echo $srcload;?>"); 
                if(time === 0){
                    $.ajax({ 
                        type:"post",
                        datatype:"json",
                        url:"<?php echo base_url('Run_mothur_qiime/check_map_file');?>/"+user+"/"+project,
                        success:function(data){
                            var map = JSON.parse(data);
                             if(map == 'Noerror'){
                                 clearInterval(interval);
                                 getGroup($data_arr);  
                                 $('#text_map').text(" No errors or warnings were found in mapping file.");
                                 document.getElementById('p_map').value = map;
                                 $('#img_map').attr("src","<?php echo $src;?>");  
                             }
                             else{ time = 5;  } 
                        }
                    });
                }

             },1000);                                   
     });

    
 });


function getGroup($data_arr){

     var data_value = $data_arr;
     $.ajax({
        type:"post",
        datatype:"json",
        url:"<?php echo base_url('Run_mothur_qiime/map_json'); ?>",
        data:{data: data_value},
        success:function(data){
            var reData = $.parseJSON(data);
            if(reData[0] == "1"){
                var name_group = "";
                name_group += "<option value='none'>none</option>";
                for (var i=0; i < reData[1].length; i++){
                        name_group += "<option value="+reData[1][i]+">"+reData[1][i]+"</option>";    
                }
                $('#permanova').html(name_group);
                $('#anosim').html(name_group);
                $('#adonis').html(name_group);   
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
                    url:"<?php echo base_url('Run_mothur_qiime/run_mothur'); ?>",
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
                url:"<?php echo base_url('Run_mothur_qiime/check_run_mothur'); ?>",
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
                             $('li.pre2').attr('id','active');
                             $('#sub-test2').attr('class','btn btn-primary');
                             $('#check_map').attr("disabled",false);
                           
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



function getvalue2(array_data){
            var data_value = array_data;
            $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_mothur_qiime/mothur_qiime1'); ?>",
                    data:{data_array: data_value},
                    success:function(data){
                      var data_job = $.parseJSON(data);
                      checkrun2(data_job);
                    },
                    error:function(e){
                      console.log(e.message);
                    }
            });
            
}

function checkrun2(job_val){
          
        $('#bar_pre2').width(1+"%");
        var time = 30;
        var interval = null;
        interval = setInterval(function(){   
            time--;
            if(time === 0){
            $.ajax({ 
                type:"post",
                datatype:"json",
                url:"<?php echo base_url('Run_mothur_qiime/check_run_mothur_qiime1'); ?>",
                data:{data_job: job_val },
                success:function(data){
                //console.log("data : " + JSON.parse(data));
                   var data_up = $.parseJSON(data);
                   if(data_up[0] == "0"){
                            $('#test_run2').html('Queue complete');
                            clearInterval(interval); 
                            $('.sw-theme-arrows > .nav-tabs > .pre2').next('li').find('a').trigger('click'); 
                            $(".Pre-show2").hide();
                            $(".Pre-test2").show();    
                   }else{
                         var show_data = data_up[0];
                         var show_num  = data_up[1];
                         $('#bar_pre2').width(show_num+"%");
                         $('.percent_pre2').html(show_num+"%");
                         $('#test_run2').html(show_data);
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


</script> 
<!--  End Advance Script -->

   