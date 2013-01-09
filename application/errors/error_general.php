<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?=$heading; ?></title>
	<style>
		body,h1,h2,h3,h4,h5,h6,hr,p,blockquote,dl,dt,dd,ul,ol,li,pre,form,fieldset,legend,button,textarea,th,td {
			margin: 0;
			padding: 0;
		}
		a,embed {
			outline: 0 none;
		}
		html {
			font-size: 100%;
			overflow-x: hidden;
			overflow-y: scroll;
			/* _ie6实现fixed效果 */
			_background: #ECECEC url(about:blank) no-repeat fixed;
		}
		body {
			font: 12px/1.5 "\5FAE\8F6F\96C5\9ED1","\5B8B\4F53", Menlo, Monaco, "Courier New", monospace;
			color: #333;
		}
		h1,h2,h3,h4,h5,h6 {
			font-size: 100%;
		}
		h1 {
			font-size: 30px;
			line-height: 36px;
		}
		h2 {
			font-size: 24px;
			line-height: 36px;
		}
		h3 {
			line-height: 27px;
			font-size: 18px;
		}
		h4, h5, h6 {
			line-height: 18px;
		}
		h4 {
			font-size: 14px;
		}
		h5 {
			font-size: 12px;
		}
		h6 {
			font-size: 11px;
		}
		.clearfix {
			*zoom: 1;
		}
		.clearfix:before,
		.clearfix:after {
			display: table;
			content: "";
		}
		.clearfix:after {
			clear: both;
		}
		.fl {float: left;}
		.fr {float: right;}
		.oauth-page{
			background: #D6DBDD url(/ui/oauth2/images/auth-body-bg.png) repeat-x 0 0;
		}
		.oauth-wrap {
			width: 580px;
			margin: 0 auto;	
		}
		.oauth-header {
			margin-bottom: 10px;
			margin-top: 10px;
		}
		.oauth-header .MI-logo {
			width: 146px;
			height: 40px;
			background: url(/ui/common/images/logo.png);
			line-height: 200px;
			overflow: hidden;
		}
		.oauth-main	{
			-moz-border-radius: 6px;
			-webkit-border-radius: 6px;
			border-radius: 6px;
			-moz-box-shadow: 0 0 5px rgba(198,198,198,1);
			-webkit-box-shadow: 0 0 5px rgba(198,198,198,1);
			box-shadow: 0 0 5px rgba(198,198,198,1);
			background: -moz-linear-gradient(top,#fff 0,#fbfbfb 80%,#f5f5f5 100%);
			background: -webkit-gradient(linear,left top,left bottom,color-stop(0%,#fff),color-stop(80%,#fbfbfb),color-stop(100%,#f5f5f5));
			background: -webkit-linear-gradient(top,#fff 0,#fbfbfb 80%,#f5f5f5 100%);
			background: -o-linear-gradient(top,#fff 0,#fbfbfb 80%,#f5f5f5 100%);
			background: -ms-linear-gradient(top,#fff 0,#fbfbfb 80%,#f5f5f5 100%);
			background: linear-gradient(top,#fff 0,#fbfbfb 80%,#f5f5f5 100%);
			border: 1px solid #d2d2d2;
			overflow: hidden;
		}
		.oauth-content {
			padding: 20px;
		}
		.oauth-error-content {
			width: 350px;
			margin: 0 0 0 150px;
			padding: 85px 0 111px;
		}
		.oauth-error-content .oauth-error-icon {
			margin-right: 10px;
			width: 36px;
			height: 36px;
			display: inline-block;
			vertical-align: middle;
			background: url(/ui/oauth2/images/auth-error.png) no-repeat;	
		}
		.oauth-error-content .error-content{
			width: 300px;
		}
		.oauth-error-content .error-content h2 {
			font-size: 16px;
		}
		.oauth-copyright {
			background: #E4E4E4;
			height: 25px;
			line-height: 25px;
			border-radius: 0 0 8px 8px;
			text-align: center;
			font-size: 12px;
		}
		.oauth-copyright a{
			margin-right: 5px;
		}
	</style>
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
					<div><?=$message; ?></div>
				</div>
			</div>
			<div class="oauth-copyright">
				<a href="/">500mi</a>版权所有
			</div>
		</div>
	</div>
</body>
</html>
