<?php

    include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");

          $user = $argv[1];
         $project = $argv[2];
         $path_in = $argv[3];
         $path_out = $argv[4];
         $GLOBALS['path_log'] = $argv[5];

         $GLOBALS['level']= $argv[6];
         $GLOBALS['size_alpha'] = $argv[7];
         $GLOBALS['size_beta'] = $argv[8];
         $GLOBALS['group_sam'] = $argv[9];
         $GLOBALS['group_ven'] =$argv[10];

         $GLOBALS['d_upgma_st'] = $argv[11];
         $GLOBALS['d_upgma_me'] = $argv[12];

         $GLOBALS['d_pcoa_st'] = $argv[13];
         $GLOBALS['d_pcoa_me'] = $argv[14];
         
         $GLOBALS['nmds']  = $argv[15];

         $GLOBALS['d_nmds_st'] = $argv[16];
         $GLOBALS['d_nmds_me'] = $argv[17];
         

         $GLOBALS['file_design'] = $argv[18];
         $GLOBALS['file_metadata'] = $argv[19];
         $GLOBALS['ah_mova'] = $argv[20];
         $GLOBALS['correlation'] = $argv[21];
         $GLOBALS['method'] = $argv[22];
         $GLOBALS['axes'] = $argv[23];

         
         # Check PCoA & NMDS
         $GLOBALS['check'] = "";

         if($GLOBALS['d_pcoa_st'] != "0" || $GLOBALS['d_pcoa_me'] != "0"){
             $GLOBALS['check'] = "pcoa";
         }elseif ($GLOBALS['d_nmds_st'] != "0" || $GLOBALS['d_nmds_me'] != "0") {
             $GLOBALS['check'] = "nmds";
         }


         if($user != "" && $project != "" && $path_in != "" && $path_out != "" && $argv[5] != "" && $argv[6] =! "" && $argv[7] != "" && $argv[8] != "" && $argv[9] != "" && $argv[10] != ""){
             
             collect_rarefaction_summary($user,$project,$path_in,$path_out);



         }else{

              echo "user : ".$user."\n";
              echo "project : ".$project."\n"; 
              echo "path_in : ".$path_in."\n";
              echo "path_out : ".$path_out."\n";
              echo "path_log : ".$GLOBALS['path_log']."\n";
              echo "level : ".$GLOBALS['level']."\n";
             
              echo "size_alpha : ".$GLOBALS['size_alpha']."\n";
              echo "size_beta : ".$GLOBALS['size_beta']."\n";
              echo "group_sam : ".$GLOBALS['group_sam']."\n";
              echo "group_ven : ".$GLOBALS['group_ven']."\n";

              echo "d_upgma_st : ".$GLOBALS['d_upgma_st']."\n";
              echo "d_upgma_me : ".$GLOBALS['d_upgma_me']."\n";

              echo "d_pcoa_st : ".$GLOBALS['d_pcoa_st']."\n";
              echo "d_pcoa_me : ".$GLOBALS['d_pcoa_me']."\n";
         
              echo "nmds : ".$GLOBALS['nmds']."\n";

              echo "d_nmds_st : ".$GLOBALS['d_nmds_st']."\n";
              echo "d_nmds_me : ".$GLOBALS['d_nmds_me']."\n" ;

              echo "file_design : ".$GLOBALS['file_design']."\n";
              echo "file_metadata : ".$GLOBALS['file_metadata']."\n" ;
              echo "ah_mova : ".$GLOBALS['ah_mova']."\n";
              echo "correlation : ".$GLOBALS['correlation']."\n";
              echo "method : ".$GLOBALS['method']."\n";
              echo "axes : ".$GLOBALS['axes']."\n";
         }



     function collect_rarefaction_summary($user,$project,$path_in,$path_out){
           echo "collect_rarefaction_summary"."\n";

           $jobname = $user."_collect_rarefaction_summary";

            $make = "collect.single(shared=final.opti_mcc.shared, calc=chao, freq=100, label=".$GLOBALS['level']." ,inputdir=$path_in,outputdir=$path_out)
					rarefaction.single(shared=final.opti_mcc.shared, calc=sobs, freq=100, label=".$GLOBALS['level'].", processors=8 ,inputdir=$path_in,outputdir=$path_out)
					summary.single(shared=final.opti_mcc.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon-simpson, subsample=".$GLOBALS['size_alpha'].", label=".$GLOBALS['level']." ,inputdir=$path_in,outputdir=$path_out)";
        
            file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
              

               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){

                   	  dist_summary_shared($user,$project,$path_in,$path_out);
                      break;
                      
                      
                   }
              }   
    }


     function dist_summary_shared($user,$project,$path_in,$path_out){
             
              echo "dist_summary_shared"."\n";
              $jobname = $user."_dist_summary_shared";

              $make= "dist.shared(shared=final.opti_mcc.shared, calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, subsample=".$GLOBALS['size_beta'].", processors=8 ,inputdir=$path_in,outputdir=$path_out)
 					  summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=".$GLOBALS['group_sam'].", all=T ,inputdir=$path_in,outputdir=$path_out)";
          
               file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
              

               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){

                   	  venn($user,$project,$path_in,$path_out);
                      break;
                      
                      
                   }
              }   
    }


    function venn($user,$project,$path_in,$path_out){
              
              echo "venn"."\n";

              $jobname = $user."_venn";

              $make = " venn(shared=final.opti_mcc.".$GLOBALS['level'].".subsample.shared, groups=".$GLOBALS['group_ven'].",inputdir=$path_in,outputdir=$path_out)";
              
              file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
              
             
               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){

                      tree_shared($user,$project,$path_in,$path_out);
                      break;
                      
                      
                   }
              }   
       }


       function tree_shared($user,$project,$path_in,$path_out){
         
         echo "tree_shared"."\n";
         $jobname = $user."_tree_shared";

         $make = "";
         # community structure
         if($GLOBALS['d_upgma_st'] != "0"){
            $d_upgma_st = explode(",", $GLOBALS['d_upgma_st']);
            for($i = 0 ; $i < sizeof($d_upgma_st); $i++){
              
             $make .="tree.shared(phylip=final.opti_mcc.".$d_upgma_st[$i].".".$GLOBALS['level'].".lt.ave.dist ,inputdir=$path_in,outputdir=$path_out)"."\n";  
            }
         }
         # community membership
         if($GLOBALS['d_upgma_me'] != "0"){
            $d_upgma_me = explode(",", $GLOBALS['d_upgma_me']);
            for($i = 0 ; $i < sizeof($d_upgma_me); $i++){
              
             $make .="tree.shared(phylip=final.opti_mcc.".$d_upgma_me[$i].".".$GLOBALS['level'].".lt.ave.dist ,inputdir=$path_in,outputdir=$path_out)"."\n";  
               
            }
         } 


         if($make != ""){

           file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
               
               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){

                      if($GLOBALS['check'] == "pcoa"){

                           pcoa($user,$project,$path_in,$path_out);
                           break;
                       }
                       elseif ($GLOBALS['check'] == "nmds") {
                          
                          nmds($user,$project,$path_in,$path_out);
                          break;
                       }
                    
                   }
              }   

         }elseif ($make == "") {

             echo "Not command tree_shared !"."\n";
             break;
           
         }

       }

   function pcoa($user,$project,$path_in,$path_out){
          
          echo "pcoa"."\n";
          $jobname = $user."_pcoa";
          $make = "";
          # community structure
          if($GLOBALS['d_pcoa_st']  != "0"){
               $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
               for($i = 0 ; $i < sizeof($d_pcoa_st); $i++){
              
                 $make .="pcoa(phylip=final.opti_mcc.".$d_pcoa_st[$i].".".$GLOBALS['level'].".lt.ave.dist ,inputdir=$path_in,outputdir=$path_out)"."\n";
               
               }
          }
         # community membership
         if($GLOBALS['d_pcoa_me']  != "0"){
            $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
            for($i = 0 ; $i < sizeof($d_pcoa_me); $i++){
              
            $make .="pcoa(phylip=final.opti_mcc.".$d_pcoa_me[$i].".".$GLOBALS['level'].".lt.ave.dist ,inputdir=$path_in,outputdir=$path_out)"."\n";
               
            }
         } 

         # run command 
         if($make != ""){

           file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
               
               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){

                        amova_homova($user,$project,$path_in,$path_out);
                        break;
                        
                   }
              }   

         }elseif ($make == "") {

             echo "Not command pcoa !"."\n";
             break;
           
         }
          
       }


        function nmds($user,$project,$path_in,$path_out){

          echo "nmds"."\n";
          $jobname = $user."_nmds";

          $make = "";
          #community Structure
          if($GLOBALS['d_nmds_st'] != "0"){
               $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
               for($i = 0 ; $i < sizeof($d_nmds_st); $i++){
              
                 $make .="nmds(phylip=final.opti_mcc.".$d_nmds_st[$i].".".$GLOBALS['level'].".lt.ave.dist, mindim=". $GLOBALS['nmds'].", maxdim=". $GLOBALS['nmds']." ,inputdir=$path_in,outputdir=$path_out)"."\n";
               
               }
          }
         # community membership
         if($GLOBALS['d_nmds_me']  != "0"){
            $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
            for($i = 0 ; $i < sizeof($d_nmds_me); $i++){
              
              $make .="nmds(phylip=final.opti_mcc.".$d_nmds_me[$i].".".$GLOBALS['level'].".lt.ave.dist, mindim=". $GLOBALS['nmds'].", maxdim=". $GLOBALS['nmds']." ,inputdir=$path_in,outputdir=$path_out)"."\n";
            }
         }
         # run command
         if($make != ""){

           file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
               
               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){

                      amova_homova($user,$project,$path_in,$path_out);
                      break;
                        
                   }
              }   

         }elseif ($make == "") {

             echo "Not command nmds !"."\n";
             break;
           
         }



       }

        function amova_homova($user,$project,$path_in,$path_out){
         
          echo "amova_homova"."\n";
          $jobname = $user."_amova_homova";

          $make = "";

          # Amova
         if($GLOBALS['ah_mova'] == "amova" && $GLOBALS['file_design'] != "0"){
              # PCoA
            if($GLOBALS['check'] == "pcoa"){
                  # community structure
                  if($GLOBALS['d_pcoa_st']  != "0"){

                    $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
                    for($i = 0 ; $i < sizeof($d_pcoa_st); $i++){

                     $make .="amova(phylip=final.opti_mcc.".$d_pcoa_st[$i].".".$GLOBALS['level'].".lt.ave.dist, design=".$GLOBALS['file_design'].",inputdir=$path_in,outputdir=$path_out)"."\n";
                    }
                  }
                 # community membership
                  if($GLOBALS['d_pcoa_me']  != "0"){

                      $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                      for($i = 0 ; $i < sizeof($d_pcoa_me); $i++){

                         $make .="amova(phylip=final.opti_mcc.".$d_pcoa_me[$i].".".$GLOBALS['level'].".lt.ave.dist, design=".$GLOBALS['file_design'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                      }
                  }
             # NMDS
            }elseif ($GLOBALS['check'] == "nmds") {
                 # community structure
                  if($GLOBALS['d_nmds_st'] != "0"){
                      $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                      for($i = 0 ; $i < sizeof($d_nmds_st); $i++){
 
                         $make .="amova(phylip=final.opti_mcc.".$d_nmds_st[$i].".".$GLOBALS['level'].".lt.ave.dist, design=".$GLOBALS['file_design'].",inputdir=$path_in,outputdir=$path_out)"."\n";
                       }
                  }
                  # community membership
                  if($GLOBALS['d_nmds_me']  != "0"){
                      $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                      for($i = 0 ; $i < sizeof($d_nmds_me); $i++){

                          $make .="amova(phylip=final.opti_mcc.".$d_nmds_me[$i].".".$GLOBALS['level'].".lt.ave.dist, design=".$GLOBALS['file_design'].",inputdir=$path_in,outputdir=$path_out)"."\n";
                         
                      }
                 }
                 
            
            }

        }

        # Homova
        elseif ($GLOBALS['ah_mova'] == "homova" && $GLOBALS['file_design'] != "0") {
                # PCoA
               if($GLOBALS['check'] == "pcoa"){
                  # community structure
                  if($GLOBALS['d_pcoa_st']  != "0"){

                    $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
                    for($i = 0 ; $i < sizeof($d_pcoa_st); $i++){

                     $make .="homova(phylip=final.opti_mcc.".$d_pcoa_st[$i].".".$GLOBALS['level'].".lt.ave.dist, design=".$GLOBALS['file_design'].",inputdir=$path_in,outputdir=$path_out)"."\n";
                  
                    }
                  }
                  # community membership
                  if($GLOBALS['d_pcoa_me']  != "0"){

                      $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                      for($i = 0 ; $i < sizeof($d_pcoa_me); $i++){

                       
                        $make .="homova(phylip=final.opti_mcc.".$d_pcoa_me[$i].".".$GLOBALS['level'].".lt.ave.dist, design=".$GLOBALS['file_design'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                      }
                  }

               }# NMDS
               elseif ($GLOBALS['check'] == "nmds") {
                 # community structure
                 if($GLOBALS['d_nmds_st'] != "0"){
                      $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                      for($i = 0 ; $i < sizeof($d_nmds_st); $i++){

                          $make .="homova(phylip=final.opti_mcc.".$d_nmds_st[$i].".".$GLOBALS['level'].".lt.ave.dist, design=".$GLOBALS['file_design'].",inputdir=$path_in,outputdir=$path_out)"."\n";
                       }
                  }
                  # community membership
                  if($GLOBALS['d_nmds_me']  != "0"){
                      $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                      for($i = 0 ; $i < sizeof($d_nmds_me); $i++){

                         $make .="homova(phylip=final.opti_mcc.".$d_nmds_me[$i].".".$GLOBALS['level'].".lt.ave.dist, design=".$GLOBALS['file_design'].",inputdir=$path_in,outputdir=$path_out)"."\n";
                         
                      }
                 }
                

               }

         }
          # run command   

          if($make != "" ){

           file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
               
               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){
                      corr_axes($user,$project,$path_in,$path_out);
                      break;
                        
                   }
              }   

         }elseif ($make == "") {

             echo "Not command amova_homova !"."\n";
             parsimony($user,$project,$path_in,$path_out);
             break;
           
         }
 

      }


       function corr_axes($user,$project,$path_in,$path_out){

          echo "corr_axes"."\n";
          $jobname = $user."_corr_axes";
          $make = "";

         # metadata
         if($GLOBALS['correlation'] == "meta" && $GLOBALS['file_metadata'] != "0"){

              # PCoA
             if($GLOBALS['check'] == "pcoa"){
                  # Community structure
                  if($GLOBALS['d_pcoa_st']  != "0"){
                    $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
                    for($i = 0 ; $i < sizeof($d_pcoa_st); $i++){

                       $make .= "corr.axes(axes=final.opti_mcc.".$d_pcoa_st[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, metadata=".$GLOBALS['file_metadata'].", method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                    }
                  }
                  # Community membership
                  if($GLOBALS['d_pcoa_me']  != "0"){
                      $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                      for($i = 0 ; $i < sizeof($d_pcoa_me); $i++){

                         $make .= "corr.axes(axes=final.opti_mcc.".$d_pcoa_me[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, metadata=".$GLOBALS['file_metadata'].", method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                      }
                  }

              }
             # NMDS
              elseif ($GLOBALS['check'] == "nmds") {
                 # Community structure
                 if($GLOBALS['d_nmds_st'] != "0"){
                      $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                      for($i = 0 ; $i < sizeof($d_nmds_st); $i++){

                         $make .= "corr.axes(axes=final.opti_mcc.".$d_nmds_st[$i].".".$GLOBALS['level'].".lt.ave.nmds.axes, metadata=".$GLOBALS['file_metadata'].", method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                       }
                  }
                  # Community membership
                  if($GLOBALS['d_nmds_me']  != "0"){
                      $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                      for($i = 0 ; $i < sizeof($d_nmds_me); $i++){
                        
                         $make .= "corr.axes(axes=final.opti_mcc.".$d_nmds_me[$i].".".$GLOBALS['level'].".lt.ave.nmds.axes, metadata=".$GLOBALS['file_metadata'].", method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";
 
                        
                      }
                 }

               }
         
        # otu
         }elseif ($GLOBALS['correlation'] == "otu") {
           # PCoA
           if($GLOBALS['check'] == "pcoa"){
                  # Community structure
                  if($GLOBALS['d_pcoa_st']  != "0"){
                    $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
                    for($i = 0 ; $i < sizeof($d_pcoa_st); $i++){

                        $make .= "corr.axes(axes=final.opti_mcc.".$d_pcoa_st[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, shared=final.opti_mcc.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                    }
                  }
                  # Community membership
                  if($GLOBALS['d_pcoa_me']  != "0"){
                      $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                      for($i = 0 ; $i < sizeof($d_pcoa_me); $i++){

                        $make .= "corr.axes(axes=final.opti_mcc.".$d_pcoa_me[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, shared=final.opti_mcc.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";


                      }
                  }

               }
               # NMDS
               elseif ($GLOBALS['check'] == "nmds") {
                 # Community structure
                 if($GLOBALS['d_nmds_st'] != "0"){
                      $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                      for($i = 0 ; $i < sizeof($d_nmds_st); $i++){

                          $make .= "corr.axes(axes=final.opti_mcc.".$d_nmds_st[$i].".".$GLOBALS['level'].".lt.ave.nmds.axes, shared=final.opti_mcc.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";


                       }
                  }
                  # Community membership
                  if($GLOBALS['d_nmds_me']  != "0"){
                      $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                      for($i = 0 ; $i < sizeof($d_nmds_me); $i++){
                        
                          $make .= "corr.axes(axes=final.opti_mcc.".$d_nmds_me[$i].".".$GLOBALS['level'].".lt.ave.nmds.axes, shared=final.opti_mcc.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                        
                      }
                 }
                

               }
         }
  
         # run command 
          if($make != ""){

           file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
               
               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){
                      
                      parsimony($user,$project,$path_in,$path_out);
                      break;
                        
                   }
              }   

         }elseif ($make == "") {
             
             echo "Not command corr_axes !"."\n";
             remove_logfile_mothur($path_out);
             break;
           
         }
        
        
      }



      function parsimony($user,$project,$path_in,$path_out){

           echo "parsimony"."\n";

           $jobname = $user."_parsimony";

           $make = "";

            # community structure
         if($GLOBALS['d_upgma_st'] != "0" && $GLOBALS['file_design'] != "0"){
            $d_upgma_st = explode(",", $GLOBALS['d_upgma_st']);
            for($i = 0 ; $i < sizeof($d_upgma_st); $i++){
              
             $make .="parsimony(tree=final.opti_mcc.".$d_upgma_st[$i].".".$GLOBALS['level'].".lt.ave.tre, group=".$GLOBALS['file_design'].", groups=all ,inputdir=$path_in,outputdir=$path_out)"."\n";
            }
         }
         # community membership
         if($GLOBALS['d_upgma_me'] != "0" && $GLOBALS['file_design'] != "0"){
            $d_upgma_me = explode(",", $GLOBALS['d_upgma_me']);
            for($i = 0 ; $i < sizeof($d_upgma_me); $i++){
              
             $make .="parsimony(tree=final.opti_mcc.".$d_upgma_me[$i].".".$GLOBALS['level'].".lt.ave.tre, group=".$GLOBALS['file_design'].", groups=all ,inputdir=$path_in,outputdir=$path_out)"."\n";
               
            }
         } 

        if($make != ""){   
           file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
              
             
               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){
                     
                      heatmap_bin_sim($user,$project,$path_in,$path_out);
                      break;
                     
                      
                   }
              } 
         }elseif ($make == "") {

             echo "Not command parsimony !"."\n";
             heatmap_bin_sim($user,$project,$path_in,$path_out);
             break;
           
         }          
                  
                   
     }


     

 # hide output
       
   function heatmap_bin_sim($user,$project,$path_in,$path_out){
       
         echo "heatmap_bin_sim"."\n";

         $jobname = $user."_heatmap_bin_sim";

         $make = "heatmap.bin(shared=final.opti_mcc.".$GLOBALS['level'].".subsample.shared, scale=log2, numotu=50 ,inputdir=$path_out,outputdir=$path_out)
                  heatmap.sim(phylip=final.opti_mcc.thetayc.".$GLOBALS['level'].".lt.ave.dist ,inputdir=$path_out,outputdir=$path_out)
                  heatmap.sim(phylip=final.opti_mcc.jclass.".$GLOBALS['level'].".lt.ave.dist ,inputdir=$path_out,outputdir=$path_out)";

        file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
        
        shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){

                      unifrac($user,$project,$path_in,$path_out);
                      
                      break;
                     
                      
                   }
              } 
    }



  # hide output
  function unifrac($user,$project,$path_in,$path_out) {


     echo "unifrac"."\n";

     $path_out_plot = "owncloud/data/$user/files/$project/output_plot/";

      $jobname = $user."_unifrac";

      $make = "";

            # community structure
         if($GLOBALS['d_upgma_st'] != "0" && $GLOBALS['file_design'] != "0"){
            $d_upgma_st = explode(",", $GLOBALS['d_upgma_st']);
            for($i = 0 ; $i < sizeof($d_upgma_st); $i++){
              
             $make .="unifrac.weighted(tree=final.opti_mcc.".$d_upgma_st[$i].".".$GLOBALS['level'].".lt.ave.tre, group=".$GLOBALS['file_design'].", random=T ,inputdir=$path_in,outputdir=$path_out)
                      unifrac.unweighted(tree=final.opti_mcc.".$d_upgma_st[$i].".".$GLOBALS['level'].".lt.ave.tre, group=".$GLOBALS['file_design'].", random=T, groups=all ,inputdir=$path_in,outputdir=$path_out)"."\n";

            }
         }
         # community membership
         if($GLOBALS['d_upgma_me'] != "0" && $GLOBALS['file_design'] != "0"){
            $d_upgma_me = explode(",", $GLOBALS['d_upgma_me']);
            for($i = 0 ; $i < sizeof($d_upgma_me); $i++){
              
             $make .="unifrac.weighted(tree=final.opti_mcc.".$d_upgma_me[$i].".".$GLOBALS['level'].".lt.ave.tre, group=".$GLOBALS['file_design'].", random=T ,inputdir=$path_in,outputdir=$path_out)
                      unifrac.unweighted(tree=final.opti_mcc.".$d_upgma_me[$i].".".$GLOBALS['level'].".lt.ave.tre, group=".$GLOBALS['file_design'].", random=T, groups=all ,inputdir=$path_in,outputdir=$path_out)"."\n";
               
            }
         } 

        if($make != ""){   
           file_put_contents($path_in.'/advance.batch', $make);
               $log = $GLOBALS['path_log'];
               $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
              
             
               shell_exec($cmd);
               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);
               
               $id_job = "" ; # give job id 
               foreach ($output as $key_var => $value ) {
              
                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }        
              }
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){

                      remove_logfile_mothur($path_out);
                      create_file_input_heatmap($user,$project,$path_in,$path_out);
                      break;
                     
                      
                   }
              } 
         }elseif ($make == "") {

             echo "Not command unifrac !"."\n";

             remove_logfile_mothur($path_out);
             create_file_input_heatmap($user,$project,$path_in,$path_out);
             break;
           
         }          


           
   }  

   # Remove log mothur

  function remove_logfile_mothur($path_out){ 
            
            $path_dir = $path_out;
            if (is_dir($path_dir)) {
                if ($read = opendir($path_dir)){
                      while (($logfile = readdir($read)) !== false) {
                        
                        $allowed =  array('logfile');
                        $ext = pathinfo($logfile, PATHINFO_EXTENSION);

                        if(in_array($ext,$allowed)) {
                           
                            unlink($path_dir.$logfile);
                        }
                      }
     
                   closedir($read);
                }
            } 
    }



   # Graph 

    function create_file_input_heatmap($user,$project,$path_in,$path_out){
     
     echo "Run create_file_input_heatmap"."\n";
     $jobname = $user."_create_file_input_heatmap";

     $log = $GLOBALS['path_log'];
     $cmd = "qsub -N $jobname -o $log  -cwd -j y -b y /usr/bin/php -f R_Script/create_input_heatmap_otu.php $user $project";
     
     exec($cmd);
     $check_qstat = "qstat  -j '$jobname' ";
     exec($check_qstat, $output);
     $id_job = ""; # give job id
     foreach ($output as $key_var => $value) {
         if ($key_var == "1") {
             $data = explode(":", $value);
             $id_job = $data[1];
         }
     }
     $loop = true;
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         if ($check_run == false) {

             echo "Go to create_file_input_abun ->"."\n";
             create_file_input_abun($user,$project,$path_in,$path_out);
             break;
         }
     }

 }


 function create_file_input_abun($user,$project,$path_in,$path_out){
    
    echo "Run create_file_input_abun "."\n";
    $jobname = $user ."_create_file_input_abun";

    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/php -f R_Script/create_input_abundance_otu.php $user $project";
    
    exec($cmd);
    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat, $output);
    $id_job = ""; # give job id
    foreach ($output as $key_var => $value) {
        if ($key_var == "1") {
            $data = explode(":", $value);
            $id_job = $data[1];
        }
    }
    $loop = true;
    while ($loop) {
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {

            echo "Go to create_input_alphash ->"."\n";
            create_input_alphash($user,$project,$path_in,$path_out);
            break;
        }
    }

}

