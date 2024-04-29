<?php

	namespace dasper\Components;

	use Bitrix\Main\Diag\Debug;
	use Bitrix\Main\Engine\Contract\Controllerable;
	use Bitrix\Main\Loader;
	use Bitrix\Main\Engine\ActionFilter;


	class iblock_feedback extends \CBitrixComponent implements Controllerable {
		public function configureActions() {
			return [
				'submitForm' => [
					'prefilters' => [
						new ActionFilter\HttpMethod(
							[ActionFilter\HttpMethod::METHOD_POST]
						),
						new ActionFilter\Csrf(),
					],
				],
			];
		}

		protected function listKeysSignedParameters() {
			return [
				'IBLOCK_ID',
				'ELEMENT_NAME',
				'SEND_EMAIL',
				'EMAIL_TEMPLATE',
			];
		}

		public function onPrepareComponentParams($arParams) {
			$this->arParams = $arParams;
			return $this->arParams;
		}

		private function getIblockProperties($iblockId) {
			$properties = [];
			$res = \CIBlockProperty::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => $iblockId]);
			while($prop = $res->Fetch()) {
				$properties[] = [
					'CODE' => $prop['CODE'],
					'NAME' => $prop['NAME'],
					'IS_REQUIRED' => ($prop['IS_REQUIRED'] === 'Y'),
				];
			}
			return $properties;
		}

		public function submitFormAction($formData) {

			Loader::includeModule('iblock');

			$element = new \CIBlockElement;
			$fields = [
				'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
				'NAME' => htmlspecialchars($this->arParams['ELEMENT_NAME']),
			];

			$this->arResult['PROPERTIES'] = $this->getIblockProperties($this->arParams['IBLOCK_ID']);

			foreach($this->arResult['PROPERTIES'] as $property) {
				$value = htmlspecialchars($formData[$property['CODE']]);
				if($property['IS_REQUIRED'] && empty($value)) {
					return ['success' => false, 'error' => 'Не заполнено обязательное поле: ' . $property['NAME']];
				}
				$fields['PROPERTY_VALUES'][$property['CODE']] = $value;
			}

			if($elementId = $element->Add($fields)) {
				if($this->arParams['SEND_EMAIL'] === 'Y' && $this->arParams['EMAIL_TEMPLATE']) {
					$this->sendEmailNotification($fields, $this->arParams['EMAIL_TEMPLATE']);
				}
				return ['success' => true];
			} else {
				return ['success' => false, 'error' => $element->LAST_ERROR];
			}
		}

		private function sendEmailNotification($fields, $templateCode) {
			$eventName = $templateCode;
			$arEventFields = $fields;

			// Отправка почтового уведомления
			\CEvent::Send($eventName, SITE_ID, $arEventFields);
		}


		public function executeComponent() {
			$this->arResult['PROPERTIES'] = [];

			if($this->arParams['IBLOCK_ID'] > 0 && Loader::includeModule('iblock')) {
				$this->arResult['PROPERTIES'] = $this->getIblockProperties($this->arParams['IBLOCK_ID']);
			}

			$this->includeComponentTemplate();
		}
	}
