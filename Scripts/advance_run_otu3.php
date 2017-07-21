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

                       $make .= "corr.axes(axes=final.tx.".$d_pcoa_st[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, metadata=".$GLOBALS['file_metadata'].", method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                    }
                  }
                  # Community membership
                  if($GLOBALS['d_pcoa_me']  != "0"){
                      $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                      for($i = 0 ; $i < sizeof($d_pcoa_me); $i++){

                         $make .= "corr.axes(axes=final.tx.".$d_pcoa_me[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, metadata=".$GLOBALS['file_metadata'].", method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                      }
                  }

              }
             # NMDS
              elseif ($GLOBALS['check'] == "nmds") {
                 # Community structure
                 if($GLOBALS['d_nmds_st'] != "0"){
                      $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                      for($i = 0 ; $i < sizeof($d_nmds_st); $i++){

                         $make .= "corr.axes(axes=final.tx.".$d_nmds_st[$i].".".$GLOBALS['level'].".lt.ave.nmds.axes, metadata=".$GLOBALS['file_metadata'].", method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                       }
                  }
                  # Community membership
                  if($GLOBALS['d_nmds_me']  != "0"){
                      $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                      for($i = 0 ; $i < sizeof($d_nmds_me); $i++){
                        
                         $make .= "corr.axes(axes=final.tx.".$d_nmds_me[$i].".".$GLOBALS['level'].".lt.ave.nmds.axes, metadata=".$GLOBALS['file_metadata'].", method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";
 
                        
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

                        $make .= "corr.axes(axes=final.tx.".$d_pcoa_st[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, shared=final.tx.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                    }
                  }
                  # Community membership
                  if($GLOBALS['d_pcoa_me']  != "0"){
                      $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                      for($i = 0 ; $i < sizeof($d_pcoa_me); $i++){

                        $make .= "corr.axes(axes=final.tx.".$d_pcoa_me[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, shared=final.tx.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";


                      }
                  }

               }
               # NMDS
               elseif ($GLOBALS['check'] == "nmds") {
                 # Community structure
                 if($GLOBALS['d_nmds_st'] != "0"){
                      $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                      for($i = 0 ; $i < sizeof($d_nmds_st); $i++){

                          $make .= "corr.axes(axes=final.tx.".$d_nmds_st[$i].".".$GLOBALS['level'].".lt.ave.nmds.axes, shared=final.tx.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";


                       }
                  }
                  # Community membership
                  if($GLOBALS['d_nmds_me']  != "0"){
                      $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                      for($i = 0 ; $i < sizeof($d_nmds_me); $i++){
                        
                          $make .= "corr.axes(axes=final.tx.".$d_nmds_me[$i].".".$GLOBALS['level'].".lt.ave.nmds.axes, shared=final.tx.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                        
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

                       $make .= "corr.axes(axes=final.opti_mcc.".$d_pcoa_st[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, metadata=".$GLOBALS['file_metadata'].",method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                    }
                  }
                  # Community membership
                  if($GLOBALS['d_pcoa_me']  != "0"){
                      $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                      for($i = 0 ; $i < sizeof($d_pcoa_me); $i++){

                           $make .= "corr.axes(axes=final.opti_mcc.".$d_pcoa_me[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, metadata=".$GLOBALS['file_metadata'].",method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                      }
                  }

              }
             # NMDS
              elseif ($GLOBALS['check'] == "nmds") {
                 # Community structure
                 if($GLOBALS['d_nmds_st'] != "0"){
                      $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                      for($i = 0 ; $i < sizeof($d_nmds_st); $i++){

                           $make .= "corr.axes(axes=final.opti_mcc.".$d_nmds_st[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, metadata=".$GLOBALS['file_metadata'].",method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                       }
                  }
                  # Community membership
                  if($GLOBALS['d_nmds_me']  != "0"){
                      $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                      for($i = 0 ; $i < sizeof($d_nmds_me); $i++){
                        
                       $make .= "corr.axes(axes=final.opti_mcc.".$d_nmds_me[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, metadata=".$GLOBALS['file_metadata'].",method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";
 
                        
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

                          $make .= "corr.axes(axes=final.opti_mcc.".$d_nmds_st[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, shared=final.opti_mcc.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";


                       }
                  }
                  # Community membership
                  if($GLOBALS['d_nmds_me']  != "0"){
                      $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                      for($i = 0 ; $i < sizeof($d_nmds_me); $i++){
                        
                            $make .= "corr.axes(axes=final.opti_mcc.".$d_nmds_me[$i].".".$GLOBALS['level'].".lt.ave.pcoa.axes, shared=final.opti_mcc.".$GLOBALS['level'].".subsample.shared, method=".$GLOBALS['method'].", numaxes=".$GLOBALS['axes'].", label=".$GLOBALS['level'].",inputdir=$path_in,outputdir=$path_out)"."\n";

                        
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

                      remove_logfile_mothur($path_out);
                      break;
                     
                      
                   }
              } 
         }elseif ($make == "") {

             echo "Not command parsimony !"."\n";
             break;
           
         }          
                  
                   
     }


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




 //$make = "system(mv ".$path_out."stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta  ".$path_out."final.opti_mcc.0.03.subsample.spearman.corr.axesThetayc3D ,outputdir=$path_out)";

// system(mv final.opti_mcc.0.03.subsample.spearman.corr.axes final.opti_mcc.0.03.subsample.spearman.corr.axesThetayc3D)


# hide output

// heatmap.bin(shared=final.opti_mcc.0.03.subsample.shared, scale=log2, numotu=50)
// heatmap.sim(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist)
// heatmap.sim(phylip=final.opti_mcc.jclass.0.03.lt.ave.dist)
// unifrac.weighted(tree=final.opti_mcc.thetayc.0.03.lt.ave.tre, group=soil.design, random=T)
// unifrac.unweighted(tree=final.opti_mcc.thetayc.0.03.lt.ave.tre, group=soil.design, random=T, groups=all)



?>