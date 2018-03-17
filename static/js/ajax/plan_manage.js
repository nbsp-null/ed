function getSearchParams(params) {
	var searchParams = new Object();
	if (params != undefined) {
		searchParams = params;
	}
	$('.SearchForm').each(function() {
		var param = $(this).val()
		if (param == null && param == undefined) {
			param = ''
		} else {
			param = param.trim();
		}
		searchParams[$(this).attr('id')] = param;
	});
	return searchParams;
}

function initUserType(id, addition) {
	var dtd = $.Deferred();
	var fillDom = function(domData) {
		$('#' + id).empty();
		if (addition != undefined) {
			$('#' + id).append('<option value="-1">全部</option>');
		}
		for (var i = 0; len = domData.length, i < len; i++) {
			$('#' + id).append('<option value="' + domData[i].intidentityid + '">' + domData[i].strname + '</option>');
		}
		dtd.resolve();
	}
	if ($('#datatable_perplaninfo').data('usertype') != undefined) {
		fillDom($('#datatable_perplaninfo').data('usertype'));
	} else {
		$.ajax({
			url : 'Type/GetUserTypeAll',
			dataType : 'json',
			type : 'post',
			success : function(data) {
				$('#datatable_perplaninfo').data('usertype', data);
				fillDom(data);
			},
			async : true
		})
	}
	return dtd.promise();
}
function initUserDepartMent(id, type, addition) {
	var typedata = type.toString().replace('-', 'minus');
	var dtd = $.Deferred();
	var filldom = function(domData) {
		$('#' + id).empty();
		if (addition != undefined) {
			$('#' + id).append('<option value="-1">全部</option>');
		}
		for (var i = 0; len = domData.length, i < len; i++) {
			$('#' + id).append('<option value="' + domData[i].intid + '">' + domData[i].strname + '</option>');
		}
		dtd.resolve();
	}
	if ($('#datatable_perplaninfo').data('userdepartment' + typedata) != undefined) {
		filldom($('#datatable_perplaninfo').data('userdepartment' + typedata));
	} else {
		$.ajax({
			url : 'Type/GetDepartMent',
			type : 'post',
			dataType : 'json',
			data : {
				id : type
			},
			success : function(data) {
				$('#datatable_perplaninfo').data('userdepartment' + typedata, data);
				filldom(data);
			},
			async : true
		})
	}

	return dtd.promise();
}
function getDialogSearchParams(params) {
	var searchParams = new Object();
	if (params != undefined) {
		searchParams = params;
	}
	$('.DialogSearchForm').each(function() {
		var param = $(this).val();
		if (param == null && param == undefined) {
			param = ''
		} else {
			param = param.trim();
		}
		searchParams[$(this).attr('id')] = param;
	});
	return searchParams;
}

function getPlanStatus(domId) {
	var dtd = $.Deferred();
	var fillDom = function(domData) {
		for (var i = 0; len = domData.length, i < len; i++) {
			$('#' + domId).append('<option value="' + domData[i].intplanstatusid + '">' + domData[i].strmark + '</option>')
		}
		dtd.resolve();
	}
	if ($('#PlanStatus').data('bookplanstatus') == undefined) {
		$.ajax({
			url : 'Plan/GetBookPlanStatus',
			dataType : 'json',
			type : 'post'
		}).success(function(data) {
			$('#PlanStatus').data('bookplanstatus', data);
			fillDom(data);
		})
	} else {
		fillDom($('#PlanStatus').data('bookplanstatus'));
	}
	return dtd.promise();
}

