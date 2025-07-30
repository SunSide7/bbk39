<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init();
?>

<div class="bx-system-auth-form">

<?
if ($arResult['SHOW_ERRORS'] === 'Y' && $arResult['ERROR'] && !empty($arResult['ERROR_MESSAGE']))
{
	ShowMessage($arResult['ERROR_MESSAGE']);
}
?>

<?if($arResult["FORM_TYPE"] == "login"):?>

<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

    <? if($arResult["BACKURL"] <> '') { ?>
        <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
    <? } ?>

    <? foreach($arResult["POST"] as $key => $value) { ?>
        <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
    <? } ?>

	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />

    <h2>Авторизация</h2>

	<div>

        <? // Field 1 ?>
        <? if(false) { ?>
            <tr>
                <td colspan="2">
                    <?=GetMessage("AUTH_LOGIN")?>:<br />
                    <input type="text" name="USER_LOGIN" maxlength="50" value="" size="17" />
                    <script>
                        BX.ready(function() {
                            var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
                            if (loginCookie)
                            {
                                var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
                                var loginInput = form.elements["USER_LOGIN"];
                                loginInput.value = loginCookie;
                            }
                        });
                    </script>
                </td>
            </tr>
        <? } ?>

        <div class="bx-authform-formgroup-container" style="margin-bottom: 12px;">
            <div class="bx-authform-label-container">
                <?//= Loc::getMessage('MAIN_AUTH_FORM_FIELD_LOGIN');?>Введите email <span class="req-red">*</span>
            </div>

            <div class="bx-authform-input-container">
                <input required type="text" name="USER_LOGIN" maxlength="50" value="" size="17" />
                <script>
                    BX.ready(function() {
                        var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
                        if (loginCookie)
                        {
                            var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
                            var loginInput = form.elements["USER_LOGIN"];
                            loginInput.value = loginCookie;
                        }
                    });
                </script>
            </div>
        </div>

        <? // Field 2 ?>
		<div class="bx-authform-formgroup-container" style="margin-bottom: 12px;">
			<td colspan="2">
                <?=GetMessage("AUTH_PASSWORD")?>:<br />
                <input type="password" name="USER_PASSWORD" maxlength="255" size="17" autocomplete="off" />

                <?if($arResult["SECURE_AUTH"]) { ?>

                    <span class="bx-auth-secure" id="bx_auth_secure<?=$arResult["RND"]?>" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
                        <div class="bx-auth-secure-icon"></div>
                    </span>

                    <noscript>
                    <span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
                        <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                    </span>
                    </noscript>

                    <script type="text/javascript">
                    document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
                    </script>

                <? } ?>

			</td>
		</div>

        <? // Remember checkbox ?>
        <? if ($arResult["STORE_PASSWORD"] == "Y") { ?>
            <div style="display:flex;justify-content: space-between;">
                <div>
                    <span valign="top"><input type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" /></span>
                    <span width="100%"><label for="USER_REMEMBER_frm" title="<?=GetMessage("AUTH_REMEMBER_ME")?>"><?echo GetMessage("AUTH_REMEMBER_SHORT")?></label></span>
                </div>

                <? // Forgot password ?>
                <div>
                    <a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
                </div>
            </div>
            <br>

        <? } ?>

        <? // Captcha ?>
        <? if ($arResult["CAPTCHA_CODE"]) { ?>
            <tr>
                <td colspan="2">
                <?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
                <input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
                <input type="text" name="captcha_word" maxlength="50" value="" /></td>
            </tr>
        <? } ?>

        <? // Submit ?>
		<tr>
			<td colspan="2">
                <input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" />
            </td>
		</tr>



        <? // Registration ?>
        <? if(false && $arResult["NEW_USER_REGISTRATION"] == "Y") { ?>
            <tr>
                <td colspan="2"><noindex><a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a></noindex><br /></td>
            </tr>
        <? } ?>
        <div class="bx-authform-link-container p-0" style="margin-top: 20px;">
            <a href="javascript:void(0);" class="register btn_blue btn_blue_lighten">Регистрация</a>
        </div>




        <? // Socials ?>
        <? if($arResult["AUTH_SERVICES"]) { ?>
            <tr>
                <td colspan="2">
                    <div class="bx-auth-lbl"><?=GetMessage("socserv_as_user_form")?></div>
                    <?
                    $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons",
                        array(
                            "AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
                            "SUFFIX"=>"form",
                        ),
                        $component,
                        array("HIDE_ICONS"=>"Y")
                    );
                    ?>
                </td>
            </tr>
        <? } ?>


        <div class="policy" style="text-align:center;">
            Нажимая на кнопку, я даю согласие на своих использование персональных данных и принимаю условия
            <a href="/upload/docs/politika-konfidencialnosli.pdf" target="_blank">“Политики конфиденциальности”</a> и
            <a href="/upload/docs/polzovatelskoe-soglashenie.pdf" target="_blank">“Пользовательского соглашения”</a>
        </div>

	</div>
</form>



<?if($arResult["AUTH_SERVICES"]):?>
<?
$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
	array(
		"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
		"AUTH_URL"=>$arResult["AUTH_URL"],
		"POST"=>$arResult["POST"],
		"POPUP"=>"Y",
		"SUFFIX"=>"form",
	),
	$component,
	array("HIDE_ICONS"=>"Y")
);
?>
<?endif?>

<?
elseif($arResult["FORM_TYPE"] == "otp"):
?>

<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?if($arResult["BACKURL"] <> ''):?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?endif?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="OTP" />
	<table width="95%">
		<tr>
			<td colspan="2">
			<?echo GetMessage("auth_form_comp_otp")?><br />
			<input type="text" name="USER_OTP" maxlength="50" value="" size="17" autocomplete="off" /></td>
		</tr>
<?if ($arResult["CAPTCHA_CODE"]):?>
		<tr>
			<td colspan="2">
			<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
			<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
			<input type="text" name="captcha_word" maxlength="50" value="" /></td>
		</tr>
<?endif?>
<?if ($arResult["REMEMBER_OTP"] == "Y"):?>
		<tr>
			<td valign="top"><input type="checkbox" id="OTP_REMEMBER_frm" name="OTP_REMEMBER" value="Y" /></td>
			<td width="100%"><label for="OTP_REMEMBER_frm" title="<?echo GetMessage("auth_form_comp_otp_remember_title")?>"><?echo GetMessage("auth_form_comp_otp_remember")?></label></td>
		</tr>
<?endif?>
		<tr>
			<td colspan="2"><input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" /></td>
		</tr>
		<tr>
			<td colspan="2"><noindex><a href="<?=$arResult["AUTH_LOGIN_URL"]?>" rel="nofollow"><?echo GetMessage("auth_form_comp_auth")?></a></noindex><br /></td>
		</tr>
	</table>
</form>

<?
else:
?>

<form action="<?=$arResult["AUTH_URL"]?>">
	<table width="95%">
		<tr>
			<td align="center">
				<?=$arResult["USER_NAME"]?><br />
				[<?=$arResult["USER_LOGIN"]?>]<br />
				<a href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><?=GetMessage("AUTH_PROFILE")?></a><br />
			</td>
		</tr>
		<tr>
			<td align="center">
			<?foreach ($arResult["GET"] as $key => $value):?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?endforeach?>
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="logout" value="yes" />
			<input type="submit" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />
			</td>
		</tr>
	</table>
</form>
<?endif?>
</div>
