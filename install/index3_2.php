<?php

@$falg=file_get_contents("../install.lock");
if($falg==true){
	header('location:./ass.php');die;
}

error_reporting("E_WARNING") ;
if($_SERVER['REQUEST_METHOD']!='POST'){
    echo "<script>window.location.href='index3_1.php'</script>";
}

$info = file_get_contents("../application/database.php");

foreach($_POST as $k=>$v){
    $info = preg_replace("/\'({$k})\'\s{0,}=>\s{0,}\'(.*)\'/","'{$k}'=>'{$v}'",$info);
}
//3. 将替换后的信息写回到配置文件中
file_put_contents("../application/database.php",$info);



//header('location:install/index.php');die;
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
				<p class="clear_box_hj">管理员设置</p>
				<div class="clear_box_nr">
				<form action="index4.php" method="post" id="fm">
					<div class="cs_nr">
						<div style="height: 40px;"></div>
						<span>用户名</span>
						<span style="text-align: left"> <input id="adminuser" name="adminuser"  type="text" value="EduWork"/> </span>
						<span>密码</span>
						<span style="text-align: left"><input  id="adminpwd" name="adminpwd"  type="text" /></span>
						<div style="clear: both"></div>
						<div  id='admintodo' style="height: 40px;line-height: 40px;margin-top: 20px;text-align: center;color: #ff3000;"></div>

					</div>
					
						 <input  name="hostname"  type="hidden" value="<?php echo $_POST['hostname'] ?>"/>
					
						<input   name="database"  type="hidden" value="<?php echo $_POST['database'] ?>"/>
					
						<input name="username"  type="hidden" value="<?php echo $_POST['username'] ?>"/>
						
						<input  name="password"   type="hidden" value="<?php echo $_POST['password'] ?>" />
				</div>


			</div>
		</div>
		
		<div class="button">

			<div class="ben_tishi">恭喜你，你可以成功安装EduWork进行工作！</div>

			
				<input type="submit" class="next_btn2" id="next_btn" value="下一步"  />
			</form>
			<div class="huise_bg"></div>
			<div class="lanse_bg3"></div>
			<div class="shuzi benone">1</div>
			<div class="shuzi bentwo">2</div>
			<div class="shuzi benthree">3</div>
			<div class="shuzi_2 benfour">4</div>
			<div class="shuzi_2 benfive">5</div>

		</div>
				<script src="./js/jquery.min.js"></script>
<script>
    $(function(){
       $("#fm").submit(function(){

  
           var adminuser = $("#adminuser").val();
           var adminpwd = $("#adminpwd").val();
         
           if(adminuser.match(/[^A-Za-z$]/)){
               admintodo.innerHTML="用户名格式不对，你可以使用大小写字母！！！";
               return false;
           }
           if(adminpwd.match(/^\s*$/)){
               admintodo.innerHTML="密码不能为空，你可以使用大小写字母！！！";
               return false;
           }
        });

    })

</script>
	</body>

</html>
