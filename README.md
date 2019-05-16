# 데모
https://todo.com1.kr

# todo_list 요구사항
* php7 이상이 설치된 웹서버
* php-mbstring
* mysql, php-mysql

# todo_list 설치 방법
ubuntu 16.04 기준이며, 아래 방법은 root 계정으로 진행하였습니다. root가 아닌 계정으로 진행하면서 권한이 필요한 명령의 경우 sudo를 이용하시기 바랍니다.

1. 패키지 업그레이드
>apt update && apt upgrade

2. apache2 설치
>apt-get install apache2 -y

3. mysql 설치
>apt-get install mysql-server -y

> root 비밀번호 생성

4. php 설치
>apt-get install php php-mysql php-mbstring -y
>apt-get install libapache2-mod-php -y

5. git clone
>git clone https://github.com/dhdbstjr98/todo_list.git

6. mysql database 생성, 테이블 입력
>mysql -u root -p

> 생성한 root 비밀번호 입력

>CREATE DATABASE todo_list;

>use todo_list;

>source todo_list/db.sql

>exit

7. db_setup.php 파일 수정
>vi todo_list/db_setup.php

```
{host} : localhost
{database} : todo_list
{user} : root
{password} : 생성한 root 비밀번호

=> 다른 계정을 사용하시려면 해당 환경에 맞게 수정
```

8. db_setup.php 파일 위치 변경
>mv todo_list/db_setup.php todo_list/src/api/db_setup.php

9. 전체 파일 위치 웹서버로 변경 **(주의! 이미 /var/www/html에 작업중인 파일이 있다면 삭제됩니다.)**

>rm -r /var/www/html

>mv todo_list/src /var/www/html

10. apache2 설정 변경
>vi /etc/apache2/apache2.conf

>AllowOverride None을 찾아 AllowOverride All로 변경 (<Directory /var/www/> 내부에 있는 것을 수정합니다.)

>a2enmod rewrite

>service apache2 restart
