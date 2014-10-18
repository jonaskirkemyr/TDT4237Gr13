#Installation guide - Using Ubuntu

##Requirements
- PHP5
	
		apt-get install php5-cli

- Apache2
- NoSql
	
		apt-get install php5-sqlite sqlite3



##Install dependencies
Download repository from github, copy to webroot, making sure Apache has access to folder.

###Composer
Cd into working directory, then run:

	curl -sS https://getcomposer.org/installer | php mv composer.phar /usr/local/bin/composer
Now, composer can be accessed globally. This makes composer an executable, and to run composer, just type:

	composer

instead of:

	php composer.phar

To download all dependencies for project, cd to the project, then run the install command:

	composer install

Composer also prepares autoloading for the project. To use it add this line to the "base" page:

	require 'vendor/autoload.php'; 

##Settings
###PHP.INI
For PDO and NoSQL to be working, extensions needs to be set in the php.ini file (/etc/php5/apache2/php.ini usually)

	extension=pdo.so
	extension=pdo_sqlite.so
	extension=sqlite.so
	extension=pdo_mysql.so

###PHPINFO
run phpinfo(), look for:

- openssl
- nosql / sqlite
- pdo

Check that each of these packes is installed and enabled!


###A2ENSITE
When lookin gup the url

	http://localhost/

a user should be redirected to the web/ folder. To accomplish this run command:

	sudo nano /etc/apache2/sites-available/000-default.conf

and change the document root, by setting it to point to the web folder. 

##Using PDO
to be able to use PDO commands, in each file ,at the top, you need to specify:
	
	use PDO;
for PDO commands to be recognized.

##Testing
Slim doesn't work (the redirecting part) if the base filename isn't specified in the url. i.e
	
	http://localhost/index.php

By only using
	
	http.//localhost/

and then navigation to the different links, a 404 message is thrown. 

###Fix
create HTACCESS file, that automatically redirects to index.php. Inside .htaccess add:

	RewriteEngine On 
	RewriteCond %{REQUEST_FILENAME} !-f 
	RewriteRule ^(.*)$ %{ENV:BASE}index.php [QSA,L]


For .htaccess files to work in ubuntu:

	sudo a2enmod rewrite

Then restart apache

	sudo /etc/init.d/apache2 restart
