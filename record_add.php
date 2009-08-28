<?php
require_once('base.php');
require_once($class_root . 'Record.php');
require_once($class_root . 'Domain.php');
print $display->header();

$default_key = $_GET['domain_id'] ? 'domain_id' : 'domain_name';
$d = null;

if(preg_match('/^\d+$/', $_GET[$default_key])) {
	$d = Domain::find('first', array('conditions' => 'id = '. $_GET[$default_key]));
} else {
	$d = $_GET[$default_key];
}

?>

<script language="JavaScript" src="http://www.mattkruse.com/javascript/datadumper/datadumper.js"></script>

<script type="text/javascript">

	function queue_record_add_all() { 
		$$('form').each(function(form) {
				if(form.disabled == undefined) { 
					queue_record_add(form);
				}
		});
	} 

	function queue_record_add(form) {

		var myhash = new Hash();
		['domain_id', 'domain_name', 'name', 'type', 'content', 'ttl', 'prio'].each(function(k) {
			if(form[k] !== undefined) {
				  myhash.set(k, form[k].getValue());
			} 
		});

		var params = myhash.toJSON();

		new Ajax.Request('api/jsonrpc.php', {
				  method: 'post',
				  parameters: {"jsonrpc": "2.0", "method": 'queue_record_add', "params": params , "id": 1},
				  onSuccess: function(r) {
					var json = r.responseText.evalJSON();
					if(json.error) {
						$('feedback').update(json.error.message + ' (' + json.error.code + ')').
						setStyle({color: 'red', display: 'block'});
					} else {
						$('feedback').update('Request added to queue').setStyle({color: 'black', display: 'block'});
						//window.location = 'record_add.php?domain_name=' + myhash.get('name') + '&template=new_domain';
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
		newform.getElementsByTagName('button')[1].writeAttribute( { 'disabled': false } );	// Enable the 'Delete row' button
		$('container').insert(newform);								// Add form to container
		newform.focusFirstElement();								// Focus
		newform.scrollTo();									// Scroll browser to position
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

	function load_template(template) { 
		var domain_name = $('form0')['domain_name'].value;
		if(template == 'new_domain') { 
			<?php 
			$serial = ''.date('Ymd').'00';
			$soa = join(" ", array( 
						$config->get('dns.ns1'),
						$config->get('dns.hostmaster'),
						$serial,
						$config->get('dns.refresh'),
						$config->get('dns.retry'),
						$config->get('dns.expire'),
						$config->get('dns.minimum') 
			));
			?>
			fill_form('form0',   new Hash({'name' : domain_name, 'type' : 'SOA', 'content' : '<?php echo $soa; ?>'}));
			fill_form(add_row(), new Hash({'name' : domain_name, 'type' : 'A', 'content': '<your ipaddress>'}));
			fill_form(add_row(), new Hash({'name' : 'www.' + domain_name, 'type' : 'CNAME', 'content' : domain_name}));
			fill_form(add_row(), new Hash({'name' : 'mail.' + domain_name, 'type' : 'CNAME', 'content' : domain_name}));
			fill_form(add_row(), new Hash({'name' : domain_name, 'type' : 'MX', 'prio' : 10, 'content' : 'mail.' + domain_name }));
			fill_form(add_row(), new Hash({'name' : 'localhost.' + domain_name, 'content' : '127.0.0.1'}));
			$('form1')[3].select();
		} 
	} 


	<?php
	if(isSet($_GET['template'])) { 
		print "document.observe('dom:loaded', function() { load_template('".$_GET['template']."') });\n";
	} 
	else { 
		print "document.observe('dom:loaded', function() { Form.focusFirstElement($('form0')); });\n";
	} 
	?>

</script>

<div id="feedback" style="display: none;"></div>

<br>

<div class="header"><?php print "Add record(s) for domain: "; print is_object($d) ? $d->name : $d; ?></div><br>
<br>

<form action="javascript:void(0);" onsubmit="return false;" id="form0">
<input type="hidden" name="<?php echo $default_key; ?>" value="<?php echo $_GET[$default_key]; ?>">
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
	 <td><input type="text" name="name" value="<?php print is_object($d) ? $d->name : $d;?>"></td>
	 <td>
				<select name="type">
				<?php foreach(Record::valid_types() as $type) {
					print '<option value="'.$type.'">'.$type.'</option>'."\n";
				} ?>
				</select>
	 </td>
	 <td><input type="text" name="content" value="" size="40"></td>
	 <td><input type="text" name="ttl" value="<?php echo $config->get('dns.ttl')?>"></td>
	 <td><input type="text" name="prio" size="3"></td>
	 <td>
		<button onclick="queue_record_add(this.ancestors()[4]);">Add to queue</button>
		<button onclick="delete_row(this.ancestors()[4]);" style="display: none;">Delete row</button>
	</td>
</tr>
</table>
</form>

<div id="container"></div>

<br>
<button onclick="add_row(); return false;">Add row</button>
<?php if(! isSet($_GET['template'])) { ?>
<button onclick="load_template('new_domain'); return false;">Template for new domain</button>
<?php } ?>
<button onclick="queue_record_add_all(); return false;">Add all to queue</button>

<?php
print $display->footer();
?>
