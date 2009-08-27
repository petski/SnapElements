<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class QueueBase extends ActiveRecord {

  protected $columns = array('id', 'ch_date', 'commit_date', 'domain_name', 'archived', 'closed', 'user_id', 'comment');
  protected $table_name = 'queue';
  protected $table_vanity_name = 'queue';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
