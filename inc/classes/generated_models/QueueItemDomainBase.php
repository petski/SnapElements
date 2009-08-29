<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class QueueItemDomainBase extends ActiveRecord {

  protected $columns = array('id', 'ch_date', 'commit_date', 'queue_id', 'user_id', 'function', 'name', 'master', 'last_check', 'type', 'notified_serial', 'account');
  protected $table_name = 'queue_item_domains';
  protected $table_vanity_name = 'queue_item_domains';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
