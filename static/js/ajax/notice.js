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
    $('#time').datepicker({
        format : "yyyy-mm-dd",
        todayBtn : "linked",
        autoclose : true,
        todayHighlight : true,
        clearBtn : true
    });
    var cellwidth = ($(".box-content.table-responsive").width() - 55) / 11;
    var psval = $('#datatable_noticeinfo').attr('data-size');
    if (psval == undefined || psval == "") {
        psval = 10;
    }
    /**
     * easyui 生成表格数据
     */
    $('#datatable_noticeinfo').datagrid({
        striped: true,
        remoteSort: false,
        idField: 'id',
        collapsible: true,
        fit: false,
        url: 'admin/notice/getDatas',//数据源路径
        loadMsg: '请等待数据载入....',
        pagination: true,
        rownumbers: true,
        fitColumns: true,
        pageSize: psval,
        pageList: [psval, psval * 2, psval * 3, psval * 4, psval * 5],
        columns: [[{
            field: 'admin',
            title: '创建人',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return value.name;
            }
        }, {
            field: 'title',
            title: '公告名称',
            align: 'center',
            width: cellwidth,
            sortable: true
        }, {
            field: 'contents',
            title: '公告内容',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return value.substring(0,6)+'...';
            }
        }, {
            field: 'type',
            title: '公告类型',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                if(value=='1'){
                    return "后台公告";
                }else if(value=='2'){
                    return "学生端公告";
                }else if(value=='3'){
                    return "老师端公告";
                }
            }
        },{
            field: 'time',
            title: '创建时间',
            align: 'center',
            width: cellwidth,
            sortable: true
        }, {
            field: 'status',
            title: '是否发布',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter: function (value) {
                if (value == 0) {
                    return "未发布";
                }
                return "已发布";
            }
        }
	/**	,{
      *     field: 'campus',
      *     title: '所在分区',
      *     align: 'center',
      *     width: cellwidth,
      *      sortable: true,
      *      formatter:function(value){
      *          return value.campus_name;
     *       }
     *   }
	*/
		]],
        onBeforeLoad: function (param) {
            param = getSearchParams(param);
        },
    });
//搜索
    $('#Search').click(function () {
        $('#datatable_noticeinfo').datagrid('reload');
    });
//添加
    $('.addbook').click(function () {
        var content = "";
        $.ajax({
            url: 'admin/notice/addnotice',
            type: 'POST',
            dataType: 'HTML',//返回的数据类型
            success: function (updatemodalhtml) {
                content = updatemodalhtml;
                //弹出添加框
                $.TeachDialog({
                    title: '新增公告信息',
                    content: content,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['保存'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        $.ajax({
                            url: 'admin/notice/insert',
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
                                    $('#datatable_noticeinfo').datagrid('reload');
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
        var row = $('#datatable_noticeinfo').datagrid('getSelections');
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
            url: 'admin/notice/updatenotice',
            data: "id=" + row[0].id,
            type: 'POST',
            dataType: 'HTML',//返回的数据类型
            success: function (updatemodalhtml) {
                content = updatemodalhtml;
                //弹出修改框
                $.TeachDialog({
                    title: '修改公告信息',
                    content: content,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['保存'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        $.ajax({
                            url: 'admin/Notice/update',
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
                                    $('#datatable_noticeinfo').datagrid('reload');
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
        var rows = $('#datatable_noticeinfo').datagrid('getSelections');
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
            selectedstr += rows[i].title + "，";
        }
        idsdata = idsdata.substring(0, idsdata.length - 1);
        selectedstr = selectedstr.substring(0, selectedstr.length - 1);

        $.TeachDialog({
            content: "确认删除以下公告？<br>" + selectedstr,
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function (sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url: 'Admin/Notice/deleteByIDs', //form action
                    dataType: 'JSON', //返回体类型
                    type: 'POST', // form type
                    data: "ids=" + idsdata,//  请求参数
                    success: function (data) {
                        if (data.status == 1) {
                            $.TeachDialog({
                                content: data.msg
                            });
                            $('#datatable_noticeinfo').datagrid('reload');
                            $('#datatable_noticeinfo').datagrid('uncheckAll');
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
     * 提交
     */
    $('.commitbook').click(function () {
        var rows = $('#datatable_noticeinfo').datagrid('getSelections');
        if (rows.length == 0) {
            $.TeachDialog({
                content: '请至少选择一行数据！'
            });
            return;
        }else {
            for (var i = 0; i < rows.length; i++) {
                if(rows[i].status == 1){
                    $.TeachDialog({
                        content: '此条公告已发表！'
                    });
                    return;
                }

            }
        }

        var idsdata = "";
        var selectedstr = "";
        for (var i = 0; i < rows.length; i++) {
            idsdata += rows[i].id + ",";
            selectedstr += rows[i].title + "，";
        }
        idsdata = idsdata.substring(0, idsdata.length - 1);
        selectedstr = selectedstr.substring(0, selectedstr.length - 1);

        $.TeachDialog({
            content: "确认提交以下公告？<br>" + selectedstr,
            showCloseButton: true,
            showCloseButtonName: '取消',
            otherButtons: ['确认'],
            otherButtonStyles: ['btn-danger'],
            clickButton: function (sender, modal, index) {
                modal.modal('hide');
                $.ajax({
                    url: 'Admin/Notice/commitByIDs', //form action
                    dataType: 'JSON', //返回体类型
                    type: 'POST', // form type
                    data: "ids=" + idsdata,//  请求参数
                    success: function (data) {
                        if (data.status == 1) {
                            $.TeachDialog({
                                content: data.msg
                            });
                            $('#datatable_noticeinfo').datagrid('reload');
                            $('#datatable_noticeinfo').datagrid('uncheckAll');
                        } else {
                            $.TeachDialog({
                                content: '发布失败'
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