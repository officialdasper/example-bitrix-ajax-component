<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	/** @var array $arCurrentValues */

	use Bitrix\Main\Loader;

	if(!Loader::includeModule('iblock')) {
		return;
	}

	$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);

	$arTypes = CIBlockParameters::GetIBlockTypes();

	$arIBlocks = [];
	$iblockFilter = [
		'ACTIVE' => 'Y',
	];
	if(!empty($arCurrentValues['IBLOCK_TYPE'])) {
		$iblockFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];
	}
	if(isset($_REQUEST['site'])) {
		$iblockFilter['SITE_ID'] = $_REQUEST['site'];
	}
	$db_iblock = CIBlock::GetList(["SORT" => "ASC"], $iblockFilter);
	while($arRes = $db_iblock->Fetch()) {
		$arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
	}

	$arProperty_LNS = [];
	$arProperty = [];
	if($iblockExists) {
		$rsProp = CIBlockProperty::GetList([
			"SORT" => "ASC",
			"NAME" => "ASC",
		], [
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"],
		]);
		while($arr = $rsProp->Fetch()) {
			$arProperty[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
			if(in_array($arr["PROPERTY_TYPE"], ["L", "N", "S"])) {
				$arProperty_LNS[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
			}
		}
	}

	$arUGroupsEx = [];
	$dbUGroups = CGroup::GetList();
	while($arUGroups = $dbUGroups->Fetch()) {
		$arUGroupsEx[$arUGroups["ID"]] = $arUGroups["NAME"];
	}

	$arComponentParameters = [
		"PARAMETERS" => [
			"AJAX_MODE" => [],
			"IBLOCK_TYPE" => [
				"PARENT" => "BASE",
				"NAME" => "Тип инфоблока",
				"TYPE" => "LIST",
				"VALUES" => $arTypes,
				"DEFAULT" => "news",
				"REFRESH" => "Y",
			],
			"IBLOCK_ID" => [
				"PARENT" => "BASE",
				"NAME" => "Код информационного блока",
				"TYPE" => "LIST",
				"VALUES" => $arIBlocks,
				"DEFAULT" => '',
				"ADDITIONAL_VALUES" => "Y",
				"REFRESH" => "Y",
			],
			"ELEMENT_NAME" => [
				"PARENT" => "BASE",
				"NAME" => "Название элемента инфоблока",
				"TYPE" => "STRING",
				"DEFAULT" => "Отправленная форма",
			],
			"SEND_EMAIL" => [
				"PARENT" => "BASE",
				"NAME" => "Отправлять уведомление по почте",
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
			],
			"EMAIL_TEMPLATE" => [
				"PARENT" => "BASE",
				"NAME" => "Шаблон почтового уведомления",
				"TYPE" => "STRING",
				"DEFAULT" => "",
			],
		],
	];
