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
    var psval = $('#datatable_card').attr('data-size');
    if (psval == undefined || psval == "") {
        psval = 10;
    }
    /**
     * easyui 生成表格数据
     */
    $('#datatable_card').datagrid({
        striped: true,
        idField : 'id',
        remoteSort: false,
        collapsible: true,
        fit: false,
        url: 'admin/Gatecard/Gatecard',//数据源路径
        loadMsg: '请等待数据载入....',
        pagination: true,
        rownumbers: true,
        fitColumns: true,
        pageSize: psval,
        pageList: [psval, psval * 2, psval * 3, psval * 4, psval * 5],
        columns: [[
        {
            field: 'classes',
            title: '班级',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return value.classes_name;
            }
        }, {
            field: 'course',
            title: '课程名称',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return value.course_name;
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
            field: 'countclass',
            title: '上课次数',
            align: 'center',
            width: cellwidth,
            sortable: true,
            
        },{
            field: 'sumact',
            title: '到课实际人数',
            align: 'center',
            width: cellwidth,
            sortable: true, 
            formatter:function(value){
                a = value;
                return a;
            }
        },{
            field: 'sumsre',
            title: '出勤率',
            align: 'center',
            width: cellwidth,
            sortable: true, 
            formatter:function(value){
				if(value==0){
					return 0;
				}else{
					b = (a/value).toFixed(3)*100+"";
					c = b.substr(0,4)+"%";
					return c;
				}
            }
        }
        
    ]],
        onBeforeLoad: function (param) {
            param = getSearchParams(param);
        }
    });
//搜索
    $('.SearchForm').change(function () {
        $('#datatable_card').datagrid('reload');
    });

  
	
});