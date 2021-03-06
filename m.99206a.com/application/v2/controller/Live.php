<?php
namespace app\v2\controller;
use app\v2\Base;
use think\Db;
use think\Config;
use think\Request;
use think\Session;
use app\live\agGame;
use app\live\bbingame;
use app\live\mycrypt;
use app\live\mgGame;
use app\live\ptGamePlayer;
use app\live\auth;
use think\Cache;

class Live extends Base
{
	public function index()
	{
		$uid = Session::get('uid'); 
		$user = Db::table('k_user')->where(array('uid'=>$uid))->find();
		
		$this->assign('user',$user);
		return $this->fetch('index');
	}
		
	public function ag($type = '',$actype = 1){	 
	    $uid = session('uid');
	    //$loginid = session(user_login_id);
	    $username =session('username');
	    $lotteryConfig = cache('lotteryConfig');
	    foreach($lotteryConfig as $v){
	        if($v['name'] == 'agzr' && $v['weihu'] == 1){
	            exit("当前游戏正在维护中!请稍后再试!");
	        }
	    }
	    
	    if(empty($uid) || empty($username)){
	        echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
	        exit();
	    }
	    if(!$actype){
	        $temp_username = $username.'tmpa';
	        if($result = agGame::regTempUser($temp_username)){
	        }
	    }else{
	        $temp_username = $username.'hga';
	        if($result = agGame::regUser($temp_username)){
	        }
	    }
	    
	    //renovate($uid,$loginid);
	    
	    if($result['Code']){
	        $this->error('登录失败!');
	    }
	    
	    $type	=	$this->request->param("type");
	    $res = '';
	    if(is_null($type)||$type==''){
	        $res = agGame::playAG($temp_username);
	    }else if($type=='buyu'){
	        $res = agGame::playAG($temp_username,6);
	    }else if($type=='dianzi'){
	        $res = agGame::playAG($temp_username,8);
	    }else{
	        echo 'run here';
	        $res = agGame::playAG($temp_username,$type);
	    }
	    header('location:'.$res);
	    exit();
	    
	    
	    
	    
	    $ssldomain = array('m.99205.site','www.99206y.com','99206y.com');
	    $host = $this->request->host();
	    if(in_array($host, $ssldomain)){
	        $domian = 'https://'.$this->request->host();;
	    }else{
	        $domian = 'http://'.$this->request->host();;
	    }
	    
	    
	    $lotteryConfig = cache('lotteryConfig');
	    foreach($lotteryConfig as $v){
	        if($v['name'] == 'agzr' && $v['weihu'] == 1){
	            exit("当前游戏正在维护中!请稍后再试!");
	        }
	    }
	    //if($lotteryConfig[''])
	    $this->assign('domian',$domian);
	    $this->assign('type',$type);
	    $this->assign('actype',$actype);
	    return $this->fetch();
	}
    public function bb($type = '',$actype = 1){
        $uid = session('uid');
        //$loginid = session(user_login_id);
        $username =session('username');
        
        $lotteryConfig = cache('lotteryConfig');
        if ($lotteryConfig){
            foreach($lotteryConfig as $v){
                if($v['name'] == 'agzr' && $v['weihu'] == 1){
                    exit("当前游戏正在维护中!请稍后再试!");
                }
            }
        }
        //执行login2登陆
        $login2F = bbingame::login2($username);
        if(!$login2F ||empty($uid) || empty($username)){
            echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
            exit();
        }
        
        $temp_username =  $username.'hga';
        $password = substr(md5(md5($username)),3,12);
        if($result = bbinGame::CreateMember($temp_username,$password)){
            ;
        }
        $is_mobile = $this->request->isMobile();
        //renovate($uid,$loginid);
        
        if($result['Code']){
            $this->error('登录失败!');
        }
        
        $type	=	$this->request->param("type");
        $res = '';
        if($is_mobile){
            if(is_null($type)||$type==''){
                $res = bbingame::playBBIN(1,$temp_username);
            }else if($type=='buyu'){
                $res = bbingame::playBBIN(1,$temp_username,6);
            }else if($type=='buyudaren'){
                $res = bbingame::ForwardGameH5By30(1,$temp_username);
            }else if($type=='buyudashi'){
                $res = bbingame::ForwardGameH5By38(1,$temp_username);
            }else if($type=='dianzi'){
                $res = bbingame::playBBIN(1,$temp_username,8);
            }else{
                $res = bbingame::playBBIN(1,$temp_username,$type);
            }
        }else{
            if(is_null($type)||$type==''){
                $res = bbingame::playBBIN(0,$temp_username);
            }else if($type=='buyu'){
                $res = bbingame::playBBIN(0,$temp_username,6);
            }else if($type=='buyudaren'){
                $res = bbingame::ForwardGameH5By30($temp_username);
            }else if($type=='buyudashi'){
                $res = bbingame::ForwardGameH5By38($temp_username);
            }else if($type=='dianzi'){
                $res = bbingame::playBBIN(0,$temp_username,8);
            }else{
                $res = bbingame::playBBIN(0,$temp_username,$type);
            }
        }
        
        
        header('location:'.$res);
        exit();
        
        
        //if($lotteryConfig[''])
        $this->assign('type',$type);
        $this->assign('actype',$actype);
        return $this->fetch();
    }

