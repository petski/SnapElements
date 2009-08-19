<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Association.php';
class HasOne extends Association {
  function __construct(&$source, $dest, $options=null) {
    parent::__construct($source, $dest, $options);
    $this->foreign_key = Inflector::foreign_key($this->source_class);
  }

  function set($value, &$source) {
    if ($value instanceof $this->dest_class) {
      if (!$source->is_new_record()) {
        $value->{$this->foreign_key} = $source->{$source->get_primary_key()};
        $value->save();
      }
      else {
        $value->{$this->foreign_key} = null;
      }
      $this->value = $value;
    }
    else {
      throw new ActiveRecordException("Did not get expected class: {$this->dest_class}", ActiveRecordException::UnexpectedClass);
    }
  }

  function get(&$source, $force=false) {
    if (!($this->value instanceof $this->dest_class) || $force) {
      if ($source->is_new_record()) { return null; }
      $this->value = call_user_func_array(
          array($this->dest_class, 'find'),
          array('first',
            array('conditions' => "{$this->foreign_key} = {$source->{$source->get_primary_key()}}")
          ));
    }
    return $this->value;
  }

  function join() {
    $dest_table = Inflector::tableize($this->dest_class);
    $source_table = Inflector::tableize($this->source_class);
    $source_inst = new $this->source_class;
    $dest_inst = new $this->dest_class;
    $columns = $dest_inst->get_columns();
    $join = "LEFT OUTER JOIN {$dest_table} ON "
          . "$source_table.".$source_inst->get_primary_key() ." = $dest_table.{$this->foreign_key}";
    return array( array($dest_table => $columns), $join);
  }
  function populate_from_find($attributes) {
    // check if all attributes are NULL
    $uniq_vals = array_unique(array_values($attributes));
    if (count($uniq_vals) == 1 && is_null(current($uniq_vals))) return;

    $class = $this->dest_class;
    $item = new $class($attributes);
    $item->new_record = false;
    $this->value = $item;
  }

}
?>
