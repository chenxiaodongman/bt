#!/bin/bash
# 更新软件包列表
echo "更新软件包列表..."
sudo apt-get update

# 安装 wget
echo "安装 wget..."
sudo apt-get install -y wget

# 下载安装脚本
echo "下载安装脚本..."
wget -O install.sh http://www.aapanel.com/script/install-ubuntu_6.0_en.sh

# 执行安装脚本，参数为 'forum'
echo "执行安装脚本，参数为 'forum'..."
yes | bash install.sh forum

# 如果存在 ssl.pl 文件，则删除
echo "如果存在 ssl.pl 文件，则删除..."
rm -f /www/server/panel/data/ssl.pl
echo "安装python依赖requests"
pip install flask requests
pip install pymysql
# 重启面板服务
echo "重启面板服务..."
/etc/init.d/bt restart
