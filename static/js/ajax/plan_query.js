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
	return searchParams;
}

function toolBarClick(type) {
	var rows = $('#datatable_perplaninfo').datagrid('getSelections');
	if (rows.length != 1) {
		$.TeachDialog({
			content : '你最多可以选择一行!',
			bootstrapModalOption : {},
		});
		return;
	}
	var planId = rows[0].intplanid;
	switch (type) {
	case 1: {
		if (rows[0].bookPlanStatus.intplanstatusid > 2) {
			$.TeachDialog({
				content : '这个计划的状态不能被改变，因为它的状态是 ' + rows[0].bookPlanStatus.strmark + '!',
				bootstrapModalOption : {},
			});
			return;
		}
		LoadModel('changeplandetailmodel').done(function(responseContent) {
			$.TeachDialog({
				title : '更改计划',
				content : responseContent,
				otherButtons : [ '确认' ],
				otherButtonStyles : [ 'btn btn-primary' ],
				dialogShow : function() {
					$("#StuChange,#TeaChange").TouchSpin({
						min : -100,
						max : 100,
						step : 1
					});
				},
				clickButton : function(sender, modal, index) {
					if (index == 0) {
						if (($('#StuChange').val() == '0' && $('#TeaChange').val() == '0') || $('#StuChange').val().trim() == "" || $('#TeaChange').val().trim() == "") {
							$.TeachDialog({
								content : '参数错误!',
							})
							return;
						}
						$.ajax({
							url : 'Plan/Change',
							dataType : 'json',
							type : 'post',
							data : {
								StuChange : $('#StuChange').val(),
								TeaChange : $('#TeaChange').val(),
								ChangeReason : $('#ChangeReason').val(),
								PlanId : planId
							}
						}).success(function(data) {
							if (data) {
								$.TeachDialog({
									content : '更改成功!',
								})
								modal.modal('hide');
								$('#datatable_perplaninfo').datagrid('reload');
							} else {
								$.TeachDialog({
									content : '更改失败!',
								})
							}
						})
					}
				},
			});
		});
		break;
	}
	case 2: {
		if (rows[0].bookPlanStatus.intplanstatusid > 2) {
			$.TeachDialog({
				content : '这个计划不能被取消，因为它的状态是 ' + rows[0].bookPlanStatus.strmark + '!',
				bootstrapModalOption : {},
			});
			return;
		}
		$.ajax({
			url : 'Plan/Cancel',
			dataType : 'json',
			type : 'post',
			data : {
				PlanId : planId
			}
		}).success(function(data) {
			if (data) {
				$.TeachDialog({
					content : '计划已经取消',
					bootstrapModalOption : {},
				});
				$('#datatable_perplaninfo').datagrid('reload');
			}
		})
		break;
	}
	case 3: {
		if (rows[0].bookPlanStatus.intplanstatusid != 2) {
			$.TeachDialog({
				content : '计划不能被再次提交，因为它的计划状态是 ' + rows[0].bookPlanStatus.strmark + '!',
				bootstrapModalOption : {},
			});
			return;
		}
		$.ajax({
			url : 'Plan/ReSubmit',
			dataType : 'json',
			type : 'post',
			data : {
				PlanId : planId
			}
		}).success(function(data) {
			if (data) {
				$.TeachDialog({
					content : 'The Plan has been canceled!',
					bootstrapModalOption : {},
				});
				$('#datatable_perplaninfo').datagrid('reload');
			}
		})
		break;
	}
	case 4: {
		$.TeachDialog({
			title : '计划历史',
			content : '<div id="historytable" class="table-responsive"><table class="table"><caption>计划历史变化</caption></table></div>',
			dialogShow : function() {
				$.ajax({
					url : 'Plan/GetPerPlanHistory',
					dataType : 'json',
					type : 'post',
					data : {
						PlanId : planId
					}
				}).success(function(data) {
					$('#historytable table').append('<thead><tr><th>Id</th><th>操作</th><th>变更</th><th>操作时间</th></tr></thead><tbody></tbody>');
					for (var i = 0, len = data.length; i < len; i++) {
						var changeString = '';
						if (data[i].bookPlanChange && data[i].bookPlanChange.intbookchangeid != -1) {
							var stcount = data[i].bookPlanChange.intstudent;
							var teacount = data[i].bookPlanChange.intteacher;
							if (stcount != 0) {
								changeString += (stcount > 0 ? '增加' : '减少') + ' 学生数 :' + Math.abs(stcount) + '<br>';
							}
							if (teacount != 0) {
								changeString += (teacount > 0 ? '增加' : '减少') + ' 教师数 :' + Math.abs(teacount) + '<br>';
							}
						} else {
							changeString = 'none';
						}
						$('#historytable table tbody').append('<tr><td>' + (i + 1) + '</td><td>' + data[i].operation.stroperationname + '</td><td>' + changeString.trimEnd('<br>') + '</td><td>' + unix2human(data[i].datecreatetime) + '</td></tr>');
					}
				});
			},
		})
		break;
	}
	}
}

