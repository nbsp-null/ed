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
        searchParams[$(this).attr('id')] = param;
    });
    return searchParams;
}


/**
 * 页面自加载
 */
$(function () {
    var cellwidth = ($(".box-content.table-responsive").width() - 55) / 11;
    var psval = $('#datatable_classroominfo').attr('data-size');
    if (psval == undefined || psval == "") {
        psval = 10;
    }
    /**
     * easyui 生成表格数据
     */
    $('#datatable_classroominfo').datagrid({
        striped: true,
        idField: "id",
        remoteSort: false,
        collapsible: true,
        fit: false,
        url: 'admin/Classroom/getDatas',//数据源路径
        loadMsg: '请等待数据载入....',
        pagination: true,
        rownumbers: true,
        fitColumns: true,
        pageSize: psval,
        pageList: [psval, psval * 2, psval * 3, psval * 4, psval * 5],
        columns: [[{
            field: 'classroom_name',
            title: '教室名称',
            align: 'center',
            width: cellwidth,
            sortable: true
        }, {
            field: 'classroom_containnum',
            title: '容纳人数',
            align: 'center',
            width: cellwidth,
            sortable: true
        }
		
		
	/*	, {
     *       field: 'campus',
      *      title: '所在校区',
      *      align: 'center',
      *      width: cellwidth,
       *     sortable: true,
       *     formatter:function(value){
       *         return value.campus_name;
       *     }
       * }
	*/
		]],
        onBeforeLoad: function (param) {
            param = getSearchParams(param);
        },
    });
//搜索
    $('#Search').click(function () {
        $('#datatable_classroominfo').datagrid('reload');
    });
//添加
    $('.addbook').click(function () {
        var content = "";
        $.ajax({
            url: 'admin/classroom/addroom',
            type: 'POST',
            dataType: 'HTML',//返回的数据类型
            success: function (updatemodalhtml) {
                content = updatemodalhtml;
                //弹出添加框
                $.TeachDialog({
                    title: '新增教室信息',
                    content: content,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['保存'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        $.ajax({
                            url: 'admin/Classroom/insert',
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
                                    $('#datatable_classroominfo').datagrid('reload');
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
    //编辑
    $('.editbook').click(function () {
        var row = $('#datatable_classroominfo').datagrid('getSelections');
        if (row.length<1) {
            $.TeachDialog({
                content: '请选择一行数据进行修改！',
            });
            return;
        }
        if (row.length>1) {
            $.TeachDialog({
                content: '只能选择一行进行修改！',
            });
            return;
        }
        var content = "";
        $.ajax({
            url: 'admin/classroom/updateroom',
            data: "id=" + row[0].id,
            type: 'POST',
            dataType: 'HTML',//返回的数据类型
            success: function (updatemodalhtml) {
                content = updatemodalhtml;
                //弹出修改框
                $.TeachDialog({
                    title: '修改教室信息',
                    content: content,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['保存'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        $.ajax({
                            url: 'admin/Classroom/update',
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
                                    $('#datatable_classroominfo').datagrid('reload');
                                }
                            },
                            error: function () {
                                $.TeachDialog({
                                    content: '系统异常，请联系管理员'
                                });
                            },

                        });

                    },
                });
            },
            error: function () {
                $.TeachDialog({
                    content: '系统异常，请联系管理员'
                });
                return;
            }
        });
    })
    /**
     * 删除
     */
    $('.removebook').click(function () {
        var rows = $('#datatable_classroominfo').datagrid('getSelections');
        if (rows.length == 0) {
            $.TeachDialog({
                content: '请至少选择一行数据！'
            });
            return;
        }

        var idsdata = "";
        var selectedstr = "";
        for (var i = 0; i < rows.length; i++) {
            idsdata += rows[i].id + ",";
            selectedstr += rows[i].classroom_name + "，";
        }
        idsdata = idsdata.substring(0, idsdata.length - 1);
        selectedstr = selectedstr.substring(0, selectedstr.length - 1);

        $.TeachDialog({
            content: "确认删除以下教室？<br>" + selectedstr,
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function (sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url: 'Admin/Classroom/deleteByIDs', //form action
                    dataType: 'JSON', //返回体类型
                    type: 'POST', // form type
                    data: "ids=" + idsdata,//  请求参数
                    success: function (data) {
                        if (data.status == 1) {
                            $.TeachDialog({
                                content: data.msg
                            });
                            $('#datatable_classroominfo').datagrid('reload');
                            $('#datatable_classroominfo').datagrid('uncheckAll');
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
});