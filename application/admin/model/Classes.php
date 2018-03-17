<?php
/**
 * Created by ghostsf
 * Date: 2016/5/31
 */

namespace app\admin\model;


use think\Model;

/**
 * 班级类
 * Class Classes
 * @package app\admin\model
 */
class Classes extends Model
{
    public function course()
    {
        return $this->belongsTo('Course','classes_courseid');
    }
    public function teacher()
    {
        return $this->belongsTo('Teacher','classes_headteacher');
    }
    public function classroom()
    {
        return $this->belongsTo('Classroom','classes_classroomid');
    }

    public function campus()
    {
        return $this->belongsTo('Campus','campusid');
    }
}