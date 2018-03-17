//
//    Main script of DevOOPS v1.0 Bootstrap Theme
//
"use strict";

jQuery.DownloadFile = function (parameters) {
    var url = parameters.url;
    var data = parameters.data;
    var method = parameters.method
    if (url && data) {
        data = typeof data == 'string' ? data : jQuery.param(data);
        var inputs = '';
        jQuery.each(data.split('&'), function () {
            var pair = this.split('=');
            inputs += '<input type="hidden" name="' + pair[0] + '" value="' + pair[1] + '" />';
        });
        jQuery('<form style="display:none;" action="' + url + '" method="' + (method || 'post') + '">' + inputs + '</form>').appendTo('body').submit().remove();
    }
}

function LoadJS(url) {
    var dtd = $.Deferred();
    $.getScript(url, function () {
        dtd.resolve();
    })
    return dtd.promise();
}

function LoadJsFile(urls) {
    if (!$.isArray(urls)) {
        return LoadJS(urls);
    }
    var ret = [];
    for (var i = 0, len = urls.length; i < len; i++) {

        ret[i] = LoadJS(urls[i]);
    }
    return $.when.apply($, ret);
}
function LoadLeafletScript(callback) {
    if (!$.fn.L) {
        $.getScript('resources/plugins/leaflet/leaflet.js', callback);
    } else {
        if (callback && typeof (callback) === "function") {
            callback();
        }
    }
}
String.prototype.trimEnd = function (trimStr) {
    if (!trimStr) {
        return this;
    }
    var temp = this;
    while (true) {
        if (temp.substr(temp.length - trimStr.length, trimStr.length) != trimStr) {
            break;
        }
        temp = temp.substr(0, temp.length - trimStr.length);
    }
    return temp;
};
function unix2human(unixtime) {
    var dateObj = new Date(unixtime);
    var UnixTimeToDate = dateObj.getFullYear() + '-' + (dateObj.getMonth() + 1) + '-' + dateObj.getDate() + ' ' + p(dateObj.getHours()) + ':' + p(dateObj.getMinutes()) + ':' + p(dateObj.getSeconds());
    return UnixTimeToDate;
}
function p(s) {
    return s < 10 ? '0' + s : s;
}

function LoadBootstrapValidatorScript(callback) {
    if (!$.fn.bootstrapValidator) {
        $.getScript('resources/plugins/bootstrapvalidator/bootstrapValidator.min.js', callback);
    } else {
        if (callback && typeof (callback) === "function") {
            callback();
        }
    }
}

function LoadDataTablesScripts(callback) {
    function LoadDatatables() {
        $.getScript('resources/plugins/datatables/jquery.dataTables.min.js', function () {
            $.getScript('resources/plugins/datatables/ZeroClipboard.min.js', function () {
                $.getScript('resources/plugins/datatables/dataTables.tableTools.min.js', function () {
                    $.getScript('resources/plugins/datatables/dataTables.bootstrap.min.js', callback);
                });
            });
        });
    }

    if (!$.fn.dataTables) {
        LoadDatatables();
    } else {
        if (callback && typeof (callback) === "function") {
            callback();
        }
    }
}

function LoadSpringyScripts(callback) {
    function LoadSpringyScript() {
        $.getScript('resources/plugins/springy/springy.js', LoadSpringyUIScript);
    }

    function LoadSpringyUIScript() {
        $.getScript('resources/plugins/springy/springyui.js', callback);
    }

    if (!$.fn.Springy) {
        LoadSpringyScript();
    } else {
        if (callback && typeof (callback) === "function") {
            callback();
        }
    }
}

