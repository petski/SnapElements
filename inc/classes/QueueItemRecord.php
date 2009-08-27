<?php

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'QueueItemRecordBase.php';
class QueueItemRecord extends QueueItemRecordBase {
	protected $belongs_to = array('queue');
}

?>
