<?php

/**
 * Created by ghostsf
 * Date: 2016/4/20
 */

namespace app\admin\controller;

use app\admin\model\Config;
use app\admin\model\Personalconfig;
use think\Controller;
use think\Db;
use think\Config as redis;

/**
 * 公共
 * Class Common
 * @package app\admin\controller
 */
class CommonController extends Controller {
	public  $redis;
    /**
     * 拦截器
     *
     * @return bool
     */
    public function _initialize() {
//session过期验证
        if (!isset($_SESSION['loginSession'])) {
            header("http/1.1", true, 389); //389 http状态码 session过期
            return false;
        }
    }
	
	/** redis连接
     * @return bool
     */

    public function redis()
    {
        $options=redis::get('database');
        $redisHost=array_key_exists("redisHost",$options)?$options['redisHost']:"127.0.0.1";
        $redisPort=array_key_exists("redisPort",$options)?$options['redisPort']:6379;
        $redisAuth=array_key_exists("redisAuth",$options)?$options['redisAuth']:'';
        $redisHost=$redisHost==null?"127.0.0.1":$redisHost;
        $redisPort=$redisPort==null? 6379:$redisPort;
        if(extension_loaded("redis")){
            $redis= new \redis();
            if($redis->connect($redisHost,$redisPort)){
                if($redisAuth!=null){
                    $redis->auth($redisAuth);
                    if(false==$redis->set('ce',1)){
                        return false;
                    }
                }
               $this->redis=$redis;
                return true;
            }else{
                  return false;
                die;
                $tishi="redis连接失败原因IP地址连接有误";
                die($tishi);
            }

        }else{
            return false;
        }
    }

    /**
     * 全局设置
     * @param array $arr
     */
    public function setGlobalSettings($arr = []) {
        foreach ($arr as $name) {
            $config = Config::get(array("name" => $name));
            $this->assign($name, $config->value);
        }
    }

    /**
     * 个性设置
     * @param array $arr
     */
    public function setPersonalSettings($arr = []) {
        $loginSession = session('loginSession');
        $userid = $loginSession['id'];
        foreach ($arr as $name) {
            $personalConfig = Personalconfig::get(array("name" => $name, "userid" => $userid));
            if ($personalConfig) {
                $this->assign($name, $personalConfig->value);
            } else {
                $this->setGlobalSettings([$name]);
            }
        }
    }

