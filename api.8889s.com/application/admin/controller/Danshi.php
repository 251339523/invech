<?php
namespace app\admin\controller;
use app\admin\Login;
use think\Db;
use think\db\Query;
class danshi extends Login{
    
    public function index(){
        $param = $this->request->param();
        $username = $param['username'] ?? '';
        $type = $param['type'] ?? '';
        $uid = $param['uid'] ?? '';
        $match_id = $param['match_id'] ?? '' ;
        $match_name = $param['match_name'] ?? '';
        $ball_sort = $param['ball_sort'] ?? '';
        $point_column = $param['point_column'] ?? '';
        $column_like = $param['column_like'] ?? '';
        $match_type = $param['match_type'] ?? '';
        $s_time = $param['s_time'] ?? '';
        $e_time = $param['e_time'] ?? '';
        $status = $param['status'] ?? '';
        $tf_id = $param['tf_id'] ?? '';
        $order = $param['order'] ?? '';
        
        $where = [];
        if($username){
            $where['username'] = ['eq',$username];
            $conf['username'] = $username;
        }
        if($type){
            $where['ball_sort'] = ['like',$type.'%'];
        }
        if($uid){
            $where['uid'] = ['eq',$uid];
        } 
        if($match_id){
            $where['match_id'] = ['eq',$match_id];
        }
        if($match_name){
            $where['match_name'] = ['eq',$match_name];
        }
        if($ball_sort){
            $where['ball_sort'] = ['eq',$ball_sort];
        }
        if($column_like){
            $where['column_lie'] = ['like',$column_like];
        }
        if($match_type){
            $where['match_type'] = ['eq',$match_type];
        }
        $timeconf = [];
        $db = DB::table('bet_user');
        $db->where($where);
        if($s_time){
            $db->where('bet_time','>=',$s_time.' 00:00:00');
        }
        if($e_time){
            $db->where('bet_time','<=',$e_time,' 23:59:59');
        }
        if($status){
            $db->where('status','in',$status);
        }
        if($tf_id){
            $db->where('number','eq',$tf_id);
        }
        if($order){
            $db->order($order,'desc');
        }else{
            $db->order('bid','desc');
        }
        config('paginate.query',$param);
        $list = $db->paginate(20);
        $this->assign('list',$list);
        $this->assign('type',$type);
        $this->assign('uid',$uid);
        $this->assign('match_id',$match_id);
        $this->assign('match_name',$match_name);
        $this->assign('ball_sort',$ball_sort);
        $this->assign('point_column',$point_column);
        $this->assign('column_like',$column_like);
        $this->assign('match_type',$match_type);
        $this->assign('s_time',$s_time);
        $this->assign('e_time',$e_time);
        $this->assign('status',$status);
        $this->assign('tf_id',$tf_id);
        $this->assign('order',$order);
        $this->assign('type',$type);
        $this->assign('username',$username);
        return $this->fetch();
    }
    
    
    public function qxbet(){
        $param = $this->request->param();
        $bid = $param['bid'] ?? '';
        $status = $param['status'] ?? '';
        $msg = \app\logic\bet::qx_bet($bid,$status) ? '操作成功' : '操作失败';
        $this->assign('bid',$bid);
        $this->assign('status',$status);
        $this->assign('msg',$msg);
        return  $this->fetch('qxbet');
    }
    
    public function setscore(){
        $param = $this->request->param();
        $bid = $param['bid'] ?? '';
        $status = $param['status'] ?? '';
        $this->assign('bid',$bid);
        $this->assign('status',$status);
        $t = Db::table('k_bet')->field(['match_id','master_guest','match_name'])->where('bid','eq',$bid)->limit(1)->find();
        //$sql	=	"select MB_Inball,TG_Inball from k_bet where MB_Inball is not null and match_id=".$t['match_id']." limit 1";
        $m =  Db::table('k_bet')->where('MB_Inball is not null')->where('match_id','eq',$t['match_id'])->find();
        if(strpos($t['master_guest'],'VS.')) $master_guest	=	explode('VS.',$t['master_guest']);
        else $master_guest	=	explode('VS',$t['master_guest']);
        $this->assign('t',$t);
        $this->assign('m',$m);
        $this->assign('master_guest',$master_guest);
        return $this->fetch();
    }
    
    public function setbet(){
        $param = $this->request->param();
        $status = $param['status'];
        $bid = $param['bid'];
        if(\app\logic\bet::set($bid,$status,$param["MB_Inball"],$param["TG_Inball"])){
            $flag = true;
        }else{
            $flag = false;
        }
        return $this->fetch();
    }
    
