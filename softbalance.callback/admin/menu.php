<?php
/**
 * Created by PhpStorm.
 * User: chernenko Nikolay ( wedoca@gmail.com )
 * Date: 13.08.14
 * Time: 13:50
 */
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("softbalance_callback") >= "R")
{
	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"section" => "softbalance_callback",
		"sort" => 550,
		"text" => GetMessage("TEXT"),
		"title"=> GetMessage("TITLE"),
		"icon" => "default",
		"page_icon" => "default",
		"items_id" => "menu_blog",
		"items" => array(
			array(
				"text" => GetMessage("CALLBACK_LIST"),
				"url" => "softbalance_callback.php?lang=".LANGUAGE_ID,
				"more_url" => array(),
				"title" => GetMessage("CALLBACK_LIST_TITLE")
			)
		)
	);

	return $aMenu;
}
return false;
?>