function create_input_alphash($user,$project,$path_in,$path_out){
   
    echo "Run create_input_alphash "."\n";
    $jobname = $user ."_create_input_alphash";

    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/php -f R_Script/create_input_alphash_otu.php $user $project";
    
    exec($cmd);
    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat, $output);
    $id_job = ""; # give job id
    foreach ($output as $key_var => $value) {
        if ($key_var == "1") {
            $data = explode(":", $value);
            $id_job = $data[1];
        }
    }
    $loop = true;
    while ($loop) {
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {

            echo "Go to plot_graph_r_heartmap ->"."\n";
            plot_graph_r_heatmap($user,$project,$path_in,$path_out);
            break;
        }
    }

}




    function plot_graph_r_heatmap($user,$project,$path_in,$path_out){

     #file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot_graph_r_heatmap"."\n", FILE_APPEND);

     echo "Run plot_graph_r_heatmap "."\n";
     $path_input_csv = "owncloud/data/$user/files/$project/output/file_after_reverse.csv";
     $path_to_save = "owncloud/data/$user/files/$project/output/heartmap.png";
     $jobname = $user ."_plot_graph_r_heartmap";
     
     $log = $GLOBALS['path_log'];
     $cmd = "qsub -N $jobname -o $log  -cwd -j y -b y /usr/bin/Rscript R_Script/heatmapPlottest.R $path_input_csv $path_to_save";
     
     exec($cmd);
     $check_qstat = "qstat  -j '$jobname' ";
     exec($check_qstat, $output);
     $id_job = ""; # give job id
     foreach ($output as $key_var => $value) {
         if ($key_var == "1") {
             $data = explode(":", $value);
             $id_job = $data[1];
         }
     }
     $loop = true;
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         if ($check_run == false) {

             echo "Go to plot_graph_r_NMD ->"."\n"; 
             plot_graph_r_NMD($user,$project,$path_in,$path_out);
             break;
         }
     }

 }

 function plot_graph_r_NMD($user,$project,$path_in,$path_out){

    #file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot_graph_r_NMD"."\n", FILE_APPEND);

    echo "Run plot_graph_r_NMD "."\n";
    $path_input_axes = "owncloud/data/$user/files/$project/output/final.opti_mcc.thetayc.".$GLOBALS['level'].".lt.ave.nmds.axes";
    $path_to_save = "owncloud/data/$user/files/$project/output/NMD.png";
    $jobname = $user."_plot_graph_r_NMD";
     
    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/NMDSpcoaplottest.R $path_input_axes $path_to_save";
   
    exec($cmd);
    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat, $output);
    $id_job = ""; # give job id
    foreach ($output as $key_var => $value) {
        if ($key_var == "1") {
            $data = explode(":", $value);
            $id_job = $data[1];
        }
    }
    $loop = true;
    while ($loop) {
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {

            echo "Go to plot_graph_r_Rare->"."\n";
            plot_graph_r_Rare($user,$project,$path_in,$path_out);
            break;
        }
    }

}


