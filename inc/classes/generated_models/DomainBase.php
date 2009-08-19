<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class DomainBase extends ActiveRecord {

  protected $columns = array('id', 'name', 'master', 'last_check', 'type', 'notified_serial', 'account');
  protected $table_name = 'domains';
  protected $table_vanity_name = 'domains';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
