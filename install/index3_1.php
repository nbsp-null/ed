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
				<p class="clear_box_hj">环境监测</p>
				<div class="clear_box_nr">
			<form action="index3_2.php" method="post" id="fm">
					<div class="cs_nr">
						<div style="height: 10px;"></div>
						<span>数据库主机</span>
						<span> <input  type="text"  name="hostname" id="dbhost"value="localhost"/> </span>
						<span>数据库名称</span>
						<span><input   name="database" id="dbname" type="text" value="EduWork"/></span>
						<span>数据库用户名</span>
						<span><input name="username" id="dbuser" type="text"/></span>
						<span>数据库密码</span>
						<span><input  name="password" id="dbpwd"  type="password" /></span>

					</div>


				</div>


			</div>
		</div>
		
		<div class="button">

			<div class="ben_tishi" id="bentishi">请配置您的安装信息！</div>

			
				<input type="submit" class="next_btn" id="next_btn" value="下一步"  />
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
    $(function() {
		$("#dbpwd").val("");
		$("#fm").submit(function () {
			var a=$("#next_btn").is('.next_btn2');
			if(a==false){
				alert("数据库账号密码错误");
				return false;
			}
			var dbname = $("#dbname").val();
			var dbhost = $("#dbhost").val();
			var dbuser = $("#dbuser").val();
			var dbpwd = $("#dbpwd").val();

			if (dbhost.length == 0) {
				alert("数据库主机不能为空");
				return false;
			}
			if (dbuser.length == 0) {
				alert("数据库用户不能为空");
				return false;
			}

			if (dbname.match(/[^A-Za-z]/)) {
				alert("数据库名称必须全英文");
				return false;
			}

			$.ajax({
				type: "post",
				url: "./do.php",
				data: "hostname=" + dbhost+ "&username="+dbuser+ "&password="+dbpwd+"&dbname="+dbname,
				async:false,
				dataType : "json",
				success : function (result) {
					if(result.status==3){
						 bool=confirm('已有此数据库，是否确认用此数据库名称');
						if(bool==true){
								return true;
							}else{
								$("#dbname").val("");
								$("#dbpwd").val("");
								return false;
						}

					}

				}

			});
			return bool;
		});



		$('#dbuser').keyup(function ()
		{
			var dbname = $("#dbname").val();
			var dbhost = $("#dbhost").val();
			var dbuser = $("#dbuser").val();
			var dbpwd = $("#dbpwd").val();

			if (dbhost.length == 0) {
				alert("数据库主机不能为空");
				return false;
			}
			if (dbuser.length == 0) {
				alert("数据库用户不能为空");
				return false;
			}

			if (dbname.match(/[^A-Za-z]/)) {
				alert("数据库名称必须全英文");
				return false;
			}

			$.ajax({
				type: "post",
				url: "./do.php",
				data: "hostname=" + dbhost+ "&username="+dbuser+ "&password="+dbpwd,
				dataType : "json",
				success : function (result) {
					if(result.status==1){
						bentishi.innerHTML=result.info;
						$("#next_btn").removeClass();
						$("#next_btn").addClass("next_btn2");
					}
					if(result.status==2){
						bentishi.innerHTML=result.info;
						$("#next_btn").removeClass();
						$("#next_btn").addClass("next_btn");
						}
				}
			});



		})


		$('#dbpwd').keyup(function ()
		{
			var dbname = $("#dbname").val();
			var dbhost = $("#dbhost").val();
			var dbuser = $("#dbuser").val();
			var dbpwd = $("#dbpwd").val();

			if (dbhost.length == 0) {
				alert("数据库主机不能为空");
				return false;
			}
			if (dbuser.length == 0) {
				alert("数据库用户不能为空");
				return false;
			}

			if (dbname.match(/[^A-Za-z]/)) {
				alert("数据库名称必须全英文");
				return false;
			}

			$.ajax({
				type: "post",
				url: "./do.php",
				data: "hostname=" + dbhost+ "&username="+dbuser+ "&password="+dbpwd,
				dataType : "json",
				success : function (result) {
					if(result.status==1){
						bentishi.innerHTML=result.info;
						$("#next_btn").removeClass();
						$("#next_btn").addClass("next_btn2");
					}
					if(result.status==2){
						bentishi.innerHTML=result.info;
						$("#next_btn").removeClass();
						$("#next_btn").addClass("next_btn");
						}
				}
			});



		})





	})



</script>
	</body>

</html>
