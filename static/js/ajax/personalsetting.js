$(function () {
    // initial date input
    $("#BookGridSize,#UserGridSize,#SupplierGridSize,#PlanGridSize").TouchSpin({
        min: 1,
        max: 50,
        step: 1
    });

    $('#AnimationToggle').bootstrapSwitch('size', 'small');

    $('#AnimationToggle').bootstrapSwitch('onSwitchChange', function (e, data) {
        $('#AnimationToggle').val(data ? 'on' : 'off');
    });
    $('#AnimationToggle').val($(this).attr('data-open') == 1 ? 'on' : 'off');
    $('#AnimationToggle').bootstrapSwitch('state', parseInt($('#AnimationToggle').attr('data-open')) == 1 ? true : false);
    $("#reset").click(function () {
        LoadAjaxContent("ajax/personal_setting");
    })

    $('#savechange').click(function () {
        $('.alert.alert-danger').slideUp();
        $('.alert.alert-danger').remove();
        var Params = new Object();
        var flag = true;
        var error = '<div class="alert alert-danger" role="alert" style="display:none;line-height: 0px;width: 80%;height: 1px;">{errormsg}</div>'
        $('.settingForm').each(function () {
            var param = $(this).val();
            if (param == undefined || param.trim() == "") {
                $(this).parent().parent().next().html(error.replace(/{errormsg}/g, "变量错误!"));
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
            url: 'Config/SavePerChanges',
            dataType: 'json',
            type: 'post',
            data: Params,
            async: true
        }).success(function (data) {
            if (data) {
                $('#animation').attr('data-open', $('#AnimationToggle').val() == 'on' ? 1 : 0);
                $.TeachDialog({
                    content: '更新成功!'
                });
            } else {
                $.TeachDialog({
                    content: '更新失败!'
                });
            }
        });
    });

})