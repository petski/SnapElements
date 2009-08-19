<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class RecordBase extends ActiveRecord {

  protected $columns = array('id', 'domain_id', 'name', 'type', 'content', 'ttl', 'prio', 'change_date');
  protected $table_name = 'records';
  protected $table_vanity_name = 'records';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
