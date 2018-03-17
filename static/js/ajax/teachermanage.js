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
    $('.SearchForm').each(function() {
        var param = $(this).val();
        var val = $("input:radio[name='teacher_gender']:checked").val();
        if (val == 0) {
            searchParams['teacher_gender'] = 0;
        } else if (val == 1) {
            searchParams['teacher_gender'] = 1;
        }
        if (param == undefined)
            param = '';
        searchParams[$(this).attr('id')] = param;
    });
    return searchParams;
}

/**
 * ajax 获得年级信息
 * @constructor
 */
function GradeList() {
    $.ajax({
        url: 'admin/Grade/getJson',
        dataType: 'JSON',
        success: function(data) {
            $("#teacher_grade_id").empty();
            $("#teacher_grade_id").append("<option value=''>选择年级</option>");
            $.each(data, function(i, d) {
                $("#teacher_grade_id").append('<option value="' + d.id + '">' + d.grade_name + '</option>');
            });
        },
        error: function() {
            $.TeachDialog({
                content: '加载年级数据失败',
            });
        }
    });
}

/**
 * ajax 获得科目信息
 * @constructor
 */
function SubjectList() {
    $.ajax({
        url: 'admin/Subject/getJson',
        dataType: 'JSON',
        success: function(data) {
            $("#teacher_subject_id").empty();
            $("#teacher_subject_id").append("<option value=''>选择科目</option>");
            $.each(data, function(i, d) {
                $("#teacher_subject_id").append('<option value="' + d.id + '">' + d.subject_name + '</option>');
            });
        },
        error: function() {
            $.TeachDialog({
                content: '加载科目数据失败',
            });
        }
    });
}



/**
 * 页面自加载
 */

