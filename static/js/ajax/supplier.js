var htmltmp = $('#newformRange').html();

function setVal(id, obj) {
	$('#editCode' + id).val(obj.intcoding);
	$('#editName' + id).val(obj.strname);
	$('#editAddress' + id).val(obj.straddress);
	$('#editCompanyPhone' + id).val(obj.strcompanyphone);
	$('#editHandlePerson' + id).val(obj.strhandlepersonname);
	$('#editHandlePhone' + id).val(obj.strhandlephone);
	$('#editContactPerson' + id).val(obj.strcontactpersonname);
	$('#editContactPhone' + id).val(obj.strcontactpersonphone);
}
$(function() {
	// initial tablegrid
	var psval = $('#datatable_supplierinfo').attr('data-size');
	if (psval == undefined || psval == "") {
		psval = 10;
	}
	var cellwidth = ($(".box-content.table-responsive").width() - 55) / 10;
	$('#datatable_supplierinfo').datagrid({
		striped : true,
		remoteSort : false,
		collapsible : true,
		fit : false,
		url : 'Supplier/GetAllSuppliers',
		loadMsg : '请等待数据载入.....',
		pagination : true,
		rownumbers : true,
		fitColumns : true,
		pageSize : psval,
		pageList : [ psval, psval * 2, psval * 3, psval * 4, psval * 5 ],
		columns : [ [ {
			field : 'intcoding',
			title : '编号',
			align : 'center',
			sortable : true,
			width : cellwidth,
		}, {
			field : 'strname',
			title : '名称',
			align : 'center',
			width : cellwidth,
			sortable : true
		}, {
			field : 'straddress',
			title : '地址',
			align : 'center',
			width : cellwidth,
			sortable : true,
		}, {
			field : 'strcompanyphone',
			title : '公司号码',
			align : 'center',
			width : cellwidth,
			sortable : true,
		}, {
			field : 'strhandlepersonname',
			title : '负责人',
			align : 'center',
			width : cellwidth,
			sortable : true,
		}, {
			field : 'strhandlephone',
			title : '负责人手机',
			align : 'center',
			width : cellwidth,
			sortable : true
		}, {
			field : 'strcontactpersonname',
			title : '联系人',
			align : 'center',
			width : cellwidth,
			sortable : true
		}, {
			field : 'strcontactpersonphone',
			title : '联系人手机',
			align : 'center',
			width : cellwidth,
			sortable : true,
		}, ] ]
	});

	$('button.addsupplier').click(function() {
		$('.newform').val('');
		$('#adderrormsg').html("");
		$('#operationpanel').slideUp();
		$('#addnewsupplier').slideDown();
	});

	$('button.submitAdd').click(function() {
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
			url : 'Supplier/AddNewSupplier',
			dataType : 'json',
			type : 'post',
			data : postdata,
			success : function(response) {
				if (response) {
					$.TeachDialog({
						content : '添加供应商成功，你需要继续添加吗 ?',
						showCloseButtonName : '取消',
						otherButtons : [ '确认', '确认且保存值' ],
						CloseButtonAddFunc : function() {
							$('#operationpanel').slideToggle();
							$('#addnewsupplier').slideToggle();
							$('#datatable_supplierinfo').datagrid('reload');
						},
						clickButton : function(sender, modal, index) {
							if (index == 0 || index == 1) {
								if (index == 0) {
									$('.newform').val('');
								}
							}
							$('#datatable_supplierinfo').datagrid('reload');
							modal.modal('hide');
						}
					});
				} else {
					$.TeachDialog({
						content : '添加失败!',
					});
				}
			}
		});
	});

	$('button.cancelAdd').click(function() {
		$('#addnewsupplier').slideUp();
		$('#operationpanel').slideDown();
	});

	$('button.editsupplier').click(
			function() {
				var rows = $('#datatable_supplierinfo').datagrid('getSelections');
				if (rows.length == 0) {
					$.TeachDialog({
						content : '你至少需要选择一行!',
						bootstrapModalOption : {},
					});
					return;
				}
				$('#supplierEditTable').html("");
				$('#editsuppliercontainer .tab-content').html("");
				$('#editerrormsg').html("");
				for (var i = 0; i < rows.length; i++) {
					var id = rows[i].intsupplierid;
					$('#supplierEditTable').append('<li role="presentation"><a href="#editpanel' + id + '" role="tab" data-toggle="tab">' + rows[i].strname + '</a></li>')
					$('#editsuppliercontainer .tab-content').append('<div role="tabpanel" class="tab-pane fade" id="editpanel' + id + '"></div>');
					$('#editpanel' + id).html(
							htmltmp.replace('newCode', 'editCode' + id).replace('newName', 'editName' + id).replace('newAddress', 'editAddress' + id).replace('newCompanyPhone', 'editCompanyPhone' + id).replace('newHandlePerson', 'editHandlePerson' + id).replace('newHandlePhone',
									'editHandlePhone' + id).replace('newContactPerson', 'editContactPerson' + id).replace('newContactPhone', 'editContactPhone' + id).replace(new RegExp('newform', "gm"), 'editform'));
					setVal(id, rows[i]);
				}
				$('#supplierEditTable a:first').tab('show')
				$('#editsuppliercontainer').slideDown();
				$('#operationpanel').slideUp();
			});
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
			postdata.supplierId = idarray;
			$.ajax({
				url : 'Supplier/UpdateSuppliers',
				type : 'post',
				dataType : 'json',
				data : postdata,
				success : function(data) {
					if (data) {
						$.TeachDialog({
							content : '编辑供应商成功!',
							CloseButtonAddFunc : function() {
								$('#operationpanel').slideDown();
								$('#editsuppliercontainer').slideUp();
								$('#datatable_supplierinfo').datagrid('reload');
							},
						});
					} else {
						$.TeachDialog({
							content : '编辑供应商失败!',
							CloseButtonAddFunc : function() {
								$('#operationpanel').slideDown();
								$('#editsuppliercontainer').slideUp();
								$('#datatable_supplierinfo').datagrid('reload');
							},
						});
					}
				}
			})
		}
	});
	$('button.cancelEdit').click(function() {
		$('#operationpanel').slideDown();
		$('#editsuppliercontainer').slideUp();
	});
	$('button.removesupplier').click(function() {
		var rows = $('#datatable_supplierinfo').datagrid('getSelections');
		if (rows.length == 0) {
			$.TeachDialog({
				content : '你至少需要选择一行 !',
				bootstrapModalOption : {},
			});
			return;
		}
		var supplierIdArray = new Array();
		var namesArray = new Array();
		for (var i = 0; i < rows.length; i++) {
			supplierIdArray.push(parseInt(rows[i].intsupplierid));
			namesArray.push(rows[i].strname);
		}
		var sureDialog = function() {
			var dtd = $.Deferred();
			$.TeachDialog({
				content : '你确定删除这些供应商 :' + namesArray + ' 吗?',
				showCloseButtonName : 'No',
				otherButtons : [ 'Yes' ],
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
				url : 'Supplier/RemoveSupplier',
				type : 'post',
				dataType : 'json',
				data : {
					supplierId : supplierIdArray
				},
				success : function(response) {
					if (response === true) {
						$.TeachDialog({
							content : '删除供应商成功!',
						});
						$('#datatable_supplierinfo').datagrid('reload');
					} else {
						if (!isNaN(response)) {
							var suppliername = "";
							for (var i = 0; i < rows.length; i++) {
								if (parseInt(rows[i].intsupplierid) == parseInt(response)) {
									suppliername = rows[i].strname;
									break;
								}
							}
							$.TeachDialog({
								content : '删除失败! 供应商:' + suppliername + ' 被使用中.',
							});
						} else {
							$.TeachDialog({
								content : '删除供应商失败!',
							});
						}
					}
				},
				async : true
			})
		})
	});
})