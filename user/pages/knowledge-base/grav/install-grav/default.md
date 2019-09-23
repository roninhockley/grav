---
visible: true
title: Installing Grav
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [grav]
---
[toc]

Source: [Boolean world](https://www.booleanworld.com/install-grav-cms-debian-ubuntu/)

#### Install Grav on Debian Stretch with nginx and PHP 7.0 from deb repos.
!!! This guide is only valid if installing nginx from the Debian repo. If installing nginx from official nginx package the username that nginx runs as will be different. This setup is the least problematic for now.

First run apt update then install the necessary PHP and nginx packages:

    sudo apt update
    sudo apt install php-fpm php-gd php-curl php-zip php-mbstring php-xml nginx

Download Grav core+admin zip file from the [Download page](https://getgrav.org/downloads) extract it and copy the files to the webroot:

    wget https://getgrav.org/download/core/grav-admin/1.6.9
    unzip 1.6.9
    sudo cp -r grav-admin/* /var/www/grav/

Set permissions on webroot after adding files:

    sudo chown -R www-data: /var/www/grav/

Last step, go to Grav website and under documentation find nginx, and follow the guide there for the nginx.conf file and the vhost file. You will paste in the nginx.conf contents and do a file copy for the vhost file.

Go to http://servername  and set up the user account.

Done.

#### The working config file for nginx at /etc/nginx/nginx.conf

```
user www-data;
worker_processes auto;
worker_rlimit_nofile 8192; # should be bigger than worker_connections
pid /run/nginx.pid;

events {
    use epoll;
    worker_connections 8000;
    multi_accept on;
}

http {
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;

    keepalive_timeout 30; # longer values are better for each ssl client, but take up a worker connection longer
    types_hash_max_size 2048;
    server_tokens off;

    # maximum file upload size
    # update 'upload_max_filesize' & 'post_max_size' in /etc/php5/fpm/php.ini accordingly
    client_max_body_size 32m;
    # client_body_timeout 60s; # increase for very long file uploads

    # set default index file (can be overwritten for each site individually)
    index index.html;

    # load MIME types
    include mime.types; # get this file from https://github.com/h5bp/server-configs-nginx
    default_type application/octet-stream; # set default MIME type

    # logging
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # turn on gzip compression
    gzip on;
    gzip_disable "msie6";
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 5;
    gzip_buffers 16 8k;
    gzip_http_version 1.1;
    gzip_min_length 256;
    gzip_types
        application/atom+xml
        application/javascript
        application/json
        application/ld+json
        application/manifest+json
        application/rss+xml
        application/vnd.geo+json
        application/vnd.ms-fontobject
        application/x-font-ttf
        application/x-web-app-manifest+json
        application/xhtml+xml
        application/xml
        font/opentype
        image/bmp
        image/svg+xml
        image/x-icon
        text/cache-manifest
        text/css
        text/plain
        text/vcard
        text/vnd.rim.location.xloc
        text/vtt
        text/x-component
        text/x-cross-domain-policy;

    # disable content type sniffing for more security
    add_header "X-Content-Type-Options" "nosniff";

    # force the latest IE version
    add_header "X-UA-Compatible" "IE=Edge";

    # enable anti-cross-site scripting filter built into IE 8+
    add_header "X-XSS-Protection" "1; mode=block";

    # include virtual host configs
    include sites-enabled/*;

```

#### The config file for the grav-site vhost

```
server {
    #listen 80;
    index index.html index.php;

    ## Begin - Server Info
    root /var/www/grav;
    server_name grav.nikkyrron.com;
    ## End - Server Info

    ## Begin - Index
    # for subfolders, simply adjust:
    # `location /subfolder {`
    # and the rewrite to use `/subfolder/index.php`
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    ## End - Index

    ## Begin - Security
    # deny all direct access for these folders
    location ~* /(\.git|cache|bin|logs|backup|tests)/.*$ { return 403; }
    # deny running scripts inside core system folders
    location ~* /(system|vendor)/.*\.(txt|xml|md|html|yaml|yml|php|pl|py|cgi|twig|sh|bat)$ { return 403; }
    # deny running scripts inside user folder
    location ~* /user/.*\.(txt|md|yaml|yml|php|pl|py|cgi|twig|sh|bat)$ { return 403; }
    # deny access to specific files in the root folder
    location ~ /(LICENSE\.txt|composer\.lock|composer\.json|nginx\.conf|web\.config|htaccess\.txt|\.htaccess) { return 403; }
    ## End - Security

    ## Begin - PHP
    location ~ \.php$ {
        # Choose either a socket or TCP/IP address
        fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
        # fastcgi_pass unix:/var/run/php5-fpm.sock; #legacy
        # fastcgi_pass 127.0.0.1:9000;

        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
    }
    ## End - PHP
}
```
! Warning: Pay particular attention to the fastcgi_pass line, the php-fpm version must match the php version itself.
