QH4框架扩展模块-后台权限模块

### 功能
1、后台用户管理

2、后台角色管理

3、权限资源管理

4、用户、角色、权限的关联关系管理

5、权限资源yml文件解析

### 依赖
该模块依赖于 `city` 城市模块，如果不想安装该模块，需要手动删除用户选择城市部分
```
composer require qh4/city
```
该模块依赖于 `Token` 模块
```
composer require qh4/token
```

### 关于解析YML文件

该方法依赖于 `yaml` 扩展，最简单的安装方式
```
pecl install ymal
```
具体安装方式因个人环境而异。

在模块的 `test` 目录提供了一份示例

可以调用 `actionParseYml` 方法来解析文件，根据你自己的 controller 引用方式，可能需要手动传入可用的 Token

yml文件必须放在 `libs` 目录下，名称为 `privilege.yml`

