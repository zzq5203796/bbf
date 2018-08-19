一. MYSQL 命令
===============

>###说明:
- username：你将创建的用户名   
- host：登陆主机，本地 localhost，任意远程主机登陆 %, ip    
- password：登陆密码，密码可以为空，为空则可以不需要密码登陆   
- privileges：  操作权限 SELECT，INSERT，UPDATE等，所有权限 ALL  
- databasename：数据库名  所有数据库 \*  
- tablename：   表名 所有数据库表 *， 所有数据库和表 \*.\*

###一. 创建用户
>命令： 创建、查询
```
CREATE USER 'username'@'host' IDENTIFIED BY 'password';
select host,user from mysql.user;
```
>说明:

- username：你将创建的用户名   
- host：登陆主机，本地 localhost，任意远程主机登陆 %, ip    
- password：登陆密码，密码可以为空，为空则可以不需要密码登陆   

>例子：
```
CREATE USER 'dog'@'localhost' IDENTIFIED BY '123456';
CREATE USER 'pig'@'192.168.1.101' IDENDIFIED BY '123456';
CREATE USER 'pig'@'%' IDENTIFIED BY '123456';
CREATE USER 'pig'@'%' IDENTIFIED BY '';
CREATE USER 'pig'@'%';
```
     
        
###二. 授权:  
 
>命令： 授权、查询
```
GRANT privileges ON databasename.tablename TO 'username'@'host';
SHOW GRANTS FOR username;
```

>说明:  
- privileges：  操作权限 SELECT，INSERT，UPDATE等，所有权限 ALL  
- databasename：数据库名  所有数据库 \*  
- tablename：   表名 所有数据库表 *， 所有数据库和表 \*.\*

>例子：
```
GRANT SELECT, INSERT ON test.user TO 'pig'@'%';
GRANT ALL ON *.* TO 'pig'@'%';
GRANT ALL ON maindataplus.* TO 'pig'@'%';
```
>注意:

用以上命令授权的用户不能给其它用户授权，如果想让该用户可以授权，用以下命令:

```
GRANT privileges ON databasename.tablename 
    TO 'username'@'host' WITH GRANT OPTION;
```

###三. 撤销用户权限
>命令：
```
REVOKE privilege ON databasename.tablename FROM 'username'@'host';
```
>说明:

- privilege, databasename, tablename：同授权部分

>例子：
```
REVOKE SELECT ON *.* FROM 'pig'@'%';
```


###四.设置与更改用户密码
>命令：
```
SET PASSWORD FOR 'username'@'host' = PASSWORD('newpassword');   
```
如果是当前登陆用户用:   
```
SET PASSWORD = PASSWORD("newpassword");
```
>例子：
```
SET PASSWORD FOR 'pig'@'%' = PASSWORD("123456");
```

###五.删除用户
>命令：
```
DROP USER 'username'@'host';
```