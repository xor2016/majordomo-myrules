<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $out['ALL_ACTIONS']=SQLSelect("SELECT * FROM myactions ORDER BY TITLE");
  $out['ALL_CONDITIONS']=SQLSelect("SELECT * FROM myconditions ORDER BY TITLE");

  $table_name='myrules';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='".$id."'");
  if ($this->mode=='update') {
   $ok=1;
  // step: default
  if ($this->tab=='') {
  //updating '<%LANG_TITLE%>' (varchar, required)
   $rec['TITLE']=gr('title');
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  //updating 'TITLE' (varchar)
   $rec['TITLE']=gr('title');
  //updating 'RULE_TEXT' (varchar)
   $rec['RULE_TEXT']=gr('rule_text');
  //updating 'ACTIVE' (varchar)
   $rec['ACTIVE']=gr('active');
  //updating 'THEN_PART' (varchar)
   $rec['THEN_PART']=gr('then_part');
  //updating 'RESULT' (varchar)
   $rec['VALUE']=gr('value');
  //updating 'ELSE_PART' (varchar)
   $rec['ELSE_PART']=gr('else_part');

  }

  // step: data
  if ($this->tab=='data') {
  }
  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
/*
     //convert rule_text -> rule_txt
     $rule_str = $rec['RULE_TEXT'];//!([РЈРЎР›РћР’РР• 1] AND [РЈРЎР›РћР’РР• 2]) OR [COND 3]
     $re = '~(?<=\[).*?(?=\])~';
     preg_match_all($re, $rule_str, $matches);
     $total = count($matches[0]);
     if($total>0){
	     for($i=0;$i<$total;$i++) {
	      $rc = SQLSelectOne("SELECT * FROM myconditions WHERE TITLE='".$matches[0][$i]."'");
	        if($rc['ID']){
	          $rule_str = str_replace('['.$matches[0][$i].']','@CON'.sprintf("%04d",(int)$rc['ID']).'@',$rule_str);
	        }else{
	          //not find -> replace with 0
	          $rule_str = str_replace('['.$matches[0][$i].']',"'0'",$rule_str);
	        }
	     }
	 $rule_str = str_replace('  ',' ',$rule_str);
	 $rec['RULE_TXT'] = $rule_str;
     }
     //convert rule_txt -> rule_text
     $rule_str = $rec['RULE_TXT'];
     $re = "/@[^@]*@/";
     preg_match_all($re, $rule_str, $matches);
     $total = count($matches[0]);
     if($total>0){
      for($i=0;$i<$total;$i++) {
           $rr = str_replace('@','',$matches[0][$i]);
           $rr = str_replace('CON','',$rr);
           $rc = SQLSelectOne("SELECT * FROM myconditions WHERE id=".(int)$rr);
           $rule_str = str_replace($matches[0][$i],'['.$rc['TITLE'].']',$rule_str);
         }
      $rec['RULE_TEXT'] = $rule_str;
     }
*/
     //$rec['RULE_TXT'] = $rule_str; //temporary
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    $this->inspect_myrules($rec['ID']);
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }
  // step: default
  if ($this->tab=='') {
  }
  // step: data
 /* if ($this->tab=='data') {
   //dataset2
   $new_id=0;
   if ($this->mode=='update') {
    global $title_new;
	if ($title_new) {
	 $prop=array('TITLE'=>$title_new,'DEVICE_ID'=>$rec['ID']);
	 $new_id=SQLInsert('myconditions',$prop);
	}
   }
   global $delete_id;
   if ($delete_id) {
    SQLExec("DELETE FROM myconditions WHERE ID='".(int)$delete_id."'");
   }
   $properties=SQLSelect("SELECT * FROM myconditions WHERE DEVICE_ID='".$rec['ID']."' ORDER BY ID");
   $total=count($properties);
   for($i=0;$i<$total;$i++) {
    if ($properties[$i]['ID']==$new_id) continue;
    if ($this->mode=='update') {
      global ${'title'.$properties[$i]['ID']};
      $properties[$i]['TITLE']=trim(${'title'.$properties[$i]['ID']});
      global ${'value'.$properties[$i]['ID']};
      $properties[$i]['VALUE']=trim(${'value'.$properties[$i]['ID']});
      global ${'linked_object'.$properties[$i]['ID']};
      $properties[$i]['LINKED_OBJECT']=trim(${'linked_object'.$properties[$i]['ID']});
      global ${'linked_property'.$properties[$i]['ID']};
      $properties[$i]['LINKED_PROPERTY']=trim(${'linked_property'.$properties[$i]['ID']});
      SQLUpdate('myconditions', $properties[$i]);
      $old_linked_object=$properties[$i]['LINKED_OBJECT'];
      $old_linked_property=$properties[$i]['LINKED_PROPERTY'];
      if ($old_linked_object && $old_linked_object!=$properties[$i]['LINKED_OBJECT'] && $old_linked_property && $old_linked_property!=$properties[$i]['LINKED_PROPERTY']) {
       removeLinkedProperty($old_linked_object, $old_linked_property, $this->name);
      }
      if ($properties[$i]['LINKED_OBJECT'] && $properties[$i]['LINKED_PROPERTY']) {
       addLinkedProperty($properties[$i]['LINKED_OBJECT'], $properties[$i]['LINKED_PROPERTY'], $this->name);
      }
     }
   }
   $out['PROPERTIES']=$properties;   
  }
*/
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);

