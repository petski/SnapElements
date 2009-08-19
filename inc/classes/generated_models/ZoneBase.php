<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class ZoneBase extends ActiveRecord {

  protected $columns = array('id', 'domain_id', 'owner', 'comment');
  protected $table_name = 'zones';
  protected $table_vanity_name = 'zones';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
