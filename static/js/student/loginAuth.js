$(function() {
    $("#btn").click(function() {
        var username = $("#username").val();
        var password = $("#password").val();
        if(username=="" || username.trim()==''){
            $('#head').html('用户名不能为空');
            return;
        }
        if(password==""){
            $('#head').html('密码不能为空');
            return;
        }
        $.ajax({
            data: "UserName=" + username + "&PassWord=" + password,
            url: "student/user/loginValidate",
            dataType: "JSON",
            type: "POST",
            success: function(data) {
                if (data.status == 0) {
                    $('#head').html(data.info);
                    return;
                }else{
                    location.reload();
                }
            }
        });
    });
});