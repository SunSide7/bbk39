<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

\Bitrix\Main\Page\Asset::getInstance()->addCss(
	'/bitrix/css/main/system.auth/flat/style.css'
);

if ($arResult['AUTHORIZED'])
{
	echo Loc::getMessage('MAIN_AUTH_FORM_SUCCESS');
    ?>
    <script>
        setTimeout(function() {

            // location.reload();

        }, 2000)
    </script>
    <?
	return;
}
?>

<div class="bx-authform">

	<?if ($arResult['ERRORS']):?>
	<div class="alert alert-danger">
		<? foreach ($arResult['ERRORS'] as $error)
		{
			echo $error;
		}
		?>
	</div>
	<?endif;?>


	<h3 class="bx-title">
        <?// = Loc::getMessage('MAIN_AUTH_FORM_HEADER');?>
        Авторизация
    </h3>

	<?if ($arResult['AUTH_SERVICES']):?>
		<?$APPLICATION->IncludeComponent('bitrix:socserv.auth.form',
			'flat',
			array(
				'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
				'AUTH_URL' => $arResult['CURR_URI']
	   		),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
		?>
		<hr class="bxe-light">
	<?endif?>

	<form class="auth-form" name="<?= $arResult['FORM_ID'];?>" method="post" action="<?= POST_FORM_ACTION_URI;?>">

		<div class="bx-authform-formgroup-container" style="margin-bottom: 12px;">

			<div class="bx-authform-label-container">
                <?//= Loc::getMessage('MAIN_AUTH_FORM_FIELD_LOGIN');?>Введите email <span class="req-red">*</span>
            </div>

			<div class="bx-authform-input-container">
				<input required type="text" name="<?= $arResult['FIELDS']['login'];?>" maxlength="255" value="<?= \htmlspecialcharsbx($arResult['LAST_LOGIN']);?>" />
			</div>
		</div>

		<div class="bx-authform-formgroup-container" style="margin-bottom: 20px;">
			<div class="bx-authform-label-container"><?= Loc::getMessage('MAIN_AUTH_FORM_FIELD_PASS');?> <span class="req-red">*</span></div>
			<div class="bx-authform-input-container">
				<?if ($arResult['SECURE_AUTH']):?>
					<div class="bx-authform-psw-protected" id="bx_auth_secure" style="display:none">
						<div class="bx-authform-psw-protected-desc"><span></span>
							<?= Loc::getMessage('MAIN_AUTH_FORM_SECURE_NOTE');?>
						</div>
					</div>
					<script type="text/javascript">
						document.getElementById('bx_auth_secure').style.display = '';
					</script>
				<?endif?>
				<input required type="password" name="<?= $arResult['FIELDS']['password'];?>" maxlength="255" autocomplete="off" />
			</div>
		</div>

		<?if ($arResult['CAPTCHA_CODE']):?>
			<input type="hidden" name="captcha_sid" value="<?= \htmlspecialcharsbx($arResult['CAPTCHA_CODE']);?>" />
			<div class="bx-authform-formgroup-container dbg_captha">
				<div class="bx-authform-label-container">
					<?= Loc::getMessage('MAIN_AUTH_FORM_FIELD_CAPTCHA');?>
				</div>
				<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?= \htmlspecialcharsbx($arResult['CAPTCHA_CODE']);?>" width="180" height="40" alt="CAPTCHA" /></div>
				<div class="bx-authform-input-container">
					<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
				</div>
			</div>
		<?endif;?>

		<?if ($arResult['STORE_PASSWORD'] == 'Y'):?>
			<div class="bx-authform-formgroup-container m-0" style="margin-bottom:20px !important">
				<div class="checkbox m-0" style="display: flex;justify-content: space-between;">
					<label class="bx-filter-param-label">
						<input type="checkbox" id="USER_REMEMBER" name="<?= $arResult['FIELDS']['remember'];?>" value="Y" />
						<span class="bx-filter-param-text">
                            <?//= Loc::getMessage('MAIN_AUTH_FORM_FIELD_REMEMBER');?>Запомнить меня
                        </span>
					</label>

                    <div class="bx-authform-link-container p-0">
                        <a href="javascript:void(0);" class="forgot-pass">Забыли свой пароль?</a>
                    </div>
				</div>
			</div>
		<?endif?>

		<div class="bx-authform-formgroup-container m-0">
			<input type="submit" class="btn_blue" name="<?= $arResult['FIELDS']['action'];?>" value="<?//= Loc::getMessage('MAIN_AUTH_FORM_FIELD_SUBMIT');?>Продолжить" />
		</div>

		<?if ($arResult['AUTH_FORGOT_PASSWORD_URL'] || $arResult['AUTH_REGISTER_URL']):?>
			<hr class="bxe-light">
			<noindex>
			<?if ($arResult['AUTH_FORGOT_PASSWORD_URL']):?>
				<div class="bx-authform-link-container">
					<a href="<?= $arResult['AUTH_FORGOT_PASSWORD_URL'];?>" rel="nofollow">
						<?= Loc::getMessage('MAIN_AUTH_FORM_URL_FORGOT_PASSWORD');?>
					</a>
				</div>
			<?endif;?>
			<?if ($arResult['AUTH_REGISTER_URL']):?>
				<div class="bx-authform-link-container">
					<a href="<?= $arResult['AUTH_REGISTER_URL'];?>" rel="nofollow">
						<?= Loc::getMessage('MAIN_AUTH_FORM_URL_REGISTER_URL');?>
					</a>
				</div>
			<?endif;?>
			</noindex>
		<?endif;?>


        <div class="bx-authform-link-container p-0" style="margin-top: 20px;">
            <a href="javascript:void(0);" class="register btn_blue btn_blue_lighten">Регистрация</a>
        </div>

        <div class="policy">
            Нажимая на кнопку, я даю согласие на использование своих персональных данных и принимаю условия
            <a href="/upload/docs/politika-konfidencialnosli.pdf" target="_blank">“Политики конфиденциальности”</a> и
            <a href="/upload/docs/polzovatelskoe-soglashenie.pdf" target="_blank">“Пользовательского соглашения”</a>
        </div>

        <span>$arResult['ERRORS']: <?= '<pre>' . print_r($arResult['ERRORS']['ERROR_PROCESSING'], false) . '</pre>' ?></span>

	</form>
</div>
<?
    $errorsString = $arResult['ERRORS']['ERROR_PROCESSING'];
?>

<script type="text/javascript">
	<?if ($arResult['LAST_LOGIN'] != ''):?>
	try{document.<?= $arResult['FORM_ID'];?>.USER_PASSWORD.focus();}catch(e){}
	<?else:?>
	try{document.<?= $arResult['FORM_ID'];?>.USER_LOGIN.focus();}catch(e){}
	<?endif?>
</script>

    <script>
        $(document).ready(function() {
            $(document).on('click', 'input[type="submit"]', function() {
                let form = $('form[name="auth-form"]');
                let url = form.attr('action');

                if (true) {

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: form.serialize() + '&is_err=<?= $errorsString ?>', // serializes the form's elements.
                        success: function(data)
                        {
                            console.log('AJAX DATA:', data)
                            console.log('$arResult["ERRORS"]:', <?= $errorsString ?>);
                            console.log('auth success!');
                            // alert("form submitted successfully");
                            // $('#form_1').hide();
                            // $('#form_2').show();

                            setTimeout(function() {

                                // location.reload();

                            }, 2000)


                        },
                        error:function(data){
                            console.log("there is an error kindly check it now");
                        }

                    });

                }
            })

            // $(document).on('submit', 'form[name="auth-form"]', function(e) {
            //
            //     console.log('AUTH FORM SUBMIT')
            //
            //     // e.preventDefault(); // avoid to execute the actual submit of the form.
            //
            //     var form = $(this);
            //     var url = form.attr('action');
            //
            //     $.ajax({
            //         type: "POST",
            //         url: url,
            //         data: form.serialize(), // serializes the form's elements.
            //         success: function(data)
            //         {
            //             // alert("form submitted successfully");
            //             // $('#form_1').hide();
            //             // $('#form_2').show();
            //
            //             setTimeout(function() {
            //
            //                 location.reload();
            //
            //             }, 2000)
            //
            //
            //         },
            //         error:function(data){
            //             alert("there is an error kindly check it now");
            //         }
            //
            //     });
            //
            //     // return false;
            //
            // });
        })
    </script>
