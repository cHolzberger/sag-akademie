FROM mosaiksoftware/php54

RUN wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.1/wkhtmltox-0.12.1_linux-wheezy-amd64.deb -O /wkhtml.deb
RUN apt-get update && apt-get install -f -y fontconfig libxrender1
RUN dpkg -i /wkhtml.deb
RUN apt-get update && apt-get install -f -y
RUN cp /usr/local/bin/wkhtmltopdf /usr/bin/

COPY --chown=www-data:www-data . /var/www
COPY ./_cron.daily/geb.sh /etc/periodic/daily/01-geb.sh
COPY ./_conf/php.ini /etc/php5/apache2/conf.d/mem.ini
COPY ./_conf/alias.conf /etc/apache2/conf.d


EXPOSE 80
