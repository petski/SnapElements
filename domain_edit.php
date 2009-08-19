<?php
require_once('base.php');
require_once($class_root . 'Domain.php');

$d = Domain::find($_GET['id']);

if($_POST['submit']) { 
	$d->type = $_POST['type'];
	if($d->is_modified()) { 
		$d->save();
	} 
	header("Location: domain_list.php");
	exit;
} 

print $display->header();
?>

<form method="POST">
	<input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>">
	<table>
	<tr class="domain">
		<td><div class="header">Name</div></td>
		<td><?php print $d->name; ?></td>
	</tr>
	<tr class="domain">
		<td><div class="header">Type</div></td>
		<td>
			<select name="type">
			<?php foreach(Domain::valid_types() as $type) { 
				print '<option value="'.$type.'"'.( $type == $d->type ? ' SELECTED' : '').'>'.$type.'</option>'."\n";
			} ?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: right;">
			<input type="submit" name="submit" value="Submit">
		</td>
	</tr>
	</table>
</form>

<?php
print $display->footer();
?>
