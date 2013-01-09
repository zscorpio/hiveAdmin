<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('showmessage'))
{
	function showmessage($msgkey, $url_forward='', $second=1, $values=array()) {
		global $_SGLOBAL;
		$CI =& get_instance();
	
		$output = ob_get_contents();
		if($output){
			ob_end_clean();
		}
		

		$CI->lang->load('show_message');
		$message = $CI->lang->line($msgkey);
		if($message) {
			$message = lang_replace($message, $values);
		} else {
			$message = $msgkey;
		}

		if(empty($_SGLOBAL['inajax']) && $url_forward && empty($second)) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: $url_forward");
			exit();	
		} else {
			if($_SGLOBAL['inajax']) {
				if($url_forward) {
					$message = "<a href=\"$url_forward\">$message</a><ajaxok>";
				}
				$message = "<a href=\"javascript:;\" onclick=\"javascript:JqueryDialog.Close();\" class=\"float_del\">X</a><div class=\"popupmenu_inner\"><span class='showmessagespan'>".$CI->lang->line('box_title')."</span> $message</div>";
				ob_start();				
				if($CI->input->get('inajax')) {
					$message = "<root><![CDATA[".trim($message)."]]></root>";
				}
				echo $message;
				exit();
			} else {
				if($url_forward) {
					$message = "<a href=\"$url_forward\">$message</a><script>setTimeout(\"window.location.href ='$url_forward';\", ".($second*1000).");</script>";
				}

				$CI->load->library(array('layout', 'session', 'MY_Cart')); 
				$CI->load->helper('view');
				$context = array();
				$attribute = $CI->session->userdata('attribute');
				$context['mi']['user']['attribute'] = $attribute;
				$context['is_login'] = (($attribute & 1) == 1) ? 1 : 0;
				$context['ls'] = $CI->session->userdata('uid') ? '1' : '0';
				$context['lastview_shop']	= $CI->session->userdata('lastview_shop');
				$context['styles']			= array('base.css');
				$context['scripts']			= array('plugin/jquery-1.7.2.min.js', 'base.js');;
				$context['search']			= array('key' => '', 'type' => 'item', 'cate' => 'all');
				$context['usertype']		= $CI->session->userdata('type');
				$context['account']			= $CI->session->userdata('account');
				$context['login_time']		= $CI->session->userdata('login_time');
				$context['ip']				= $CI->input->ip_address();
				$context['message']			= $message;
				$context['second']			= $second;
				$context['url_forward']		= $url_forward;
				$context["locate"]			= $CI->session->userdata('locate');
				$context['address_count']	= 1;
				$context['menu_active']		= false;
				$context['submenu_show']	= false;
				$context['submenu_content']	= '';
				$context['subcate_list']	= array();
				$context['pay_items']		= $CI->my_cart->pay_items();
				$context['siteurl']			= $CI->config->item('base_url');
				$context['imgurl']			= $CI->config->item('img_url');
				$context['seo']['title']	= $CI->config->item('seo_title');
				$context['seo']['keywords']	= $CI->config->item('seo_keywords');
				$context['seo']['desc']		= $CI->config->item('seo_desc');

				$CI->layout->template('showmessage',$context);
			}
		}
	}
}

function lang_replace($text, $vars) {
	if($vars) {
		foreach ($vars as $k => $v) {
			$rk = $k + 1;
			$text = str_replace('\\'.$rk, $v, $text);
		}
	}
	return $text;
}


function ckstart($start, $perpage) {
	$maxpage = 100000;
	$maxstart = $perpage*intval($maxpage);
	if($start < 0 || ($maxstart > 0 && $start >= $maxstart)) {
		showmessage('length_is_not_within_the_scope_of');
	}
}

function geturl($vars) {
	$url = '';
	foreach($vars as $key => $var) {
		$vars[$key] = $key . '=' . $var;
	}
	$url = implode('&', $vars);
	return $url;
}

function multi($num, $perpage, $curpage, $mpurl, $ajaxdiv='', $todiv='') {
	global $_SGLOBAL;
	$maxpage = 100000;
	$showpage = 0;

	if(empty($ajaxdiv) && $_SGLOBAL['inajax']) {
		$ajaxdiv = $_GET['ajaxdiv'];
	}

	$page = 5;
	if($showpage) $page = $showpage;

	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&' : '?';
	$realpages = 1;
	if($num > $perpage) {
		$offset = 2;
		$realpages = @ceil($num / $perpage);
		$pages = $maxpage && $maxpage < $realpages ? $maxpage : $realpages;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = '';
		$urlplus = $todiv?"#$todiv":'';
		if($curpage - $offset > 1 && $pages > $page) {
			$multipage .= "<a ";
			if($_SGLOBAL['inajax']) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=1&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=1{$urlplus}\"";
			}
			$multipage .= " class=\"first\">1 ...</a>";
		}
		if($curpage > 1) {
			$multipage .= "<a ";
			if($_SGLOBAL['inajax']) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=".($curpage-1)."&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=".($curpage-1)."$urlplus\"";
			}
			$multipage .= " class=\"prev\">上一页</a>";
		}
		for($i = $from; $i <= $to; $i++) {
			if($i == $curpage) {
				$multipage .= '<span class="current">'.$i.'</span>';
			} else {
				$multipage .= "<a ";
				if($_SGLOBAL['inajax']) {
					$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=$i&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
				} else {
					$multipage .= "href=\"{$mpurl}page=$i{$urlplus}\"";
				}
				$multipage .= ">$i</a>";
			}
		}

		if($to < $pages) {
			$multipage .= "<span class=\"page-more\">···</span><a ";
			if($_SGLOBAL['inajax']) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=$pages&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=$pages{$urlplus}\"";
			}
			$multipage .= " class=\"last\">$realpages</a>";
		}

		if($curpage < $pages) {
			$multipage .= "<a ";
			if($_SGLOBAL['inajax']) {
				$multipage .= "href=\"javascript:;\" onclick=\"ajaxget('{$mpurl}page=".($curpage+1)."&ajaxdiv=$ajaxdiv', '$ajaxdiv')\"";
			} else {
				$multipage .= "href=\"{$mpurl}page=".($curpage+1)."{$urlplus}\"";
			}
			$multipage .= " class=\"next\">下一页</a>";
		}
		if($multipage) {
			$multipage = '<div class="mi-pagination-inner"><span class="mi-pagination-count">共'.$num.'条</span>'.$multipage.'</div>';
		}
	}
	return $multipage;
}
	
?>
