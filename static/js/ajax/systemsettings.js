/**
 * Created by teddyzhu on 2015/4/1.
 */
$(function() {
	$.ajax({
		url : 'Config/GetDialogClass',
		dataType : 'json',
		type : 'post',
		async : true
	}).success(function(data) {
		$('#DialogClassAnimation').empty();
		for (var i = 0, len = data.length; i < len; i++) {
			$('#DialogClassAnimation').append('<option value="' + data[i] + '">' + data[i] + '</option>')
		}
		$('#DialogClassAnimation').val($('#DialogClassAnimation').attr('data-value'));
	});

	$('#planSubmitToggle').bootstrapSwitch('size', 'small');

	$('#planSubmitToggle').bootstrapSwitch('onSwitchChange', function(e, data) {
		$('#planSubmitToggle').val(data ? 'on' : 'off');
	});
	$('#planSubmitToggle').val($('#planSubmitToggle').attr('data-open') == 1 ? 'on' : 'off');
	$('#planSubmitToggle').bootstrapSwitch('state', parseInt($('#planSubmitToggle').attr('data-open')) == 1 ? true : false);

	$('#diglogClassTest').on('click', function() {
		$.TeachDialog({
			animation : $('#DialogClassSpeed').val() + ' ' + $('#DialogClassAnimation').val(),
			content : '这是一个动画测试弹出框',
			bootstrapModalOption : {}
		});
	})
	$('#DialogClassSpeed').val($('#DialogClassSpeed').attr('data-value'));
	$('#savechange').click(function() {
		$('.alert.alert-danger').slideUp();
		$('.alert.alert-danger').remove();
		var Params = new Object();
		var flag = true;
		var error = '<div class="alert alert-danger" role="alert" style="display:none;line-height: 0px;width: 80%;height: 1px;">{errormsg}</div>'
		$('.settingForm').each(function() {
			var param = $(this).val();
			if (param == undefined || param.trim() == "") {
				$(this).parent().parent().next().html(error.replace(/{errormsg}/g, "参数错误!"));
				flag = false;
				return flag;
			} else {
				Params[$(this).attr('id')] = param;
			}
		});
		if (!flag) {
			$('.alert.alert-danger').slideDown();
			return;
		}
		$.ajax({
			url : 'Config/SaveGlobalChanges',
			dataType : 'json',
			type : 'post',
			data : Params,
			async : true
		}).success(function(data) {
			if (data) {
				$('#dialoganimation').html($('#DialogClassSpeed').val() + ' ' + $('#DialogClassAnimation').val());
				$.TeachDialog({
					content : '更新设置成功!'
				});

			} else {
				$.TeachDialog({
					content : '更新设置失败!'
				});
			}
		});
	});

})