    public function playag($type = '',$actype = 1){
	    
	    $uid = session('uid');
	    //$loginid = session(user_login_id);
	    $username =session('username');
	    if(empty($uid) || empty($username)){
	        echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
	        exit();
	    }
	    if(!$actype){
	        $temp_username = $username.'tmpa';
	        if($result = agGame::regTempUser($temp_username)){
	        }
	    }else{
	        $temp_username = $username.'hga';
	        if($result = agGame::regUser($temp_username)){
	        }
	    }
	    
	    //renovate($uid,$loginid);
	    
	    if($result['Code']){
	        $this->error('登录失败!');
	    }
	    
	    $type	=	$this->request->param("type");
	    $res = '';
	    if(is_null($type)||$type==''){
	        $res = agGame::playAG($temp_username);
	    }else if($type=='buyu'){
	        $res = agGame::playAG($temp_username,6);
	    }else if($type=='dianzi'){
	        $res = agGame::playAG($temp_username,8);
	    }else{
	        echo 'run here';
	        $res = agGame::playAG($temp_username,$type);
	    }
	    $this->assign('res',$res);
	    return $this->fetch();
	}
	
	
	public function bbin(){
	    $lotteryConfig = cache('lotteryConfig');
	    foreach($lotteryConfig as $v){
	        if($v['name'] == 'bbzr' && $v['weihu'] == 1){
	            exit("当前游戏正在维护中!请稍后再试!");
	        }
	    }
	    $uid = Session('uid');
	    $username=Session('username');//$userinfo["username"];
	    if(empty($uid) || empty($username)){
	        echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
	        exit();
	    }
	    $temp_username = $username . 'hga';
	    $password = substr(md5(md5($username)),3,12);
	    if($return = bbinGame::CreateMember($temp_username,$password)){
	        ;
	    }
	    $return = bbingame::Login($temp_username, $password);
	    $this->assign('return',$return);
	    return $this->fetch();
	}
	
