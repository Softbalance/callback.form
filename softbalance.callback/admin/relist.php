<?
/*
* SoftBalance: 1C Import Terms Index
*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/softbalance.excel/classes/SBMain.php');
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
if(!$USER->IsAdmin()) $APPLICATION->AuthForm();

global $DB;
IncludeModuleLangFile(__FILE__);

$APPLICATION->SetTitle(GetMessage("MODULE_NAME"));


$table = SoftBalanceExcelMain::$table;;
$sTableID = "tbl_sb_excel_profiles";
$oSort = new CAdminSorting($sTableID, "SORT", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$bReadOnly = false;
if ($lAdmin->EditAction() && !$bReadOnly){
	// foreach($_POST['FIELDS'] as $ID=>$arFields){
	// $DB->StartTransaction();
	// $ID = IntVal($ID);
	// if (!$iAdmin->IsUpdated($ID)){
	// continue;
	// }
	// if(!CSBExtraProfile::Update($ID, $arFields)){
	// if($ex = $APPLICATION->GetException()){
	// $iAdmin->AddUpdateError($ex->GetString(), $ID);
	// }else{
	// $iAdmin->AddUpdateError(GetMessage("SBP_PROFILE_UPDATE_ERROR"), $ID);
	// }
	// $DB->Rollback();
	// }
	// $DB->Commit();
	// }
}
if(($arID = $lAdmin->GroupAction()) && !$bReadOnly){
	if($_REQUEST['action_target']=='selected'){
		$arID = Array();
		$DB->StartTransaction();
		$dbResultList = $DB->Query("SELECT * FROM {$table} ORDER BY ID ASC");
		while($arR = $dbResultList->Fetch()){
			$arID[] = $arR['id'];
		}
		// $dbResultList = CSBExtraProfile::GetList(array($by=>$order), $arFilter);
		// foreach($dbResultList as $arR){
		// $arID[] = $arR["ID"];
		// }
	}
	foreach($arID as $ID){
		// $ID = IntVal($_REQUEST['ID']);
		if(strlen($ID) <= 0){
			continue;
		}
		switch ($_REQUEST['action']){
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!$resDel = $DB->Query("DELETE FROM {$table} WHERE ID = {$ID}")){
					$DB->Rollback();
				}
				$DB->Commit();
				break;
		}
	}
}

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>GetMessage("ID"), "sort"=>"ID", "default"=>true),
	array("id"=>"NAME","content"=>GetMessage("NAME"), "sort"=>"NAME", "default"=>true),
	array("id"=>"IBLOCK", "content"=>GetMessage("IBLOCK"), "sort"=>"IBLOCK", "default"=>true),
	array("id"=>"FILE", "content"=>GetMessage("FILE"), "sort"=>"FILE", "default"=>true),
	array("id"=>"SHEET", "content"=>GetMessage("SHEET"), "sort"=>"SHEET", "default"=>true),
	array("id"=>"ROW", "content"=>GetMessage("ROW"), "sort"=>"ROW", "default"=>true),
));

$getTermsList = $DB->Query("SELECT * FROM {$table} ORDER BY ID ASC");
$getTermsList->NavStart(20);
$lAdmin->NavText($getTermsList->GetNavPrint(GetMessage("RULES")));

while($arTermsItem = $getTermsList->Fetch()) {
	$row =& $lAdmin->AddRow($arTermsItem["id"],$arTermsItem,"softbalance.excel_edit.php?ID=".urlencode($arTermsItem["id"])."&lang=".LANGUAGE_ID,GetMessage("EDIT"));
	$row->AddField("ID", $arTermsItem["id"]);
	$row->AddField("NAME", $arTermsItem["name"]);
	$row->AddField("IBLOCK", $arTermsItem["iblock"]);
	$row->AddField("FILE", htmlspecialchars($arTermsItem["file"]));
	$row->AddField("SHEET", $arTermsItem["sheet"]);
	$row->AddField("ROW", $arTermsItem["row"]);
	$arActions = Array();
	$arActions[] = Array(
		"ICON" => "edit",
		"TEXT" => GetMessage("EDIT"),
		"ACTION" => $lAdmin->ActionRedirect("softbalance.excel_edit.php?ID=".urlencode($arTermsItem["id"]).'&amp;lang='.LANGUAGE_ID),
		"DEFAULT" => true,
	);
	$arActions[] = Array(
		"SEPARATOR" => true,
	);
	$arActions[] = Array(
		"ICON" => "delete",
		"TEXT" => GetMessage("DELETE"),
		"ACTION" => "if(confirm('".CUtil::JSEscape(GetMessage("DELETE_CONFIRM"))."')) ".$lAdmin->ActionDoGroup($arTermsItem["id"],"delete"),
	);
	$row->AddActions($arActions);
}


$aContext = array(array("TEXT"=>GetMessage("ADD"),"LINK"=> "softbalance.excel_edit.php", "ICON" => "btn_new"));
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");


$lAdmin->DisplayList();


require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>