    /**
     * 根据校区id区分数据显示
     */
    public function getDataByCampusid($arr = [], $str = "") {
//为空是用来判断添加数据时要不要传参
//$str是专门用于获取数据（其他是campusid 校区管理是id） 针对校区管理 除了校区管理  其他功能不用传
        if (empty($arr)) {
            if (isNotNull(session('loginSession')['campusid'])) {
                $arr['campusid'] = session('loginSession')['campusid'];
            }
        } else {
            foreach ($arr as $key => $value) {
                if ($key == 'page') {
                    unset($arr[$key]);
                } else if ($key == 'rows') {
                    unset($arr[$key]);
                } else if ($value == '') {
                    unset($arr[$key]);
                } else {
                    $arr[$key] = Array('like', '%' . $value . '%');
                }
            }
            if (isNotNull(session('loginSession')['campusid']) && $str == "") {
                $arr["campusid"] = session('loginSession')['campusid'];
            }
            if (isNotNull(session('loginSession')['campusid']) && $str != "") {
                $arr["id"] = session('loginSession')['campusid'];
            }
        }


        return $arr;
    }
    /**
     * 筛选搜索条件不是like是=
     */
    public function searchNotLike($searchPath=[],$postArr=[],$one='',$two='',$three='') {
        if (isset($searchPath[$one]) && $one != '') {
            unset($searchPath[$one]);
            $searchPath[$one] = $postArr[$one];
        }
        if (isset($searchPath[$two]) && $two != '') {
            unset($searchPath[$two]);
            $searchPath[$two] = $postArr[$two];
        }
        if (isset($searchPath[$three]) && $three != '') {
            unset($searchPath[$three]);
            $searchPath[$three] = $postArr[$three];
        }
        return $searchPath;
    }
    /**
     * 排课---日期处理--仅限排课使用
     * @param array $arr
     */
    public function arrangeDateSet($arr = []) {
        $beginD = strtotime($arr['schedule_starttime']);
        //计算有多少已经报名的学生用的
        $starttime = $arr['schedule_starttime'];
        //计算结束
        $overD = strtotime($arr['schedule_endtime']);
        $result = array();
//判断重复
        $param = $this->getDataByCampusid();
        $classroom = Db::table('ew_schedule')->where($param)->where('schedule_classroomid', $arr['schedule_classroomid'])->where('schedule_status', 0)->field('min(schedule_starttime) as schedule_starttime,schedule_endtime')->group('schedule_endtime')->select();
        $class = Db::table('ew_schedule')->where($param)->where('schedule_classid', $arr['schedule_classid'])->where('schedule_status', 0)->field('min(schedule_starttime) as schedule_starttime,schedule_endtime')->group('schedule_endtime')->select();
        $teacher = Db::table('ew_schedule')->where($param)->where('schedule_teacherid', $arr['schedule_teacherid'])->where('schedule_status', 0)->field('min(schedule_starttime) as schedule_starttime,schedule_endtime,schedule_classbegin,schedule_classover')->group('schedule_endtime')->select();

        if (!empty($classroom)) {
            foreach ($classroom as $room) {
                $end = strtotime($room['schedule_endtime']);
                $start = strtotime($room['schedule_starttime']);
                if ($beginD > $end || $overD < $start) {
                    continue;
                } else {
                    $result['status'] = 0;
                    $result['msg'] = '此教室在此日期范围内已被占用';
                    return $result;
                }
            }
        }
        if (!empty($class)) {
            foreach ($class as $cla) {
                $end = strtotime($cla['schedule_endtime']);
                $start = strtotime($cla['schedule_starttime']);
                if ($beginD > $end || $overD < $start) {
                    continue;
                } else {
                    $result['status'] = 0;
                    $result['msg'] = '此班级在此日期范围内已被占用';
                    return $result;
                }
            }
        }
        if (!empty($teacher)) {
            foreach ($teacher as $teach) {
                $end = strtotime($teach['schedule_endtime']);
                $start = strtotime($teach['schedule_starttime']);
                if ($beginD > $end || $overD < $start) {
                    continue;
                } else {
                    $oldbegin = explode(':', $teach['schedule_classbegin']);
                    $oldover = explode(':', $teach['schedule_classover']);
                    $newbegin = explode(':', $arr['schedule_classbegin']);
                    $newover = explode(':', $arr['schedule_classover']);
                    $oldbegin = $oldbegin[0] * 60 + $oldbegin[1];
                    $oldover = $oldover[0] * 60 + $oldover[1];
                    $newbegin = $newbegin[0] * 60 + $newbegin[1];
                    $newover = $newover[0] * 60 + $newover[1];
                    if ($newbegin > $oldover || $newover < $oldbegin) {
                        continue;
                    } else {
                        $result['status'] = 0;
                        $result['msg'] = '此老师在此日期范围上课时间段内已有排课';
                        return $result;
                    }
                }
            }
        }
//判断完毕
        $day = floor(($overD - $beginD) / 86400);
        $one = 3600 * 24;
        $week = $arr['schedule_perweek'];
        unset($arr['schedule_perweek']);
        unset($arr['schedule_starttime']);

        for ($i = 0; $i <= $day; $i++) {
            foreach ($week as $value) {
                if (date('w', $beginD + $i * $one) == $value) {
//处理关联的年级ID和课程ID
                    $classid = $arr['schedule_classid'];
                    $class = Db::table('ew_classes')->where('id', $classid)->find();
                    $courseid = $class['classes_courseid'];
                    if(empty($courseid)){
                        $result['status'] = 0;
                        $result['msg'] = '请先去完善班级 "'.$class['classes_name'].'" 的信息';
                        return $result;
                    }
                    $course = Db::table('ew_course')->where('id', $courseid)->find();
                    $gradeid = $course['course_grade_id'];
                    if(empty($gradeid)){
                        $result['status'] = 0;
                        $result['msg'] = '请先去完善课程 "'.$course['course_name'].'" 的信息';
                        return $result;
                    }
//==========处理完毕========
//处理应该招多少人和实际已有多少人
                    $student['student_classid'] = $classid;
// $student['student_gradeid'] = $gradeid;
                    $student['student_courseid'] = $courseid;
                    $student['campusid'] = $_POST['campusid'];
                    // TODO 应该添加一个时间的限制，报名日期小于开始日期的人数才对
                    $alreadtHas = Db::table('ew_student')->where($student)->where('student_status',0)->count();
                    $allowHas = Db::table('ew_classroom')->where('id', $_POST['schedule_classroomid'])->find()['classroom_containnum'];
                    if($alreadtHas > $allowHas){
                        $result['status'] = 0;
                        $result['msg'] = '此教室只能容纳'.$allowHas.'人，现已有'.$alreadtHas.'人，已满';
                        return $result;
                    }
                    $arr['schedule_prenum'] = $alreadtHas . '/' . $allowHas;
                    $arr['schedule_sctnum'] = $alreadtHas;
                    $arr['schedule_courseid'] = $courseid;
                    $arr['schedule_gradeid'] = $gradeid;
                    $arr['schedule_perweek'] = $value;
                    $arr['schedule_starttime'] = date('Y-m-d', $beginD + $i * $one);
                    $arr['schedule_status'] = 0;
                    array_push($result, $arr);
                }
            }
        }
        if (empty($result)) {
            $result['status'] = 0;
            $result['msg'] = '此日期段内无合适要求，请重新填写开始/结束日期';
            return $result;
        }
        return $result;
    }

