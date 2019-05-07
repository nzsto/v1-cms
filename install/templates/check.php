<div class="container">

	<div class="content">
		<p class="title">目录权限</p>
		<table class="table">
		<?php
			$check=0;
			foreach($check_dir as $k=>$v){
		?>
		<tr>
			<td>【<?php echo $v;?>】文件夹</td>
			<?php if(is_writable($v)) {
			 echo "<td>【可写】</td>";
			} else {
				echo "<td style=\"color:red\">【不可写】</td>";$check=1;
			}?>
		</tr>
		<?php }?>
		</table>
		<p class="title">系统环境</p>
		<table class="table">
			<tr>
				<td>【GD】支持</td>
				<?php echo extension_loaded('gd')&&function_exists('imagecreate')?'<td>√支持GD</td>':'<td style="color:red">×不支持GD(与图片有关的一些功能将不能使用)</td>';?>

			</tr>
			<tr>
				<td>【MySQL】支持</td>
				<?php if(extension_loaded('mysql')&&function_exists('mysql_connect')){
					echo '<td>√支持Mysql</td>';
				}else{
					echo '<td style="color:red">×不支持Mysql</td>';
					$check=1;
				}?>
			</tr>
			<tr>
				<td>【PHP版本】</td>
				<td><?php echo PHP_VERSION;?></td>
			</tr>
			<tr>
				<td>【操作系统】</td>
				<td><?php echo PHP_OS;?></td>
			</tr>
			<tr>
				<td>【服务器】</td>
				<td><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
			</tr>
			<tr>
				<td>【服务器域名】</td>
				<td><?php echo $_SERVER['HTTP_HOST'];?></td>
			</tr>
		</table>
	</div>

	<?php if($check==1){ echo"<span style=\"color:red\">安装失败</span>";}?>

	<div class="act">
		<button type="button" class="btn btn-primary prevStep">上一步：说明</button>
		<button type="button" class="btn btn-primary">重新检查</button>
		<button class="btn btn-primary nextStep" <?php if($check){ echo "style=\"display:none\" disabled=\"disabled\"";}?> type="button">下一步：配置系统</button>
	</div>

</div>
</div>
<script type="text/javascript">
$('.prevStep').click(function(){
	window.location.href='index.php?a=index';
});
$('.nextStep').click(function(){
	window.location.href='index.php?a=config';
});
</script>

</body>
</html>