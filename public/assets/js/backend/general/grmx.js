define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form, Upload) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "general/grmx/index",
                    "add_url": "",
                    "edit_url": "",
                    "del_url": "",
                    "multi_url": "",
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'id', title: 'ID'},
                        {field: 'memo', title: __('Title')},
                        {field: 'money', title: __('金额'), align: 'left'},
                        {field: 'oid', title: __('订单id')},
                        {field: 'createtime', title: __('时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                    ]
                ],
                commonSearch: false
            });

            // 为表格绑定事件
            Table.api.bindevent(table);//当内容渲染完成后

            // 给上传按钮添加上传成功事件
            $("#plupload-avatar").data("upload-success", function (data) {
                var url = Backend.api.cdnurl(data.url);
                $(".profile-user-img").prop("src", url);
                Toastr.success("上传成功！");
            });
            
            // 给表单绑定事件
            Form.api.bindevent($("#update-form"), function () {
                $("input[name='row[password]']").val('');
                var url = Backend.api.cdnurl($("#c-avatar").val());
                top.window.$(".user-panel .image img,.user-menu > a > img,.user-header > img").prop("src", url);
                return true;
            });
        },
    };
    return Controller;
});