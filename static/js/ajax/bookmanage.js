var htmltmp = $('#newformRange').html();
function setVal(id, obj) {
	$('#editCode' + id).val(obj.strbookcoding);
	$('#editName' + id).val(obj.strbookname);
	$('#editSN' + id).val(obj.strbooksn);
	$('#editPress' + id).val(obj.strpress);
	$('#editAuthor' + id).val(obj.strauthor);
	$('#editPrice' + id).val(obj.strprice);
	$('#editDisCount' + id).val(obj.intpricediscount);
	initBookType('editBookType' + id, undefined).done(function() {
		$('#editBookType' + id).val(obj.bookType.intbooktypeid);
	});
	initSupplierType('editSupplierType' + id, undefined).done(function() {
		$('#editSupplierType' + id).val(obj.supplier.intsupplierid);
	});
}

function initBookType(id, addition) {
	var dtd = $.Deferred();
	var fillDom = function(domData) {
		$('#' + id).empty();
		if (addition != undefined) {
			$('#' + id).append('<option value="-1">全部</option>');
		}
		for (var i = 0; len = domData.length, i < len; i++) {
			$('#' + id).append('<option value="' + domData[i].intbooktypeid + '">' + domData[i].strbooktypename + '</option>');
		}
		dtd.resolve();
	}
	if ($('#operationpanel').data('booktype') != undefined) {
		fillDom($('#operationpanel').data('booktype'));
	} else {
		$.ajax({
			url : 'Type/GetBookType',
			dataType : 'json',
			type : 'post',
			async : true
		}).success(function(data) {
			$('#operationpanel').data('booktype', data);
			fillDom(data);
		})
	}
	return dtd.promise();
}

function initSupplierType(id, addition) {
	var dtd = $.Deferred();
	var fillDom = function(domData) {
		$('#' + id).empty();
		if (addition != undefined) {
			$('#' + id).append('<option value="-1">全部</option>');
		}
		for (var i = 0; len = domData.length, i < len; i++) {
			$('#' + id).append('<option value="' + domData[i].intsupplierid + '">' + domData[i].strname + '</option>');
		}
		dtd.resolve();
	}
	if ($('#operationpanel').data('suppliertype') != undefined) {
		fillDom($('#operationpanel').data('suppliertype'));
	} else {
		$.ajax({
			url : 'Type/GetSupplierType',
			dataType : 'json',
			type : 'post',
			success : function(data) {
				$('#operationpanel').data('suppliertype', data);
				fillDom(data);
			}
		})
	}
	return dtd.promise();
}

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

