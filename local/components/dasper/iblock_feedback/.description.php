<?php

	use Bitrix\Main\Localization\Loc;

	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	Loc::loadMessages(__FILE__);

	$arComponentDescription = array(
		"NAME" => Loc::getMessage('NAME'),
		"DESCRIPTION" => Loc::getMessage('DESCRIPTION'),
		"PATH" => array(
			"ID" => "dasper",
			"NAME" => "dAspeR Components",
		),
	);
?>