function logout() {
    $.TeachDialog({
        content: '你确定要退出吗?',
        showCloseButton: true,
        bootstrapModalOption: {},
        otherButtons: ['确定'],
        clickButton: function (sender, modal, index) {
            if (index == 0) {
                $.ajax({
                    url: "admin/Admin/authLogout",
                    type: 'post',
                    async: false,
                    success: function (r) {
                        var ref = "";
                        if (r == "1") {
                            if (window.location.href.indexOf('ref=') != -1) {
                                ref = window.location.href.replace(/(.*?)ref=\//, '');
                            } else if (window.location.href.indexOf('?campusId=') != -1) {
                                ref = window.location.href.substring(0, window.location.href.indexOf('?campusId='));
                            }
                            window.location.href = ref;

                        }
                    }
                })
            }
        }
    });
}
function sessionout() {
    $.TeachDialog({
        content: '你的会话过期了，请重新登录!',
        dialogHidden: function () {
            window.top.location.href = '/?ref=' + window.top.location.pathname + window.top.location.hash;
        },
        bootstrapModalOption: {}
    });
}

function MessagesMenuWidth() {
    var W = window.innerWidth;
    var W_menu = $('#sidebar-left').outerWidth();
    var w_messages = (W - W_menu) * 16.666666666666664 / 100;
    $('#messages-menu').width(w_messages);
}

function LoadFancyboxScript(callback) {
    if (!$.fn.fancybox) {
        $.getScript('resources/plugins/fancybox/jquery.fancybox.js', callback);
    } else {
        if (callback && typeof (callback) === "function") {
            callback();
        }
    }
}

function LoadAjaxContent(url) {
    $('.preloader').show();
    //noinspection JSDuplicatedDeclaration
    $.ajax({
        mimeType: 'text/html; charset=utf-8',
        url: url,
        type: 'GET',
        dataType: "html",
        success: function (data) {
            $('.preloader').hide();
            $('#ajax-content').html(data);
            if (parseInt($('#animation').attr('data-open')) == 1) {
                $('#ajax-content').addClass('animated fadeInRight');
                $('#ajax-content').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    $(this).removeClass('animated fadeInRight');
                })
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
        },
        complete: function (xhr, textStatus) {
            if (xhr.status == 3389) {
                sessionout();
                return;
            }
        },
        async: false
    });
}

function LoadModel(modelName) {
    var dtd = $.Deferred();
    if (modelName == undefined || modelName.trim() == "") {
        dtd.reject();
    }
    if ($('.box-content.table-responsive').data(modelName) != undefined) {
        dtd.resolve($('.box-content.table-responsive').data(modelName));
    } else {
        $.ajax({
            mimeType: 'text/html; charset=utf-8',
            url: 'Model/' + modelName,
            type: 'POST',
            dataType: "html",
            async: false
        }).success(function (data) {
            $('.box-content.table-responsive').data(modelName, data);
            dtd.resolve(data);
        }).fail(function () {
            dtd.reject();
        })
    }
    return dtd;
}
function SetMinBlockHeight(elem) {
    elem.css('min-height', window.innerHeight - 49)
}

function DashboardTabChecker() {
    $('#content').on('click', 'a.tab-link', function (e) {
        e.preventDefault();
        var attr = $(this).attr('id');
        $('div#dashboard_tabs').find('div[id^=dashboard]').each(function () {
            if ($(this).attr('id') != "dashboard-" + attr) {
                $(this).slideUp();
            }
        });
        $('#' + 'dashboard-' + attr).slideDown();
        $(this).closest('.nav').find('li').removeClass('active');
        $(this).closest('li').addClass('active');
    });
}

function OpenModalBox(header, inner, bottom) {
    var modalbox = $('#modalbox');
    modalbox.find('.modal-header-name span').html(header);
    modalbox.find('.devoops-modal-inner').html(inner);
    modalbox.find('.devoops-modal-bottom').html(bottom);
    modalbox.fadeIn('fast');
    $('body').addClass("body-expanded");
}

function CloseModalBox() {
    var modalbox = $('#modalbox');
    modalbox.fadeOut('fast', function () {
        modalbox.find('.modal-header-name span').children().remove();
        modalbox.find('.devoops-modal-inner').children().remove();
        modalbox.find('.devoops-modal-bottom').children().remove();
        $('body').removeClass("body-expanded");
    });
}