function toolBarClick(type, oneneed, pendingneed) {
	var rows = $('#datatable_perplaninfo').datagrid('getSelections');

	if (rows.length < 1) {
		$.TeachDialog({
			content : '请至少选择一行!',
			bootstrapModalOption : {}
		});
		return;
	}
	if (oneneed) {
		if (rows.length > 1) {
			$.TeachDialog({
				content : '最多选择一行!',
				bootstrapModalOption : {}
			});
			return;
		}
	}
	var plans = new Array();

	for (var i = 0, len = rows.length; i < len; i++) {
		plans.push(rows[i].intplanid);
		if (pendingneed && rows[i].bookPlanStatus.intplanstatusid != 1) {
			$.TeachDialog({
				content : '你选择的计划状态不是审核中!',
				bootstrapModalOption : {}
			});
			return;
		}
	}

	var url = undefined;
	switch (type) {
	case 1: {
		if (url == undefined) {
			url = 'PassPlan';
		}
	}
	case 2: {
		if (url == undefined) {
			url = 'RejectPlan';
		}
	}
	case 3: {
		if (url == undefined) {
			url = 'RefusePlan';
		}
		$.ajax({
			url : 'Plan/' + url,
			dataType : 'json',
			type : 'post',
			data : {
				planId : plans
			}
		}).success(function(data) {
			if (data) {
				$.TeachDialog({
					content : '计划状态变更成功!',
					bootstrapModalOption : {}
				});
			} else {
				$.TeachDialog({
					content : '计划状态变更失败!',
					bootstrapModalOption : {}
				});
			}
		})
		break;
	}
	case 4: {
		url = 'ChangeStatus';
		LoadModel('changeplanstatusmodel').done(function(responseContent) {
			$.TeachDialog({
				title : '更改计划状态',
				content : responseContent,
				otherButtons : [ '更改' ],
				otherButtonStyles : [ 'btn btn-primary' ],
				dialogShow : function() {
					getPlanStatus('ChangePlanStatus');
				},
				clickButton : function(sender, modal, index) {
					if (index == 0) {
						$.ajax({
							url : 'Plan/' + url,
							dataType : 'json',
							type : 'post',
							data : {
								planId : plans,
								Status : $('#ChangePlanStatus').val()
							}
						}).success(function(data) {
							modal.modal('hide');
							if (data) {
								$.TeachDialog({
									content : '更改成功!'
								});
								$('#datatable_perplaninfo').datagrid('reload');
							} else {
								$.TeachDialog({
									content : '更改失败!'
								});
							}
						})
					}
				}
			})
		})
		break;
	}
	case 5: {
		$.TeachDialog({
			title : '计划历史',
			content : '<div id="historytable" class="table-responsive"><table class="table"><caption>计划历史记录</caption></table></div>',
			largeSize : true,
			dialogShow : function() {
				$.ajax({
					url : 'Plan/GetPlanHistory',
					dataType : 'json',
					type : 'post',
					data : {
						PlanId : plans[0]
					}
				}).success(function(data) {
					$('#historytable table').append('<thead><tr><th>Id</th><th>操作</th><th>具体信息</th><th>操作人</th><th>操作时间</th></tr></thead><tbody></tbody>');
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
						$('#historytable table tbody').append('<tr><td>' + (i + 1) + '</td><td>' + data[i].operation.stroperationname + '</td><td>' + changeString.trimEnd('<br>') + '</td><td>' + data[i].user.strname + '</td><td>' + unix2human(data[i].datecreatetime) + '</td></tr>');
					}
				});
			}
		})
		break;
	}
	}
}

