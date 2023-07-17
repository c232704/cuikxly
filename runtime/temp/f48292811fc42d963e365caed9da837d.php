<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:85:"/www/wwwroot/xjc.demo.hongcd.com/public/../application/admin/view/txjl/txjl/edit.html";i:1614055896;s:75:"/www/wwwroot/xjc.demo.hongcd.com/application/admin/view/layout/default.html";i:1588765310;s:72:"/www/wwwroot/xjc.demo.hongcd.com/application/admin/view/common/meta.html";i:1588765310;s:74:"/www/wwwroot/xjc.demo.hongcd.com/application/admin/view/common/script.html";i:1588765310;}*/ ?>
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
        <label for="c-money" class="control-label col-xs-12 col-sm-2"><?php echo __('提现金额'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-money" data-rule="required" class="form-control" type="text" value="<?php echo htmlentities($row['money']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-sxf" class="control-label col-xs-12 col-sm-2"><?php echo __('手续费'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-sxf" data-rule="required" class="form-control"  type="text"  value="<?php echo htmlentities($row['sxf']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-moneydz" class="control-label col-xs-12 col-sm-2"><?php echo __('实际到账'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-moneydz" data-rule="required" class="form-control" type="number" value="<?php echo htmlentities($row['moneydz']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-type" class="control-label col-xs-12 col-sm-2"><?php echo __('类型'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-type" data-rule="required" class="form-control" type="text" value="<?php echo htmlentities($row['type']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-name" class="control-label col-xs-12 col-sm-2"><?php echo __('提现姓名'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-name" data-rule="required" class="form-control" type="text" value="<?php echo htmlentities($row['name']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-cord" class="control-label col-xs-12 col-sm-2"><?php echo __('提现帐号'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-cord" data-rule="required" class="form-control" type="text" value="<?php echo htmlentities($row['cord']); ?>">
        </div>
    </div>
    
    <div class="form-group">
        <label for="c-iscl" class="control-label col-xs-12 col-sm-2"><?php echo __('是否审核'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[iscl]', ['1'=>__('未审核'), '2'=>__('已审核'), '3'=>__('已驳回')], $row['iscl']); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="c-memoj" class="control-label col-xs-12 col-sm-2"><?php echo __('备注'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-memoj" class="form-control" name="row[memoj]" type="text" value="<?php echo htmlentities($row['memoj']); ?>">
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

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>