$(function() {
	
	$('button.editbook').click(
			function() {
				var rows = $('#datatable_bookinfo').datagrid('getSelections');
				if (rows.length == 0) {
					$.TeachDialog({
						content : '你需要选择一行 !',
						bootstrapModalOption : {},
					});
					return;
				}
				$('#bookEditTable').html("");
				$('#editbookcontainer .tab-content').html("");
				$('#editerrormsg').html("");
				for (var i = 0; i < rows.length; i++) {
					var id = rows[i].intbookid;
					$('#bookEditTable').append('<li role="presentation"><a href="#editpanel' + id + '" role="tab" data-toggle="tab">' + rows[i].strbookname + '</a></li>')
					$('#editbookcontainer .tab-content').append('<div role="tabpanel" class="tab-pane fade" id="editpanel' + id + '"></div>');
					$('#editpanel' + id).html(
							htmltmp.replace('newCode', 'editCode' + id).replace('newName', 'editName' + id).replace('newSN', 'editSN' + id).replace('newBookType', 'editBookType' + id).replace('newPress', 'editPress' + id).replace('newAuthor', 'editAuthor' + id).replace('newPrice', 'editPrice' + id)
									.replace('newDisCount', 'editDisCount' + id).replace('newSupplierType', 'editSupplierType' + id).replace(new RegExp('newform', "gm"), 'editform'));
					setVal(id, rows[i]);
				}
				$('#bookEditTable a:first').tab('show')
				$('#editbookcontainer').slideDown();
				$('#operationpanel').slideUp();

			})
	$('button.submitAdd').click(function() {
		// auth form
		var postdata = {};
		var check = true;
		$('.newform').each(function() {
			if ($(this).val().trim() == "") {
				$('#adderrormsg').html("请输入 " + $(this).prev().html() + "!");
				check = false;
				return false;
			} else {
				postdata[$(this).attr('id')] = $(this).val().trim();
			}
		});
		if (!check) {
			return;
		}
		$.ajax({
			url : 'Book/AddBook',
			dataType : 'json',
			type : 'post',
			data : postdata,
			success : function(response) {
				if (response) {
					$.TeachDialog({
						content : '添加成功，你要继续添加吗 ?',
						showCloseButtonName : 'No',
						otherButtons : [ '确认', '确认 且保持值' ],
						bootstrapModalOption : {},
						CloseButtonAddFunc : function() {
							$('#operationpanel').slideToggle();
							$('#addnewbook').slideToggle();
							$('#datatable_bookinfo').datagrid('reload');
						},
						clickButton : function(sender, modal, index) {
							if (index == 0 || index == 1) {
								if (index == 0) {
									$('.newform').val('');
									$('#newBookType').get(0).selectedIndex = 0;
									$('#newSupplierType').get(0).selectedIndex = 0;
								}
							}
							$('#datatable_bookinfo').datagrid('reload');
							modal.modal('hide');
						}
					});
				} else {
					$.TeachDialog({
						content : '添加失败!',
					});
				}
			}
		})
	});

	$('button.cancelAdd').click(function() {
		$('.newform').val('');
		$('#operationpanel').slideDown();
		$('#addnewbook').slideUp();
	})

	$('button.addbook').click(function() {
		$('.newform').val('');
		initBookType('newBookType', undefined);
		initSupplierType('newSupplierType', undefined);
		$('#adderrormsg').html("");
		$('#operationpanel').slideUp();
		$('#addnewbook').slideDown();

	});

	$('button.cancelEdit').click(function() {
		$('#operationpanel').slideDown();
		$('#editbookcontainer').slideUp();
		$('#bookEditTable').html("");
		$('#editbookcontainer .tab-content').html("");
	})

	$('button.submitEdit').click(function() {
		var idarray = new Array();
		$('[id^=editCode]').each(function() {
			var l = $(this).attr('id');
			idarray.push(l.substring(8, l.length));
		})
		var postdata = {};
		if (idarray.length != 0) {
			var check = true;
			$('.editform').each(function() {
				if ($(this).val().trim() == "") {
					$('#editerrormsg').html("请输入 " + $(this).prev().html() + "!");
					check = false;
					return false;
				} else {
					postdata[$(this).attr('id')] = $(this).val().trim();
				}
			});
			if (!check) {
				return;
			}
			postdata.bookId = idarray;
			$.ajax({
				url : 'Book/EditBook',
				type : 'post',
				dataType : 'json',
				data : postdata,
				success : function(data) {
					if (data) {
						$.TeachDialog({
							content : '编辑成功!',
							CloseButtonAddFunc : function() {
								$('#operationpanel').slideToggle();
								$('#editbookcontainer').slideToggle();
								$('#datatable_bookinfo').datagrid('reload');
							},
						});
					} else {
						$.TeachDialog({
							content : '编辑失败!',
							CloseButtonAddFunc : function() {
								$('#operationpanel').slideToggle();
								$('#editbookcontainer').slideToggle();
								$('#datatable_bookinfo').datagrid('reload');
							},
						});
					}
				}
			})
		}
	})

	$('button.removebook').click(function() {
		var rows = $('#datatable_bookinfo').datagrid('getSelections');
		if (rows.length == 0) {
			$.TeachDialog({
				content : '你至少要选择一行',
				bootstrapModalOption : {},
			});
			return;
		}
		var bookIdArray = new Array(), bookNameArray = new Array();
		for (var i = 0; i < rows.length; i++) {
			bookIdArray.push(parseInt(rows[i].intbookid));
			bookNameArray.push(rows[i].strbookname);
		}
		var sureDialog = function() {
			var dtd = $.Deferred();
			$.TeachDialog({
				content : '你确定要删除书籍 :' + bookNameArray + ' ?',
				showCloseButtonName : '取消',
				otherButtons : [ '确认' ],
				CloseButtonAddFunc : function() {
					dtd.reject();
				},
				clickButton : function(sender, modal, index) {
					if (index == 0) {
						dtd.resolve();
					}
					modal.modal('hide');
				}
			});
			return dtd.promise();
		};
		sureDialog().done(function() {
			$.ajax({
				url : 'Book/RemoveBook',
				type : 'post',
				dataType : 'json',
				data : {
					bookId : bookIdArray
				},
				success : function(response) {
					if (response === true) {
						$.TeachDialog({
							content : '删除成功!',
						})
						$('#datatable_bookinfo').datagrid('reload');
					} else {
						if (!isNaN(response)) {
							var bookname = "";
							for (var i = 0; i < rows.length; i++) {
								if (parseInt(rows[i].intbookid) == parseInt(response)) {
									bookname = rows[i].strbookname;
									break;
								}
							}
							$.TeachDialog({
								content : '删除失败! 书籍:' + bookname + " 被使用中.",
							})
						} else {
							$.TeachDialog({
								content : '删除失败!',
							})
						}
					}
				},
				async : true
			})
		})
	})

	$('#SearchDate').datepicker({
		format : "yyyy-mm-dd",
		todayBtn : "linked",
		autoclose : true,
		todayHighlight : true,
		clearBtn : true
	});
	var psval = $('#datatable_bookinfo').attr('data-size');
	if (psval == undefined || psval == "") {
		psval = 10;
	}
	initBookType('SearchType', "type");
	initSupplierType('SearchSupplier', "type")
	cellwidth = ($(".box-content.table-responsive").width() - 55) / 11;
	$('#datatable_bookinfo').datagrid({
		striped : true,
		remoteSort : false,
		collapsible : true,
		fit : false,
		url : 'Book/GetBooks',
		loadMsg : '请等待数据载入....',
		pagination : true,
		rownumbers : true,
		fitColumns : true,
		pageSize : psval,
		pageList : [ psval, psval * 2, psval * 3, psval * 4, psval * 5 ],
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
	});

	$('#Search').click(function() {
		$('#datatable_bookinfo').datagrid('reload');
	})

});