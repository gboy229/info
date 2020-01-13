<?php
session_start();
require_once dirname(__FILE__).'/TnCode.class.php';
$tn  = new TnCode();

if($tn->check()){
	$_SESSION['tncode_check'] = 'ok';
	//setcookie('tncode_check','ok',time()+300,'/');
    echo "ok";
}else{
	$_SESSION['tncode_check'] = 'error';
	//setcookie('tncode_check','error',time()+300,'/');
	
    echo "error";
}

?>
