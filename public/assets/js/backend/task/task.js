define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'task/task/index',
                    add_url: 'task/task/add',
                    edit_url: 'task/task/edit',
                    del_url: 'task/task/del',
                    multi_url: 'task/task/multi',
                    table: 'task',
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
                        {field: 'name', title: __('name'), align: 'left',operate: 'LIKE'},
                        {field: 'type', title: __('type'),align: 'left', operate: 'LIKE'},
                        {field: 'price', title: __('投放单价'),align: 'left', operate: 'LIKE'},
                        {field: 'sum', title: __('投放数量'),align: 'left', operate: 'LIKE'},
                        {field: 'endtime', title: __('结束时间'),align: 'left', operate: 'LIKE'},
                        {field: 'subtime', title: __('提交时间'), align: 'left',operate: 'LIKE'},
                        {field: 'shetime', title: __('审核周期'),align: 'left', operate: 'LIKE'},
                        {field: 'createtime', title: __('添加时间'),align: 'left', visible: false,formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'user.username', title: __('Username'),align: 'left', operate: 'LIKE'},
                        {field: 'user.nickname', title: __('Nickname'),align: 'left',visible: false, operate: 'LIKE'},
                        {field: 'user.mobile', title: __('电话'),align: 'left',visible: false, operate: 'LIKE'},
                        {field: 'user.avatar', title: __('头像'),align: 'left', events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'buytype', title: __('支付类型'),align: 'left', operate: 'LIKE'},
                        {field: 'ishot', title: __('是否推荐'),align: 'left', formatter: Controller.api.formatter.ishot, searchList: {1: __('未推荐'), 2:__('已推荐')}},
                        {field: 'paytype', title: __('支付状态'),align: 'left', formatter: Controller.api.formatter.paytype, searchList: {1: __('未支付'), 2:__('已支付')}},
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
                ishot: function (value) {
                    return value==2 ? '<span class="label label-danger">' + __("已推荐") + '</span>': '<span class="label label-default">'+ __('未推荐')+ '</span>';
                }
            }
        }
    };
    return Controller;
});