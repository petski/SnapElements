<?php
require_once('base.php');
require_once($class_root . 'Domain.php');
require_once($class_root . 'Record.php');

print $display->header();
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() { Form.focusFirstElement($('queryForm')); });
</script>

Search through domains (name) and records (name/content). SQL 'LIKE' syntax supported: an underscore (_) in pattern matches any single character, a percent sign (%) matches any string of zero or more characters.<br><br>

<form method="GET" id="queryForm" name="queryForm">
<input type="text" name="query" value="<?php echo isSet($_GET['query']) ? $_GET['query'] : ''; ?>">
<input type="submit" name="submit" value="Search">
</form>

<?php

if($_GET["query"]) { 
	$dFindResult = Domain::get_all(array('conditions' => 'd.name LIKE '.Domain::quote($_GET['query'])));
	if(is_array($dFindResult) && count($dFindResult) > 0) { 
		print '<h2>Domains ('.count($dFindResult).')</h2>';
		print $display->domains_header();
		foreach($dFindResult as $domain) {
			print $display->domain($domain);
		}
		print $display->domains_footer();
		print '<br>';
	} 
	flush();


	$rFindResult = Record::find('all', array('conditions' => 'name LIKE '.Record::quote($_GET['query']).' OR '.
								 'content LIKE '.Record::quote($_GET['query']), 'order' => 'name'));
	if(is_array($rFindResult) && count($rFindResult) > 0) { 
		print '<h2>Records ('.count($rFindResult).')</h2>';
		print $display->records_header();
		foreach($rFindResult as $record) {
			print $display->record($record);
		}
		print $display->records_footer();
	} 
}

print $display->footer();
?>
