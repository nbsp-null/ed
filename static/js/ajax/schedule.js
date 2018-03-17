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
    var psval = $('#datatable_scheduleinfo').attr('data-size');
    if (psval == undefined || psval == "") {
        psval = 10;
    }
    /**
     * easyui 生成表格数据
     */
    $('#datatable_scheduleinfo').datagrid({
        striped: true,
        idField : 'id',
        remoteSort: false,
        collapsible: true,
        fit: false,
        url: 'admin/Schedule/getDatas',//数据源路径
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
            title: '所在教室',
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
            title: '开始日期',
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
            title: '上课时长',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
               var   a =  parseInt(value/60);
               var  b = (((value/60)-parseInt(value/60))*60).toFixed(0);
              if(b.length<2){
                  b = "0"+b;
              }
               return a+":"+b;
            }
        },{
           field: 'schedule_prenum',
           title: '班级容纳',
            align: 'center',
            width: cellwidth,
           formatter:function(value){
                    b = value;
                    return b;
           } 
        },{
            field: 'schedule_actnum',
            title: '到课情况',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                var num = b.indexOf("/");
                return value+"/"+b.substr(0,num);
            }
        },{
            field:"schedule_status",
            title:"状态",
            align:"center",
            width:cellwidth,
            formatter:function(value){
                if(value==0){
                    return "<font color='blue'>未上课</font>";
                }else if(value==1){
                    return "<font color='gray'>已上课</font>";
                }else if(value==2){
                    return "<font color='red'>已取消</font>"
                }
            }
        },{
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
        },
        
    ]],
        onBeforeLoad: function (param) {
            param = getSearchParams(param);
        }
    });
//搜索
    $('.SearchForm').change(function () {
        $('#datatable_scheduleinfo').datagrid('reload');
    });
    //查看到课情况
    $('.editbook').click(function () {
        var row = $('#datatable_scheduleinfo').datagrid('getSelections');
        
        if (row.length!=1) {
            $.TeachDialog({
                content: '只能一个班查看！',
            });
            return;
        }
        var content = "";
        $.ajax({
            url: 'admin/Schedule/getSchedule',
            data: "id=" + row[0].id,
            type: 'POST',
            dataType: 'HTML',//返回的数据类型
            success: function (updatemodalhtml) {
                content = updatemodalhtml;
                //弹出修改框
                $.TeachDialog({
                    title: '点名',
                    content: content, 
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['保存'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        $.ajax({
                            url: 'admin/Schedule/mark',
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
                                    $('#datatable_scheduleinfo').datagrid('reload');
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
  
	
});