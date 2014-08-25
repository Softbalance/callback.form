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

$adminList = new CAdminList($listTableId, $oSort);

$arFilterFields = array(
	"find_created_from",
	"find_created_to",
	"find_user_id"
);

$adminList->InitFilter($arFilterFields);

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

$adminList->NavText($myData->GetNavPrint(GetMessage("MY_STAT_ADMIN_NAV")));

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
$adminList->AddHeaders($colHeaders);

$visibleHeaderColumns = $adminList->GetVisibleHeaderColumns();
$arUsersCache = array();

while ($arRes = $myData->GetNext())
{
	$row =& $adminList->AddRow($arRes["ID"], $arRes);

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
		"ACTION" => "if(confirm('".GetMessageJS("DELETE_CONF")."')) ".$adminList->ActionDoGroup($arRes["ID"], "delete"),
	);
	$row->AddActions($arActions);
}

$adminList->AddFooter(
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

$adminList->CheckListMode();

$APPLICATION->SetTitle(GetMessage("MY_STAT_ADMIN_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
	<form name="filter_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
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
			<td><b><?echo GetMessage("MY_STAT_ADMIN_FILTER_CREATED")?>:</b></td>
			<td nowrap>
				<?echo CalendarPeriod("find_created_from", htmlspecialcharsex($find_created_from), "find_created_to", htmlspecialcharsex($find_created_to), "filter_form")?>
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("MY_STAT_ADMIN_FILTER_USER_ID")?>:</td>
			<td><?echo FindUserID("find_user_id", $find_user_id, "", "filter_form", "5", "", " ... ", "", "");?></td>
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
$adminList->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>