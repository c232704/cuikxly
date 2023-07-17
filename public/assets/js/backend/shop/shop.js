define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shop/shop/index',
                    add_url: 'shop/shop/add',
                    edit_url: 'shop/shop/edit',
                    del_url: 'shop/shop/del',
                    multi_url: 'shop/shop/multi',
                    table: 'shop',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),align: 'left',align: 'left', sortable: true},
                        {field: 'pic', title: __('封面'),align: 'left', events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'name', title: __('name'), align: 'left',operate: 'LIKE'},
                        {field: 'price', title: __('价格'),align: 'left', operate: 'LIKE'},
                        {field: 'priceline', title: __('划线价'),align: 'left', operate: 'LIKE'},
                        {field: 'vipzk', title: __('VIP折扣'),align: 'left', formatter: Controller.api.formatter.vipzk},
                        {field: 'createtime', title: __('添加时间'),align: 'left', visible: false,formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'user.nickname', title: __('Nickname'),align: 'left', operate: 'LIKE'},
                        
                        {field: 'is_hot', title: __('是否推荐'),align: 'left', formatter: Controller.api.formatter.ishot, searchList: {1: __('未推荐'), 2:__('已推荐')}},
                        {field: 'status', title: __('Status'),align: 'left',  formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [],
                            formatter: Table.api.formatter.operate
                        }
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
                vipzk:function (value) {
                    return value*100/10
                },

            }
        }
    };
    return Controller;
});