	public function mg($item_id,$app_id){
	    if($this->request->isMobile()){
	        $app_id = '1002';
	    }else{
	        $app_id = '1001';
	    }
        $lotteryConfig = cache('lotteryConfig');
	    foreach($lotteryConfig as $v){
	        if($v['name'] == 'mgzr' && $v['weihu'] == 1){
	            exit("当前游戏正在维护中!请稍后再试!");
	        }
	    }
	    $uid = Session('uid');
	    $username = Session('username');
	    if(empty($uid) || empty($username)){
	        echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
	        exit();
	    }
	    $sql = "select * from k_userlive where uid = '$uid' and platform = 'mg'";
	    $query = Db::query($sql);
	    $result = '';
	    if($query){
	        $result = $query[0];
	    }
	    if($result){
	        $mgusername = $result['username'].'@hga';
	        $code = $result['code'];
	        $salt = $result['salt'];
	        $password = mycrypt::decrypt($code, $salt);
	        $auth = mgGame::authenticate();
	        if($auth['success']){
	            $access_token = $auth['body']['access_token'];
	            $info = mgGame::lauchItem($access_token, $mgusername, $item_id, $app_id);
	            if($info['success']){
	                header('location:'.$info['body']);
	            }else{
//	                var_dump($info);
	                exit('登录失败!');
	            }
	        }else{
	            exit('认证失败!');
	        }
	    }else{
	        $salt = mycrypt::generate_password(4);
	        $password = mycrypt::generate_password(8);
	        $code = mycrypt::encrypt($password, $salt);
	        $auth = mgGame::authenticate();
	        if($auth['success']){
	            $access_token = $auth['body']['access_token'];
	            $mgusername = $username.'@hga';
	            $info = mgGame::getAccountByExtref($access_token, $mgusername);
	            if($info['success']){ //用户已经存在,更新用户信息
	                $uid = db('k_user')->where(['username'=>$username])->field('uid')->find();
	                $updateinfo = mgGame::updateMemberPassword($access_token, $password, $mgusername);
	                if($updateinfo['success']){
	                    $insertData = array(
	                        'username'=>$username,
	                        'salt' => $salt,
	                        'code' => mycrypt::encrypt($password, $salt),
	                        'platform'=>'mg',
	                        'uid'   => $uid['uid'],
	                    );
	                    db('k_userlive')->insert($insertData);
	                    $info = mgGame::lauchItem($access_token, $mgusername, $item_id, $app_id);
	                    if($info['success']){
	                        header('location:'.$info['body']);
	                    }else{
	                        exit("登录游戏失败!");
	                    }
	                }else{
	                    echo '<script>alert("用户注册了MG账户,但是数据库中并不存在,尝试更新用户信息和本地数据库时失败!")</script>';
	                }
	            }else{ //用户不存在,创建用户
	                $info = mgGame::createMember($access_token, $mgusername, $password,$mgusername);
	                if($info['success']){
	                    $sql ="insert into k_userlive (uid,username,code,salt,platform) values
	                    ({$uid},'{$username}','{$code}','{$salt}','mg')";
	                    $q = Db::execute($sql);
	                    $auth = mgGame::authenticate();
	                    if($auth['success']){
	                        $access_token = $auth['body']['access_token'];
	                        $info = mgGame::lauchItem($access_token, $mgusername, $item_id, $app_id);
	                        if($info['success']){
	                            header('location:'.$info['body']);
	                        }else{
	                            exit("登录游戏失败!");
	                        }
	                    }else{
	                        exit("认证失败!");
	                    }
	                    
	                }else{
	                    exit("创建用户失败!错误:".$info['body']['message']);
	                }
	            }
	        }else{
	            exit("认证失败!");
	        }
	    }
	}

