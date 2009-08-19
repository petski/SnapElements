<?php
require_once('base.php');

print $display->header();

print '<ul>';
foreach($display->pages as $url => $props) { 
	if($props['menu']) 
		print '<li><a href="'.$url.'">'.$props['name'].'</a></li>'."\n";
} 
print '</ul>';

print $display->footer();
?>
