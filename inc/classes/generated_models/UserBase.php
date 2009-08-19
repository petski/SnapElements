<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class UserBase extends ActiveRecord {

  protected $columns = array('id', 'username', 'password', 'fullname', 'email', 'description', 'perm_templ', 'active');
  protected $table_name = 'users';
  protected $table_vanity_name = 'users';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
