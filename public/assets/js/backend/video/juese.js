define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'video/juese/index'+location.search,
                    add_url: 'video/juese/add'+location.search,
                    edit_url: 'video/juese/edit'+location.search,
                    del_url: 'video/juese/del',
                    multi_url: 'video/juese/multi',
                    table: 'juese',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),align: 'left',  sortable: true},
                        {field: 'user.avatar', title: __('图片'),align: 'left', events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'user.nickname', title: __('Nickname'),align: 'left', operate: 'LIKE'},
                        {field: 'uid', title: __('UID'),align: 'left', visible: false,operate: 'LIKE'},
                        {field: 'videolist.name', title: __('name'),align: 'left', operate: '='},
                         {field: 'type', title: __('类型'),align: 'left', formatter: Controller.api.formatter.type, searchList: {'演员': __('演员'), '导演':__('导演'),'讲师':__('讲师')}},
                        {field: 'createtime', title: __('添加时间'),align: 'left', visible: false,formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'status',align: 'left', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {field: 'operate', title: __('Operate'),align: 'left', table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                paytype: function (value) {
                    return value==2 ? '<span class="label label-danger">' + __("已支付") + '</span>': '<span class="label label-default">'+ __('未支付')+ '</span>';
                },
                ishot: function (value) {
                    return value==2 ? '<span class="label label-danger">' + __("已推荐") + '</span>': '<span class="label label-default">'+ __('未推荐')+ '</span>';
                },
                type: function (value) {
                    if(value=='演员'){
                        return '<span class="label label-info">'+ __('演员')+ '</span>';
                    }else if(value=='导演'){
                        return '<span class="label label-warning">' + __("导演") + '</span>';
                    }else if(value=='讲师'){
                        return '<span class="label label-success">' + __("讲师") + '</span>'; 
                    }
                }
            }
        }
    };
    return Controller;
});