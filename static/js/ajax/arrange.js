/**
 * 获得搜索参数
 * @param params
 * @returns {Object}
 */
function getSearchParams(params) {
    var searchParams = new Object();
    if (params != undefined) {
        searchParams = params;
    }
    $('.SearchForm').each(function () {
        var param = $(this).val().trim();
        if (param == undefined)
            param = '';
        searchParams[$(this).attr('name')] = param;
    });
    return searchParams;
}
/**
 * 更新已招人数
 */
function updateArrange(){
    $.ajax({
        url: 'Admin/Arrange/updateArrange', //form action
        dataType: 'JSON', //返回体类型
        type: 'POST', // form type
        success: function (data) {

        },
        error: function () {
            /* $.TeachDialog({
               // content: '更新数据异常，请联系管理员'
            } );*/
        }

    });
}
/**
 * 页面自加载
  */
function getIdStatus(){
    $.ajax({
        url: 'Admin/Arrange/getIdStatus', //form action
        dataType: 'JSON', //返回体类型
        type: 'POST', // form type
        success: function (data) {
            myStatus = data;
        }
    });
};
$(function () {
    getIdStatus();
    updateArrange();
    var cellwidth = ($(".box-content.table-responsive").width() - 55) / 11;
    var psval = $('#datatable_arrangeinfo').attr('data-size');
    if (psval == undefined || psval == "") {
        psval = 10;
    }
    /**
     * easyui 生成表格数据
     */
    $('#datatable_arrangeinfo').datagrid({
        striped: true,
        idField : 'id',
        remoteSort: false,
        collapsible: true,
        singleSelect: true,
        fit: false,
        url: 'admin/Arrange/getDatas',//数据源路径
        loadMsg: '请等待数据载入....',
        pagination: true,
        rownumbers: true,
        fitColumns: true,
        pageSize: psval,
        pageList: [psval, psval * 2, psval * 3, psval * 4, psval * 5],
        columns: [[{
            field: 'classes',
            title: '班级',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return value.classes_name;
            }
        }, {
            field: 'classroom',
            title: '上课教室',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return value.classroom_name;
            }
        },{
            field: 'teacher',
            title: '任课老师',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return value.teacher_name;
            }
        }, {
            field: 'schedule_starttime',
            title: '上课日期',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return value.substr(0,10);
            }
        }, {
            field: 'schedule_classbegin',
            title: '上课时间',
            align: 'center',
            width: cellwidth,
            sortable: true
        },{
            field: 'schedule_classover',
            title: '下课时间',
            align: 'center',
            width: cellwidth,
            sortable: true
        },{
            field: 'schedule_classlength',
            title: '课时(小时 : 分钟)',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                var minutes = value % 60;
                var hour = parseInt(value / 60);
                if(minutes < 10){
                    minutes = '0' + minutes;
                };
                return hour+' : '+minutes;
            }
        },{
            field: 'schedule_prenum',
            title: '已招/可容纳',
            align: 'center',
            width: cellwidth,
            sortable: true
        }, {
            field: 'schedule_perweek',
            title: '星期',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                if(value == 1){
                    return '星期一';
                }else if(value == 2){
                    return '星期二';
                }else if(value == 3){
                    return '星期三';
                }else if(value == 4){
                    return '星期四';
                }else if(value == 5){
                    return '星期五';
                }else if(value == 6){
                    return '星期六';
                }else if(value == 0){
                    return '星期日';
                }
            }
        }, {
            field: 'schedule_status',
            title: '状态',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                if(value==0){
                    return "<font color='red'>未上课</font>";
                };
                if(value==1){
                    return "<font color='gray'>已上课</font>";
                };
                if(value==2){
                    return "<font color='gray'>已取消</font>";
                };
            }
        }, {
            field: 'id',
            title: '操作',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return '<button id="'+value+'" name="'+myStatus[value]+'" onmouseover="something(this)" class="alert_popover" data-toggle="popover">操作</button>';
            }
        }]],
        onBeforeLoad: function (param) {
            param = getSearchParams(param);
        },
    });
