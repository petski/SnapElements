<?php

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'QueueItemDomainBase.php';
class QueueItemDomain extends QueueItemDomainBase {
	protected $belongs_to = array('queue');
}

?>