(function ($) {
    $.fn.beautyTables = function () {
        var table = this;
        var string_fill = false;
        this.on('keydown', function (event) {
            var target = event.target;
            var tr = $(target).closest("tr");
            var col = $(target).closest("td");
            if (target.tagName.toUpperCase() == 'INPUT') {
                if (event.shiftKey === true) {
                    switch (event.keyCode) {
                        case 37: // left arrow
                            col.prev().children("input[type=text]").focus();
                            break;
                        case 39: // right arrow
                            col.next().children("input[type=text]").focus();
                            break;
                        case 40: // down arrow
                            if (!string_fill) {
                                tr.next().find('td:eq(' + col.index() + ') input[type=text]').focus();
                            }
                            break;
                        case 38: // up arrow
                            if (!string_fill) {
                                tr.prev().find('td:eq(' + col.index() + ') input[type=text]').focus();
                            }
                            break;
                    }
                }
                if (event.ctrlKey === true) {
                    switch (event.keyCode) {
                        case 37: // left arrow
                            tr.find('td:eq(1)').find("input[type=text]").focus();
                            break;
                        case 39: // right arrow
                            tr.find('td:last-child').find("input[type=text]").focus();
                            break;
                        case 40: // down arrow
                            if (!string_fill) {
                                table.find('tr:last-child td:eq(' + col.index() + ') input[type=text]').focus();
                            }
                            break;
                        case 38: // up arrow
                            if (!string_fill) {
                                table.find('tr:eq(1) td:eq(' + col.index() + ') input[type=text]').focus();
                            }
                            break;
                    }
                }
                if (event.keyCode == 13 || event.keyCode == 9) {
                    event.preventDefault();
                    col.next().find("input[type=text]").focus();
                }
                if (!string_fill) {
                    if (event.keyCode == 34) {
                        event.preventDefault();
                        table.find('tr:last-child td:last-child').find("input[type=text]").focus();
                    }
                    if (event.keyCode == 33) {
                        event.preventDefault();
                        table.find('tr:eq(1) td:eq(1)').find("input[type=text]").focus();
                    }
                }
            }
        });
        table.find("input[type=text]").each(function () {
            $(this).on('blur', function (event) {
                var target = event.target;
                var col = $(target).parents("td");
                if (table.find("input[name=string-fill]").prop("checked")) {
                    col.nextAll().find("input[type=text]").each(function () {
                        $(this).val($(target).val());
                    });
                }
            });
        })
    };
})(jQuery);
//
// Beauty Hover Plugin (backlight row and col when cell in mouseover)
//
//
(function ($) {
    $.fn.beautyHover = function () {
        var table = this;
        table.on('mouseover', 'td', function () {
            var idx = $(this).index();
            var rows = $(this).closest('table').find('tr');
            rows.each(function () {
                $(this).find('td:eq(' + idx + ')').addClass('beauty-hover');
            });
        }).on('mouseleave', 'td', function (e) {
            var idx = $(this).index();
            var rows = $(this).closest('table').find('tr');
            rows.each(function () {
                $(this).find('td:eq(' + idx + ')').removeClass('beauty-hover');
            });
        });
    };
})(jQuery);