//搜索
    $('#Search').click(function () {
        $('#datatable_arrangeinfo').datagrid('reload');
    });
//添加
    $('.addbook').click(function () {
        var content = "";
        $.ajax({
            url: 'admin/arrange/addarrange',
            type: 'POST',
            dataType: 'HTML',//返回的数据类型
            success: function (updatemodalhtml) {
                content = updatemodalhtml;
                //弹出添加框
                $.TeachDialog({
                    title: '新增排课信息',
                    content: content,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['保存'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        $.ajax({
                            url: 'admin/Arrange/insert',
                            data: $("#addForm").serialize(),
                            type: 'POST',
                            dataType: 'JSON',
                            success: function (returnData) {
                                if (returnData.status == 0) {
                                    $.TeachDialog({
                                        content: returnData.msg
                                    });
                                } else {
                                    modal.modal('hide');
                                    $.TeachDialog({
                                        content: returnData.msg
                                    });
                                    getIdStatus();
                                    $('#datatable_arrangeinfo').datagrid('reload');
                                }
                            },
                            error: function () {
                                $.TeachDialog({
                                    content: '系统异常，请联系管理员',
                                });
                            },

                        });

                    },
                });
            },
            error: function () {
                $.TeachDialog({
                    content: '系统异常，请联系管理员',
                });
                return;
            }
        });
    });
    
    /**
     * 删除
     */
    $('.removebook').click(function () {
        var row = $('#datatable_arrangeinfo').datagrid('getSelected');

        if (row == null) {
            $.TeachDialog({
                content: '请选择一行数据！'
            });
            return;
        }
        $.TeachDialog({
            content: "确认删除这堂课？<br>",
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function (sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url: 'Admin/Arrange/deleteLesson', //form action
                    dataType: 'JSON', //返回体类型
                    type: 'POST', // form type
                    data: "id=" + row.id,//  请求参数
                    success: function (data) {
                        if (data.status == 1) {
                            $.TeachDialog({
                                content: data.msg
                            });
                            $('#datatable_arrangeinfo').datagrid('reload');
                            $('#datatable_arrangeinfo').datagrid('uncheckAll');
                        } else {
                            $.TeachDialog({
                                content: '删除失败'
                            });
                        }
                    },
                    error: function () {
                        $.TeachDialog({
                            content: '系统异常，请联系管理员'
                        });
                    }
                });
            }
        });
    })
    /**
     * 临时调课
     */
    $('.changeLesson').click(function () {
        var row = $('#datatable_arrangeinfo').datagrid('getSelected');

        if (row == null) {
            $.TeachDialog({
                content: '请选择一行数据！'
            });
            return;
        }
        if (row.schedule_status != 0) {
            $.TeachDialog({
                content: '本节课已经上课或者已经取消上课，请确认操作！'
            });
            return;
        }
        $.TeachDialog({
            content: "确认要调课吗？<br>",
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function (sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url:"admin/Arrange/updateOne",
                    type:'POST',
                    dataType:"HTML",
                    data:'id='+row.id,
                    success:function(res){
                        var content = res;
                        $.TeachDialog({
                            title:'临时调课',
                            content:content,
                            showCloseButton: true,
                            showCloseButtonName: '关闭',
                            otherButtons: ['保存'],
                            otherButtonStyles: ['btn-primary'],
                            clickButton: function (sender, modal, index) {
                                $.ajax({
                                    url: 'admin/Arrange/update',
                                    data: $("#updateForm").serialize(),
                                    type: 'POST',
                                    dataType: 'JSON',
                                    success: function (returnData) {
                                        if (returnData.status == 0) {
                                            $.TeachDialog({
                                                content: returnData.msg
                                            });
                                        } else {
                                            modal.modal('hide');
                                            $.TeachDialog({
                                                content: returnData.msg
                                            });
                                            getIdStatus();
                                            $('#datatable_arrangeinfo').datagrid('reload');
                                        }
                                    },
                                    error: function () {
                                        $.TeachDialog({
                                            content: '系统异常，请联系管理员',
                                        });
                                    },
                                });
                            },
                        })
                    }
                })
            }
        });
    })
    /**
     * 修改排课
     */
    $('.changeAll').click(function () {
        var row = $('#datatable_arrangeinfo').datagrid('getSelected');

        if (row == null) {
            $.TeachDialog({
                content: '请选择一行数据！'
            });
            return;
        }
        if (row.schedule_status != 0) {
            $.TeachDialog({
                content: '本节课已经上课或者已经取消上课，请确认操作或先撤消上课！'
            });
            return;
        }
        $.TeachDialog({
            content: "重新排课会删除本排课数据，确认要重新排课吗？<br>",
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function (sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url:"admin/Arrange/updateAll",
                    type:'POST',
                    dataType:"HTML",
                    data:'id='+row.id,
                    success:function(res){
                        var content = res;
                        $.TeachDialog({
                            title:'重新排课',
                            content:content,
                            showCloseButton: true,
                            showCloseButtonName: '关闭',
                            otherButtons: ['保存'],
                            otherButtonStyles: ['btn-primary'],
                            clickButton: function (sender, modal, index) {
                                $.ajax({
                                    url: 'admin/Arrange/updateDone',
                                    data: $("#updateAllForm").serialize(),
                                    type: 'POST',
                                    dataType: 'JSON',
                                    success: function (returnData) {
                                        if (returnData.status == 0) {
                                            $.TeachDialog({
                                                content: returnData.msg
                                            });
                                        } else {
                                            modal.modal('hide');
                                            $.TeachDialog({
                                                content: returnData.msg
                                            });
                                            getIdStatus();
                                            $('#datatable_arrangeinfo').datagrid('reload');
                                        }
                                    },
                                    error: function () {
                                        $.TeachDialog({
                                            content: '系统异常，请联系管理员',
                                        });
                                    },
                                });
                            },
                        })
                    }
                })
            }
        });
    })
    /**
     * 取消上课
     */
    $('.cancelLesson').click(function () {
        var row = $('#datatable_arrangeinfo').datagrid('getSelected');

        if (row == null) {
            $.TeachDialog({
                content: '请选择一行数据！'
            });
            return;
        }
        if (row.schedule_status != 0) {
            $.TeachDialog({
                content: '本节课已经上课或者已经取消上课，请确认操作！'
            });
            return;
        }
        $.TeachDialog({
            content: "确认要取消这节课吗？<br>",
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function (sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url:"admin/Arrange/cancelLesson",
                    type:'POST',
                    dataType:"JSON",
                    data:'id='+row.id,
                    success:function(returnData){
                        if (returnData.status == 0) {
                            $.TeachDialog({
                                content: returnData.msg
                            });
                        } else {
                            $.TeachDialog({
                                content: returnData.msg
                            });
                            getIdStatus();
                            $('#datatable_arrangeinfo').datagrid('reload');
                        }
                    },
                    error: function () {
                        $.TeachDialog({
                            content: '系统异常，请联系管理员',
                        });
                    },
                })
            }
        });
    })
    /**
     * 撤消上课（恢复成未上课的样子）
     */
    $('.backLesson').click(function () {
        var row = $('#datatable_arrangeinfo').datagrid('getSelected');

        if (row == null) {
            $.TeachDialog({
                content: '请选择一行数据！'
            });
            return;
        }
        if (row.schedule_status == 0) {
            $.TeachDialog({
                content: '本节课还未上课，请确认操作！'
            });
            return;
        }
        $.TeachDialog({
            content: "确认要恢复未上课状态吗？<br>",
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function (sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url:"admin/Arrange/backLesson",
                    type:'POST',
                    dataType:"JSON",
                    data:'id='+row.id,
                    success:function(returnData){
                        if (returnData.status == 0) {
                            $.TeachDialog({
                                content: returnData.msg
                            });
                        } else {
                            $.TeachDialog({
                                content: returnData.msg
                            });
                            getIdStatus();
                            $('#datatable_arrangeinfo').datagrid('reload');
                        }
                    },
                    error: function () {
                        $.TeachDialog({
                            content: '系统异常，请联系管理员',
                        });
                    },
                })
            }
        });
    })
    
    //查看空闲教师
    $('.teacherFree').click(function () {
        var content = "";
        $.ajax({
            url: 'admin/arrange/teacherFree',
            type: 'POST',
            dataType: 'HTML',//返回的数据类型
            success: function (updatemodalhtml) {
                content = updatemodalhtml;
                //弹出添加框
                $.TeachDialog({
                    title: '查看教师空闲时间',
                    content: content,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['查询'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        $.ajax({
                            url: 'admin/Arrange/displayTeacher',
                            data: $("#teacherForm").serialize(),
                            type: 'POST',
                            dataType: 'JSON',
                            success: function (returnData) {
                                if (returnData.status == 0) {
                                    $.TeachDialog({
                                        content: returnData.msg
                                    });
                                } else {
                                    var teachers = returnData.msg;
                                    $("#teacherTable").empty();
                                    $("#teacherTable").append("<tr><td><b>此范围内有以下老师空闲</b></td><td><b>联系电话</b></td><td><b>性别</b></td></tr>");
                                    for(var i=0;i<teachers.length;i++){
                                        $("#teacherTable").append("<tr id='teacher"+i+"'></tr>");
                                        $.each(teachers[i],function(k,v){
                                            if(v == 0){v = '男';}else if(v == 1){v = '女';}
                                            if(k!='id'){
                                                $("#teacher"+i).append("<td>"+v+"</td>");
                                            }
                                        });
                                    }
                                }
                            },
                            error: function () {
                                $.TeachDialog({
                                    content: '系统异常，请联系管理员',
                                });
                            },

                        });
                    }
                });
            },
            error: function () {
                $.TeachDialog({
                    content: '系统异常，请联系管理员',
                });
                return;
            }
        });
    });
     //查看空闲教室
    $('.classroomFree').click(function () {
        var content = "";
        $.ajax({
            url: 'admin/arrange/classroomFree',
            type: 'POST',
            dataType: 'HTML',//返回的数据类型
            success: function (updatemodalhtml) {
                content = updatemodalhtml;
                //弹出添加框
                $.TeachDialog({
                    title: '查看空闲教室',
                    content: content,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['查询'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        $.ajax({
                            url: 'admin/Arrange/displayClassroom',
                            data: $("#classroomForm").serialize(),
                            type: 'POST',
                            dataType: 'JSON',
                            success: function (returnData) {
                                if (returnData.status == 0) {
                                    $.TeachDialog({
                                        content: returnData.msg
                                    });
                                } else {
                                    var classrooms = returnData.msg;
                                    $("#classroomTable").empty();
                                    $("#classroomTable").append("<tr><td><b>此范围内有以下空闲教室</b></td><td><b>可容纳人数（人）</b></td></tr>");
                                    for(var i=0;i<classrooms.length;i++){
                                        $("#classroomTable").append("<tr id='classroom"+i+"'></tr>");
                                        $.each(classrooms[i],function(k,v){
                                            if(k!='id'){
                                                $("#classroom"+i).append("<td>"+v+"</td>");
                                            }

                                        });
                                    }
                                }
                            },
                            error: function () {
                                $.TeachDialog({
                                    content: '系统异常，请联系管理员',
                                });
                            },

                        });
                    }
                });
            },
            error: function () {
                $.TeachDialog({
                    content: '系统异常，请联系管理员',
                });
                return;
            }
        });
    });
});
//