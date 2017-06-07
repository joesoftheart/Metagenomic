<html>
	<head>


	<script src="<?php echo base_url('js/jquery-1.8.3.js'); ?>"></script>
		
	</head>
	<body>
	      <H2>Check Run Advance</H2>	
      

       <div id="time">30</div>
       <div id="test">run queue</div>

	</body>


	<script type="text/javascript">
      $(window).ready(function(){
          var time = 30;
         

          setInterval(function(){   
          	time--;
            $('#time').html(time);

            if(time === 0){

            	$.ajax({ 
            		type:"post",
            		datatype:"json",
            		url:"<?php echo base_url('Run_owncloud/check_run'); ?>",
                data:{id_job: <?php echo $id_job ?>},
                success:function(data){
                      console.log("data :" + data);
                      $('#test').html(data);
                       time = 30;
                    },
                error:function(e){
                      console.log(e.message);
                    }
            	});

            	//location.reload();
            }
          },1000);
     
      });

	</script>


     

</html>