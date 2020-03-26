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
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['myconditions_qry'];
  } else {
   $session->data['myconditions_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_myconditions;
  if (!$sortby_myconditions) {
   $sortby_myconditions=$session->data['myconditions_sort'];
  } else {
   if ($session->data['myconditions_sort']==$sortby_myconditions) {
    if (Is_Integer(strpos($sortby_myconditions, ' DESC'))) {
     $sortby_myconditions=str_replace(' DESC', '', $sortby_myconditions);
    } else {
     $sortby_myconditions=$sortby_myconditions." DESC";
    }
   }
   $session->data['myconditions_sort']=$sortby_myconditions;
  }
  if (!$sortby_myconditions) $sortby_myconditions="ACTIVE, ID DESC";
  $out['SORTBY']=$sortby_myconditions;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM myconditions WHERE $qry ORDER BY ".$sortby_myconditions);
  if ($res[0]['ID']) {
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    //$using=SQLSelectOne("SELECT ID FROM rules_linked_conditions WHERE CONDITION_ID='".$res[$i]['ID']."'");
    //if (!$using['ID']) {
     //$res[$i]['CAN_DELETE']=1;
    //}
   }
   $out['RESULT']=$res;
  }
