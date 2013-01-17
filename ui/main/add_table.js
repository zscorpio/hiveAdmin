var Table = {
	init : function(){
	},
	add_tpl : function(){
		var data_type = {
			'string' 	: 	'String',
			"tinyint" 	: 	'Tiny int(3)',
			"smallint" 	: 	'Small int(5)',
			"int"		: 	'Int(10)',
			"bigint" 	: 	'Big int(19)',
			"double"	: 	'Double',
			"float"		: 	'Float',
			"boolean"	: 	'Boolean'
		};
		var format_type = {
			"text" 		:"纯文本",
			"lzop" 		:"lzo压缩",
			"sequence" 	:"序列化文件",
			"rcfile" 	:"RCFile(hive 0.6.0以上版本)",
			"bzip2" 	:"bzip2压缩",
			"gzip" 		:"gzip压缩"
		};
		var	type_list = '';
		for(i in data_type){
			type_list += '<option value="'+i+'">'+data_type[i]+'</option>';
		}
		var format_list = '';
		for(i in format_type){
			format_list += '<option value="'+i+'">'+format_type[i]+'</option>';
		}
		var tpl=[
			'<table class="table table-bordered">',
			'	<thead>',
			'		<tr>',
			'			<th>字段名</th>',
			'			<th>字段类型</th>',
			'			<th>注释</th>',
			'		</tr>',
			'	</thead>',
			'	<tbody>',
			'		{@each i in range(0, column_num)}',
			'			<tr>',
			'				<td><input type="text" name="field_name[]"></td>',
			'				<td>',
			'					<select name="field_type[]">',
									type_list,
			'					</select>',
			'				</td>',
			'				<td><input type="text" name="comment[]"></td>',
			'			</tr>',
			'		{@/each}',
			'	</tbody>',
			'</table>',
			'<table class="table table-bordered">',
			'	<tr>',
			'		<th>AS select_statment (无需请留空):</th>',
			'		<td><input type="text" name="as" disabled="disabled">(我不知道干吗的...)</td>',
			'	</tr>',
			'	<tr>',
			'		<th>数据分隔符,支持正则(, \\t \\n |):</th>',
			'		<td><input type="text" name="data_terminator" value=","></td>',
			'	</tr>',
			'	<tr>',
			'		<th>行分隔符 支持正则(, \\t \\n |):</th>',
			'		<td><input type="text" name="column_terminator" value="\\n"></td>',
			'	</tr>',
			'	{@if external==2}',
			'	<tr>',
			'		<th>外部路径(hdfs://):</th>',
			'		<td><input type="text" name="column_terminator" value="hdfs://"></td>',
			'	</tr>	',
			'	<tr>',
			'		<th>源数据格式:</th>',
			'		<td>',
			'			<select name="format">',
							format_list,
			'			</select>',
			'		</td>',
			'	</tr>',
			'	{@/if}',
			'</table>'
		].join('\n');
		this._compiled_add_tpl = juicer(tpl);
	},
	add : function(){
		var data = {
			'column_num' : $("#column_num").val()?$("#column_num").val():0,
			'external'	 : $("#external").val()
		}
		Table.add_tpl();
		$(".add_table_tpl").html(Table._compiled_add_tpl.render(data));
	},
	alert_tpl : function(type,info){
		var tpl=[
			'<div class="alert alert-'+type+'">',
			'	<button type="button" class="close" data-dismiss="alert">×</button>',
				info,
			'</div>'
		].join('\n');
		return tpl;	
	},
	load : function(database,table){
		var tpl=[
			'<tr class="load_data_tr">',
			'	<td>选择文件系统</td>',
			'	<td>加载数据路径(数据的完整路径)</td>',
			'	<td>分区?(可选) ex: (ds="2012-10-24")</td>',
			'	<td colspan="3">覆盖原数据?</td>',
			'	<td colspan="2"><button class="btn btn-danger remove">移除</button></td>',
			'</tr>',
			'<tr class="load_data_tr">',
			'	<td>',
			'		<select name="local" class="local">',
			'			<option value="LOCAL">本地</option>',
			'			<option value="HDFS">HDFS</option>',
			'		</select>',
			'	</td>',
			'	<td><input type="text" name="path" class="path" required="required"></td>',
			'	<td><input type="text" name="partition" class="partition" disabled></td>',
			'	<td colspan="3"><input type="checkbox" name="overwrite" value="1" class="overwrite"></td>',
			'	<td colspan="2"><button class="btn btn-primary load" data-database="'+database+'" data-table="'+table+'">加载</button></td>',
			'</tr>'
		].join('\n');
		return tpl;
	}
}
$(function(){
	Table.add();
	$('#column_num').on('input',function(){
		Table.add();
	})
	$("#external").change(function(){
		Table.add();
	})

	// 删除表
	$(".delete_table").click(function(){
		if(confirm('你确定删除吗?')){
			$(".content").prepend(Table.alert_tpl('info','删除ing...'));
			var database = $(this).data('database'),
				table 	 = $(this).data('table'),
				obj 	 = $(this);
			$.get('/main/table/drop?database='+database+"&table="+table,function(data){
				$(".alert").remove();
				if(data == 'success'){
					obj.parents('tr').remove();
					$(".content").prepend(Table.alert_tpl('success','删除成功'));
				}else{
					$(".content").prepend(Table.alert_tpl('error','删除失败'));
				}
			});
		}
	})

	// 复制表
	$(".copy_table").click(function(){
		var tpl = [
			'<div class="input-append">',
			'	<select class="external span3">',
			'		<option value="EXTERNAL_TABLE">外部表</option>',
			'		<option value="MANAGED_TABLE">内部表</option>',
			'		<option value="INDEX_TABLE" disabled style="color:#D14;">索引表</option>',
			'		<option value="VIRTUAL_VIEW" disabled style="color:#D14;">虚拟视图</option>',
			'	</select>',
			'	<input class="new_table span3" type="text" placeholder="请输入表名">',
			'	<button class="btn copt_btn" type="button">复制</button>',
			'</div>'
		].join('\n');
		$(this).after(tpl);
	})

	// 复制表
	$(".copt_btn").live('click',function(){
		var data = {
			database 	: $(this).parent().siblings('a').data('database'),
			table 		: $(this).parent().siblings('a').data('table'),
			new_table 	: $(this).siblings('.new_table').val(),
			external 	: $(".external").val()
		}
		$.post('/main/table/copy', data, function(response) {
			$(".alert").remove();
			if(response == 'success'){
				$(".content").prepend(Table.alert_tpl('success','复制成功'));
				location.reload();
			}else{
				$(".content").prepend(Table.alert_tpl('error','复制失败'));
			}
		});
	})

	// 加载数据
	$(".load_data").click(function(){
		$(".active_tr").removeClass('active_tr');
		$(this).parents('tr').addClass('active_tr');
		$(".load_data_tr").remove();
		var database = $(this).data('database');
		var table = $(this).data('table');
		$(this).parents('tr').after(Table.load(database,table));
	})

	// 加载数据
	$(".load").live("click",function(){
		if($(this).parents('tr').find('.overwrite').attr("checked")){
			var overwrite = 'cover';
		}else{
			var overwrite = 'nocover';
		}
		var data = {
			database 	: $(this).data('database'),
			table 		: $(this).data('table'),
			local 		: $(this).parents('tr').find('.local').val(),
			path 		: $(this).parents('tr').find('.path').val(),
			partition 	: $(this).parents('tr').find('.partition').val(),
			overwrite 	: overwrite
		}	
		$.post('/main/table/load', data, function(response) {
			$(".alert").remove();
			if(response == 'success'){
				$(".content").prepend(Table.alert_tpl('success','导入成功'));
				location.reload();
			}else{
				$(".content").prepend(Table.alert_tpl('error','导入失败'));
			}
		});
	})

	// 移除加载
	$(".remove").live("click",function(){
		$(".active_tr").removeClass('active_tr');
		$(".load_data_tr").remove();
	})
})