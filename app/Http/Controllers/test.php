<?php
define("OK", true);
session_start();
require_once("global.php");

if(!get_magic_quotes_gpc()){
        $_REQUEST[USERNAME]=addslashes($_REQUEST[USERNAME]);
        $_REQUEST[PASSWORD]=addslashes($_REQUEST[PASSWORD]);
}
//print_r($_GET);
if($_REQUEST[Memo]) $_POST[Memo]=$_REQUEST[Memo];
if(!isset($_SESSION['goip_username'])){
        //echo "1SELECT id FROM user WHERE username='".$_REQUEST[USERNAME]."' and password='".md5($_REQUEST[PASSWORD])."'";
        $rs=$db->fetch_array($db->query("SELECT id FROM user WHERE username='".$_REQUEST[USERNAME]."' and password='".md5($_REQUEST[
PASSWORD])."'"));

        if(empty($rs[0])){
                require_once ('login.php');
                exit;
        }
        $userid=$rs[0];
}
else $userid=$_SESSION[goip_userid];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<title>Sending Messge</title>
</head>
<body>
<?php
ignore_user_abort(true);
ob_end_flush();
set_time_limit(0);
ini_set("memory_limit", "1024M");
echo str_pad(" ", 256);
if($goipcronport)
        $port=$goipcronport;
else
        $port=44444;
        //ob_end_flush();

