<?php
/**
 * Created by ghostsf
 * Date: 2016/4/21
 */

namespace app\admin\model;


use think\Model;

class Course extends Model
{
    public function campus()
    {
        return $this->belongsTo('Campus','campusid');
    }
    public function grade()
    {
        return $this->belongsTo('Grade','course_grade_id');
    }
    public function subject()
    {
        return $this->belongsTo('Subject','course_subject_id');
    }

}