<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:90:"/www/wwwroot/xjc.demo.hongcd.com/public/../application/admin/view/video/videolist/add.html";i:1678075184;s:75:"/www/wwwroot/xjc.demo.hongcd.com/application/admin/view/layout/default.html";i:1588765310;s:72:"/www/wwwroot/xjc.demo.hongcd.com/application/admin/view/common/meta.html";i:1588765310;s:74:"/www/wwwroot/xjc.demo.hongcd.com/application/admin/view/common/script.html";i:1588765310;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
    <div class="form-group">
        <label for="c-name" class="control-label col-xs-12 col-sm-2"><?php echo __('标题'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-name" data-rule="required" class="form-control" name="row[name]" type="text" value="">
        </div>
    </div>
    
    <div class="form-group" style="display:none;">
        <label for="c-bid" class="control-label col-xs-12 col-sm-2"><?php echo __('bid'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-bid" data-rule="required" readonly="readonly" class="form-control" name="row[bid]" type="text" value="<?php echo $id; ?>">
        </div>
    </div>
    
    <div class="form-group">
        <label for="c-lx" class="control-label col-xs-12 col-sm-2"><?php echo __('类型'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <!--<?php echo build_radios('row[lx]', ['1'=>__('视频'), '2'=>__('音频'), '3'=>__('小说'), '4'=>__('图片'), '5'=>__('图文')], '1'); ?>-->
            <?php echo build_radios('row[lx]', ['1'=>__('视频'), '4'=>__('图片')], '1'); ?>
        </div>
    </div>
    <div id="spwz">
        <div class="form-group">
            <label for="c-type" class="control-label col-xs-12 col-sm-2"><?php echo __('视频分类'); ?>:</label>
            <div class="col-xs-12 col-sm-4">
               <?php echo build_select('row[type][]', $groupdata, null, ['class'=>'form-control selectpicker', 'multiple'=>'']); ?>
            </div>
        </div>
        <div class="form-group">
            <label for="c-adddd" class="control-label col-xs-12 col-sm-2"><?php echo __('视频地区'); ?>:</label>
            <div class="col-xs-12 col-sm-4">
               <?php echo build_select('row[adddd][]', $adddddata, null, ['class'=>'form-control selectpicker', 'multiple'=>'']); ?>
            </div>
        </div>
        <div class="form-group">
            <label for="c-yearq" class="control-label col-xs-12 col-sm-2"><?php echo __('视频年份'); ?>:</label>
            <div class="col-xs-12 col-sm-4">
               <?php echo build_select('row[yearq]', $yearqdata, null, ['class'=>'form-control']); ?>
            </div>
        </div>
    </div>
    <div id="ypwz" style="display:none;">
        <div class="form-group">
            <label for="c-type2" class="control-label col-xs-12 col-sm-2"><?php echo __('音频分类'); ?>:</label>
            <div class="col-xs-12 col-sm-4">
               <?php echo build_select('row[type2][]', $type2data, null, ['class'=>'form-control selectpicker', 'multiple'=>'']); ?>
            </div>
        </div>
    </div>
    <div id="xswz" style="display:none;">
        <div class="form-group">
            <label for="c-type3" class="control-label col-xs-12 col-sm-2"><?php echo __('小说分类'); ?>:</label>
            <div class="col-xs-12 col-sm-4">
               <?php echo build_select('row[type3][]', $type3data, null, ['class'=>'form-control selectpicker', 'multiple'=>'']); ?>
            </div>
        </div>
    </div> 
    <div id="tpwz" style="display:none;">
        <div class="form-group">
            <label for="c-type4" class="control-label col-xs-12 col-sm-2"><?php echo __('图片分类'); ?>:</label>
            <div class="col-xs-12 col-sm-4">
               <?php echo build_select('row[type4][]', $type4data, null, ['class'=>'form-control selectpicker', 'multiple'=>'']); ?>
            </div>
        </div>
    </div>
    <div id="twfl" style="display:none;">
        <div class="form-group">
            <label for="c-type4" class="control-label col-xs-12 col-sm-2"><?php echo __('图文分类'); ?>:</label>
            <div class="col-xs-12 col-sm-4">
               <?php echo build_select('row[type5][]', $type5data, null, ['class'=>'form-control selectpicker', 'multiple'=>'']); ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="c-avatar" class="control-label col-xs-12 col-sm-2"><?php echo __('缩略图'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-avatar" data-rule="" class="form-control" size="50" name="row[img]" type="text" value="">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-avatar" class="btn btn-danger plupload" data-input-id="c-avatar" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-avatar"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-avatar" class="btn btn-primary fachoose" data-input-id="c-avatar" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-avatar"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-avatar"></ul>
        </div>
    </div>
    <div class="form-group" id="tpwzpic" style="display:none;">
        <label for="c-info1" class="control-label col-xs-12 col-sm-2"><?php echo __('图片集'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-info1" data-rule="" class="form-control" size="50" name="row[pic]" type="hidden" value="">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-info1" class="btn btn-danger plupload" data-input-id="c-info1" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-info1"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-info1" class="btn btn-primary fachoose" data-input-id="c-info1" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-info1"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-info1"></ul>
        </div>
    </div>
    <div class="form-group">
        <label for="c-uname" class="control-label col-xs-12 col-sm-2"><?php echo __('用户ID'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <div class="input-group">
                <input id="c-uname"  class="form-control" size="50" name="row[uname]" type="text" value="">
                <input id="c-uid" class="form-control" size="50" name="row[uid]" type="hidden" value="">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="fachoose-name" class="btn btn-primary fachooseuser" data-uid="c-uid" data-uname="c-uname"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="c-price" class="control-label col-xs-12 col-sm-2"><?php echo __('普通价格'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-price" data-rule="required" class="form-control" name="row[price]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label for="c-vipprice" class="control-label col-xs-12 col-sm-2"><?php echo __('VIP价格'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-vipprice" data-rule="required" class="form-control" name="row[vipprice]" type="text" value="">
        </div>
    </div>
    
    <div class="form-group">
        <label for="c-text" class="control-label col-xs-12 col-sm-2"><?php echo __('简介'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-text" data-rule="required" class="form-control" name="row[text]" type="text" placeholder="简介" value="">
        </div>
    </div>
    <div class="form-group">
        <label for="c-story" class="control-label col-xs-12 col-sm-2"><?php echo __('剧情介绍'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-story" class="form-control" placeholder="剧情介绍" name="row[story]"></textarea>
        </div>
    </div>
    
    <div class="form-group">
        <label for="c-ishot" class="control-label col-xs-12 col-sm-2"><?php echo __('是否推荐'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[ishot]', ['1'=>__('未推荐'), '2'=>__('已推荐')], '1'); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="c-info" class="control-label col-xs-12 col-sm-2"><?php echo __('详情'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-info" data-rule="required" class="form-control editor" name="row[info]"></textarea>
        </div>
    </div> 
    <div class="form-group">
        <label for="content" class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[status]', ['normal'=>__('Normal'), 'hidden'=>__('Hidden')], 'normal'); ?>
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>
<script src="/assets/libs/jquery/dist/jquery.min.js?v=1619157096"></script>
<script src="/assets/libs/fastadmin-layer/dist/layer.js?v=1619157592"></script>
<script>
    // 点击单选按钮后触发，即，我们选择“男”时，触发一个事件，弹出选中的值
    $("input[name='row[lx]']").click(function(){
        var lx = $(this).val();
        if(lx==1){
            $('#spwz').show();
            $('#ypwz').hide();
            $('#xswz').hide();
            $('#tpwz').hide();
            $('#tpwzpic').hide();
            $('#twfl').hide();
        }else{
            $('#spwz').hide();
        }
        if(lx==2){
            $('#spwz').hide();
            $('#ypwz').show();
            $('#xswz').hide();
            $('#tpwz').hide();
            $('#tpwzpic').hide();
            $('#twfl').hide();
        }else{
            $('#ypwz').hide();
        }
        if(lx==3){
            $('#spwz').hide();
            $('#ypwz').hide();
            $('#xswz').show();
            $('#tpwz').hide();
            $('#tpwzpic').hide();
            $('#twfl').hide();
        }else{
            $('#xswz').hide();
        }
        if(lx==4){
            $('#spwz').hide();
            $('#ypwz').hide();
            $('#xswz').hide();
            $('#twfl').hide();
            $('#tpwz').show();
            $('#tpwzpic').show();
        }else{
            $('#tpwz').hide();
            $('#tpwzpic').hide();
        }
        if(lx==5){
            $('#spwz').hide();
            $('#ypwz').hide();
            $('#xswz').hide();
            $('#tpwz').hide();
            $('#tpwzpic').hide();
            $('#twfl').show();
        }else{
            $('#twfl').hide();
        }
        //alert(lx);
    });
</script>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>