    public function setbet3(){
        $param = $this->request->param();
        $sql	=	"select match_name,master_guest,bet_info,bet_point,bet_money,bet_win,match_id,ball_sort,bet_time,bid,uid from k_bet where bid=".intval($_GET["bid"])." limit 1";
        $rows = db('k_bet')->where('bid','eq',$param['bid'])->field([
            'match_name',
            'master_guest',
            'bet_info',
            'bet_point',
            'bet_money',
            'bet_win',
            'match_id',
            'ball_sort',
            'bet_time',
            'bid',
            'uid',
        ])->find();
        $this->assign('rows',$rows);
        return $this->fetch();
    }
    
    public function savebet3(){
        $param = $this->request->param();
        $action = $param['action'] ?? '';
        $bid = intval($param['bid'] ?? '');
        $uid = intval($param['uid'] ?? '');
        $why = $param['sys_about'];
        $back_bet_money = $param['back_bet_money'] ?? 1;
        $master_guest = $param['master_guest'] ?? '';
        $new = $param['new'] ?? '';
        if($param["action"]=="save"){
            $num		=	0;
            if($back_bet_money=="1"){
                $num	=	2;
                $sql	=	"update k_user,k_bet set k_user.money=k_user.money+k_bet.bet_money,k_bet.win=k_bet.bet_money,k_bet.status=3,k_bet.sys_about='".($why ? $why : '手工无效')."',k_bet.update_time=now() where status=0 and k_user.uid=k_bet.uid and k_bet.bid=$bid";
            }else{
                $num	=	1;
                $sql	=	"update k_bet set status=3,sys_about='$why',update_time=now() where status=0 and k_bet.bid=$bid";
            }
            Db::startTrans();
            try{
                $q1 = Db::execute($sql);
                if($q1 == $num){
                    Db::commit(); //事务提交
                    sys_log(Session('uid'), "审核了编号为".$bid."的注单,设为无效，退还了投注金额");
                    $msg = new \app\model\msg();
                    $msg->msg_add($uid,'结算中心',$master_guest."_注单已取消",$master_guest.'<br/>'.$param["bet_info"].'<br/>'.$why);
                    echo "<script>alert('操作成功');\r\n";
                    if($new) echo "refash();</script>";
                    else echo "location.href='".$_SERVER['HTTP_REFERER']."';</script>";
                }else{
                    Db::rollback();
                    Alert('操作失败！',$_SERVER['HTTP_REFERER']);
                }
            }catch(Exception $e){
                Db::rollback();
                Alert('操作失败！',$_SERVER['HTTP_REFERER']);
            }
        }
    }
    
