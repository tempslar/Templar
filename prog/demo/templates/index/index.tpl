<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Templar Framework - A Flexing Light PHP Framework with Redis, Memcache, and MySQL, on Linux and Apache/NginX Web Server</title>
	<meta name="keywords" content="PHP, PHP framework, Templar, Laravel, Symfony">
	<meta name="description" content="Templar Framework, a PHP framework, A Flexing Light PHP Framework with Redis, Memcache, and MySQL, on Linux and Apache/NginX Web Server">

    <!-- Bootstrap -->
    <link href="<{$res_domain}>/bs335/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
    /* Space out content a bit */
body {
  padding-top: 20px;
  padding-bottom: 20px;
}

/* Everything but the jumbotron gets side spacing for mobile first views */
.header,
.marketing,
.footer {
  padding-right: 15px;
  padding-left: 15px;
}

/* Custom page header */
.header {
  padding-bottom: 20px;
  border-bottom: 1px solid #e5e5e5;
}
/* Make the masthead heading the same height as the navigation */
.header h3 {
  margin-top: 0;
  margin-bottom: 0;
  line-height: 40px;
}

/* Custom page footer */
.footer {
  padding-top: 19px;
  color: #777;
  border-top: 1px solid #e5e5e5;
}

/* Customize container */
@media (min-width: 768px) {
  .container {
    max-width: 730px;
  }
}
.container-narrow > hr {
  margin: 30px 0;
}

/* Main marketing message and sign up button */
.jumbotron {
  text-align: center;
  border-bottom: 1px solid #e5e5e5;
}
.jumbotron .btn {
  padding: 14px 24px;
  font-size: 21px;
}

/* Supporting marketing content */
.marketing {
  margin: 40px 0;
}
.marketing p + h4 {
  margin-top: 28px;
}

/* Responsive: Portrait tablets and up */
@media screen and (min-width: 768px) {
  /* Remove the padding we set earlier */
  .header,
  .marketing,
  .footer {
    padding-right: 0;
    padding-left: 0;
  }
  /* Space out the masthead */
  .header {
    margin-bottom: 30px;
  }
  /* Remove the bottom border on the jumbotron for visual effect */
  .jumbotron {
    border-bottom: 0;
  }
}
    </style>
</head>
<body>
  
	<div class="container">
		<div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation" class="active"><a href="https://github.com/tempslar/Templar">Home</a></li>
            <li role="presentation"><a href="./?act=about_index">About</a></li>
            <li role="presentation"><a href="#">Contact</a></li>
          </ul>
        </nav>
        <h3 class="text-muted">Templar</h3>
      </div>
      
      <div class="jumbotron">
        <h1>Templar Framework</h1>
        <p class="lead">A Flexing Light PHP Framework with Redis, Memcache, and MySQL, on Linux and Apache/NginX Web Server.</p>
        <p><a class="btn btn-lg btn-success" href="https://github.com/tempslar/Templar" role="button">Download Templar</a></p>
      </div>

      <div class="row marketing">
        <div class="col-lg-6">
          <h4>More than MVC!</h4>
          <p>Templar Framework provides 7 Layers to achieve low Coupling and help developer spread their code into different layers.</p>

          <h4>Cache</h4>
          <p>Supporting both Memcached and Redis. Supporting Memcached consistency hash</p>
          
          <h4>No-SQL</h4>
          <p>Using Redis as DB.</p>
          
        </div>

        <div class="col-lg-6">
          <h4>Manipulate Databases Easily!</h4>
          <p>Using Mysqli extension. Automaticly master/slave selection, and developers have no need to write SQL.</p>
          
          <h4>A few ready-to-go utilities</h4>
          <p>File Lock, Captcha, Hash, Http Access, Http Auth, Pagebar, Log, and Fiel Uploading</p>

          <h4>Bootstrap</h4>
          <p>Integrating Bootstrap</p>

        </div>
      </div>

      <{include file="footer.tpl"}>
      
	</div>



    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<{$res_domain}>/js/jquery-1.11.3.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<{$res_domain}>/bs335/js/bootstrap.min.js"></script>
  </body>
</html>