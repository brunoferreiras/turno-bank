FROM nginx:1.21.3-alpine

# ADD Custom Config
ADD .docker/nginx/nginx.conf /etc/nginx/nginx.conf
ADD .docker/nginx/default.conf /etc/nginx/conf.d/default.conf
ADD .docker/nginx/upstream.conf /etc/nginx/conf.d/upstream.conf
ADD .docker/nginx/entrypoint.sh /bin/entrypoint.sh

# Make Directory
COPY ./public/ /var/www/html/public/

# ADD and set Group
RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

# Set Group to Workspace
RUN chown laravel:laravel /var/www/html

STOPSIGNAL SIGQUIT

CMD /bin/entrypoint.sh
