set COMPOSER_VERSION=download/1.8.6/
set GIT_SSH=c:\Program Files (x86)\putty\plink.exe
set PHPBIN=c:\php\php.exe
powershell -command "& { iwr https://getcomposer.org/%COMPOSER_VERSION%composer.phar -OutFile .\composer.phar }"
%PHPBIN% .\composer.phar self-update --stable
%PHPBIN% .\composer.phar update
%PHPBIN% .\composer.phar install
%PHPBIN% .\composer.phar dumpautoload -o
del .\composer.phar
pause