/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 获取浏览器类型以及版本号
 * 支持国产浏览器:猎豹浏览器、搜狗浏览器、傲游浏览器、360极速浏览器、360安全浏览器、
 * QQ浏览器、百度浏览器等.
 * 支持国外浏览器:IE,Firefox,Chrome,safari,Opera等.
 * 使用方法:
 * 获取浏览器版本:Browser.client.version
 * 获取浏览器名称(外壳):Browser.client.name
 * @author:base on xuzengqiang
 * @since :2015-1-27 10:26:11
 **/
var Browser = Browser || (function (window) {
    var document = window.document,
            navigator = window.navigator,
            agent = navigator.userAgent.toLowerCase(),
            //IE8+支持.返回浏览器渲染当前文档所用的模式
            //IE6,IE7:undefinded.IE8:8(兼容模式返回7).IE9:9(兼容模式返回7||8)
            //IE10:10(兼容模式7||8||9)
            ieMode = document.documentMode,
            chrome = window.chrome || false,
            System = {
                agent: agent,
                isIE: /msie/.test(agent), //是否为IE
                isGecko: agent.indexOf("gecko") > 0 && agent.indexOf("like gecko"), //Gecko
                isWebkit: agent.indexOf("webkit") > 0, //webkit内核
                isStrict: document.compatMode == "CSS1Compat", //是否为标准模式
                supportSubTitle: function () {//是否支持subtitle
                    return "track" in document.createElement("track");
                },
                supportScope: function () {//是否支持scoped
                    return "scoped" in document.createElement("style");
                },
                ieVersion: function () {//获取ie的版本号
                    try {
                        return agent.match(/msie ([\d.]+)/)[1] || 0;
                    } catch (e) {
                        console.log("error");
                        return ieMode;
                    }
                },
                operaVersion: function () {//获取Opera版本号
                    try {
                        if (window.opera) {
                            return agent.match(/opera.([\d.]+)/)[1];
                        } else if (agent.indexOf("opr") > 0) {
                            return agent.match(/opr\/([\d.]+)/)[1];
                        }
                    } catch (e) {
                        console.log("error");
                        return 0;
                    }

                },
                versionFilter: function () {//version过滤,如31.0.252.152 只保留31.0
                    if (arguments.length === 1 && typeof arguments[0] === 'string') {
                        var version = arguments[0],
                                start = version.indexOf(".");
                        if (start > 0) {
                            var end = version.indexOf(".", start + 1);
                            if (end !== -1) {
                                return version.substr(0, end);
                            }
                        }
                        return version;
                    } else if (arguments.length === 1) {
                        return arguments[0];
                    }
                    return 0;

                }
            };
            
            try{
                //浏览器类型(IE、Opera、Chrome、Safari、Firefox)
                System.type = System.isIE?"IE":
                        window.opera || (agent.indexOf("opr") > 0)?"Opera":
                        (agent.indexOf("chrome")>0)?"Chrome":
                        //safari也是提供了专门的判定方式
                        window.openDatabase?"Safari":
                        (agent.indexOf("firefox")>0)?"Firefox":'unknow';
                
                //版本号
                System.version = (System.type === "IE")?System.ieVersion():
                        (System.type === "Firefox")?agent.match(/firefox\/([\d.]+)/)[1]:
                        (System.type === "Chrome") ?agent.match(/chrome\/([\d.]+)/)[1]:
                        (System.type === "Opera")?System.operaVersion():
                        (System.type === "Safari")?agent.match(/version\/([\d.]+)/)[1]:
                        "0";
                //浏览器外壳
                System.shell=function(){
                    //遨游浏览器
                    if(agent.indexOf("maxthon") > 0){
                        System.version = agent.match(/maxthon\/([\d.]+)/)[1] || System.version ;
				return "傲游浏览器";
                    }
                    //QQ浏览器
                    if(agent.indexOf("qqbrowser")>0){
                        System.version = agent.match(/qqbrowser\/([\d.]+)/)[1] || System.version ;
				return "QQ浏览器";
                    }
                    //搜狗浏览器
                    if(agent.indexOf("se 2.x") > 0){
                        return '搜狗浏览器';
                    }
                    
                    //Chrome:也可以使用window.chrome && window.chrome.webstore判断
                    if(chrome && System.type !== "Opera"){
                        var external = window.external,
                            clientInfo = window.clientInformation,
                                //客户端语言:zh-cn,zh.360下面会返回undefinded
                            clientLanguage = clientInfo.languages;
                            
                            //猎豹浏览器:或者agent.indexOf("lbbrowser")>0
                            if( external && 'LiebaoGetVersion' in external) {
					 return '猎豹浏览器';
			    }
                            //百度浏览器
                            if(agent.indexOf("bidubrowser")>0){
                               System.version = agent.match(/bidubrowser\/([\d.]+)/)[1] || 
						agent.match(/chrome\/([\d.]+)/)[1];
					return "百度浏览器";
                                        
                            }
                            //360极速浏览器和360安全浏览器
                            if(System.supportSubTitle() && typeof clientLanguage === "undefined") {
					//object.key()返回一个数组.包含可枚举属性和方法名称
					var storeKeyLen = Object.keys(chrome.webstore).length,
						v8Locale = "v8Locale" in window;
					return storeKeyLen > 1? '360极速浏览器':'360安全浏览器';	
			    }
			   return "Chrome";
                     }
                 return System.type;
                }
                
                //浏览器名称(如果是壳浏览器,则返回壳名称)
                System.name = System.shell();
            
                //对版本号进行过滤过处理
                System.version = System.versionFilter(System.version);
                
            }catch(e){
                console.log("error");
            }
            return {
                client:System
            }

})(window);
//alert(Browser.client.name+"  "+Browser.client.version);

/**
 * 当浏览器不支持时,增加提示信息
 * @param {type} param1
 * @param {type} param2
 */
window.onload = function(){
    var pObj = document.createElement("p");
    pObj.setAttribute('id', 'browsehappy');
    pObj.setAttribute('style', 'text-align:center');
    pObj.innerHTML = '你正在使用<strong style="font-size:1.2em;color:#e02427;">过时</strong>的浏览器，当前访问的系统暂不支持。 请' +
            '<a href="http://browsehappy.com/" target="_blank" style="font-size:1.2em;" >升级浏览器</a>' +
            '以获得更好的体验！';
    var first = document.body.firstChild;//得到页面的第一个元素 
    if(Browser.client.name === "IE"){
        document.body.insertBefore(pObj, first);//在得到的第一个元素之前插入 
    }
    
};