function queryPlanHistory() {
	$.TeachDialog({
		title : '计划信息',
		content : '<div id="historytable" class="table-responsive"><table class="table"><caption>计划历史信息</caption></table></div>',
		dialogShow : function() {
			$.ajax({
				url : 'Plan/GetPerPlanHistory',
				dataType : 'json',
				type : 'post',
				data : {
					PlanId : planId
				}
			}).success(function(data) {
				$('#historytable table').append('<thead><tr><th>Id</th><th>操作</th><th>具体信息</th><th>操作时间</th></tr></thead><tbody></tbody>');
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
		}
	})
}

$(function() {
	console.log('aa')
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
		type : 'post'
	}).success(function(data) {
		for (var i = 0; len = data.length, i < len; i++) {
			$('#CourseType').append('<option value="' + data[i].intcoursetypeid + '">' + data[i].strcoursename + '</option>')
		}
	})

	getPlanStatus('PlanStatus');

	var psval = $('#datatable_perplaninfo').attr('data-size');
	if (psval == undefined || psval == "") {
		psval = 10;
	}
	cellwidth = ($(".box-content.table-responsive").width() - 55) / 11;
	var $mydatagrid = $('#datatable_perplaninfo');
	$mydatagrid.datagrid({
		striped : true,
		remoteSort : false,
		fit : false,
		url : 'Plan/GetAllPlan',
		loadMsg : '请等待数据载入.....',
		pagination : true,
		rownumbers : true,
		fitColumns : true,
		pageSize : psval,
		pageList : [ psval, psval * 2, psval * 3, psval * 4, psval * 5 ],
		columns : [ [ {
			field : 'strcoursename',
			title : '课程名',
			align : 'center',
			sortable : true,
			width : cellwidth
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
			sortable : true
		}, {
			field : 'intstudcount',
			title : '学生数',
			align : 'center',
			width : cellwidth * 0.5,
			sortable : true
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
			title : '时间',
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
			field : 'user',
			title : '申请人',
			align : 'center',
			width : cellwidth,
			formatter : function(value) {
				return value.strname;
			}
		}, {
			field : 'strmark',
			title : '备注',
			align : 'center',
			width : cellwidth
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
			text : "通过计划",
			iconCls : 'fa fa-pencil',
			handler : function() {
				toolBarClick(1, false, true);
			}
		}, '-', {
			text : "驳回计划",
			iconCls : 'fa fa-remove',
			handler : function() {
				toolBarClick(2, false, true);
			}
		}, '-', {
			text : "拒绝计划",
			iconCls : 'fa fa-recycle',
			handler : function() {
				toolBarClick(3, false, true);
			}
		}, '-', {
			text : "更改为其他状态",
			iconCls : 'fa fa-search',
			handler : function() {
				toolBarClick(4, false, false);
			}
		}, '-', {
			text : "查询历史",
			iconCls : 'fa fa-search',
			handler : function() {
				toolBarClick(5, true, false);
			}
		}, '-', {
			text : "导出下方所有数据到Excel",
			iconCls : 'fa fa-download',
			handler : function() {
				$.DownloadFile({
					url : 'Plan/ExportPerPlan',
					method : 'post',
					data : getSearchParams()
				});
			}
		}, '-', {
			text : "按班级导出数据统计到Excel",
			iconCls : 'fa fa-download',
			handler : function() {
				$.DownloadFile({
					url : 'Plan/ExportAllPlanByClasses',
					method : 'post',
					data : getSearchParams()
				})
			}
		} ],
		onClickRow : function(rowIndex, rowData) {
			$('#notification').html(
					'<strong class="col-xs-3">单价:' + rowData.book.strprice.toFixed(2) + '</strong><strong class="col-xs-3">折扣:' + rowData.book.intpricediscount.toFixed(2) + '</strong><strong class="col-xs-3">学生总计:'
							+ (rowData.intstudcount * rowData.book.strprice * rowData.book.intpricediscount / 10).toFixed(2) + '</strong><strong class="col-xs-3">教师总计:' + (rowData.intteaccount * rowData.book.strprice * rowData.book.intpricediscount / 10).toFixed(2) + '</strong>');
		},
		onLoadSuccess : function(data) {
			$('#notification').html('未选择计划');
		}
	});

	$('#userselect').click(function() {
		LoadModel('selectusermodel').done(function(responseContent) {
			$.TeachDialog({
				modalID : "SelectUsersModal",
				title : '从表中选择用户',
				content : responseContent,
				largeSize : true,
				otherButtons : [ '清除', '选择' ],
				otherButtonStyles : [ 'btn btn-primary' ],
				clickButton : function(sender, modal, index) {
					if (index == 1) {
						var rows = $('#datatable_userinfo').datagrid('getSelections');
						if (rows.length != 1) {
							$.TeachDialog({
								content : '请至少选择一行!',
								bootstrapModalOption : {}
							});
							return;
						} else {
							$('#UserId').empty();
							$('#UserId').append('<option value="' + rows[0].intid + '">' + rows[0].strname + '</option>')
						}
					} else if (index == 0) {
						$('#UserId').empty();
					}
					modal.modal('hide');
				},
				dialogHide : function() {
					
				},
				dialogShown : function() {
					$('#SearchTime').datepicker({
						format : "yyyy-mm-dd",
						todayBtn : "linked",
						autoclose : true,
						todayHighlight : true,
						clearBtn : true
					});

					initUserType('SearchUserType', true);
					initUserDepartMent('SearchDepartMent', 1, true).done(function() {
						$('#SearchDepartMent').change(function() {
							initUserDepartMent('SearchMajor', $('#SearchDepartMent').val(), true);
						})
						initUserDepartMent('SearchMajor', $('#SearchDepartMent').val(), true);
					})

					var cellwidth = ($(".modal-body").width() - 55) / 11;
					$('#datatable_userinfo').datagrid({
						striped : true,
						remoteSort : false,
						collapsible : true,
						fit : false,
						url : 'User/GetAllUser',
						loadMsg : '请等待数据载入.....',
						pagination : true,
						rownumbers : true,
						singleSelect : true,
						fitColumns : true,
						columns : [ [ {
							field : 'intid',
							title : '用户ID',
							align : 'center',
							sortable : true,
							width : cellwidth * 0.5
						}, {
							field : 'username',
							title : '用户名',
							align : 'center',
							sortable : true,
							width : cellwidth
						}, {
							field : 'strname',
							title : '姓名',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'userType',
							title : '用户组',
							align : 'center',
							width : cellwidth,
							sortable : true,
							formatter : function(value) {
								return value.strname;
							}
						}, {
							field : 'userDepartMent',
							title : '系部',
							align : 'center',
							width : cellwidth,
							sortable : true,
							formatter : function(value) {
								return value.strname;
							}
						}, {
							field : 'userMajor',
							title : '专业',
							align : 'center',
							width : cellwidth * 1.4,
							sortable : true,
							formatter : function(value) {
								return value.strname;
							}
						}, {
							field : 'strstunum',
							title : '学号',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'strphone',
							title : '手机',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'strmail',
							title : '邮箱',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'dateregtime',
							title : '创建时间',
							align : 'center',
							width : cellwidth * 1.1 + 10,
							sortable : true,
							formatter : function(value) {
								return unix2human(value);
							}
						} ] ],
						onBeforeLoad : function(param) {
							param = getDialogSearchParams(param);
						},
						onDblClickRow : function(rowIndex, rowData) {
							$('#UserId').empty();
							$('#UserId').append('<option value="' + rowData.intid + '">' + rowData.strname + '</option>')
							$('#SelectUsersModal').modal('hide');
						}
					});

					$('#DialogSearch').click(function() {
						$('#datatable_userinfo').datagrid('reload');
					})
				}
			});
		})
	})
	$('#Search').click(function() {
		$mydatagrid.datagrid('reload');
	})
})