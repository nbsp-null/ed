<?php
/**
 * Created by ghostsf
 * Date: 2016/5/31
 */

namespace app\admin\model;


use think\Model;

/**
 * 师生评价
 * Class Feedback
 * @package app\admin\model
 */
class Feedback extends Model
{
	public function classes()
	{
		return $this->belongsTo('classes','feedback_classid');
	}
	public function teacher()
	{
		return $this->belongsTo('teacher','feedback_teacherid');
		
	}
	public function campus()
	{
		return $this->belongsTo('campus','campusid');
	}
	public function student()
	{
		return $this->belongsTo('student','feedback_studentid');
	}
	public function grade()
	{
		return $this->belongsTo('grade','feedback_gradeid');
	}
	
	
}