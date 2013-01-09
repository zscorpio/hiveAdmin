<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$title; ?></title>
	<link type="text/css" href="/ui/oauth2/css/oauth_web.css" rel="stylesheet" />
</head>
<body>
	<div class="oauth-wrap">
		<div class="oauth-header clearfix">
			<h1 class="MI-logo fl" title="五百米生活圈">五百米生活圈</h1>
			<p class="login-account"></p>
		</div>
		<div class="oauth-main">
			<div class="oauth-error-content clearfix">
				<span class="oauth-error-icon fl"></span>
				<div class="error-content fl">
					<h2><?=$heading; ?></h2>
					<p>你所访问的站点在500mi的认证失败，请您稍后再试。</p>
					(<?=$message; ?>)
				</div>
			</div>
			<div class="oauth-copyright">
				<a href="#">500mi</a>版权所有
			</div>
		</div>
	</div>
</body>
</html>