    public function pt1($item_id){
	    $lotteryConfig = cache('lotteryConfig');
	    foreach($lotteryConfig as $v){
	        if($v['name'] == 'ptzr' && $v['weihu'] == 1){
	            exit("当前游戏正在维护中!请稍后再试!");
	        }
	    }
	    $uid = Session('uid');
	    $username = Session('username');
	    if(empty($uid) || empty($username)){
	        echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
	        exit();
	    }
	    $sql = "select * from k_userlive where uid = '$uid' and platform = 'pt'";
	    $result = Db::query($sql);
	    $result = $result[0] ?? '';
	    if($result){
	        $mgusername = $result['username'].'@hga';
	        $code = $result['code'];
	        $salt = $result['salt'];
	        $password = mycrypt::decrypt($code, $salt);
	        $data = array(
	            'param1' => $mgusername,
	            'param2' => $password,
	            'param3' => 'hga',
	            'param4' => date("Y-m-d H:i:s"),
	            'param5' => $item_id,
	        );
	        $auth = new auth();
	        $return = $auth -> test($data);
	    }else{
	        $salt = mycrypt::generate_password(4);
	        $password = mycrypt::generate_password(8);
	        $code = mycrypt::encrypt($password, $salt);
	        $mgusername = $username .'@hga';
	        $userinfo = ptGamePlayer::info($mgusername);
	        //var_dump($userinfo);
	        $info = ptGamePlayer::create($mgusername,$password);
	        //echo $password,"<br/>";
	        //$info = ptGamePlayer::update($mgusername, array('password'=>$password));
	        if(!isset($info['error'])){
	            $sql ="insert into k_userlive (uid,username,code,salt,platform) values
	            ({$uid},'{$username}','{$code}','{$salt}','pt')";
	            Db::query($sql);
	            //发送帐号密码到服务器上验证
	            $data = array(
	                'param1' => $mgusername,
	                'param2' => $password,
	                'param3' => 'hag',
	                'param4' => date("Y-m-d H:i:s"),
	                'param5' => $item_id,
	            );
	            $auth = new auth();
	            $return = $auth -> test($data);
	        }else{
	            
	        }
	    }
	    $this->assign('return',$return);
	    return  $this->fetch();
	}
    public function pt($item_id){
        $lotteryConfig = cache('lotteryConfig');
        foreach((array)$lotteryConfig as $v){
            if($v['name'] == 'ptzr' && $v['weihu'] == 1){
                exit("当前游戏正在维护中!请稍后再试!");
            }
        }
        $uid = Session('uid');
        $username = Session('username');
        if(empty($uid) || empty($username)){
            echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
            exit();
        }
        //重置并同步第三方密码
        $data1 = \app\index\controller\Live::rePass($username,$username.'@hga',$uid);
        $data = array(
            'param1' => $data1['mgusername'],
            'param2' => $data1['password'],
            'param3' => 'hga',
            'param4' => date("Y-m-d H:i:s"),
            'param5' => $item_id,
        );
        $auth = new auth();
        $return = $auth -> test($data);
        $this->assign('return',$return);
        return  $this->fetch();
    }

    public function sunbet(){
	    $lotteryConfig = cache('lotteryConfig');
//	    foreach($lotteryConfig as $v){
//	        if($v['name'] == 'sbzr' && $v['weihu'] == 1){
                echo "<script type='text/javascript'>alert('当前游戏正在维护中!请稍后再试!');window.location='/';</script>";
                exit();
	            //exit("当前游戏正在维护中!请稍后再试!");
//	        }
//	    }
        $uid = Session('uid');
        $username=Session('username');//$userinfo["username"];
	    if(empty($uid) || empty($username)){
	        echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
	        exit();
	    }
	}
	
	public function og(){
	    $lotteryConfig = cache('lotteryConfig');
	    foreach($lotteryConfig as $v){
	        if($v['name'] == 'ptzr' && $v['weihu'] == 1){
	            exit("当前游戏正在维护中!请稍后再试!");
	        }
	    }
	    $uid = Session('uid');
	    $username = Session('username');
	    if(empty($uid) || empty($username)){
	        echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
	        exit();
	    }
	    $temp_username = $username.'hga';
	    $user = Db::table('k_user')->where('uid','eq',$uid)->find();
//	    var_dump($user);
	    
	    //如果会员未激活OG  激活OG
	    if ($user['og_username'] == '') {
	        // $og ->createUser($user['username'],'oga123456');
	        $result = \app\live\oggame::CheckAndCreateAccount($temp_username, 'oga123456');
	        //$result = og::createUser("jp1" . $user['username'], 'oga123456');
	        if ($result == '1') {
	            $s = "UPDATE k_user  set  og_username='" . $temp_username."' WHERE  uid=" . $uid;
	            Db::query($s);
	        }
	    }
	    
	    $url = \app\live\oggame::TransferGame($temp_username, 'oga123456', 'cashapi.jinpaizhan.com', '1', '0');
	    //$url = og::createGameUrl("jp1" . $user['username'], 'oga123456');
	    
	    header("location:" . $url);
	    //return $this->fecth();
	}
	
