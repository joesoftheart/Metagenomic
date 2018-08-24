
<input type="hidden" id="advance_num" value="0">

<script>  


function advance_mode(obj){

            var pid = "<?=$currentproject ?>";
            var user = "<?=$username ?>";
            var project = "<?=$project ?>";
            var va_num = 0;
                 va_num += Number(obj.getAttribute("href"));
            var num = $('#advance_num').val();
            var check = va_num-num;
            if(check == 1){
                 document.getElementById('advance_num').value = va_num ;
            }
            
            $.ajax({
                   type:"post",
                   datatype:"json",
                   url:"<?php echo base_url('recheck'); ?>",
                   data:{data_status: pid},
                   success:function(data){

                        var status = $.parseJSON(data);
                        if(status[0] == "0" && status[1] == "4"){
                           alert('No Run Queue');

                             $('li.pre').attr('id','active');
                             $('li.pre2').attr('id','');
                             $('li.pre3').attr('id','');

                        }else if(status[0] != "0" && status[1] == "1" && check == 1){
                           alert('Run step '+status[1] );
                                 $(".Pre-test").hide();
                                 $(".Pre-show").show();
                                 var data = new Array(status[2],pid);
                                 checkrun(data);
                                 $('#test_run').html('Checking Process Queue');

                                 $('li.pre').attr('id','active');
                                 $('li.pre2').attr('id','');
                                 $('li.pre3').attr('id','');

                        }else if(status[0] != "0" && status[1] == "2" && check == 1){
                            alert('Run step '+status[1] );
                            $('.sw-theme-arrows > .nav-tabs > .pre').next('li').find('a').trigger('click');
                                 $(".Pre-test2").hide();
                                 $(".Pre-show2").show();
                                 var data = new Array(status[2],pid);
                                 check_subsample(data);
                                 $('#test_run2').html('Checking Process Queue');

                                 $('li.pre').attr('id','done');
                                 $('li.pre2').attr('id','active');
                                 $('li.pre3').attr('id','');


                        }else if(status[0] != "0" && status[1] == "3" && check == 1){
                            alert('Run step '+status[1] );
                            $('.sw-theme-arrows > .nav-tabs > .pre2').next('li').find('a').trigger('click');
                               $(".Pre-test3").hide();
                               $(".Pre-show3").show();
                               var data = new Array(status[2],pid);
                               ckeck_analysis(data);
                               $('#test_run3').html('Checking Process Queue');

                                 $('li.pre').attr('id','done');
                                 $('li.pre2').attr('id','done');
                                 $('li.pre3').attr('id','active');
                        } 
                      
                   },
                   error:function(e){
                     console.log(e.message);
                   }
           });    
 }


function checkvalue(){
             var mbig = document.getElementById('mbig');
             if(mbig.value == ""){
                $('#mbig').css("border","1px solid #FF0000");  
             }else{
                $('#mbig').css("border","1px solid #e1ede1");
             }
}

function checkvalue2(){
           var mhomo = document.getElementById('mhomo');
            if(mhomo.value == ""){
                $('#mhomo').css("border","1px solid #FF0000");  
             }else{
                $('#mhomo').css("border","1px solid #e1ede1");
             }
}

function checkvalue3(){
           var miniread = document.getElementById('miniread');
            if(miniread.value == ""){
                $('#miniread').css("border","1px solid #FF0000");  
             }else{
                $('#miniread').css("border","1px solid #e1ede1");
             }
}

function checkvalue4(){
           var maxread = document.getElementById('maxread');
           if(maxread.value == ""){
                $('#maxread').css("border","1px solid #FF0000");  
             }else{
                $('#maxread').css("border","1px solid #e1ede1");
             }
}

function validateNumber(event) {
            var key = window.event ? event.keyCode : event.which;
            if (event.keyCode === 8 || event.keyCode === 46) {
                    return true;
            } else if ( key < 48 || key > 57 ) {
                return false;
            } else {
                return true;
            }
}


