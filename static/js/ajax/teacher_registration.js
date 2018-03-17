/**
 * ajax 获得年级信息
 * @constructor
 */
function GradeList(){
    $.ajax({
        url : 'admin/Teacher/getGrade',
        dataType : 'JSON',
        success:function(data){
            $("#grade_id").empty();
            $("#grade_id").append("<option value=''>选择年级</option>");
            $.each(data, function(i,d){
                $("#grade_id").append('<option value="' + d.id + '">' + d.grade_name + '</option>');
            });
        },
        error:function () {
            $.TeachDialog({
                content: '加载年级数据失败',
            });
        }
    });
}

/**
 * ajax 获得科目信息
 * @constructor
 */
function SubjectList(){
    $.ajax({
        url : 'admin/Subject/getJSON',
        dataType : 'JSON',
        success:function(data){
            $("#subject_id").empty();
            $("#subject_id").append("<option value=''>选择科目</option>");
            $.each(data, function(i,d){
                $("#subject_id").append('<option value="' + d.id + '">' + d.subject_name + '</option>');
            });
        },
        error:function () {
            $.TeachDialog({
                content: '加载科目数据失败',
            });
        }
    });
}

$(function () {
    /**
     * ajax 数据加载
     */
	GradeList();
	SubjectList();

    /**
     * 提交学生报名信息
     */
    $("#Submit").click(function () {
        //

        $.ajax({
            url: 'admin/Teacher/insert',
            data: $("#listForm").serialize(),
            type: 'POST',
            dataType:'JSON',
            success: function (returnData) {
                if (returnData.status ==0) {
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
                            document.getElementById('listForm').reset();
                        },
                        otherButtons: ['去往管理页'],
                        otherButtonStyles: ['btn-primary'],
                        clickButton: function (sender, modal, index) {
                            $.ajax({
                                url:'admin/Ajax/teacher_manage',
                                dataType:'HTML',
                                success:function(data){
                                    modal.modal('hide');
                                   $('#teacherGoManage').html(data);
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