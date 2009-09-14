<?php
require_once('base.php');
require_once($class_root . 'Record.php');
require_once($class_root . 'Domain.php');

$offset = 0;
$rowamount = (int) $config->get('iface.rowamount');
$start = 1;
if(isSet($_GET["start"])) {
    $offset = (($_GET["start"] - 1) * $rowamount);
    $start = $_GET["start"];
}

print $display->header();

if(! preg_match('/^\d+$/',$_GET['id'])) {
	print $display->error("You hacker!");
	print $display->footer();
	exit(1);
}

try {
        $d = Domain::find($_GET['id']);
        $result = ActiveRecord::query("SELECT COUNT(*) AS count FROM records WHERE domain_id=$d->id");
        $rCount = (int) $result[0]['count'];
        if($rCount > $rowamount) {
                $findResult = Record::find('all', array(
                                                                'limit' => "$rowamount",
                                                                'offset' => "$offset",
                                                                'conditions' => 'domain_id = '.Record::quote($d->id),
                                                                'order' => 'name'));
        } else {
                $findResult = Record::find('all', array('conditions' => 'domain_id = '.Record::quote($d->id), 'order' => 'name'));
        }
} catch (Exception $e) {
        print $e->getMessage();
        print $display->footer();
        exit(0);
}


?>

<script language="JavaScript" src="http://www.mattkruse.com/javascript/datadumper/datadumper.js"></script>

<script type="text/javascript">

	function queue_record_edit_all() { 
		$$('form').each(function(form) {
				if(form.disabled == undefined) { 
					queue_record_edit(form);
				}
		});
	} 

	function queue_record_edit(form) {

		var myhash = new Hash();
		['domain_id', 'record_id', 'name', 'type', 'content', 'ttl', 'prio'].each(function(k) {
			if(form[k] !== undefined) {
				  myhash.set(k, form[k].getValue());
			} 
		});

		var params = myhash.toJSON();

		new Ajax.Request('api/jsonrpc.php', {
				  method: 'post',
				  parameters: {"jsonrpc": "2.0", "method": 'queue_record_edit', "params": params , "id": 1},
				  onSuccess: function(r) {
					var json = r.responseText.evalJSON();
					if(json.error) {
						$('feedback').update(json.error.message + ' (' + json.error.code + ')').
						setStyle({color: 'red', display: 'block'});
					} else {
						$('feedback').update('Request added to queue').setStyle({color: 'black', display: 'block'});
						queue_counter();
						form[form.disabled ? 'enable' : 'disable']();
						form.disabled = !form.disabled;
						form.getElementsByTagName('button')[0].writeAttribute( { 'disabled': true } );
						form.getElementsByTagName('button')[1].writeAttribute( { 'disabled': true } );
					}
				  }});
	} 

	var rowcounter = 0;

	function add_row() { 
		var e = $('form0'); // This is the 'template'
		var id = 'form' + ++rowcounter;
		var newform = new Element('form', { 
						'action': e.readAttribute('action'), 
						'onsubmit': e.readAttribute('onsubmit'),
						'id': id
		                          }).update(e.innerHTML);


		newform.getElementsByTagName('tr')[0].hide();  						// Hide header
		newform.getElementsByTagName('button')[1].show();					// Show 'delete row' button
		newform.enable();				 					// Enable the form (when form0 is disabled)
		newform.getElementsByTagName('button')[0].writeAttribute( { 'disabled': false } );	// Enable the 'Add to' button
		newform.getElementsByTagName('button')[1].writeAttribute( { 'disabled': true } );	// Enable the 'Delete row' button
		$('container').insert(newform);								// Add form to container
		//newform.focusFirstElement();								// Focus
		//newform.scrollTo();									// Scroll browser to position
		return id;
	} 

	function delete_row(form) { 
		form.remove();
	} 

	// TODO : Should clean this function up once ..
	function fill_form(id, h) { 
		var form = $(id);
		h !== undefined && h.each(function(pair) { 
				$A(form.type.options).each(function(e) { 
					if(pair.key == e.value) { 
						e.selected = true;
					} 
				});
				form[pair.key].value = pair.value 
		});
	} 

	function load_records_to_form() { 
			<?php 
				foreach($findResult as $record) {
					 print "fill_form(add_row(), new Hash({'record_id': '$record->id', 'name' : '$record->name', 'type' : '$record->type', 'content': '$record->content', 'ttl': '$record->ttl', 'prio': '$record->prio'}));\n";
				}
				print "delete_row(form0)\n";
			?>
			$('form0')[0].select();
	} 

	<?php
		print "document.observe('dom:loaded', function() { load_records_to_form() });\n";
	?>

</script>

<div id="feedback" style="display: none;"></div>

<br>

<div class="header"><?php print "Edit record(s) for domain: "; print is_object($d) ? $d->name : $d; ?></div><br>
<br>

<?php

if($rCount > $rowamount) {
    print $display->show_pages($rCount, $rowamount,$d->id,null,$start);
    print "<br><br>";
}

?>

<form action="javascript:void(0);" onsubmit="return false;" id="form0">
<input type="hidden" name="domain_id" value="<?php echo $d->id; ?>">
<input type="hidden" name="record_id" value="">
<table>
<tr>
 <th>Name</th>
 <th>Type</th>
 <th>Content</th>
 <th>TTL</th>
 <th>Prio</th>
 <th>Action</th>
</tr>
<tr class="record">
	 <td><input type="text" name="name" value=""></td>
	 <td>
				<select name="type">
				<?php foreach(Record::valid_types() as $type) {
					print '<option value="'.$type.'">'.$type.'</option>'."\n";
				} ?>
				</select>
	 </td>
	 <td><input type="text" name="content" value="" size="40"></td>
	 <td><input type="text" name="ttl" value=""></td>
	 <td><input type="text" name="prio" size="3"></td>
	 <td>
		<button onclick="queue_record_edit(this.ancestors()[4]);">Add to queue</button>
		<button onclick="delete_row(this.ancestors()[4]);" style="display: none;">Delete row</button>
	</td>
</tr>
</table>
</form>

<div id="container"></div>

<br>
<button onclick="queue_record_edit_all(); return false;">Add all to queue</button>

<?php
print $display->footer();
?>
