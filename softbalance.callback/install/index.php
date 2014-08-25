<?php
/**
 * Created by PhpStorm.
 * User: chernenko Nikolay ( wedoca@gmail.com )
 * Date: 13.08.14
 * Time: 13:24
 */
IncludeModuleLangFile(__FILE__);

if(class_exists("softbalance_callback"))
	return;

class softbalance_callback extends CModule
{
	var $MODULE_ID = "softbalance.callback";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_JS;

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("SB_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SB_INSTALL_DESCRIPTION");

		$this->PARTNER_NAME = GetMessage("SB_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("SB_PARTNER_URI");

		$this->EVENT_NAME = "SB_CALLBACK_FORM";
	}

	function InstallDB()
	{
		global $DB;
		$DB->RunSQLBatch(dirname(__FILE__)."/sql/install.sql");
		RegisterModule("softbalance.callback");
		RegisterModuleDependences("main", "OnBeforeEndBufferContent", "softbalance.callback", "\\Softbalance\\Callback\\CallbackTable", "OnBeforeEndBufferContent");
		return true;
	}

	function UnInstallDB()
	{
		global $DB;

		$DB->RunSQLBatch(dirname(__FILE__)."/sql/uninstall.sql");
		UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", "softbalance.callback", "\\Softbalance\\Callback\\CallbackTable", "OnBeforeEndBufferContent");
		UnRegisterModule("softbalance.callback");
		return true;
	}

	function InstallEvent(){
		//Создаем тип почтового события
		$fields = "\n#USER_NAME# имя отправителя\n#USER_PHONE# телефон отправителя\n#USER_COMMENT# комментарий отправителя";

		$et = new CEventType;
		$et->Add(array(
			"LID"           => "ru",
			"EVENT_NAME"    => $this->EVENT_NAME,
			"NAME"          => "Заказ звонка с сайта",
			"DESCRIPTION"   => $fields
		));

		//формируем массив из идентификаторов сайтов
		$arSite = array();
		$dbSites = CSite::GetList(($b = ""), ($o = ""), Array("ACTIVE" => "Y"));
		while($site = $dbSites->Fetch())
		{
			$arSite[] = $site["LID"];
		}

		//создаем почтовый шаблон для всех сайтов
		$arr = array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => $this->EVENT_NAME,
			"LID" => $arSite,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
			"BCC" => "#BCC#",
			"SUBJECT" => GetMessage("EVENT_SUBJECT"),
			"BODY_TYPE" => "text",
			"MESSAGE" => Getmessage("EVENT_MESSAGE")
		);

		$emess = new CEventMessage;
		$emess->Add($arr);
	}

	function UnInstallEvent(){
		global $DB;

		//Удаляем тип почтового события
		$et = new CEventType;
		$et->Delete($this->EVENT_NAME);

		//Находим все почтовые шаблоные которые были привязаны к нашему типу
		$DB->StartTransaction();
		$emessage = new CEventMessage;
		$rsMess = CEventMessage::GetList($by="site_id", $order="desc", array("TYPE_ID"=>$this->EVENT_NAME));

		//рекурсивно по одному удаляем найденные шаблоны
		while($events = $rsMess->GetNext()){
			$emessage->Delete(intval($events["ID"]));
			$DB->Commit();
		};
	}

	function InstallFiles()
	{
		CopyDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		CopyDirFiles(dirname(__FILE__)."/components", $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

		return true;
	}

	function DoInstall()
	{
		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallEvent();
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvent();
	}
}