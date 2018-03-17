$("#Search").click(function(){
    $.ajax({
        url:"admin/config/update",
        type:"POST",
        data:$("#updateForm").serialize(),
        dataType:"JSON",
        success:function(data){
            if(data.status == 1){
                $.TeachDialog({
                    content:data.msg
                })
                location.reload();

            }else{
                $.TeachDialog({
                    content:data.msg
                })
            }
        },
        error:function(){
            $.TeachDialog({
                content:"系统错误，请联系管理员"
            })
        }
    })
})
$("[name='my-checkbox']").bootstrapSwitch({
    onText:"已开启",
    offText:"已关闭",
    onColor:"primary",
    offColor:"info",
    size:"normal",
    onSwitchChange:function(event,state){
        if(state==true){
            $(this).val('1');
        }else{
            $(this).val('0');
        }
        $.ajax({
            url:"admin/config/updateAnimation",
            data:"value="+$(this).val(),
            type:"POST",
            dataType:"JSON",
            success:function(result){
                if (result.status == 1) {
                    $.TeachDialog({
                        content:result.msg
                    });
                } else {
                    $.TeachDialog({
                        content: '修改失败'
                    });
                }
            },
            error: function () {
                $.TeachDialog({
                    content: '系统异常，请联系管理员'
                });
            }
        })
    }
});