$(function() {

	// initial date input
	$('#SearchDate').datepicker({
		format : "yyyy-mm-dd",
		todayBtn : "linked",
		autoclose : true,
		todayHighlight : true,
		clearBtn : true
	});

	// initial type
	$.ajax({
		url : 'Plan/GetCourseType',
		dataType : 'json',
		type : 'post',
	}).success(function(data) {
		for (var i = 0; len = data.length, i < len; i++) {
			$('#CourseType').append('<option value="' + data[i].intcoursetypeid + '">' + data[i].strcoursename + '</option>')
		}
	})

	$.ajax({
		url : 'Plan/GetBookPlanStatus',
		dataType : 'json',
		type : 'post',
	}).success(function(data) {
		for (var i = 0; len = data.length, i < len; i++) {
			$('#PlanStatus').append('<option value="' + data[i].intplanstatusid + '">' + data[i].strmark + '</option>')
		}
	})
	var psval = $('#datatable_perplaninfo').attr('data-size');
	if (psval == undefined || psval == "") {
		psval = 10;
	}
	cellwidth = ($(".box-content.table-responsive").width() - 55) / 10;
	var $mydatagrid = $('#datatable_perplaninfo');
	$mydatagrid.datagrid({
		striped : true,
		remoteSort : false,
		fit : false,
		url : 'Plan/GetPerPlan',
		loadMsg : '请等待数据载入.....',
		pagination : true,
		rownumbers : true,
		singleSelect : true,
		fitColumns : true,
		pageSize : psval,
		pageList : [ psval, psval * 2, psval * 3, psval * 4, psval * 5 ],
		columns : [ [ {
			field : 'strcoursename',
			title : '课程名',
			align : 'center',
			sortable : true,
			width : cellwidth,
		}, {
			field : 'courseType',
			title : '课程类型',
			align : 'center',
			width : cellwidth,
			sortable : true,
			formatter : function(value) {
				return value.strcoursename;
			}
		}, {
			field : 'strclass',
			title : '班级',
			align : 'center',
			width : cellwidth,
			sortable : true,
		}, {
			field : 'intstudcount',
			title : '学生数',
			align : 'center',
			width : cellwidth * 0.5,
			sortable : true,
		}, {
			field : 'intteaccount',
			title : '教师数',
			align : 'center',
			width : cellwidth * 0.5,
			sortable : true
		}, {
			field : 'book',
			title : '书名',
			align : 'center',
			width : cellwidth * 2,
			sortable : true,
			formatter : function(value) {
				return value.strbookname;
			}
		}, {
			field : 'intfromyear',
			title : '学年',
			align : 'center',
			width : cellwidth,
			formatter : function(value, row) {
				return value + '-' + row.inttoyear;
			}
		}, {
			field : 'intterm',
			title : '学期',
			align : 'center',
			width : cellwidth,
			sortable : true,
			formatter : function(value) {
				if (value) {
					return '下半学年';
				} else {
					return '上半学年';
				}
			}
		}, {
			field : 'strmark',
			title : '备注',
			align : 'center',
			width : cellwidth,
		}, {
			field : 'datecreatetime',
			title : '申请时间',
			align : 'center',
			width : cellwidth + 35,
			sortable : true,
			formatter : function(value) {
				return unix2human(value);
			}
		}, {
			field : 'bookPlanStatus',
			title : '状态',
			align : 'center',
			width : cellwidth,
			sortable : true,
			formatter : function(value) {
				return value.strmark;
			}
		} ] ],
		onBeforeLoad : function(param) {
			param = getSearchParams(param);
		},
		toolbar : [ {
			text : "更改计划",
			iconCls : 'fa fa-pencil',
			handler : function() {
				toolBarClick(1);
			}
		}, '-', {
			text : "取消计划",
			iconCls : 'fa fa-remove',
			handler : function() {
				toolBarClick(2);
			}
		}, '-', {
			text : "再次提交",
			iconCls : 'fa fa-recycle',
			handler : function() {
				toolBarClick(3);
			}
		}, '-', {
			text : "查询历史",
			iconCls : 'fa fa-search',
			handler : function() {
				toolBarClick(4);
			}
		}, '-', {
			text : "导出数据到Excel",
			iconCls : 'fa fa-download',
			handler : function() {
				$.DownloadFile({
					url : 'Plan/ExportPerPlan',
					method : 'post',
					data : getSearchParams(),
				})
			}
		} ]
	});

	$('#Search').click(function() {
		$mydatagrid.datagrid('reload');
	})
})