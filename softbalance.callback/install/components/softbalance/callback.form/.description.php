<?php
/**
 * Created by PhpStorm.
 * User: chernenko Nikolay ( wedoca@gmail.com )
 * Date: 19.08.14
 * Time: 10:52
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage('COMPONENT_NAME'),
	"DESCRIPTION" => Loc::getMessage('COMPONENT_DESCRIPTION'),
	"ICON" => '/images/icon.png',
	"SORT" => 10,
	"PATH" => array(
		"ID" => 'softbalance',
		"NAME" => Loc::getMessage('COMPONENT_DESCRIPTION_GROUP'),
		"SORT" => 10,
		"CHILD" => array(
			"ID" => 'sbcallback',
			"NAME" => Loc::getMessage('COMPONENT_DESCRIPTION_DIR'),
			"SORT" => 10
		)
	),
);