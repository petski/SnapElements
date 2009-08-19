<?php
require_once('base.php');
require_once($class_root . 'Domain.php');
require_once($class_root . 'Queue.php');

$errors = array();

if($_POST['submit']) { 

	if(! Domain::is_valid('name', $_POST['name'])) { 
		array_push($errors, 'Name not valid!');
	} 

	if(! Domain::is_valid('type', $_POST['type'])) { 
		array_push($errors, 'You hacker!');
	} 
	
	# Shouldn't be a existing domain
	if(Domain::find('first', array('conditions' => 'name = '.Domain::quote($_POST['name'])))) {
		array_push($errors, 'Domain already exists!');
	} 

	# Shouldn't be a pending (domain_add) change for this domain.
	$qFindResult = Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0 AND function="domain_add"'));
	foreach($qFindResult as $entry) { 
		if(with(json_decode($entry->change))->{'name'} == $_POST['name']) { 
			array_push($errors, 'Already pending change for this domain!');
		} 
	} 

	if(count($errors) == 0) { 
		$queue = new Queue(array(
					'change_date' => date("Y-m-d\TH:i:s"),
					'archived' => 0,
					'user_id' => 1,			# TODO
					'user_name' => 'henkie',	# TODO
					'function' => basename(__FILE__,'.php'),
					'change' => json_encode(array('name' => $_POST['name'], 'type' => $_POST['type'])),
				));
		$queue->save();
		header('Location: record_add.php?domain_name='.$_POST['name'].'&template=new_domain');
		exit;
	}
} 

print $display->header();

foreach($errors as $error) { 
	print $display->error($error);
} 

?>

<script type="text/javascript">
	document.observe("dom:loaded", function() { Form.focusFirstElement($('addform')); });
</script>

<form method="POST" name="addform" id="addform">
	<table>
	<tr class="domain">
		<td><div class="header">Name</div></td>
		<td><input type="text" name="name" value="<?php echo isSet($_POST['name']) ? $_POST['name'] : ''; ?>"></td>
	</tr>
	<tr class="domain">
		<td><div class="header">Type</div></td>
		<td>
			<select name="type">
			<?php foreach(Domain::valid_types() as $type) { 
				print '<option value="'.$type.'"'.( $type == $_POST['type'] ? ' SELECTED' : '').'>'.$type.'</option>'."\n";
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
