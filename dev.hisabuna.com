server {
    server_name app.hisabuna.com;

    root /var/www/hisabuna/backend/public;  # Adjust this to the actual path of your Laravel project
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }


    location ~ /\.ht {
        deny all;
    }

    # Redirect all HTTP traffic to HTTPS (if SSL is set up)
    # return 301 https://$host$request_uri;

    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/app.hisabuna.com/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/app.hisabuna.com/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot

}
server {
    if ($host = app.hisabuna.com) {
        return 301 https://$host$request_uri;
    } # managed by Certbot


    listen 80;
    server_name app.hisabuna.com;
    return 404; # managed by Certbot


}