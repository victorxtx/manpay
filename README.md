手动代支付系统 - A manual third-party payment system
简介：在中国大陆如果没有来得及注册企业资质，那么你的项目会没有渠道可以在互联网上直接收费。这时就需要借助第三方代支付平台，把你用户的支付请求发到代支付网站，代支付网站完成支付后再给你的网站发回调通知，你的网站收到通知再完成对你的玩家的支付逻辑（比如增加玩家的代币）。
本系统的角色就是这个代支付网站。不过市场上的代支付网站后台都比较厉害，能自动对接上游合法的有支付牌照的支付接口完成对终端玩家的自动收款逻辑，但为了安全，本系统没有最后这一段自动支付完成逻辑，需要管理侧手动操作。OVER。
P.S 使用本系统有一些基本技术要求，完全没有技术支持的朋友抱歉了。
P.S 系统没有按照专业的软件工程编写，其中还有一些不合理但能跑的算法，若有更好的架构或算法建议尽管提，也不知道传上去以后有没有人理。
# 前置安装：
## 1、所有代码都是纯原生 HTML + CSS + JavaScript + PHP，本系统没有一键安装、UI 引导安装，没有使用任何框架，这对绝大多数站长是毁灭性的，没有技术支持的站长抱歉了。
## 2、本系统在 Linux Ubuntu 系统开发，适配最好。应该也能兼容 Win + PHP，若要使用 Windows，那么 Good Luck。
## 3、开发环境为 Ubuntu 22.04 + Nginx 1.28.0 + PHP 8.4.7 + MySQL-8.0.40，没有奇怪的逻辑代码，所以对上述组件版本要求不严格，但 PHP 最好别小于 7.0，MySQL 最好别小于 5.7，Nginx 都行。
## 4、开发用的 Nginx 和 PHP 均为自编译，非 apt yum 等安装版，整个过程我也没有统计需要开启什么模块，反正我知道的模块都开了，这里把我自己的编译参数贴一下。
## 如果是愿意走编译流程的朋友，那么需要为编译安装依赖，下面分别贴一下，有重复无所谓，会自动忽略重复
### apt install gcc build-essential libssl-dev libpcre2-dev zlib1g-dev libgd-dev libxslt1.1 libxslt1-dev libxml2-dev libgeoip-dev libperl-dev libgoogle-perftools-dev libatomic-ops-dev
### apt install gcc build-essential libsystemd-dev sqlite3 libsqlite3-dev libzip-dev libtidy-dev valgrind libsnmp-dev libenchant-2-dev libavif-dev libwebp-dev libgmp-dev libkrb5-dev libargon2-dev systemtap-sdt-dev libbz2-dev libpq-dev libaspell-dev libpspell-dev libedit-dev libmm-dev libsodium-dev libcurl4-openssl-dev libonig-dev libpng-dev libjpeg-dev libxpm-dev libfreetype-dev libxslt1-dev libexpat1-dev bison re2c postgresql libpq-dev autoconf
### Nginx 编译参数：（参数中提到的路径，需要自己去官网下载免费代码并放入 /repo/ 下解压。并不是每个参数在本项目中都必须，我暂时没有计划排查哪些模块必须，其中比较麻烦的是 brotli，如果不会搞可以删掉，brotli 非必须）
### [Nginx-1.28.0 源码地址](https://nginx.org/download/nginx-1.28.0.tar.gz)
#### ./configure --user=root --group=root --prefix=/opt/nginx --pid-path=/opt/nginx/var/nginx.pid --with-pcre --with-pcre-jit --with-http_ssl_module --with-http_stub_status_module --with-http_gzip_static_module --with-http_v2_module --with-http_realip_module --with-http_addition_module --with-http_image_filter_module --with-http_sub_module --with-http_dav_module --with-http_flv_module --with-http_mp4_module --with-http_secure_link_module --with-http_slice_module --with-mail=dynamic --with-mail_ssl_module --with-stream --with-stream_ssl_module --with-stream_realip_module --with-stream_geoip_module --with-threads --with-file-aio --with-http_xslt_module --with-http_geoip_module --with-http_mp4_module --with-http_gunzip_module --with-http_auth_request_module --with-http_random_index_module --with-http_degradation_module --with-http_perl_module=dynamic --with-mail_ssl_module --with-compat --with-stream_ssl_preread_module --with-mail --with-mail_ssl_module --with-libatomic --with-debug --add-module=/repo/ngx_modules/ngx_brotli --add-module=/repo/ngx_modules/nginx-http-concat --add-module=/repo/ngx_modules/ngx-cache-purge --add-module=/repo/ngx_modules/redis2-nginx-module --with-openssl=/repo/openssl-3.5.0
### 5、PHP 编译参数：（PHP 比较单纯，没有太多坑）
### [PHP-8.4.7 源码地址](https://www.php.net/distributions/php-8.4.7.tar.gz)
#### ./configure --prefix=/opt/php --with-config-file-path=/opt/php/etc --with-fpm-user=root --with-fpm-group=root --enable-fpm --with-fpm-systemd --with-fpm-acl --enable-mysqlnd --with-mysqli --with-pdo-mysql --enable-opcache --enable-pcntl --enable-mbstring --enable-soap --with-zip --enable-calendar --enable-bcmath --enable-exif --enable-ftp --enable-intl --enable-shmop --with-curl --enable-gd --with-freetype --with-gettext --with-mhash --with-tidy --with-external-libcrypt --enable-debug --enable-debug-assertions --enable-sysvsem --with-openssl=/repo/openssl-3.5.0 --with-zlib --enable-xml --with-snmp --enable-sockets --with-enchant --with-ffi --with-avif --with-webp --with-jpeg --with-xpm --enable-gd-jis-conv --with-gmp --with-mysql-sock --with-password-argon2 --enable-sysvshm --with-xsl --enable-dtrace --enable-address-sanitizer --with-system-ciphers --with-bz2 --with-ldap-sasl --with-pgsql --with-libedit --with-readline --with-mm --with-sodium --with-expat --enable-zend-test --enable-mysqlnd --disable-gcc-global-regs
#### PHP 必须安装 phpredis 扩展，用框架的自便，若编译安装，这里贴一下项目：[Phpredis Extension](https://github.com/phpredis/phpredis)。简单说一下，编译时需调用上面的 phpize 告知 phpredis 当前 php 的相关配置，再编译安装，最后需要在 php.ini 中 extension=redis.so。
## 6、编译安装的组件可能需要自己写 systemd 的服务配置文件，这个贴不贴呢...你们自己探索吧，不是什么秘密，都能搜到教程的。
## 7、其中编译安装的 Nginx 的配置里没有默认对 PHP 的转发，但是所有运维框架里都有，我不推荐使用框架，推荐你们自己写 Nginx 配置文件，但是实在是不会写就用框架吧。
## 8、安装 redis 数据库实例。
## 9、网站安装：（至此，环境已准备完成）
### 1) 把项目 web 目录下所有文件拷到网站目录下，可以是网站根目录，也可以是其下子目录
### 2) 找到 lib/config.php 打开编辑，按照文件注释提示详细修改所有带 (*) 的必填项。
### 3.0) 安装 Redis 服务器并启动，必须设置如下项：
#### unixsocket "/dev/shm/redis.sock"
#### requirepass YourRedisAuthString # 这里设置密码，需要在 config.php 的 const REDIS_AUTH = ''; 的单引号中填写同样的值，这里比如说就是 YourRedisAuthString。
### 4) 
### 5) 安装并配置 MySQL 服务器，在 lib/config.php 里填入自己设置的验证信息
#### 找到项目根目录的 mysql.sql 文件，进入 MySQL 执行建库建表
### 初始化网站
#### 其实没有专门的初始化页面，启动网站进入网站根目录（确保能访问到 index.php）即可。
#### 没有提供 UI 设置管理员，请先注册一个帐号，然后到数据库 `user` 表中把用户的 `side` 字段改为 0 就是管理员。
# 功能介绍
## 1、注册用户即为商户（修改 side 为 0 可以变成管理员；修改 side 为 1 可变成客服，客服不能上传收款二维码）
## 2、在未登录状态下，test.php 为模拟发起支付页面，需要先以管理员身份登录，设置收款金额范围、上传三类二维码后再使用模拟提交
# 效果图
![首页](https://img.vickygames.cn/manpay/index.jpg)
![订单](https://img.vickygames.cn/manpay/order.jpg)
![二维码管理](https://img.vickygames.cn/manpay/qr-admin.jpg)
![二维码上传](https://img.vickygames.cn/manpay/qr-upload.jpg)
![对接文档](https://img.vickygames.cn/manpay/api-doc.jpg)
![商户管理](https://img.vickygames.cn/manpay/merch-admin.jpg)
![结算列表](https://img.vickygames.cn/manpay/settle-show.jpg)
