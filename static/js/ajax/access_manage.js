var setting = {
	check : {
		enable : true,
		chkboxType : {
			"Y" : "p",
			"N" : "ps"
		},
	},
	data : {
		key : {
			checked : "intauthvalue",
			name : "strname",
			url : ""
		},
		simpleData : {
			enable : true,
			idKey : "intpowerid",
			pIdKey : "intparentid",
			rootPId : 0
		},
		view : {
			showLine : true,
			showIcon : true
		}
	},
};
var treeObj, curTreeNode;
function LoadAccessTree() {
	$.ajax({
		url : 'Power/GetUserTypePower',
		dataType : 'json',
		type : 'post',
		data : {
			id : $('#usertype').val()
		}
	}).success(function(data) {
		nodes = $.fn.zTree.init($("#ul_tree"), setting, data);
		treeObj = $.fn.zTree.getZTreeObj("ul_tree");
		var rootnode = treeObj.getNodeByParam("intparentid", 0);
		rootnode.nocheck = true;
		treeObj.updateNode(rootnode);
		treeObj.expandAll(true);
	})
}
$(function() {
	$('.panel-body').css('height', $(window).height() * 0.625 + 'px');
	$.ajax({
		url : 'Type/GetUserTypeAll',
		dataType : 'json',
		type : 'post',
	}).success(function(data) {
		$('#usertype').empty();
		for (var i = 0; len = data.length, i < len; i++) {
			$('#usertype').append('<option value="' + data[i].intidentityid + '">' + data[i].strname + '</option>');
		}
		LoadAccessTree();
	});
	$('.usertype').click(function() {
		LoadAccessTree();
	});
	$('#Update').click(function() {
		var postdata = {};
		var powerId = new Array();
		var changes = treeObj.getChangeCheckedNodes();
		if (changes.length > 0) {
			for (var i = 0; i < changes.length; i++) {
				powerId.push(changes[i].intpowerid);
				postdata["value" + changes[i].intpowerid] = changes[i].intauthvalue;
			}
			postdata.powerId = powerId;
			$.ajax({
				url : 'Power/UpdateUserTypePower',
				dataType : 'json',
				type : 'post',
				data : postdata,
			}).success(function() {
				$.TeachDialog({
					content : '更新 ' + $('#usertype').find("option:selected").text() + ' 权限成功!',
				});
			})
		} else {
			$.TeachDialog({
				content : '无改变!',
			});
		}

	});
	$('#Reset').click(function() {
		LoadAccessTree();
	});
	$('#usertype').change(function() {
		LoadAccessTree();
	})

})