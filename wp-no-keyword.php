<?php
/*
Plugin Name: WP-No-Keyword
Plugin URI: http://blog.lolily.com/wordpress-plugin-wp-no-keyword.html
Plugin Description: Protect your keywords from being blocking.
Version: 1.4
Author: Ariagle
Author URI: http://blog.lolily.com/
*/

$nkw_opt = nkw_get_opt();
if ($nkw_opt['nkw_mode']!=0) {
	$nkw_path = ABSPATH.str_replace(get_bloginfo('siteurl').'/',"",WP_PLUGIN_URL).'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	wp_enqueue_script('jquery');
}
if ($nkw_opt['nkw_mode']==1) {

	include_once($nkw_path.'phpjsrsa/biginteger.php');
	include_once($nkw_path.'phpjsrsa/bigintext.php');
	include_once($nkw_path.'phpjsrsa/rsa.class.php');
	include_once($nkw_path.'phpjsrsa/PHP/Compat/Function/bcpowmod.php');
	add_filter('wp_head', 'nkw_head_phpjsrsa');
	
} elseif ($nkw_opt['nkw_mode']==2) {

	include_once($nkw_path.'runeword/runeword.php');
	add_filter('wp_head', 'nkw_head_runeword');
	
}

/**
 * 过滤输出内容
 */
function nkw_filter($text) {
	$opt = nkw_get_opt();
	if ($opt['nkw_mode']==1 and !is_feed()) {
		$text = nkw_phpjsrsa($text, $opt);
	} elseif ($opt['nkw_mode']==2 and !is_feed()) {
		$text = nkw_runeword($text, $opt);
	} else {
		$text = nkw_normal($text, $opt);
	}
	return $text;
}
add_filter('the_content', 'nkw_filter');
add_filter('the_excerpt', 'nkw_filter');
add_filter('comment_text', 'nkw_filter');
add_filter('comment_excerpt', 'nkw_filter');

/**
 * Unicode + HTML tag过滤
 */
function nkw_normal($text, $opt) {
	if ($opt['nkw_key_word']) {
		
		$img_array = nkw_content2text($text, 0); // 过滤特殊元素
		$text = nkw_content2text($text, 1);
		foreach ($opt['nkw_key_word'] as $key => $value) {
			if ($value!='') {
				$html = '';
				$temp = nkw_cut_str($value, 65535, 0, 'UTF-8');
				foreach ($temp as $str) {
					$html .= '<span>'.utf8_unicode($str).'</span>';
				}
				$text = str_ireplace($value, $html, $text);
			}
		}
		$text = nkw_text2content($text, $img_array); // 还原特殊元素
		
	}
	return $text;
}

/**
 * phpjsrsa模式
 */
function nkw_phpjsrsa($text, $opt) {
	if ($opt['nkw_key_word']) {
		$RSA = new RSA();
		$keys = $RSA->generate_keys('62700433', '62702257', 0);
		$img_array = nkw_content2text($text, 0); // 过滤特殊元素
		$text = nkw_content2text($text, 1);
		foreach ($opt['nkw_key_word'] as $num => $value) {
			if ($value!='') {
				$html = '<span name="encode_'.$num.'">'.$RSA->encrypt($value, $keys[1], $keys[0], 6).'</span>';
				$len = strlen($value);
				$text = str_ireplace($value, $html, $text);
			}
		}
		$text = nkw_text2content($text, $img_array); // 还原特殊元素
	}
	return $text;
}


/**
 * 生成phpjsrsa的JS
 */
function nkw_phpjsrsa_js() {
	$opt = nkw_get_opt();
	if ($opt['nkw_key_word']) {
		$RSA = new RSA();
		$keys = $RSA->generate_keys('62700433', '62702257', 0);
		$js = "\n<script  type=\"text/javascript\">\n jQuery(document).ready(function(){\n var d=".$keys[2].";\n var n=".$keys[0].";\n";
		$js .= "var encode=new Array();\n var k=0;\n";
		foreach ($opt['nkw_key_word'] as $num => $value) {
			if ($value!='') {
				$js .= " if (document.getElementsByName(\"encode_$num\")) {\n";
				$js .= "  encode[$num]=document.getElementsByName(\"encode_$num\");\n";
				$js .= "  for(k=0;k<encode[$num].length;k++){\n";
				$js .= "   if (encode[$num][k]) {\n";
				$js .= "    var s_$num=encode[$num][k].innerHTML;\n";
				$js .= "    str_$num=decrypt(s_$num,d,n);\n";
				$js .= "    document.getElementsByName(\"encode_$num\")[k].innerHTML=str_$num;\n";
				$js .= "   }\n";
				$js .= "  }\n";
				$js .= " }\n";
			}
		}
		$js .= "});</script>\n";
		echo $js;
	}
}

/**
 * 加载phpjsrsa js文件
 */
function nkw_head_phpjsrsa() {
	echo "<!-- START WP-No-Keyword -->\n";
	echo "<script type=\"text/javascript\" src=\"" . plugins_url('wp-no-keyword/phpjsrsa/jsbn.js') . "\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"" . plugins_url('wp-no-keyword/phpjsrsa/jsbn2.js') . "\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"" . plugins_url('wp-no-keyword/phpjsrsa/rsa.js') . "\"></script>\n";
	nkw_phpjsrsa_js();
	echo "<!-- END WP-No-Keyword -->\n";
}

/**
 * runeword模式
 */
function nkw_runeword($text, $opt) {
	if ($opt['nkw_key_word']) {
		$img_array = nkw_content2text($text, 0); // 过滤特殊元素
		$text = nkw_content2text($text, 1);
		foreach ($opt['nkw_key_word'] as $num => $value) {
			if ($value!='') {
				$html = '<span class="nkw_runeword">'.rw_encode($value).'</span>';
				$text = str_ireplace($value, $html, $text);
			}
		}
		$text = nkw_text2content($text, $img_array); // 还原特殊元素
	}
	return $text;
}

