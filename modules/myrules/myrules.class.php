<?php
/**
* Мои Правила 2 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 20:02:47 [Feb 09, 2020])
*/
//
//
class myrules extends module {
/**
* myrules
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="myrules";
  $this->title="Мои Правила 2";
  $this->module_category="<#LANG_SECTION_OBJECTS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=1) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_source)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 //
if ($this->data_source=='myrules' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_myrules') {
   $this->search_myrules($out);
  }
  if ($this->view_mode=='edit_myrules') {
   $this->edit_myrules($out, $this->id);
  }
  if ($this->view_mode=='inspect_myrules') {
   $this->inspect_myrules($this->id);
   $this->redirect("?data_source=myrules");
  }
  if ($this->view_mode=='delete_myrules') {
   $this->delete_myrules($this->id);
   $this->redirect("?data_source=myrules");
  }
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='myconditions') {
  if ($this->view_mode=='' || $this->view_mode=='search_myconditions') {
   $this->search_myconditions($out);
  }
  if ($this->view_mode=='edit_myconditions') {
   $this->edit_myconditions($out, $this->id);
  }
  if ($this->view_mode=='delete_myconditions') {
   $this->delete_myconditions($this->id);
   $this->redirect("?data_source=myconditions");
  }
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='myactions') {
  if ($this->view_mode=='' || $this->view_mode=='search_myactions') {
   $this->search_myactions($out);
  }
  if ($this->view_mode=='edit_myactions') {
   $this->edit_myactions($out, $this->id);
  }
  if ($this->view_mode=='delete_myactions') {
   $this->delete_myactions($this->id);
   $this->redirect("?data_source=myactions");
  }
 }



}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* myrules search
*
* @access public
*/
 function search_myrules(&$out) {
  require(DIR_MODULES.$this->name.'/myrules_search.inc.php');
 }
/**
* myrules inspect
*
* @access public
*/
 function inspect_myrules($id) {
   //проверим и поменяем правила/условия/действия
   $rec = SQLSelectOne("SELECT * FROM myrules WHERE ID='$id'");
     //convert rule_text -> rule_txt
     $rule_str = $rec['RULE_TEXT'];//!([УСЛОВИЕ 1] AND [УСЛОВИЕ 2]) OR [COND 3]
     $rule_strx = $rule_str; //unified rule
     $re = '~(?<=\[).*?(?=\])~';
     preg_match_all($re, $rule_str, $matches);
     $total = count($matches[0]);
     if($total>0){
	     for($i=0;$i<$total;$i++) {
	      $rc = SQLSelectOne("SELECT * FROM myconditions WHERE TITLE='".$matches[0][$i]."'");
	        if($rc['ID']){
	          $rule_strx = str_replace('['.$matches[0][$i].']','@CON'.sprintf("%04d",(int)$rc['ID']).'@',$rule_strx);
	        }else{
	          //not find -> replace with 0
	          $rule_str = str_replace('['.$matches[0][$i].']',"'0'",$rule_str);
	          $rule_strx = str_replace('['.$matches[0][$i].']',"'0'",$rule_strx);
	        }
	     }
	 $rule_strx = str_replace('!',' !',$rule_strx);
	 $rule_strx = preg_replace('/ не /isu',' !',$rule_strx);
	 $rule_strx = preg_replace('/ and /isu',' && ',$rule_strx);
	 $rule_strx = preg_replace('/ и /isu',' && ',$rule_strx);
	 $rule_strx = preg_replace('/ or /isu',' || ',$rule_strx);
	 $rule_strx = preg_replace('/ или /isu',' || ',$rule_strx);
     $rule_strx = preg_replace('/\s\s+/', ' ', $rule_strx); //уберём лишние пробелы

	 $rec['RULE_TXT'] = $rule_strx;
	 $rec['RULE_TEXT'] = $rule_str;
     }
    //check action
    $rc = SQLSelectOne("SELECT * FROM myactions WHERE TITLE='".$rec['THEN_PART']."'");
    if(!$rc['ID']){
      $rec['THEN_PART'] = '';
      $rec['ACTIVE'] = 0;
    }
    //check action
    $rc = SQLSelectOne("SELECT * FROM myactions WHERE TITLE='".$rec['ELSE_PART']."'");
    if(!$rc['ID']){
      $rec['ELSE_PART'] = '';
      //$rec['ACTIVE'] = 0;
    }
/*
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
     SQLUpdate('myrules', $rec); // update
 }
/**
* myrules edit/add
*
* @access public
*/
 function edit_myrules(&$out, $id) {
  require(DIR_MODULES.$this->name.'/myrules_edit.inc.php');
 }
/**
* myrules delete record
*
* @access public
*/
 function delete_myrules($id) {
  $rec=SQLSelectOne("SELECT * FROM myrules WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM myrules WHERE ID='".$rec['ID']."'");
 }
/**
* myconditions search
*
* @access public
*/
 function search_myconditions(&$out) {
  require(DIR_MODULES.$this->name.'/myconditions_search.inc.php');
 }
/**
* myconditions edit/add
*
* @access public
*/
 function edit_myconditions(&$out, $id) {
  require(DIR_MODULES.$this->name.'/myconditions_edit.inc.php');
 }
 
 function delete_myconditions($id) {
  $rec=SQLSelectOne("SELECT * FROM myconditions WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM myconditions WHERE ID='".$rec['ID']."'");
 }
/**
* myactions search
*
* @access public
*/
 function search_myactions(&$out) {
  require(DIR_MODULES.$this->name.'/myactions_search.inc.php');
 }
/**
* myactions edit/add
*
* @access public
*/
 function edit_myactions(&$out, $id) {
  require(DIR_MODULES.$this->name.'/myactions_edit.inc.php');
 }
 function delete_myactions($id) {
  $rec=SQLSelectOne("SELECT * FROM myactions WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM myactions WHERE ID='".$rec['ID']."'");
 }


function propertySetHandle($object, $property, $value) {
   $table = 'myconditions';
   $value ="'".$value."'";
   $recs = SQLSelect("SELECT * FROM $table WHERE LINKED_OBJECT = '".DBSafe($object)."' AND LINKED_PROPERTY = '".DBSafe($property)."' AND ACTIVE=1");
   $total = count($recs);
   if ($total) {
    for($i=0;$i<$total;$i++) {
         $id     = $recs[$i]['ID'];
         $title  = $recs[$i]['TITLE'];
         $op     = $recs[$i]['OPERAND'];
         $check  = $recs[$i]['CHECK_PART'];
         $check2 = $recs[$i]['CHECK_PART2'];
         
         if (is_integer(strpos($check, "%"))) { //for  [=='%ThisComputer.timeNow%']
           $check = processTitle($check);
         }
         if (is_integer(strpos($check2, "%"))) { //for  [==processTitle('%ThisComputer.timeNow%')]
           $check2 = processTitle($check2);
         }

         //$value = str_replace("''","'",$value);
         if($op == "=" || $op == "==")
                                {$ch = $value." == ".$check;$typeOp = 0;
         }elseif($op == ">")    {$ch = "(float)".$value." > ".$check;$typeOp = 0;
         }elseif($op == "<")    {$ch = "(float)".$value." < ".$check;$typeOp = 0;
         }elseif($op == ">=")   {$ch = "(float)".$value." >= ".$check;$typeOp = 0;
         }elseif($op == "<=")   {$ch = "(float)".$value." <= ".$check;$typeOp = 0;
         }elseif($op == "betw") {$ch = "(float)".$value." >= ".$check." && (float)".$value." <= ".$check2;$typeOp = 0;
         }elseif($op == "!betw"){$ch = "(float)".$value." < ".$check." || (float)".$value." > ".$check2;$typeOp = 0;
         }elseif($op == "renew"){$ch = 'renew';$res = 1;$typeOp = 1;
         }elseif($op == "php")  {$ch = $check; $typeOp = 2;}
         //debmes('check condition ['.$ch. '] for ['.$title.']','myrules');
         if($typeOp == 0){
	         $chk = "\$res = (".$ch.")?1:0;"; //only 1/0
	         $res = 0;
	         try {
	            $success = eval($chk);
	         } catch(Exception $e){
	             DebMes('Error: exception '.get_class($e).', '.$e->getMessage().'.','myrules');
	         }
         }
         if($typeOp == 2){ //php code: if($a>$b){$res = 1;} else {$res =0;};
	         //$chk = "\$res = (".$ch.")?1:0;"; //only 1/0
	         $res = 0;
	         try {
	            $success = eval($ch);
	         } catch(Exception $e){
	             DebMes('Error: exception '.get_class($e).', '.$e->getMessage().'.','myrules');
	         }
         }
         $old_result = $recs[$i]['VALUE'];
         if($old_result<>$res || $typeOp == 1){//если значение условия не изменилось, ничего не делаем. кроме renew
           //set Condition's value and search linked Rules and calculate
           $recs[$i]['VALUE'] = $res;
           SQLUpdate($table, $recs[$i]);
           $this->findRulesByCond($title);
         }
    }
   } //no active -- exit
 }
function findRulesByCond($condition){
//найдём Правила активные с этим условием
   $table = 'myrules';
   $recs = SQLSelect("SELECT * FROM $table WHERE RULE_TEXT LIKE '%[".DBSafe($condition)."]%' AND ACTIVE=1");
   $total = count($recs);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     //debmes('findRulesByCond ['.$recs[$i]['TITLE']. ']','myrules');
     $this->processRule($recs[$i]);
    }
   }
}
function processRule($rule){
//получим значения условий, вычислим правило
     $rule_str = $rule['RULE_TXT'];
     $re = "/@[^@]*@/";
     preg_match_all($re, $rule_str, $matches);
     $total = count($matches[0]);
     if($total>0){
      for($i=0;$i<$total;$i++) {
           $rr = str_replace('@','',$matches[0][$i]);
           $rr = str_replace('CON','',$rr);
           $rc = SQLSelectOne("SELECT * FROM myconditions WHERE id=".(int)$rr);
           if(is_array($rc)){
             $rule_str = str_replace($matches[0][$i],"'".$rc['VALUE']."'",$rule_str);
           }else{
              $rule_str = str_replace($matches[0][$i],"'0'",$rule_str); //not find ->0 ((
           }
         }
      //echo($rule_str.'<br>');
      $chk = "\$res = (".$rule_str.")?1:0;"; //only 1/0
      $res = 0;
	         try {
	            $success = eval($chk);
	         } catch(Exception $e){
	             echo('Error: exception '.get_class($e).', '.$e->getMessage().'<br>');
	         }
     }
     $rule['VALUE'] = $res;
     debmes('processRule ['.$rule['TITLE']. '] ='.$res,'myrules');
     SQLUpdate('myrules', $rule);
     global $myrule_id;
     global $myrule_name;
     $myrule_id = $rule['ID'];
     $myrule_name = $rule['TITLE'];
     if($res){
      //registerEvent('MyRules/'.$rule['ID'], $details=$rule['TITLE']);
       $rc = SQLSelectOne("SELECT * FROM myactions WHERE TITLE='".$rule['THEN_PART']."'");
       if(is_array($rc)){ 
	         try {
	            $success = eval($rc['CODE']);
	         } catch(Exception $e){
	             debmes('Error: exception '.get_class($e).', '.$e->getMessage(),'myrules');
	         }
      }
     }else{
      //todo execute $rule['ELSE_PART']
       /*$rc = SQLSelectOne("SELECT * FROM myactions WHERE TITLE='".$rule['ELSE_PART']."'");
       if(is_array($rc)){ 
	         try {
	            $success = eval($rc['CODE']);
	         } catch(Exception $e){
	             debmes('Error: exception '.get_class($e).', '.$e->getMessage(),'myrules');
	         }
      }*/
     }
}

