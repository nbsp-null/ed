var booksetting = {
	data : {
		key : {
			name : "strbooktypename",
			url : ""
		},
		simpleData : {
			enable : true,
			idKey : "intbooktypeid",
			rootPId : 0
		},
		view : {
			showLine : true,
			showIcon : true
		}
	},
	callback : {
		onClick : bookTreeonClick
	}
};
var curBookTreeNode = null,bookTreeObj;
function bookTreeonClick(event, treeId, treeNode, clickFlag) {
	curBookTreeNode = treeNode;
	$('#book_editname').val(curBookTreeNode.strbooktypename);
}
function loadBookType() {
	$.ajax({
		url : "Type/GetBookTypeForType",
		type : 'post',
		dataType : 'json',
		success : function(response) {
			nodes = $.fn.zTree.init($("#ul_tree_booktype"), booksetting, response);
			bookTreeObj = $.fn.zTree.getZTreeObj("ul_tree_booktype");
			bookTreeObj.expandAll(true);
			curBookTreeNode = null;
		},
		async : true,
	});
}

$(function() {
	// initial icheck
	if ($("#ul_tree_booktype").length > 0) {
		loadBookType();
	}
	$('.booktype').click(function() {
		loadBookType();
	})

	$('#book_edit').click(function() {
		var name = $('#book_editname').val().trim();
		if (curBookTreeNode == null) {
			$.TeachDialog({
				content : '请至少选择一行!',
			});
			return;
		}
		if (name == "" || name == undefined) {
			$.TeachDialog({
				content : '请输入一个名称!',
			});
			return;
		}
		$.ajax({
			url : "TypeOperate/UpdateBookType",
			type : 'post',
			dataType : 'json',
			data : {
				id : curBookTreeNode.intbooktypeid,
				name : name
			},
			success : function(response) {
				if (response) {
					curBookTreeNode.strbooktypename = name;
					bookTreeObj.updateNode(curBookTreeNode);
					$.TeachDialog({
						content : '更新书籍类型成功!',
					});
				} else {
					$.TeachDialog({
						content : '更新书籍类型失败!',
					});
				}
			},
			async : true
		})
	});
	$('#book_insert').click(function() {

		var name = $('#book_insertname').val().trim();

		if (name == "" || name == undefined) {
			$.TeachDialog({
				content : '请输入一个名称!',
			});
			return;
		}
		$.ajax({
			url : "TypeOperate/InsertBookType",
			type : 'post',
			dataType : 'json',
			data : {
				name : name
			},
			success : function(response) {
				if (!isNaN(response)) {
					bookTreeObj.addNodes(null, {
						intbooktypeid : parseInt(response),
						strbooktypename : name
					});
					$.TeachDialog({
						content : '新增成功!',
					});
				} else {
					$.TeachDialog({
						content : '新增失败!',
					});
				}
			},
			async : true
		})
	});

	$('#book_delete').click(function() {
		if (curBookTreeNode == null) {
			$.TeachDialog({
				content : '请至少选择一行!',
			});
			return;
		}
		$.ajax({
			url : "TypeOperate/DeleteBookType",
			type : 'post',
			dataType : 'json',
			data : {
				id : curBookTreeNode.intbooktypeid
			},
			success : function(response) {
				if (response) {
					bookTreeObj.removeNode(curBookTreeNode);
					$.TeachDialog({
						content : '删除书籍类型成功!',
					});
				} else {
					$.TeachDialog({
						content : '删除书籍类型失败！',
					});
				}
			},
			async : true
		})

	});
})
