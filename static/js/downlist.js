/**
 js,动态加载软件列表
 */

var vf = ['mp4'];

function getFormat(name) {
    return name.substring(name.lastIndexOf('.') + 1);
    ;
}

function contains(arr, obj) {
    var i = arr.length;
    while (i--) {
        if (arr[i] === obj) {
            return true;
        }
    }
    return false;
}

function isVideoFile(name) {
    var format = getFormat(name);
    console.log(format);
    return contains(vf, format);
}

var dList = function (obj, sid, keydown) {
    obj.className += 'soft-list';//加一个class样式
    obj.page = 1;//记录现在的页码
    /**
     * 加载一页
     */
    obj.load = function (page) {
        page = page || obj.page;
        get({
            url: home + '/index/api/getSoftList?keydown=' + keydown + '&sid=' + sid + '&page=' + page,//URL打错了来着
            success: function (str) {
                var json = JSON.parse(str);
                obj.innerHTML = '';
                for (var key in json.rows) {
                    var html = '';
                    html = '<div class="item">\n' +
                        '            <div class="item-left">\n' +
                        '                <img src="' + home + '/static/res/images/' + json.rows[key].soft_logo + '" alt="">\n' +
                        '                <a class="soft-title">\n' +
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
                        '                <a class="download" target="_blank" href="' + home + '/d/' + json.rows[key].sid +
                        '?file=' + json.rows[key].soft_filename + '">立即下载</a>';
                    if (isVideoFile(json.rows[key].soft_filename)) {
                        html += '<a class="download" target="_blank" href="' + home + '/play/' + json.rows[key].sid + '">在线播放</a>';
                    }
                    html += '购买积分:' + json.rows[key].price + '\n' +
                        '            </div>\n' +
                        '        </div>';
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

var bindFunc = [];

/**
 * 试试自己实现一个
 * @param attr
 * @param func
 * @param select
 */
function bind(father, attr, func, select) {
    var id = {};
    if (typeof father == 'string') {
        id = document.getElementById(father);
    } else if (typeof father == 'object') {
        id = father;
    }
    bindFunc.push({select: select, func: func});
    id['on' + attr] = function (ev) {
        var oEvent = ev || event;
        var _this = oEvent.srcElement || oEvent.target;
        //用寻找的方法更好
        for (var item in bindFunc) {
            if (_this.className.toUpperCase().indexOf(bindFunc[item].select.toUpperCase()) >= 0) {
                if (bindFunc[item].func) bindFunc[item].func.call(_this, oEvent);
            }
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
    var ret = t.getFullYear() + '/' + (t.getMonth() + 1) + '/' + t.getDate() +
        ' ' + zero(t.getHours()) + ':' + zero(t.getMinutes()) + ':' + zero(t.getSeconds());
    return ret;
}

function zero(str) {
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