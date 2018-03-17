$(function() {
    $("#personal").click(function() {
        location.href = "teacher/user/personal";
    });
    $("#notice").click(function() {
        location.href = "teacher/notice/notice";
    });
    $("#course").click(function() {
      
      location.href="teacher/lession/schedule";
    });
    $("#logout").click(function(){
        $.ajax({
            url:"teacher/user/authLogout",
            success:function(){
               location.href="teacher" ;
            }
        });
        
    });
});