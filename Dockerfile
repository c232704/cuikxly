# 使用官方提供的 PHP 镜像作为基础镜像
FROM php:7.0-apache

# 设置工作目录
WORKDIR /var/www/html

# 复制应用程序代码到容器中
COPY . /var/www/html

# 安装 PHP 相关扩展
RUN docker-php-ext-install pdo_mysql

# 启用 Apache Rewrite 模块
RUN a2enmod rewrite

# 设置自定义的 Apache 配置文件
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# 设置环境变量
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# 更改用户和组的权限
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/runtime

# 暴露容器内部的 80 端口
EXPOSE 80