	function checkAndCreateUser($uid = '',$username = '',$mysqli){
	    if(empty($uid) || empty($username)){
	        echo "<script type='text/javascript'>alert('请登录!');window.location='/';</script>";
	        return true;
	    }
	    $sql = "select * from k_userlive where uid = '$uid' and platform = 'mg'";
	    $query = $mysqli->query($sql);
	    if($query){
	        $result = $query->fetch_array();
	    }
	    if(!$result){
	        $salt = mycrypt::generate_password(4);
	        $password = mycrypt::generate_password(8);
	        $code = mycrypt::encrypt($password, $salt);
	        $auth = mgGame::authenticate();
	        if($auth['success']){
	            $access_token = $auth['body']['access_token'];
	            $mgusername = $username.'@hag';
	            $info = mgGame::createMember($access_token, $mgusername, $password,$mgusername);
	            if($info['success']){
	                $sql ="insert into k_userlive (uid,username,code,salt,platform) values
	                ({$uid},'{$username}','{$code}','{$salt}','mg')";
	                $mysqli->query($sql);
	                return true;
	            }else{
	                return false;
	            }
	        }else{
	            return false;
	        }
	    }
	    return true;
	}
	
    public function lobby($type='ag'){
        if ( !request()->isAjax()){
//            $dzyx = Cache::get('dzyx1');
            $getData =  Request::instance()->param();
            $type = !empty($getData['type'])?$getData['type']:'ag';
//            if(!$dzyx){
                    $dzyx =[];
                    $dzyx = db('dzyx')->where('platform',$type)->limit(16)->select();
                    if ($type=='mg'){
                        $dzyx = db('dzyx')->where('platform',$type)->where('is_h5',1)->limit(16)->select();
                    }
//                    Cache::set('dzyx1',$dzyx);
//            }
                //$play_type = input('p');
                $this->assign('type',$type);
                $this->assign('dzyx',$dzyx);

                return $this->fetch();
        }else{
            $postData = Request::instance()->param();
            $type = !empty($postData['type'])?$postData['type']:'ag';
            $page = !empty($postData['page'])?$postData['page']:1;
            $dzyx = db('dzyx')->where('platform',$type)->page($page)->limit(16)->select();
            if ($type=='mg'){
                $dzyx = db('dzyx')->where('platform',$type)->where('is_h5',1)->page($page)->limit(16)->select();
            }
            $html = '';
            if ($dzyx){
                foreach ($dzyx as $k=>$v) {
                    $typeS = $v['platform'];
                    $iamges = $v['imageurl'];
                    $games = $v['gamename'];
                    $gameid = $v['gameid'];
                    $gameid2 = $v['gameid2'];
                    if ($typeS=='ag'){
                        $html .= <<<AG
	<a href="/live/ag/type/$typeS" style="display: inline;">
				<div class="search_img">
					<img src="/images/agimg/$iamges" />
				</div>
				<span class="search_text" data-num="0">$games</span>
			</a>
AG;

                    }
                    if ($typeS=='bb'){
                        $html .= <<<BB
            <a href="/live/bb/type/$gameid" style="display: inline;">
				<div class="search_img">
					<img src="/images/bbimg/$iamges" />
				</div>
				<span class="search_text" data-num="0">$games</span>
			</a>
BB;
                    }
                    if ($typeS=='mg'){
                        $html .= <<<MG
	<a href="/live/mg/item_id/$gameid/app_id/$gameid"  style="display: inline;">
				<div class="search_img">
					<img  src="/images/mgimg/$iamges" />
				</div>
				<span class="search_text" data-num='2'>$games</span>
			</a>
MG;
                    }
                    if ($typeS=='pt'){
                        $html .= <<<PT
	<a href="/live/pt/item_id/$gameid" style="display: inline;" >
				<div class="search_img">
					<img  src="/images/ptimg/$gameid.png"/>
				</div>
				<span class="search_text" data-num='1'>$games</span>
			</a>
PT;


                    }
                    if ($typeS=='sb'){
                        $html .= <<<SB

SB;
                    }
                }
            }

            return json_encode($html);
        }
    }
}
