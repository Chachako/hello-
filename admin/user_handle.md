# user_handle 接口参数

---

## 1.add_account

增加新用户

    url : user_handle.php?action=add_account
    method : post
    param : 
        username
        password
        email
        phone
        level 
        group  传ID
        group_category  传ID
        product 

    return 
        成功：success
        失败：
            当前用户名已经存在 exists      
            插入失败          fail



## 2.update_password

修改密码

    url:user_handle.php?action=update_password
    method:post
    param:
        password
        userId
    return
        新密码与旧密码一致: the old password is identical to the new one
        成功：success
        失败：fail

## 3. personal_info

获取个人信息

    url:user_handle.php?action=personal_info
    method:post
    param:
        userId 用户id
    return:
        成功: 返回个人信息 json格式
        失败: fail

## 4. account_list

用户列表

    url:user_handle.php?action=account_list
    method:post
    param:

    return:
        成功:
            {
                code:0,
                msg:"",
                count:"",
                data:{
                    ....
                }
            }
        失败:
            fail

## 5. searchCol

查询某个字段是否存在(用户判断用户名是否是重复, 修改密码时 新旧密码是否一致)

    url:user_handle.php?action=isset_username   判断用户名
        user_handle.php?action=repeat_password  判断密码是否一致
 
    method: post
    param:
        username  isset_username 用户名

        oldPassword  repeat_password 判断密码
        id  用户id

    return
        成功:success
        失败:fail





```
/*
 * @Author: moxuan 
 * @Date: 2019-03-02 09:23:53 
 * @Last Modified by: moxuan
 * @Last Modified time: 2019-03-05 09:45:18
 */
```

    
    



    