function plot_graph_r_Rare($user,$project,$path_in,$path_out){

    #file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot_graph_r_Rare"."\n", FILE_APPEND);

    echo "Run plot_graph_r_Rare "."\n";
    $path_input_rarefaction = "owncloud/data/$user/files/$project/output/final.opti_mcc.groups.rarefaction";
    $path_to_save = "owncloud/data/$user/files/$project/output/Rare.png";
    $jobname = $user."_plot_graph_r_Rare";
    
    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/RarefactionSoiltest_otu.R $path_input_rarefaction $path_to_save";
   
    exec($cmd);
    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat, $output);
    $id_job = ""; # give job id
    foreach ($output as $key_var => $value) {
        if ($key_var == "1") {
            $data = explode(":", $value);
            $id_job = $data[1];
        }
    }
    $loop = true;
    while ($loop) {
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {

            echo "Go to plot_graph_r_Abun ->"."\n";
            plot_graph_r_Abun($user,$project,$path_in,$path_out);
            break;
        }
    }

}


function plot_graph_r_Abun($user,$project,$path_in,$path_out){

    #file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot_graph_r_Abun"."\n", FILE_APPEND);

    
    echo "Run plot_graph_r_Abun "."\n";
    $path_input_phylumex = "owncloud/data/$user/files/$project/output/file_phylum_count.txt";
    $path_to_save = "owncloud/data/$user/files/$project/output/Abun.png";
    $jobname = $user ."_plot_graph_r_Abun";
    
    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/Abundancebarplottest.R $path_input_phylumex $path_to_save";
   
    exec($cmd);
    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat, $output);
    $id_job = ""; # give job id
    foreach ($output as $key_var => $value) {
        if ($key_var == "1") {
            $data = explode(":", $value);
            $id_job = $data[1];
        }
    }
    $loop = true;
    while ($loop) {
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {

            echo "Go to plot_graph_r_Alphash ->"."\n";
            plot_graph_r_Alphash($user,$project,$path_in,$path_out);
            break;
        }
    }

}


