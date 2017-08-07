#!/usr/bin/bash

#自动批量执行命令
#ddz01	180.150.178.110
#ddz02	123.59.140.154
#ddz03	120.132.59.104
#ddz04	123.59.47.27
#ddz05	123.59.56.158
#ddz06	123.59.83.104
#ddz07	106.75.14.252
#ddz host	120.132.58.159
#ddz test	180.150.178.112

#制定版本号
version=389
#指定路径
path=game
#检出代码
##co="svn co svn://120.26.4.188/ddz/$path --username huangxf --password huangxf -r $version /data/svn "
##cmd="rm -rf /data/svn && "$co

#备份旧代码
#backup=" mv /data/sweety/$path/ /data/$(date +%Y%m%d-%H:%M:%S) && mv /data/svn /data/sweety/$path"
#scmd=$backup

#检查
#cmd="svn info /data/sweety/$path/"

#更新
cmd="svn up -r $version /data/sweety/$path"
echo $cmd


echo 'S1服上传'
ssh -p 2021 -i ~/Downloads/Identity root@180.150.178.110 $cmd
echo 'S2服上传'
ssh -p 2021 -i ~/Downloads/Identity root@123.59.140.154 $cmd
echo 'S3服上传'
ssh -p 2021 -i ~/Downloads/Identity root@120.132.59.104 $cmd
echo 'S4服上传'
ssh -p 2021 -i ~/Downloads/Identity root@123.59.47.27 $cmd
echo 'S5服上传'
ssh -p 2021 -i ~/Downloads/Identity root@123.59.56.158 $cmd
echo 'S6服上传'
ssh -p 2021 -i ~/Downloads/Identity root@123.59.83.104 $cmd
echo 'S7服上传'
ssh -p 2021 -i ~/Downloads/Identity root@106.75.14.252 $cmd
echo 'Host服上传'
ssh -p 2021 -i ~/Downloads/Identity root@120.132.58.159 $cmd