    public function zqlr(){
        $val	=	explode("|||",$this->request->param("value"));
        $mid	=	$val[3];
        if($mid){
            $rows = Db::connect('sportdb')->table('bet_match')->where('Match_ID','eq',$mid)->find();
            $match_name	=	$rows["Match_Name"];
            $Match_Master	=	$rows["Match_Master"];
            $Match_Guest	=	$rows["Match_Guest"];
            $Match_Date	=	$val[2];
            $matche_id = Db::connect('sportdb')->table('bet_match')->where('match_name','eq',$match_name)
                        ->where('Match_Master','eq',$Match_Master)->where('Match_Guest','eq',$Match_Guest)
                        ->where('Match_Date','like','%'.$Match_Date.'%')->field(['Match_ID'])->select();
            $mid = '';
            foreach ($matche_id as $k => $v){
                $mid .= $v['Match_ID'].',';
            }
            $mid			=	rtrim($mid,",");
            $value			=	"";
            
            $type			=	$val[4];
            if($type == 'sb'){ //上半场
                $MB_Inball_HR	=	$val[0];
                $TG_Inball_HR	=	$val[1];
                $sql = "update bet_match set mb_inball_hr='$MB_Inball_HR',tg_inball_hr='$TG_Inball_HR' where match_id in($mid)";
                Db::connect('sportdb')->query($sql);
                $sql			=	"select bid from k_bet where lose_ok=1 and (point_column in ('match_bmdy','match_bgdy','match_bhdy','match_bho','match_bao','match_bdpl','match_bxpl') or point_column like 'match_hr_bd%') and status=0 and match_id in($mid) order by bid desc"; 
                $result = Db::query($sql);
                $bid			=	"";
                foreach ($result as $k => $v){
                    $bid .= $v['bid'].',';
                }
                $bid			=	rtrim($bid,",");
                if($bid != ''){
                    $sql		=	"update k_bet set MB_Inball='$MB_Inball_HR',TG_Inball='$TG_Inball_HR' where bid in($bid)";
                    Db::query($sql);
                }
                $sql			=	"select bid from k_bet_cg where status=0 and match_id in($mid) and (ball_sort like('%上半场%') or bet_info like('%上半场%')) order by bid desc";
                $result_cg = Db::query($sql);
                $bid = '';
                foreach ($result_cg as $k => $v){
                    $bid .= $v['bid'].',';
                }
                if($bid != ""){
                    $bid		=	rtrim($bid,",");
                    $sql		=	"update k_bet_cg set mb_inball='$MB_Inball_HR',tg_inball='$TG_Inball_HR' where bid in($bid)";
                    Db::query($sql);
                }
                echo 2;
            }else{
                $MB_Inball		=	$val[0];
                $TG_Inball		=	$val[1];
                $sql			=	"update bet_match set mb_inball='$MB_Inball',tg_inball='$TG_Inball' where match_id in($mid)";
                Db::connect('sportdb')->query($sql);
                
                //保存所有全场单式注单比分
                $sql			=	"select bid from k_bet where lose_ok=1 and status=0 and match_id in($mid) and not(ball_sort like('%上半场%') or bet_info like('%上半场%')) order by bid desc ";
                $result			=	Db::query($sql); //单式
                $bid			=	"";
                foreach ($result as $k => $v){
                    $bid .= $v['bid'].',';
                }
                if($bid != ""){ //全场
                    $bid	=	rtrim($bid,",");
                    $sql	=	"update k_bet set MB_Inball='$MB_Inball',TG_Inball='$TG_Inball' where bid in($bid)";
                    Db::query($sql);
                }
                
                //保存全场有串关注单比分
                $sql		=	"select bid from k_bet_cg where status=0 and match_id in($mid) and not(ball_sort like('%上半场%') or bet_info like('%上半场%')) order by bid desc";
                $result_cg	=	Db::table('k_bet_cg')->query($sql); //串关
                $bid		=	"";
                foreach ($result_cg as $k=>$v){
                    $bid .= $v['bid'].',';
                }
                if($bid != ""){
                    $bid	=	rtrim($bid,",");
                    $sql	=	"update k_bet_cg set mb_inball='$MB_Inball',tg_inball='$TG_Inball' where bid in($bid)";
                    Db::table('k_bet_cg')->query($sql);
                }
                
                echo 1;
                exit;
            }
        }else{
            echo 3;
            exit;
        }
        
    }
    
    public function wpblr(){
        $val	=	explode("|||",$_POST["value"]);
        $mid	=	$val[3];
        $table	=	$val[4];
        if($mid){
            $db = Db::connect('sportdb');
            $MB_Inball		=	$val[0];
            $TG_Inball		=	$val[1];
            
            $sql			=	"select Match_Master,match_name,Match_Guest from $table where Match_ID=$mid limit 1";
            $rows			=	$db::query($sql)[0];
            
            $match_name	=	$rows["match_name"];
            $Match_Master	=	$rows["Match_Master"];
            $Match_Guest	=	$rows["Match_Guest"];
            $Match_Date	=	$val[2];
            
            $sql			=	"select Match_ID from $table where match_name='$match_name' and Match_Master='$Match_Master' and Match_Guest='$Match_Guest' and Match_Date='".$Match_Date."'";
            $rows = $db::query($sql);
            $mid			=	"";
            foreach($rows as $k => $v){
                $mid .= $v["Match_ID"].",";
            }
            $mid			=	rtrim($mid,",");
            $value			=	"";
            if($MB_Inball!="" && $TG_Inball!=""){ //保存全场
                $sql		=	"update $table set mb_inball='$MB_Inball',tg_inball='$TG_Inball' where match_id in($mid)";
                $db::query($sql);
                
                //保存所有全场单式注单比分
                $sql		=	"select bid from k_bet where lose_ok=1 and status=0 and match_id in($mid) order by bid desc ";
                $rows = Db::query($sql);
                $bid		=	"";
                foreach($rows as $k => $v){
                    $bid .= $v['bid'].',';
                }
                if($bid != ""){ //全场
                    $bid	=	rtrim($bid,",");
                    $sql	=	"update k_bet set MB_Inball='$MB_Inball',TG_Inball='$TG_Inball' where bid in($bid)";
                    $mysqli->query($sql);
                }
                
                echo 1;
                exit;
            }
        }else{
            echo 3;
            exit;
        }
    }
    
