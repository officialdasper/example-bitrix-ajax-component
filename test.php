<?php
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
	$APPLICATION->SetTitle("test");
?>
	
	
<?php $APPLICATION->IncludeComponent(
	"dasper:iblock_feedback", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE" => "feedback",
		"IBLOCK_ID" => "18",
		"ELEMENT_NAME" => "Фулл тест",
		"SEND_EMAIL" => "N",
		"EMAIL_TEMPLATE" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
); ?>


<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>