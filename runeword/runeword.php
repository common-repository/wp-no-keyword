<?php
/********************
 “符文之语” -- Rune Word
 darasion[at]gmail.com
 
 躲避关键字过滤的一种尝试。
*********************/

// 以下是php代码举例(在tubewall代码里取出的)：

// 中文Base64的编码表
// 由65个互不相同的字符组成
// 例如，天干、地支、十二生肖、五行、八卦、元素周期表，等。
// 原则是不要使用可能组合成关键词的字符。
// 
$nkw_runeword_alpha = '甲乙丙丁戊己庚辛壬癸子丑寅卯辰巳午未申酉戌亥金木水火土乾兑离震巽坎艮坤壹贰叁肆伍陆柒捌玖春夏秋冬宫商角徵羽鼠牛虎兔龙蛇马羊猴鸡狗猪';

// 也可以多于65个字符，打乱后取其中65个字符即可
//$nkw_runeword_alpha .= 'abcdefghijklmnopqrstuvwxyz';

// 数字 没有8964，囧。
//$nkw_runeword_alpha .= '012357';


// 这里采用了一种投机取巧的办法，直接将正常的base64编码后英文字符的相应的位置映射到中文编码表的相应位置。

// base64编码改

function rw_encode($in){
	
	global $nkw_runeword_alpha;
	
	$tp = base64_encode($in);
	$mp = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
	$out='';
	
	for($i=0,$l = strlen($tp); $i<$l; $i++){
		$out.= mb_substr($nkw_runeword_alpha, strpos($mp, $tp[$i]), 1, 'utf-8');
	}
	
	return $out;
}

// base64解码改
function rw_decode($in){

	global $nkw_runeword_alpha;
	
	$mp = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
	
	for($i=0,$l = mb_strlen($in, 'utf-8'); $i<$l; $i++){
		$out.= substr($mp, mb_strpos($nkw_runeword_alpha, mb_substr($in,$i,1,'utf-8'), 0, 'utf-8'), 1);
	}
	
	$out = base64_decode($out);
	return $out;
}

// 编码表打乱函数
function rw_shuffle(&$nkw_runeword_alpha){

	$arr = array();
	for($i = 0 , $l = mb_strlen($nkw_runeword_alpha, 'utf-8'); $i<$l; $i++){
		array_push($arr, mb_substr($nkw_runeword_alpha, $i, 1, 'utf-8'));
	}
	shuffle($arr);
	//print_r($arr);
	
	$nkw_runeword_alpha = mb_substr(implode('', $arr), 0, 65, 'utf-8');
	
	//理论上返回65个字符即可，如有需要，就用这个返回所有字符：
	//$alpha = implode('', $arr);
}

//可以每次都打乱，但也不是非要如此，定期打乱也行：
//rw_shuffle($nkw_runeword_alpha);
?>