$(document).on('change', '#custo_mer', function(){
               
               var file_data = $('#custo_mer').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file', file_data);
                   var file_name = file_data.name;
                   var file_size = file_data.size;
                   var file_mb = (file_data.size/1024/1024).toFixed(0); // MB
                   
                   var type = file_name.substring(file_name.lastIndexOf('.')+1);
                   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                     var i = parseInt(Math.floor(Math.log(file_size) / Math.log(1024)));
                     var f_size = Math.round(file_size / Math.pow(1024, i), 2) + ' ' + sizes[i];
                  
                   if(type == 'fasta' || type == 'align'){
                        if(file_size == 0){
                            alert('Size : '+file_size +' Bytes');
                            document.getElementById('custo_mer').value = ""; 
                        }
                        else if(file_mb <= 800){
                          //alert(file_name+' '+f_size+' '+type);
                         get_fasta(form_data);

                        }else{
                            alert('file is too large : '+ f_size);
                            document.getElementById('custo_mer').value = ""; 
                        } 
                    }
                    else{ 
                        alert('file is not fasta or align');
                        document.getElementById('custo_mer').value = ""; 
                     }
                 
});


function get_fasta(file_data){
            
          var user = "<?=$username ?>";
          var project = "<?=$currentproject ?>";
          var bar = $('#bar');
          var percent = $('.percent');
          var status = $('#status');
          
           $.ajax({
                   type:"post",
                   dataType: 'text',
                   url:"<?php echo base_url('upfasta');?>/"+user+"/"+project,
                   data: file_data,
                   cache: false,
                   processData: false,
                   contentType: false,
                    beforeSend: function () {
                        console.log("beforeSend");
                        status.empty();
                        var percentVal = '0%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        //Download progress
                        xhr.upload.addEventListener("progress", function (evt) {
                             //console.log(evt.loaded);
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                bar.width(Math.round(percentComplete * 100) + "%");
                                percent.html(Math.round(percentComplete * 100) + "%");
                                
                            }
                        }, false);
                       return xhr;
                    },
                    complete: function (xhr) {
                         
                          if(xhr.responseText == '0'){
                                alert("File is not fasta");
                                status.html("File is not fasta");
                                document.getElementById('custo_mer').value = ""; 
                                bar.width(Math.round(0) + "%");
                                percent.html(Math.round(0) + "%");
                          }else { 
                               alert(xhr.responseText); 
                               status.html(xhr.responseText);
                               bar.width(Math.round(0) + "%");
                               percent.html(Math.round(0) + "%");
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
                    url:"<?php echo base_url('preprocess'); ?>",
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
              // $('#time').html(time);
              if(time === 0){
                $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('chkpreprocess'); ?>",
                    data:{data_job: job_val },
                    success:function(data){
                      //console.log("data : " + JSON.parse(data));
                     var data_up = $.parseJSON(data);
                      if(data_up[0] == "0"){

                            $('#test_run').html('Queue complete');
                            clearInterval(interval);
                            get_prepare(data_up);
                           
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

function get_prepare(data){
            $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('readcount'); ?>",
                    data:{data_count: data },
                    success:function(data){
                     var d_group  = "";  
                     var d_count = $.parseJSON(data);
                     for(var i=0;i < d_count.length; i++){
               
                         if(i == d_count.length-1 ){

                             document.getElementById('max_num_subsample').value = Number(d_count[i]);
                             document.getElementById('sub_sample').value = Number(d_count[i]);
                             $('#sub_sample').attr({'max': Number(d_count[i])});
                             document.getElementById('show_group').value = d_group;

                             $('.sw-theme-arrows > .nav-tabs > .pre').next('li').find('a').trigger('click'); 
                             $(".Pre-show").hide();
                             $(".Pre-test").show();
                             
                             $('li.pre').attr('id','done');
                             $('li.pre2').attr('id','active');
                             $('#sub-test2').attr('class','btn btn-primary');
                               
                               $('#test_run').html("Wait Queue"); 
                               $('#bar_pre').width(Math.round(0) +"%");
                               $('.percent_pre').html(Math.round(0) +"%"); 

                         }else{
                            d_group += d_count[i];
                         }

                     }
                      
                    },
                    error:function(e){
                      console.log(e.message);
                    }
                });
}



function get_subsample(array_data){
           var data_value = array_data;
           $.ajax({
                   type:"post",
                   datatype:"json",
                   url:"<?php echo base_url('subsample'); ?>",
                   data:{data_sample: data_value},
                   success:function(data){
                        var job_sample = $.parseJSON(data);
                        check_subsample(job_sample);
                   },
                   error:function(e){
                     console.log(e.message);
                   }
           });
}
                                 
function check_subsample(jobsample){

          $('#bar_pre2').width(1+"%");
          var time = 20;
          var interval = null;
          interval = setInterval(function(){   
              time--;
              // $('#time2').html(time);
              if(time === 0){
                $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('chksample'); ?>",
                    data:{job_sample: jobsample },
                    success:function(data){
                     var sample_data = $.parseJSON(data);
                      if(sample_data[0] == "0"){
                           
                           if(sample_data[1] =="gg"){
                               $('.Greengene').show();
                               $('.Silva_RDP').hide();
                               $('.Otu').hide();
                               $('#setm1').hide();
                               $('#seto1').hide();
                               document.getElementById('g_level').setAttribute("name","level");

                           }else if((sample_data[1] == "silva") || (sample_data[1] == "rdp")) {
                               $('.Greengene').hide();
                               $('.Silva_RDP').show();
                               $('.Otu').hide();
                               $('#setm3').hide();
                               $('#seto3').hide();
                               document.getElementById('sr_level').setAttribute("name","level");
                           }
                           else{

                               $('.Greengene').hide();
                               $('.Silva_RDP').hide();
                               $('.Otu').show();
                               document.getElementById('o_level').setAttribute("name","level");

                           }

                            /*start div value vene*/
                             var group = "";
                                 group += "<option value=0> </option>";
                                 for (var i=0; i < sample_data[2].length; i++) {
                                   group += "<option value="+sample_data[2][i]+">"+sample_data[2][i]+"</option>";    
                                 }
                                 $('#venn1').html(group);
                                 $('#venn2').html(group);
                                 $('#venn3').html(group);
                                 $('#venn4').html(group);

                             /*end div value vene*/
                             
                           
                             /*start div sample-name*/
                              var samname = "";
                                  samname += "<option value=0> </option>";
                              for (var i=0; i < sample_data[4].length; i++) {

                                   samname += "<option value="+sample_data[4][i]+">"+sample_data[4][i]+"</option>";    
                             }

                             $('#sample_name').html(samname);

                            /*end div sample-name*/
   
                             var sam_group  = "";  
                             for(var i=0 ;i < sample_data[3].length; i++){

                                if(i == sample_data[3].length-1){

                                   document.getElementById('sub_sample').value = Number(sample_data[3][i]);
                                   document.getElementById('show_group').value = sam_group; 
                                   document.getElementById('alpha').value = Number(sample_data[3][i]);
                                   document.getElementById('beta').value = Number(sample_data[3][i]);
                                   document.getElementById('myradio').value = sample_data[3][i];
                                   document.getElementById('myradio1').value = sample_data[3][i];
                                   document.getElementById('max_num_subsample').value = sample_data[3][i];
                                   $('#sub_sample').attr({'max': Number(sample_data[3][i])});
                                   $('#alpha').attr({'max': Number(sample_data[3][i])});
                                   $('#beta').attr({'max': Number(sample_data[3][i])});
                                   
                                }else{
                                    sam_group += sample_data[3][i];
                                   }
                              }

                             $('#test_run2').html('Queue complete');
                             clearInterval(interval);
                             $('.sw-theme-arrows > .nav-tabs > .pre2').next('li').find('a').trigger('click'); 
                             $(".Pre-show2").hide();
                             $(".Pre-test2").show();
                             $('#sub-test2').attr('class','btn btn-primary');

                             $('li.pre').attr('id','done');
                             $('li.pre2').attr('id','done');
                             $('li.pre3').attr('id','active');

                               /* set processbar 0   */
                               $('#bar_pre2').width(0+"%");
                               $('.percent_pre2').html(0+"%"); 
                            

                      }else{

                            var num = sample_data[1];
                            $('#bar_pre2').width(num+"%");
                            $('.percent_pre2').html(num+"%");
                            $('#test_run2').html('sub sample');
                            time = 20;  
  
                      }  
                    },
                    error:function(e){
                      console.log(e.message);
                    }
                });
              }
          },1000);
}


function create_var(checkboxes){
             var vals = "";
                    for (var i=0, n=checkboxes.length;i<n;i++){
                       if (checkboxes[i].checked){ 
                            vals += checkboxes[i].value+" ";
                        }
                    }
             var str = vals.trim();
             var data_var = str.replace(/ /g,",");
             return data_var;
}
         


function get_analysis(array_data){
           var data_value = array_data;
           $.ajax({
                   type:"post",
                   datatype:"json",
                   url:"<?php echo base_url('analysis'); ?>",
                   data:{data_analysis: data_value},
                   success:function(data){
                        var job_analysis = $.parseJSON(data);
                        //var job_analysis = JSON.parse(data);
                        ckeck_analysis(job_analysis);
                      
                   },
                   error:function(e){
                     console.log(e.message);
                   }
           });
  
}

function ckeck_analysis(job_analy){

      $('#bar_pre3').width(1+"%");
      var u = "<?php echo $username ?>";
      var p = "<?php echo $project ?>";
      var time = 30;
      var interval = null;
            interval = setInterval(function(){   
              time--;
              // $('#time3').html(time);
              if(time === 0){
                $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('chkanalysis'); ?>",
                    data:{job_analysis: job_analy },
                    success:function(data){
                      //var analysis = JSON.parse(data);
                      var analysis = $.parseJSON(data);
                     
                      if( analysis[0] == "0"){
                         clearInterval(interval);

                           
                             $('.sw-theme-arrows > .nav-tabs > .pre3').next('li').find('a').trigger('click'); 
                             $(".Pre-show3").hide();
                             $(".Pre-test3").show();

                             $('li.pre').attr('id','done');
                             $('li.pre2').attr('id','done');
                             $('li.pre3').attr('id','done');

                             $('#bar_pre3').width(0+"%");
                             $('.percent_pre3').html(0+"%");
                             $('#test_run3').html("Wait Queue");
                              
                              // insert value to  projects_run
                              writereport(analysis[2]);


                      }else{

                         var num = analysis[1];
                         var show_data = analysis[2];
                         $('#bar_pre3').width(num+"%");
                         $('.percent_pre3').html(num+"%");
                         $('#test_run3').html(show_data);
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


function writereport(proid){
  //console.log('id : '+proid);
  $.ajax({
     type:"post",
     datatype:"json",
     url:"<?php echo base_url('Advance_report/index'); ?>",
     data:{projectid: proid  },
     success:function(data){
        
        console.log(data);
     },
     error:function(e){
        console.log(e.message);
     }
  });

}


$(document).ready(function(){


     $("#check_design").click(function () {
                 design_stop = "start";
                 var user = "<?=$username ?>";
                 var project = "<?=$currentproject ?>";
                 var time = 10;
                 var interval = null;
                 interval = setInterval(function(){   
                 time--;
                   $('#img_design').attr("src","<?php echo $srcload;?>");
                    
                    if(time === 0){
                     $.ajax({ 
                       type:"post",
                       datatype:"json",
                       url:"<?php echo base_url('checkdesign');?>/"+user+"/"+project,
                         success:function(data){
                            var design = JSON.parse(data);
                             if(design != "No File" || design_stop == "stop"){
                                   clearInterval(interval);
                                   $('#pass_design').text(" "+design);
                                   document.getElementById('p_design').value = design;
                             $('#img_design').attr("src","<?php echo $src;?>");
                                  
                             }
                             else{  
                                  time = 5; 

                             } 
                         }
                     });
                   }

                 },1000);      
      });

      $("#check_metadata").click(function(){
                 metadata_stop = "start";
                 var user = "<?=$username ?>";
                 var project = "<?=$currentproject ?>";
                 var time = 10;
                 var interval = null;
                 interval = setInterval(function(){   
                 time--;
                  $('#img_metadata').attr("src","<?php echo $srcload;?>");
                    
                    if(time === 0){
                     $.ajax({ 
                       type:"post",
                       datatype:"json",
                       url:"<?php echo base_url('checkmetadata');?>/"+user+"/"+project,
                         success:function(data){
                             var metadata = JSON.parse(data);
                             if(metadata  != "No File" || metadata_stop == "stop"){
                                   clearInterval(interval);
                                   $('#pass_metadata').text(" "+metadata);
                                   document.getElementById('p_metadata').value = metadata;
                            $('#img_metadata').attr("src","<?php echo $src;?>");
                             }
                             else{  
                                  time = 5;  
                             } 
                         }
                     });
                   }

                 },1000);      
                  
      });


            $('#radio_pcoa').on('change', function() {
                $(".nmds").attr("disabled", true); 
                $(".nmds").prop('checked', false);
                $(".pcoa").removeAttr("disabled");

            });

             $('#radio_nmds').on('change', function() {
                $(".pcoa").attr("disabled", true);
                $(".pcoa").prop('checked', false);
                $(".nmds").removeAttr("disabled");
                
              });

       
});


    
</script>
