    function updateName(){
        var data = $('#name').text();
        location.href = '../../student/user/updateData?title=名字&name=student_name&content='+data;
    }
    function updateSex(){
        var data = $('#sex').text();
        location.href = '../../student/user/updateData?title=性别&name=student_sex&content='+data;
    }
    function updatePhone(){
        var data = $('#phone').text();
        location.href = '../../student/user/updateData?title=联系电话&name=student_phone&content='+data;
    }
    function updateSchool(){
        var data = $('#school').text();
        location.href = '../../student/user/updateData?title=毕业学校&name=student_school&content='+data;
    }
    function updatePassword(){
        var data = $('#username').val();
        location.href = '../../student/user/modifyData';
    }