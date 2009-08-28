<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'string.php');
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Domain.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Record.php';

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

	public function button($type = '', $alt = null, $onclick = '') { 
		switch($type) {
			case "add":
				$image	= "add";
				($alt) ? null : $alt = "Add";
				break;
			case "close":
				$image	= "lock_open";
				($alt) ? null : $alt = "Close";
				break;
			case "closed":
				$image	= "lock";
				($alt) ? null : $alt = "Closed";
				break;
			case "commit":
				$image	= "disk";
				($alt) ? null : $alt = "Commit";
				break;
			case "delete":
				$image	= "delete";
				($alt) ? null : $alt = "Delete";
				break;
			case "edit":
				$image	= "wrench";
				($alt) ? null : $alt = "Edit";
				break;
			case "save":
				$image	= "disk";
				($alt) ? null : $alt = "Edit";
				break;
			case "view":
				$image	= "magnifier";
				($alt) ? null : $alt = "View";
				break;
			default:
				$image	= "error";
				$alt	= "Unknown image";
				break;
		}

		return sprintf('<img src="images/icons/%s.png" title="%s" alt="%s" width="16" height="16">', $image, $alt, $alt);
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
		$amount = count($domain->records);
		return <<< __EOS__
		       <tr class="domain" id="tr_entry{$domain->id}">
			       <td>$domain->name</td>
			       <td>$domain->type</td>
			       <td>$amount</td>
			       <td>xx</td>
			       <td id="action_entry{$domain->id}">
				       {$this->link('domain_edit.php?id='.$domain->id,$this->button('edit'))}
				       {$this->link('#',$this->button('delete'), "domain_delete($domain->id, '$domain->name')")}
				       {$this->link('record_list.php?id='.$domain->id,$this->button('view'))}
			       </td>
		       </tr>
__EOS__;
	} 

	public function queue_header() {
                return <<< __EOS__
                        <table>
                        <tr>
                         <th>Date</th>
                         <th>Domain</th>
                         <th>Comment</th>
                         <th>Action</th>
                        </tr>
__EOS__;
	}

	public function queue_footer() {
                return $this->table_footer();
	}


	public function queue($queue = array()) {
		$queue = (object)$queue;
		$rd_class = ($queue->closed == 0) ? "queue" : "queue_closed";
		$lock_icon = ($queue->closed == 0) ? "close" : "closed";

		return <<< __EOS__
			   <tr class="{$rd_class}" id="tr_entry{$queue->id}">
						<td>$queue->ch_date</td>
						<td>$queue->domain_name</td>
						<td>$queue->comment</td>
						<td id="action_entry{$queue->id}">
								{$this->link('#',$this->button($lock_icon), "queue_close($queue->id)")}
								{$this->link('#',$this->button('commit'), "queue_commit($queue->id)")}
								{$this->link('#',$this->button('delete'), "queue_delete($queue->id)")}
								{$this->link('queue_list.php?id='.$queue->id,$this->button('view'))}
						</td>
			   </tr>
__EOS__;
	}

	public function queue_domain_header() { 
		return <<< __EOS__
			<table>
			<tr>
			 <th>Date</th>
			 <th>Function</th>
			 <th>Name</th>
			 <th>Type</th>
			 <th>By</th>
			 <th>Actions</th>
			</tr>
__EOS__;
	} 

	public function queue_domain_footer() { 
		return $this->table_footer();
	} 

	public function queue_domain($queue_domain = array()) { 
		$queue_domain = (object)$queue_domain;

		return <<< __EOS__
		       <tr class="domain" id="tr_entry{$queue_domain->id}">
			       <td>$queue_domain->ch_date</td>
			       <td>$queue_domain->function</td>
			       <td>$queue_domain->name</td>
			       <td>$queue_domain->type</td>
			       <td>$queue_domain->user_id</td>
			       <td id="action_entry{$domain->id}">
				       {$this->link('#',$this->button('delete'), "queueItem_delete($queue_domain->id,'queueItem_domain_delete'); return false;")}
			       </td>
		       </tr>
__EOS__;
	} 

	public function queue_record_header() { 
		return <<< __EOS__
			<table>
			<tr>
			 <th>Date</th>
			 <th>Function</th>
			 <th>Name</th>
			 <th>Type</th>
			 <th>Content</th>
			 <th>TTL</th>
			 <th>Prio</th>
			 <th>By</th>
			 <th>Actions</th>
			</tr>
__EOS__;
	} 

	public function queue_record_footer() { 
		return $this->table_footer();
	} 

	public function queue_record($queue_record = array()) { 
		$queue_record = (object)$queue_record;

		return <<< __EOS__
		       <tr class="record" id="tr_entry{$queue_record->id}">
			       <td>$queue_record->ch_date</td>
			       <td>$queue_record->function</td>
			       <td>$queue_record->name</td>
			       <td>$queue_record->type</td>
			       <td>$queue_record->content</td>
			       <td>$queue_record->ttl</td>
			       <td>$queue_record->prio</td>
			       <td>$queue_record->user_id</td>
			       <td id="action_entry{$queue_record->id}">
				       {$this->link('#',$this->button('delete'), "queueItem_delete($queue_record->id,'queueItem_record_delete'); return false;")}
			       </td>
		       </tr>
__EOS__;
	} 
}

?>
