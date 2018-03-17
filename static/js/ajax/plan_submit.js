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
function initBookType(id, addition) {
	$.ajax({
		url : 'Type/GetBookType',
		dataType : 'json',
		type : 'post',
		success : function(data) {
			$('#' + id).empty();
			if (addition != undefined) {
				$('#' + id).append('<option value="-1">全部</option>');
			}
			for (var i = 0; len = data.length, i < len; i++) {
				$('#' + id).append('<option value="' + data[i].intbooktypeid + '">' + data[i].strbooktypename + '</option>');
			}
		},
		async : true
	})
}

function initSupplierType(id, addition) {
	$.ajax({
		url : 'Type/GetSupplierType',
		dataType : 'json',
		type : 'post',
		success : function(data) {
			$('#' + id).empty();
			if (addition != undefined) {
				$('#' + id).append('<option value="-1">全部</option>');
			}
			for (var i = 0; len = data.length, i < len; i++) {
				$('#' + id).append('<option value="' + data[i].intsupplierid + '">' + data[i].strname + '</option>');
			}
		}
	})
}

$(function() {
	$.ajax({
		url : 'Plan/GetCourseType',
		dataType : 'json',
		type : 'post',
	}).success(function(data) {
		for (var i = 0; len = data.length, i < len; i++) {
			$('#CourseType').append('<option value="' + data[i].intcoursetypeid + '">' + data[i].strcoursename + '</option>')
		}
	})

	$('#bookselect').click(function() {
		LoadModel('selectbookmodel').done(function(responseConent) {
			$.TeachDialog({
				modalID : "SelectBooksModal",
				title : '从表中选择书籍',
				content : responseConent,
				largeSize : true,
				otherButtons : [ '选择' ],
				otherButtonStyles : [ 'btn btn-primary' ],
				clickButton : function(sender, modal, index) {
					if (index == 0) {
						var rows = $('#datatable_bookinfo').datagrid('getSelections');
						if (rows.length != 1) {
							$.TeachDialog({
								content : '你至少选择一行!',
								bootstrapModalOption : {},
							});
							return;
						} else {
							$('#BookId').empty();
							$('#BookId').append('<option value="' + rows[0].intbookid + '">' + rows[0].strbookname + '</option>')
						}
						modal.modal('hide');
					}
				},
				dialogHide : function() {
				},
				dialogShown : function() {
					$('#SearchDate').datepicker({
						format : "yyyy-mm-dd",
						todayBtn : "linked",
						autoclose : true,
						todayHighlight : true,
						clearBtn : true
					});
					initBookType('SearchType', "type");
					initSupplierType('SearchSupplier', "type")
					cellwidth = ($(".modal-body").width() - 55) / 11;
					var $mydategrid = $('#datatable_bookinfo');
					$mydategrid.datagrid({
						striped : true,
						remoteSort : false,
						collapsible : true,
						singleSelect : true,
						fit : false,
						url : 'Book/GetBooks',
						loadMsg : '请等待数据载入.....',
						pagination : true,
						rownumbers : true,
						fitColumns : true,
						columns : [ [ {
							field : 'strbookcoding',
							title : '编号',
							align : 'center',
							sortable : true,
							width : cellwidth,
						}, {
							field : 'strbookname',
							title : '书名',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'strbooksn',
							title : 'SN号',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'bookType',
							title : '类型',
							align : 'center',
							width : cellwidth,
							sortable : true,
							formatter : function(value) {
								return value.strbooktypename;
							}
						}, {
							field : 'strpress',
							title : '出版社',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'strauthor',
							title : '作者',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'supplier',
							title : '供应商',
							align : 'center',
							width : cellwidth,
							sortable : true,
							formatter : function(value) {
								return value.strname;
							}
						}, {
							field : 'strprice',
							title : '单价',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'intpricediscount',
							title : '折扣',
							align : 'center',
							width : cellwidth,
							sortable : true
						}, {
							field : 'dateaddtime',
							title : '录入时间',
							align : 'center',
							width : cellwidth + 35,
							sortable : true,
							formatter : function(value) {
								return unix2human(value);
							}
						} ] ],
						onBeforeLoad : function(param) {
							param = getSearchParams(param);
						},
						onDblClickRow : function(rowIndex, rowData) {
							$('#BookId').empty();
							$('#BookId').append('<option value="' + rowData.intbookid + '">' + rowData.strbookname + '</option>')
							$('#SelectBooksModal').modal('hide');
						}
					});
					$('#Search').click(function() {
						$mydategrid.datagrid('reload');
					})
				}
			});
		});
	})
	$('#reset').click(function() {
		LoadAjaxContent("ajax/plan_submit");
	})
	$('#submitform').click(function() {
		$(this).button('loading');
		$('.alert.alert-danger').slideUp();
		$('.alert.alert-danger').remove();
		var postdata = {};
		var error = '<div class="alert alert-danger" role="alert" style="display:none;line-height: 0px;width: 80%;height: 1px;">{errormsg}</div>'
		var check = true;
		$('.planinfo').each(function() {
			var curId = $(this).attr('id');
			var curval = $(this).val();
			if (!curval || curval.trim() === '') {
				if ($(this).prev().html() === undefined) {
					$(this).parent().parent().next().html(error.replace(/{errormsg}/g, '表单 ' + $(this).parent().prev().html().trimEnd(':') + ' 不能为空!'));
				} else {
					$(this).parent().next().html(error.replace(/{errormsg}/g, '表单 ' + $(this).prev().html().trimEnd(':') + ' 不能为空！'));
				}
				check = false;
				return false;
			} else {
				postdata[curId] = curval.trim();
			}
		});
		if (!check) {
			$('.alert.alert-danger').slideDown();
			$(this).button('reset');
			return;
		}
		$.ajax({
			url : 'Plan/Submit',
			dataType : 'json',
			type : 'post',
			async : true,
			data : postdata,
		}).success(function(response) {
			if (response) {
				$.TeachDialog({
					content : '更新成功!',
				});
				LoadAjaxContent("ajax/plan_submit");
			} else {
				$.TeachDialog({
					content : '更新失败!',
				});
			}

		});
		$(this).button('reset');
	})
})
