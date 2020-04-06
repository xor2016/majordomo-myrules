<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='myconditions';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  global $old_linked_object;
  global $old_linked_property;
  $old_linked_object = $rec['LINKED_OBJECT'];
  $old_linked_property = $rec['LINKED_PROPERTY'];

  if ($this->mode=='update') {
   $ok=1;
  //updating 'TITLE' (varchar, required)
   global $title;
   $rec['TITLE']=$title;
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  //updating 'ACTIVE' (select)
   global $active;
   $rec['ACTIVE']=$active;
  //updating 'LINKED_OBJECT' (varchar)
   global $linked_object;
   $rec['LINKED_OBJECT']=$linked_object;
  //updating 'LINKED_PROPERTY' (varchar)
   global $linked_property;
   $rec['LINKED_PROPERTY']=$linked_property;

   global $operand;
   $rec['OPERAND']=$operand;

  //updating 'CHECK_PART' 
   global $check_part;
   $rec['CHECK_PART']=$check_part;
  //updating 'RESULT'
   global $value;
   if($value<>'1')$value = '0'; //умолчание
   $rec['VALUE']=$value;
  //updating 'CHECK_PART2' (text)
   global $check_part2;
   $rec['CHECK_PART2']=$check_part2;
  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }

    $out['OK']=1;
    if($rec['LINKED_OBJECT'].'.'.$rec['LINKED_PROPERTY']<>$old_linked_object.'.'.$old_linked_property){
      //clean old link
      removeLinkedProperty($old_linked_object, $old_linked_property, $this->name);
    }

    if ($rec['LINKED_OBJECT'] && $rec['LINKED_PROPERTY']) {
     addLinkedProperty($rec['LINKED_OBJECT'], $rec['LINKED_PROPERTY'], $this->name);
    }

   } else {
    $out['ERR']=1;
   }
  }
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
