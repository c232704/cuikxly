<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:89:"/www/wwwroot/xjc.demo.hongcd.com/public/../application/admin/view/general/txmx/index.html";i:1678088652;s:75:"/www/wwwroot/xjc.demo.hongcd.com/application/admin/view/layout/default.html";i:1588765310;s:72:"/www/wwwroot/xjc.demo.hongcd.com/application/admin/view/common/meta.html";i:1588765310;s:74:"/www/wwwroot/xjc.demo.hongcd.com/application/admin/view/common/script.html";i:1588765310;}*/ ?>
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
                                <style>
    .profile-avatar-container {
        position: relative;
        width: 100px;
        margin: 0 auto;
    }

    .profile-avatar-container .profile-user-img {
        width: 100px;
        height: 100px;
    }

    .profile-avatar-container .profile-avatar-text {
        display: none;
    }

    .profile-avatar-container:hover .profile-avatar-text {
        display: block;
        position: absolute;
        height: 100px;
        width: 100px;
        background: #444;
        opacity: .6;
        color: #fff;
        top: 0;
        left: 0;
        line-height: 100px;
        text-align: center;
    }

    .profile-avatar-container button {
        position: absolute;
        top: 0;
        left: 0;
        width: 100px;
        height: 100px;
        opacity: 0;
    }
</style>
<div class="row animated fadeInRight">
    <div class="col-md-4">
        <div class="box box-success">
            <div class="panel-heading">
                <?php echo __('Profile'); ?>
            </div>
            <div class="panel-body">

                <form id="update-form" role="form" data-toggle="validator" method="POST" action="<?php echo url('general.txmx/update'); ?>">
                    <div class="box-body box-profile">
                        <div class="form-group">
                            <label for="money" class="control-label"><?php echo __('余额'); ?>:</label>
                            <input type="text" class="form-control" id="money" name="row[money]" value="<?php echo htmlentities($admin['money']); ?>" data-rule="required"  disabled />
                        </div>
                        <div class="form-group">
                            <label for="money" class="control-label"><?php echo __('提现金额'); ?>:</label>
                            <input type="number" class="form-control" id="money" name="row[money]" value="" data-rule="required"/>
                        </div>
                        <div class="form-group">
                            <label for="name" class="control-label"><?php echo __('姓名'); ?>:</label>
                            <input type="text" class="form-control" id="name" name="row[name]" value="" data-rule="required"/>
                        </div>
                        <div class="form-group">
                            <label for="type" class="control-label"><?php echo __('开户行/支付宝'); ?>:</label>
                            <input type="text" class="form-control" id="type" name="row[type]" value="" data-rule="required"/>
                        </div>
                        <div class="form-group">
                            <label for="cord" class="control-label"><?php echo __('卡号/帐号'); ?>:</label>
                            <input type="text" class="form-control" id="cord" name="row[cord]" value="" data-rule="required"/>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success"><?php echo __('提现'); ?></button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>
    <div class="col-md-8">
        <div class="panel panel-default panel-intro panel-nav">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#one" data-toggle="tab"><i class="fa fa-list"></i> <?php echo __('提现明细'); ?></a></li>
                </ul>
            </div>
            <div class="panel-body">
                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="one">
                        <div class="widget-body no-padding">
                            <div id="toolbar" class="toolbar">
                                <?php echo build_toolbar('refresh'); ?>
                            </div>
                            <table id="table" class="table table-striped table-bordered table-hover" width="100%">

                            </table>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>