$(function () {

    
    var buildflag = true;
    $(".builddiv .arrpng").click(function () {
        if (buildflag) {
            $(".builddiv>ul").show();
            buildflag = false;
        } else {
            $(".builddiv>ul").hide();
            buildflag = true;
        }

    })
    var stationflag = true;
    $(".stationdiv .arrpng").click(function () {
        if (stationflag) {
            $(".stationdiv>ul").show();
            stationflag = false;
        } else {
            $(".stationdiv>ul").hide();
            stationflag = true;
        }

    })
    var productflag = true;
    $(".productdiv .arrpng").click(function () {
        if (productflag) {
            $(".productdiv>ul").show();
            productflag = false;
        } else {
            $(".productdiv>ul").hide();
            productflag = true;
        }

    })
    var projectflag = true;
    $(".projectdiv .arrpng").click(function () {
        if (projectflag) {
            $(".projectdiv>ul").show();
            projectflag = false;
        } else {
            $(".projectdiv>ul").hide();
            projectflag = true;
        }

    })
    var buildflag1 = true;
    $(".builddiv1 .arrpng").click(function () {
        if (buildflag1) {
            $(".builddiv1>ul").show();
            buildflag1 = false;
        } else {
            $(".builddiv1>ul").hide();
            buildflag1 = true;
        }

    })
    var stationflag1 = true;
    $(".stationdiv1 .arrpng").click(function () {
        if (stationflag1) {
            $(".stationdiv1>ul").show();
            stationflag1 = false;
        } else {
            $(".stationdiv1>ul").hide();
            stationflag1 = true;
        }

    })
    var productflag1 = true;
    $(".productdiv1 .arrpng").click(function () {
        if (productflag1) {
            $(".productdiv1>ul").show();
            productflag1  = false;
        } else {
            $(".productdiv1>ul").hide();
            productflag1 = true;
        }

    })
    var projectflag1 = true;
    $(".projectdiv1 .arrpng").click(function () {
        if (projectflag1) {
            $(".projectdiv1>ul").show();
            projectflag1 = false;
        } else {
            $(".projectdiv1>ul").hide();
            projectflag1 = true;
        }

    })

    var user_id = $('.user_id').html();
    //    alert(user_id)
    //请求专案
    $.ajax({
        type: "post",
        url: "./create_task.php?action=create_search",
        dataType: "json",
        data: {
            'user_id': user_id
        },
        success: function (response) {
            // console.log(response);
            var projectstr = "";
            var stationstr = "";
            var buildstr = "";
            var assignstr = "";
            var productstr = "";
            for (var i = 0; i < response['project'].length; i++) {
                projectstr = "<li class='licom' id='" + response['project'][i]['id'] + "'>" + response['project'][i]['project'] + "</li>"
                $(".projectdiv>ul").append(projectstr);
                $(".projectdiv1>ul").append(projectstr);
            }
            for (var j = 0; j < response['station'].length; j++) {
                stationstr = "<li class='licom' id='" + response['station'][j]['id'] + "'>" + response['station'][j]['station'] + "</li>"
                $(".stationdiv>ul").append(stationstr);
                $(".stationdiv1>ul").append(stationstr);
            }
            for (var k = 0; k < response['group'].length; k++) {
                assignstr = "<input class='licom' type='radio' name='assign' value='" + response['group'][k]['id'] + "' title='" + response['group'][k]['group'] + "'>"
                $(".assign").append(assignstr);
            }
            for (var m = 0; m < response['build'].length; m++) {
                buildstr = "<li class='licom' id='" + response['build'][m]['id'] + "'>" + response['build'][m]['build'] + "</li>"
                $(".builddiv>ul").append(buildstr);
                $(".builddiv1>ul").append(buildstr);
            }
            for (var n = 0; n < response['product'].length; n++) {
                productstr = "<li class='licom' id='" + response['product'][n]['id'] + "'>" + response['product'][n]['product'] + "</li>"
                $(".productdiv>ul").append(productstr);
                $(".productdiv1>ul").append(productstr);
            }

            //点击下拉列框切换值
            $(".builddiv>ul>li").click(function () {
                var build = $(this).text();
                $(".buildinput").val(build);
                $(".builddiv>ul").hide();
                buildflag = true;
            })
            $(".stationdiv>ul>li").click(function () {
                var station = $(this).text();
                $(".stationinput").val(station);
                $(".stationdiv>ul").hide();
                stationflag = true;
            })
            $(".builddiv1>ul>li").click(function () {
                var build1 = $(this).text();
                $(".buildinput1").val(build1);
                $(".builddiv1>ul").hide();
                buildflag1 = true;
            })
            $(".stationdiv1>ul>li").click(function () {
                var station1 = $(this).text();
                $(".stationinput1").val(station1);
                $(".stationdiv1>ul").hide();
                stationflag = true;
            })
             //切换product改变project值
            $(".productdiv>ul>li").click(function () {
                var product = $(this).text();
                var product_id = $(this).attr('id');
                $(".productinput").val(product);
                $(".productdiv>ul").hide();
                productflag = true;
                $.ajax({
                    type: "post",
                    url: "./create_task.php?action=create_search",
                    dataType: "json",
                    data: {
                        'product_id': product_id,
                        'user_id': user_id
                    },
                    success: function (response) {
                        $(".projectdiv>ul").html("");
                        $(".projectinput").val("");
                        var projectstr = "";
                        for (var i = 0; i < response['project'].length; i++) {
                            projectstr = "<li class='licom' id='" + response['project'][i]['id'] + "'>" + response['project'][i]['project'] + "</li>"
                            $(".projectdiv>ul").append(projectstr);
                        }
                        $(".projectdiv>ul>li").click(function () {
                            // alert('1')
                            var project = $(this).text();
                            $(".projectinput").val(project);
                            $(".projectdiv>ul").hide();
                            projectflag = true;
                        })
                    }
                })
            })
              //同步时切换product改变project值
            $(".productdiv1>ul>li").click(function () {
                var product1 = $(this).text();
                var product_id1 = $(this).attr('id');
                $(".productinput1").val(product1);
                $(".productdiv1>ul").hide();
                productflag = true;
                $.ajax({
                    type: "post",
                    url: "./create_task.php?action=create_search",
                    dataType: "json",
                    data: {
                        'product_id': product_id1,
                        'user_id': user_id
                    },
                    success: function (response) {
                        $(".projectdiv1>ul").html("");
                        $(".projectinput1").val("");
                        var projectstr1 = "";
                        for (var i = 0; i < response['project'].length; i++) {
                            projectstr1 = "<li class='licom' id='" + response['project'][i]['id'] + "'>" + response['project'][i]['project'] + "</li>"
                            $(".projectdiv1>ul").append(projectstr1);
                        }
                        $(".projectdiv1>ul>li").click(function () {
                            // alert('1')
                            var project1 = $(this).text();
                            $(".projectinput1").val(project1);
                            $(".projectdiv1>ul").hide();
                            projectflag1 = true;
                        })
                    }
                })
            })   
        }
    });
})