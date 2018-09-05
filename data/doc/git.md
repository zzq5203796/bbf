Git 常见问题
===============

>  #### Git忽略文件不起作用解决方案
>>解决办法：
>> ```
>> # 清除本地库的缓存
>> git rm -r --cached .
>> # 讲本地代码重新加入
>> git add .
>> # 并让 .gitignore 文件夹生效，读取我配置的过滤规则
>> git commit -m "update .gitignore"
>> ```


