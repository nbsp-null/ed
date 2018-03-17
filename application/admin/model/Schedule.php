<?php
/**
 * Created by ghostsf
 * Date: 2016/5/31
 */

namespace app\admin\model;


use think\Model;


/**
 * 排课Model
 * Class Schedule
 * Author ghostsf
 * Blog www.ghostsf.com
 * @package app\admin\model
 */
class Schedule extends Model
{

    public function classes()
    {
        return $this->belongsTo('Classes','schedule_classid');
    }
    public function classroom()
    {
        return $this->belongsTo('Classroom','schedule_classroomid');
    }
    public function teacher()
    {
        return $this->belongsTo('Teacher','schedule_teacherid');
    }
    public function course()
    {
        return $this->belongsTo('Course','schedule_courseid');
    }
}