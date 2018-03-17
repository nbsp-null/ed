<?php
@$falg=file_get_contents("../install.lock");
if($falg==true){
	header('location:./ass.php');die;
}

 $phpv = phpversion();
    $sp_os = PHP_OS;
    $sp_server = $_SERVER['SERVER_SOFTWARE'];
    $sp_host = (empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR']);
    $sp_name = $_SERVER['SERVER_NAME'];

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>安装程序 - EduWork管理系统</title>
		<link rel="stylesheet" type="text/css" href="css/install.css"/>

	</head>
	<body>
		<!-- 上面 -->
		<div class="top">
			<div class="clear_box">
				<img src="images/logo.png" class="logo"/>
				<span>中小学单校区版</span>
				<div style="clear: both;"></div>
				<p class="clear_box_hj">环境监测</p>
				<div class="clear_box_nr">
					<div class="cs_top">
						<span>参数</span>
						<span>值</span>
					</div>
					<div class="cs_nr">
						<div style="height: 10px;"></div>
						<span>服务器域名</span>
						<span><?php echo $sp_name; ?></span>
						<span>操作系统</span>
						<span><?php echo $sp_os; ?></span>
						<span>解析引擎</span>
						<span><?php echo substr($sp_server,0,21); ?></span>
						<span>PHP版本</span>
						<span><?php echo $phpv; ?></span>
					</div>


				</div>


			</div>
		</div>
		
		<div class="button">

			<div class="ben_tishi">恭喜你，你可以成功安装EduWork进行工作！</div>

			<form action="index3_1.php"  method="post" >
				<input type="submit" class="next_btn2" id="next_btn" value="下一步"  />
			</form>
			<div class="huise_bg"></div>
			<div class="lanse_bg2"></div>
			<div class="shuzi benone">1</div>
			<div class="shuzi bentwo">2</div>
			<div class="shuzi_2 benthree">3</div>
			<div class="shuzi_2 benfour">4</div>
			<div class="shuzi_2 benfive">5</div>

		</div>
	</body>

</html>
