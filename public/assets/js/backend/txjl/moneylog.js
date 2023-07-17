define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'txjl/moneylog/index',
                    add_url: 'txjl/moneylog/add',
                    edit_url: 'txjl/moneylog/edit',
                    del_url: 'txjl/moneylog/del',
                    multi_url: 'txjl/moneylog/multi',
                    table: 'moneylog',
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
                        {field: 'user_id', title: __('uid'), align: 'left',operate: '='},
                        {field: 'user.nickname', title: __('昵称'),align: 'left', operate: 'LIKE'},
                        {field: 'money', title: __('变更金额'),align: 'left', operate: '='},
                        {field: 'before', title: __('变更前余额'),align: 'left', operate: '='},
                        {field: 'after', title: __('变更后余额'),align: 'left', operate: '='},
                        {field: 'createtime', title: __('提交时间'),align: 'left', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'memo', title: __('备注'),align: 'left', operate: 'LIKE'},
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
                iscl: function (value) {
                    if(value==1){
                        return '<span class="label label-default">'+ __('未审核')+ '</span>';
                    }else if(value==2){
                        return '<span class="label label-danger">' + __("已审核") + '</span>';
                    }else if(value==3){
                        return '<span class="label label-success">' + __("已驳回") + '</span>'; 
                    }
                }
            }
        }
    };
    return Controller;
});