function restart(&$goiprow,$len,$msg)
{
        global $db;
        global $port;
        global $re_ask_timer;
        $query=$db->query("SELECT prov.*,goip.* FROM goip,prov where prov.id=goip.provider and alive=1 and gsm_status!='LOGOUT' and  goip.id=$goiprow[id]");
        $rs=$db->fetch_array($query);
        //echo "SELECT prov.*,goip.* FROM goip,prov where prov.id=goip.provider and alive=1 and gsm_status!='LOGOUT' and goip.id=$goiprow[id]". "restart: $rs[name] !!!<br>";
        if($rs[0]){
                if( $rs['remain_count']==0 || $rs['remain_count_d']==0) {
                        echo "GoIP Line($goiprow[name]) remain count is down<br>";
                        return;
                }
                $buf="DONE $goiprow[messageid]\n";
                if (@socket_sendto($goiprow[sock],$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                        echo ("sendto error");

                $goiprow['timer']=3;
                $goiprow['send']="MSG";
                //echo "$sendid $goiprow[id] $goiprow[messageid] <br>";
                $goiprow['time']=time();//??????
                $goiprow[host]=$rs[host];
                $goiprow[port]=$rs[port];
                $buf="START ".$goiprow['messageid']." $goiprow[host] $goiprow[port]\n";
                //echo $buf."<br>"."<br>"."<br>"."<br>"."<br>"."<br>";
                if (@socket_sendto($goiprow[sock],$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                        echo ("sendto error");
                for($i=0;$i<3;$i++){
                        //echo "check:$i";
                        $read=array($goiprow[sock]);
                        $err=socket_select($read, $write, $except, 5);
                        if($err>0){
                                //echo "11213134";
                                if(($n=@socket_recvfrom($goiprow[sock],$buf,1024,0,$ip,$port1))==false){
                                        echo("recvform error".socket_strerror($ret)."<br>");
                                        continue;
                                }
                                else{
                                        //echo "111111111:$buf";
 if($buf=="OK"){
                                                $flag=1;
                                                break;
                                        }
                                }
                        }
                }//for
                if($i>=3)
                        die("Cannot get response from process named \"goipcron\". please check this process.");
                //$buf="MSG ".$goiprow['messageid']." $len $msg\n";
                //if (@socket_sendto($socket,$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                        //echo ("sendto error");
        }
        else {
                echo "$goiprow[name] logout, after 100 seconds ask again.<br>";
                $goiprow[send]="RMSG";
                $goiprow[timer]=$re_ask_timer;
        }
}

/*????????????*/
        function dolastsend( &$goipsend,$len,$msg)
        {
                global $port;
                $sendid=$goipsend[messageid];
                //echo "dolastsend $goipsend[send]";
                if($goipsend[send]=="RMSG"){
                        if($goipsend[timer] <=1 ){
                                restart($goipsend,$len,$msg);
                                //echo "$goipsend[send] $goipsend[timer]";
                                //$goipsend[send]="MSG";
                                //$goipsend[timer]=3;
                        }
                        else return;
                }

                if($goipsend[send]=="HELLO"){
                        $buf="HELLO $sendid\n";
                        if (@socket_sendto($goipsend[sock],$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                                echo ("sendto error");
//$goipsend[timer]=0;

                }
                elseif($goipsend[send]=="PASSWORD"){
                        $buf="PASSWORD $sendid $goipsend[password]\n";

                        @socket_sendto($goipsend[sock],$buf, strlen($buf), 0, "127.0.0.1", $port);
                        //$goipsend[timer]=0;
                }
                elseif($goipsend[send]=="SEND"){
                        if($goipsend[telrow][msg])
                                $buf="SMS $sendid $goipsend[telid] $goipsend[password] ".$goipsend[telrow][tel]." ".$goipsend[telrow][msg];
                        else
                                $buf="SEND $sendid $goipsend[telid] $goipsend[tel]\n";
                        $buf="SEND $sendid $goipsend[telid] $goipsend[tel]\n";
                        @socket_sendto($goipsend[sock],$buf, strlen($buf), 0, "127.0.0.1", $port);
                        echo "$buf ($goipsend[name] $goipsend[prov]) <br>";
                        //$goipsend[timer]=0;
                }
                elseif($goipsend[send]=="MSG"){
                        $buf="MSG ".$sendid." $len $msg\n";
                        echo "$buf ($goipsend[name] $goipsend[prov]) <br>";
                        @socket_sendto($goipsend[sock],$buf, strlen($buf), 0, "127.0.0.1", $port);
                        //$goipsend[timer]=0;
                }
        }


        function do_cron($db,$crontime,$port)
        {
                if(!$port) $port=44444;
                $rs=$db->fetch_array($db->query("SELECT id FROM message WHERE crontime>0 and crontime<$crontime and over=0"));//???????????????????????????????????????????????????
                $flag=1;
                if(empty($rs[0])){
                        $flag=0;
                        /* ????????????????????? ??????????????????*/
                        if (($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) <= 0) {
                                echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
                                exit;
                        }
                        if (socket_sendto($socket,"CRON", 4, 0, "127.0.0.1", $port)===false)
                                echo ("sendto error:". socket_strerror($socket));
                        for($i=0;$i<3;$i++){
                                $read=array($socket);
                                $err=socket_select($read, $write, $except, 5);
                                if($err>0){
                                        if(($n=@socket_recvfrom($socket,$buf,1024,0,$ip,$port))==false){
                                                //echo("recvform error".socket_strerror($ret)."<br>");
                                                continue;
                                        }
                                        else{
                                                if($buf=="OK"){
                                                        $flag=1;
                                                        break;
                                                }
                                        }
                                }

                        }//for

                        }//????????????
                if($flag)
                        echo "The task has been inserted.";
                else
                        echo "The task has been inserted,but cannot get response from process named \"goipcron\". please check this process.";
        }

        function checktime()
        {
                $dabuf=explode(' ',$_POST[datehm]);
                $date=explode('-',$dabuf[0]);
                $time=explode(':',$dabuf[1]);
                //print_r($dabuf);
                //print_r($date);
                //print_r($time);
                //echo date ("Y-m-d H:i:s", $_POST[datehm]);
                $crontime=mktime ($time[0],$time[1],0,$date[1],$date[2],$date[0]);
                $nowtime=time();
                //if($nowtime > $crontime)
                        //die("????????????".date ("Y-m-d H:i:s")."??????????????????$_POST[datehm]???");

                return $crontime;
        }

        function checkover($goipdb)
        {
                global $endless_send;
                if(!$endless_send) return false;
                //echo "checkover <br>";
                foreach($goipdb as $the0 => $goipsend){
                        //echo $goipdb[$the0][send]." <br>";
                        if($goipsend[timer]>0){//??????
                                if($goipdb[$the0][send]!="RMSG"){  //????????????????????????????????????????????????GOIP
                                        return false;//?????????
                                }
                        }
                }
                return true;
        }
        /*????????????????????????*/
//print_r($_POST);
if(!isset($_POST['crowdid']) && !isset($_POST['groupid']) && !isset($_POST['alltype']) && !isset($_REQUEST['method']) && $_REQUEST['action']!='upload')
        die("Permission denied");
if(get_magic_quotes_gpc())
        $memo=stripslashes($_POST[Memo]);
else
        $memo=$_POST[Memo];
if(!get_magic_quotes_gpc())
        $_POST[Memo]=addslashes($_POST[Memo]);

        //exit;

$query=$db->query("SELECT * from prov ");
while($row=$db->fetch_assoc($query)) {
        //echo $row[id]." ".$row[inter]."<br>";
        $row[interlen]=strlen($row[inter]);
        $row[locallen]=strlen($row[local]);
        $prov[$row[id]]=$row;
        $prov_db[$row[prov]]=$row[id];
}

//envoi de message de groupe
if(isset($_POST['groupid'])){   //????????????????????????id
if(empty($_POST[groupid]))
        die("Permission denied");
if($_SESSION['goip_permissions'] > 1){

        $query=$db->query("SELECT id FROM refgroup WHERE groupsid=$_POST[groupid] and userid=$_SESSION[goip_userid]");
        $rs=$db->fetch_array($query);
        if(empty($rs[0])){
                $query=$db->query("SELECT groups.crowdid FROM refcrowd,groups WHERE groups.id=$_POST[groupid] and refcrowd.crowdid=groups.crowdid and refcrowd.userid=$_SESSION[goip_userid]");
                $rs=$db->fetch_array($query);
                if(empty($rs[0])){
                        die("Permission denied");
                }
        }
}

//      exit;
                $totalnum=0;


/*tels 6???????????????*/
                $num=$_POST['boxsa'];

                for($i=0;$i<$num;$i++)
                {
                        if(!empty($_POST["ida$i"])){
                                if($Ida=="")
                                        $Ida=$_POST["ida$i"];
                                else
                                        $Ida=$_POST["ida$i"].",$Ida";
                        }
                }
                for($i=0;$i<$num;$i++)
                {
                        if(!empty($_POST["idb$i"])){
                                if($Idb=="")
                                        $Idb=$_POST["idb$i"];
                                                                        else
                                        $Idb=$_POST["idb$i"].",$Idb";
                        }
                }
                for($i=0;$i<$num;$i++)
                {
                        if(!empty($_POST["idc$i"])){
                                if($Idc=="")
                                        $Idc=$_POST["idc$i"];
                                else
                                        $Idc=$_POST["idc$i"].",$Idc";
                        }
                }

                $total=get_count_from_sms($_POST['Memo']);
                if($_POST[submit2]){
                        $crontime=checktime();
                        if($_GET[id])
                                $db->query("update message set receiverid='$Ida', ")
                        else

                                $db->query("INSERT INTO message (receiverid,receiverid1,receiverid2,ms")
                        do_cron($db,$crontime,$goipcronport);
                        exit;
                }

                $tels=array();
                if(!$Ida) $Ida=0;
                if(!$Idb) $Idb=0;
                if(!$Idc) $Idc=0;
                $query=$db->query("(SELECT id,name,provider,tel,0 as lev FROM receiver ")
                $teltmp=0;
                while($row=$db->fetch_assoc($query)) {
                        if(!$row[tel]) continue;
                        $row['total']=$total;
                        if(!($prov[$row[provider]][locallen] && !strncmp($row[tel], $prov[$row[provider]][local], $prov[$row[provider]][locallen])) && $row[tel][0] !='+')
                                $row[tel]=$prov[$row[provider]][inter].$row[tel];
                        if($row[tel] && $row[tel] != $teltmp) {
                                $tels[$row[provider]][]=$row;
                                $teltmp=$row[tel];
                                $totalnum++;

                                 }
                }
                        //print_r($rsdb);
                /*?????????????????????*/
                //foreach($tels as $prov => $telrow){
                        //$tels[$prov]=array_unique($telrow);
                //}
                $db->query("INSERT INTO message (receiverid,receiverid1,receiverid2,msg,")
                $sendsiddb=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));
                $sendid=$sendsiddb[0];
                echo "sending message???$memo <br>";
                echo "total will send: $totalnum <br>";
                startdo($db, $tels, $sendid);
}elseif(isset($_POST['crowdid'])){//???????????????????????????
                $num=$_POST['boxsa'];

                for($i=0;$i<$num;$i++)
                {
                        if(!empty($_POST["id$i"])){
                                if($Id=="")
                                    $Id=$_POST["id$i"];
                                else
                                        $Id=$_POST["id$i"].",$Id";
                        }
                }
                if($_POST['jr']) $Ida=$Id;
                if($_POST['jr1']) $Idb=$Id;
                if($_POST['jr2']) $Idc=$Id;
                $total=get_count_from_sms($_POST['Memo']);
                if($_POST[submit2]){
                        $crontime=checktime();
                        if($_GET[id])
                                $db->query("update message set type=1,groupid='$Ida', groupid1='$Idb', groupid2='$Idc', msg='$_POST[Memo]', userid=$_SESSION[goip_userid],total='$total',cronti$
                        else
                                $db->query("INSERT INTO message (type,groupid,groupid1,groupid2,msg,userid,crontime,total) VALUES (1,'$Ida','$Idb','$Idc','$_POST[Memo]',$_SESSION[goip_userid]$
                        do_cron($db,$crontime,$goipcronport);
                        exit;
                }
                if(!$Ida) $Ida=0;
                if(!$Idb) $Idb=0;
                if(!$Idc) $Idc=0;
                $query=$db->query("(SELECT receiver.id,name,provider");
                $tels=array();
                $teltmp=0;
                while($row=$db->fetch_assoc($query)) {
                        if(!$row[tel]) continue;
                        $row['total']=$total;
                        if(!($prov[$row[provider]][locallen] && !strncmp($row[tel], $prov[$row[provider]][local], $prov[$row[provider]][locallen])) && $row[tel][0] !='+')
                                $row[tel]=$prov[$row[provider]][inter].$row[tel];
                        if($row[tel] && $row[tel] != $teltmp) {
                                $tels[$row[provider]][]=$row;
                                $teltmp=$row[tel];
                                $totalnum++;
                                //echo $teltmp."<br>";
                        }
                }
                //die("haha");

                echo "sending message???$memo <br>";
                echo "total will send: $totalnum <br>";
                //print_r($tels);
                $db->query("INSERT INTO message (type,groupid,groupid1,groupid2,msg,userid,total) VALUES (1,'$Ida','$Idb','$Idc','$_POST[Memo]',$_SESSION[goip_userid], '$total')");
                $sendsiddb=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));
                $sendid=$sendsiddb[0];
                startdo($db, $tels, $sendid);
                //}

}
elseif(isset($_POST['alltype'])) {//??????????????????
        //print_r($_POST);
        $jr=$_POST['jr']?1:0;
        $jr1=$_POST['jr1']?1:0;
        $jr2=$_POST['jr2']?1:0;
        $total=get_count_from_sms($_POST['Memo']);
        if($_POST[submit2]){
                $crontime=checktime();
                if($_GET[id])
                        $db->query("update message set type=2, recv=$jr, recv1=$jr1")
                else
                        $db->query("INSERT INTO message (type,msg,userid,crontime,recv,recv1,recv2");
                do_cron($db,$crontime,$goipcronport);
                exit;
        }
        $query=$db->query("(SELECT id,name,provider,tel,0 as lev FROM receiver where $jr");
        $tels=array();
        $teltmp=0;
        while($row=$db->fetch_assoc($query)) {
                if(!$row[tel]) continue;
                $row['total']=$total;
                if(!($prov[$row[provider]][locallen] && !strncmp($row[tel], $prov[$row[provider]][local], $prov[$row[provider]][locallen])) && $row[tel][0] !='+')
                        $row[tel]=$prov[$row[provider]][inter].$row[tel];
                if($row[tel] && $row[tel] != $teltmp) {
                        $tels[$row[provider]][]=$row;
                        $teltmp=$row[tel];
                        $totalnum++;
                }
        }

$db->query("INSERT INTO message (type,msg,userid,recv,recv1,recv2,total) VALUES (2,'$_POST[Memo]',$_SESSION[goip_userid],$jr,$jr1,$jr2,'$total')");
        $sendsiddb=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));
        $sendid=$sendsiddb[0];
        echo "sending message???$memo <br>";
        echo "total will send: $totalnum <br>";
        startdo($db, $tels, $sendid);
}
elseif($_REQUEST[method]==2) //??????????????????
{
        $provid=$_REQUEST[smsprovider];
        $mobile=$_REQUEST[smsnum];
        //$goipid=$_REQUEST['smsgoip'];
        if(!$_REQUEST[smsgoip] && $_REQUEST['goipname']){
                $rs=$db->fetch_array($db->query("select id from goip where name='$_REQUEST[goipname]'"));
                $_REQUEST[smsgoip]=$rs[0];
        }
        $total=get_count_from_sms($_REQUEST['Memo']);
        if($_REQUEST[submit2]){
                $crontime=checktime();
                if($_REQUEST[id])
                        $db->query("update message set msg='$_REQUEST[Memo]', userid=$useri");
                else
                        $db->query("INSERT INTO message (type,msg,userid,crontime,tel,prov");
                do_cron($db,$crontime,$goipcronport);
                exit;
        }
        $mobiles=array();
        $mobiles=explode(",",$mobile);
        foreach($mobiles as $tel_num){
                if(!($prov[$provid][locallen] && !strncmp($tel_num, $prov[$provid][local], $prov[$provid][locallen])) && $tel_num[0] !='+')
                        $tel_num=$prov[$provid][inter].$tel_num;
                $xrow['total']=$total;
                $xrow['provider']=$provid;
                $xrow['lev']=0;
                $xrow['id']=0;
                $xrow['tel']=$tel_num;
                $tels[$provid][]=$xrow;
                //$teltmp=$row[tel];
                $totalnum++;
        }

         $db->query("INSERT INTO message (msg,userid,type,tel,prov,goipid,total) VALUES ('$_POST[Memo]',$userid,4,'$mobile','$provid','$_REQUEST[smsgoip]','$total')");
        //echo "INSERT INTO message (msg,userid,type,tel,prov,goipid) VALUES ('$_POST[Memo]',$userid,4,'$mobile','$provid','$_REQUEST[smsgoip]')";
        $sendsiddb=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));
        $sendid=$sendsiddb[0];
                echo "sending message???$memo <br>";
                echo "total will send: $totalnum <br>";
        startdo($db, $tels, $sendid, $_REQUEST[smsgoip]);
}
elseif($_REQUEST['action']=="upload"){
        require_once('xmlClass.php');
        $data = implode("",file($_FILES['xmlfile']['tmp_name'])) or die("could not open XML input file");
        $obj = new xml($data,"xml");
/*
        $db->query("select * from prov order by id");
        while($row=$db->fetch_assoc($query)) {
                $prov_db[$row['prov']]=$row[id];
        }
*/
        for($i=0;$i<sizeof($xml["send_sms"]);$i++){
                $msg=addslashes($xml["send_sms"][$i]->content[0]);
                $tel_num=addslashes($xml["send_sms"][$i]->number[0]);
                $provname=addslashes($xml["send_sms"][$i]->provider[0]);
                if($tel_num && $prov && $prov_db[$provname]){
                        $provid=$prov_db[$provname];
                        if(!($prov[$provid][locallen] && !strncmp($tel_num, $prov[$provid][local], $prov[$provid][locallen])) && $tel_num[0]!='+')
                        $tel_num=$prov[$provid][inter].$tel_num;

                        $xrow['provider']=$provid;
                        $xrow['lev']=0;
                        $xrow['id']=0;
                        $xrow['tel']=$tel_num;
                        $xrow['msg']=$msg;
                        $xrow['total']=get_count_from_sms($msg);
                        $tels[$provid][]=$xrow;
                        $totalnum++;
                        if($totalnum==1)
                                $mobile=$tel_num;
                        else
                                $mobile.=",".$tel_num;
                }
                //$goipname=addslashes($xml["send_sms"][$i]->goipname[0]);
                echo "$msg $tel_num $provname $provid $goipname";
        }
        if($totalnum > 0){
                //echo "INSERT INTO message (userid,type,tel) VALUES ($userid,9,'$mobile')";
                //die;
                $db->query("INSERT INTO message (userid,type,tel) VALUES ($userid,9,'$mobile')");
                $sendsiddb=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));
                $sendid=$sendsiddb[0];
                echo "total will send: $totalnum <br>";
                startdo($db, $tels, $sendid);
        }
        else die("none sms");

}
else print_r($_POST);

