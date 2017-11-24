(function ($) {
    var createCMLTable = function (tableJson) {
        var tableDiv = $(this);
        var TableObject;
        var tableHtml = '<table class="cmltable">\n<tr>\n';
        var selectHtml = '<select class="cmltable-select">\n';
        var columns = tableJson.columns;
        var nowPage = 1;
        var totalPage = 0;
        var keyword='';
        var rows='';
        for (var i = 0; i < columns.length; i++) {
            tableHtml += '<th style="padding-left:4px;">\n';
            if (columns[i].checkbox == true) {
                tableHtml += '<div class="cmltable-check cmltable-check-head"></div>\n';
            } else if (columns[i].field) {
                tableHtml += columns[i].title + '\n';
            }
            tableHtml += '</th>\n';
        }
        if (tableJson.action) {
            for (var i = 0; i < tableJson.action.length; i++) {
                selectHtml += ' <option value="' + tableJson.action[i].action + '">' + tableJson.action[i].title + '</option>\n';
            }
        }
        tableHtml += '</tr>\n</table>\n<div class="cmltable-footer">\n<p>总共有<span class="cmltable-page"></span>页,<span class="cmltable-total"></span>条数据</p>\n<div class="cmltable-btngroup cmltable-pages"></div></div>'
        selectHtml += '</select>\n';
        $(this).html(selectHtml + '<button class="cmltable-btn cmltable-sublit">确定</button>\n' + '<div class="cmltable-search">\n<input type="text" class="cmltable-select cmltable-search-edit" />\n' + '\n<button class="cmltable-btn  cmltable-search-btn">搜索</button>\n</div>' + tableHtml);
        $(this).find('.cmltable-sublit').click(function (event) { //选择列表框确定
            for (var i = 0; i < tableJson.action.length; i++) {
                if (tableJson.action[i].action == $(this).prev().val()) {
                    tableJson.action[i].func(TableObject);
                    break;
                }
            }
        });
        $(this).find('.cmltable-check-head').click(function (event) { //全选
            headCheck = $(this).attr('check');
            TableObject.find('.cmltable-check').each(function (index, el) {
                if (index == 0) return 0;
                if (headCheck == 'true') {
                    $(this).text('');
                    $(this).attr('check', 'false');
                } else {
                    $(this).text('✔');
                    $(this).attr('check', 'true');
                }
            });

        });

        $(this).on('click', '.cmltable-footer > .cmltable-pages > .cmltable-btn', function (event) { //下一页或者上一页
            var pageText = $(this).text();
            if (pageText == '<') {
                if (parseInt(nowPage) != 1) {
                    nowPage--;
                }
            } else if (pageText == '>') {
                if (parseInt(nowPage) != totalPage) {
                    nowPage++;
                }
            } else {
                nowPage = pageText;
            }
            TableObject.refresh();
        });

        TableObject = $(this).children('.cmltable');
        /**
         * 获取选中
         * @return {json} [选中项]
         */
        TableObject.getSelect = function () {
            var check = {};
            var i = 0;
            $(this).find('.cmltable-check').each(function (index, el) {
                if ($(this).attr('check') == 'true' && $(this).attr('cml-value') != 0) {
                    check['id[' + i++ + ']'] = $(this).attr('cml-value');
                }
            });
            return check;
        };
        TableObject.findById=function (keyid) {
            for (var n = 0; n < rows.length; n++) {
                if(rows[n][tableJson.keyID]==keyid){
                    return rows[n];
                }
            }
            return false;
        }
        /**
         * 刷新
         * @return {null}
         */
        TableObject.refresh = function () {
            //清空
            TableObject.find("tr:not(:first)").remove();
            jQuery.ajax({ //读取内容
                url: tableJson.url,
                type: 'get',
                data: {'page': nowPage,'keyword':keyword},
                success: function (jsonData) {
                    tableDiv.find('.cmltable-page').html(jsonData['page']);
                    tableDiv.find('.cmltable-total').html(jsonData['total']);
                    //填入内容
                    rows=jsonData.rows;
                    for (var n = 0; n < jsonData.rows.length; n++) {
                        var newRow = "<tr>";
                        for (var i = 0; i < columns.length; i++) {
                            newRow += "<td style='padding-left:4px;'>";
                            if (columns[i].checkbox == true) {
                                newRow += '<div class="cmltable-check" cml-value="' + jsonData.rows[n][tableJson.keyID] + '"></div>\n';
                            } else if (columns[i].field) {
                                if (columns[i].format) {
                                    newRow += columns[i].format(jsonData.rows[n][columns[i].field], jsonData.rows[n][tableJson.keyID]) + '\n';
                                } else {
                                    newRow += jsonData.rows[n][columns[i].field] + '\n';
                                }

                            }
                            newRow += "</td>\n";
                        }
                        newRow += "</tr>\n"
                        TableObject.find('tr:last').after(newRow);
                    }
                    switchInit();
                    var pageHtml = '<button class="cmltable-btn">&lt;</button>';
                    //设置页码
                    if (jsonData['page'] <= 8) {
                        for (var i = 0; i < jsonData['page']; i++) {
                            pageHtml += '<button class="cmltable-btn' + (nowPage == (i + 1) ? ' cmltable-btn-active' : '') + '">' + (i + 1) + '</button>';
                        }
                    }
                    pageHtml += '<button class="cmltable-btn">&gt;</button>';
                    totalPage = jsonData['page'];
                    tableDiv.find('.cmltable-pages').html(pageHtml);
                }
            });
        };
        $(this).find('.cmltable-search-btn').click(function (event) {//搜索
            keyword=$('.cmltable-search-edit').val();
            TableObject.refresh();
        });
        TableObject.refresh();
        return TableObject;
    };
    $.fn.createCMLTable = createCMLTable;
})(jQuery);


