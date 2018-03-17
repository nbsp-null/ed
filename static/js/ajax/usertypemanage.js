var usersetting = {
	data : {
		key : {
			name : "strname",
			url : ""
		},
		simpleData : {
			enable : true,
			idKey : "intidentityid",
			rootPId : -1
		},
		view : {
			showLine : true,
			showIcon : true
		}
	},
	callback : {
		onClick : userTreeonClick
	}
};
var curUserTreeNode = null, userTreeObj;
function userTreeonClick(event, treeId, treeNode, clickFlag) {
	curUserTreeNode = treeNode;
	console.log(curUserTreeNode)
	$('#user_editname').val(curUserTreeNode.strname);
	if (curUserTreeNode.intallowreg == 1) {
		$('#regcheck').iCheck('check');
	} else {
		$('#regcheck').iCheck('uncheck');
	}

}

function loadUserType() {
	$.ajax({
		url : "Type/GetUserTypeAllForType",
		type : 'post',
		dataType : 'json',
		success : function(response) {
			nodes = $.fn.zTree.init($("#ul_tree_usertype"), usersetting, response);
			userTreeObj = $.fn.zTree.getZTreeObj("ul_tree_usertype");
			userTreeObj.expandAll(true);
			curUserTreeNode = null;
		},
		async : true,
	});
}
$(function() {
	// initial icheck
	$('#regcheck').iCheck({
		checkboxClass : 'icheckbox_square-blue',
	})
	$('#insertcheck').iCheck({
		checkboxClass : 'icheckbox_square-blue',
	})
	$('.icheckbox_square-blue').css('margin-top', '5px');

	if ($("#ul_tree_usertype").length > 0) {
		loadUserType();
	}
	$('.usertype').click(function() {
		loadUserType();
	})
	$('#user_edit').click(function() {
		var name = $('#user_editname').val().trim();

		if (curUserTreeNode == null) {
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
		var allregcheck = $('#regcheck').parent().hasClass('checked') ? 1 : 0;
		$.ajax({
			url : "TypeOperate/UpdateUserType",
			type : 'post',
			dataType : 'json',
			data : {
				id : curUserTreeNode.intidentityid,
				allowreg : allregcheck,
				name : name
			},
			success : function(response) {
				if (response) {
					curUserTreeNode.strname = name;
					curUserTreeNode.intallowreg = allregcheck;
					userTreeObj.updateNode(curUserTreeNode);
					$.TeachDialog({
						content : '更新用户类型成功!',
					});
				} else {
					$.TeachDialog({
						content : '更新用户类型失败!',
					});
				}
			},
			async : true
		})
	});

	$('#user_insert').click(function() {
		var name = $('#user_insertname').val().trim();

		if (name == "" || name == undefined) {
			$.TeachDialog({
				content : '请输入名称!',
			});
			return;
		}
		$.ajax({
			url : "TypeOperate/InsertUserType",
			type : 'post',
			dataType : 'json',
			data : {
				allowreg : $('#insertcheck').parent().hasClass('checked') ? 1 : 0,
				name : name
			},
			success : function(response) {
				if (!isNaN(response)) {
					userTreeObj.addNodes(null, {
						intidentityid : parseInt(response),
						strname : name
					});
					$.TeachDialog({
						content : '新增用户类型成功!',
					});
				} else {
					$.TeachDialog({
						content : '新增用户类型失败!',
					});
				}
			},
			async : true
		})
	});

	$('#user_delete').click(function() {
		if (curUserTreeNode == null) {
			$.TeachDialog({
				content : '请至少选择一行!',
			});
			return;
		}
		$.ajax({
			url : "TypeOperate/DeleteUserType",
			type : 'post',
			dataType : 'json',
			data : {
				id : curUserTreeNode.intidentityid,
			},
			success : function(response) {
				if (response) {
					treeObj.removeNode(curUserTreeNode);
					$.TeachDialog({
						content : '删除用户类型成功!',
					});
				} else {
					$.TeachDialog({
						content : '删除用户类型失败!',
					});
				}
			},
			async : true
		})
	});
	$("[data-toggle='tooltip']").tooltip();
	$("[data-toggle='tooltip']").click(function() {
		LoadAjaxContent('ajax/access_manage');
	});
})
