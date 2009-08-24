<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'string.php');

class Display {

	protected $options = array();

	public $pages = array(
				'index.php' => array('name' => 'Home', 'menu' => true),
				'search.php' => array('name' => 'Search domains and records', 'menu' => true),
				'domain_add.php' => array('name' => 'Add domain', 'menu' => true),
				'domain_list.php' => array('name' => 'List domains', 'menu' => true),
				'domain_edit.php' => array('name' => 'Edit domain', 'menu' => false),
				'queue.php' => array('name' => 'Queue', 'menu' => true),
				'record_add.php' => array('name' => 'Add record(s)', menu => false),
				'record_list.php' => array('name' => 'Record list', menu => false),
	);

	public function __construct($options) { 
		$this->options = $options;
	} 

	public function pagename() {
		return $this->pages[basename($_SERVER['PHP_SELF'])]['name'];
	}

	public function header() {
		$string = <<< __EOS__
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$this->options['config']->get('application.name')} :: {$this->pagename()}</title>
<link rel=stylesheet href="style/{$this->options['config']->get('iface.style')}.css" type="text/css">
<script src="js/prototype.js" type="text/javascript"></script>
<script src="js/scriptaculous.js" type="text/javascript"></script>
<script type="text/javascript">

	function queue_counter() { 
	        new Ajax.Request('{$this->options['config']->get('jsonrpc.uri')}', {
                          method: 'post',
                          parameters: {"jsonrpc": "2.0", "method": "queue_count_all", "params": null, "id": 1},
                          onSuccess: function(r) {
                            $$('#queue_count_all').each(function(e) { e.update('(' + r.responseJSON.result + ')') });
                          }
		});
	}

        document.observe("dom:loaded", function() { queue_counter(); });
	new PeriodicalExecuter(queue_counter, 10);

</script>
</head>
<body>
<h1>{$this->options['config']->get('application.name')}</h1>
__EOS__;

		$string .= '<div class="menu">';
		foreach($this->pages as $url => $props) { 
			if($props['menu']) 
				$string .= '<span class="menuitem"><a href="'.$url.'">'.$props['name'].'</a></span>'."\n";
			if($url == 'queue.php') 
				$string .= '<span class="menuitem" id="queue_count_all" style="color: red; font-weight: bold;"></span>'."\n";
		} 
		$string .= '</div><br>';

		return $string;
	}
	
	public function footer() {
		return <<< __EOS__
</body>
</html>
__EOS__;
	} 

	public function button($type = 'edit', $onclick = '') { 
		return sprintf('<img src="images/%s.gif" title="%s" alt="%s" width="16" height="16">', $type, $type, $type);
	} 

	public function link($href = '', $content = '', $onclick = '') { 
		return sprintf('<a href="%s"%s>%s</a>',$href, $onclick != '' ? sprintf(' onclick="%s"', $onclick) : '', $content);
	} 

	public function error($txt = '') {
		return sprintf('<div class="%s">%s</div><br>',__FUNCTION__,$txt);
	} 

	public function alert($txt = '') {
		return sprintf('<div class="%s">%s</div><br>',__FUNCTION__,$txt);
	} 

	public function records_header() { 
		return <<< __EOS__
			<table>
			<tr>
			 <th>Name</th>
			 <th>Type</th>
			 <th>Content</th>
			 <th>TTL</th>
			</tr>
__EOS__;
	} 

	public function record($record = array()) {
		$record = (object)$record;

		return <<< __EOS__
		<tr class="record">
			<td>$record->name</td>
			<td>$record->type</td>
			<td>$record->content</td>
			<td>$record->ttl</td>
		</tr>
__EOS__;
	} 

	private function table_footer() { 
		return '</table>';
	} 

	public function records_footer() { 
		return $this->table_footer();
	} 

	public function domains_header() { 
		return <<< __EOS__
			<table>
			<tr>
			 <th>Name</th>
			 <th>Type</th>
			 <th>Records</th>
			 <th>Owner</th>
			 <th>Actions</th>
			</tr>
__EOS__;
	} 

	public function domains_footer() { 
		return $this->table_footer();
	} 

	public function domain($domain = array()) { 
		$domain = (object)$domain;

		return <<< __EOS__
		       <tr class="domain" id="tr_entry{$domain->id}">
			       <td>$domain->name</td>
			       <td>$domain->type</td>
			       <td>$domain->record_count</td>
			       <td>xx</td>
			       <td id="action_entry{$domain->id}">
				       {$this->link('domain_edit.php?id='.$domain->id,$this->button('edit'))}
				       {$this->link('#',$this->button('delete'), "domain_delete($domain->id, '$domain->name')")}
				       {$this->link('record_list.php?id='.$domain->id,$this->button('view'))}
			       </td>
		       </tr>
__EOS__;
	} 

}

?>
