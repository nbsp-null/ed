

$(function () {
	var cellwidth = ($(".box-content.table-responsive").width() - 55) / 11;
         /**
	 * 该班所有学生信息
	 **/
	$('#datatable_latestudent').datagrid({
        striped: true,
        idField : 'id',
        remoteSort: false,
        collapsible: true, 
        fit: false,
        url: 'admin/Schedule/getlatestudent',//数据源路径
        loadMsg: '请等待数据载入....',
        pagination: true,
        rownumbers: true,
        fitColumns: true,
        pageSize: 10,
        pageList: [10, 10 * 2, 10 * 3, 10 * 4, 10 * 5],
        rowStyler:function(index,row){
          if(row.called_status==0){
              return "color:red";
          }else if(row.called_status==1){
              return "color:green";
          }  
        },
        columns: [[{
            field: 'student',
            title: '学生姓名',
            align: 'center',
            width: cellwidth,
            sortable: true,
            formatter:function(value){
                return value.student_name;
            }
          
            },{
            field: 'called_evaluate',
            title: '评价',
            align: 'center',
            width: cellwidth, 
            sortable: true
            },{
             field:'called_absent',
             title:'旷课原因',
             align:'center',
             width:cellwidth
            }
        ]],
            onBeforeLoad: function (param) {
            param['id'] = $("#id").val();
        }
       
    });	
});










