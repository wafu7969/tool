<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/1
 * Time: 15:16
 * liunx 相关脚本
 */





/**
 * start
 * git自动拉取脚本  git.sh
 * 添加定时任务


#!/bin/sh
export git_home=/usr/local/bin
export PATH=$git_home:$PATH
source /etc/profile
cd /mydisk/www/v5_test_c/public_html/
unset GIT_DIR
git pull https://xmh2016:xmh686868@gitee.com/beijing/XXXX.git dev
chown www.www -R /mydisk/www/v5_test_c/public_html/
chmod -R 777 /mydisk/www/v5_php/public_html/public/uploads
chmod -R 777 /mydisk/www/v5_php/public_html/runtime
#日志名称
#log="/mydisk/shell/git/upgrade.log"   #操作日志存放路径
#fsize=2000000            #如果日志大小超过上限，则保存旧日志，重新生成日志文件
#exec 2>>$log   #如果执行过程中有错误信息均输出到日志文件中



 end */






/**
 * start
 * 备份文件目录脚本  bak.sh
 * 添加定时任务

#!/bin/sh

#备份程序
cp -r /mydisk/www/v5_php /mydisk/backup/v5_php     #数据存在data目录下，备份到backup目录下，所以先将数据拷过来
tar -zcvf /mydisk/backup/v5_php$(date +%Y%m%d).tar.gz /mydisk/backup/v5_php  #将数据所在文件夹bak打包
rm -rf /mydisk/backup/v5_php   #删除临时文件内容

#备份前端
cp -r /mydisk/www/v5_html_b /mydisk/backup/v5_html_b     #数据存在data目录下，备份到backup目录下，所以先将数据拷过来
tar -zcvf /mydisk/backup/v5_html_b$(date +%Y%m%d).tar.gz /mydisk/backup/v5_html_b  #将数据所在文件夹bak打包
rm -rf /mydisk/backup/v5_html_b   #删除临时文件内容

#备份前端
cp -r /mydisk/www/v5_html_admin /mydisk/backup/v5_html_admin     #数据存在data目录下，备份到backup目录下，所以先将数据拷过来
tar -zcvf /mydisk/backup/v5_html_admin$(date +%Y%m%d).tar.gz /mydisk/backup/v5_html_admin  #将数据所在文件夹bak打包
rm -rf /mydisk/backup/v5_html_admin   #删除临时文件内容

#备份C端
cp -r /mydisk/www/v5_c /mydisk/backup/v5_c     #数据存在data目录下，备份到backup目录下，所以先将数据拷过来
tar -zcvf /mydisk/backup/v5_c$(date +%Y%m%d).tar.gz /mydisk/backup/v5_c  #将数据所在文件夹bak打包
rm -rf /mydisk/backup/v5_c   #删除临时文件内容

find /mydisk/backup/ -mtime +10 -name "*.tar.gz" -exec rm -rf {} \;   #删除改文件夹下超过10天的文件



 end */






/**
 * start
 * nginx日志切割脚本  nginx_log.sh
 * 添加定时任务


#/bin/bash

yesterday=$(date +%Y-%m-%d)        #取得服务器当前时间
savepath_log='/mydisk/nginx/logs'  #分割后的日志文件保存目录(注意结尾目录斜杠去掉)
nglogs='/mydisk/nginx/logs'        #nginx日志文件目录，具体根据实际地址替换(结尾目录斜杠去掉 以免与下面路径中的"/"重复)

#判断分割日志保存目录是否存在不存在则创建
if [ ! -d ${savepath_log} ]   # 注意 语句之间 空格不可省略，这是bash脚本 不是php脚本那样随和
then
mkdir -p ${savepath_log}
fi

#通过mv命令将日志移动到分割后的日志目录里，然后发送kill -USR1信号给Nginx的主进程号，让Nginx重新生成一个新的日志文件。

mv ${nglogs}/access.log  ${savepath_log}/access_${yesterday}.log  #具体根据你的实际日志文件名路径 进行修改access.log名字
mv ${nglogs}/error.log  ${savepath_log}/error_${yesterday}.log  #具体根据实际日志文件名地址 修改error.log名字
#mv ${nglogs}/host.access.log  ${savepath_log}/host.access_${yesterday}.log
kill -USR1 $(cat /mydisk/nginx/logs/nginx.pid)  #通知nginx重新生成新的日志


end */

