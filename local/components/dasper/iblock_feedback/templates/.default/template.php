<?php
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<form id="feedbackForm">
    <input type="hidden" name="sessid" value="<?= bitrix_sessid() ?>">
	<?php foreach($arResult['PROPERTIES'] as $property): ?>
        <label><?= $property['NAME'] ?><?= $property['IS_REQUIRED'] ? '*' : '' ?>:</label>
        <input type="text" name="<?= $property['CODE'] ?>"<?= $property['IS_REQUIRED'] ? ' required' : '' ?>><br>
	<?php endforeach; ?>
    <button type="submit">Отправить</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        var form = document.getElementById('feedbackForm');

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(form);
                // Отправка данных через AJAX-запрос к компоненту Bitrix
                var params = <?=\Bitrix\Main\Web\Json::encode(['signedParameters' => $this->getComponent()->getSignedParameters()])?>; //получаем параметры

                BX.ajax.runComponentAction('dasper:iblock_feedback', 'submitForm', {
                    mode: 'class',
                    data: {formData: Object.fromEntries(formData.entries())}, // Преобразуем FormData в объект для передачи
                    signedParameters: params.signedParameters
                }).then(function (response) {
                    if (response.data.success) {
                        alert('Данные успешно отправлены!');
                        location.reload(); // Перезагрузка страницы при успешной отправке
                    } else {
                        alert('Ошибка: ' + response.data.error);
                    }
                })
                    .catch(function (reason) {
                        console.error('Ошибка AJAX-запроса:', reason);
                    });
            });
        }
    });
</script>
