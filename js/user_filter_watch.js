var with_screen = screen.width*0.88 
$(".user_list_wrap ").css("min-width",with_screen);
layui.config({
    base:'../../js/'
}).extend({
    xmSelect: 'xm-select'
}).use(['element', 'upload', 'form', 'jquery', 'table', 'layer','laydate','laypage', 'xmSelect','soulTable'], function() {
    var $ = layui.jquery;
    var element = layui.element;
    var upload = layui.upload;
    var laypage = layui.laypage;
    var layer = layui.layer;
    var laydate = layui.laydate;
    var soulTable=layui.soulTable;
    var table = layui.table;
    var form = layui.form;
    var xmSelect = layui.xmSelect;
    laydate.render({
        elem: '#checkDate',
        range: true,
        lang: 'en'
    });

    var sessionFilter = layui.sessionData('filter');
    //表格渲染
    $.ajax({
        url: '../../watch/filter/filter_handle.php?action=account_list',
        dataType: 'json',
        success: function (res) {
            table.reload('userTable', {
                data: res.data
            })
        },
        complete: function () {
            layer.close()
        }
    })
    var mytable=table.render({
        elem: '#userTable',
        // url: '../../watch/filter/filter_handle.php?action=account_list', //数据接口,
        id: 'userTable',
        height: 'full-200',
        align: 'right',
        loading: true,
        page: true,
        limit: 20,
        limits: [20,40,60,80],
        cols: [
            [ //表头
                { field: 'no', width: 80, align: 'center', title: 'No.', sort: true, fixed: 'left', type: 'numbers' },
                { field: 'username', align: 'center', title: 'Account', templet: '#username' ,sort: true, width: 150},
                { field: 'filtername', align: 'center', title: 'Filtername', sort: true, width: 150,filter:true},
                { field: 'temp_product', align: 'center', title: 'Product', sort: true, width: 150},
                { field: 'temp_project', align: 'center', title: 'Project', sort: true, width: 150, hide: false,filter:true},
                { field: 'temp_build', align: 'center', title: 'Stage', sort: true, width: 150, hide: false,filter:true},
                { field: 'temp_station', align: 'center', title: 'Station', sort: true, width: 250, hide: false,filter:true},
                { field: 'temp_status', align: 'center', title: 'Status', templet: '#status',  hide: false},
                { field: 'starttime', align: 'center', title: 'Starttime' , width: 150},
                { field: 'endtime', align: 'center', title: 'Endtime' , width: 150},
                // { fixed: 'right', align: 'center', title: 'Action', toolbar: '#barDemo'},
            ]
        ],
        done:function(res){
                
            soulTable.render(this)
        }
    
    });
    $(".allfilter").click(function(){
        $('.filter-setting').next().find('.layui-input').val(''); 
        $(".allfilter").addClass("layui-btn-normal");
        $(".allfilter").removeClass("layui-btn-primary ");
        $(".myfilter").addClass("layui-btn-primary");
        $(".myfilter").removeClass("layui-btn-normal ");
        var arr = xm_stage.getValue();
        var array = new Array();
        for (x in arr)
        {
          array[x] = arr[x].value
        }
        stageId = array.join("|");

        var arr1=xm_station.getValue();
        var array1 = new Array();
        for (x in arr1)
        {
          array1[x] = arr1[x].value
        }
        stationId = array1.join("|");
        $.ajax({
            url: ' ../../watch/filter/filter_handle.php?action=account_list',
            dataType: 'json',
            data:{
                stageId:stageId,
                stationId:stationId
            },
            success: function (res) {
                table.reload('userTable', {
                    data: res.data
                })
            },
            complete: function () {
                layer.close()
            }
        })
        soulTable.clearFilter(mytable.config);
    })
    
    
    $(".myfilter").click(function(){
        $('.filter-setting').next().find('.layui-input').val(''); 
        
        $(".myfilter").addClass("layui-btn-normal");
        $(".myfilter").removeClass("layui-btn-primary ");
        $(".allfilter").addClass("layui-btn-primary");
        $(".allfilter").removeClass("layui-btn-normal ");
        var arr = xm_stage.getValue();
        var array = new Array();
        for (x in arr)
        {
          array[x] = arr[x].value
        }
        stageId = array.join("|");

        var arr1=xm_station.getValue();
        var array1 = new Array();
        for (x in arr1)
        {
          array1[x] = arr1[x].value
        }
        stationId = array1.join("|");
        $.ajax({
            url: '../../watch/filter/filter_handle.php?action=my_account_list',
            dataType: 'json',
            data:{
                stageId:stageId,
                stationId:stationId
            },
            success: function (res) {
                table.reload('userTable', {
                    data: res.data
                })
            },
            complete: function () {
                layer.close()
            }
        })
        soulTable.clearFilter(mytable.config);
     
    })
     

    form.on('select(filter_setting)', function (data) {
        $(".myfilter").addClass("layui-btn-primary");
        $(".myfilter").removeClass("layui-btn-normal ");
        $(".allfilter").addClass("layui-btn-primary");
        $(".allfilter").removeClass("layui-btn-normal ");
        var arr = xm_stage.getValue();
        var array = new Array();
        for (x in arr)
        {
          array[x] = arr[x].value
        }
        stageId = array.join("|");

        var arr1=xm_station.getValue();
        var array1 = new Array();
        for (x in arr1)
        {
          array1[x] = arr1[x].value
        }
        stationId = array1.join("|");
        var filter_id = data.value;
            $.ajax({
                url: '../filter_handle.php?action=my_account_list',
                dataType: 'json',
                data:{
                    filter_id:filter_id,
                    stageId:stageId,
                    stationId:stationId
                },
                success: function (res) {
                    table.reload('userTable', {
                        data: res.data
                    })
                },
                complete: function () {
                    layer.close()
                }
            })
    soulTable.clearFilter(mytable.config);
    })
  
    var xm_stage = xmSelect.render({
        el: '#xm_stage',
        language: 'en',
        tips: 'Select Stage', 
        filterable: true,
        model: {
            label: {
                type: 'block',
                block: {
                    //最大显示数量, 0:不限制
                    showCount: 2,
                    //是否显示删除图标
                    showIcon: true,
                }
            }
        },
        filterMethod: function(val, item, index, prop){
            if(item.name == null){
                return false;
            }
            if(val == item.value){//把value相同的搜索出来
                return true;
            }
            if(item.name.indexOf(val.toUpperCase()) != -1){//名称中包含的搜索出来
                return true;
            }
            return false;//不知道的就不管了
        },
        toolbar: {
            show: true,
            list: [ 'ALL', 'CLEAR', 'REVERSE' ]
        },
        data: [],
    })

    var xm_station = xmSelect.render({
        el: '#xm_station',
        language: 'en',
        tips: 'Select Station', 
        filterable: true,
        model: {
            label: {
                type: 'block',
                block: {
                    //最大显示数量, 0:不限制
                    showCount: 2,
                    //是否显示删除图标
                    showIcon: true,
                }
            }
        },
        filterMethod: function(val, item, index, prop){
            if(item.name == null){
                return false;
            }
            if(val == item.value){//把value相同的搜索出来
                return true;
            }
            if(item.name.indexOf(val.toUpperCase()) != -1){//名称中包含的搜索出来
                return true;
            }
            return false;//不知道的就不管了
        },
        toolbar: {
            show: true,
            list: [ 'ALL', 'CLEAR', 'REVERSE' ]
        },
        data: [],
    })

        $.ajax({

            url: '../../watch/filter_handle.php?action=get_stage_station_list',
            type: 'post',
            dataType: 'json',
            success: function (res) {
                // console.log(res);
                xm_stage.update({
                    data: res['stage'],
                    on({ arr, change, isAdd }){
                        var array = new Array();
                        for (x in arr)
                        {
                            array[x] = arr[x].value
                        }
                        stageId = array.join("|");
                        var arr = xm_station.getValue();
                        var array = new Array();
                        for (x in arr)
                        {
                          array[x] = arr[x].value
                        }
                        stationId = array.join("|");
                        // console.log(stationId);
                        depId = $("[name='group_list1']:checked").val();
                        var ismyfilter = $(".myfilter").hasClass("layui-btn-normal");
                        var isallfilter = $(".allfilter").hasClass("layui-btn-normal");
                        if(isallfilter == true){
                             
                            skip_url = 'search_all_filter'                    
                        }else if(ismyfilter == true){
                            skip_url = 'search_my_filter'
                        }else{
                            $('.filter-setting').next().find('.layui-input').val('');
                            $(".allfilter").addClass("layui-btn-normal");
                            $(".allfilter").removeClass("layui-btn-primary");

                            skip_url = 'search_all_filter'
                        }
                        $.ajax({
                            url: '../search.php?action='+skip_url,
                            dataType: 'json',
                            data:{
                                stageId: stageId,
                                stationId: stationId,
                                depId:depId
                            },
                            success: function (res) {
                                table.reload('userTable', {
                                    data: res.data
                                })
                            },
                            complete: function () {
                                layer.close()
                            }
                        })
                        soulTable.clearFilter(mytable.config);
                    },
                });
                
                xm_station.update({
                    data: res['station'],
                    on({ arr, change, isAdd }){
                        // alert(`已有: ${arr.length} 变化: ${change.length}, 状态: ${isAdd}`) 
                        var arr_stage = xm_stage.getValue();
                        var array = new Array();
                        for (x in arr_stage)
                        {
                          array[x] = arr_stage[x].value
                        }
                        stageId = array.join("|");
                        // console.log(stageId);
                        var array = new Array();
                        for (x in arr)
                        {
                            array[x] = arr[x].value
                        }
                        stationId = array.join("|");
                        // console.log(stationId);
                        depId = $("[name='group_list1']:checked").val();
                        var ismyfilter = $(".myfilter").hasClass("layui-btn-normal");
                        var isallfilter = $(".allfilter").hasClass("layui-btn-normal");

                        if(isallfilter == true){
                            skip_url = 'search_all_filter'
                                              
                        }else if(ismyfilter == true){
                            skip_url = 'search_my_filter'
                        }else{
                            $('.filter-setting').next().find('.layui-input').val('');
                            $(".allfilter").addClass("layui-btn-normal");
                            $(".allfilter").removeClass("layui-btn-primary");

                            skip_url = 'search_all_filter'
                            
                        }
                        $.ajax({
                            url: '../search.php?action='+skip_url,
                            dataType: 'json',
                            data:{
                                stageId: stageId,
                                stationId: stationId,
                                depId:depId
                            },
                            success: function (res) {
                                table.reload('userTable', {
                                    data: res.data
                                })
                            },
                            complete: function () {
                                layer.close()
                            }
                        })
                        soulTable.clearFilter(mytable.config);
                    },
                })
            }

            
        });

    //监听工具条
    table.on('tool(user)', function(obj) {
        var data = obj.data;
         console.log(data);
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
            console.log(id);

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
        layer.open({
            type: 2,
            area: ['1000px', '700px'],
            fix: false, //不固定
            maxmin: true,
            shadeClose: true,
            shade: 0.4,
            title: 'Add Filter',
            content: './filter_add.html',
            end: function() {
                // $('.layui-laypage-btn').click(); //模拟点击
                window.location.reload();
            }
        });
    })

  
    table.on('row(user)', function (obj) {
        var data = obj.data;
        var username1="<?=$username?>";
        layui.sessionData('filter', {
            key:'filterSetting',
            value:data
        });
        var scrollTop=0;
        var layuiTable = $('.layui-table-main');
        if(layuiTable!=null&&layuiTable.length>0){
            scrollTop = layuiTable[0].scrollTop;  
        }
        var username=obj.data['username'];
        var filter_id = obj.data['id'];
        var ismyfilter = $(".myfilter").hasClass("layui-btn-normal");
        var isallfilter = $(".allfilter").hasClass("layui-btn-normal");

        if(isallfilter==true){
            
        var url = '../../watch/filter/edit_filter.html?filter_id=' + filter_id + '&scrollTop=' + scrollTop;
        x_admin_show('Filter', url, '1200', null, scrollTop)
        
           
          
        }
        if(ismyfilter == true){
            var url = '../../watch/filter/edit_filter.html?filter_id=' + filter_id + '&scrollTop=' + scrollTop;
        x_admin_show('Filter', url, '1200', null, scrollTop)
                              
        }
        // window.location.href = './detail_task.html?task_id=' + task_id;
        
    });

    if (undefined != sessionFilter.filter) {
        // console.log(sessionFilter.filter);
        $("#ong").removeClass("sizefont");
        $("#blc").removeClass("sizefont");
        $("#can").removeClass("sizefont");
        $("#do").removeClass("sizefont");


        $(".myfilter").addClass("layui-btn-primary");
        $(".myfilter").removeClass("layui-btn-normal ");
        $(".allfilter").addClass("layui-btn-primary");
        $(".allfilter").removeClass("layui-btn-normal ");
        
        data = sessionFilter.filter;
        table.reload('userTable', {
            url: './task_handle.php?action=task_filter',
            method: 'post',
            where: data,
            // 设置重新从第一页开始读取
            page: {
                curr: 1
            }
        })

        // 清空本地session 缓存
        layui.sessionData('filter', null);
    }

 //chaxun
//    $('.query').click(function() {
    
//       var filtername=$('.filtername').val();
//       var product=$('.product').val();
//       var project=$('.project').val();
//       var username=$('.username').val();
//     //   console.log(username)

//       table.reload('userTable', {
//         url: '../../admin/filter_handle.php?action=search_filter',
//         method: 'post',
//         where: {
//             filtername: filtername,
//             product: product,
//             project: project,
//             username:username
//         },
//         // 设置重新从第一页开始读取
//         page: {
//             curr: 1
//         }
//     })

   
    
//     return false;

// })

          
   
})