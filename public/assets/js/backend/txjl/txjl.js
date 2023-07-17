define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'txjl/txjl/index',
                    add_url: 'txjl/txjl/add',
                    edit_url: 'txjl/txjl/edit',
                    del_url: 'txjl/txjl/del',
                    multi_url: 'txjl/txjl/multi',
                    table: 'txjl',
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
                        {field: 'uid', title: __('提现人id'), align: 'left',visible: false,operate: '='},
                        {field: 'user.nickname', title: __('提现人'),formatter: Controller.api.formatter.nickname, align: 'left', operate: 'LIKE'},
                        {field: 'money', title: __('提现金额'),align: 'left', operate: '='},
                        {field: 'sxf', title: __('提现手续费'),align: 'left', operate: '='},
                        
                        {field: 'type', title: __('类型'),align: 'left', operate: 'LIKE'},
                        {field: 'cord', title: __('提现帐号'),align: 'left', operate: 'LIKE'},
                        {field: 'name', title: __('提现姓名'),align: 'left', operate: 'LIKE'},
                        {field: 'moneydz', title: __('实际到账'),align: 'left', operate: '='},
                        {field: 'createtime', title: __('提现时间'),align: 'left', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'memoj', title: __('备注'),align: 'left', operate: 'LIKE'},
                        {field: 'iscl', title: __('是否审核'),align: 'left', formatter: Controller.api.formatter.iscl, searchList: {1: __('未审核'), 2:__('已审核'), 3:__('已驳回')}},
                         {field: 'islx', title: __('提现类型'),align: 'left', formatter: Controller.api.formatter.islx, searchList: {1: __('前端提现'), 2:__('后台提现')}},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                            //   	{
                            //         name: 'index',
                            //         title:function (data) {
                            //             return '审核';
                            //         },
                            //       	extend:'data-area=\'["50%","60%"]\'',
                            //         classname: 'btn btn-xs btn-primary btn-dialog',
                            //         icon: 'fa fa-cog',
                            //         url:function (data) {
                            //             return 'txjl/txjl/info?pid='+data.id;
                            //         }
                            //     }
                            ],
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
                iscl: function (value) {
                    if(value==1){
                        return '<span class="label label-default">'+ __('未审核')+ '</span>';
                    }else if(value==2){
                        return '<span class="label label-danger">' + __("已审核") + '</span>';
                    }else if(value==3){
                        return '<span class="label label-success">' + __("已驳回") + '</span>'; 
                    }
                },
                nickname: function (value) {
                    if(value){
                        return value;
                    }else{
                        return "后台提现";
                    }
                },
                islx: function (value) {
                    if(value==1){
                        return '<span class="label label-default">'+ __('前端提现')+ '</span>';
                    }else if(value==2){
                        return '<span class="label label-danger">' + __("后台提现") + '</span>';
                    }else if(value==3){
                        return '<span class="label label-success">' + __("已驳回") + '</span>'; 
                    }
                }
            }
        }
    };
    return Controller;
});