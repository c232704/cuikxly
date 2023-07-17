<?php

return array (
  0 => 
  array (
    'name' => 'accessKeyId',
    'title' => 'AccessKey ID',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'LTAIH2H5ow3XxjX6',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'accessKeySecret',
    'title' => 'AccessKey Secret',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '47OgCZgItbW9SrkhyUeIz9Quene4ww',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'bucket',
    'title' => 'Bucket名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '6dbcn',
    'rule' => 'required;bucket',
    'msg' => '',
    'tip' => '阿里云OSS的空间名',
    'ok' => '',
    'extend' => 'data-rule-bucket="[/^[0-9a-z_\\-]{3,63}$/, \'请输入正确的Bucket名称\']"',
  ),
  3 => 
  array (
    'name' => 'endpoint',
    'title' => 'Endpoint',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'oss-cn-beijing.aliyuncs.com',
    'rule' => 'required;endpoint',
    'msg' => '',
    'tip' => '请填写从阿里云存储获取的Endpoint',
    'ok' => '',
    'extend' => 'data-rule-endpoint="[/^(?!http(s)?:\\/\\/).*$/, \'不能以http(s)://开头\']"',
  ),
  4 => 
  array (
    'name' => 'cdnurl',
    'title' => 'CDN地址',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'https://6dbcn.oss-cn-beijing.aliyuncs.com',
    'rule' => 'required;cdnurl',
    'msg' => '',
    'tip' => '请填写CDN地址，必须以http(s)://开头',
    'ok' => '',
    'extend' => 'data-rule-cdnurl="[/^http(s)?:\\/\\/.*$/, \'必需以http(s)://开头\']"',
  ),
  5 => 
  array (
    'name' => 'uploadmode',
    'title' => '上传模式',
    'type' => 'select',
    'content' => 
    array (
      'server' => '服务器中转(占用服务器带宽,可备份)',
    ),
    'value' => 'server',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  6 => 
  array (
    'name' => 'serverbackup',
    'title' => '服务器中转模式备份',
    'type' => 'radio',
    'content' => 
    array (
      1 => '备份(附件管理将产生2条记录)',
      0 => '不备份',
    ),
    'value' => '0',
    'rule' => '',
    'msg' => '',
    'tip' => '服务器中转模式下是否备份文件',
    'ok' => '',
    'extend' => '',
  ),
  7 => 
  array (
    'name' => 'savekey',
    'title' => '保存文件名',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '/uploads/{year}{mon}{day}/{filemd5}{.suffix}',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  8 => 
  array (
    'name' => 'expire',
    'title' => '上传有效时长',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '600',
    'rule' => 'required',
    'msg' => '',
    'tip' => '用户停留页面上传有效时长，单位秒',
    'ok' => '',
    'extend' => '',
  ),
  9 => 
  array (
    'name' => 'maxsize',
    'title' => '最大可上传',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '10M',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  10 => 
  array (
    'name' => 'mimetype',
    'title' => '可上传后缀格式',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'jpg,png,bmp,jpeg,gif,zip,rar,xls,xlsx,mp4',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  11 => 
  array (
    'name' => 'multiple',
    'title' => '多文件上传',
    'type' => 'bool',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  12 => 
  array (
    'name' => 'thumbstyle',
    'title' => '缩略图样式',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '用于后台列表缩略图样式，可使用：?x-oss-process=image/resize,m_lfit,w_120,h_90',
    'ok' => '',
    'extend' => '',
  ),
  13 => 
  array (
    'name' => 'chunking',
    'title' => '分片上传',
    'type' => 'radio',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  14 => 
  array (
    'name' => 'chunksize',
    'title' => '分片大小',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => '4194304',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  15 => 
  array (
    'name' => 'syncdelete',
    'title' => '附件删除时是否同步删除文件',
    'type' => 'bool',
    'content' => 
    array (
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  16 => 
  array (
    'name' => 'apiupload',
    'title' => 'API接口使用云存储',
    'type' => 'bool',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  17 => 
  array (
    'name' => 'noneedlogin',
    'title' => '免登录上传',
    'type' => 'checkbox',
    'content' => 
    array (
      'api' => 'API',
      'index' => '前台',
      'admin' => '后台',
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
