Templar
=======

A Flexing Light PHP Framework with Redis, Memcache, and MySQL, on Linux and Apache/NginX Web Server

Table of contents
=======
[Features](#featrues)

[FileStructure](#filestructure)

[Layers](#layers)

[Database](#database)

[Cache](#cache)

[No-SQL](#no-sql)

[Template](#template)

[Utilities](#utilities)

[Debug](#debug)

[Plugins](#plugins)


Featrues
======
More than MVC!

Templar Framework provides 7 Layers to achieve low Coupling and help developer spread their code into different layers.




FileStructure
======
Templar

|-common   - Framework libraries

|-utility  - Common utilities

|-plugins  - Proxy classes and third-party libraries

|-resource - resource files like bootstrap and jquery can be accessed by all applications

|-prog     - Application Directives

	|-example
	
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

