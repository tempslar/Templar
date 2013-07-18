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
            <h1>发送通知</h1>
        </div>
        
        <!-- row -->
        <div class="row">
        <div class="span12">
            <form action="./admin.php?act=notice_send" method="post" class="form-horizontal">
            	<input type="hidden" name="author_id" value="1" /><!--发件人id-->
				<input type="hidden" name="type" value="1" />
                <!-- debug -->
                <input type="hidden" name="debug" value="do" />
                <!-- /debug -->
              <div class="control-group">
                <label class="control-label" for="inputEmail">接收人</label>
                <div class="controls">
					<select name="agency">
                      <option value="-1">全部用户</option>
                      <!-- SMARTY: agency list -->
                      <{foreach item=agency from=$agency_list}>
                      	<option value="<{$agency.id}>"><{$agency.name}></option>
                      <{/foreach}>
                      <!-- /SMARTY: agency list -->
                    </select>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="inputPassword">标题</label>
                <div class="controls">
                  <input type="text" name="title" id="inputTitle" placeholder="" >
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="inputPassword">内容</label>
                <div class="controls">
	              <textarea name="content" rows="4"></textarea>
                </div>
              </div>
              <div class="control-group">
                <div class="controls">
                  <button type="submit" class="btn btn-danger">发送</button>&emsp;&emsp;
                  <button type="button" class="btn btn-primary" value="javascript:void(0);" onclick="javascript:form.action='./admin.php?act=notice_add&status=0';form.submit()">保存</button>
                </div>
              </div>
            </form>
        </div><!-- /span -->
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