define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'kami/kami/index',
                    add_url: 'kami/kami/add',
                    edit_url: 'kami/kami/edit',
                    del_url: 'kami/kami/del',
                    multi_url: 'kami/kami/multi',
                    table: 'kami',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'user.id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),align: 'left',align: 'left', sortable: true},
                        {field: 'pc', title: __('批次'), align: 'left',operate: 'LIKE'},
                        {field: 'crd', title: __('卡号'),align: 'left', operate: 'LIKE'},
                        {field: 'price', title: __('卡密金额'),align: 'left', operate: 'LIKE'},
                        {field: 'createtime', title: __('添加时间'),align: 'left', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'type', title: __('是否使用'),align: 'left', formatter: Controller.api.formatter.type, searchList: {1: __('未使用'), 2:__('已使用')}},
                        {field: 'user.nickname', title: __('使用人'),align: 'left', operate: 'LIKE'},
                        {field: 'uid', title: __('使用人ID'),align: 'left', operate: 'LIKE'},
                        {field: 'stime', title: __('使用时间'),align: 'left',formatter: Table.api.formatter.datetime, operate: 'LIKE'},
                        
                        {field: 'status', title: __('Status'),align: 'left',  formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
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
                type: function (value) {
                    return value==2 ? '<span class="label label-danger">' + __("已使用") + '</span>': '<span class="label label-default">'+ __('未使用')+ '</span>';
                }
            }
        }
    };
    return Controller;
});