<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>用户反馈列表</title>
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
            <h1>用户反馈列表</h1>
        </div>
        
        <!-- row -->
        <div class="row">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>联系方式</th>
                    <th>反馈内容</th>
                </tr>
            </thead>
            <tbody>
            <!-- content -->
            <{foreach item=msg from=$list}>
            <tr>
                <td><{$msg.id}></td>
                <td><{$msg.contact}></td>
                <td><{$msg.content}></td>
            </tr>
            <{/foreach}>
            <!-- /content -->
            </tbody>
            
        </table>
        </div><!-- /row -->
    </div><!-- /container -->
    
    <div id="push"></div>
    
    			<!--Page navgation begin-->
                <!--
			<div class="pagination pagination-centered">
				<ul>
					<li <{if $pg eq 1}>class="disabled"<{/if}> ><a href="./admin.php?act=feedback_list&pg=1&pn=15">1st</a></li>
						
					<{if $max_page > 1}>
					
						<{section start="1" step="1" name="pagebar" max="$total"}>
						
							<li <{if $pg eq $pg+1}>class="active"<{/if}> >
								<a href="./?m=User&pg=<{$page_id+1}>"><{$pg+1}></a>
							</li>
							
						<{/section}>
						
					<{else}>
					
						<li class="active"><a href="./?m=User&pg=1">1</a></li>
						
					<{/if}>
					
					<li class=""><a href="./?m=User&pg={$max_page}">last</a></li>
				</ul>
			</div>
            -->
			<!--Page navgation end-->

    
</div><!-- /wrap -->

<div id="footer">
    <div class="container">
        <p class="muted credit">ATA</p>
    </div>
</div>

<!-- Le javascript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="/resource/bs/js/bootstrap.js"></script>
</body>
</html>