<?php
set_time_limit ( 0 );
@$falg=file_get_contents("../install.lock");
if($falg==true){
	header('location:./ass.php');die;
}
if($_SERVER['REQUEST_METHOD']!='POST'){
    echo "<script>window.location.href='index3_1.php'</script>";
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>安装程序 - EduWork管理系统</title>
		<link rel="stylesheet" type="text/css" href="css/install.css"/>
		<script type="text/javascript">
function showmessage(message) {
                 document.getElementById('azts').innerHTML = message ;
                 document.getElementById('azts').scrollTop = 100;
}
</script>
	</head>
	<body>
		<!-- 上面 -->
		<div class="top">
			<div class="clear_box">
				<img src="images/logo.png" class="logo"/>
				<span>中小学单校区版</span>
				<div style="clear: both;"></div>
				<img src="images/chilun.png" class="chilun" />
				<p class="clear_box_font2">程序猿<font>飞哥</font>和工程师<font>雷哥</font>正在为你搭建牢固的城墙！！</p>
			</div>
		</div>
		
		<div class="button">

			<div class="ben_tishi" id="azts">请稍后...</div>

			<form action="" >
				<input type="button" class="next_btn2" id="next_btn" value="下一步"  />
			</form>
			<div class="huise_bg"></div>
			<div class="lanse_bg4"></div>
			<div class="shuzi benone">1</div>
			<div class="shuzi bentwo">2</div>
			<div class="shuzi benthree">3</div>
			<div class="shuzi benfour">4</div>
			<div class="shuzi_2 benfive">5</div>

		</div>

	</body>

</html>
<?php


$admin = $_POST['adminuser'];
$password = $_POST['adminpwd'];

$link=mysqli_connect("{$_POST['hostname']}","{$_POST['username']}","{$_POST['password']}") or die("error");

date_default_timezone_set("PRC");
$time=date("Y-m-d");
$sql="create database {$_POST['database']} ";
$bool=mysqli_query($link,$sql);
//if($bool==false){
//
//	$sql="use {$_POST['database']}";
//	mysqli_query($link,$sql);
//	$sql="show tables";
//	$res=mysqli_query($link,$sql);
//	$arr=array();
//	while($TableName=mysqli_fetch_assoc($res)){
//		$value=array_values($TableName);
//		array_push($arr,$value[0]);
//	}
//	$sql="select * from ew_menus_copy ";
//	$result=mysqli_query($link,$sql);
//	if($result==true&&count($arr)>=22){
//		$info = file_get_contents("../index.php");
//		$info=preg_replace("/header\(\'location\:install\/index\.php\'\);die;/"," ", $info);
//		file_put_contents("../index.php",$info);
//		mysqli_close($link);
//		echo "<meta http-equiv=\"refresh\" content=\"1; url=ass.php\" />";
//		die;
//	}
//
//}

mysqli_select_db($link,$_POST['database']);
mysqli_set_charset($link,'utf8');
$sql="CREATE TABLE ew_admin (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '管理员用户名',
  password varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '密码',
  name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '管理员显示姓名',
  typeid int(11) DEFAULT NULL COMMENT '类型',
  phone varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '电话',
  mail varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '邮箱',
  avatar varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '头像',
  regtime datetime DEFAULT NULL COMMENT '注册时间',
  campusid int(11) DEFAULT '1' COMMENT '校区id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户表';";

$bool=mysqli_query($link,$sql);


$sql = "INSERT INTO `ew_admin` VALUES ('1', '{$admin}', '{$password}', 'admin', '1', '332232323', 'eee@qq.com', '', '{$time}', '1');";
$bool=mysqli_query($link,$sql);

$sql = "
CREATE TABLE ew_campus (
  id int(11) NOT NULL AUTO_INCREMENT,
  campus_name varchar(255) DEFAULT NULL,
  campus_address varchar(255) DEFAULT NULL COMMENT '校区地址',
  campus_createtime date DEFAULT NULL COMMENT '创建时间',
  campus_remark varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='校区表';";

$bool=mysqli_query($link,$sql);

$sql = "CREATE TABLE ew_admintype (
  id int(11) NOT NULL,
  name varchar(255) DEFAULT NULL COMMENT '类型名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员类型表 备用';";
$bool=mysqli_query($link,$sql);


$sql= "CREATE TABLE ew_called (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  called_studentid int(255) DEFAULT NULL COMMENT '学生id',
  called_scheduleid int(10) unsigned NOT NULL COMMENT '对应排课表里的id',
  called_evaluate varchar(255) DEFAULT NULL COMMENT '上课评价',
  called_status int(11) DEFAULT NULL,
  called_absent varchar(255) DEFAULT NULL COMMENT '旷课原因',
  campusid int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=gbk;";
$bool=mysqli_query($link,$sql);


$sql = "CREATE TABLE ew_classes (
  id int(11) NOT NULL AUTO_INCREMENT,
  classes_name varchar(255) DEFAULT NULL COMMENT '班级名称',
  classes_headteacher int(11) DEFAULT NULL COMMENT '班主任 老师ID',
  classes_lessonteacher int(11) DEFAULT NULL COMMENT '任课老师 老师ID',
  classes_courseid int(11) DEFAULT NULL COMMENT '课程ID',
  classes_classroomid int(11) DEFAULT NULL COMMENT '教室ID',
  classes_planstudents int(11) DEFAULT NULL COMMENT '预招学生数',
  classes_plantimes varchar(255) DEFAULT NULL COMMENT '计划课时次数',
  campusid int(11) DEFAULT NULL COMMENT '所属校区',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COMMENT='班级表';";
$bool=mysqli_query($link,$sql);

$sql = "CREATE TABLE ew_classroom (
  id int(11) NOT NULL AUTO_INCREMENT,
  classroom_name varchar(255) DEFAULT NULL,
  classroom_containnum int(11) DEFAULT NULL COMMENT '可容纳人数',
  campusid int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='教室表';";
$bool=mysqli_query($link,$sql);

$sql = "CREATE TABLE ew_config (
  id int(11) NOT NULL AUTO_INCREMENT,
  type int(11) DEFAULT '0' COMMENT '类型 0不可修改 1可修改',
  name varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '名称',
  value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '值',
  remark varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=134 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='全局配置表';";

$bool=mysqli_query($link,$sql);

$sql = "CREATE TABLE ew_course (
  id int(11) NOT NULL AUTO_INCREMENT,
  course_name varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '课程名称',
  course_grade_id varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '年级id',
  course_subject_id varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '科目ID',
  course_type varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '类型',
  course_unitprice double(11,0) DEFAULT NULL COMMENT '一课时的价钱',
  course_periodnum int(11) DEFAULT NULL COMMENT '课时数',
  course_status tinyint(4) DEFAULT '0' COMMENT '0停用 1启用',
  course_total int(11) DEFAULT NULL,
  campusid int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='课程表';";

$bool=mysqli_query($link,$sql);


$sql = "CREATE TABLE ew_feedback (
  id int(11) NOT NULL AUTO_INCREMENT,
  feedback_classid int(11) DEFAULT NULL COMMENT '班级ID',
  feedback_teacherid int(11) DEFAULT NULL COMMENT '老师ID',
  feedback_studentid int(11) DEFAULT NULL COMMENT '学生ID',
  feedback_type tinyint(1) DEFAULT '0' COMMENT '类型 默认0老师给学生 1学生给老师',
  feedback_content varchar(255) DEFAULT NULL COMMENT '评论内容',
  feedback_gradeid int(11) DEFAULT NULL COMMENT '评价等级 备用',
  feedback_time datetime DEFAULT NULL COMMENT '评论时间',
  campusid int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='师生评价表';";


$bool=mysqli_query($link,$sql);

$sql = "CREATE TABLE ew_grade (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT '年级ID',
  grade_name varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '年级名称',
  grade_remark varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '备注',
  campusid int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='年级表';";


$bool=mysqli_query($link,$sql);

$sql = "CREATE TABLE ew_menus (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent_menuid int(11) NOT NULL,
  menu_value varchar(255) COLLATE utf8_bin NOT NULL,
  menu_name varchar(255) COLLATE utf8_bin NOT NULL,
  menu_iconclass varchar(255) COLLATE utf8_bin NOT NULL,
  status int(11) DEFAULT '1' COMMENT '0 禁用 1启用 默认启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1008 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='菜单表';";


$bool=mysqli_query($link,$sql);


$sql = "CREATE TABLE ew_menus_copy (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent_menuid int(11) NOT NULL,
  menu_value varchar(255) COLLATE utf8_bin NOT NULL,
  menu_name varchar(255) COLLATE utf8_bin NOT NULL,
  menu_iconclass varchar(255) COLLATE utf8_bin NOT NULL,
  status int(11) DEFAULT '1' COMMENT '0 禁用 1启用 默认启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1005 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='菜单表';";


$bool=mysqli_query($link,$sql);


$sql = "CREATE TABLE ew_notice (
  id int(11) NOT NULL AUTO_INCREMENT,
  creator int(11) DEFAULT NULL COMMENT '创建人',
  title varchar(255) DEFAULT NULL COMMENT '公告标题',
  contents varchar(10000) DEFAULT NULL COMMENT '公告内容',
  time datetime DEFAULT NULL COMMENT '发布时间',
  type int(11) DEFAULT NULL COMMENT '公告类型 1分区后台公告 2学生端公告 3老师端公告',
  status int(11) DEFAULT '0' COMMENT '是否发布 0否 1是',
  campusid int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='公告表';";


$bool=mysqli_query($link,$sql);



$sql = "CREATE TABLE ew_permission (
  id int(11) NOT NULL AUTO_INCREMENT,
  parentid int(11) DEFAULT NULL COMMENT '父节点ID',
  name varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '名称',
  user_typeid int(11) DEFAULT NULL COMMENT '用户类型id',
  authname varchar(20) COLLATE utf8_bin DEFAULT '1' COMMENT '权限名',
  authvalue int(11) DEFAULT '0' COMMENT '权限值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='权限表 备用';";


$bool=mysqli_query($link,$sql);



$sql = "CREATE TABLE ew_personalconfig (
  id int(11) NOT NULL AUTO_INCREMENT,
  userid int(11) NOT NULL,
  name varchar(255) COLLATE utf8_bin NOT NULL,
  value int(11) NOT NULL,
  remark varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='个性化设置表';";


$bool=mysqli_query($link,$sql);



$sql = "CREATE TABLE ew_schedule (
  id int(11) NOT NULL AUTO_INCREMENT,
  schedule_classid int(11) NOT NULL COMMENT '班级ID',
  schedule_courseid int(11) NOT NULL,
  schedule_gradeid int(11) NOT NULL,
  schedule_teacherid int(11) NOT NULL COMMENT '任课老师ID',
  schedule_classroomid int(11) NOT NULL COMMENT '教室ID',
  schedule_starttime datetime DEFAULT NULL COMMENT '开始时间',
  schedule_endtime datetime DEFAULT NULL COMMENT '结束时间',
  schedule_classover varchar(100) DEFAULT NULL,
  schedule_classbegin varchar(100) DEFAULT NULL,
  schedule_classlength int(11) DEFAULT '0' COMMENT '上课时长 单位分',
  schedule_perweek int(11) DEFAULT NULL COMMENT '每周几 1周一 2周二  7 周日',
  schedule_prenum varchar(100) DEFAULT '' COMMENT '可上课学生数/应到上课学生数',
  schedule_actnum int(11) DEFAULT '0' COMMENT '实到人数',
  schedule_sctnum varchar(255) DEFAULT '0' COMMENT '实招人数',
  schedule_status tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0未上课 1已上课 2取消上课',
  schedule_content varchar(255) DEFAULT NULL COMMENT '上课内容',
  schedule_remark varchar(255) DEFAULT NULL COMMENT '备注',
  campusid int(11) DEFAULT NULL,
  schedule_update int(1) DEFAULT '0' COMMENT '1代表调课',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=395 DEFAULT CHARSET=utf8 COMMENT='班级排课表';";


$bool=mysqli_query($link,$sql);



$sql = "CREATE TABLE ew_student (
  id int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  campusid int(11) DEFAULT NULL COMMENT '所属校区',
  student_studentid int(11) NOT NULL COMMENT '学号（年+几年级+班级+递增号）10位',
  student_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '姓名',
  student_classid int(11) NOT NULL COMMENT '培训班级',
  student_courseid int(11) NOT NULL COMMENT '课程ID',
  student_school varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '毕业学校',
  student_gradeid int(11) NOT NULL COMMENT '学校年级',
  student_idcard varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '身份证',
  student_phone varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '手机号码',
  student_sex tinyint(1) DEFAULT '0' COMMENT '性别 0男 1女',
  student_cardnum varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '支付宝账号',
  student_chargetime date DEFAULT NULL COMMENT '收费日期',
  student_createtime datetime DEFAULT NULL COMMENT '报名创建时间',
  student_status int(4) DEFAULT '0' COMMENT '学生状态 0在读 1毕业',
  student_remark varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `index1` (`student_createtime`),
  KEY `index2` (`campusid`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='学员表';";


$bool=mysqli_query($link,$sql);



$sql = "
CREATE TABLE ew_subject (
  id int(11) NOT NULL AUTO_INCREMENT,
  campusid int(11) DEFAULT NULL,
  subject_name varchar(255) DEFAULT NULL COMMENT '科目名称',
  subject_remark varchar(255) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='科目表';";


$bool=mysqli_query($link,$sql);



$sql = "CREATE TABLE ew_teacher (
  id int(11) NOT NULL AUTO_INCREMENT,
  campusid int(11) NOT NULL COMMENT '所属校区',
  teacher_name varchar(255) DEFAULT NULL COMMENT '姓名',
  teacher_gender int(11) DEFAULT '0' COMMENT '性别 0男 1女',
  teacher_idcard varchar(255) DEFAULT NULL COMMENT '身份证号码',
  teacher_bankaccount varchar(255) DEFAULT NULL,
  teacher_jobtype int(11) DEFAULT '0' COMMENT '在职类型 0兼职 1全职',
  teacher_subject_id int(11) NOT NULL COMMENT '科目id',
  teacher_grade_id int(11) NOT NULL COMMENT '级别（年级）id',
  teacher_telphone varchar(255) DEFAULT NULL COMMENT '联系方式（手机号码）',
  teacher_email varchar(255) DEFAULT NULL COMMENT '邮箱',
  teacher_qq varchar(255) DEFAULT NULL COMMENT 'QQ号码',
  teacher_joindate date DEFAULT NULL COMMENT '入职日期',
  teacher_status int(11) DEFAULT '0' COMMENT '状态 是否正式员工 0不是 1是',
  teacher_befulldate date DEFAULT NULL COMMENT '转正日期',
  teacher_remark varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='老师表';
";


$bool=mysqli_query($link,$sql);


$sql = "CREATE TABLE ew_user (
  id int(11) NOT NULL AUTO_INCREMENT,
  campusid int(11) DEFAULT NULL COMMENT '校区id',
  username varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  password varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '姓名',
  typeid int(11) DEFAULT NULL COMMENT '类型',
  phone varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '电话',
  mail varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '邮箱',
  avatar varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '头像',
  redtime datetime DEFAULT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=220 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户表 学员和老师';";


$bool=mysqli_query($link,$sql);



$sql = "CREATE TABLE ew_usertype (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户类型';";


$bool=mysqli_query($link,$sql);


$sql = "CREATE TABLE ew_versionlog (
  id int(11) NOT NULL AUTO_INCREMENT,
  fun_version varchar(255) NOT NULL,
  build_version varchar(255) NOT NULL,
  version varchar(255) NOT NULL,
  update_comment varchar(255) NOT NULL,
  update_time datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$bool=mysqli_query($link,$sql);



$sql = "INSERT INTO `ew_campus` VALUES ('1', '点击修改', '铁心大厦', '{$time}', '主校区');";
mysqli_query($link,$sql);
//执行配置增加
$sql="INSERT INTO `ew_config` VALUES ('58', '1', 'GlobalTitle', 'EduWork ManageMent System', '网站标题');";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('59', '1', 'BackGroundTitle', 'EduWork', '后台头部标题');";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('60', '1', 'LoginPageTitle', 'EduWork ManageMent System', '登录页面标题(英)');";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('61', '1', 'LoginPageSTitle', '培训学校运营管理系统以及服务系统', '登录页面标题(中)');";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('62', '0', 'DialogClassSpeed', 'animated-fast', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('63', '0', 'DialogClassAnimation', 'bounceIn', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('64', '0', 'DialogClassSelect', 'bounce', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('65', '0', 'DialogClassSelect', 'flash', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('66', '0', 'DialogClassSelect', 'pulse', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('67', '0', 'DialogClassSelect', 'rubberBand', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('68', '0', 'DialogClassSelect', 'shake', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('69', '0', 'DialogClassSelect', 'swing', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('70', '0', 'DialogClassSelect', 'tada', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('71', '0', 'DialogClassSelect', 'wobble', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('72', '0', 'DialogClassSelect', 'bounceIn', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('73', '0', 'DialogClassSelect', 'bounceInDown', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('74', '0', 'DialogClassSelect', 'bounceInLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('75', '0', 'DialogClassSelect', 'bounceInRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('76', '0', 'DialogClassSelect', 'bounceInUp', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('77', '0', 'DialogClassSelect', 'bounceOut', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('78', '0', 'DialogClassSelect', 'bounceOutDown', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('79', '0', 'DialogClassSelect', 'bounceOutLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('80', '0', 'DialogClassSelect', 'bounceOutRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('81', '0', 'DialogClassSelect', 'bounceOutUp', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('82', '0', 'DialogClassSelect', 'fadeIn', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('83', '0', 'DialogClassSelect', 'fadeInDown', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('84', '0', 'DialogClassSelect', 'fadeInLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('85', '0', 'DialogClassSelect', 'fadeInRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('86', '0', 'DialogClassSelect', 'fadeInUp', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('87', '0', 'DialogClassSelect', 'fadeOut', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('88', '0', 'DialogClassSelect', 'fadeOutDown', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('89', '0', 'DialogClassSelect', 'fadeOutLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('90', '0', 'DialogClassSelect', 'fadeOutRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('91', '0', 'DialogClassSelect', 'fadeOutUp', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('92', '0', 'DialogClassSelect', 'flipInX', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('93', '0', 'DialogClassSelect', 'flipInY', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('94', '0', 'DialogClassSelect', 'flipOutX', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('95', '0', 'DialogClassSelect', 'flipOutY', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('96', '0', 'DialogClassSelect', 'lightSpeedIn', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('97', '0', 'DialogClassSelect', 'lightSpeedOut', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('98', '0', 'DialogClassSelect', 'rotateIn', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('99', '0', 'DialogClassSelect', 'rotateInDownLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('100', '0', 'DialogClassSelect', 'rotateInDownRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('101', '0', 'DialogClassSelect', 'rotateInUpLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('102', '0', 'DialogClassSelect', 'rotateInUpRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('103', '0', 'DialogClassSelect', 'rotateOut', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('104', '0', 'DialogClassSelect', 'rotateOutDownLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('105', '0', 'DialogClassSelect', 'rotateOutDownRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('106', '0', 'DialogClassSelect', 'rotateOutUpLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('107', '0', 'DialogClassSelect', 'rotateOutUpRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('108', '0', 'DialogClassSelect', 'hinge', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('109', '0', 'DialogClassSelect', 'rollIn', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('110', '0', 'DialogClassSelect', 'rollOut', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('111', '0', 'DialogClassSelect', 'zoomIn', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('112', '0', 'DialogClassSelect', 'zoomInDown', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('113', '0', 'DialogClassSelect', 'zoomInLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('114', '0', 'DialogClassSelect', 'zoomInRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('115', '0', 'DialogClassSelect', 'zoomInUp', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('116', '0', 'DialogClassSelect', 'zoomOut', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('117', '0', 'DialogClassSelect', 'zoomOutDown', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('118', '0', 'DialogClassSelect', 'zoomOutLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('119', '0', 'DialogClassSelect', 'zoomOutRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('120', '0', 'DialogClassSelect', 'zoomOutUp', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('121', '0', 'DialogClassSelect', 'slideInDown', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('123', '0', 'DialogClassSelect', 'slideInLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('124', '0', 'DialogClassSelect', 'slideInRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('125', '0', 'DialogClassSelect', 'slideInUp', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('126', '0', 'DialogClassSelect', 'slideOutDown', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('127', '0', 'DialogClassSelect', 'slideOutLeft', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('128', '0', 'DialogClassSelect', 'slideOutRight', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('129', '0', 'DialogClassSelect', 'slideOutUp', null);";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('130', '1', 'openAnimation', '0', '动画效果');";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('131', '1', 'coursegridsize', '15', '课程列表每页显示数');";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('132', '1', 'studentgridsize', '15', '学生列表每页显示数');";
mysqli_query($link,$sql);$sql="INSERT INTO `ew_config` VALUES ('133', '1', 'teachergridsize', '15', '教师列表每页显示数');";

$bool=mysqli_query($link,$sql);


$sql = "INSERT INTO `ew_menus` VALUES ('1', '0', 'menu', 'Menu', 'fa', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('2', '1', 'dashboard', '系统概况', 'fa fa-dashboard', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('3', '1', 'campus_manage', '校区管理', 'fa fa-group', '0');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('4', '1', 'student', '学员管理', 'fa fa-calculator', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('5', '1', 'teacher', '老师管理', 'fa fa-group', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('6', '1', 'academic_manage', '教务管理', 'fa fa-suitcase', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('7', '1', 'teachers_students', '师生消息', 'fa fa-group', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('8', '1', 'form_count', '报表统计', 'fa fa-calculator', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('9', '1', 'notice_manage', '公告管理', 'fa fa-volume-up ', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('10', '1', 'system_settings', '系统设置', 'fa fa-cogs', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('31', '3', 'campus_manage', '校区管理', 'fa fa-building', '0');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('32', '3', 'principal_manage', '校长管理', 'fa fa-male', '0');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('41', '4', 'student_registration', '新学员报名', 'fa fa-paper-plane-o', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('42', '4', 'student_manage', '学员信息管理', 'fa fa-file-archive-o', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('43', '5', 'teacher_registration', '新老师注册', 'fa fa-paper-plane-o', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('44', '5', 'teacher_manage', '老师信息管理', 'fa fa-file-archive-o', '1');";
mysqli_query($link,$sql);

$sql = "INSERT INTO `ew_menus` VALUES ('61', '6', 'course_manage', '课程管理', 'fa fa-rub', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('62', '6', 'class_manage', '班级管理', 'fa fa-archive', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('63', '6', 'course_arrange', '排课管理', 'fa fa-pie-chart', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('64', '6', 'schedule_rollcall', '课表和点名', 'fa fa-paypal', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('65', '6', 'makeup_lessons', '补课管理', 'fa fa-slideshare', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('71', '7', 'feedback_manage', '师生评价', 'fa fa-eyedropper', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('91', '10', 'department_manage', '部门设置', 'fa fa-clipboard', '0');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('92', '10', 'subject_manage', '科目设置', 'fa fa-ruble', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('93', '10', 'grade_manage', '年级设置', 'fa fa-copyright', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('94', '10', 'classroom_manage', '教室设置', 'fa fa-group', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('95', '10', 'classtime_manage', '上课时间设置', 'fa fa-key', '0');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('96', '10', 'common_settings', '基础参数设置', 'fa fa-clipboard', '0');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('97', '10', 'personal_settings', '个性化设置', 'fa fa-anchor', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('98', '10', 'menu_manage', '菜单管理', 'fa fa-server', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('99', '10', 'global_config', '系统设置', 'fa fa-cog', '0');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('1000', '1', 'javascript:logout();', '退出', 'fa fa-sign-out', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('1001', '0', '', '', '', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('1003', '8', 'gate_card', '班级出勤率', 'fa fa-google-wallet', '1');";
mysqli_query($link,$sql);
$sql = "INSERT INTO `ew_menus` VALUES ('1004', '8', 'school_lession', '课消率', 'fa fa-paypal', '1');";
$bool=mysqli_query($link,$sql);
//header('location:install/index.php');die;
mysqli_close($link);
if($bool==true){
    $info = file_get_contents("../index.php");
    $info=preg_replace("/header\(\'location\:install\/index\.php\'\);die;/"," ", $info);
    file_put_contents("../index.php",$info);
    //echo "<meta http-equiv=\"refresh\" content=\"2; url=index5.php\" />";
	
function showjsmessage($message) {
  echo '<script type="text/javascript">showmessage(\''.addslashes($message).' \');</script>'."\r\n";
  flush();
  ob_flush();
}
$database=array('user','admin','called','teacher','schedule','personalconfig','classroom','class','permission','grade','feedback','student','config','menus');
$jd=array('7%','14%','20%','26%','32%','39%','45%','51%','59%','66%','71%','80%','87%','92%');
for ($i = 1; $i <=count($database); $i++) {
  showjsmessage("进行数据表{$database[$i]} 安装..进度{$jd[$i]}");
  sleep(1);
}
	showjsmessage("正在插入数据");
	showjsmessage("安装成功");
	sleep(1);
	showjsmessage("2秒后自动跳转页面");
	echo "<meta http-equiv=\"refresh\" content=\"2; url=index5.php\" />";
}else{
	
    echo "<meta http-equiv=\"refresh\" content=\"1; url=index5.php\" />";
}
?>