    /**
     * 排课批量修改--查找符合条件的排课
     */
    public function getNeedData($array = []) {
// 为什么foreach出不来值
        $res = array();
        $res['schedule_classid'] = $array['schedule_classid'];
        $res['schedule_endtime'] = $array['schedule_endtime'];
        return $res;
    }

    /**
     * +----------------------------------------------------------
     * Export Excel | 2016.09.14
     * Author:ghostsf <ghostsf@163.com>
     * +----------------------------------------------------------
     * @param $expTitle     string File name
     * +----------------------------------------------------------
     * @param $expCellName  array  Column name
     * +----------------------------------------------------------
     * @param $expTableData array  Table data
     * +----------------------------------------------------------
     */
    public function exportExcel($expTitle, $expCellName, $expTableData) {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); //文件表名称
        $fileName = $xlsTitle . date('_YmdHis'); //文件名称
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("phpoffice.phpexcel.Classes.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
//$objPHPExcel->getActiveSheet()->mergeCells('A1:' . $cellName[$cellNum - 1] . '1');//合并单元格
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle . '  Export time:' . date('Y-m-d H:i:s'));
        $objPHPExcel->getDefaultStyle()->getFont()->setName('微软雅黑');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true
                    )
                )
        );
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '1', $expCellName[$i][1]);
            $objPHPExcel->getActiveSheet()->getColumnDimension($cellName[$i])->setAutoSize(true);
        }
// Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet()->setCellValue($cellName[$j] . ($i + 2), $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls"); //attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * +----------------------------------------------------------
     * Import Excel | 2016.0914
     * Author:ghostsf <ghostsf@163.com>
     * +----------------------------------------------------------
     * @param  $file   upload file $_FILES
     * +----------------------------------------------------------
     * @return array   array("error","message")
     * +----------------------------------------------------------
     */
    public function read($filename,$encode='utf-8')
   {
          vendor("phpoffice.phpexcel.Classes.PHPExcel.IOFactory");
          $objReader = \PHPExcel_IOFactory::createReader('Excel5');

          $objReader->setReadDataOnly(true);

          $objPHPExcel = $objReader->load($filename);

          $objWorksheet = $objPHPExcel->getActiveSheet();

         $highestRow = $objWorksheet->getHighestRow();
         $highestColumn = $objWorksheet->getHighestColumn();
         $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
         $excelData = array();
             for ($row = 1; $row <= $highestRow; $row++) {
             for ($col = 0; $col < $highestColumnIndex; $col++) {
                 $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
           }
         }
      
        return $excelData;

    }
    /**
     *根据日期间隔获得空闲教室
     */
    public function getFreeClassroom($start,$end)
    {
        $param = $this->getDataByCampusid();
        $datas = Db::table('ew_schedule')->where('schedule_starttime|schedule_endtime','between',"$start,$end")->where($param)->where('schedule_status',0)->group('schedule_classroomid')->select();
        $dataTwo = Db::table('ew_schedule')->where("schedule_starttime",'<=',$start)->where('schedule_endtime','>=',$end)->where($param)->where('schedule_status',0)->group('schedule_classroomid')->select();
        if(!empty($dataTwo)){
            foreach($dataTwo as $addData){
                $datas[] = $addData;
            }
        };
        if(empty($datas)){
            $classrooms = Db::table('ew_classroom')->where($param)->field('id,classroom_name,classroom_containnum')->select();
            return $classrooms;
        };
        foreach($datas as $data){
            $classroomid[] = $data['schedule_classroomid'];
        }

        $classrooms = Db::table('ew_classroom')->where('id','not in',$classroomid)->where($param)->field('id,classroom_name,classroom_containnum')->select();
        return $classrooms;
    }
    /**
     *根据日期间隔获得空闲老师
     */
    public function getFreeTeacher($start,$end)
    {
        $param = $this->getDataByCampusid();
        $datas = Db::table('ew_schedule')->where('schedule_starttime|schedule_endtime','between',"$start,$end")->where($param)->where('schedule_status',0)->group('schedule_teacherid')->select();
        $dataTwo = Db::table('ew_schedule')->where("schedule_starttime",'<=',$start)->where('schedule_endtime','>=',$end)->where($param)->where('schedule_status',0)->group('schedule_teacherid')->select();
        if(!empty($dataTwo)){
            foreach($dataTwo as $addData){
                $datas[] = $addData;
            }
        };
        if(empty($datas)){
            $teachers = Db::table('ew_teacher')->where($param)->field('id,teacher_name,teacher_telphone,teacher_gender')->select();
            return $teachers;
        };
        foreach($datas as $data){
            $teacherid[] = $data['schedule_teacherid'];
        }

        $teachers = Db::table('ew_teacher')->where('id','not in',$teacherid)->where($param)->field('id,teacher_name,teacher_telphone,teacher_gender')->select();
        return $teachers;
    }
    /**
     *根据日期间隔获得空闲班级
     */
    public function getFreeClass($start,$end)
    {
        $param = $this->getDataByCampusid();
        $datas = Db::table('ew_schedule')->where('schedule_starttime|schedule_endtime','between',"$start,$end")->where($param)->where('schedule_status',0)->group('schedule_classid')->select();
        $dataTwo = Db::table('ew_schedule')->where("schedule_starttime",'<=',$start)->where('schedule_endtime','>=',$end)->where($param)->where('schedule_status',0)->group('schedule_classid')->select();
        if(!empty($dataTwo)){
            foreach($dataTwo as $addData){
                $datas[] = $addData;
            }
        };
        if(empty($datas)){
            $classes = Db::table('ew_classes')->where($param)->field('id,classes_name')->select();
            return $classes;
        };
        foreach($datas as $data){
            $classesid[] = $data['schedule_classid'];
        }

        $classes = Db::table('ew_classes')->where('id','not in',$classesid)->where($param)->field('id,classes_name')->select();
        return $classes;
    }
 
}
