define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form, Upload) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "general/txmx/index",
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
                        {field: 'id', title: __('Id'),align: 'left',align: 'left', sortable: true},
                        {field: 'money', title: __('提现金额'),align: 'left', operate: '='},
                        {field: 'sxf', title: __('提现手续费'),align: 'left', operate: '='},
                        
                        {field: 'type', title: __('类型'),align: 'left', operate: 'LIKE'},
                        {field: 'cord', title: __('提现帐号'),align: 'left', operate: 'LIKE'},
                        {field: 'name', title: __('提现姓名'),align: 'left', operate: 'LIKE'},
                        {field: 'moneydz', title: __('实际到账'),align: 'left', operate: '='},
                        {field: 'createtime', title: __('提现时间'),align: 'left', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'memoj', title: __('备注'),align: 'left', operate: 'LIKE'},
                        {field: 'iscl', title: __('是否审核'),align: 'left', formatter: Controller.api.formatter.iscl, searchList: {1: __('未审核'), 2:__('已审核'), 3:__('已驳回')}},
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
        api: {
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