function startdo($db, $tels,$sendid, $goipid=0){
                global $prov;
                global $port;
                global $memo;
                global $userid;
                global $endless_send;
                global $re_ask_timer;

                session_write_close();

                $query=$db->query("SELECT send_page_jump_enable FROM system WHERE 1 ");
                $sys_rs=$db->fetch_array($query);

                if($_REQUEST['not_jump']!=1 && $sys_rs['send_page_jump_enable']==1){
                        $i=0;
                        $sqlv="insert into sends (messageid,userid,telnum,provider,recvid,recvlev,time,msg,total) values";
                        foreach($tels as $provname => $provtels){
                                foreach($provtels as $send){
                                        $sql.="($sendid,$userid,'".$send[tel]."','$provname',".$send[id].",".$send[lev].",'','".$send[msg]."','".$send[total]."'),";
                                        $i++;
                                        if($i%2000==0){
                                                $sql[strlen($sql)-1]="";
                                                 $db->query($sqlv.$sql);
                                                $sql="";
                                        }
                                }
                        }
                        if($sql){
                                $sql[strlen($sql)-1]="";
                                $db->query($sqlv.$sql);
                        }
                        //die;
                        echo "<script language='javascript'>";
                        echo "window.location = 'resend.php?messageid=".$sendid."&USERNAME=".$_REQUEST[USERNAME]."&PASSWORD=".$_REQUEST[PASSWORD]."'";
                        echo "</script>";
                        die;
}

                //print_r($prov);
                $msg=$memo;
                $len=strlen($msg);
                $nowtime=date ("Y-m-d H:i:s");
                //$sendid=1111;
                //$id=0;
                //$query=$db->query("SELECT prov.*,goip.* FROM goip,prov where prov.id=goip.provider and alive=1 and gsm_status!='LOGOUT' ORDER BY name");
                $db->query("update message set over=1 where id=$sendid");
                $query=$db->query("SELECT prov.*,goip.* FROM goip,prov where prov.id=goip.provider ORDER BY name");
                $socks=array();
                $errortels=array();
                $goipdb=array();
                while($goiprow=$db->fetch_array($query)) {
                        $goipname[]=$goiprow[provider];



                        /*??????????????????*/
                        if(($goipid && $goiprow[id]==$goipid) || (!$goipid && $tels[$goiprow[provider]])){ //?????????????????????????????????????????????
                                $errortels[$goiprow[provider]]=array();
                                if (($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) <= 0) {
                                        echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
                                        exit;
                                }

                                $goiprow['sock']=$socket;
                                $goiprow['time']=time();//??????
                                if($goiprow[alive] != 1 || $goiprow[gsm_status] == 'LOGOUT'){
                                        $goiprow['timer']=$re_ask_timer;
                                        $goiprow['send']="RMSG";
                                        $goiprow['messageid']=$sendid+($goiprow[id] << 16);
                                        $goipdb[]=$goiprow;
                                        $socks[]=$socket;
                                        echo "$goiprow[name] logout, after 100 seconds ask again.<br>";
                                        continue;
                                }
                                $goiprow['timer']=3;
                                $goiprow['send']="MSG";
                                $goiprow['messageid']=$sendid+($goiprow[id] << 16);
                                $goipdb[]=$goiprow;
                                echo "$sendid $goiprow[id] $goiprow[messageid] <br>";
                                $buf="START ".$goiprow['messageid']." $goiprow[host] $goiprow[port]\n";
                                if (@socket_sendto($socket,$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                                        echo ("sendto error");
                                for($i=0;$i<3;$i++){
                                         $read=array($socket);
                                        $err=socket_select($read, $write, $except, 5);
                                        if($err>0){
                                                if(($n=@socket_recvfrom($socket,$buf,1024,0,$ip,$port1))==false){
                                                        //echo("recvform error".socket_strerror($ret)."<br>");
                                                        continue;
                                                }
                                                else{
                                                        if($buf=="OK"){
                                                                $flag=1;
                                                                break;
                                                        }
                                                }
                                        }

                                }//for
                                if($i>=3)
                                        die("Cannot get response from process named \"goipcron\". please check this process.");
                                $buf="MSG ".$goiprow['messageid']." $len $msg\n";
                                if (@socket_sendto($socket,$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                                        echo ("sendto error");
                                $socks[]=$socket;
                                //print_r($goiprow);
                        }
                        //print_r($goiprow);
                }
                foreach($tels as $provtmp => $valuetmp){
                        foreach($goipdb as $goiptmp){
                                if($goiptmp['provider']==$provtmp)
                                        continue 2;
                        }
                        $n=count($valuetmp);
                        echo "<font color='#FF0000'>Will send {$n} message(s) to receivers of ");
                }
                //$read = array($socket);
                $timeout=5;
                for(;;){
                        $read=$socks;
                        flush();
                        if(count($read)==0)
                                break;
                        $err=socket_select($read, $write, $except, $timeout);
                        //echo "select:$err <br>";
                        if($err===false)
                                echo "select error!";
                        elseif($err==0){ //????????????
                                //echo "select timeout";
                                $i=0;
                                $flag=1;
                                $nowtime=time();
                                //reset($goipdb);
                                //while (list (, $goipsend) = each ($goipdb)) {
                                foreach($goipdb as $the0 => $goipsend){
                                        //$goipsend=$goipdb[$the0];
                                        $goipdb[$the0]['time']=$nowtime;
                                        //echo("<br>$i $goipsend[send] timer:".$goipsend[timer]."<br>");
                                        if($goipsend[timer]>0){//??????

                                                if($goipdb[$the0][send]!="RMSG")  //????????????????????????????????????????????????GOIP
                                                        $flag=0;//?????????
                                                        dolastsend( $goipdb[$the0],$len,$msg);
                                                $goipdb[$the0]['timer']--;
                                                //echo("<br>$i $goipsend[send] timer:".$goipsend[timer]."<br>");
                                                $i++;
                                        }
                                        else{ //????????????
                                                if($goipsend[send]=="OK") //????????????
                                                        continue;

                                                if($goipsend[send]=="SEND"){
                                                        //echo "inser: $goipnow[telid] $goipnow[tel] faile<br>";
                                                        echo "<font color='#FF0000'>$goipnow[telid] $goipnow[tel] faile</font><br>";
                                                        foreach($goipdb as $the => $goiptmp){
                                                                if($goiptmp[send]=="OK" && $goiptmp[provider]==$goipsend[provider]){
                                                                        if($goipsend[telrow][error] && in_array($goiptmp[id],$goipsend[telrow][error]))
                                                                                continue;//???????????????
                                                                        $goipdb[$the][send]="SEND";
                                                                        $goipdb[$the][tel]=$goipsend[tel];
                                                                        $goipdb[$the][telid]=$goipsend[telid];
                                                                        //$goipsend[send]=="OK"; //??????
                                                                        dolastsend( $goiptmp,$len,$msg);
                                                                        $goipdb[$the0][send]="RMSG";//?????????goip???100s?????????
                                                                        $goipdb[$the0][timer]=$re_ask_timer;
                                                                        $goipdb[$the0][tel]=0;
                                                                        $goipdb[$the0][telid]=0;
                                                                        break;
                                                                }
                                                        }
                                                }
                                                else{
                                                        /*100s ???????????????*/
                                                        echo "<font color='#FFOO00'>cannot get response from goip: $goipsend[send] ($goipsend[name] $goipsend[prov])</font><br>";
                                                        $goipdb[$the0][send]="RMSG";
                                                        $goipdb[$the0][timer]=$re_ask_timer;
                                                }
                                                if($goipsend[send]=="SEND"){//?????????????????????goip?????????????????????100s???????????????
                                                        if($goipsend[telrow][error])
                                                                array_push($errortels[$goipsend[provider]], $goipsend[telrow]);//??????????????????
                                                        else
                                                                array_push($tels[$goipsend[provider]], $goipsend[telrow]); //??????
                                                                //array_push($tels[$goipsend[provider]], $goipsend[telrow]); //??????
                                                        /*???????????????*/
                                                        $db->query("delete from sends where id=$goipsend[telid]");
                                                        $goipdb[$the0][send]="RMSG";
                                                        $goipdb[$the0][timer]=$re_ask_timer;
                                                        $goipdb[$the0][tel]=0;
                                                        $goipdb[$the0][telid]=0;
                                                }
                                        }
                                }

                                if($flag && !$endless_send)
                                        break; //????????????

                        }//????????????
                        else{ //??????

                          foreach($read as $socket){
                                unset($buf);
                                //$buf="";

                                if(($n=@socket_recvfrom($socket,$buf,1024,0,$ip,$port1))==false){
                                        echo("recvform error".socket_strerror($ret)."<br>");
                                        continue;
                                }
                                //echo "hahaha ".$buf;
                                  $comm=explode(" ",$buf);
                                        //$teli=(int)substr($comm[1], -1)-1;
                                        foreach($goipdb as $the => $goipnow){
                                                //echo "$key => $val\n";
                                                if($goipnow[sock]==$socket){
                                                        break;
                                                }
                                        }

                                        if(empty($goipnow)){ //????????????????????????
                                                continue;
                                        }
                                        if(strncmp($goipnow[messageid],$comm[1], strlen($goipnow[messageid])))//???????????????id
                                                continue;
                                        //echo "$goipnow[name] <br>";
                                        if($comm[0]=="OK"){
                                                //??????????????????????????????
                                                if(is_numeric($comm[3]))
                                                        $db->query("update sends set over=1,sms_no=$comm[3]");
                                                else
                                                        $db->query("update sends set over=1,goipid=$goipnow[id],time=now() where id='".$goipnow[telid]."' and messageid=$sendid");

                                                /**/
                                                if($goipnow[send]!="SEND"){//??????????????????????????????
                                                        //echo "net send status <br>";
                                                        continue;
                                                }
                                                if($comm[2]==$goipnow[telid]){ //?????????????????????,?????????????????????

                                                        echo "<font color='#00FF00'>$goipnow[telid] ".$goipnow[telrow][name]." $goipnow[tel] ($goipnow[name] $goipnow[prov]) ok</font><br>";
                                                        $goipdb[$the]['send']="OK";//?????????
                                                        $goipdb[$the][telid]=0;
                                                        $goipdb[$the][tel]=0;
                                                        $goipdb[$the][timer]=0;
                                                        if(!check_sms_remain_count($db,$goipnow[id],$goipnow[name],$goipdb[$the][telrow][total])) continue;
                                                        if(($goipdb[$the][telrow]=array_pop($tels[$goipnow[provider]]))!==NULL){//??????
                                                                /*????????????????????????id, ??????*/

                                                                        $goipdb[$the][tel]=$goipdb[$the][telrow][tel];
                                                                $db->query("INSERT INTO sends (messageid,userid,telnum,goipid,provider,recvid,recvlev,msg,total) VALUES ($sendid,$userid,'".$go$
                                                                $telid=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));
                                                                $goipdb[$the][telid]=$telid[0];
                                                                //$goipdb[$the][telid]=$testid++;
                                                                //echo "inser: ".$goipdb[$the][telid]." ".$goipdb[$the][tel]." start<br>";
                                                                $goipdb[$the]['send']="SEND";
/*

                                                                $buf="SEND ".$goipdb[$the][messageid]." ".$goipdb[$the][telid]." ".$goipdb[$the][tel]."\n";
                                                                echo "$buf ($goipnow[name] $goipnow[prov])<br>";
                                                                if (socket_sendto($socket,$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                                                                        echo ("sendto error");
*/
                                                                $goipdb[$the][timer]=3;
                                                                dolastsend( $goipdb[$the],$len,$msg);
                                                        }
                                                        elseif($errortels[$goipnow[provider]]){
                                                                foreach($errortels[$goipnow[provider]] as $telthe => $nowrow){
                                                                        if(!in_array($goipnow[id],$nowrow['error'])){
                                                                                $goipdb[$the][telrow]=$nowrow;
                                                                                unset($errortels[$goipnow[provider]][$telthe]);
                                                                                        $goipdb[$the][tel]=$goipdb[$the][telrow][tel];
                                                                                $db->query("INSERT INTO sends ");

                                                                                $goipdb[$the][telid]=$telid[0];
                                                                                //$goipdb[$the][telid]=$testid++;
                                                                                //echo "inser: ".$goipdb[$the][telid]." ".$goipdb[$the][tel]." start<br>";
                                                                                $goipdb[$the]['send']="SEND";
/*
                                                                                $buf="SEND ".$goipdb[$the][messageid]." ".$goipdb[$the][telid]." ".$goipdb[$the][tel]."\n";
                                                                                echo "$buf ($goipnow[name] $goipnow[prov])<br>";
                                                                                if (socket_sendto($socket,$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                                                                                        echo ("sendto error
*/
                                                                                $goipdb[$the][timer]=3;
                                                                                dolastsend( $goipdb[$the],$len,$msg);
                                                                                break;
                                                                        }
                                                                }
                                                        }
                                                        if(checkover($goipdb)) break 2;
                                                }

                                        }
                                        elseif($comm[0]=="WAIT"){
                                                echo "WAIT $goipnow[send] $comm[2] ($goipnow[name] $goipnow[prov] $goipnow[tel])<br>";
                                                if($goipnow[send]=="SEND" && $comm[2]==$goipnow[telid]){
                                                        $goipdb[$the][timer]=3;//????????????
                                                }
                                        }
                                        elseif($comm[0]=="MSG"){ //???????????????
                                        }
                                        elseif($comm[0]=="SEND"){
                                                //$goipnow['ok']=1;
                                                 //$goipnow[send]="SEND";
                                                if($goipnow[send]=="SEND")//????????????????????????
                                                        continue;
                                                $goipdb[$the]['send']="OK";//?????????
                                                $goipdb[$the][telid]=0;
                                                $goipdb[$the][tel]=0;
                                                $goipdb[$the][timer]=0;
                                                if(!check_sms_remain_count($db,$goipnow[id],$goipnow[name],0)) continue;
                                                if(($goipdb[$the][telrow]=array_pop($tels[$goipnow[provider]]))!==NULL){//??????
                                                        /*????????????????????????id, ??????*/
                                                                $goipdb[$the][tel]=$goipdb[$the][telrow][tel];
                                                        $db->query("INSERT INTO sends (messageid,userid,telnum,goipid,provider,recvid,recvlev,msg,total) VALUES ($sendid,$userid,'".$goipdb[$th$
                                                        $telid=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));

                                                        $goipdb[$the][telid]=$telid[0];
                                                        //$goipdb[$the][telid]=$testid++;
                                                        //echo "inser: ".$goipdb[$the][telid]." ".$goipdb[$the][tel]." start<br>";
                                                        $goipdb[$the]['send']="SEND";
/*
                                                        $buf="SEND ".$goipdb[$the][messageid]." ".$goipdb[$the][telid]." ".$goipdb[$the][tel]."\n";
                                                        echo "$buf ($goipnow[name] $goipnow[prov])<br>";
                                                        if (socket_sendto($socket,$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                                                                echo ("sendto error");
*/
                                                        $goipdb[$the][timer]=3;
                                                        dolastsend( $goipdb[$the],$len,$msg);
                                                }
                                                elseif($errortels[$goipnow[provider]]){
                                                        foreach($errortels[$goipnow[provider]] as $telthe => $nowrow){
                                                                if(!in_array($goipnow[id],$nowrow[error])){
                                                                        $goipdb[$the][telrow]=$nowrow;
                                                                        unset($errortels[$goipnow[provider]][$telthe]);

                                                                        $goipdb[$the][tel]=$goipdb[$the][telrow][tel];
                                                                        $db->query("INSERT INTO sends ");
                                                                        $telid=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));

                                                                        $goipdb[$the][telid]=$telid[0];
                                                                        //$goipdb[$the][telid]=$testid++;
                                                                        //echo "inser: ".$goipdb[$the][telid]." ".$goipdb[$the][tel]." start<br>";
                                                                        $goipdb[$the]['send']="SEND";
/*
                                                                        $buf="SEND ".$goipdb[$the][messageid]." ".$goipdb[$the][telid]." ".$goipdb[$the][tel]."\n";
                                                                        echo "$buf ($goipnow[name] $goipnow[prov])<br>";
                                                                        if (socket_sendto($socket,$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                                                                                echo ("sendto error");
*/
                                                                        $goipdb[$the][timer]=3;
                                                                        dolastsend( $goipdb[$the],$len,$msg);
                                                                        break;
                                                                }
                                                        }
                                                }
                                                if(checkover($goipdb)) break 2;
                                        }
                                        elseif($comm[0]=="PASSWORD"){
                                                //echo "PASSWORD ".$goipnow['send'];
                                                //$teli=substr($comm[1], -1);
                                                if($goipnow['send']!="PASSWORD" && $goipnow['send']!="MSG")//????????????????????????????????????
                                                        continue;
                                                if($goipnow['send']=="MSG"){
                                                        $goipdb[$the]['timer']=3;
                                                        $goipdb[$the]['send']="PASSWORD";
                                                }
                                                if($goipdb[$the]['send']=="PASSWORD"){
                                                        if($goipdb[$the]['timer']-- > 0)
                                                                dolastsend( $goipdb[$the],$len,$msg);
                                                        else {
                                                                $goipdb[$the]['timer']=$re_ask_timer;
                                                                $goipdb[$the]['send']="RMSG";
                                                        }
                                                }
                                                /*
                                                socket_sendto($socket,"PASSWORD $goipnow[messageid] $goipnow[password]\n", strlen("PASSWORD $comm[1] $goipnow[password]\n"), 0, "127.0.0.1", $p$
                                                $goipdb[$the][send]="PASSWORD";
                                                $goipdb[$the][timer]=3;
                                                */
                                        }
                                        elseif($comm[0]=="ERROR"){
                                                echo "<font color='#FF0000'>$buf($goipnow[name] $goipnow[prov] $goipnow[tel] ".$goipdb[$the][send].")</font><br>";
                                                                                                if($goipdb[$the][send]=="PASSWORD" && ($comm[2]=="SENDID" || $comm[2]=="GSM_LOGOUT")){
                                                                $goipdb[$the]['timer']=$re_ask_timer;
                                                                $goipdb[$the]['send']="RMSG";
                                                }
                                                elseif($goipdb[$the][send]=="SEND" && ($comm[2]=="SENDID" || $comm[2]=="GSM_LOGOUT")){//sendid??????
                                                        $goipdb[$the]['telrow']['error'][]=$goipdb[$the]['id'];
                                                        $findokflag=0;
                                                        foreach($goipdb as $the1 => $goiptmp){
                                                                if($goiptmp[send]=="OK" && $goiptmp[provider]==$goipsend[provider] && !in_array($goiptmp[id],$goipdb[$the]['telrow']['error']) $
                                                                        $goipdb[$the1][send]="SEND";
                                                                        $goipdb[$the1][tel]=$goipnow[tel];
                                                                        $goipdb[$the1][telid]=$goipnow[telid];
                                                                        $goipdb[$the1][telrow]=$goipdb[$the]['telrow'];
                                                                        //$goipdb[$the1][telrow]['error']=$goipnow[telrow]['error'];
                                                                        $goipdb[$the1][timer]=3;
                                                                        $findokflag=1;
                                                                        $db->query("update sends set goipid=$goiptmp[id] where id=$goipnow[telid]");
                                                                        dolastsend( $goipdb[$the1],$len,$msg);
                                                                        break;
                                                                }

                                                        }
                                                        if(!$findokflag){
                                                                array_push($errortels[$goipsend[provider]], $goipdb[$the][telrow]);
                                                                $db->query("delete from sends where id=$goipnow[telid]");
                                                                //array_push($tels[$goipsend[provider]], $goipsend[tel]); //??????
                                                        }

                                                        $goipdb[$the][telid]=0;
                                                        $goipdb[$the][tel]=0;
                                                        if($comm[2]=="SENDID"){
                                                                $goipdb[$the]['send']="MSG";//?????????
                                                                $goipdb[$the][timer]=3;
                                                                dolastsend($goipdb[$the],$len,$msg);
                                                        }
                                                        elseif($comm[2]=="GSM_LOGOUT"){
                                                                echo "GSM_LOGOUT!!!!ask after 100S<br>";
                                                                $goipdb[$the]['timer']=$re_ask_timer;
                                                                $goipdb[$the]['send']="RMSG";                                                                                               }
                                                }
                                                elseif($goipdb[$the][send]=="SEND" && $comm[2]==$goipnow[telid]){//????????????
                                                                                                                $goipdb[$the]['telrow']['error'][]=$goipdb[$the]['id'];
                                                        $findokflag=0;
                                                        foreach($goipdb as $the1 => $goiptmp){
                                                                if(!check_sms_remain_count($db,$goiptmp['id'],$goiptmp['name'])) continue;
                                                                if($goiptmp[send]=="OK" && $goiptmp[provider]==$goipsend[provider] && !in_array($goiptmp[id],$goipdb[$the]['telrow']['error']) $
                                                                        $goipdb[$the1][send]="SEND";
                                                                        $goipdb[$the1][tel]=$goipnow[tel];
                                                                        $goipdb[$the1][telid]=$goipnow[telid];
                                                                        $goipdb[$the1][telrow]=$goipdb[$the]['telrow'];
                                                                        //$goipdb[$the1][telrow]['error']=$goipnow[telrow]['error'];
                                                                        $goipdb[$the1][timer]=3;
                                                                        $findokflag=1;
                                                                        $db->query("update sends set goipid=$goiptmp[id] where id=$goipnow[telid]");
                                                                        dolastsend( $goipdb[$the1],$len,$msg);
                                                                        break;
                                                                }
                                                        }

                                                        if(!$findokflag){
                                                                array_push($errortels[$goipsend[provider]], $goipdb[$the][telrow]);
                                                                $db->query("delete from sends where id=$goipnow[telid]");
                                                                //array_push($tels[$goipsend[provider]], $goipsend[tel]); //??????
                                                        }

                                                        $goipdb[$the]['send']="OK";//?????????
                                                        $goipdb[$the][telid]=0;
                                                        $goipdb[$the][tel]=0;
                                                        $goipdb[$the][timer]=0;
                                                        if(!check_sms_remain_count($db,$goipdb[$the][id],$goipdb[$the][name])) continue;
                                                        if(($goipdb[$the][telrow]=array_pop($tels[$goipnow[provider]]))!==NULL){//??????
                                                                /*????????????????????????id, ??????*/

                                                                        $goipdb[$the][tel]=$goipdb[$the][telrow][tel];
                                                                $db->query("INSERT INTO sends (messageid,userid,telnum,goipid,provider,recvid,recvlev,msg,total) VALUES ($sendid,$userid,'".$go$
                                                                $telid=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));

                                                                $goipdb[$the][telid]=$telid[0];
                                                                //$goipdb[$the][telid]=$testid++;
                                                                //echo "inser: ".$goipdb[$the][telid]." ".$goipdb[$the][tel]." start<br>";
                                                                $goipdb[$the]['send']="SEND";
/*
                                                                $buf="SEND ".$goipdb[$the][messageid]." ".$goipdb[$the][telid]." ".$goipdb[$the][tel]."\n";
                                                                echo "$buf ($goipnow[name] $goipnow[provider])<br>";
                                                                if (socket_sendto($socket,$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                                                                        echo ("sendto error");
*/
                                                                $goipdb[$the][timer]=3;
                                                                dolastsend( $goipdb[$the],$len,$msg);
                                                        }
                                                        elseif($errortels[$goipnow[provider]]){
                                                                foreach($errortels[$goipnow[provider]] as $telthe => $nowrow){
                                                                        if(!in_array($goipnow[id],$nowrow[error])){
                                                                                $goipdb[$the][telrow]=$nowrow;
                                                                                unset($errortels[$goipnow[provider]][$telthe]);
                                                                                        $goipdb[$the][tel]=$goipdb[$the][telrow][tel];
                                                                                $db->query("INSERT INTO sends ");
                                                                                $telid=$db->fetch_array($db->query("SELECT LAST_INSERT_ID()"));

                                                                                $goipdb[$the][telid]=$telid[0];
                                                                                //$goipdb[$the][telid]=$testid++;
                                                                                        //echo "inser: ".$goipdb[$the][telid]." ".$goipdb[$the][tel]." start<br>";
                                                                                $goipdb[$the]['send']="SEND";
/*
                                                                                $buf="SEND ".$goipdb[$the][messageid]." ".$goipdb[$the][telid]." ".$goipdb[$the][tel]."\n";
                                                                                echo "$buf ($goipnow[name] $goipnow[prov])<br>";
                                                                                if (socket_sendto($socket,$buf, strlen($buf), 0, "127.0.0.1", $port)===false)
                                                                                        echo ("sendto error");
*/
                                                                                $goipdb[$the][timer]=3;
                                                                                dolastsend( $goipdb[$the],$len,$msg);
                                                                                break;
                                                                        }
                                                                }
                                                        }
                                                        if(checkover($goipdb)) break 2;
                                                }
                                                else if($goipnow[timer]>0){
                                                        dolastsend( $goipdb[$the],$len,$msg);
                                                        $goipdb[$the][timer]--;
                                                }
                                                 else //??????????????????
                                                        continue;
                                        }//elseif($comm[0]=="ERROR"){
                                        $goipdb[$the]['time']=time();
                                //}//foreach($bufline as $line){
                          }//foreach($read
                                $i=0;

                                $nowtime=time();
                                foreach($goipdb as $the0 => $goipsend){
                                        $flag=1;
                                        //echo "12346789000000000000000000 $goipsend[time]  $nowtime<br>";
                                        if($goipsend['time'] <$nowtime-$timeout && $goipsend[send]!="OK"){//?????????
                                                $goipdb[$the0]['time']=$nowtime;
                                                if($goipsend[timer]>0){//??????
                                                        if($goipdb[$the0][send]!="RMSG")  //????????????????????????????????????????????????GOIP
                                                                $flag=0;//?????????
                                                        dolastsend( $goipdb[$the0],$len,$msg);
                                                        $goipdb[$the0][timer]--;
                                                        //echo("<br>$i timer:".$goipsend[timer]."<br>");
                                                         $i++;
                                                }
                                                else{ //????????????
                                                        if($goipsend[send]=="SEND"){
                                                                echo "<font color='#FF0000'>$goipnow[name] $goipnow[telid] $goipnow[tel] cannot get answer</font><br>";
                                                                //$db->query("dela");
                                                                foreach($goipdb as $the => $goiptmp){
                                                                        if($goiptmp[send]=="OK" && $goiptmp[provider]==$goipsend[provider]){
                                                                                if($goipsend[telrow][error] && in_array($goiptmp[id],$goipsend[telrow][error]))
                                                                                        continue;//???????????????
                                                                                $goipdb[$the][send]="SEND";
                                                                                $goipdb[$the][tel]=$goipsend[tel];
                                                                                $goipdb[$the][telid]=$goipsend[telid];
                                                                                $goipdb[$the0][send]=="OK"; //??????
                                                                                dolastsend( $goiptmp,$len,$msg);
                                                                                $goipdb[$the0][send]="RMSG";//?????????goip???100s?????????
                                                                                $goipdb[$the0][timer]=$re_ask_timer;
                                                                                $goipdb[$the0][tel]=0;
                                                                                $goipdb[$the0][telid]=0;
                                                                                break;
                                                                                 }

                                                                }
                                                        }
                                                        else{
                                                                echo "<font color='#FFOO00'>2cannot get response from goip:  $goipsend[send] ($goipsend[name] $goipsend[provider])</font><br>";
                                                                /*100s ???????????????*/
                                                                $goipdb[$the0][send]="RMSG";
                                                                $goipdb[$the0][timer]=$re_ask_timer;
                                                        }
                                                        if($goipsend[send]=="SEND"){//?????????????????????goip?????????????????????100s???????????????
                                                                if($goipsend[telrow][error])
                                                                        array_push($errortels[$goipsend[provider]], $goipsend[telrow]);//??????????????????
                                                                else
                                                                        array_push($tels[$goipsend[provider]], $goipsend[telrow]); //??????

                                                                /*???????????????*/
                                                                $db->query("delete from sends where id=$goipsend[telid]");
                                                                $goipdb[$the0][send]="RMSG";
                                                                $goipdb[$the0][timer]=$re_ask_timer;
                                                                 $goipdb[$the0][tel]=0;
                                                                $goipdb[$the0][telid]=0;
                                                        }
                                                        if(checkover($goipdb)) break 2;
                                                }
                                        }
                                        //$goipdb[$the0]['time']=$nowtime;

                                        //else $flag++;//??????
                                }
                        }//else{ //??????
                        /*????????????*/
                }//for(;;){
                foreach($socks as $socket){
                        foreach($goipdb as $the => $goipnow){
                                //echo "$key => $val\n";
                                if($goipnow[sock]==$socket){
                                        break;
                                }
                        }
                         if($goipnow[sock]==$socket)
                                socket_sendto($socket,"DONE $goipnow[messageid]\n", strlen("DONE $goipnow[messageid]\n"), 0, "127.0.0.1", $port);
                }


                $i=0;
                foreach($tels as $provname => $provtels){
                        foreach($provtels as $send){
                                $db->query("INSERT INTO sends (messageid,userid,telnum,provider,recvid,recvlev,msg,total) VALUES ($sendid,$userid,'".$send[tel]."','$provname',".$send[id].",".$
                                $i++;
                        }
                }
                foreach($errortels as $provname => $provtels){
                        foreach($provtels as $send){
                                $db->query("INSERT INTO sends (messageid,userid,telnum,provider,recvid,recvlev,msg,total) VALUES ($sendid,$userid,'".$send[tel]."','$provname',".$send[id].",".$
                                $i++;
                        }
                }
                $db->query("update message set over=2 where id=$sendid");
                echo "All sendings done! Failure:{$i}";
                 echo "<br><br>";
                echo "<a href=sendinfo.php?id=$sendid target=main><font size=2'>Click me to check details.</font></a>";

}
?>
</body>
</html>