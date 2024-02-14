# Dockerfile
FROM php:5.6-apache

# Instalar dependências
RUN apt-get update && \
    apt-get install -y zip unzip git && \
    echo "memory_limit=-1" > /usr/local/etc/php/php.ini

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Habilitar módulos do Apache
RUN a2enmod rewrite

# Copiar os arquivos da aplicação
COPY ./php /var/www/html

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Expor a porta 80
EXPOSE 80

# Comando padrão ao iniciar o contêiner
CMD ["apache2-foreground"]
