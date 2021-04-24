/*用户-添加*/
function member_add(title, url, w, h) {
    x_admin_show(title, url, w, h);
}
layui.use(['element', 'upload', 'form', 'jquery', 'table', 'layer'], function() {
    var $ = layui.jquery;
    var element = layui.element;
    var upload = layui.upload;
    var layer = layui.layer;
    var table = layui.table;
    var form = layui.form;
    //表格渲染
    table.render({
        elem: '#userTable',
        url: '../admin/user_handle.php?action=account_list', //数据接口,
        id: 'userTable',
        height: 'auto',
        align: 'right',
        loading: true,
        page: true,
        limit: 10,
        limits: [10, 30, 50,100,200],
        cols: [
            [ //表头
                { field: 'no', width: 80, align: 'center', title: 'No.', sort: true, fixed: 'left', type: 'numbers' },
                { field: 'username', align: 'center', title: 'Account', templet: '#account' , width: 150},

                { field: 'product', align: 'center', title: 'Product', templet: '#product' , width: 150},
                { field: 'project', align: 'center', title: 'Project', templet: '#project' ,  width: 150, hide: false},
                { field: 'group', align: 'center', title: 'Departpment', templet: '#department' , width: 80  ,sort:true},
                { field: 'long_phone', align: 'center', title: 'MobilePhone' },
                { field: 'phone', align: 'center', title: 'ShortPhone' , width: 150},
                { field: 'email', align: 'center', title: 'Email', templet: '#email' },
                { field: 'enable', align: 'center', title: 'Enable', templet: '#status', hide: true },
                { fixed: 'right', align: 'center', title: 'Action', toolbar: '#barDemo' }
            ]
        ]
    });


    // 监听工具条
    table.on('tool(user)', function(obj) {
        var data = obj.data;
         console.log(data);

        var layEvent = obj.event;
        var tr = obj.tr;
        
        if (layEvent === 'edit') {
            layer.open({
                type: 1,
                title: 'Edit',
                area: ['500px;', '600px'],
                anim: 1, //弹出动画
                id: 'btn_edit',
                moveType: 1,
                // shade: 0.8,
                content: $('#editForm'),
                success: function(layer, index) {
                
                    $('#userid').val(data.id);
                    $('#level').val(data.level);
                    $('#username').val(data.username);
                    $('#group').val(data.group);
                    $('#long_phone').val(data.long_phone);
                    $('#phone').val(data.phone);
                    $('#email').val(data.email);
                    // console.log(data.group_category);
                    console.log(data.product);
                    if(data.group_category==1){
                        $("input[name='group_category'][value='1']").attr("checked",'true');
                    }else{
                        $("input[name='group_category'][value='2']").attr("checked",'true');
                    }
                    form.render('radio');

                    $.ajax({
                        url: '../admin/handle.php?action=get_product_list&page=1&limit=10',
                        type: 'get',
                        dataType: 'json',
                        success: function(res) {
                            $(".customer_product").html("");
                            $("#customer_product").show();
                            var productarr = data.product.split('|');

                            // console.log(productarr)
                            var cus_productstr = "";
                            $(".customer_product").html("");
                            for (var j = 0; j < res['data'].length; j++) {
                                cus_productstr = "<input type='checkbox' name='product[" + res['data'][j]['id'] + "]'  value='"+res['data'][j]['id'] +"'      lay-skin='primary'  lay-filter='testproduct'  id='productApp' title='" + res['data'][j]['product'] + "'>";
                                $(".customer_product").append(cus_productstr);
                                // for (var i = 0; i < productarr.length; i++) {
                                //     $("input[type=checkbox][title='" + productarr[i] + "']").attr("checked", 'true')
                                // }
                            }
                            form.render('checkbox')

                        }
                    })





                }
            });
        }
        
        
        else if (layEvent === 'change_psd') {
            var title = true;
            layer.open({
                type: 1,
                title: 'Update Password',
                area: '500px;',
                anim: 1, //弹出动画
                id: 'btn_add',
                moveType: 1,
                shade: 0.6,
                anim: 1,
                content: $('#updateForm'),
                success: function(layer, index) {
                    $('#change_userid').val(data.id)
                }
            })
        } else if (layEvent === 'forbidden_user'){
            layer.confirm('Are you sure?', function(index){
                $.ajax({
                   type: 'post',
                   url: '../admin/user_handle.php?action=forbidden_account',
                   data: data,
                   cache: false,
                  
                   dataType: 'json',
                   success: function(data) {
                        if(data=="success"){
                            alert('Update Success!')
                            layer.closeAll();
                            table.reload('userTable', {
                                url: '../admin/user_handle.php?action=account_list',
                                // 设置重新从第一页开始读取
                                page: {
                                    curr: 1
                                }
                            })
                        }else{                      
                            alert('fail 用户以创建task不能删除');
                            layer.closeAll();
                        }                  
                    }
                });
            });
        } else if (layEvent === 'renew_user'){
            layer.confirm('Are you sure?', function(index){
                $.ajax({
                   type: 'post',
                   url: '../admin/user_handle.php?action=renew_account',
                   data: data,
                   cache: false,
                  
                   dataType: 'json',
                   success: function(data) {
                        if(data=="success"){
                            alert('Update Success!')
                            layer.closeAll();
                            table.reload('userTable', {
                                url: '../admin/user_handle.php?action=account_list',
                                // 设置重新从第一页开始读取
                                page: {
                                    curr: 1
                                }
                            })
                        }else{                      
                            alert('fail 用户以创建task不能删除');
                            layer.closeAll();
                        }                  
                    }
                });
            });
        }
    });

 //chaxun
   $('.query').click(function() {
      var name=$('.name').val();
      var product=$('.product').val();
      var project=$('.project').val();
      var department=$('.department').val();
    //   console.log(123131);

      table.reload('userTable', {
        url: '../admin/user_handle.php?action=search_user',
        method: 'post',
        where: {
            name: name,
            product: product,
            project: project,
            department: department
        },
        // 设置重新从第一页开始读取
        page: {
            curr: 1
        }
    })
   
 
    // $.ajax({
    //     type: 'post',
    //     url: '../admin/user_handle.php?action=search_user',
    //     data: {
    //         name: name,
    //         product: product,
    //         project: project,
    //         department: department
    //     },
       
    //     dataType: 'json',
    //     success: function(res) {
    //         console.log(res);
       
    //     },
    //     error:function(res){
    //         console.log(res);
    //     }
    // });

    return false;
})



    //修改密码
    $('.updatebtn').click(function(e) {
        e.preventDefault()
        var user_id = $('#change_userid').val();
        var psd = $('#password').val();
        $.ajax({
            type: 'post',
            url: '../admin/user_handle.php?action=update_password',
            data: {
                password: psd,
                userId: user_id
            },
            cache: false,
            async: false,
            dataType: 'json',
            success: function(res) {
                console.log(res)
                if (res == 1) {
                    alert('Update Success!')
                    layer.closeAll();
                } else if (res == -1) {
                    alert('the old password is identical to the new one!')
                    layer.closeAll();
                } else {
                    alert('Update fail!')
                    layer.closeAll();
                }
            }
        });
    })

     


    form.on('checkbox(testproduct)', function (data) {
                // 获取已经选中的product 
                var proArr=[];
                $('input:checkbox[id=productApp]:checked').each(function(){
                      proArr.push($(this).val());
                 })
                if(proArr.length==0){
                    $(".customer_project").empty();
                }
                console.log(proArr);
                var product_id = data.value; 
                 // console.log(product_id);

                    $.ajax({
                    type: 'post',
                    url: '../admin/user_handle.php?action=get_product_project',
                    data: {
                        product_id:product_id,
                        productArr:proArr
                    },

               
                    dataType: 'json',
                    success: function(response) {
                        $(".customer_project").empty();

                        console.log(response);
                         
                        for (var j = 0; j < response.length; j++) {
                          projectstr = "<input type='checkbox' name='project[" + response[j]['id'] + "]'  value='"+response[j]['id'] +"'    lay-skin='primary' id='projectApp' title='" + response[j]['project'] + "'>";
                        $(".customer_project").append(projectstr);
                       }

                       form.render('checkbox')
                
                    }
                });

   

            });




  


    // 修改用户信息
    form.on('submit(editform)', function(data) {

                     var proArr=[];
                $('input:checkbox[id=productApp]:checked').each(function(){
                      proArr.push($(this).val());
                 })
                if(proArr.length==0){
                    alert("Products  are empty");
                    return false ;
                }
               
                var projectArr=[];
                $('input:checkbox[id=projectApp]:checked').each(function(){
                      projectArr.push($(this).val());
                 })
                if(projectArr.length==0){
                    alert("Projects  are empty");
                    return false ;
                }

        
        //  alert('1')
        // console.log(data)
        $.ajax({
            type: 'post',
            url: '../admin/user_handle.php?action=update_account',
            data: data.field,
            cache: false,
            async: false,
            dataType: 'json',
            success: function(data) {
                // console.log(data)
                if (data == '1') {
                    alert('Update Success!')
                    layer.closeAll();
                    window.location.href = './account_list.html';
                    // table.reload('userTable', {
                    //     url: '../admin/user_handle.php?action=account_list',
                    //     // 设置重新从第一页开始读取
                    //     page: {
                    //         curr: 1
                    //     }
                    // })
                } else {
                    alert('Update fail!')
                    layer.closeAll();
                }
            }
        });
    })

})