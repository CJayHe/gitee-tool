# gitee 工具集
![](https://beijing-online-seo-1257309290.cos.ap-beijing.myqcloud.com/seo/material/icon.png)
## 运行环境

  php5.6
  
  安装git和对应的ssh公钥
 
## 文件权限目录 --读写权限

   chmod -R 777 app/cache app/logs  
   
## 创建应用 

   [应用创建教程](https://gitee.com/api/v5/oauth_doc#/list-item-3)
    
## 命令集合
    
1. 全部仓库备份命令 （执行命令用已配置好ssh公钥的用户执行,不要sudo）克隆本账号下全部的仓库的master分支；
    
   ```php app/console gitee:project:all:back```
   
## 执行过程简述
   
   该工具根据`gitee`官方开发接口开发；使用官方接口需要获得授权；
   
   所以使用本工具集内的命令需要填入授权的参数，参数的具体输入在执行命令后会有对应提示，按照提示输入即可；
   
   授权参数可以查看上文`创建应用给出的链接`深入了解，简单总结，需要的参数有`gitee`的`登陆账密`和账号下应用的`client_id`和`client_secret`;
   
   应用的创建点击上文的`创建应用教程`链接，进入页面后在侧边栏中找到`创建应用流程`，按照说明操作即可。
   
   为了确保命令的成功,应用的授权范围最好给到最大。
   
## 隐私和安全

   本工具完全本地部署，不和外界产生数据交换。
  
   
 
   

 
  




