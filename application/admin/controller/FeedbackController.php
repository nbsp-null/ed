<?php
/**
 * Author：ghostsf
 * Blog：www.ghostsf.com
 * Date: 2016/6/16
 */

namespace app\admin\controller;
use app\admin\model\Feedback;
use think\Db;

/**
 * 师生评价
 * Class FeedbackController
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\controller
 */
class FeedbackController extends CommonController
{
	/**
	 * 获取师生评价信息
	 **/
	public function getfeedbacks()
	{
		$path = $this->getDataByCampusid($_POST);
		$rows = $_POST['rows'];
		$page = $_POST['page'];
		$searchPath = $this->searchNotLike($path,$_POST,'feedback_teacherid','schedule_studentid');
		if(isset($searchPath['campusid'])){
			$searchPath['feedback.campusid'] = $searchPath["campusid"];
			unset($searchPath["campusid"]);
		}
		$feedback = feedback::with("classes,teacher,campus,grade,student")->where($searchPath)->limit($rows * ($page - 1), $rows)->select();
		$total = feedback::with("classes,teacher,campus,grade,student")->where($searchPath)->count();
		$list['rows'] = $feedback;
		$list['total'] = $total;
		return json_encode($list);
		
	}
}