<?php
$base_name = plugin_basename('wp-no-keyword/options.php');
$base_page = 'admin.php?page='.$base_name;
$text = '';

if(isset($_POST['submit'])) {

	$opt = nkw_keyword2array($_POST['nkw_key_word']);
	$opt['nkw_mode'] = $_POST['nkw_mode'];
	//if ($opt['nkw_phpjsrsa'] == '') { $opt['nkw_phpjsrsa'] = false; } else { $opt['nkw_phpjsrsa'] = true; }
	update_option('wp_no_key_word', $opt);
	//print_r($opt['nkw_key_word']);
	$text .= '<font color="green">设置已更新。</font>';
	
}

$opt = nkw_get_opt();
?>
<div class="wrap">

	<?php screen_icon(); ?>
	
    <h2>WP No Keyword 选项</h2>
    
    <?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
        
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>">
    	<h3>加密模式</h3>
    	
    	<p><input type="radio" value="0" name="nkw_mode" id="nkw_normal"<?php if($opt['nkw_mode'] == 0 || ($opt['nkw_mode']!=1 and $opt['nkw_mode']!=2)) { echo ' checked="checked"'; } ?> /><label for="nkw_normal">默认模式</label></p>
    	
    	<p><input type="radio" value="1" name="nkw_mode" id="nkw_phpjsrsa"<?php if($opt['nkw_mode'] == 1) { echo ' checked="checked"'; } ?> /><label for="nkw_phpjsrsa">phpjsrsa模式</label>
    	
    	<p><input type="radio" value="2" name="nkw_mode" id="nkw_runeword"<?php if($opt['nkw_mode'] == 2) { echo ' checked="checked"'; } ?> /><label for="nkw_runeword">符文之语模式</label></p>
    	
    	<p>
    		<strong>说明：</strong><br/>
    		1、默认模式：关键词中的每个字将被转换成unicode并以span标签来分离。<br/>
    		2、phpjsrsa模式：关键词将在输出前进行加密成数字，输出到网页后再用JS进行解密。<br/>
    		3、符文之语模式：关键词将在输出前进行加密成不相干文字，输出到网页后再用JS进行解密。
    	</p>
    
    	<h3>关键词</h3>
    
    	<p>每行一个关键词，英文不区分大小写，中文区分简繁体。</p>
    
    	<p><textarea cols="50" rows="10" name="nkw_key_word" id="knw_key_word"><?php echo nkw_array2keyword($opt['nkw_key_word']); ?></textarea></p>
    
    	<p><input name="submit" class="button" value=" 保 存 " type="submit" /></p>
    
	</form>
	
	<p>访问：<a href="http://blog.lolily.com/wordpress-plugin-wp-no-keyword.html" title="插件页面">插件页面</a> | <a href="http://blog.lolily.com/" title="Lo极乐园">作者主页</a></p>
	
</div>