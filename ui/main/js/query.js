var Hadoop = { 
	// 入口
	init : function(method,data){
		switch(method){
			case 'createBox':
				Hadoop._createBox(data);
			break
			default:
				console.log('default');
		}
	},
	// 生成弹出层
	_createBox : function(data){
		var tmp = {
			title 	: data.title,
			key 	: data.key,
			detail 	: data.detail
		}
		Hadoop._boxTpl();
		$(".hp-box-container").append(Hadoop.cpBoxTpl.render(tmp));
	},
	// 弹出层模版
	_boxTpl:function(){
		var tpl = [
			'	<li class="hp-detail-box" data-key="${key}">',
			'		<h4><input type="checkbox" class="check-reverse">${title}</h4>',
			'		<ul>',
			'			{@each detail as key,value}',
			'			<li><input type="checkbox" class="check-li" data-key="${value}">${key}</li>',
			'			{@/each}',
			'		</ul>',
			'		<div class="op-area">',
			'			<span class="op-min">▼</span>',
			'			<span class="op-close">×</span>',
			'		</div>',
			'	</li>'
		].join('\n');
		this.cpBoxTpl = juicer(tpl);
	}
};
var Condition = {
	// 入口
	init : function(method){
		switch(method){
			case 'where':
				Condition._where();
			break
			default:
				console.log('default');
		}
	},
	// where语句
	_where : function(){
		var tpl = [
			'<li class="condition-bg warn">',
			'	<span class="first-params">(选择)</span>',
			'	<b class="middle-condition" value="=">(=)</b>',
			'	<span class="second-params">(选择)</span>',
			'	<a class="d-close" href="javascrit:;" style="display: block;">×</a>',
			'</li>'
		].join('\n');
		$(".condition-preview ul").append(tpl);
	},
	// 项目列表
	chose_list : function(){
		var list_string = "";
		$(".hp-detail-box").each(function(){
			var parent_key = $(this).data('key');
			var parent_value = $(this).find('h4').text();
			$(this).find("ul li").each(function(){
				list_string += '<p class="condition-p" data-value="'+parent_key+"."+$(this).find('input').data('key')+'">'+parent_value+'的'+$(this).text()+'</p>';
			})
		})
		list_string += '<input type="text" class="user_input">'
		return list_string;
	},
	// 条件列表
	condition_list : function(){
		var list_string = '';
		var symbol = this.symbol();
		for(i in symbol){
			list_string += '<p class="condition-p" data-value="'+symbol[i]+'">'+i+'</p>';
		}
		console.log(list_string);
		return list_string;
	},
	// 符号列表
	symbol : function(){
		var _symbol = {
			'等于' 			: '=',
			'不等于' 		: '!=',
			'小于' 			: '<',
			'小于等于' 		: '<=',
			'大于' 			: '>',
			'大于等于' 		: ' >=',
			'包含' 			: 'like',
			'不包含' 		: 'not like',
			'为空' 			: 'is null',
			'不为空' 		: 'is not null',
			'在某个区间' 	: 'is between',
			'不在某个区间' 	: 'is not between',
			// '在某个列表' 	: 'is in list',
			// '不在某个列表' 	: 'is not in list'
		};
		return _symbol;
	}
}
var Sql = {
	init : function(){
		var sql_string = 
			this._selectSql()+
			this._fromSql()+
			this._whereSql();
		$(".hp-code-area").text(sql_string);
		$(".CodeMirror").remove();
		var editor = CodeMirror.fromTextArea(document.getElementById("mysql-code"), {
			mode: "text/x-mysql",
			tabMode: "indent"
		});
	},
	_selectSql : function(){
		var select_sring = "SELECT ";
		var select_array = [];
		$(".hp-detail-box").each(function(){
			var length = 0;
			var obj = $(this);
			var per_select_array = [];
			$(this).find(".check-li").each(function(){
				if( $(this).attr("checked") ){
					length++;
					per_select_array.push(obj.data("key") + "."+$(this).data('key'));
				}
			})
			if( !length || length == $(this).find(".check-li").length ){
				select_array.push(obj.data("key") + ".*");
			}else{
				select_array.push(per_select_array.join(","));
			}
		})
		return "SELECT " + select_array + "\n";
	},
	_fromSql : function(){
		var from_array = [];
		$(".hp-box-container .hp-detail-box").each(function(){
			from_array.push($(this).data('key'));
		})
		var from_string = 'FROM '+from_array.join(",")+"\n";
		return from_string;
	},
	_whereSql : function(){
		var where_array = [],
			where_string = '';
		if(this._whereCheck()){
			$(".condition-bg").each(function(){
				var obj = $(this),
					string = '',
					first_params = obj.find(".first-params").data("value"),
					middle_condition = obj.find(".middle-condition").data("value"),
					second_params = obj.find(".second-params").data("value"),
					string = first_params+middle_condition+second_params;
				console.log(string);
				where_array.push(string);
			});
			if(where_array.length>=1){
				where_string = 'WHERE '+where_array.join("AND")+"\n";
			}
		}
		return where_string;
	},
	_whereCheck : function(){
		var error_num = 0;
		$(".condition-bg").each(function(){
			var obj = $(this);
			var first_params = obj.find(".first-params").data("value");
			var middle_condition = obj.find(".middle-condition").data("value");
			var second_params = obj.find(".second-params").data("value");
			if(first_params && middle_condition && second_params){
				obj.removeClass('warn').removeClass('success').addClass('success');
			}else{
				error_num++;
				console.log("yyy");
				obj.removeClass('warn').removeClass('success').addClass('warn');
			}
		})
		if(error_num){
			return false;
		}else{
			return true;
		}
	}
}
$(function(){
	$(".add-table").click(function(){
		var obj = $(this);
		var key_list = $(".hp-code-preview").data("key_list");
		var key = obj.data('key');
		if( key_list.indexOf( ","+key ) == "-1" ){
			var data = {
				title 	: obj.siblings("a").text(),
				key 	: key,
				detail 	: obj.data('detail')
			}
			console.log(data);
			Hadoop.init('createBox',data);
			$(".hp-code-preview").data("key_list",key_list+","+key);
		}
	})
	$(".check-reverse").live('click',function(){
		$(this).parent('h4').siblings('ul').find('.check-li').each(function () {  
			$(this).attr("checked", !$(this).attr("checked"));  
		});  
	})
	$(".condition-add ul li").live('click',function(){
		Condition.init($(this).data('type'));
	})
	$(".op-close").live("click",function(){
		var key = $(this).parents(".hp-detail-box").data('key');
		var key_list = $(".hp-code-preview").data("key_list");
		$(this).parents(".hp-detail-box").remove();
		$(".hp-code-preview").data("key_list",key_list.replace(","+key, ""));
	})
	$(".op-min").live("click",function(){
		$(this).parent(".op-area").siblings("ul").slideToggle();
	})
	$(".condition-bg span").live("click",function(){
		$("#dialog-follow,#active-span,#active-b").attr("id","");
		$(this).parent("li").attr("id","dialog-follow");
		$(this).attr("id","active-span");
		var dialog = $.dialog.get('condition-dialog');
		if(dialog){
			dialog.close();
		}
		$.dialog({
			id: 'condition-dialog',
			title:'添加选项',
			content: Condition.chose_list(),
			padding: 10,
			follow: document.getElementById('dialog-follow')
		});
		$(".condition-p").live("click",function(){
			$("#active-span").text($(this).text()).data("value",$(this).data('value'));
		})
	})
	$(".condition-bg b").live("click",function(){
		$("#dialog-follow,#active-b,#active-span").attr("id","");
		$(this).parent("li").attr("id","dialog-follow");
		$(this).attr("id","active-b");
		var dialog = $.dialog.get('condition-dialog');
		if(dialog){
			dialog.close();
		}
		$.dialog({
			id: 'condition-dialog',
			title:'添加选项',
			content: Condition.condition_list(),
			padding: 10,
			follow: document.getElementById('dialog-follow')
		});
		$(".condition-p").live("click",function(){
			$("#active-b").text($(this).text()).data("value",$(this).data('value'));
		})
	})
	$('.user_input').live('keyup', function(){		
		if (event.keyCode == 13 && $(".user_input").val()){ 
			$("#active-b,#active-span").text($(this).val()).data("value",$(this).val());
		}
	});
	$(".d-close").live("click",function(){
		$(this).parent("li").remove();
	})
	// 检查判断条件的合法性
	$('body').live("click",function(){
		Sql.init();
	})
})