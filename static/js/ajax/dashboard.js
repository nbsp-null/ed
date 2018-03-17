/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {


        flag=0;
        $("#id_almanac").almanac({


            /**typeof(reValue) == undefined)
             * 单击某一天的事件
             */

            clickDay: function(elem){

                var _this = $(elem);

                setTimeout(function(){


                    if(_this.attr('data-solor')==null){

                                       $.ajax({
                                           url: 'admin/admin/alertlog',
                                           data: "t=4" ,
                                           type: 'POST',
                                           dataType: 'HTML', //返回的数据类型
                                           success: function(updatemodalhtml) {
                                               content = updatemodalhtml;
                                               //弹出修改框
                                               $.TeachDialog({
                                                   modalId: null,
                                                   animation: null,
                                                   title: '系统消息',
                                                   content: content,
                                                   showCloseButton: true,
                                                   showCloseButtonName: '关闭',
                                                   CloseButtonAddFunc: function() {
                                                   },

                                                   otherButtonStyles: [],
                                                   bootstrapModalOption: {
                                                       backdrop: 'static'
                                                   },
                                                   largeSize: false,
                                                   smallSize: false,
                                               });
                                           },
                                           error: function() {
                                               $.TeachDialog({
                                                   content: '获取数据失败，无法进行修改',
                                               });
                                               return;
                                           }
                                       });
                        console.log('阳历：' + _this.attr('data-year') + '年' + _this.attr('data-month') + '月' + _this.attr('data-solor'));


                    }else{
                        // console.log('阳历：' + _this.attr('data-year') + '年' + _this.attr('data-month') + '月' + _this.attr('data-solor'));
                        $.ajax({
                            url: 'admin/admin/alertlog',
                            data: "t=5" ,
                            type: 'POST',
                            dataType: 'HTML', //返回的数据类型
                            success: function(updatemodalhtml) {
                                content = updatemodalhtml;
                                //弹出修改框
                                $.TeachDialog({
                                    modalId: null,
                                    animation: null,
                                    title: '系统消息',
                                    content: content,
                                    showCloseButton: true,
                                    showCloseButtonName: '关闭',
                                    CloseButtonAddFunc: function() {
                                    },

                                    otherButtonStyles: [],
                                    bootstrapModalOption: {
                                        backdrop: 'static'
                                    },
                                    largeSize: false,
                                    smallSize: false,
                                });
                            },
                            error: function() {
                                $.TeachDialog({
                                    content: '获取数据失败，无法进行修改',
                                });
                                return;
                            }
                        });

                    }
                },400);
            }
        });



    $("#bc").click(function() {
        $("#con").style.display = 'inline-block';
    })
 
   $("#prev").click(function(){
       var page = parseInt($("#prev").attr("p"))-1;
      $.ajax({
          url:"admin/dashboard/notice",
          data:"page="+page,
          dataType: "HTML", //返回数据类型
          type:"POST",
          success: function(modal) {
                content = modal;
               document.getElementById("much").innerHTML = content;

          }
      })
   });
   $("#next").click(function(){
       var page = parseInt($("#next").attr("p"))+1;
      $.ajax({
          url:"admin/dashboard/notice",
          data:"page="+page,
          dataType: "HTML", //返回数据类型
          type:"POST",
          success: function(modal) {
                content = modal;
               
               document.getElementById("much").innerHTML = content;

          }
      })
   });
});
var studentCountChart = echarts.init(document.getElementById('student'));

$.get('admin/Dashboard/getStudentNum').done(function(data) {
    data = eval("(" + data + ")");
    var stutest = new Array();
    for (var i = 0; i < data.length; i++) {
        stutest.push(data[i].value);
    }
    var maxData = Math.max.apply(null, stutest);
    if (maxData < 5) {
        maxData = 5;
    } else {
        maxData = null;
    }
    studentCountChart.setOption({
        title: {
            text: '学生数据图'
        },
        tooltip: {},
        legend: {
            data: ['学生统计:单位/人']
        },
        xAxis: {
            data: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"]
        },
        yAxis: {
            max: maxData
        },
        series: [{
                itemStyle: {
                    normal: {
                        lineStyle: {
                            color: '#000080'
                        }
                    }
                },
                name: '学生统计:单位/人',
                type: 'line',
                data: data
            }]
    });
});

var teacherCountChart = echarts.init(document.getElementById('teacher'));

$.get('admin/Dashboard/getTeacherNum').done(function(data) {
    data = eval("(" + data + ")");
    var test = new Array();
    for (var i = 0; i < data.length; i++) {
        test.push(data[i].value);
    }
    var maxData = Math.max.apply(null, test);
    if (maxData < 5) {
        maxData = 5;
    } else {
        maxData = null;
    }
    teacherCountChart.setOption({
        title: {
            text: '老师数据图'
        },
        tooltip: {},
        legend: {
            data: ['老师统计:单位/人']
        },
        xAxis: {
            data: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"]
        },
        yAxis: {
            max: maxData
        },
        series: [{
                itemStyle: {
                    normal: {
                        lineStyle: {
                            color: '#800080'
                        }
                    }
                },
                name: '老师统计:单位/人',
                type: 'line',
                data: data
            }]
    });
});

var schoolChart = echarts.init(document.getElementById('school'));
$.get('admin/Dashboard/getSchool').done(function(data) {
    data = eval("(" + data + ")");
    if (data == "") {
        die;
    }
    schoolChart.setOption({
        title: {
            text: '生源分布图',
            x: 'center'
        },
        color:['#dc69aa','#8d98b3','#ffb980','#5ab1ef','#95706D','#2ec7c9','#b6ade','#da7a80','#6950a1','#1d953f','#44693'],
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        series: [
            {
                name: '学生来源',
                type: 'pie',
                radius: '55%',
                center: ['50%', '60%'],
                data: data

            }
        ]
    });
});
var myPie = echarts.init(document.getElementById('student_pie'));

$.get("admin/Dashboard/getStudentByCourse").done(function(data) {
    data = eval("(" + data + ")");
    if (data == "") {
        die;
    }
    myPie.setOption({
        title: {
            text: '课程学生数统计',
            x: 'center'
        },
        color:['#95706D','#2ec7c9','#ffb980','#5ab1ef','#b6ade','#da7a80','#dc69aa','#8d98b3','#6950a1','#1d953f','#44693'],
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        series: [
            {
                name: '数据统计',
                type: 'pie',
                radius: '55%',
                center: ['50%', '60%'],
                data: data
            }
        ]
    });
});

