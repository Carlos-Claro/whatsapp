
# Deploy
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update
sudo apt-get install libapache2-mod-php php php-common php-xml php-mysql php-gd php-mbstring php-tokenizer php-json php-bcmath php-curl php-zip unzip -y

sites-available/default.conf
```

<IfModule mod_ssl.c>
	<VirtualHost _default_:443>
		ServerAdmin webmaster@localhost
		
		DocumentRoot /var/www/html/whatsapp/public 
		ServerName testes.carlosclaro.com.br
		ServerAdmin carlosclaro79@gmail.com
        SSLEngine on
		SSLProtocol                         all -SSLv2 -SSLv3
	        SSLCipherSuite                      ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA:ECDHE-RSA-AES256-SHA384:ECDHE-RSA-AES128-SHA:ECDHE-ECDSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA:ECDHE-RSA-AES256-SHA:DHE-RSA-AES128-SHA256:DHE-RSA-AES128-SHA:DHE-RSA-AES256-SHA256:DHE-RSA-AES256-SHA:ECDHE-ECDSA-DES-CBC3-SHA:ECDHE-RSA-DES-CBC3-SHA:EDH-RSA-DES-CBC3-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384:AES128-SHA256:AES256-SHA256:AES128-SHA:AES256-SHA:DES-CBC3-SHA:!DSS
        	SSLHonorCipherOrder                 on
	        SSLOptions                          +StrictRequire
        	SSLCertificateFile                  /docker-volumes/etc/letsencrypt/live/testes.carlosclaro.com.br/cert.pem
	        SSLCertificateKeyFile               /docker-volumes/etc/letsencrypt/live/testes.carlosclaro.com.br/privkey.pem
        	SSLCertificateChainFile             /docker-volumes/etc/letsencrypt/live/testes.carlosclaro.com.br/fullchain.pem

        <FilesMatch "\.(cgi|shtml|phtml|php)$">
				SSLOptions +StdEnvVars
		</FilesMatch>
		<Directory /usr/lib/cgi-bin>
				SSLOptions +StdEnvVars
		</Directory>
		<Directory /var/www/html/whatsapp/public>
                	Options Indexes FollowSymLinks
	                AllowOverride All
        	        Require all granted
	        </Directory>

    </VirtualHost>
</IfModule>
```


# Reference
- https://www.iankumu.com/blog/how-to-deploy-laravel-on-apache-server/
