<?php
require_once('base.php');
require_once($class_root . 'Domain.php');

print $display->header();

?>

<script type="text/javascript">
	document.observe("dom:loaded", function() { Form.focusFirstElement($('addform')); });

	function queue_domain_add(id) {
                var myhash = new Hash();
                ['name', 'type'].each(function(k) {
                        if($(id)[k] !== undefined) {
                                  myhash.set(k, $(id)[k].getValue());
                        } 
                });

                var params = myhash.toJSON();
		//window.params = params;

                new Ajax.Request('api/jsonrpc.php', {
							method: 'post',
							parameters: {"jsonrpc": "2.0", "method": 'queue_domain_add', "params": params , "id": 1},
							onSuccess: function(r) {
								var json = r.responseText.evalJSON();
								if(json.error) { 
									$('feedback').update(json.error.message + ' (' + json.error.code + ')').
									setStyle({color: 'red', display: 'block'});
								} else {
									$('feedback').update('Request added to queue').setStyle({color: 'black', display: 'block'});
									window.location = 'record_add.php?domain_name=' + myhash.get('name') + '&template=new_domain';
								}
							}});

	} 
</script>

<div id="feedback" style="display: none;"></div>

<form name="addform" id="addform" onSubmit="queue_domain_add(this.id); return false;">
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
