<?php

namespace app\member\service;
use think\Db;
use think\Hook;
use think\Model;
use gboy\IDCard;

class Member extends Model{


    protected  $reg_password='';
    protected  $two_password='';
    protected  $pid=0;
    protected  $group_id=0;

    protected $wallets_db;

    public function initialize()
    {
       $this->model = model('member/Member');

       $this->wallets_db=db('member_wallets');
    }


    /**
     * 用户初始化
     * @return array
     */
    public function inits() {

        $_member = [
            'id' => 0,
            'username' => '游客',
            'group_id' => 0,
            'email' => '',
            'mobile' => '',
            'money' => 0,
            'integral' => 0,
            'exp' => 0
        ];
        $authkey = session('gboy_member_auth');
        if ($authkey) {



            list($uid, $rand,$login_key) = explode("\t", authcode($authkey));


            $_member = $this->get_find(['id'=>$uid]);
            if($_member){
                $_member=$_member->toArray();
            }
        }
		
        //hook('memberInit',$_member);
        return $_member;
    }


    public function get_list($sqlmap,$field='*'){

        /*
        $page=input('page/d');


        $subQuery = $this->model->field('id')->order('id desc')->page($page)->limit(20)->buildSql();

        $lists=$this->model->table($subQuery)->alias('a')->join(' member b ','a.id=b.id','left')->paginate();
        */

        $lists = $this->model->field($field)->where($sqlmap)->order('id desc')->paginate();




        return $lists;

    }


    public function get_find($sqlmap=[],$field=''){

        if(!$result=$this->model->where($sqlmap)->field($field)->find()){
            $this->errors=lang('_param_error_');
            return false;
        }



        return $result;

    }


    public function register(array $params){



		
         Hook::listen('registerInit',$params);

        if($params['reg_status']===0){
            $this->errors = $params['reg_error'];
            return false;
        }

        if(empty($params)) {
            $this->errors = lang('_param_error_');
            return false;
        }

        foreach( $params as $k => $v ) {
            $method = '_valid_'.$k;
            if($k == 'vcode'){
                if(method_exists($this,$method) && $this->$method($v,$params['mobile']) === false) return false;
            }else{
                if(method_exists($this,$method) && $this->$method($v) === false) return false;
            }
            $params[$k] = trim($v);
        }

        if(empty($params['vcode'])){
            $this->errors='请输入手机验证码';
            return false;
        }



        $result=check_sms($params['mobile'],$params['vcode'],'register');

        if($result!==true){

            $this->errors='手机验证码不正确';
            return false;
        }


        $data = $params;

        /*
        $reg_user_fields = unserialize(config('cache.reg_user_fields'));


        $sms_enabled = model('notify/Notify')->where(['code'=>'sms','enabled'=>1])->value('code');

        $sms_reg = false;

        if($sms_enabled){
            $sqlmap=[];
            $sqlmap['id'] = 'sms';
            $sqlmap['enabled'] = ['like','%register_validate%'];
            $sms_reg = model('notify/Template')->where($sqlmap)->value('id');
        }

        if(in_array('phone',$reg_user_fields) && $sms_reg){
            $sqlmap = [];
            $sqlmap['mobile'] = $params['mobile'];
            $sqlmap['dateline'] = ['EGT',time()-300];
            $vcode = db('vcode')->where($sqlmap)->order('dateline desc')->value('vcode');
            if($vcode != $params['vcode']){
                $this->errors = lang('captcha_error');
                return false;
            }else{
                $data['mobile_status'] = 1;
            }
        }
        */


        $data['username']=$params['mobile'];
        $data['encrypt'] = random(6);
        $data['register_ip']=getip();
        $data['register_time']=time();
        $data['company']=$params['company'];
        $data['group_id']=$params['group_id'];
        $data['password'] = $this->create_password($data['password'],$data['encrypt']);

        hook('before_register',$data);
        if($data['_callback'] === false){
            $this->errors = $params['_message'];
            return false;
        }

        $this->model->startTrans();
        $this->model->allowField(true)->isUpdate(false)->data($data)->save();
        $data['reg_id']=$this->model->getLastInsID();





        if(!$data['reg_id']){
            $this->wallets_db->rollBack();
            $this->model->rollBack();
            $this->errors='注册失败，重新再试';
            return false;
        }

        $this->model->commit();
        hook('afterRegister',$data);


        $this->dologin($data['reg_id']);

        return true;

    }