    public function lqlr(){
        $val				=	explode("|||",$_POST["value"]);
        $mid				=	$val[3];
        
        if($mid){
            $MB			=	$val[0];
            $TG			=	$val[1];
            $db = Db::connect('sportdb');
            $sql			=	"select Match_Master,match_name,Match_Guest from lq_match where Match_ID=$mid limit 1";
            $rows = $db->query($sql)[0];
            
            $match_name	=	$rows["match_name"];
            $Match_Master	=	$rows["Match_Master"];
            $Match_Guest	=	$rows["Match_Guest"];
            $Match_Date	=	$val[2];
            
            $sql			=	"select Match_ID from lq_match where match_name='$match_name' and Match_Master='$Match_Master' and Match_Guest='$Match_Guest' and Match_Date='".$Match_Date."'";
            $rows = $db->query($sql);
            $mid			=	"";
            foreach ($rows as $k => $v){
                $mid .= $v['Match_ID'].',';
            }
            $mid			=	rtrim($mid,",");
            $value			=	"";
            if($MB!="" && $TG!=""){ //保存
                $mb_inball	=	'MB_Inball'; //默认全场
                $tg_inball	=	'TG_Inball'; //默认全场
                $preg1		=	"/第[1-4]節/";
                if(strpos($Match_Master,'上半') && strpos($Match_Guest,'上半')){
                    $mb_inball		=	'MB_Inball_HR'; //上半场
                    $tg_inball		=	'TG_Inball_HR'; //上半场
                }elseif(preg_match($preg1,$Match_Master,$num) && preg_match($preg1,$Match_Guest,$num)){
                    if(strpos($num[0],'1')){
                        $mb_inball	=	'MB_Inball_1st'; //第1节
                        $tg_inball	=	'TG_Inball_1st'; //第1节
                    }elseif(strpos($num[0],'2')){
                        $mb_inball	=	'MB_Inball_2st'; //第2节
                        $tg_inball	=	'TG_Inball_2st'; //第2节
                    }elseif(strpos($num[0],'3')){
                        $mb_inball	=	'MB_Inball_3st'; //第3节
                        $tg_inball	=	'TG_Inball_3st'; //第3节
                    }elseif(strpos($num[0],'4')){
                        $mb_inball	=	'MB_Inball_4st'; //第4节
                        $tg_inball	=	'TG_Inball_4st'; //第4节
                    }
                }elseif(strpos($Match_Master,'下半') && strpos($Match_Guest,'下半')){
                    $mb_inball		=	'MB_Inball_ER'; //下半场
                    $tg_inball		=	'TG_Inball_ER'; //下半场
                }elseif(strpos($Match_Master,'加時') && strpos($Match_Guest,'加時')){
                    $mb_inball		=	'MB_Inball_ADD'; //加时
                    $tg_inball		=	'TG_Inball_ADD'; //加时
                }
                
                
                $sql		=	"update lq_match set $mb_inball='$MB',$tg_inball='$TG',MB_Inball_OK='$MB',TG_Inball_OK='$TG' where match_id in($mid)";
                $db->query($sql);
                
                //保存所有全场单式注单比分
                $sql		=	"select bid from k_bet where lose_ok=1 and status=0 and match_id in($mid) order by bid desc ";
                $rows = Db::query($sql);
                $bid		=	"";
                foreach ($rows as $k => $v){
                    $bid .= $v['bid'].',';
                }
                if($bid != ""){ //全场
                    $bid	=	rtrim($bid,",");
                    $sql	=	"update k_bet set MB_Inball='$MB',TG_Inball='$TG' where bid in($bid)";
                    Db::query($sql);
                }
                
                //保存所有全场串关注单比分
                $sql		=	"select bid from k_bet_cg where status=0 and match_id in($mid) order by bid desc";
                $rows	=	Db::query($sql); //串关
                $bid		=	"";
                foreach ($rows as $k => $v){
                    $bid .= $rows['bid'].',';
                }
                if($bid != ""){
                    $bid	=	rtrim($bid,",");
                    $sql	=	"update k_bet_cg set mb_inball='$MB',tg_inball='$TG' where bid in($bid)";
                    Db::query($sql);
                }
                
                echo "1,$mb_inball,$tg_inball";
                exit;
            }
        }else{
            echo 3;
            exit;
        }
    }
    
