<?php
include "BigInteger.php";
include "BigIntext.php";
include('rsa.class.php');

$RSA = new RSA();
$keys = $RSA->generate_keys ('62700433', '62702257', 1);
$message='大部分浏览器在访问https网站的时候都会在显眼的地方提供查看证书的功能。一般来说应该是一个锁头一样的图标。证书可以帮助你确认站点身份，这样你就不会被钓鱼或DNS欺骗。这些具体的欺骗手法我以后还会说到。只要判断证书是否是由是可信任的机构颁发的，是否是这个网站的正确拥有者所有，就可以知道你浏览器打开的这个网站是不是真身了。证书有问题，那么这个网站一定有问题，这时候就不要进行登录或是找回密码或是重设密码之类的操作了，无论页面上写了什么，牢记这个原则，证书不符合，就是不可信任的。对于163,sina这种邮箱来说，只在登陆的时候提供了ssl加密，用户在输入密码之前甚至没有机会察看一下证书，实在是令人遗憾的事。';
$time1=time();
$encoded = $RSA->encrypt ($message, $keys[1], $keys[0], 6);
$time2=time()-$time1;
$len=strlen($message);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
 <script type='text/javascript' src='jsbn.js'></script>
 <script type='text/javascript' src='jsbn2.js'></script>
 <script type='text/javascript' src='rsa.js'></script>

<body>
<span id="encode"><?php echo $encoded;?></span>
<br><span id="time"><?php echo $encoded;?></span>
</body>
 <script>
         var d=<?php echo $keys[2]?>;
         var n=<?php echo $keys[0]?>;
         var s=document.getElementById('encode').innerHTML;
         var before = new Date();
         str=decrypt(s,d,n);
         var after= new Date();
         var time="Time: " + (after - before) + "ms";
         document.getElementById('encode').innerHTML=str;
         document.getElementById('time').innerHTML=time;
 </script>
 </html>