$(function() {
    function getSearchParams(params) {
        var searchParams = new Object();
        if (params != undefined) {
            searchParams = params;
        }
        $('.SearchForm').each(function() {
            var param = $(this).val().trim();
            if (param == undefined)
                param = '';
            searchParams[$(this).attr('id')] = param;
        });
        console.info("搜索参数:" + searchParams);
        return searchParams;
    }
    var cellwidth = ($("#datatable-feedback").width() - 55) / 7;
    var psval = $('#datatable-feedback').attr('data-size');
    if (psval == undefined || psval == "") {
        psval = 10;
    }
    $("#datatable-feedback").datagrid({
        striped: true,
        idField: 'id',
        remoteSort: false,
        collapsible: true,
        fit: false,
        url: 'admin/feedback/getfeedbacks',
        loadMsg: '请等待数据载入....',
        pagination: true,
        rownumbers: true,
        fitColumns: false,
        pageSize: psval,
        pageList: [psval, psval * 2, psval * 3, psval * 4, psval * 5],
        columns: [[
                {
                    field: 'classes',
                    title: '班级',
                    align: 'center',
                    width: cellwidth,
                    formatter: function(value) {
                        return value.classes_name;
                    }
                }, {
                    field: 'grade',
                    title: '年级',
                    align: 'center',
                    width: cellwidth,
                    formatter: function(value) {
                        return value.grade_name;
                    }

                }, {
                    field: 'teacher',
                    title: '老师',
                    align: 'center',
                    width: cellwidth,
                    formatter: function(value) {
                        return value.teacher_name;
                    }

                }, {
                    field: 'student',
                    title: '学生',
                    align: 'center',
                    width: cellwidth,
                    formatter: function(value) {
                        return value.student_name;
                    }
                }, {
                    field: 'feedback_content',
                    title: '评价',
                    align: 'center',
                    width: cellwidth,
                },{
                    field:'feedback_type',
                    title:"评价类型",
                    align:"center",
                    width:cellwidth,
                    formatter:function(value){
                        if(value==1){
                            return "学生评价老师";
                        }else if(value==0){
                            return "老师评价学生";
                        }
                    }
                }, {
                    field: 'feedback_time',
                    title: '时间',
                    align: 'center',
                    width: cellwidth
                }
            ]],
        onBeforeLoad: function(param) {
            param = getSearchParams(param);

        },
    });

    $('#Search').click(function() {

        $('#datatable-feedback').datagrid('reload');
    })

})