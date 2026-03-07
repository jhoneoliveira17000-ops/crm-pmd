# Usa a imagem oficial do PHP com o servidor web Apache
FROM php:8.2-apache

# Habilita o mod_rewrite do Apache (útil para rotas amigáveis, se aplicável)
RUN a2enmod rewrite

# Instala as extensões necessárias do PHP para conectar ao TiDB (que é compatível com MySQL)
# A biblioteca pdo_mysql é a recomendada para conectar o PHP ao banco de dados moderno
RUN docker-php-ext-install pdo pdo_mysql

# Aumenta limites de upload do PHP (padrão é 2MB, insuficiente para logos/fotos)
RUN echo "upload_max_filesize = 10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 128M" >> /usr/local/etc/php/conf.d/uploads.ini

# Copia todos os arquivos do seu repositório/projeto atual para o diretório raiz web do Apache no container
COPY . /var/www/html/

# Ajusta as permissões de pasta para o usuário web do Apache, garantindo que o seu sistema funcione sem erro de permissões
RUN chown -R www-data:www-data /var/www/html/

# Informa à plataforma (Koyeb/Render) que a aplicação está executando internamente na porta 80
EXPOSE 80
