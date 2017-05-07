FROM centos:7

EXPOSE 5000

RUN yum install -y epel-release && \
    yum install -y https://mirror.webtatic.com/yum/el7/webtatic-release.rpm && \
    yum upgrade -y && \
    yum install -y php71w-fpm php71w-opcache php71w-mysqlnd php71w-odbc nginx && \
    yum clean all

RUN sed -ie s/\;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/ /etc/php.ini && \
    sed -ie s/listen\ =\ 127.0.0.1:9000/listen\ \=\ \\/var\\/run\\/php-fpm\\/php-fpm.sock/ /etc/php-fpm.d/www.conf && \
    sed -ie s/\;listen.owner\ \=\ nobody/listen.owner\ \=\ nginx/ /etc/php-fpm.d/www.conf && \
    sed -ie s/\;listen.group\ \=\ nobody/listen.group\ \=\ nginx/ /etc/php-fpm.d/www.conf && \
    sed -ie s/\;listen.mode\ \=\ 0660/listen.mode\ \=\ 0770/ /etc/php-fpm.d/www.conf && \
    sed -ie s/user\ \=\ apache/user\ \=\ nginx/ /etc/php-fpm.d/www.conf && \
    sed -ie s/group\ \=\ apache/group\ \=\ nginx/ /etc/php-fpm.d/www.conf

COPY src/* /var/www/html/
COPY nginx.conf /etc/nginx/nginx.conf
COPY startup.sh /startup.sh

CMD [ "bash", "startup.sh" ]
