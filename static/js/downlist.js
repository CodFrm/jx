/**
 js,动态加载软件列表
 */

var dList = function (obj) {
    obj.className += 'soft-list';//加一个class样式
    obj.page = 1;//记录现在的页码
    /**
     * 加载一页
     */
    obj.load = function (page) {
        page = page || obj.page;
        get({
            url: home+'/index/api/getSoftList?action=getSoftList&page=' + page,//URL打错了来着
            success: function (str) {
                var json = JSON.parse(str);
                obj.innerHTML = '';
                for (var key in json.rows) {
                    var html = '';
                    html = '<div class="item">\n' +
                        '            <div class="item-left">\n' +
                        '                <img src="'+home+'/static/image/softlogo/' + json.rows[key].soft_logo + '" alt="">\n' +
                        '                <a href="#" class="soft-title">\n' +
                        json.rows[key].soft_name +
                        '                </a>\n' +
                        '                <p class="soft-exp">\n' +
                        json.rows[key].soft_exp +
                        '                </p>\n' +
                        '            </div>\n' +
                        '            <div class="item-right">\n' +
                        '            <span class="down-number">\n';
                    html += timestamp2date(json.rows[key].soft_time) +
                        '            </span>\n' +
                        '                <button class="download" sid="' + json.rows[key].sid +
                        //吧id弄进去,用来标识的
                        '">立即下载</button>\n' +
                        '            </div>\n' +
                        '        </div>';//用到之前写的去,直接复制进去,ide也帮你处理好了
                    obj.innerHTML += html;
                }
                var html = '<br><div class="page">\n';
                if (page != 1) {
                    html += '<div class="page-item" page="' + (parseInt(page) - 1) + '">&lt;</div>\n';
                }
                if (json.total > 8) {//当页码大于8
                    page = parseInt(page);
                    var startPage = page - 3, endPage = page + 4;
                    if (page <= 3) {
                        //小于4
                        startPage = 1;
                        endPage = 8;
                    } else if (page + 4 > json.total) {
                        //页数+5还比总页数大
                        startPage = json.total - 7;
                        endPage = json.total;
                    }
                    for (var i = startPage; i <= endPage; i++) {//输出页码
                        var active = '';
                        if (i == page) {
                            active = 'active';
                            //如果是当前页的话,就标注一下
                        }
                        html += '<div class="page-item ' + active + '" page="' + i + '">' + i + '</div>';
                    }
                } else if (json.total <= 8) {//总页数小于等于8 全部输出
                    for (var i = 1; i <= json.total; i++) {//输出页码
                        var active = '';
                        if (i == page) {
                            active = 'active';
                        }
                        html += '<div class="page-item ' + active + '" page="' + i + '">' + i + '</div>';
                    }
                }

                if (page != json.total) {//判断与总页数相等
                    html += '<div class="page-item" page="' + (parseInt(page) + 1) + '">&gt;</div>\n';
                }
                obj.innerHTML += html;

            }
        });
    }
    //绑定按钮被按下
    bind(obj, 'click', function (ev) {
        //然后这里我要取到id的值,难道我要用文本处理的方法提取出来?
        var html = this.outerHTML;
        var sid = subString(html, 'sid="', '"');
        //然后打开一个下载链接,向服务器要
        // window.location='?action=download&sid='+sid;//我喜欢用单引号
        window.open('?action=download&sid=' + sid);//打开新的窗口去下载
    }, 'download');
    //绑定翻页
    bind(obj, 'click', function (ev) {
        var html = this.outerHTML;
        var page = subString(html, 'page="', '"');
        obj.load(page);
    }, 'page-item');
    //然后是翻页,有点复杂
    return obj;
}


/**
 * 写一个提取文本中间的函数
 * @param str
 * @param left
 * @param right
 */
function subString(str, left, right) {
    var leftPos = str.indexOf(left);
    var rightPos = str.indexOf(right, leftPos + left.length);
    return str.substring(leftPos + left.length, rightPos);
}


/**
 * 试试自己实现一个
 * @param attr
 * @param func
 * @param select
 */
function bind(father, attr, func, select) {
    //知道大概原理了,就自己写成函数,=_=全都用原生的...感觉还是学到了不少
    var id = {};
    if (typeof father == 'string') {
        id = document.getElementById(father);
    } else if (typeof father == 'object') {
        id = father;
    }
    id['on' + attr] = function (ev) {
        var oEvent = ev || event;
        var _this = oEvent.srcElement || oEvent.target;
        //用寻找的方法更好
        if (_this.className.toUpperCase().indexOf(select.toUpperCase()) >= 0) {
            if (func) func.call(_this, oEvent);
        }
    }
    return 0;
}

/**
 * 时间戳转日期
 * @param time
 * @returns {string}
 */
function timestamp2date(time) {
    var t = new Date(time * 1000);
    var commonTime = t.toLocaleString();
    var ret = t.getFullYear() + '/' + (t.getMonth() + 1) + '/' + t.getDay() +
        ' ' + zero(t.getHours()) + ':' + zero(t.getMinutes()) + ':' + zero(t.getSeconds());
    //年/月/日 时:分:秒
    //貌似是不会补0的,强迫症犯了,这样不好看额=_=
    //js日期有个奇葩的问题,月是从0开始,
    return ret;
}

function zero(str) {
    //传进来的是一个数值,转换一下
    //好看多了,上午就到这儿,下午继续2333,感觉一个人在尬聊,emmm,弹幕呢?
    str = str.toString();
    if (str.length < 2) {
        str = '0' + str;
    }
    return str;
}

/**
 * 封装xmlhttp get
 * @param obj
 * @returns {*}
 */
function get(obj) {
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    }
    else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (obj.success != undefined) {
                obj.success(xmlhttp.response);
            }
        }
    }
    xmlhttp.open("GET", obj.url, true);
    xmlhttp.send();
    return obj;
}