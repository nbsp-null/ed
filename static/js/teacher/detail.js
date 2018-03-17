$(function(){
   $("#start").click(function(){
        document.getElementById("dialog").style.display = "block";
   });
   $("#false").click(function(){
       document.getElementById("dialog").style.display = "none";
   })
   /**
    * 开始上课提交数据
    */
   $("#sure").click(function(){
       var scheduleid = $("#scheduleid").val();
       var content = $("#schedule_content").val();
       var remark = $("#schedule_remark").val();
       
       $.ajax({
           data:"scheduleid=" + scheduleid + "&content=" + content + "&remark=" + remark,
           url:"../../teacher/lession/start",
           dataType: "json",
           type: "POST",
           success:function(data){
               alert(data.info)
           }
           
       });
       document.getElementById("dialog").style.display = "none";
   });
});


