<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class QueueItemRecordBase extends ActiveRecord {

  protected $columns = array('id', 'ch_date', 'commit_date', 'queue_id', 'user_id', 'function', 'domain_name', 'record_id', 'name', 'type', 'content', 'ttl', 'prio', 'change_date');
  protected $table_name = 'queue_item_records';
  protected $table_vanity_name = 'queue_item_records';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
