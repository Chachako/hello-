$(function () {
    // 动态获取department和product
			$.ajax({
				type: "get",
				url: "../admin/handle.php?action=get_product_group",
				dataType: "json",
				success: function (response) {
					console.log(response);
                    var groupstr = "";
                    var productstr = "";
                    for (var i = 0; i < response['group'].length; i++) {
                        groupstr = "<option value='" + response['group'][i]['id'] + "'>" + response['group'][i]['group'] + "</option>";
                        $(".department").append(groupstr);
                    }
                    for (var j = 0; j < response['product'].length; j++) {
                        productstr = "<option value='" + response['product'][j]['id'] + "'>" + response['product'][j]['product'] + "</option>";
                        $(".product").append(productstr);
                    }
                    
				}
			});
})