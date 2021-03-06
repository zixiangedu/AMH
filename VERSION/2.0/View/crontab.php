<?php include('header.php'); ?>
<script src="View/js/task.js"></script>
<style>
#STable td.td_block {
	padding:10px 20px;
	text-align:left;
	line-height:23px;
}
.crontab_item {
	display:inline-block;
	margin-right:20px;
	/*float:left;*/
	width:80%;
	margin:8px 0px;
}
.crontab_item .name {
	width:55px;
	display:inline-block;
}
.crontab_item span, .crontab_item select, .crontab_item input {
	float:left;
	margin:0px 1px;
}
.crontab_item select {
	width:100px;
}
.crontab_item span {
	padding:0px 1px;
}
.average_input {
	width:30px;
}
.respectively {
	width:100px;
}
</style>

<div id="body">
<h2>AMH » Task </h2>

<?php
	if (!empty($top_notice)) echo '<div style="margin:18px 2px;"><p id="' . $status . '">' . $top_notice . '</p></div>';
?>
<p>任务计划列表:</p>
<table border="0" cellspacing="1"  id="STable" style="width:850px;">
	<tr>
	<th>ID</th>
	<th>分钟</th>
	<th>小时</th>
	<th>天</th>
	<th>月</th>
	<th>星期段</th>
	<th>运行脚本/命令</th>
	<th>所属组</th>
	<th>添加时间</th>
	<th>操作</th>
	</tr>
	<?php 
	if(!is_array($crontab_list) || count($crontab_list) < 1)
	{
	?>
		<tr><td colspan="10">暂无任务计划.</td></tr>
	<?php	
	}
	else
	{
		foreach ($crontab_list as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo $val['crontab_id'];?></th>
			<td><?php echo $val['crontab_minute'];?></td>
			<td><?php echo $val['crontab_hour'];?></td>
			<td><?php echo $val['crontab_day'];?></td>
			<td><?php echo $val['crontab_month'];?></td>
			<td><?php echo $val['crontab_week'];?></td>
			<td><?php echo $val['crontab_ssh'];?></td>
			<td><?php echo $val['crontab_type'];?></td>
			<td><?php echo $val['crontab_time'];?></td>
			<td>
			<?php if($val['crontab_type'] == 'ssh') { ?>
			<a href="javascript:" class="button disabled"><span class="pen icon disabled"></span> 编辑</a>
			<a href="javascript:" class="button disabled"><span class="cross icon disabled"></span> 删除</a>
			<?php } else {?>
			<a href="index.php?c=index&a=task&edit=<?php echo $val['crontab_id'];?>" class="button"><span class="pen icon"></span> 编辑</a>
			<a href="index.php?c=index&a=task&del=<?php echo $val['crontab_id'];?>" class="button" onclick="return confirm('确认删除任务计划ID:<?php echo $val['crontab_id'];?> ?');"><span class="cross icon"></span> 删除</a>
			<?php }?>
			</td>
			</tr>
	<?php
		}
	}
	?>
</table>
<br /><br />

<?php
	if (!empty($notice)) echo '<div style="margin:18px 2px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<table border="0" cellspacing="1"  id="STable" style="width:800px;">
	<tr>
	<th><?php echo !isset($edit_task) ? '创建' : '编辑' ;?>任务计划</th>
	</tr>
	<tr>
	<td class="td_block">
	<form action="" method="POST"  id="task" />
	<div id="crontab">
	</div>

	运行脚本: &nbsp; 
	<input type="text" class="input_text" style="width:190px" name="crontab_ssh" value="<?php echo isset($_POST['crontab_ssh']) ? $_POST['crontab_ssh'] : '';?>" /> <font class="red">*</font> AMH相关命令
	<br /> 
	<br />


	<?php if (isset($edit_task)) { ?>
	<input type="hidden" name="crontab_id" value="<?php echo $_POST['crontab_id'];?>"/>
	<button type="submit" class="primary button" name="save_submit"><span class="check icon"></span>保存</button> 
	<?php } else { ?>
	<button type="submit" class="primary button" name="task_submit"><span class="check icon"></span>创建</button> 
	<?php }?>

	</form>
	</td>
	</tr>
</table>


<script>
var post = {};
<?php
	foreach ($_POST as $key=>$val)
	{
		$val = json_encode($val);
		echo "post['$key'] = $val;\n";
	}
?>
var crontab_run = function ()
{
	for (var k in  crontab_object)
	{
		for (var i in crontab_object[k] )
		{
			var name = null;
			if(crontab_object[k][i].name) name = crontab_object[k][i].name;
			if(crontab_object[k][i].amh_name) name = crontab_object[k][i].amh_name;
			if (name && post[name])
			{
				if (typeof(post[name]) == 'object')
				{
					for (var s =0; s < crontab_object[k][i].length; ++s)
					{
						if(crontab_object[k][i][s] && post[name].join('').indexOf(crontab_object[k][i][s].value) != -1)
							crontab_object[k][i][s].selected = true;
					}
				}
				else
				{
					crontab_object[k][i].value = post[name];
				}
			}
		}
		crontab_object[k].select.onchange();
	}
}
if (document.all)
	window.attachEvent('onload', function(){ crontab_run(); });
else
	window.addEventListener('load', function(){ crontab_run(); }, false);
</script>


<div id="notice_message" style="width:730px;">
<h3>» 任务计划</h3>
时间属性说明：<br />
1) 定时：固定在指定的时间点运行。* 为不限制。<br />
2) 期间：在指定时间范围内的每个时间点执行。<br />
3) 平均：在指定时间范围内平均多少个时间点执行一次。如分钟设置 0 到 59 / 2，即为平均2分钟执行一次。<br />
4) 选择：使用Ctrl铵键，选择您需的时间点执行。<br />
<br />
温馨提示: <br />
WEB端只允许添加AMH命令，例如添加AMH即时备份命令：amh backup n n 任务计划自动备份
<br />SSH添加的任务计划WEB端不可更改。
</div>


</div>
<?php include('footer.php'); ?>