    public function login($account, $password) {

        if (empty($account)) {
            $this->errors = '请输入账号';
            return false;
        }



        if (empty($password)) {
            $this->errors = lang('login_password_empty');
            return false;
        }
      
        $member = [];
        hook('before_login', $member);
        if (empty($member)) {

            $sqlmap = [];

            /*
            if (is_mobile($account)) {
                $sqlmap['mobile'] = $account;
                $sqlmap['mobile_status'] = 1;
            } elseif (is_email($account)) {
                $sqlmap['email'] = $account;
                $sqlmap['email_status'] = 1;
            } else {
                $sqlmap['username'] = $account;
            }

            $sqlmap['mobile'] = $account;
            $sqlmap['mobile_status'] = 1;
            */
            //$sqlmap = array_merge($sqlmap, $this->sqlmap);

            $sqlmap['username']=$account;
            $member = $this->model->where($sqlmap)->find();

            if (!$member || $this->create_password($password,$member['encrypt']) != $member['password']) {
                $this->errors = lang('username_not_exist');
                return false;
            }

            //到期自动解封
            if($member['is_lock']==1){
				 $this->errors = '封号不可登录';
                 return false;
            }

        }


        hook_exec('app\\member\\hook\\Hook','afterLogin',$member);
        $this->dologin($member['id']);

        return true;
    }


    /**
     * 判断并实现会员自动升级
     * @param  int $mid 会员主键
     * @return [bool]
     */
    public function dologin($mid) {
        if((int) $mid < 1){
            $this->errors = lang('_param_error_');
            return FALSE;
        }

        $rand = random(6);
        $login_key=random(32);
        $auth = authcode($mid."\t".$rand."\t".$login_key, 'ENCODE');

        $out_time=config('cache.member_login_out');

        $out_time=$out_time?$out_time:null;

		
	
		
        session('gboy_member_auth', $auth);
        $login_info = array(
            'id' => $mid,
            'login_key'=>$login_key,
            'login_time' => time(),
            'login_ip'	=> getip()
            //'login_num' => ['exp','login_num+1']
        );
        $this->model->isUpdate(true)->save($login_info);
        $this->model->isUpdate(true)->where(['id'=>$mid])->setInc('login_num',1);
        return true;
    }


    public function edit($data, $isupdate = false, $valid = true, $msg = []){

        if($isupdate){


            $member=$this->where(['id'=>$data['id']])->field('encrypt,auth_time,is_auth')->find();

            $encrypt=$member['encrypt'];




            if(empty($encrypt)){
                $encrypt = random(6);

                $data['encrypt']=$encrypt;
            }



            unset($data['username']);
            if(!$data['password']){
                unset($data['password']);
            }else{
                $data['password']=$this->create_password($data['password'],$encrypt);
            }

            if(!$data['two_password']){
                unset($data['two_password']);
            }else{
                $data['two_password']=$this->create_password($data['two_password'],$encrypt);
            }

            //封号和矿机
            //db('kuangji_order')->where(['buy_id'=>$data['id']])->data(['member_status'=>$data['islock']])->update();






        }else{
            $data['encrypt']=random(6);
            $data['password']=$this->create_password($data['password'],$data['encrypt']);

        }


        $result = $this->model->except('id,pid,path_id')->validate($valid, $msg)->isupdate($isupdate)->save($data);

        if ($result === false) {

            $this->errors = $this->model->getError();
            return false;
        }

        return true;
    }




