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
                 <li class="pre"><a href="#">Step 1<br/>Pre process </a></li>
                 <li class="pre2"><a href="#">Step 2<br/>Pick OTUs </a></li>
                 <li class="pre3"><a href="#">Step 3<br/>Chimerac detection</a></li>
                 <li class="pre4"><a href="#">Step 4<br/>Core diversity analysis</a></li>
                
             </ul>
         
         <ul class="uk-switcher uk-margin">


         <!--pre -->
         <li>
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
                            <a  data-toggle="collapse" data-parent="#accordion" href="#collapse13" >Generate Map file 
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
                 </div>
                 </div>   

                 </div>  <!-- /.col-lg-12 -->
                 </div><!-- /.row -->
                
                 <form name="Pre-form" method="post" action="#" enctype="multipart/form-data">

                 <input type="hidden" name="username" value="<?= $username ?>">
                 <input type="hidden" name="project" value="<?= $current_project ?>">
                
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
    <!--End pre -->


     <!--pre2-->
     <li>

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
     <!--End pre2-->

    <!--pre3-->
     <li>
        <div class="row">
            <div class="col-lg-11">
                    <div class="Pre-show3" style="display:none"> 
                      <div class="loader">
                          <p class="h1">Qiime Chimerac detection </p>
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
     <!--End pre3-->


    <!--pre4-->
     <li>
        <div class="row">
            <div class="col-lg-11">
                    <div class="Pre-show4" style="display:none"> 
                      <div class="loader">
                          <p class="h1">Qiime Core diversity analysis</p>
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
     <!--End pre4-->

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
                         var $data_arr = new Array(user,project);
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
                                 getGroup($data_arr);  
                                 $('#text_map').text("No errors or warnings found in mapping file.");
                                 $('#img_map').attr("src","<?php echo $src;?>");  
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
                  
                  //alert(username);
                 

                       // $(".Pre-test").hide();
                       // $(".Pre-show").show();
                        //getvalue(array_data);
                      
        });

    
 });


function getGroup($data_arr){

     var data_value = $data_arr;
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


</script> 
<!--  End Advance Script -->

   