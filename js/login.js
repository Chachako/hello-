$(function () {
    $('body').height = document.body.clientHeight;
    //点击enter键查询
    document.onkeydown = function (event) {
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if (e && e.keyCode == 13) {
            aja();
        }
    }
    $(".login-btn").click(function () {
        aja();
    })

    function aja() {
        var username = $('.username').val();
        var password = $('.password').val();
        var remember = $('#rememberbox').val();
        if ($('#rememberbox').attr("checked") == 'checked') {
            remember = 1; //1记住密码
        } else {
            remember = 0; //0不记住密码
        }
        $.ajax({
            type: "post",
            url: "./userlogin.php",
            data: {
                'username': username,
                'password': password,
                'remember': remember
            },
            dataType: "json",
            success: function (response) {
                // alert(response);
                if (response[0].level == '1') { //1管理员
                    // alert(response[0].level)
                    window.location.href = './manage/manage_main.html'
                } else if (response[0].level == '0' || response[0].level == '2') { //0或2进前台
                    // alert(response[0].level)
                    window.location.href = './welcome.html'
                } else {
                    $(".right p").html(response);
                }
            }
        });
    }
})