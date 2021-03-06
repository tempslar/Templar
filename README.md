Templar Framework
=======

A Flexing Light PHP Framework with Redis, Memcached, and MySQL, on Linux and Apache/NginX Web Server

Table of contents
=======
[Features](#featrues)

[Requirements](#requirements)

[File Structure](#file-structure)

[Layers](#layers)

[Database](#database)

[Cache](#cache)

[No-SQL](#no-sql)

[Template](#template)

[Utilities](#utilities)

[Debug](#debug)

[Plugins](#plugins)

[Lift Off](#lift-off)


Featrues
======
More than MVC!

Templar Framework provides 7 Layers to achieve loss coupling and help developers spread their code into different layers.


Requirements
======
PHP: 5.6

Web Server: Apache or Nginx

PHP extension:

MySQLi

Memcached

Redis


File Structure
======

Templar

	|-common   - Framework libraries
	
	|-utility  - Common utilities
	
	|-plugins  - Proxy classes and third-party libraries
	
	|-resource - resource files like bootstrap and jquery 
					which you want to be accessed by all applications
	
	|-prog     - Application Directories (working directories)
	
		|-demo
		
			|-model
			
			|-core
			
			|-app
			
			|-data
			
			|-storage 
			
			|-view
			
			|-templates

Layers
======
Model     - Data Model

Core      - Controller

App       - Logic

Data      - Data flow control

Storage   - DB 

View      - Output

Templates - Smarty Templates


Database
======
MySQLi

SQL Query

Master/Slave Database



Cache
======
Memcached
Redis


No-SQL
======
Redis


Template
======
Smarty



Utilities
======
File Lock


Captcha


Hash


Http Access


Http Auth


Json


Pagebar


Log


Upload File


Debug
======
FirePHP


Plugins
======
Create a proxy class to use a third-party library as a plugin.


Lift Off
======
Depoly the Templar source code into your web service directories (etc, /www/htdocs/templar).


Create your project in 'prog' directory like 'demo' (etc, /www/htdocs/templar/prog/demo), and create index.php in it.

Setup web server, set domain (etc, www.example.com) root to your project directory (/www/htdocs/templar/prog/demo).

Setup init.php (etc, /www/htdocs/templar/init.php and /www/htdocs/templar/prog/demo/init.php) which should contain the settings for all projects and special settings for 'demo'.

If you want to define any constant for 'demo' project, please extends Common/Config Class to project/config and put the settings in it (etc, /www/htdocs/templar/prog/demo/config/config.class.php)

Now you can visit 'demo' using http://www.example.com/?act=index_index. - New Router Module will come soon.



