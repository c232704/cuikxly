define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/xxts/index',
                    add_url: 'user/xxts/add',
                    edit_url: 'user/xxts/edit',
                    del_url: 'user/xxts/del',
                    multi_url: 'user/xxts/multi',
                    table: 'xxts',
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
                        {field: 'name', title: __('标题'), align: 'left',operate: 'LIKE'},
                        {field: 'info', title: __('内容'),align: 'left', operate: 'LIKE'},
                        {field: 'url', title: __('推送地址'),align: 'left', operate: 'LIKE'},
                        {field: 'urlid', title: __('推送id'),align: 'left', operate: 'LIKE'},
                        {field: 'createtime', title: __('添加时间'),align: 'left', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'type', title: __('是否推送'),align: 'left', formatter: Controller.api.formatter.type, searchList: {1: __('未推送'), 2:__('已推送')}},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                              	{
                                    name: 'index',
                                    title:function (data) {
                                        return '【'+data.name+'】 推送';
                                    },
                                  	extend:'data-area=\'["300px","350px"]\'',
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    icon: 'fa fa-list-ol',
                                    url:function (data) {
                                        return 'user/xxts/tuis?ids='+data.id;
                                    }
                                }
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
                type: function (value) {
                    return value==2 ? '<span class="label label-danger">' + __("已推送") + '</span>': '<span class="label label-default">'+ __('未推送')+ '</span>';
                }
            }
        }
    };
    return Controller;
});