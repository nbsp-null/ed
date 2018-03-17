<?php
		
error_reporting("E_WARNING") ;
      $link=mysqli_connect("{$_POST['hostname']}","{$_POST['username']}","{$_POST['password']}");
	  if(isset($_POST['dbname'])){
		   $sql="show databases";
		   $res=mysqli_query($link,$sql);
		   $databases=array();
		   $bool=false;
           while($key=mysqli_fetch_assoc($res)){	
		   if($key['Database']==strtolower($_POST['dbname'])){
			 $bool=true;
		    }
	       }
		   if($bool){
			   $data['status']=3;
			   echo json_encode($data);
			   die;
		   }
		 
	  }
		
	  if(is_object($link)){
		$data['status']=1;
		$data['info']="恭喜你，你可以成功安装EduWork进行工作！";

		echo json_encode($data);
		die;
	  }else{
		$data['status']=2;
		$data['info']="请检查MySQL连接数据是否正确";
		echo json_encode($data);
		die;
	  }
