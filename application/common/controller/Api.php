<?php

namespace app\common\controller;

use app\common\library\Auth;
use think\Config;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Hook;
use think\Lang;
use think\Loader;
use think\Request;
use think\Response;
use think\Route;
use fast\Tree;
use think\Db;
use app\common\model\MoneyLog;
/**
 * API控制器基类
 */
class Api
{

    /**
     * @var Request Request 实例
     */
    protected $request;

    /**
     * @var bool 验证失败是否抛出异常
     */
    protected $failException = false;

    /**
     * @var bool 是否批量验证
     */
    protected $batchValidate = false;
    /**
     * 快速搜索时执行查找的字段
     */
    protected $searchFields = 'id';

    /**
     * 是否是关联查询
     */
    protected $relationSearch = false;
    /**
     * 是否开启数据限制
     * 支持auth/personal
     * 表示按权限判断/仅限个人
     * 默认为禁用,若启用请务必保证表中存在admin_id字段
     */
    protected $dataLimit = false;
    /**
     * @var array 前置操作方法列表
     */
    protected $beforeActionList = [];

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 权限Auth
     * @var Auth
     */
    protected $auth = null;

    /**
     * 默认响应输出类型,支持json/xml
     * @var string
     */
    protected $responseType = 'json';
    protected $pids=0;
    /**
     * 构造方法
     * @access public
     * @param Request $request Request 对象
     */
    public function __construct(Request $request = null)
    {
        $this->request = is_null($request) ? Request::instance() : $request;

        // 控制器初始化
        $this->_initialize();

        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ?
                    $this->beforeAction($options) :
                    $this->beforeAction($method, $options);
            }
        }
    }

    /**
     * 初始化操作
     * @access protected
     */
    protected function _initialize()
    {
        header("Access-Control-Allow-Origin: *");
        $site=config('site');
        if(input('version')==$site['azbb']){//版本强制更新
           $this->success('version10001',$site['azxz']); 
        }
        if (Config::get('url_domain_deploy')) {
            $domain = Route::rules('domain');
            if (isset($domain['api'])) {
                if (isset($_SERVER['HTTP_ORIGIN'])) {
                    header("Access-Control-Allow-Origin: " . $this->request->server('HTTP_ORIGIN'));
                    header('Access-Control-Allow-Credentials: true');
                    header('Access-Control-Max-Age: 86400');
                }
                if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
                    }
                    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                    }
                }
            }
        }

        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');

        $this->auth = Auth::instance();

        $modulename = $this->request->module();
        $controllername = strtolower($this->request->controller());
        $actionname = strtolower($this->request->action());

        // token
        
        $_gettoken=\think\Cookie::get('token');
        $gettoken=isset($_gettoken)?$_gettoken:$this->request->request("token");

        $token = $this->request->server('HTTP_TOKEN', $this->request->request('token',$_gettoken));

        $path = str_replace('.', '/', $controllername) . '/' . $actionname;
        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            //初始化
            $this->auth->init($token);
            //检测是否登录
            if (!$this->auth->isLogin()) {
                $this->error(__('Please login first'), null, 401);
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($path)) {
                    $this->error(__('You have no permission'), null, 403);
                }
            }
        } else {
            // 如果有传递token才验证是否登录状态
            if ($token) {
                $this->auth->init($token);
            }
        }

        $upload = \app\common\model\Config::upload();

        // 上传信息配置后
        Hook::listen("upload_config_init", $upload);

        Config::set('upload', array_merge(Config::get('upload'), $upload));

        // 加载当前控制器语言包
        $this->loadlang($controllername);
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadlang($name)
    {
        $name =  Loader::parseName($name);
        Lang::load(APP_PATH . $this->request->module() . '/lang/' . $this->request->langset() . '/' . str_replace('.', '/', $name) . '.php');
    }

    /**
     * 操作成功返回的数据
     * @param string $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为1
     * @param string $type   输出类型
     * @param array  $header 发送的 Header 信息
     */
    protected function success($msg = '', $data = null, $code = 1, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 操作失败返回的数据
     * @param string $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型
     * @param array  $header 发送的 Header 信息
     */
    protected function error($msg = '', $data = null, $code = 0, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed  $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型，支持json/xml/jsonp
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function result($msg, $data = null, $code = 0, $type = null, array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $data,
        ];
        // 如果未设置类型则自动判断
        $type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : $this->responseType);

        if (isset($header['statuscode'])) {
            $code = $header['statuscode'];
            unset($header['statuscode']);
        } else {
            //未设置状态码,根据code值判断
            $code = $code >= 1000 || $code < 200 ? 200 : $code;
        }
        $response = Response::create($result, $type, $code)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 前置操作
     * @access protected
     * @param  string $method  前置操作方法名
     * @param  array  $options 调用参数 ['only'=>[...]] 或者 ['except'=>[...]]
     * @return void
     */
    protected function beforeAction($method, $options = [])
    {
        if (isset($options['only'])) {
            if (is_string($options['only'])) {
                $options['only'] = explode(',', $options['only']);
            }

            if (!in_array($this->request->action(), $options['only'])) {
                return;
            }
        } elseif (isset($options['except'])) {
            if (is_string($options['except'])) {
                $options['except'] = explode(',', $options['except']);
            }

            if (in_array($this->request->action(), $options['except'])) {
                return;
            }
        }

        call_user_func([$this, $method]);
    }

    /**
     * 设置验证失败后是否抛出异常
     * @access protected
     * @param bool $fail 是否抛出异常
     * @return $this
     */
    protected function validateFailException($fail = true)
    {
        $this->failException = $fail;

        return $this;
    }
    public function config(){
        $site=config('site');
        $tree = Tree::instance();
        $this->model = model('app\common\model\Category');
        $tree->init(collection($this->model->where(['type'=>'type','status'=>'normal'])->order('weigh desc,id desc')->select())->toArray(), 'pid');
        $fenlei = $tree->getTreeList($tree->getTreeArray(0), 'name');

        $config['site']=$site;
        $config['fenlei']=$fenlei;
        
        $das=$tree->init(collection($this->model->where(['type'=>'shopmenu','status'=>'normal','flag'=>['like','%menu%']])->order('weigh desc,id desc')->select())->toArray(), 'pid');
        
        $config['shopmenu']= $tree->getTreeArray(0);
        if($config['shopmenu']){
            foreach ($config['shopmenu'] as $k=>$v){
                if(strpos($v['image'],'http') !== false){ 
                    $config['shopmenu'][$k]['image']=$v['image'];
                }else{
                    $config['shopmenu'][$k]['image']=$site['imgurl'].$v['image']; 
                }
                if(isset($v['url'])){
                    if(strpos($v['url'],'http') !== false){
                        $config['shopmenu'][$k]['url']='/pages/client/webva?url='.$v['url'];
                    }else{
                        $config['shopmenu'][$k]['url']=$v['url'];
                    } 
                }else{
                    $config['shopmenu'][$k]['url']='';
                }
            }
        }
        
        
        if(is_array(config('site.banner'))){
            $banner=config('site.banner');
            $background=config('site.background');
            $link=config('site.link');
            foreach ($banner as $k=>$v){
                if(strpos($v,'http') !== false){ 
                    $config['banner'][$k]['src']=$v;
                }else{
                    $config['banner'][$k]['src']=$site['imgurl'].$v; 
                }
                
                $config['banner'][$k]['background']=isset($background[$k])?$background[$k]:'#ffffff';
                
                if(isset($link[$k])){
                    $url=isset($link[$k])?$link[$k]:'';
                    if(strpos($link[$k],'http') !== false){
                        $config['banner'][$k]['link']='/pages/client/webva?url='.$url;
                    }else{
                        $config['banner'][$k]['link']=$url;
                    } 
                }else{
                    $config['banner'][$k]['link']='';
                }
                
               
            }
        }
        
        
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $times=[$beginToday,$endToday];
        $map['memo']='看广告赠送';
        $total = Db::name('user_money_log')
                ->whereTime('createtime', 'between', $times)
                ->where($map)
                ->count();
        $config['mrcsjr']=$total;
        $config['mrcs']=$site['mrcs'];
        
        $config['tels']=$site['tels'];
        $config['weixin']=$site['weixin'];
        $config['iskq']=$site['iskq'];
        $config['fxdj']=$site['fxdj'];
        $config['vipsj']=$site['vipsj'];
        $config['mbgColor']=$site['mbgColor'];
        $config['name']=$site['name'];
        return $config;
    }
    
    public function dailiyongjin($oid,$sid,$mid,$uid,$money,$tp){ //代理达人佣金结算
        $site=config('site');
        if($site['isdl']==1){
            Db::startTrans();
            try {
                $videolist=Db::name('videolist')->where('id',$sid)->find();
                $bid=isset($videolist['bid'])?$videolist['bid']:0;
                $this->dailiyj($oid,$sid,$mid,$bid,$money,$tp,1);
                Db::commit();
            }catch (Exception $e){
                Db::rollback();
                $this->error($e->getMessage());
            }  
        }
    }
    public function dailiyj($oid,$sid,$mid,$bid,$money,$tp,$c){
        $site=config('site');
        if($bid>0 and $c<=2){
            $vadmin=Db::name('admin')->where('id',$bid)->find();
            $islx=isset($vadmin['islx'])?$vadmin['islx']:0;
            $bl=0;
            if($islx==1){
                $bl=$site['dlfy'];
            }
            if($islx==2){
                $bl=$site['drbl'];
            }
            $this->dailiyj($oid,$sid,$mid,$vadmin['pid'],$money,$tp,$c+1);
            $moneys=$money*$bl;
            if($moneys>=0.01){
                $before = $vadmin['money'];
                $after = $vadmin['money'] + $moneys;
                Db::name('admin') ->where('id',$bid)->setInc('money', $moneys);
                Db::name('admin_money_log')->insertGetId(['admin_id' =>$bid, 'money' => $moneys,'createtime' => time(), 'before' => $before, 'sid' => $sid,'mid' => $mid, 'oid' => $oid, 'after' => $after, 'memo' => $tp]);
            }
            
        }
    }
    public function yongjin($oid,$uid,$money,$tp){
        $site=config('site');
        if($site['iskq']==1 and $site['fxdj']){
            foreach ($site['fxdj'] as $k=>$v){
                $this->pids=0;
                $this->get_parent_id($uid,$k,1);
                //echo $k.'-'.$this->pids.',';
                if($this->pids>0){
                   $this->yongjinjs($oid,$this->pids,$money*$v,$k,$tp);
                }
            }    
        }
    }
    public function get_parent_id($pid,$cj,$sum){
        $User=model('User')->where(['id'=>$pid])->find();
        if($User){
            $this->pids = $User['pid'];
            if($sum<$cj){
               $this->get_parent_id($User['pid'],$cj,$sum+1); 
            }
        }
    }
    public function yongjinjs($oid,$uid,$money,$cj,$tp='佣金结算'){
            $user = \app\common\model\User::getById($uid);
            $before = $user->money;
            $after = $user->money + $money;
            $user->save(['money' => $after]);
            //写入日志
            MoneyLog::create(['user_id' => $user['id'], 'money' => $money, 'before' => $before,'oid' => $oid, 'after' => $after, 'memo' => $cj.$tp]);
    }
    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @param  mixed        $callback 回调方法（闭包）
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate($data, $validate, $message = [], $batch = false, $callback = null)
    {
        if (is_array($validate)) {
            $v = Loader::validate();
            $v->rule($validate);
        } else {
            // 支持场景
            if (strpos($validate, '.')) {
                list($validate, $scene) = explode('.', $validate);
            }

            $v = Loader::validate($validate);

            !empty($scene) && $v->scene($scene);
        }

        // 批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }
        // 设置错误信息
        if (is_array($message)) {
            $v->message($message);
        }
        // 使用回调验证
        if ($callback && is_callable($callback)) {
            call_user_func_array($callback, [$v, &$data]);
        }

        if (!$v->check($data)) {
            if ($this->failException) {
                throw new ValidateException($v->getError());
            }

            return $v->getError();
        }

        return true;
    }
    /**
     * 生成查询所需要的条件,排序方式
     * @param mixed   $searchfields   快速查询的字段
     * @param boolean $relationSearch 是否关联查询
     * @return array
     */
    protected function buildparams($searchfields = null, $relationSearch = null)
    {
        $searchfields = is_null($searchfields) ? $this->searchFields : $searchfields;
        $relationSearch = is_null($relationSearch) ? $this->relationSearch : $relationSearch;
        $search = $this->request->get("search", '');
        $filter = $this->request->get("filter", '');
        $op = $this->request->get("op", '', 'trim');
        $sort = $this->request->get("sort", !empty($this->model) && $this->model->getPk() ? $this->model->getPk() : 'id');
        $order = $this->request->get("order", "DESC");
        $offset = $this->request->get("offset", 0);
        $limit = $this->request->get("limit", 0);
        $filter = (array)json_decode($filter, true);
        $op = (array)json_decode($op, true);
        $filter = $filter ? $filter : [];
        $where = [];
        $tableName = '';
        if ($relationSearch) {
            if (!empty($this->model)) {
                $name = \think\Loader::parseName(basename(str_replace('\\', '/', get_class($this->model))));
                $name = $this->model->getTable();
                $tableName = $name . '.';
            }
            $sortArr = explode(',', $sort);
            foreach ($sortArr as $index => & $item) {
                $item = stripos($item, ".") === false ? $tableName . trim($item) : $item;
            }
            unset($item);
            $sort = implode(',', $sortArr);
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $where[] = [$tableName . $this->dataLimitField, 'in', $adminIds];
        }
        if ($search) {
            $searcharr = is_array($searchfields) ? $searchfields : explode(',', $searchfields);
            foreach ($searcharr as $k => &$v) {
                $v = stripos($v, ".") === false ? $tableName . $v : $v;
            }
            unset($v);
            $where[] = [implode("|", $searcharr), "LIKE", "%{$search}%"];
        }
        foreach ($filter as $k => $v) {
            $sym = isset($op[$k]) ? $op[$k] : '=';
            if (stripos($k, ".") === false) {
                $k = $tableName . $k;
            }
            $v = !is_array($v) ? trim($v) : $v;
            $sym = strtoupper(isset($op[$k]) ? $op[$k] : $sym);
            switch ($sym) {
                case '=':
                case '<>':
                    $where[] = [$k, $sym, (string)$v];
                    break;
                case 'LIKE':
                case 'NOT LIKE':
                case 'LIKE %...%':
                case 'NOT LIKE %...%':
                    $where[] = [$k, trim(str_replace('%...%', '', $sym)), "%{$v}%"];
                    break;
                case '>':
                case '>=':
                case '<':
                case '<=':
                    $where[] = [$k, $sym, intval($v)];
                    break;
                case 'FINDIN':
                case 'FINDINSET':
                case 'FIND_IN_SET':
                    $where[] = "FIND_IN_SET('{$v}', " . ($relationSearch ? $k : '`' . str_replace('.', '`.`', $k) . '`') . ")";
                    break;
                case 'IN':
                case 'IN(...)':
                case 'NOT IN':
                case 'NOT IN(...)':
                    $where[] = [$k, str_replace('(...)', '', $sym), is_array($v) ? $v : explode(',', $v)];
                    break;
                case 'BETWEEN':
                case 'NOT BETWEEN':
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr)) {
                        continue 2;
                    }
                    //当出现一边为空时改变操作符
                    if ($arr[0] === '') {
                        $sym = $sym == 'BETWEEN' ? '<=' : '>';
                        $arr = $arr[1];
                    } elseif ($arr[1] === '') {
                        $sym = $sym == 'BETWEEN' ? '>=' : '<';
                        $arr = $arr[0];
                    }
                    $where[] = [$k, $sym, $arr];
                    break;
                case 'RANGE':
                case 'NOT RANGE':
                    $v = str_replace(' - ', ',', $v);
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr)) {
                        continue 2;
                    }
                    //当出现一边为空时改变操作符
                    if ($arr[0] === '') {
                        $sym = $sym == 'RANGE' ? '<=' : '>';
                        $arr = $arr[1];
                    } elseif ($arr[1] === '') {
                        $sym = $sym == 'RANGE' ? '>=' : '<';
                        $arr = $arr[0];
                    }
                    $where[] = [$k, str_replace('RANGE', 'BETWEEN', $sym) . ' time', $arr];
                    break;
                case 'LIKE':
                case 'LIKE %...%':
                    $where[] = [$k, 'LIKE', "%{$v}%"];
                    break;
                case 'NULL':
                case 'IS NULL':
                case 'NOT NULL':
                case 'IS NOT NULL':
                    $where[] = [$k, strtolower(str_replace('IS ', '', $sym))];
                    break;
                default:
                    break;
            }
        }
        $where = function ($query) use ($where) {
            foreach ($where as $k => $v) {
                if (is_array($v)) {
                    call_user_func_array([$query, 'where'], $v);
                } else {
                    $query->where($v);
                }
            }
        };
        return [$where, $sort, $order, $offset, $limit];
    }
    public function pissrc($str){
        if(!$str){
           return '';
        }
        $site=config('site');
        if(strpos($str,'http') !== false){ 
            $str=$str;
        }else{
            $str=$site['imgurl'].$str; 
        }
        return $str;
    }
    /**
     * 获取数据限制的管理员ID
     * 禁用数据限制时返回的是null
     * @return mixed
     */
    protected function getDataLimitAdminIds()
    {
        if (!$this->dataLimit) {
            return null;
        }
        if ($this->auth->isSuperAdmin()) {
            return null;
        }
        $adminIds = [];
        if (in_array($this->dataLimit, ['auth', 'personal'])) {
            $adminIds = $this->dataLimit == 'auth' ? $this->auth->getChildrenAdminIds(true) : [$this->auth->id];
        }
        return $adminIds;
    }
}