/**
 * 加载runeword js文件
 */
function nkw_head_runeword() {
	echo "<!-- START WP-No-Keyword -->\n";
	echo "<script type=\"text/javascript\" src=\"" . plugins_url('wp-no-keyword/runeword/runeword.js') . "\"></script>\n";
	nkw_runeword_js();
	echo "<!-- END WP-No-Keyword -->\n";
}

/**
 * 生成runeword的JS
 */
function nkw_runeword_js() {
	global $nkw_runeword_alpha;
	$js = "\n<script  type=\"text/javascript\">\n";
	$js .= "var nkw_runes=\"$nkw_runeword_alpha\";\n";
	$js .= "String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g,\"\"); }\n";
	$js .= "jQuery(function(){ \n";
	$js .= " jQuery('.nkw_runeword').each(function(){\n";
	$js .= "  var ".'$this'." = jQuery(this)\n";
	$js .= "  ".'$this'.".html(twRuneWord.decode(".'$this'.".html().trim()));\n";
	$js .= " });\n";
	$js .= "});\n";
	$js .= "</script>\n";
	echo $js;
}

/**
 * 抽取文本中无需加密的内容，如a、img。参数：(文本, 返回值)，返回值：0=特殊内容，1=替换后的文本。
 */
function nkw_content2text($text, $mode=0) {
	$n = 0;
	$img_array = array();
	$pattern = "/ (href|src|alt|name|title|style|class|value|type|classid|codebase|flashvars)=(\"|\'){0,}(.*?)(\"|\')/"; // 仅处理小写字母的HTML
	preg_match_all($pattern, $text, $img_array);
	while ($text != preg_replace($pattern, "", $text, 1)) {
		$text = preg_replace($pattern, "#n[k[w[i[m[g_".$n."#", $text, 1);
		$n++;
	}
	if ($mode==0) {
		return $img_array;
	} else {
		return $text;
	}
}

/**
 * 将无需加密的内容，如Img，放回文本中。
 */
function nkw_text2content($text, $img_array) {
	$n = 0;
	while ($text != str_replace("#n[k[w[i[m[g_".$n."#", "", $text)) {
		$text = str_replace("#n[k[w[i[m[g_".$n."#", $img_array[0][$n], $text);
		$n++;
	}
	return $text;
}

/**
 * 将关键词转换成数组并返回
 */
function nkw_keyword2array($temp) {
	if ($temp) {
		$temp = preg_split("/\r|\n/",stripslashes( $temp ));
		foreach ($temp as $key => $value) {
			if (trim($value)!='') {
				$opt['nkw_key_word'][] = $value;
			}
		}
	}
	return $opt;
}

/**
 * 将关键词数组转换成字符串文本并返回
 */
function nkw_array2keyword($temp) {
	$n = 0;
	if ($temp) {
		foreach ($temp as $key => $value) {
			if ($n == count($temp)-1) {
				$txt .= $value;
			} else {
				$txt .= $value . "\n";
			}
			$n++;
		}
	}
	return $txt;
}

/**
 * 加载关键词
 */
function nkw_get_opt() {
	return get_option('wp_no_key_word');
}

/**
 * Utf-8、gb2312都支持的汉字截取函数。参数：(字符串, 截取长度, 开始长度, 编码)。 来源于网络。
 */
function nkw_cut_str($string, $sublen, $start = 0, $code = 'UTF-8') {

	if ($code == 'UTF-8') {

		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";

		preg_match_all($pa, $string, $t_string);

		/*if (count($t_string[0]) - $start > $sublen) {
			return join('', array_slice($t_string[0], $start, $sublen))."...";
		} else {
			return join('', array_slice($t_string[0], $start, $sublen));
		} */
		return array_slice($t_string[0], $start, $sublen);

	} else {

		$start = $start*2;
		$sublen = $sublen*2;
		$strlen = strlen($string);
		$tmpstr = '';
		
		for ($i=0; $i<$strlen; $i++) {

			if ($i>=$start && $i<($start+$sublen)) {
				if (ord(substr($string, $i, 1))>129) {
					$tmpstr.= substr($string, $i, 2);
				} else {
					$tmpstr.= substr($string, $i, 1);
				}
			}

			if (ord(substr($string, $i, 1))>129) { $i++; }

		}

		//if (strlen($tmpstr)<$strlen ) { $tmpstr.= "..."; }
		return $tmpstr;
		
	}

} 


/**
 * 将字符串转换成unicode编码。utf8 -> unicode。取自网络。
 */
function utf8_unicode($c) {
	switch(strlen($c)) {
	case 1:
		return $c;
	case 2:
		$n = (ord($c[0]) & 0x3f) << 6;
		$n += ord($c[1]) & 0x3f;
		break;
	case 3:
		$n = (ord($c[0]) & 0x1f) << 12;
		$n += (ord($c[1]) & 0x3f) << 6;
		$n += ord($c[2]) & 0x3f;
		break;
	case 4:
		$n = (ord($c[0]) & 0x0f) << 18;
		$n += (ord($c[1]) & 0x3f) << 12;
		$n += (ord($c[2]) & 0x3f) << 6;
		$n += ord($c[3]) & 0x3f;
		break;
	}
	return "&#$n;";
}


/**
 * 加载后台选项
 */
function nkw_options() {
	if (function_exists('add_options_page')) {
		add_options_page('WP No Keyword', 'WP No Keyword', 9, 'wp-no-keyword/options.php');
	}
}
add_action('admin_menu', 'nkw_options');
?>