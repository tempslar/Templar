<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>通知发送页</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- CSS -->
<link href="/resource/bs/css/bootstrap.css" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/resource/bs/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/resource/bs/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/resource/bs/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="/resource/bs/ico/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="/resource/bs/ico/favicon.png">
</head>
<body>

<!-- Part 1: Wrap all page content here -->
<div id="wrap"> 
    
    <!-- Begin page content -->
    <div class="container">
        <div class="page-header">
            <h1><{$cnMsg}></h1>
        </div>
        
        <!-- row -->
        <div class="row">
        	<p><{$cnMsg}></p>
            <p><{$msg}></p>
            <p><a href="<{$back_url}>" title="跳转">点此跳转</a></p>
            
        </div><!-- /row -->
    </div><!-- /container -->
</div><!-- /wrap -->

<div id="footer">
    <div class="container">
        <p class="muted credit">&copy;ATA</p>
    </div>
</div>

<!-- Le javascript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="/resource/bs/js/bootstrap.js"></script>
</body>
</html>