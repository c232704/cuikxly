define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: 'user/user/add',
                    edit_url: 'user/user/edit',
                    del_url: 'user/user/del',
                    multi_url: 'user/user/multi',
                    table: 'user',
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
                        {field: 'id', title: __('Id'), sortable: true},
                        {field: 'group.name',align: 'left', title: __('Group')},
                        {field: 'username', align: 'left',title: __('Username'), operate: 'LIKE'},
                        {field: 'nickname',align: 'left', title: __('Nickname'), operate: 'LIKE'},
                        {field: 'email', align: 'left',align: 'left',title: __('Email'),visible: false, operate: 'LIKE'},
                        {field: 'mobile',align: 'left', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'avatar',align: 'left', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'level',align: 'left', title: __('Level'),visible: false, operate: 'BETWEEN', sortable: true},
                        {field: 'gender',align: 'left', title: __('Gender'), visible: false, searchList: {1: __('Male'), 0: __('Female')}},
                        {field: 'score', align: 'left',title: __('Score'), operate: 'BETWEEN', sortable: true},
                        {field: 'money',align: 'left', title: __('Money'), operate: 'BETWEEN', sortable: true},
                        {field: 'pid',align: 'left', title: __('邀请码'), operate: 'BETWEEN', sortable: true},
                        {field: 'successions', align: 'left',title: __('Successions'), visible: false, operate: 'BETWEEN', sortable: true},
                        {field: 'maxsuccessions',align: 'left', title: __('Maxsuccessions'), visible: false, operate: 'BETWEEN', sortable: true},
                        {field: 'logintime',align: 'left', title: __('Logintime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'loginip',align: 'left', title: __('Loginip'),visible: false, formatter: Table.api.formatter.search},
                        {field: 'jointime',align: 'left', title: __('Jointime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'joinip',align: 'left', title: __('Joinip'),visible: false, formatter: Table.api.formatter.search},
                        {field: 'status',align: 'left', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        selectuser: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/selectuser',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                sortName: 'id',
                showToggle: false,
                showExport: false,
                columns: [
                    [
                        {field: 'id', title: __('Id'),align: 'left', sortable: true},
                        {field: 'avatar', title: __('Avatar'),align: 'left', events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'username', title: __('Username'),align: 'left', operate: 'LIKE'},
                        {field: 'nickname', title: __('Nickname'),align: 'left', operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'),align: 'left',visible: false, operate: 'LIKE'},
                        {field: 'status', title: __('Status'),align: 'left', formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {
                            field: 'operate', title: __('Operate'), events: {
                                'click .btn-chooseone': function (e, value, row, index) {
                                    var multiple = Backend.api.query('multiple');
                                    multiple = multiple == 'true' ? true : false;
                                    Fast.api.close({id: row.id, nickname: row.nickname});
                                },
                            }, formatter: function () {
                                return '<a href="javascript:;" class="btn btn-danger btn-chooseone btn-xs"><i class="fa fa-check"></i> ' + __('Choose') + '</a>';
                            }
                        }
                    ]
                ]
            });

            // 选中多个
            $(document).on("click", ".btn-choose-multi", function () {
                var urlArr = new Array();
                $.each(table.bootstrapTable("getAllSelections"), function (i, j) {
                    urlArr.push(j.url);
                });
                var multiple = Backend.api.query('multiple');
                multiple = multiple == 'true' ? true : false;
                Fast.api.close({url: urlArr.join(","), multiple: multiple});
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            require(['upload'], function (Upload) {
                Upload.api.plupload($("#toolbar .plupload"), function () {
                    $(".btn-refresh").trigger("click");
                });
            });
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
            gender: function (value) {
                return value==1 ? __('Male') : __('FeMale');
            }
        }
    };
    return Controller;
});