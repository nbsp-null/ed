<?php

	$a=$_SERVER['SCRIPT_NAME'];
	$arr=explode('/',$a);
	foreach  ($arr as $k=>$v){
			if($arr[$k]=='install'){
				$a=$k;
			}
	}
	$b='';
	for($i=0;$i<=$a-1;$i++){
		$b.=$arr[$i].'/';
	}
	if($b==null)$b='/';
	file_put_contents("../install.lock","1");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>安装程序 - EduWork管理系统</title>
		<link rel="stylesheet" type="text/css" href="css/install.css"/>
		  <script type="text/javascript" src="js/jquery.min.js"></script>
    <script >


    </script>
	</head>
	<body>
		<!-- 上面 -->
		<div class="top">
			<div class="clear_box">
				<img src="images/logo.png" class="logo"/>
				<span>中小学单校区版</span>
				<div style="clear: both;"></div>
				<img src="images/cwu.png" class="zque" />


			</div>
		</div>
		
		<div class="button">
			
			<div class="ben_tishi">也许你不敢相信，但是事实就是事实，你已经安装过我们的系统了<br />
				虽然你很<font>爱</font>我，但是我们<font>做</font>一次就够了</div>

			<form action="/" method="post" >
				<a href="<?php echo $b;?>"><input type="button" class="next_btn_1" id="next_btn" value="现在登录"  /></a>
			</form>

		</div>
	</body>

</html>