    /**
     * 修改登录密码
     * @param $mid 用户ID
     * @param $params 参数
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit_pwd($mid,$params){


        if(empty($params)) {
            $this->errors = lang('_param_error_');
            return false;
        }

        if(empty($params['old_pwd'])){
            $this->errors='请输入旧密码';
            return false;
        }

        if(empty($params['password'])){
            $this->errors='请输入新密码';
            return false;
        }

        if(empty($params['repassword'])){
            $this->errors='请输入确认新密码';
            return false;
        }

        if($params['repassword']!==$params['password']){
            $this->errors='两次输入的密码不一致';
            return false;
        }

        if(!$this->_valid_password($params['password'])){
            $this->errors=$this->errors;
            return false;
        }

        $member=$this->model->where(['id'=>$mid])->field('encrypt,password')->find();

        if($this->create_password($params['old_pwd'],$member['encrypt'])!==$member['password']){
            $this->errors='旧密码不正确';
            return false;
        }


        if(!$this->model->isupdate(true)->where(['id'=>$mid])->data(['password'=>$this->create_password($params['password'],$member['encrypt'])])->update()){
            $this->errors='修改失败了';
            return false;
        }

        //退出登录
        $this->logout();

        return true;



    }


    /**
     * 找回登录密码
     * @param $params 参数
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_login_pwd($params){


        if(empty($params)) {
            $this->errors = lang('_param_error_');
            return false;
        }


        if(empty($params['mobile'])){
            $this->errors='请输入手机号';
            return false;
        }

        if(!is_mobile($params['mobile'])){
            apijson('手机号不正确');
        }

        if(!$this->where(['username'=>$params['mobile']])->value('id')){
            $this->errors='此账号不存在';
            return false;
        }



        if(empty($params['password'])){
            $this->errors='请输入新密码';
            return false;
        }

        if(empty($params['repassword'])){
            $this->errors='请输入确认新密码';
            return false;
        }

        if($params['repassword']!==$params['password']){
            $this->errors='两次输入的密码不一致';
            return false;
        }

        if(!$this->_valid_password($params['password'])){
            $this->errors=$this->errors;
            return false;
        }

        if(empty($params['code'])){
            $this->errors='请输入手机验证码';
            return false;
        }

        $result=check_sms($params['mobile'],$params['code'],'getpwd');

        if($result!==true){
            $this->errors='手机验证码不正确';
            return false;
        }

        $member=$this->model->where(['username'=>$params['mobile']])->field('id,encrypt,password')->find();

        $encrypt = $member['encrypt'];

        if(empty($encrypt)){
            $encrypt = random(6);
        }



        if(!$this->model->isupdate(true)->where(['id'=>$member['id']])->data(['encrypt'=>$encrypt,'password'=>$this->create_password($params['password'],$encrypt)])->update()){
            $this->errors='修改失败了';
            return false;
        }


        return true;



    }


    /**
     * 修改交易密码
     * @param $mid 用户ID
     * @param $params 参数
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit_twopwd($mid,$params){

        $member=$this->model->where(['id'=>$mid])->field('username,encrypt,password,two_password')->find();

        if(empty($params)) {
            $this->errors = lang('_param_error_');
            return false;
        }

        /*
        if($member['two_password']){

            //已设置交易密码才验证，否则无需验证
            if(empty($params['old_pwd'])){
                $this->errors='请输入旧密码';
                return false;
            }
        }
        */





        if(empty($params['password'])){
            $this->errors='请输入新密码';
            return false;
        }

        if(empty($params['repassword'])){
            $this->errors='请输入确认新密码';
            return false;
        }

        if($params['repassword']!==$params['password']){
            $this->errors='两次输入的密码不一致';
            return false;
        }

        if(!$this->_valid_password($params['password'])){
            $this->errors=$this->errors;
            return false;
        }

        if(empty($params['code'])){
            $this->errors='请输入手机验证码';
            return false;
        }


        $result=check_sms($member['username'],$params['code'],'tpwd');

        if($result!==true){
            $this->errors='验证码不正确';
            return false;
        }

