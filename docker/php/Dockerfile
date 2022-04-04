FROM fsilva/php:8.0

RUN apt-install libyaml-dev

# Install YAML
RUN pecl install yaml \
    && echo "extension=yaml.so" > /usr/local/etc/php/conf.d/yaml.ini

COPY *.conf /etc/apache2/sites-available/
COPY xdebug-profiler.ini /usr/local/etc/php/xdebug.d/