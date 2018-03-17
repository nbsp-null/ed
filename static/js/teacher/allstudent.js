$(function(){
    /**
     * 点名按钮
     */
    $(".btn").click(function(){
        var studentid = this.id;
        document.getElementById("studentid").value = studentid;
        document.getElementById("dialog").style.display = "block";
    });
    /**
     * 取消按钮
     */
    $("#false").click(function(){
        document.getElementById("dialog").style.display = "none";
    });
    /**
     * 评价按钮
     */
    $("#sure").click(function(){
        var studentid = $("#studentid").val();
        var evaluate = $("#evaluate").val();
 
        $.ajax({
            data:"studentid="+studentid+"&evaluate="+evaluate,
            url:"../../teacher/lession/feedback",
            dataType:"JSON",
            type:"POST",
            success:function(data){
                if(data.status==1){
                    document.getElementById("dialog2").style.display = "block";
                }
            }
        });
        document.getElementById("dialog").style.display = "none";
    });
    /*
     * 提示确认按钮
     */
    $("#exit").click(function(){
        document.getElementById("dialog2").style.display = "none";
    });
});


