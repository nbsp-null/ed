/**
 * ajax 获得课程信息
 * @constructor
 */
function getCourseJSON() {
    $.ajax({
        url: 'admin/Course/getCourseJSON',
        dataType: 'JSON',
        success: function (data) {
            $("#select_courseid").empty();
            $("#select_courseid").prepend("<option value=''>请选择课程</option>");
            for (var i = 0; i < data.length; i++) {
                $("#select_courseid").append('<option value="' + data[i].id + '">' + data[i].course_name + '</option>');
            }
        },
        error: function () {
            $.TeachDialog({
                content: '加载课程数据失败',
            });
        }
    });
}
/**
 *ajax 获得年级信息
 *
 */
function getGradeJSON() {
    $.ajax({
        url: 'admin/Grade/getJSON',
        dataType: 'JSON',
        success: function (data) {
            $("#select_gradeid").empty();
            $("#select_gradeid").prepend("<option value=''>请选择年级</option>");
            for (var i = 0; i < data.length; i++) {
                $("#select_gradeid").append('<option value="' + data[i].id + '">' + data[i].grade_name + '</option>');
            }
        },
        error: function () {
            $.TeachDialog({
                content: '加载年级数据失败',
            });
        }
    });
}
/**
 * ajax 获得班级信息
 * @constructor
 */
function getClassJSON() {
    $.ajax({
        url: 'admin/Classes/getClassesJSON',
        dataType: 'JSON',
        success: function (data) {
            $("#select_classid").empty();
            $("#select_classid").prepend("<option value=''>请选择班级</option>");
            for (var i = 0; i < data.length; i++) {
                $("#select_classid").append('<option value="' + data[i].id + '">' + data[i].classes_name + '</option>');
            }
        },
        error: function () {
            $.TeachDialog({
                content: '加载班级数据失败',
            });
        }
    });
}
// /**
//  *
//  *获得校区信息
//  */
//
// function getCampusJSON(){
// 	$.ajax({
// 		url:'admin/student/getCampusJSON',
// 		dataType:'JSON',
// 		success:function(data){
// 			$("#select_campusid").empty();
// 			$("#select_campusid").prepend("<option value=''>请选择年级</option>");
// 			for(var i=0;i<data.length;i++){
// 				$("#select_campusid").append('<option value="'+data[i].id+'">'+data[i].campus_name+'</option>');
// 			}
// 		},
// 		error:function(){
// 			$.TeachDialog({
// 				content:'校区加载失败',
// 			});
// 		}
// 	})
// }

$(function () {
    /**
     * ajax 数据加载
     */
    getCourseJSON();
    getClassJSON();
	getGradeJSON();

    /**
     * 提交学生报名信息
     */
    $("#Submit").click(function () {
        $.ajax({
            url: 'admin/Student/insert',
            data: $("#studentForm").serialize(),
            type: 'POST',
            dataType: 'JSON',
            success: function (returnData) {
                console.info("success");
                if (returnData.status == 0) {
                    $.TeachDialog({
                        content: returnData.msg,
                    });
                } else {
                    $.TeachDialog({
                        title:"<h3>系统信息<h3>",
                        content: returnData.msg,
                        showCloseButton: true,
                        showCloseButtonName: '继续添加',
                        CloseButtonAddFunc: function () {
                            document.getElementById('studentForm').reset();
                            getCourseJSON();
                            getClassJSON();
                            getGradeJSON();
                        },
                        otherButtons: ['去往管理页'],
                        otherButtonStyles: ['btn-primary'],
                        clickButton: function (sender, modal, index) {
                            $.ajax({
                                url:'admin/Ajax/student_manage',
                                dataType:'HTML',
                                success:function(data){
                                    modal.modal('hide');
                                    $('#studentGoManage').html(data);
                                }

                            });
                        }
                    });
                }
            },
            error: function () {
                $.TeachDialog({
                    content: '添加失败',
                });
            }
        });
    });
});