/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  $rec = SQLSELECT('Select distinct LINKED_OBJECT,LINKED_PROPERTY from myconditions');
  $total = count($rec);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     removeLinkedProperty($rec[$i]['LINKED_OBJECT'], $rec[$i]['LINKED_PROPERTY'], 'myrules');
    }
   }
  SQLExec('DROP TABLE IF EXISTS myrules');
  SQLExec('DROP TABLE IF EXISTS myconditions');
  SQLExec('DROP TABLE IF EXISTS myactions');

  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
/*
myrules - 
myconditions - 
*/
  $data = <<<EOD
 myrules: ID int(10) unsigned NOT NULL auto_increment
 myrules: TITLE varchar(100) NOT NULL DEFAULT ''
 myrules: RULE_TEXT text NOT NULL DEFAULT ''
 myrules: RULE_TXT text NOT NULL DEFAULT ''
 myrules: ACTIVE INT( 3 ) NOT NULL DEFAULT '0'
 myrules: THEN_PART varchar(255) NOT NULL DEFAULT ''
 myrules: VALUE INT( 3 ) NOT NULL DEFAULT '0'
 myrules: RESULT varchar(255) NOT NULL DEFAULT ''
 myrules: ELSE_PART varchar(255) NOT NULL DEFAULT ''
 myconditions: ID int(10) unsigned NOT NULL auto_increment
 myconditions: TITLE varchar(100) NOT NULL DEFAULT ''
 myconditions: VALUE varchar(255) NOT NULL DEFAULT ''
 myconditions: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 myconditions: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 myconditions: ACTIVE INT( 3 ) NOT NULL DEFAULT '0'
 myconditions: RESULT varchar(255) NOT NULL DEFAULT ''
 myconditions: CHECK_PART varchar(255) NOT NULL DEFAULT ''
 myconditions: CHECK_PART2 varchar(255) NOT NULL DEFAULT ''
 myconditions: OPERAND varchar(255) NOT NULL DEFAULT ''
 myconditions: USED varchar(255) NOT NULL DEFAULT ''
 myactions: ID int(10) unsigned NOT NULL auto_increment
 myactions: TITLE varchar(100) NOT NULL DEFAULT ''
 myactions: CODE text NOT NULL DEFAULT ''
 myactions: ACTIVE INT( 3 ) NOT NULL DEFAULT '0'
 myactions: USED INT( 3 ) NOT NULL DEFAULT '0'

EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgRmViIDA5LCAyMDIwIHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
