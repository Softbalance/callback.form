<?
/**
 * Created by PhpStorm.
 * User: chernenko Nikolay ( wedoca@gmail.com )
 * Date: 19.08.14
 * Time: 11:06
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<a id="callbackLink"><?=GetMessage("START_BUTTON")?></a>
<form id="callbackForm">
	<h2><?=GetMessage("TITLE")?></h2>
	<p><?=GetMessage("DESCRIPTION")?></p>
	<div></div>
	<input type="text" name="USER_NAME" placeholder="<?=GetMessage("YOUR_NAME")?>">
	<input type="text" name="USER_PHONE" placeholder="<?=GetMessage("YOUR_PHONE")?>">
	<textarea name="USER_COMMENT" placeholder="<?=GetMessage("YOUR_COMMENT")?>"></textarea>
	<input type="submit" value="<?=GetMessage("SEND")?>">
	<a class="close" title="<?=GetMessage("CLOSE")?>">x</a>
</form>