    public function bqlr(){
        $val	=	explode("|||",$_POST["value"]);
        $mid	=	$val[3];
        
        if($mid){
            $db = Db::connect('sportdb');
            $sql			=	"select Match_Master,match_name,Match_Guest from baseball_match where Match_ID=$mid limit 1";
            $rows			=	$db->query($sql)[0];
            
            $match_name	=	$rows["match_name"];
            $Match_Master	=	$rows["Match_Master"];
            $Match_Guest	=	$rows["Match_Guest"];
            $Match_Date	=	$val[2];
            
            $sql			=	"select Match_ID from baseball_match where match_name='$match_name' and Match_Master='$Match_Master' and Match_Guest='$Match_Guest' and Match_Date like ('%".$Match_Date."%')";
            $rows = $db->query($sql);
            $mid			=	"";
            foreach ($rows as $k => $v){
                $mid .= $rows['Match_ID'].',';
            }
            $mid			=	rtrim($mid,",");
            $value			=	"";
            
            $type			=	$val[4];
            if($type == 'sb'){ //上半场
                $MB_Inball_HR	=	$val[0];
                $TG_Inball_HR	=	$val[1];
                $sql			=	"update baseball_match set mb_inball_hr='$MB_Inball_HR',tg_inball_hr='$TG_Inball_HR' where match_id in($mid)";
                $db->query($sql);
                
                echo 2;
                exit;
            }else{ //保存全场
                $MB_Inball		=	$val[0];
                $TG_Inball		=	$val[1];
                $sql			=	"update baseball_match set mb_inball='$MB_Inball',tg_inball='$TG_Inball' where match_id in($mid)";
                $mysqlis->query($sql);
                
                //保存所有全场单式注单比分
                $sql			=	"select bid from k_bet where lose_ok=1 and status=0 and match_id in($mid) order by bid desc ";
                $rows = Db::query($sql);
                $bid			=	"";
                while($rows		=	$result->fetch_array()){
                    $bid			.=	$rows["bid"].",";
                }
                if($bid != ""){ //全场
                    $bid	=	rtrim($bid,",");
                    $sql	=	"update k_bet set MB_Inball='$MB_Inball',TG_Inball='$TG_Inball' where bid in($bid)";
                    $rows = Db::query($sql);
                }
                echo 1;
                exit;
            }
        }else{
            echo 3;
            exit;
        }
    }
    
    public function setresult(){
        $param = $this->request->param();
        if($param['action'] ?? ''){
            $sql	=	"insert into t_guanjun_team(team_name,point,xid) values('".$_POST["team_name"]."','".floatval($_POST["point"])."','".$_GET["id"]."')";
            Db::connect('sportdb')->query($sql);
        }
        $sql	=	"select * from t_guanjun where x_id=".intval($_GET["id"])." limit 1";
        $rows = Db::connect('sportdb')->query($sql);
        if(!$rows){
            $this->error('系统未找到您查找的赛事。');
        }
        $this->assign('rows',$rows[0]);
        $sql	=	"select team_name,point,tid from t_guanjun_team where xid=".$_GET["id"];
        $list = Db::connect('sportdb')->query($sql);
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    public function setresultcmd(){
        
        $db = Db::connect('sportdb');
        $sql	=	"select x_result from t_guanjun where x_id=".$_GET["xid"]." limit 1"; //先取出原来的结果
        $rows	=	$db ->query($sql);
        $result	=	$rows[0]["x_result"];
        
        $sql	=	"select team_name from t_guanjun_team where tid=".$_GET["tid"]." limit 1"; //取出要添加上去的结果
        
        $rows	=	$db ->query($sql)[0];
        if($result) $result .= '<br>'.$rows['team_name']; //两个以上的结果
        else $result = $rows['team_name']; //未设置结果
        
        $sql	=	"update t_guanjun set x_result='$result' where x_id=".$_GET["xid"];
        $affected_rows = $db->query($sql);
        if($affected_rows == 1){
            sys_log(Session("adminid"),"设置了金融冠军赛事结果，金融冠军项目ID,".$_GET["xid"]);
        }
        header("location:".$_SERVER['HTTP_REFERER']);
    }
    
    public function teamdel(){
        $sql	=	'';
        if($_GET['type'] == 1){
            $sql	=	"update t_guanjun set x_result=null where x_id=".$_GET["xid"];
        }
        $flag = Db::connect('sportdb')->execute($sql);
        if($flag == 1){
            if($_GET['type'] == 1){
                sys_log(Session('adminid'), "清除了金融冠军赛事结果：".$_GET["xid"]);
            }
        }
        header("location:".$_SERVER['HTTP_REFERER']);
    }
}