FROM ubuntu:wily
MAINTAINER Leonard Marschke <github@marschke.me>

#ARG DEBIAN_FRONTEND=noninteractive

# install texlive-full, inspired by https://github.com/TimNN/docker-texlive2015/blob/master/Dockerfile
RUN apt-get -y update \
	&& apt-get -y upgrade \
	&& apt-get -y install \
			software-properties-common \
	&& apt-add-repository -y ppa:fkrull/deadsnakes \
	&& apt-get -y remove --purge software-properties-common \
	&& apt-get -y update \
	&& apt-get -y install \
			git \
			python3-pip \
			python3.5 \
			texlive-full \
	&& apt-get -y autoremove \
	&& apt-get -y clean \
	&& rm -rf /var/lib/apt/lists/*

ENV ENGINE=lualatex
ENV PYTHONUNBUFFERED=1

ADD https://raw.githubusercontent.com/TimNN/docker-texlive2015/master/lbuild /usr/local/bin/lbuild
RUN chmod 755 /usr/local/bin/lbuild

# inspired by dockerfile of https://hub.docker.com/r/jonathonf/debian-phpfpm/~/dockerfile/
RUN apt-get update \
	&& apt-get -y install php5-fpm \
	&& apt-get -y clean \
	&& rm -rf /var/lib/apt/lists/*

RUN sed -i '/daemonize /c daemonize = no' /etc/php5/fpm/php-fpm.conf

RUN sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo = 0/' /etc/php5/fpm/php.ini \
	&& sed -i 's/zlib.output_compression = Off/zlib.output_compression = On/' /etc/php5/fpm/php.ini \
	&& sed -i 's/;zlib.output_compression_level = -1/zlib.output_compression_level = 6/' /etc/php5/fpm/php.ini \
	&& sed -i 's/expose_php = On/expose_php = Off/' /etc/php5/fpm/php.ini \
	&& sed -i 's/display_errors = On/display_errors = Off/' /etc/php5/fpm/php.ini \
	&& sed -i 's/allow_url_include = On/allow_url_include = Off/' /etc/php5/fpm/php.ini \
	&& sed -i 's/;date.timezone =/date.timezone = Europe\/Berlin/' /etc/php5/fpm/php.ini

# install nginx
RUN apt-get update \
	&& apt-get -y install nginx \
	&& apt-get -y clean \
	&& rm -rf /var/lib/apt/lists/*

ADD nginx/sites-available/latex /etc/nginx/sites-available/
RUN ln -s /etc/nginx/sites-available/latex /etc/nginx/sites-enabled/latex

RUN rm /etc/nginx/sites-available/default
RUN rm -rf /var/www
COPY www /var/www
RUN chown www-data -R /var/www

# install supervisord
RUN apt-get update \
	&& apt-get -y install supervisor \
	&& apt-get -y clean \
	&& rm -rf /var/lib/apt/lists/*

COPY supervisord.conf /etc/supervisor/supervisord.conf

CMD ["supervisord"]