$(function() {
    //加载下拉框数据
    GradeList();
    SubjectList();

    var cellwidth = ($(".box-content.table-responsive").width() - 55) / 11;
    var psval = $('#datatable_teacherinfo').attr('data-size');
    if (psval == undefined || psval == "") {
        psval = 10;
    }
    /**
     * easyui 生成表格数据
     */
    $('#datatable_teacherinfo').datagrid({
        singleSelect: false, //允许选择多行
        striped: true,
        idField: 'id',
        remoteSort: false,
        collapsible: true,
        fit: false,
        url: 'admin/teacher/getTeachers', //数据源路径
        loadMsg: '请等待数据载入....',
        pagination: true,
        rownumbers: true,
        fitColumns: true,
        pageSize: psval,
        pageList: [psval, psval * 2, psval * 3, psval * 4, psval * 5],
        columns: [[{
                    field: 'ck',
                    checkbox: true, //复选框
                    align: 'center',
                    sortable: true,
                    width: cellwidth,
                }, {
                    field: 'teacher_name',
                    title: '姓名',
                    align: 'center',
                    width: cellwidth,
                    sortable: true
                }, {
                    field: 'teacher_telphone',
                    title: '电话',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                }, {
                    field: 'teacher_gender',
                    title: '性别',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(value) {
                        if (value == 0) {
                            return "男";
                        } else if (value == 1) {
                            return "女";
                        }
                    },
                }, {
                    field: 'teacher_idcard',
                    title: '身份证号',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                }, {
                    field: 'teacher_email',
                    title: 'email',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                }, {
                    field: 'teacher_qq',
                    title: 'qq',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                }, {
                    field: 'subject',
                    title: '科目',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(subject) {
                        return subject.subject_name;
                    },
                }, {
                    field: 'grade',
                    title: '年级',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(grade) {
                        return grade.grade_name;
                    },
                }, {
                    field: 'teacher_jobtype',
                    title: '在职类型',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(value) {
                        if (value == 0) {
                            return "兼职";
                        } else if (value == 1) {
                            return "全职";
                        }
                    },
                }, {
                    field: 'teacher_status',
                    title: '是否在职',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(value) {
                        if (value == 1) {
                            return "是";
                        } else if (value == 0) {
                            return "否";
                        }
                    },
                }, {
                    field: 'teacher_bankaccount',
                    title: '银行卡号',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                }, {
                    field: 'teacher_joindate',
                    title: '入职日期',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                }, {
                    field: 'teacher_befulldate',
                    title: '转正日期',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                }]],
        onBeforeLoad: function(param) {
            param = getSearchParams(param);
        },
    });


    $('#Search').click(function() {
        $('#datatable_teacherinfo').datagrid('reload');
    })
    /*
     *编辑
     */
    $('#edit').click(function() {
        var rows = $('#datatable_teacherinfo').datagrid('getSelections');
        if (rows.length != 1) {
            $.TeachDialog({
                content: '请选择一行数据！',
            });
            return;
        }

        var content = "";
        $.ajax({
            url: 'admin/teacher/updatemodal',
            data: "id=" + rows[0].id,
            dataType: "HTML", //返回数据类型
            type: 'POST',
            success: function(updatemodalhtml) {
                content = updatemodalhtml;
                //弹出修改框
                $.TeachDialog({
                    modalId: null,
                    animation: null,
                    title: '教师信息',
                    content: content,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    CloseButtonAddFunc: function() {
                    },
                    otherButtons: ['修改'],
                    otherButtonStyles: [],
                    bootstrapModalOption: {
                        backdrop: 'static'
                    },
                    largeSize: false,
                    smallSize: false,
                    dialogShow: function() {
                    },
                    dialogShown: function() {
                    },
                    dialogHide: function() {
                    },
                    dialogHidden: function() {
                    },
                    clickButton: function(sender, modal, index) {
                        $.ajax({
                            url: 'admin/Teacher/update',
                            data: $("#listForm").serialize(),
                            type: 'POST',
                            dataType: 'JSON',
                            success: function(returnData) {
                                if (returnData.status == 0) {
                                    $.TeachDialog({
                                        content: returnData.msg,
                                        showCloseButton: false,
                                        CloseButtonAddFunc: function() {

                                        },
                                    });
                                } else {
                                    modal.modal('hide');

                                    $.TeachDialog({
                                        content: returnData.msg,
                                        showCloseButton: false,
                                    });
                                    $('#datatable_teacherinfo').datagrid('reload');
                                }
                            },
                            error: function() {
                                $.TeachDialog({
                                    content: '添加失败',
                                });
                            }
                        });
                    },
                });
            }
        });

    })
    /**
     * 删除
     */
    $('#del').click(function() {
        var rows = $('#datatable_teacherinfo').datagrid('getSelections');
        if (rows.length == 0) {
            $.TeachDialog({
                content: '请至少选择一行数据！',
            });
            return;
        }
        var idsdata = "";
        var selectedstr = "";
        for (var i = 0; i < rows.length; i++) {
            idsdata += rows[i].id + ",";
            selectedstr += rows[i].teacher_name + "，";
        }
        idsdata = idsdata.substring(0, idsdata.length - 1);
        selectedstr = selectedstr.substring(0, selectedstr.length - 1);
        $.TeachDialog({
            content: "确认删除以下老师？<br>" + selectedstr,
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function(sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url: 'Admin/Teacher/deleteByIDs', //form action
                    dataType: 'JSON', //返回体类型
                    type: 'POST', // form type
                    data: "ids=" + idsdata, //  请求参数
                    success: function(data) {
                        if (data.status == 1) {
                            $.TeachDialog({
                                content: data.msg,
                                CloseButtonAddFunc: function() {
                                    $('#datatable_teacherinfo').datagrid('reload');
                                }
                            });
                        } else {
                            $.TeachDialog({
                                content: '删除失败',
                            });
                        }
                    },
                    error: function() {
                        $.TeachDialog({
                            content: '删除失败',
                        });
                    }
                });
            }
        })



    })
    /*
     * 导入老师信息excel表
     */
    var uploadOption =
            {
                // 提交url
                action: "admin/teacher/import",
                // 服务端接收的名称
                name: "Filedata",
                // 自动提交
                autoSubmit: false,
                // 选择文件之后…
                onChange: function(file, extension) {
                    if (new RegExp(/(xls)/i).test(extension)) {
                         $("#state").val(file);
                    } else {
                        $.ajax({
                            url: 'admin/admin/alertlog',
                            data: "t=3" ,
                            type: 'POST',
                            dataType: 'HTML', //返回的数据类型
                            success: function(updatemodalhtml) {
                                content = updatemodalhtml;
                                //弹出修改框
                                $.TeachDialog({
                                    modalId: null,
                                    animation: null,
                                    title: '系统消息',
                                    content: content,
                                    showCloseButton: true,
                                    showCloseButtonName: '关闭',
                                    CloseButtonAddFunc: function() {
                                    },

                                    otherButtonStyles: [],
                                    bootstrapModalOption: {
                                        backdrop: 'static'
                                    },
                                    largeSize: false,
                                    smallSize: false,
                                });
                            },
                            error: function() {
                                $.TeachDialog({
                                    content: '获取数据失败，无法进行修改',
                                });
                                return;
                            }
                        });
                    }
                },
                // 开始上传文件
                onSubmit: function(file, extension) {
                    $("#state").val("正在上传" + file + "..");
                },
                // 上传完成之后
                onComplete: function(file, response) {
                    $.ajax({
                        url: 'admin/admin/alertlog',
                        data: "t=2" ,
                        type: 'POST',
                        dataType: 'HTML', //返回的数据类型
                        success: function(updatemodalhtml) {
                            content = updatemodalhtml;
                            //弹出修改框
                            $.TeachDialog({
                                modalId: null,
                                animation: null,
                                title: '系统消息',
                                content: content,
                                showCloseButton: true,
                                showCloseButtonName: '关闭',
                                CloseButtonAddFunc: function() {
                                },

                                otherButtonStyles: [],
                                bootstrapModalOption: {
                                    backdrop: 'static'
                                },
                                largeSize: false,
                                smallSize: false,
                            });
                        },
                        error: function() {
                            $.TeachDialog({
                                content: '获取数据失败，无法进行修改',
                            });
                            return;
                        }
                    });
                   $("#state").val("");
				   $('#datatable_teacherinfo').datagrid('reload');
                }
            }

    // 初始化excel上传
    var oAjaxUpload = new AjaxUpload('#selector', uploadOption);

    // 给上传按钮增加上传动作
    $("#up").click(function()
    {
		if($("#state").val().length==0){
            $.ajax({
                url: 'admin/admin/alertlog',
                data: "t=1" ,
                type: 'POST',
                dataType: 'HTML', //返回的数据类型
                success: function(updatemodalhtml) {
                    content = updatemodalhtml;
                    //弹出修改框
                    $.TeachDialog({
                        modalId: null,
                        animation: null,
                        title: '系统消息',
                        content: content,
                        showCloseButton: true,
                        showCloseButtonName: '关闭',
                        CloseButtonAddFunc: function() {
                        },

                        otherButtonStyles: [],
                        bootstrapModalOption: {
                            backdrop: 'static'
                        },
                        largeSize: false,
                        smallSize: false,
                    });
                },
                error: function() {
                    $.TeachDialog({
                        content: '获取数据失败，无法进行修改',
                    });
                    return;
                }
            });
		}
        oAjaxUpload.submit();
    });
});