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
        var param = $(this).val().trim();
        var val = $("input:radio[name='student_sex']:checked").val();
        if (val == 0) {
            searchParams['student_sex'] = 0;
        } else if (val == 1) {
            searchParams['student_sex'] = 1;
        }
        if (param == undefined)
            param = '';
        searchParams[$(this).attr('name')] = param;
    });
    return searchParams;
}
//清空搜索条件
$("#Reset").click(function() {
    $('.SearchForm').val('');
    $("input:radio[name='student_sex']").removeAttr('checked');
});
/**
 * 页面自加载
 */
$(function() {
    $('#student_createtime').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true,
        clearBtn: true
    });

    var cellwidth = ($(".box-content.table-responsive").width() - 55) / 11;
    var psval = $('#datatable_studentinfo').attr('data-size');
    if (psval == undefined || psval == "") {
        psval = 10;
    }
    /**
     * easyui 生成表格数据
     */
    $('#datatable_studentinfo').datagrid({
        singleSelect: false, //允许选择多行
        striped: true,
        idField: 'id',
        remoteSort: false,
        collapsible: true,
        fit: false,
        url: 'admin/student/getStudents', //数据源路径
        loadMsg: '请等待数据载入....',
        pagination: true,
        rownumbers: true,
        fitColumns: false,
        pageSize: psval,
        pageList: [psval, psval * 2, psval * 3, psval * 4, psval * 5],
        columns: [[{
                    field: 'ck',
                    checkbox: true, //复选框
                    align: 'center',
                    sortable: true,
                    width: cellwidth,
                }, {
                    field: 'student_name',
                    title: '姓名',
                    align: 'center',
                    width: cellwidth,
                    sortable: true
                }, {
                    field: 'student_studentid',
                    title: '学号',
                    align: 'center',
                    width: cellwidth,
                    sortable: true
                }, {
                    field: 'student_phone',
                    title: '联系方式',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                }, {
                    field: 'student_idcard',
                    title: '身份证号',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                }, {
                    field: 'student_school',
                    title: '就读学校',
                    align: 'center',
                    width: cellwidth,
                    sortable: true
                }, {
                    field: 'grade',
                    title: '年级',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(value) {
                        return value.grade_name;
                    }

                },
                {
                    field: 'classes',
                    title: '班级',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(value) {
                        return value.classes_name;
                    }
                }, {
                    field: 'student_sex',
                    title: '性别',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(value) {
                        if (value == 0) {
                            return "男";
                        }
                        return "女";
                    }
                }, {
                    field: 'course',
                    title: '课程',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(value) {
                        return value.course_name;
                    }
                }, {
                    field: 'student_cardnum',
                    title: '支付宝账号',
                    align: 'center',
                    width: cellwidth,
                    sortable: true
                }, {
                    field: 'student_createtime',
                    title: '报名日期',
                    align: 'center',
                    width: cellwidth,
                    sortable: true
                }, {
                    field: 'student_status',
                    title: '状态',
                    align: 'center',
                    width: cellwidth,
                    sortable: true,
                    formatter: function(value) {
                        if (value == 0) {
                            return "在读";
                        }
                        return "毕业";
                    },
                }, {
                    field: 'student_remark',
                    title: '备注',
                    align: 'center',
                    width: cellwidth,
                    sortable: true
                }]],
        onBeforeLoad: function(param) {
            param = getSearchParams(param);
        },
    });


    $('#Search').click(function() {
        $('#datatable_studentinfo').datagrid('reload');
    })


    /**
     * 删除
     */
    $('#del').click(function() {
        var rows = $('#datatable_studentinfo').datagrid('getSelections');
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
            selectedstr += rows[i].student_name + "，";
        }
        idsdata = idsdata.substring(0, idsdata.length - 1);
        selectedstr = selectedstr.substring(0, selectedstr.length - 1);
        $.TeachDialog({
            content: "确认删除以下学生？<br>" + selectedstr,
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function(sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url: 'Admin/Student/deleteByIDs', //form action
                    dataType: 'JSON', //返回体类型
                    type: 'POST', // form type
                    data: "ids=" + idsdata, //  请求参数
                    success: function(data) {
                        if (data.status == 1) {
                            $.TeachDialog({
                                content: data.msg,
                            });
                            $('#datatable_studentinfo').datagrid('reload');
                            $('#datatable_studentinfo').datagrid('uncheckAll');
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


    /**
     * 编辑
     */
    $("#edit").click(function() {
        var row = $('#datatable_studentinfo').datagrid('getSelections');
        if (row.length < 1) {
            $.TeachDialog({
                content: '请选择一行数据进行修改！',
            });
            return;
        }
        if (row.length > 1) {
            $.TeachDialog({
                content: '只能选择一行进行修改！',
            });
            return;
        }
        //获得修改框内容
        var content = "";
        $.ajax({
            url: 'admin/student/updatemodal',
            data: "id=" + row[0].id,
            type: 'POST',
            dataType: 'HTML', //返回的数据类型
            success: function(updatemodalhtml) {
                content = updatemodalhtml;
                //弹出修改框
                $.TeachDialog({
                    modalId: null,
                    animation: null,
                    title: '修改学生信息',
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
                            url: 'admin/Student/update',
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
                                    $('#datatable_studentinfo').datagrid('reload');
                                }
                            },
                            error: function() {
                                $.TeachDialog({
                                    content: '修改失败',
                                });
                            },
                        });
                    },
                });
            },
            error: function() {
                $.TeachDialog({
                    content: '获取数据失败，无法进行修改',
                });
                return;
            }
        });

    });
    /*
     * 导入老师信息excel表
     */
    var uploadOption =
            {
                // 提交目标
                action: "admin/student/import",
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
					$('#datatable_studentinfo').datagrid('reload');
                }
            }

    // 初始化excel上传
    var oAjaxUpload = new AjaxUpload('#selector', uploadOption);

    // 给上传按钮增加上传动作
    $("#up").click(function()
    {
        var content = "";
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
