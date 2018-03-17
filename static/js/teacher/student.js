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
     * 到课按钮
     */
    $("#sure").click(function(){
        var studentid = $("#studentid").val();
        var scheduleid = $("#scheduleid").val();
        var evaluate = $("#evaluate").val();
        var absent = $("#absent").val();
 
        $.ajax({
            data:"studentid="+studentid+"&scheduleid="+scheduleid+"&evaluate="+evaluate+"&absent="+absent,
            url:"../../teacher/lession/arrive",
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
    /**
     * 旷课按钮
     */
    $("#absen").click(function(){
        var studentid = $("#studentid").val();
        var scheduleid = $("#scheduleid").val();
        var evaluate = $("#evaluate").val();
        var absent = $("#absent").val();
 
        $.ajax({
            data:"studentid="+studentid+"&scheduleid="+scheduleid+"&evaluate="+evaluate+"&absent="+absent,
            url:"../../teacher/lession/absent",
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
    })
});


