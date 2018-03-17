<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\admin\model;


use think\Model;

/**
 * 班级类
 * Class Classes
 * @package app\admin\model
 */
class Called extends Model
{
    public function student()
    {
        return $this->belongsTo('student','called_studentid');
    }
    public function course()
    {
        return $this->belongsTo('course','called_courseid');
    }
    public function teacher()
    {
        return $this->belongsTo('teacher','called_teacherid');
    }
    public function schedule()
    {
        return $this->belongsTo('schedule','called_scheduleid');
    }
    public function grade()
    {
        return $this->belongsTo("grade",'called_gradeid');
    }
}
