function unix2human(unixtime) {
    var dateObj = new Date(unixtime);
    var UnixTimeToDate = dateObj.getFullYear() + '-' + (dateObj.getMonth() + 1) + '-' + dateObj.getDate() + ' ' + p(dateObj.getHours()) + ':' + p(dateObj.getMinutes()) + ':' + p(dateObj.getSeconds());
    return UnixTimeToDate;
}
function p(s) {
    return s < 10 ? '0' + s : s;
}

var error = '<div class="alert alert-danger" role="alert" style="display:none;line-height: 0px;width: 80%;height: 1px;">{errormsg}</div>'
var hander = {
    action: {
        SetFailed: function(domId) {
            var Opdom = $('#' + domId).parent();
            Opdom.removeClass('has-success').removeClass('has-error').addClass('has-error').addClass("animated-fast shake").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                $(this).removeClass('animated-fast shake');
            })
        },
        SetSucccess: function(domId) {
            var Opdom = $('#' + domId).parent();
            Opdom.removeClass('has-success').removeClass('has-error').addClass('has-success');
        }
    }
}
function delShakeClass(domId) {
    $('#' + domId).parent().removeClass('animated shake');
}
function returntimeer(domId) {
    return function() {
        delShakeClass(domId);
    };
}


function backgroundToggle(flag) {
    if (!flag) {
        $('body').css('cssText', 'background-color:#353535 ! important');
    } else {
        $('body').css('cssText', 'background-image:url(https://unsplash.it/' + $(this).width() + '/' + $(this).height() + '?random) ; background-repeat: no-repeat; background-attachment: fixed; background-position: center 0; background-size: cover;');
    }
}
$(function() {

    $('#loginModal').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
        $(this).removeClass('animated fadeInDown');
    })

    $.getScript("static/plugins/pnotify/pnotify.custom.min.js", function() {
  
    });

    NProgress.configure({
        ease: 'ease',
        speed: 1000
    });

    $(document).ajaxStart(function() {
        NProgress.start();
    });
    $(document).ajaxStop(function() {
        NProgress.done();
    });
    $('a.UserAccount').click(function() {
        account();
    })
    $('#toggleBackGround').bootstrapSwitch("size", "mini");

    $('#toggleBackGround').bootstrapSwitch('onSwitchChange', function(e, data) {
        backgroundToggle(data);
    });


    $('#loginButton').click(function() {

        $('.preloader').fadeToggle("slow");
        var userName = $('#loginUserName').val();
        var mark = true;
        if (userName == "" || userName.trim() == "") {
            document.getElementById("errorTipMsg").style.display = "none";
            document.getElementById("nulln").style.display = "inline";
            return;
        } else {
            document.getElementById("nulln").style.display = "none";
        }
        var passWord = $('#loginPassWord').val();
        if (passWord == "" || passWord.trim() == "") {
            document.getElementById("errorTipMsg").style.display = "none";
            document.getElementById("nullp").style.display = "inline";
            return;
        } else {
            document.getElementById("nullp").style.display = "none";

        }

        if (!mark) {
            $(this).button('reset');
            $('.preloader').fadeToggle("slow");
            return;
        }

        $.ajax({
            url: "admin/Admin/authLogin",
            type: 'post',
            data: {
                UserName: userName,
                PassWord: passWord,
            },
            complete: function(data) {
            },
            success: function(data) {
                if (data == "1") {
                    $("#loginButton").button('loading');
                    location.href = "";
                    return;
                } else {
                    document.getElementById("errorTipMsg").style.display = "inline";
                }
            },

            async: true
        });
        $(this).button('reset');
    });





    $('.passwordc.input-group-addon.glyphicon').click(function() {
        var ret = $(this).hasClass('glyphicon-eye-open');
        var str = 'text';
        if (ret)
            str = "password";
        $(this).prev().attr('type', str);
        if (ret) {
            $(this).removeClass('glyphicon-eye-open');
            $(this).addClass('glyphicon-eye-close');
        } else {
            $(this).removeClass('glyphicon-eye-close');
            $(this).addClass('glyphicon-eye-open');
        }
    });


    $('#resetButton').click(function() {
        $('#loginPassWord').val('');
        $('#loginUserName').val('');
    })

    $('#loginPassWord').keypress(function(e) {
        if (e.which == 13) {
            $('#loginButton').trigger('click');
        }
    })
    $('#UserName').blur(function() {
        authUserRepeat();
    })

})
function authUserRepeat() {
    var userName = $('#UserName').val();
    if (userName == "" || userName.trim() == "") {
        return;
    }
    $.ajax({
        url: 'User/AuthUserName',
        type: 'post',
        data: {
            UserName: userName
        },
        dataType: 'json',
        complete: function(data) {
        },
        success: function(data) {
            if (data != null) {
                if (data) {
                    hander.action.SetSucccess('UserName');
                } else {
                    hander.action.SetFailed('UserName');
                }
            } else {
                hander.action.SetFailed('UserName');
            }
        },
        error: function(data) {
            console.debug(data.status);
        },
        async: true
    });
}
function selectchange() {
    var fillDom = function(domData) {
        $('#Majors').empty();
        for (var i = 0, len = domData.length; i < len; i++) {
            $('#Majors').append('<option value="' + domData[i].intid + '">' + domData[i].strname + '</option>');
        }
    }
    if ($('#registerModal').data('department' + $('#DepartMent').val()) == undefined) {
        $.ajax({
            url: 'Type/GetDepartMent',
            type: 'post',
            dataType: 'json',
            data: {
                id: $('#DepartMent').val()
            },
            complete: function(data) {
            },
            success: function(data) {
                if (data != null) {
                    if (data != null) {
                        $('#registerModal').data('department' + $('#DepartMent').val(), data);
                        fillDom(data);
                    }
                } else {
                    $.TeachDialog({
                        content: '获取专业信息失败!'
                    });
                }

            },
            async: true
        });
    } else {
        fillDom($('#registerModal').data('department' + $('#DepartMent').val()));
    }

}