define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'txjl/czlog/index',
                    add_url: 'txjl/czlog/add',
                    edit_url: 'txjl/czlog/edit',
                    del_url: 'txjl/czlog/del',
                    multi_url: 'txjl/czlog/multi',
                    table: 'czlog',
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
                        {field: 'id', title: __('Id'),align: 'left', sortable: true},
                        {field: 'oid', title: __('订单号'), align: 'left', formatter: Controller.api.formatter.oidy,  operate: '='},
                        {field: 'out_trade_no', title: __('编号'), align: 'left',operate: '='},
                        {field: 'uid', title: __('UID'), align: 'left',operate: '='},
                        {field: 'user.nickname', title: __('昵称'),align: 'left', operate: 'LIKE'},
                        {field: 'amount', title: __('金额'),align: 'left', operate: '='},
                        {field: 'buytype', title: __('支付方式'),align: 'left', operate: '='},
                        {field: 'oid', title: __('类型'), align: 'left',formatter: Controller.api.formatter.oidlx, operate: '='},
                        {field: 'type', title: __('支付状态'),align: 'left', formatter: Controller.api.formatter.types, operate: '='},
                        {field: 'createtime', title: __('提交时间'),align: 'left', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
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
                types: function (value) {
                    return value==2 ? '<span class="label label-danger">' + __("已支付") + '</span>': '<span class="label label-default">'+ __('未支付')+ '</span>';
                },
                oidlx: function (value) {
                    if(value==0){
                        return '<span class="label label-default">'+ __('用户充值')+ '</span>';
                    }else if(value>2){
                        return '<span class="label label-danger">' + __("购买产品") + '</span>';
                    }
                },
                oidy: function (value) {
                    if(value==0){
                        return '---';
                    }else if(value>2){
                        return value;
                    }
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