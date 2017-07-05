<?php  

    include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");

         $user = $argv[1];
         $project = $argv[2];
         $path_in = $argv[3];
         $path_out = $argv[4];



         if($user != "" && $project != "" && $path_in != "" && $path_out != "" ){
             
             //collect_rarefaction_summary($user,$project,$path_in,$path_out);

         }else{
              echo "user : ".$user."\n";
              echo "project : ".$project."\n"; 
              echo "path_in : ".$path_in."\n";
              echo "path_out : ".$path_out."\n";
              
         }
    




    function collect_rarefaction_summary($user,$project,$path_in,$path_out){
           echo "collect_rarefaction_summary"."\n";

           $jobname = $user."_collect_rarefaction_summary";

            $cmd = "collect.single(shared=final.tx.shared, calc=chao, freq=100,inputdir=$path_in,outputdir=$path_out)
                    rarefaction.single(shared=final.tx.shared, calc=sobs, freq=100, processors=8,inputdir=$path_in,outputdir=$path_out)
                    summary.single(shared=final.tx.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon, subsample=5000,inputdir=$path_in,outputdir=$path_out)";
        
             file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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

              $cmd = "dist.shared(shared=final.tx.shared, calc=thetayc-jclass-lennon-morisitahorn-braycurtis, subsample=5000,inputdir=$path_in,outputdir=$path_out)
                      summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=soils1_1-soils2_1-soils3_1-soils4_1, all=T,inputdir=$path_in,outputdir=$path_out)";
          
              file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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
              
              echo "vene"."\n";

              $jobname = $user."_venn";

              $cmd = "venn(shared=final.tx.2.subsample.shared, groups=soils1_1-soils2_1-soils3_1-soils4_1,inputdir=$path_in,outputdir=$path_out)";

              file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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


        

       function tree_shared($user,$project,$path_in,$path_out){

            // tree.shared(phylip=final.tx.thetayc.2.lt.ave.dist)
			// tree.shared(phylip=final.tx.morisitahorn.2.lt.ave.dist)
			// tree.shared(phylip=final.tx.jclass.2.lt.ave.dist)
			// tree.shared(phylip=final.tx.braycurtis.2.lt.ave.dist)
			// tree.shared(phylip=final.tx.lennon.2.lt.ave.dist)
       }

       function parsimony($user,$project,$path_in,$path_out){
       	   // parsimony(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design,  groups=all) 
       }

       
       function pcoa($user,$project,$path_in,$path_out){
       	   	//pcoa(phylip=final.tx.morisitahorn.2.lt.ave.dist)
			//pcoa(phylip=final.tx.thetayc.2.lt.ave.dist)
			//pcoa(phylip=final.tx.jclass.2.lt.ave.dist)
       }
       function nmds($user,$project,$path_in,$path_out){
       	//nmds(phylip=final.tx.morisitahorn.2.lt.ave.dist, mindim=3, maxdim=3)
		//nmds(phylip=final.tx.thetayc.2.lt.ave.dist, mindim=2, maxdim=2)
		//nmds(phylip=final.tx.jclass.2.lt.ave.dist, mindim=3, maxdim=3)
       }

       function amova_homova_corr($user,$project,$path_in,$path_out){

       	//amova(phylip=final.tx.thetayc.2.lt.ave.dist, design=soil.design) #No need
		//homova(phylip=final.tx.thetayc.2.lt.ave.dist, design=soil.design)
		//corr.axes(axes=final.tx.thetayc.2.lt.ave.nmds.axes, shared=final.tx.2.subsample.shared, method=spearman, numaxes=2, label=2)
		//corr.axes(axes=final.tx.thetayc.2.lt.ave.nmds.axes, metadata=soilpro.metadata, method=pearson, numaxes=2, label=2)

       }

       
       # hide output
       
       function heatmap_bin_sim($user,$project,$path_in,$path_out){
    	//heatmap.bin(shared=final.tx.2.subsample.shared, scale=log2, numotu=10) 
    	//heatmap.sim(phylip=final.tx.thetayc.2.lt.ave.dist) 
        //heatmap.sim(phylip=final.tx.jclass.2.lt.ave.dist) 
       }

       function unifrac($user,$project,$path_in,$path_out) {
       		//unifrac.weighted(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design, random=T) #No need
			//unifrac.unweighted(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design, random=T, groups=all) #No need
       }


?>