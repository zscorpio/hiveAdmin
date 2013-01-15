if(!window.console){
	window.console={}
	window.console.log=function(){return;}
}
$(function(){
	// 监听input变化
	if(document.all){
		$('input[type="text"]').each(function() {
			var that=this;
			if(this.attachEvent) {
				this.attachEvent('onpropertychange',function(e) {
					if(e.propertyName!='value') return;
					$(that).trigger('input');
				});
			}
		})
	}

	// 左侧数据库高亮
	var url = $.url(),
		database = url.param('database');
	$(".sidebar li a").each(function(){
		if($(this).text() == database){
			$(this).parent('li').addClass('active')
		}
	})	

	// 点击close按钮
	$(".alert .close").live('click',function(){
		$(this).parent().remove();
	}) 

	// 点击删除数据库
	$(".remove-db").click(function(){
		var obj = $(this);
		var db_name = $(this).data('db');
		if (confirm('确定删除?')==true){		
			$.get('/main/del?database='+db_name, function(data) {
				if(data == "success"){
					obj.parents("li").remove();
				}else{
					alert('删除失败')
				}
			});
		}
	})
})