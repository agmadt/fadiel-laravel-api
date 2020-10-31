FROM nginx:alpine

COPY nginx.conf /etc/nginx/conf.d/default.conf

COPY . /var/www/html

WORKDIR /var/www/html/