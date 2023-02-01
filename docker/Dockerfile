FROM centos:7

# 既存パッケージを更新
RUN yum -y update && yum clean all

# Apache2.4のインストール
RUN yum -y install httpd

# Apacheの自動起動設定
RUN systemctl enable httpd

# epelパッケージのインストール
RUN yum -y install epel-release.noarch

# Remi's RPM repository
RUN yum -y install -y https://rpms.remirepo.net/enterprise/remi-release-7.rpm

# php 7.4
RUN yum install -y --enablerepo=remi-php74 php php-mbstring php-mysql php-pdo php-xml

#ディレクトリ作成
RUN mkdir /var/www/html/public

# 設定ファイル
COPY setting/httpd.conf /etc/httpd/conf/httpd.conf
COPY setting/php.ini /etc/php.ini

CMD ["/sbin/init"]