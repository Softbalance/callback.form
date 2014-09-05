<?php
/**
 * Created by PhpStorm.
 * User: chernenko Nikolay ( wedoca@gmail.com )
 * Date: 13.08.14
 * Time: 15:55
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

CModule::IncludeModule("softbalance.callback");
IncludeModuleLangFile(__FILE__);
$lAdmin = new CAdminList($sTableID, $oSort);
$listTableId = "tbl_softbalance_callback_list";

$oSort = new CAdminSorting($listTableId, "ID", "asc");
$arOrder = (strtoupper($by) === "ID"? array($by => $order): array($by => $order, "ID" => "ASC"));

$lAdmin = new CAdminList($listTableId, $oSort);

$arFilterFields = array(
	"find_created_from",
	"find_created_to",
	"find_user_id"
);

$lAdmin->InitFilter($arFilterFields);


//echo "<pre>";
//	print_r($_REQUEST);
//	print_r(check_bitrix_sessid());
//echo "</pre>";

// Processing with actions
if($lAdmin->EditAction()) {
	foreach($FIELDS as $ID=>$arFields) {
		if(!$lAdmin->IsUpdated($ID)) continue;
		$ID = IntVal($ID);

		if($_REQUEST["cancel"] == "Y") break;

		if(isset($_REQUEST["save"]) && strlen($_REQUEST["save"])>0){
			$result = \Softbalance\Callback\CallbackTable::update($ID,$arFields);

			if($result->isSuccess())
			{
				$lAdmin->AddGroupError(GetMessage("rub_save_error"), $ID);
			}
			else
			{
				$message = $result->getErrorMessages();
				$lAdmin->AddGroupError($result->getErrorMessages(), $ID);
			}
		}

	}
}

if($arID = $lAdmin->GroupAction()){
	foreach($arID as $ID) {
		if (isset($_REQUEST['action']) && $_REQUEST['action_button'] === 'delete' && check_bitrix_sessid())
		{
			\Softbalance\Callback\CallbackTable::delete($ID);
		}
	}
};




$arFilter = array();

//if (!empty($find_user_id))
//	$arFilter["USER_ID"] = $find_user_id;
if (!empty($find_created_from))
	$arFilter[">=CREATED"] = $find_created_from;
if (!empty($find_created_to))
	$arFilter["<=CREATED"] = $find_created_to;

$myData = \Softbalance\Callback\CallbackTable::getList(
	array(
		'filter' => $arFilter,
		'order' => $arOrder
	)
);

$myData = new CAdminResult($myData, $listTableId);
$myData->NavStart();

$lAdmin->NavText($myData->GetNavPrint(GetMessage("MY_STAT_ADMIN_NAV")));

$cols = \Softbalance\Callback\CallbackTable::getMap();
$colHeaders = array();
foreach ($cols as $colId => $col)
{
	$colHeaders[] = array(
		"id" => $colId,
		"content" => $col["title"],
		"sort" => $colId,
		"default" => true,
	);
}
$lAdmin->AddHeaders($colHeaders);






$visibleHeaderColumns = $lAdmin->GetVisibleHeaderColumns();
$arUsersCache = array();


$status = array(
	"new" => GetMessage("SB_CALLBACK_STATUS_NEW"),
	"dialing" => GetMessage("SB_CALLBACK_STATUS_DIALING"),
	"completed" => GetMessage("SB_CALLBACK_STATUS_COMPLETED"),
);

while ($arRes = $myData->GetNext())
{
	$arRes["STATUS"] = $status[$arRes["STATUS"]];

	$row =& $lAdmin->AddRow($arRes["ID"], $arRes);


	$StatusHTML = '<select name="FIELDS['.$arRes["ID"].'][STATUS]">';
	foreach($status as $key=>$value){
		$selected = "";
		if($key == $arRes["~STATUS"])
			$selected = "selected";

		$StatusHTML .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
	}
	$StatusHTML .= '</select>';


	$row->AddEditField("STATUS", $StatusHTML);

	$row->AddViewField("ID", $arRes["ID"]);

	$row->AddInputField("NAME",array("SIZE" => "30"));

	$row->AddViewField("CREATED", CDatabase::FormatDate($f_DATETIME, "YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL")));

	$row->AddInputField("PHONE",array("SIZE" => "30"));

	$sHTML = "<textarea cols='30' rows='4'name='FIELDS[".$arRes["ID"]."][USER_COMMENT]'>".$arRes["USER_COMMENT"]."</textarea>";
	$row->AddEditField("USER_COMMENT", $sHTML);

	$sHTML = "<textarea cols='30' rows='4'name='FIELDS[".$arRes["ID"]."][ADMIN_COMMENT]'>".$arRes["ADMIN_COMMENT"]."</textarea>";
	$row->AddEditField("ADMIN_COMMENT", $sHTML);

	//echo "<pre style='display: none'>";
	//print_r($arRes);
	//echo "</pre>";
	$arActions = array();
	$arActions[] = array(
		"ICON" => "edit",
		"TEXT" => GetMessage("EDIT"),
		"ACTION" => $lAdmin->ActionRedirect("softbalance_callback_edit.php?ID=".urlencode($arRes["ID"]).'&amp;lang='.LANGUAGE_ID),
		"DEFAULT" => true
	);
	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => GetMessage("DELETE"),
		"ACTION" => "if(confirm('".GetMessageJS("DELETE_CONF")."')) ".$lAdmin->ActionDoGroup($arRes["ID"], "delete"),
	);
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $myData->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

$lAdmin->AddGroupActionTable(Array(
	"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
	//"set_y" => GetMessage("WEBDEBUG_CALLME_ADMINLIST_ACTION_SET_Y"),
	//"set_n" => GetMessage("WEBDEBUG_CALLME_ADMINLIST_ACTION_SET_N"),
));

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("MY_STAT_ADMIN_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
	<form name="filter_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?
		$oFilter = new CAdminFilter(
			$listTableId."_filter",
			array(
				GetMessage("MY_STAT_ADMIN_FILTER_USER_ID")
			)
		);

		$oFilter->Begin();
		?>
		<tr>
			<td><b><?=GetMessage("MY_STAT_ADMIN_FILTER_CREATED")?>:</b></td>
			<td nowrap>
				<?=CalendarPeriod("find_created_from", htmlspecialcharsex($find_created_from), "find_created_to", htmlspecialcharsex($find_created_to), "filter_form")?>
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("MY_STAT_ADMIN_FILTER_USER_ID")?>:</td>
			<td><?=FindUserID("find_user_id", $find_user_id, "", "filter_form", "5", "", " ... ", "", "");?></td>
		</tr>
		<?
		$oFilter->Buttons(
			array(
				"table_id" => $listTableId,
				"url" => $APPLICATION->GetCurPage(),
				"form" => "filter_form"
			)
		);
		$oFilter->End();
		?>
	</form>
<?
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>