//
// Function for create test sliders on Progressbar page
//
function CreateAllSliders() {
    $(".slider-default").slider();
    var slider_range_min_amount = $(".slider-range-min-amount");
    var slider_range_min = $(".slider-range-min");
    var slider_range_max = $(".slider-range-max");
    var slider_range_max_amount = $(".slider-range-max-amount");
    var slider_range = $(".slider-range");
    var slider_range_amount = $(".slider-range-amount");
    slider_range_min.slider({
        range: "min",
        value: 37,
        min: 1,
        max: 700,
        slide: function (event, ui) {
            slider_range_min_amount.val("$" + ui.value);
        }
    });
    slider_range_min_amount.val("$" + slider_range_min.slider("value"));
    slider_range_max.slider({
        range: "max",
        min: 1,
        max: 100,
        value: 2,
        slide: function (event, ui) {
            slider_range_max_amount.val(ui.value);
        }
    });
    slider_range_max_amount.val(slider_range_max.slider("value"));
    slider_range.slider({
        range: true,
        min: 0,
        max: 500,
        values: [75, 300],
        slide: function (event, ui) {
            slider_range_amount.val("$" + ui.values[0] + " - $" + ui.values[1]);
        }
    });
    slider_range_amount.val("$" + slider_range.slider("values", 0) + " - $" + slider_range.slider("values", 1));
    $("#equalizer > div.progress > div").each(function () {
        // read initial values from markup and remove that
        var value = parseInt($(this).text(), 10);
        $(this).empty().slider({
            value: value,
            range: "min",
            animate: true,
            orientation: "vertical"
        });
    });
}
function ScreenSaver() {
    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");
    // Size of canvas set to fullscreen of browser
    var W = window.innerWidth;
    var H = window.innerHeight;
    canvas.width = W;
    canvas.height = H;
    // Create array of particles for screensaver
    var particles = [];
    for (var i = 0; i < 25; i++) {
        particles.push(new Particle());
    }
    function Particle() {
        // location on the canvas
        this.location = {
            x: Math.random() * W,
            y: Math.random() * H
        };
        // radius - lets make this 0
        this.radius = 0;
        // speed
        this.speed = 3;
        // random angle in degrees range = 0 to 360
        this.angle = Math.random() * 360;
        // colors
        var r = Math.round(Math.random() * 255);
        var g = Math.round(Math.random() * 255);
        var b = Math.round(Math.random() * 255);
        var a = Math.random();
        this.rgba = "rgba(" + r + ", " + g + ", " + b + ", " + a + ")";
    }

    // Draw the particles
    function draw() {
        // re-paint the BG
        // Lets fill the canvas black
        // reduce opacity of bg fill.
        // blending time
        ctx.globalCompositeOperation = "source-over";
        ctx.fillStyle = "rgba(0, 0, 0, 0.02)";
        ctx.fillRect(0, 0, W, H);
        ctx.globalCompositeOperation = "lighter";
        for (var i = 0; i < particles.length; i++) {
            var p = particles[i];
            ctx.fillStyle = "white";
            ctx.fillRect(p.location.x, p.location.y, p.radius, p.radius);
            // Lets move the particles
            // So we basically created a set of particles moving in random
            // direction
            // at the same speed
            // Time to add ribbon effect
            for (var n = 0; n < particles.length; n++) {
                var p2 = particles[n];
                // calculating distance of particle with all other particles
                var yd = p2.location.y - p.location.y;
                var xd = p2.location.x - p.location.x;
                var distance = Math.sqrt(xd * xd + yd * yd);
                // draw a line between both particles if they are in 200px range
                if (distance < 200) {
                    ctx.beginPath();
                    ctx.lineWidth = 1;
                    ctx.moveTo(p.location.x, p.location.y);
                    ctx.lineTo(p2.location.x, p2.location.y);
                    ctx.strokeStyle = p.rgba;
                    ctx.stroke();
                    // The ribbons appear now.
                }
            }
            // We are using simple vectors here
            // New x = old x + speed * cos(angle)
            p.location.x = p.location.x + p.speed * Math.cos(p.angle * Math.PI / 180);
            // New y = old y + speed * sin(angle)
            p.location.y = p.location.y + p.speed * Math.sin(p.angle * Math.PI / 180);
            // You can read about vectors here:
            // http://physics.about.com/od/mathematics/a/VectorMath.htm
            if (p.location.x < 0)
                p.location.x = W;
            if (p.location.x > W)
                p.location.x = 0;
            if (p.location.y < 0)
                p.location.y = H;
            if (p.location.y > H)
                p.location.y = 0;
        }
    }

    setInterval(draw, 30);
}
function removeAnmaClass() {
    if ($('#ajax-content').hasClass('fadeInRight')) {
        $('#ajax-content').removeClass('am-animation-slide-right');
    }
}

