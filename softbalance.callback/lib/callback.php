<?php
/**
 * Created by PhpStorm.
 * User: chernenko Nikolay ( wedoca@gmail.com )
 * Date: 13.08.14
 * Time: 14:38
 */
namespace Softbalance\Callback;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class CallbackTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> CREATED datetime mandatory
 * <li> NAME string(50) mandatory
 * <li> STATUS string(20) mandatory
 * <li> PHONE string(20) mandatory
 * <li> USER_COMMENT string(500) optional
 * <li> ADMIN_COMMENT string(500) optional
 * <li> SITE_ID string(10) mandatory
 * </ul>
 *
 * @package Bitrix\Callback
 **/

class CallbackTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'softbalance_callback';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('CALLBACK_ENTITY_ID_FIELD'),
			),
			'CREATED' => array(
				'data_type' => 'datetime',
				'required' => true,
				'title' => Loc::getMessage('CALLBACK_ENTITY_CREATED_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('CALLBACK_ENTITY_NAME_FIELD'),
			),
			'STATUS' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateStatus'),
				'title' => Loc::getMessage('CALLBACK_ENTITY_STATUS_FIELD'),
			),
			'PHONE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validatePhone'),
				'title' => Loc::getMessage('CALLBACK_ENTITY_PHONE_FIELD'),
			),
			'USER_COMMENT' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateUserComment'),
				'title' => Loc::getMessage('CALLBACK_ENTITY_USER_COMMENT_FIELD'),
			),
			'ADMIN_COMMENT' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateAdminComment'),
				'title' => Loc::getMessage('CALLBACK_ENTITY_ADMIN_COMMENT_FIELD'),
			),
			'SITE_ID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('CALLBACK_ENTITY_SITE_ID_FIELD'),
			),
		);
	}
	public static function validateName()
	{
		return array(
			new Entity\Validator\Length(null, 50),
		);
	}
	public static function validateStatus()
	{
		return array(
			new Entity\Validator\Length(null, 20),
		);
	}
	public static function validatePhone()
	{
		return array(
			new Entity\Validator\Length(null, 20),
		);
	}
	public static function validateUserComment()
	{
		return array(
			new Entity\Validator\Length(null, 500),
		);
	}
	public static function validateAdminComment()
	{
		return array(
			new Entity\Validator\Length(null, 500),
		);
	}
	public static function validateSiteId()
	{
		return array(
			new Entity\Validator\Length(null, 10),
		);
	}
}