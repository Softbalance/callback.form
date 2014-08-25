<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Created by PhpStorm.
 * User: chernenko Nikolay ( wedoca@gmail.com )
 * Date: 19.08.14
 * Time: 10:51
 */
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc as Loc;

class CallbackForm extends \CBitrixComponent
{
	public function onIncludeComponentLang()
	{
		$this -> includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}

	protected function fields($arFields)
	{


	}

	protected function json_request()
	{
		if($_REQUEST["json_request_callback_form"] && $_REQUEST["json_request_callback_form"] == "Y"){
			global $APPLICATION;
			$APPLICATION->restartBuffer();

			$this->arResult = array();
			$this->arResult["error"] = array();

			if(!empty($_REQUEST["form"])){
				foreach($_REQUEST["form"] as $val){
					$this->arResult["form"][$val["name"]]=$val["value"];

					if(($val["name"] == "USER_NAME" && strlen($val["value"]) < 2) || ($val["name"] == "USER_PHONE" && strlen($val["value"] < 6))){
						$this->arResult["error"][] = array(
							"name" => $val["name"],
							"message" => GetMessage("ERROR_".$val["name"])
						);
					}else{
						$this->arResult["field"][$val["name"]] = $val["value"];
					}
				}

			}
	
			if(count($this->arResult["error"]) == 0){
				CEvent::Send("SB_CALLBACK_FORM",SITE_ID,$this->arResult["form"]);


				\Bitrix\Main\Loader::includeModule("softbalance.callback");
				$data = array(
					"CREATED" => new \Bitrix\Main\Type\DateTime(),
					"NAME" => $this->arResult["field"]["USER_NAME"],
					"STATUS" => "new",
					"PHONE" => $this->arResult["field"]["USER_PHONE"],
					"USER_COMMENT"=> $this->arResult["field"]["USER_COMMENT"],
					"ADMIN_COMMENT" => "",
					"SITE_ID" => SITE_ID
				);

				$result = \Softbalance\Callback\CallbackTable::add($data);

				if($result){
					$this->arResult["complete"]=true;
					$this->arResult["ok"]=GetMessage("ok");
				}

			}


			echo json_encode($this->arResult);
			die();
		}
	}

	public function executeComponent()
	{
		try
		{
			$this->json_request();

			$this->includeComponentTemplate();
		}
		catch(Exception $e){
			ShowError($e -> getMessage());
		}
	}
}