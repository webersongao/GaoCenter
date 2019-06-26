# GaoCenter 问答SNS系统简介

---

GaoCenter是基于WeCenter3.3.3版本开发的一款现代化SNS系统，支持Rest Api接口调用。 http://demo.taobihu.com/ 本项目的开发希望在Wecenter的基础上提供完整的SNS以及Wiki系统服务

### GaoCenter 问答系统的环境需求

 1. 可用的 www 服务器，如 Apache、IIS、nginx, 推荐使用性能高效的 Apache 或 nginx.
 2. PHP 7.0 及以上
 3. MySQL 5.0 及以上, 服务器需要支持 MySQLi 或 PDO_MySQL
 4. GD 图形库支持或 ImageMagick 支持, 推荐使用 ImageMagick, 在处理大文件的时候表现良好

### GaoCenter 问答系统的安装

 1. 上传 upload 目录中的文件到服务器
 2. 设置目录属性（windows 服务器可忽略这一步），以下这些目录需要可读写权限

    ./
    ./system
    ./system/config 含子目录

 3. 访问站点开始安装
 4. 参照页面提示，进行安装，直至安装完毕

### GaoCenter 问答系统在 Sina App Engine 安装

参见这篇文章: [http://www.wecenter.com/category/support/sae-install/][1]

### GaoCenter Rewrite 开启方法

参见这篇文章: [http://www.wecenter.com/category/support/settings/][2]

### GaoCenter 问答系统的升级

升级过程非常简单, 覆盖所有文件之后运行 http://您的域名/index.php?/upgrade/ 按照提示操作即可

### GaoCenter 软件的技术支持

当您在安装、升级、日常使用当中遇到疑难，请您到以下站点获取技术支持。

 - 支持：[http://www.wecenter.com/support/][3]
 - 讨论区：[http://wenda.wecenter.com][4]

### GaoCenter 大感谢

- WeCenter官方：[https://github.com/wecenter/wecenter/][5]
 
[1]: http://www.wecenter.com/category/support/sae-install/
[2]: http://www.wecenter.com/category/support/settings/
[3]: http://www.wecenter.com/support/
[4]: http://wenda.wecenter.com
[5]: https://github.com/wecenter/wecenter/