jQuery(document).ready(function ($) {
    $('.cmltable-check').each(function (index, el) {
        if ($(this).attr('check') == 'true') {
            $(this).text('✔');
            $(this).attr('check', 'true');
        } else {
            $(this).text('1');
            $(this).attr('check', 'false');
        }
    });
    $(document).on('click', '.cmltable-check', function (event) {
        if ($(this).attr('check') == 'true') {
            $(this).text('');
            $(this).attr('check', 'false');
        } else {
            $(this).text('✔');
            $(this).attr('check', 'true');
        }
    });
    switchInit();
    $(document).on('click', '.cmltable-switch', function (event) {
        if ($(this).attr('check') == 'true') {
            $(this).attr('check', 'false');
            $(this).children('.cmltable-switch-on').css({float: 'left', background: '#64bd63'});
        } else {
            $(this).attr('check', 'true');
            $(this).children('.cmltable-switch-on').css({float: 'right', background: '#ffc800'});
        }
    });
});

function switchInit() {
    $('.cmltable-switch').each(function (index, el) {
        if ($(this).attr('check') == 'true') {
            $(this).attr('check', 'true');
            $(this).children('.cmltable-switch-on').css({float: 'left', background: '#64bd63'});
        } else {
            $(this).attr('check', 'false');
            $(this).children('.cmltable-switch-on').css({float: 'right', background: '#ffc800'});
        }
    });
}

function editShow(title, data) {
    var html = '<div class="showmsg">\n' +
        '    <div class="cml-msg">\n' +
        '        <div class="cml-msg-header">\n' +
        '            <div class="cml-msg-body">' + title + '</div>\n' +
        '        </div>\n' +
        '        <div class="cml-msg-body">\n';
    for (var i = 0; i < data.rows.length; i++) {
        if (data.rows[i].type == undefined) {
            data.rows[i].type = 'default';
        }
        if (data.rows[i].value != undefined) {
            if (data.rows[i].type == 'html') {
                html += '<div>' + data.rows[i].value + '</div>';
            } else if (data.rows[i].type == 'multi') {
                html += '<div><label class="cml-form-label">' + data.rows[i].title + ': </label>' +
                    '<textarea class="cml-form-textarea ' + data.rows[i].field + '" rows="3" cols="20">' + data.rows[i].value +
                    '</textarea>' +
                    '</div>';
            } else if (data.rows[i].type == 'select') {
                html += '<div><label class="cml-form-label">' + data.rows[i].title + ': </label><select style="min-width: 200px;" class="' + data.rows[i].field + '">';
                rows = data.rows[i].value;
                if (data.rows[i].now == undefined) {
                    data.rows[i].now = rows[0];
                }
                for (var key in rows) {
                    if (data.rows[i].now == rows[key]) {
                        html += '<option selected="true" value ="' + rows[key] + '">' + key + '</option>';
                    } else {
                        html += '<option value ="' + rows[key] + '">' + key + '</option>';
                    }
                }
                html += '</select></div>';
            } else {
                html += '<div><label class="cml-form-label">' + data.rows[i].title + ': </label>';
                html += '<input class="cml-form-input ' + data.rows[i].field + '" type="text"';
                html += ' value="' + data.rows[i].value + '"></div>\n';
            }

        } else {
            html += '></div>\n';
        }
    }

    html += '        </div>\n' +
        '        <div class="cml-msg-footer">\n' +
        '            <button class="cml-btn cancel" style="float: right;">取消</button>\n' +
        '            <button class="cml-btn confirm" style="float: right;">提交</button>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</div>';
    $('body:last').append(html);
    $('.cml-btn.confirm').click(function () {
        if (data['post'] == undefined) {
            $('.showmsg').remove();
            return;
        }
        var param = {};
        for (var i = 0; i < data.rows.length; i++) {
            if (data.rows[i].type == undefined) {
                data.rows[i].type = 'default';
            }
            if (data.rows[i].value != undefined) {
                if (data.rows[i].type == 'default') {
                    param[data.rows[i].field] = $('.' + data.rows[i].field).val();
                } else if (data.rows[i].type == 'select') {
                    param[data.rows[i].field] = $('.' + data.rows[i].field).val();
                } else if (data.rows[i].type == 'multi') {
                    param[data.rows[i].field] = $('.' + data.rows[i].field).val();
                }

            } else {
                html += '></div>\n';
            }
        }
        if (data['post'](param)) {
            $('.showmsg').remove();
            return;
        }

    });
    $('.cml-btn.cancel').click(function () {
        $('.showmsg').remove();
        return;
    });
    var obj = {};
    obj.close = function () {
        $('.showmsg').remove();
    }
    return obj;
}