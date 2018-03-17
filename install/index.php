<?php 

@$falg=file_get_contents("../install.lock");
if($falg==true){
	header('location:./ass.php');die;
}

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
				<p class="clear_box_font"><font>安装</font>从未如此<font>性感</font>！！</p>
				<img src="images/fj.png" class="fj"/>
			</div>
		</div>
		
		<div class="button">
			<form action="index2.php"  method="post">
				<input type="checkbox" value="我已经阅读并同意EduWork安装协议" class="xy_btn" id="xy_btn" onclick="xyBtn()"/><span style="top: 20px"><a target="_blank" href="http://www.eduwork.cc/index.php?ac=article&at=read&did=5"> 我已经阅读并同意EduWork安装协议</a></span><br />
				<input type="button" onclick="document.getElementById('xy_btn').checked ?window.location.href='index2.php' : alert('您必须同意软件许可协议才能安装！');" class="next_btn" id="next_btn" value="下一步"  />
			</form>
			<div class="huise_bg"></div>
			<!--<div class="lanse_bg"></div>-->
			<div class="shuzi benone">1</div>
			<div class="shuzi_2 bentwo">2</div>
			<div class="shuzi_2 benthree">3</div>
			<div class="shuzi_2 benfour">4</div>
			<div class="shuzi_2 benfive">5</div>

		</div>
	</body>
	<script type="text/javascript">

		function xyBtn() {
			var xyBtn=document.getElementById("xy_btn").checked;
			if(!xyBtn){
				var next_btn=document.getElementById("next_btn");
				next_btn.style.backgroundColor="#b7b7b7";
				next_btn.style.color="#666";
				next_btn.style.borderColor="#b7b7b7";
			} else {
				var next_btn=document.getElementById("next_btn");
				next_btn.style.backgroundColor="#1774d3";
				next_btn.style.color="#fff";
				next_btn.style.borderColor="#0050a1";
			}
		}
	</script>
</html>
