<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */
 
namespace app\admin\service;
use think\Model;
use think\Session;
use think\Request;
use think\Loader;
use think\Db;
class Admin extends Model{

    public function initialize(){

        $this->model=model('admin/Admin');
        $this->admin_user_service=model('admin/AdminUser','service');

    }

    public static function init() {

        $_admin = [
            'id' => 0,
            'group_id' => 0,
            'username'	=> '',
            'avatar'	=> '',
            'rules'		=> '',
            'formhash'	=> random(6)
        ];

        //$authkey = session('gboy_admins_authkey');
        $authkey=Session::get('gboy_gadmins_authkey');
        $admins_time=Session::get('gboy_gadmins_time');



        if(empty($admins_time)){
            session('gboy_gadmins_authkey', NULL);
            session('gboy_gadmins_time', NULL);
            session('gboy_guuadminislogin', NULL);
            return false;
        }else{
            $out_time=2*60*60;
            // echo time().">=$admins_time+$out_time";
            if(time()>=$admins_time+$out_time){
                session('gboy_gadmins_authkey', NULL);
                session('gboy_gadmins_time', NULL);
                session('gboy_guuadminislogin', NULL);
                return false;
            }else{
                session::set('gboy_gadmins_time',time());
            }
        }


        if($authkey) {

            list($admin_id,$user,$pwd, $authkey) = explode("\t", authcode($authkey, 'DECODE'));


            $_admin = model('admin/AdminUser')->where(['id'=>$admin_id,'username'=>$user,'password'=>$pwd])->find();


            if($_admin) {
                $_admin['rules'] = model('admin/AdminGroup')->where(array('id' => $_admin['group_id']))->value('rules');
                $_admin['formhash'] = $authkey;

                //echo $_admin['password'];
                //$_admin['avatar'] = $this->getthumb($_admin['id']);
            }else{

                session('gboy_gadmins_authkey', NULL);
                session('gboy_gadmins_time', NULL);
                session('gboy_guuadminislogin', NULL);
            }



        }

        return $_admin;

    }


    /**
     * @param $rules
     * @return bool
     */
    public function auth($rules) {


        
        $rules = explode(",", $rules);
        $module_name=Request::instance()->module();
        $controller_name=Request::instance()->controller();
        $action_name=Request::instance()->action();
        $_map = [];
        $_map['m'] = strtolower($module_name);
        $_map['c'] = strtolower(Loader::parseName($controller_name, 0, true));
        $_map['a'] = strtolower(Loader::parseName($action_name, 0, true));

        $rule_id = model('admin/Node')->where($_map)->value('id');

        if($rule_id && !in_array($rule_id, $rules)) {
            return false;
        }
        return true;
    }



    public function check_safe(){

        $auth=true;

        //计算机名验证

        /*
        $auth_computer_name=config('cache.auth_computer_name');
        if($auth_computer_name){

            $uname_arr=explode(' ',php_uname());
            $computer_name=$uname_arr[2];

            $computer_arr=explode(',',$auth_computer_name);
            if(!in_array($computer_name,$computer_arr)){
                    $auth=false;
            }
        }*/





        //IP验证
        $auth_computer_ip=config('cache.auth_computer_ip');
        if($auth_computer_ip){
            $computer_ip_arr=explode(',',$auth_computer_ip);
            if(!in_array(getip(),$computer_ip_arr)){
                    $auth=false;
            }
        }


        return $auth;

    }


    /**
     * 登录
     * @param string $username
     * @param string $password
     * @param string $code
     */
    public function login($username, $password,$code) {



        $data=[];
        $data['username']=$username;
        $data['password']=$password;
        $data['code']=$code;

        $validate = validate('Admin');
        if(!$validate->check($data)){
            $this->errors=$validate->getError();
            return false;
        }
    

        $admin_user = $this->model->getByUsername($username);

        if(!$admin_user) {
            $this->errors = lang('admin_user_not_exist');
            return FALSE;
        }
        
        if($admin_user['password'] !== $this->admin_user_service->create_password($password, $admin_user['encrypt'])) {
            $this->errors = lang('password_checked_error');
            return FALSE;
        }

    
	
		
        $this->_dologin($admin_user);
    }



    private  function _dologin($user) {

        $auth = authcode($user['id']."\t".$user['username']."\t".$user['password']."\t".  random(6), 'ENCODE');


        $this->model->where(['id'=>$user['id']])->data(['last_login_time'=>time(),'last_login_ip'=>getip(),'login_num'=>Db::raw('login_num+1')])->update();

        //session('authkey', $auth);
        session::set('gboy_gadmins_authkey',$auth);
        session::set('gboy_gadmins_time',time());
        return true;
    }


    /**
     * 退出登录
     * @return boolean
     */
    public function logout() {
        session('gboy_gadmins_authkey', NULL);
        session('gboy_gadmins_time', NULL);
        return TRUE;
    }


}