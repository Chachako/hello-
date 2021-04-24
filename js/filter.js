/*用户-添加*/
function member_add(title, url, w, h) {
    x_admin_show(title, url, w, h);
}
layui.use(['element', 'upload', 'form', 'jquery', 'table', 'layer','laydate'], function() {
    var $ = layui.jquery;
    var element = layui.element;
    var upload = layui.upload;
    var layer = layui.layer;
    var laydate = layui.laydate;
    var table = layui.table;
    var form = layui.form;
    laydate.render({
        elem: '#checkDate',
        range: true,
        lang: 'en'
    });
    function x_admin_close() {
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    }


    
   
    //表格渲染
    table.render({
        elem: '#userTable',
        url: '../../admin/filter_handle.php?action=account_list', //数据接口,
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
                { field: 'username', align: 'center', title: 'Account', templet: '#username' , width: 120},
                { field: 'filtername', align: 'center', title: 'Filtername',  width: 180},
                { field: 'temp_product', align: 'center', title: 'Product',  width: 120},
                { field: 'temp_project', align: 'center', title: 'Project',  width: 120, hide: false},
                { field: 'temp_build', align: 'center', title: 'Build',  width: 150, hide: false},
                { field: 'temp_station', align: 'center', title: 'Station',  width: 180, hide: false},
                { field: 'temp_status', align: 'center', title: 'Status',  width: 180, hide: false},
                { field: 'start_time', align: 'center', title: 'start_time'},
                { field: 'end_time', align: 'center', title: 'end_time'},
                { fixed: 'right', align: 'center', title: 'Action', toolbar: '#barDemo'},
            ]
        ]
    });

    //监听工具条
    table.on('tool(user)', function(obj) {
        var data = obj.data;
        //  console.log(data);
        var layEvent = obj.event;
        var tr = obj.tr;
       var filtername=data.filtername;
        if (layEvent === 'edit') {
           
            var filter_id = data.id;
            layui.sessionData('filter', {
                key:'filterSetting',
                value:data
            });
            window.location.href="index.html";
           


        }  
        else if (layEvent === 'delete') {

            var id=data.id;
            // console.log(id);

            layer.confirm('Are you sure?', function(index){
                // obj.del(); //删除对应行（tr）的DOM结构
                // layer.close(index);
                $.ajax({
                   type: 'post',
                   url: '../../admin/filter_handle.php?action=del_filterId',
                   data: {
                    id:id
                },
                   cache: false,
                   dataType: 'json',
                   success: function(msg) {
                        if(msg=="success"){
                            alert('Success!');
                            layer.closeAll();
                            table.reload('userTable', {
                                content: './index copy.html',
                                // 设置重新从第一页开始读取
                                page: {
                                    curr: 1
                                }
                            })
                        }else{                      
                            alert('fail');
                            layer.closeAll();
                        }                  
                    }
                });
            });

        }
    });

    
    $('.add').click(function() {
        window.location.href="filter_add.html";
       
    })

 
 //chaxun
   $('.query').click(function() {
    
      var filtername=$('.filtername').val();
      var product=$('.product').val();
      var project=$('.project').val();
      var username=$('.username').val();
    //   console.log(username)

      table.reload('userTable', {
        url: '../../admin/filter_handle.php?action=search_filter',
        method: 'post',
        where: {
            filtername: filtername,
            product: product,
            project: project,
            username:username
        },
        // 设置重新从第一页开始读取
        page: {
            curr: 1
        }
    })

   
    
    return false;

})

          
   
})