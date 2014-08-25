<?
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/include.php"); // инициализация модуля
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/prolog.php"); // пролог модуля
//CModule::IncludeModule("softbalance.callback");
\Bitrix\Main\Loader::includeModule("softbalance.callback");


// подключим языковой файл
IncludeModuleLangFile(__FILE__);

// сформируем список закладок
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("TAB_NAME"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("TAB_NAME_TITLE"))
);

$tabControl = new CAdminForm("table_calls", $aTabs);

$ID = intval($_REQUEST["ID"]);		// идентификатор редактируемой записи
$message = null;		// сообщение об ошибке
$bVarsFromForm = false; // флаг "Данные получены с формы", обозначающий, что выводимые данные получены с формы, а не из БД.


// ******************************************************************** //
//                ОБРАБОТКА ИЗМЕНЕНИЙ ФОРМЫ                             //
// ******************************************************************** //
//echo "<pre>";print_r($_REQUEST);echo "</pre>";
if($REQUEST_METHOD == "POST" &&	($_REQUEST["save"]!="" || $_REQUEST["apply"]!="") && check_bitrix_sessid())
{

	if($ID > 0)
	{
		$arFields = Array(
			"NAME"    => $_REQUEST["NAME"],
			"USER_COMMENT"  =>$_REQUEST["USER_COMMENT"],
			"ADMIN_COMMENT"  =>$_REQUEST["ADMIN_COMMENT"],
			"PHONE" => $_REQUEST["PHONE"],
			"STATUS" => $_REQUEST["STATUS"],
			"SITE_ID" => "s1"
		);
		$result = \Softbalance\Callback\CallbackTable::update($ID,$arFields);
	}
	else
	{
		$arFields = Array(
			"CREATED" =>new \Bitrix\Main\Type\DateTime(),
			"NAME"    => $_REQUEST["NAME"],
			"USER_COMMENT"  =>$_REQUEST["USER_COMMENT"],
			"ADMIN_COMMENT"  =>$_REQUEST["ADMIN_COMMENT"],
			"PHONE" => $_REQUEST["PHONE"],
			"STATUS" => $_REQUEST["STATUS"],
			"SITE_ID" => "s1"
		);
		$result = \Softbalance\Callback\CallbackTable::add($arFields);
		$ID = $result->getId();
	}

	if($result->isSuccess())
	{
		if($request["save"] <> '')
			LocalRedirect(BX_ROOT."/admin/softbalance_callback.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect(BX_ROOT."/admin/softbalance_callback_edit.php?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
	}
	else
	{
		$message = $result->getErrorMessages();
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete' && check_bitrix_sessid())
{
	\Softbalance\Callback\CallbackTable::delete($ID);
	LocalRedirect("/bitrix/admin/softbalance_callback.php?lang=".LANG);
}
// ******************************************************************** //
//                ВЫБОРКА И ПОДГОТОВКА ДАННЫХ ФОРМЫ                     //
// ******************************************************************** //

// выборка данных
if($ID>0){
	$block = Softbalance\Callback\CallbackTable::getById($ID)->fetch();
	if(!$block)
		$ID=0;
}

// дополнительная подготовка данных
if($ID>0 && !$message)
	$DAYS_OF_WEEK = explode(",", $str_DAYS_OF_WEEK);
if(!is_array($DAYS_OF_WEEK))
	$DAYS_OF_WEEK = array();

// если данные переданы из формы, инициализируем их
if($bVarsFromForm)
	$DB->InitTableVarsForEdit("b_list_rubric", "", "str_");

// ******************************************************************** //
//                ВЫВОД ФОРМЫ                                           //
// ******************************************************************** //

// установим заголовок страницы
$APPLICATION->SetTitle(($ID>0 ? GetMessage("EDIT_ELEMENT",array("#ID#"=>$ID)) : GetMessage("ADD_ELEMENT")));

// не забудем разделить подготовку данных и вывод
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// конфигурация административного меню
$aMenu = array(
	array(
		"TEXT"=>GetMessage("CALL_LIST"),
		"TITLE"=>GetMessage("CALL_LIST_TITLE"),
		"LINK"=>"softbalance_callback.php?lang=".LANG,
		"ICON"=>"btn_list",
	)
);
if($ID>0)
{
	$aMenu[] = array(
		"TEXT"=>GetMessage("CALL_ADD"),
		"TITLE"=>GetMessage("CALL_ADD_TITLE"),
		"LINK"=>"softbalance_callback_edit.php?lang=".LANG,
		"ICON"=>"btn_new",
	);
	$aMenu[] = array(
		"TEXT"=>GetMessage("CALL_DELETE"),
		"TITLE"=>GetMessage("CALL_DELETE_TITLE"),
		"LINK"=>"javascript:if(confirm('".GetMessage("rubric_mnu_del_conf")."'))window.location='softbalance_callback_edit.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
		"ICON"=>"btn_delete",
	);
}
// создание экземпляра класса административного меню
$context = new CAdminContextMenu($aMenu);
// вывод административного меню
$context->Show();
// если есть сообщения об ошибках или об успешном сохранении - выведем их.
if($_REQUEST["mess"] == "ok" && $ID>0)
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("CALL_SAVED"), "TYPE"=>"OK"));

if($message)
	echo $message->Show();
elseif($rubric->LAST_ERROR!="")
	CAdminMessage::ShowMessage($rubric->LAST_ERROR);
?>

<?
// далее выводим собственно форму
?>
<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
	<?=bitrix_sessid_post();?>
	<?
	$tabControl->BeginPrologContent();
	$tabControl->EndPrologContent();
	$tabControl->BeginEpilogContent();
	?>
	<input type="hidden" name="ID" value="<?=htmlspecialcharsbx($block['ID'])?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<?
	$tabControl->EndEpilogContent();
	$tabControl->Begin();


//********************
// первая закладка - форма редактирования параметров рассылки
//********************
$tabControl->BeginNextFormTab();

$tabControl->AddEditField("NAME", GetMessage("USER_NAME").":", true, array("size" => 63, "maxlength" => 255), $block["NAME"]);
$tabControl->AddEditField("PHONE", GetMessage("USER_PHONE").":", true, array("size" => 63, "maxlength" => 255), $block["PHONE"]);

$status = array(
	"new" => GetMessage("STATUS_NEW"),
	"dialing" => GetMessage("STATUS_DIALING"),
	"completed" => GetMessage("STATUS_COMPLETED"),
);

$tabControl->BeginCustomField("field");
?>
	<tr>
		<td><?=GetMessage("USER_COMMENT")?></td>
		<td><textarea name="USER_COMMENT" cols="65" rows="5" wrap="VIRTUAL"><?=$block["USER_COMMENT"]?></textarea></td>
	</tr>
	<tr>
		<td><?=GetMessage("ADMIN_COMMENT")?></td>
		<td><textarea name="ADMIN_COMMENT" cols="65" rows="5" wrap="VIRTUAL"><?=$block["ADMIN_COMMENT"]?></textarea></td>
	</tr>
	<tr>
		<td><?=GetMessage("CALL_STATUS")?></td>
		<td>
			<select name="STATUS">
				<?foreach($status as $key=>$value):?>
					<option value="<?=$key?>"<?if($block["STATUS"]==$key):?> selected<?endif;?>><?=$value?></option>
				<?endforeach;?>
			</select>
		</td>
	</tr>
<?
$tabControl->EndCustomField("field");

$tabControl->Buttons(
	array(
		//"disabled"=>($POST_RIGHT<"W"),
		"back_url"=>"softbalance_callback.php?lang=".LANG,
	)
);

$tabControl->Show();

// дополнительное уведомление об ошибках - вывод иконки около поля, в котором возникла ошибка
$tabControl->ShowWarnings("post_form", $message);
?>

<?=BeginNote();?>
	<span class="required">*</span><?echo GetMessage("REQUIRED_FIELDS")?>
<?=EndNote();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>