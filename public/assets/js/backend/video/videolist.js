define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'video/videolist/index',
                    add_url: 'video/videolist/add',
                    edit_url: 'video/videolist/edit',
                    del_url: 'video/videolist/del',
                    multi_url: 'video/videolist/multi',
                    table: 'video',
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
                        {field: 'img', title: __('图片'),align: 'left', events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'name', title: __('name'), align: 'left',operate: 'LIKE'},
                        {field: 'type_text', title: __('分类'), operate:false, formatter: Table.api.formatter.label},
                        {field: 'price', title: __('普价格'),align: 'left', operate: 'LIKE'},
                        {field: 'vipprice', title: __('VIP价格'),align: 'left', operate: 'LIKE'},

                        {field: 'createtime', title: __('添加时间'),align: 'left', visible: false,formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'user.nickname', title: __('Nickname'),align: 'left', operate: 'LIKE'},
                        
                        {field: 'ishot', title: __('是否推荐'),align: 'left', formatter: Controller.api.formatter.ishot, searchList: {1: __('未推荐'), 2:__('已推荐')}},
                        {field: 'lx', title: __('类型'),align: 'left', formatter: Controller.api.formatter.lx, searchList: {1: __('视频'), 2:__('音频'), 3:__('小说'), 4:__('图片'), 5:__('图文')}},
                        {field: 'status', title: __('Status'),align: 'left',  formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                              	{
                                    name: 'index',
                                    title:function (data) {
                                        return '演员';
                                    },
                                  	extend:'data-area=\'["80%","80%"]\'',
                                    classname: 'btn btn-xs btn-warning btn-dialog',
                                    icon: 'fa fa-drivers-license-o',
                                    url:function (data) {
                                        return 'video/juese/index?pid='+data.id;
                                    }
                                },
                                {
                                    name: 'index',
                                    title:function (data) {
                                        return '【'+data.name+'】 章节';
                                    },
                                  	extend:'data-area=\'["80%","80%"]\'',
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    icon: 'fa fa-list-ol',
                                    url:function (data) {
                                        return 'video/video/index?pid='+data.id;
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
                        return '<span class="label label-success">' + __("小说") + '</span>'; 
                    }else if(value==4){
                        return '<span class="label label-danger">' + __("图片") + '</span>'; 
                    }else if(value==5){
                        return '<span class="label label-warning">' + __("图文") + '</span>'; 
                    }
                }
            }
        }
    };
    return Controller;
});