        /*
        if($member['two_password']) {

            if ($this->create_password($params['old_pwd'], $member['encrypt']) !== $member['two_password']) {
                $this->errors = '旧密码不正确';
                return false;
            }

        }
        */


        //登录密码和交易密码不能一致

        $two_password=$this->create_password($params['password'],$member['encrypt']);

        if($two_password===$member['password']){
            $this->errors='交易密码不能和登录密码一样';
            return false;
        }


        if(!$this->model->isupdate(true)->where(['id'=>$mid])->data(['two_password'=>$two_password])->update()){
            $this->errors='修改失败了';
            return false;
        }


        return true;



    }



    /**
     *
     * 退出登录
     * @return mixed
     */
    public function logout() {
        hook('after_logout');
        return session('gboy_member_auth', null);
    }



    /**
     * @param array $ids id主键
     * @return bool
     */
    public function del($sqlmap){

        if(empty($sqlmap)){
            $this->errors = lang('_param_error_');
            return false;
        }

        $this->model->destroy($sqlmap);

        return true;
    }


    /**
     * 登录密码
     * @param $password 密码
     * @param $encrypt 令牌
     * @return bool|string
     */
    public function create_password($password,$encrypt) {


        $salt = '$2a$11$' . substr(md5($password.$encrypt), 5, 23);
        return substr(crypt($password, $salt),7);

    }


    private function _valid_group_id($value) {

        if(empty($value)){
            $this->errors='请选择注册类型';
            return false;
        }
        if(!in_array($value,[1,2,3])){
            $this->errors='注册类型不正确';
            return false;
        }



        $this->group_id=$value;
        return true;
    }


    /* 校验用户名 */
    private function _valid_username($value) {
        if(strlen($value) < 3 || strlen($value) > 15) {
            $this->errors = lang('username_length_require');
            return false;
        }

        $setting = config('cache');

        $censorexp = '/^('.str_replace(['\\*', "\r\n", ' '], ['.*', '|', ''], preg_quote(($setting['reg_user_censor'] = trim($setting['reg_user_censor'])), '/')).')$/i';
        if($setting['reg_user_censor'] && @preg_match($censorexp, $value)) {
            $this->errors = lang('username_disable_keyword');
            return false;
        }

        /* 检测重名 */
        if($this->model->where(["username" => $value])->count()) {
            $this->errors = lang('username_exist');
            return false;
        }
        return true;
    }


    private function _valid_password($value) {
        $reg_pass_lenght = config('cache.reg_pass_lenght');

        $reg_pass_lenght = max(3, (int) $reg_pass_lenght);

        if(strlen($value) < $reg_pass_lenght ) {
            $this->errors = '密码至少为 '. $reg_pass_lenght. ' 位字符';
            return false;
        }
        $reg_pass_complex=unserialize(config('cache.reg_pass_complex'));

        if($reg_pass_complex) {
            $strongpws = [];
            if(in_array('num',$reg_pass_complex) && !preg_match("/\d/",$value)){
                $strongpws[] = '数字';
            }
            if(in_array('small',$reg_pass_complex) && !preg_match("/[a-z]/",$value)){
                $strongpws[] = '小写字母';
            }
            if(in_array('big',$reg_pass_complex) && !preg_match("/[A-Z]/",$value)){
                $strongpws[] = '大写字母';
            }
            if(in_array('sym',$reg_pass_complex) && !preg_match("/[^a-zA-z0-9]+/",$value)){
                $strongpws[] = '特殊字符 ';
            }
            if($strongpws){
                $this->errors = '密码必须包含'.implode(',', $strongpws);
                return false;
            }
        }

        $this->reg_password=$value;

        return true;
    }


    private function _valid_two_password($value) {

        if($value==''){
            $this->errors='请输入交易密码';
            return false;
        }

        if(!$this->_valid_password($value)){
            $this->errors=$this->errors;
            return false;
        }

        $this->two_password=$value;

        return true;
    }

    private function _valid_repassword($value) {

        if($value==''){
            $this->errors='请输入确认密码';
            return false;
        }

        if($value!==$this->reg_password){
            $this->errors='确认密码不一致';
            return false;
        }

        return true;
    }


    private function _valid_two_repassword($value) {

        if($value==''){
            $this->errors='请输入确认交易密码';
            return false;
        }

        if($value!==$this->two_password){
            $this->errors='交易密码不一致';
            return false;
        }

        return true;
    }


    public function _valid_realname($value){

        if(empty($value)){
            $this->errors='请输入真实姓名';
            return false;
        }

        return true;

    }

    public function _valid_id_card($value){

        if(empty($value)){
            $this->errors='请输入身份证号';
            return false;
        }


        $IDCard = new IDCard();
        if(!$IDCard->vaild($value)){
            $this->errors='身份号码不正确';
            return false;
        }



        return true;

    }


    public function _valid_company($value){
        if($this->group_id>1){
            if(empty($value)){
                $this->errors='请输入公司名称';
                return false;
            }
        }

        return true;
    }


    public function _valid_email($value) {
        if(!is_email($value)) {
            $this->errors = lang('email_format_error');
            return false;
        }

        if($this->model->where(['email'=>$value])->count()) {
            $this->errors = lang('email_format_exist');
            return false;
        }
        return true;
    }

    public function _valid_mobile($value) {
        if(!is_mobile($value)) {
            $this->errors = lang('mobile_format_error');
            return false;
        }
        if($this->model->where(['username'=>$value])->count(1)) {
            $this->errors = lang('mobile_format_exist');
            return false;
        }
        return true;
    }



    /**
     * 变更用户账户
     * @param  string $os_type 类型
     * @param int $mid
     * @param string $type
     * @param int $num
     * @param boolean $iswritelog
     * @return boolean 状态
     */
    public function change_account($mid, $field ,$num, $msg = '',$iswritelog = TRUE) {

        $_member=db('member')->where(['id'=>$mid])->field('id,realname,username,money')->find();


        if(strpos($num, '-') === false && strpos($num, '+') === false) $num = '+'.$num;
        if(strpos($num, '-') === false){
            //累计
            $result =db('member')->where(['id' => $mid])->setInc($field,$num);
        }else{
            $result =db('member')->where(['id' => $mid])->setDec($field,abs($num));
        }

        if($result === false) {
            $this->errors = lang('_operation_fail_');
            return FALSE;
        }
        if($iswritelog === true) {


            $new_money=db('member')->where(['id'=>$mid])->value($field);

            $money_arr=['old_money'=>$_member[$field],'new_money' => sprintf('%.2f' ,$new_money)];
            $log_info = [
                'mid'      => $mid,
                'user'      => $_member['username'],
                'realname'      => $_member['realname'],
                'value'    => $num,
                'type'     => $field,
                'msg'      => $msg,
                'dateline' => time(),
                'admin_id' => (defined('IN_ADMIN')) ? ADMIN_ID : 0,
                'money_detail' => json_encode($money_arr)
            ];
            if(!db('member_log')->insertGetId($log_info)){
                return false;
            }
        }

        return TRUE;
    }




    public function tree($pid){

        $list=$this->model->where(['pid'=>$pid])->field('id,username,realname,push_num,my_sl,team_num,team_sl,is_lock,is_auth')->select();

        $i=0;
        foreach($list as $k=>$v){

            $isparent=$this->model->where(['pid'=>$v['id']])->value('id')?true:false;

            $isauth=$v['is_auth']?'已认证':'未认证';
            $islock=$v['is_lock']?'锁定':'正常';

            $name='ID：'.$v['id'].'，用户：'.$v['username'].'，姓名：'.$v['realname'];
            $name.='，直推人数：('.$v['push_num'].')，团队人数：('.$v['team_num'].')';
            $name.='，'.$isauth.'，'.$islock;;

            $list[$i]=$v;
            $list[$i]['isParent']=$isparent;
            $list[$i]['name']=$name;
            $i++;
        }


        return json_encode($list);

    }

}