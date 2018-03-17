$(function() {
    $("#btn").click(function() {
        var username = $("#username").val();
        var password = $("#password").val();

        $.ajax({
            data: "UserName=" + username + "&PassWord=" + password,
            url: "teacher/user/authLogin",
            dataType: "json",
            type: "POST",
            success: function(data) {
                if (data.status == 1) {
                    document.getElementById("head").innerHTML= data.info;
                }
                if(data.status ==2){
                    document.getElementById("head").innerHTML= data.info;
                }
                if(data.status ==0){
                    document.getElementById("head").innerHTML= data.info;
                }
                if(data.status==3){
                    location.reload();
                }
            }
        });
    });
});