function plot_graph_r_Alphash($user,$project,$path_in,$path_out){

    #file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot_graph_r_Aphash"."\n", FILE_APPEND);

    
    echo "Run plot_graph_r_Alphash "."\n";
    $path_input_chao_shannon = "owncloud/data/$user/files/$project/output/file_after_chao.txt";
    $path_to_save = "owncloud/data/$user/files/$project/output/Alpha.png";
    $jobname = $user."_plot_graph_r_Alphash";
    
    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/AlphachaoshannonSoil.R $path_input_chao_shannon $path_to_save";
    
    exec($cmd);
    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat, $output);
    $id_job = ""; # give job id
    foreach ($output as $key_var => $value) {
        if ($key_var == "1") {
            $data = explode(":", $value);
            $id_job = $data[1];
        }
    }
    $loop = true;
    while ($loop) {
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            
            echo "Go to change name ->"."\n";
            change_name($user,$project,$path_in,$path_out);
            break;
        }
    }

}

function change_name($user,$project,$path_in,$path_out){
    $dir = $path_out;
    $file_read = array( 'svg');
    $dir_ignore = array();
    $scan_result = scandir( $dir );

    foreach ( $scan_result as $key => $value ) {

        if (!in_array($value, array('.', '..'))) {

            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {

                if (in_array($value, $dir_ignore)) {
                    continue;
                }


            } else {

                $type = explode('.', $value);
                $type = array_reverse($type);
                if (in_array($type[0], $file_read)) {

                    $file_name = preg_split("/[.]/",$value );
                    if (in_array("bin", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "bin.svg");
                        echo $value." change to  bin.svg";
                    }
                    if (in_array("sharedsobs", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "sharedsobs.svg");
                        echo $value." change to sharedsobs.svg";
                    }
                    if (in_array("jclass", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "jclass.svg");
                        echo $value." change to jclass.svg";
                    }
                    if (in_array("thetayc", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "thetayc.svg");
                        echo $value." change to thetayc.svg";
                    }
                }
            }
        }
    }
}


 






?>