$(function () {
    if (parseInt($('#animation').attr('data-open')) == 1) {
        $('#ajax-content').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            $(this).removeClass('animated fadeInRight');
        })
        $('#headernavbar').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            $(this).removeClass('animated fadeInDown');
        })
        $('#sidebar-left').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            $(this).removeClass('animated fadeInRight');
        })
    }
    $('body').on('click', '.show-sidebar', function (e) {
        e.preventDefault();
        $('div#main').toggleClass('sidebar-show');
        setTimeout(MessagesMenuWidth, 250);
    });
    var ajax_url = location.hash.replace(/^#/, '');
    if (ajax_url.length < 1) {
        ajax_url = 'dashboard';
    }
    //TODO admin模块 默认
    LoadAjaxContent('admin/ajax/' + ajax_url);
    var item = $('.main-menu li a[href$="' + ajax_url + '"]');
    item.addClass('active-parent active');
    $('.dropdown:has(li:has(a.active)) > a').addClass('active-parent active');
    $('.dropdown:has(li:has(a.active)) > ul').css("display", "block");
    var basemenu_height = $('.main-menu').height();
    $('.main-menu').on('click', 'a', function (e) {
        var parents = $(this).parents('li');
        var li = $(this).closest('li.dropdown');
        var another_items = $('.main-menu li').not(parents);
        another_items.find('a').removeClass('active');
        another_items.find('a').removeClass('active-parent');
        if ($(this).hasClass('dropdown-toggle') || $(this).closest('li').find('ul').length == 0) {
            $(this).addClass('active-parent');
            var current = $(this).next();
            var l_h = 0;
            if (current.is(':visible')) {
                l_h = basemenu_height - current.height();
                li.find("ul.dropdown-menu").slideUp('fast');
                li.find("ul.dropdown-menu a").removeClass('active')
            } else {
                l_h = basemenu_height + current.height();
                another_items.find("ul.dropdown-menu").slideUp('fast');
                current.slideDown('fast');
            }
            var com_h = $(window).height() - 50;
            if (l_h > com_h) {
                $('#sidebar-left').css({"overflow-y": "scroll"});
                $('#sidebar-left').css({"height": com_h + "px"});
            } else {
                $('#sidebar-left').css({"overflow-y": "hidden"});
                $('#sidebar-left').css({"height": "auto"});
            }
        } else {
            if (li.find('a.dropdown-toggle').hasClass('active-parent')) {
                var pre = $(this).closest('ul.dropdown-menu');
                pre.find("li.dropdown").not($(this).closest('li')).find('ul.dropdown-menu').slideUp('fast');
            }
        }
        if (!$(this).hasClass('active')) {
            $(this).parents("ul.dropdown-menu").find('a').removeClass('active');
            $(this).addClass('active')
        }
        if ($(this).hasClass('ajax-link')) {
            e.preventDefault();
            if ($(this).hasClass('add-full')) {
                $('#content').addClass('full-content');
            } else {
                $('#content').removeClass('full-content');
            }
            var url = $(this).attr('href');
            window.location.hash = url;
            //TODO 模块admin 是否可默认
            LoadAjaxContent('admin/ajax/' + url);
        }
        if ($(this).attr('href') == '#') {
            e.preventDefault();
        }
    });
    var height = $(window).height() - 50;
    $('#main').css('min-height', height).on('click', '.expand-link', function (e) {
        var body = $('body');
        e.preventDefault();
        var box = $(this).closest('div.box');
        var button = $(this).find('i');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        box.toggleClass('expanded');
        body.toggleClass('body-expanded');
        var timeout = 0;
        if (body.hasClass('body-expanded')) {
            timeout = 100;
        }
        setTimeout(function () {
            box.toggleClass('expanded-padding');
        }, timeout);
        setTimeout(function () {
            box.resize();
            box.find('[id^=map-]').resize();
        }, timeout + 50);
    }).on('click', '.collapse-link', function (e) {
        e.preventDefault();
        var box = $(this).closest('div.box');
        var button = $(this).find('i');
        var content = box.find('div.box-content');
        content.slideToggle('fast');
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        setTimeout(function () {
            box.resize();
            box.find('[id^=map-]').resize();
        }, 50);
    }).on('click', '.close-link', function (e) {
        e.preventDefault();
        var content = $(this).closest('div.box');
        content.remove();
    });
    $('body').on('click', 'a.close-link', function (e) {
        e.preventDefault();
        CloseModalBox();
    });
    $('.dropdown-menu .top-link').on('click', function (e) {
        e.preventDefault();
        var ajax_url = $(this).attr('href');
        var item = $('.main-menu li a[href$="' + ajax_url + '"]');
        item.addClass('active-parent active');
        $('.dropdown:has(li:has(a.active)) > a').addClass('active-parent active');
        $('.dropdown:has(li:has(a.active)) > ul').css("display", "block");
        window.location.hash = ajax_url;
        item.trigger('click');
    })
    $('#search').on('keydown', function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            var ajax_url = $('#searchMenu').attr('data-value');
            var item = $('.main-menu li a[href$="' + ajax_url + '"]');
            item.addClass('active-parent active');
            $('.dropdown:has(li:has(a.active)) > a').addClass('active-parent active');
            $('.dropdown:has(li:has(a.active)) > ul').css("display", "block");
            window.location.hash = ajax_url;
            item.trigger('click');
        }
    });
    $('#search i').on('click', function () {
        var e = $.Event('keydown');
        e.keyCode = 13
        $(this).trigger(e);
    })
    $(".main-menu .dropdown a[class!='ajax-link']").not("[class~='ajax-link']").not("[href~='javascript:logout();']").on('click', function (e) {
        e.preventDefault();
    })
    $('#content').css('height', $(window).height() - $('#headernavbar').height() + 'px');
    NProgress.configure({
        parent: '#headernavbar',
        ease: 'ease',
        speed: 800
    })
    $(document).ajaxStart(function () {
        NProgress.start();
    });
    $(document).ajaxStop(function () {
        NProgress.done();
    });
    var menus = [];
    $("[class~='ajax-link'][href!='#']").each(function (e) {
        var menu = {};
        menu.data = $(this).attr('href');
        menu.value = $.trim($(this).html().replace(/<i(.*)<\/i>/, ""));
        menus.push(menu);
    })
    $('#searchMenu').autocomplete({
        lookup: menus
    });
    // for session out of date
    $.ajaxSetup({
        complete: function (xhr, textStatus) {
            switch (xhr.status) {
                case 3389: {
                    sessionout();
                    break;
                }
                case 500: {
                    $.TeachDialog({
                        content: '服务端错误!',
                        showCloseButton: true,
                    });
                    break;
                }
                case 3388: {
                    $.TeachDialog({
                        content: '你没有权限这么做!',
                        showCloseButton: true,
                    });
                    break;
                }
                case 3387: {
                    $.TeachDialog({
                        content: '未知错误!',
                        showCloseButton: true,
                    });
                    break;
                }
                case 3386: {
                    $.TeachDialog({
                        content: '参数错误!',
                        showCloseButton: true,
                    });
                    break;
                }
                case 3385: {
                    $.TeachDialog({
                        content: '信息验证错误!',
                        showCloseButton: true,
                    });
                    break;
                }
                case 3384: {
                    $.TeachDialog({
                        content: '类型被使用!',
                        showCloseButton: true,
                    });
                    break;
                }
                case 3383: {
                    $.TeachDialog({
                        content: '保存Log错误!',
                        showCloseButton: true,
                    });
                    break;
                }
                case 3382: {
                    $.TeachDialog({
                        content: '空数据错误!',
                        showCloseButton: true,
                    });
                    break;
                }
                case 405: {
                    $.TeachDialog({
                        content: '非法访问!',
                        showCloseButton: true,
                    });
                    break;
                }
            }
        }
    });
    $('#personinfo_manage').click(function(){
        $.ajax({
            url:'admin/admin/personinfo_manage',
            dataType:'HTML',
            success:function(data){
                var con = data;
                $.TeachDialog({
                    title:'<h3>个人信息</h3>',
                    content:con
                })
            }
        })
    })
    $('#personal_setting').click(function(){
        $.ajax({
            url:'admin/admin/personal_setting',
            dataType:'HTML',
            success:function(data){
                var con = data;
                $.TeachDialog({
                    title:'<h3>个人设置</h3>',
                    content:con,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['修改'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        $.ajax({
                            url: 'admin/Admin/updateSetting',
                            data: $("#setting").serialize(),
                            type: 'POST',
                            dataType: 'JSON',
                            success: function (returnData) {
                                if (returnData.status == 0) {
                                    $.TeachDialog({
                                        content: returnData.msg
                                    });
                                } else {
                                    modal.modal('hide');
                                    $.TeachDialog({
                                        content: returnData.msg
                                    });
                                }
                            },
                            error: function () {
                                $.TeachDialog({
                                    content: '系统异常，请联系管理员'
                                });
                            },

                        });

                    }
                })
            }
        })
    })
    $('#change_password').click(function(){
        $.ajax({
            url:'admin/admin/change_password',
            dataType:'HTML',
            success:function(data){
                var con = data;
                $.TeachDialog({
                    title:'<h3>修改密码</h3>',
                    content:con,
                    showCloseButton: true,
                    showCloseButtonName: '关闭',
                    otherButtons: ['修改'],
                    otherButtonStyles: ['btn-primary'],
                    clickButton: function (sender, modal, index) {
                        if($('#onePass').val()=='' || $('#twoPass').val()==''){
                            $.TeachDialog({
                                content: '密码不能为空'
                            });
                            return;
                        }
                        $.ajax({
                            url: 'admin/Admin/updatePassword',
                            data: $("#password").serialize(),
                            type: 'POST',
                            dataType: 'JSON',
                            success: function (returnData) {
                                if (returnData.status == 0) {
                                    $.TeachDialog({
                                        content: returnData.msg
                                    });
                                } else {
                                    modal.modal('hide');
                                    $.TeachDialog({
                                        content: returnData.msg
                                    });
                                }
                            },
                            error: function () {
                                $.TeachDialog({
                                    content: '系统异常，请联系管理员'
                                });
                            },

                        });

                    }
                })
            }
        })
    })

});
