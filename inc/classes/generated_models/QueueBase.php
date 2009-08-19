<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class QueueBase extends ActiveRecord {

  protected $columns = array('id', 'change_date', 'commit_date', 'archived', 'user_id', 'user_name', 'function', 'change');
  protected $table_name = 'queue';
  protected $table_vanity_name = 'queue';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
