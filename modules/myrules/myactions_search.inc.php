<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  //searching 'TITLE' (varchar)
  global $title;
  if ($title!='') {
   $qry.=" AND TITLE LIKE '%".DBSafe($title)."%'";
   $out['TITLE']=$title;
  }
  if (IsSet($this->script_id)) {
   $script_id=$this->script_id;
   $qry.=" AND SCRIPT_ID='".$this->script_id."'";
  } else {
   global $script_id;
  }
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['myactions_qry'];
  } else {
   $session->data['myactions_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_myactions;
  if (!$sortby_myactions) {
   $sortby_myactions=$session->data['myactions_sort'];
  } else {
   if ($session->data['myactions_sort']==$sortby_myactions) {
    if (Is_Integer(strpos($sortby_myactions, ' DESC'))) {
     $sortby_myactions=str_replace(' DESC', '', $sortby_myactions);
    } else {
     $sortby_myactions=$sortby_myactions." DESC";
    }
   }
   $session->data['myactions_sort']=$sortby_myactions;
  }
  if (!$sortby_myactions) $sortby_myactions="ACTIVE, ID DESC";
  $out['SORTBY']=$sortby_myactions;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM myactions WHERE $qry ORDER BY ".$sortby_myactions);
  if ($res[0]['ID']) {
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    //$using=SQLSelectOne("SELECT ID FROM rules_linked_actions WHERE ACTION_ID='".$res[$i]['ID']."'");
    //if (!$using['ID']) {
    // $res[$i]['CAN_DELETE']=1;
    //}
   }
   $out['RESULT']=$res;
  }
