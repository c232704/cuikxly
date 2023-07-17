define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'video/video/index'+location.search,
                    add_url: 'video/video/add'+location.search,
                    edit_url: 'video/video/edit'+location.search,
                    del_url: 'video/video/del',
                    multi_url: 'video/video/multi',
                    table: 'video',
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
                        {field: 'img', title: __('图片'),align: 'left', events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'videolist.name', title: __('name'),align: 'left', operate: '='},
                        {field: 'name', title: __('章节'), align: 'left',operate: 'LIKE'},
                        {field: 'weigh', title: __('排序'), align: 'left',operate: 'LIKE'},
                        {field: 'price', title: __('普价格'),align: 'left', operate: 'LIKE'},
                        {field: 'vipprice', title: __('VIP价格'),align: 'left', operate: 'LIKE'},

                        {field: 'createtime', title: __('添加时间'),align: 'left', visible: false,formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        
                        {field: 'lx', title: __('类型'),align: 'left', formatter: Controller.api.formatter.lx, searchList: {1: __('视频'), 2:__('音频'), 3:__('文章')}},
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
                },
                lx: function (value) {
                    if(value==1){
                        return '<span class="label label-info">'+ __('视频')+ '</span>';
                    }else if(value==2){
                        return '<span class="label label-warning">' + __("音频") + '</span>';
                    }else if(value==3){
                        return '<span class="label label-success">' + __("文章") + '</span>'; 
                    }
                }
            }
        }
    };
    return Controller;
});