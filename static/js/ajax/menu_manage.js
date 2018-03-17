var setting = {
	data : {
		key : {
			name : "menu_name"
		},
		simpleData : {
			enable : true,
			idKey : "id",
			pIdKey : "parent_menuid",
			rootPId : 0
		},
		view : {
			showLine : true,
			showIcon : true
		}
	},
	callback : {
		onClick : treeClick
	}
};
var treeObj, curTreeNode;
function LoadMenuTree() {
	$.ajax({
		url : 'admin/Menu/tree',
		dataType : 'json',
		type : 'post',
	}).success(function(data) {
		nodes = $.fn.zTree.init($("#ul_tree"), setting, data);
		treeObj = $.fn.zTree.getZTreeObj("ul_tree");
		treeObj.expandAll(true);
		curTreeNode = null;
	})
}
function treeClick(event, treeId, treeNode, clickFlag) {
	curTreeNode = treeNode;
	$('#menuname').val(treeNode.menu_name);
	$('#menuicon').val(treeNode.menu_iconclass);
}
$(function() {
	LoadMenuTree();
	$('#refresh').click(function() {
		LoadMenuTree();
	})
	$('#Update').click(function() {
		if (curTreeNode == null) {
			$.TeachDialog({
				content : '请至少选择一行!',
			});
			return;
		}
		if (curTreeNode.intmenuid == 1) {
			$.TeachDialog({
				content : '不能编辑根节点!',
			});
			return;
		}
		var menuname = $('#menuname').val();
		var icon = $('#menuicon').val();
		$.ajax({
			url : "admin/Menu/update",
			type : 'post',
			dataType : 'json',
			data : {
				menu : curTreeNode.id,
				menuname : menuname,
				menuicon : icon
			},
			success : function(response) {
				if (response) {
					curTreeNode.menu_name = menuname;
					curTreeNode.menu_iconclass = icon;
					curTreeNode.iconSkin = icon + " iconskin";
					treeObj.updateNode(curTreeNode);
					if (curTreeNode.parent_menuid == 1 && curTreeNode.id != 2) {
						$("[href='" + curTreeNode.menu_value + "'] span").html(menuname);
					} else {
						var html = $("[href='" + curTreeNode.menu_value + "']").html();
						$("[href='" + curTreeNode.menu_value + "']").html(html.replace(/<\/i>(.*)/, '<\/i>' + menuname))

					}
					$("[href='" + curTreeNode.menu_value + "'] i").attr('class', icon);
					var menus = [];
					$("[class~='ajax-link'][href!='#']").each(function(e) {
						var menu = {};
						menu.data = $(this).attr('href');
						menu.value = $.trim($(this).html().replace(/<i(.*)<\/i>/, ""));
						menus.push(menu);
					})
					$('#searchMenu').autocomplete().setOptions({
						lookup : menus
					});
					$.TeachDialog({
						content : '更新成功!',
					});
				} else {
					$.TeachDialog({
						content : '更新失败!',
					});
				}
			},
			async : true
		})

	})
})