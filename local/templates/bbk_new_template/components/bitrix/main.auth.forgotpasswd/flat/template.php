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
	echo Loc::getMessage('MAIN_AUTH_PWD_SUCCESS');
	return;
}
?>

<div class="bx-authform">

    <?// MESSAGES ?>
	<? if ($arResult['ERRORS'])
    { ?>
        <div class="alert alert-danger">
            <? foreach ($arResult['ERRORS'] as $error)
            {
                echo $error;
            }
            ?>
        </div>
	<? }
    elseif
    ($arResult['SUCCESS']) {?>
        <div class="alert alert-success">
            <?= $arResult['SUCCESS'];?>
        </div>
	<? } ?>



    <? if(false) { ?>

        <?// TITLE ?>
        <h3 class="bx-title"><?= Loc::getMessage('MAIN_AUTH_PWD_HEADER');?></h3>

        <?// MESSAGE ?>
        <p class="bx-authform-content-container"><?= Loc::getMessage('MAIN_AUTH_PWD_NOTE');?></p>

    <? } ?>


    <?// FORM ?>
	<form name="bform" method="post" target="_top" action="<?= POST_FORM_ACTION_URI;?>">

        <?// NAME ?>
        <? if(false) { ?>
		<div class="bx-form-container">
			<div class="bx-authform-label-container"><?= Loc::getMessage('MAIN_AUTH_PWD_FIELD_LOGIN');?></div>
			<div class="bx-authform-input-container">
				<input type="text" name="<?= $arResult['FIELDS']['login'];?>" maxlength="255" value="<?= \htmlspecialcharsbx($arResult['LAST_LOGIN']);?>" />
			</div>
			<span class="login-label"><?= Loc::getMessage('MAIN_AUTH_PWD_OR');?></span>
		</div>
        <? } ?>

        <?// EMAIL ?>
		<div class="bx-form-container">
			<div class="bx-authform-label-container"><?= Loc::getMessage('MAIN_AUTH_PWD_FIELD_EMAIL');?></div>
			<div class="bx-authform-input-container">
				<input type="text" name="<?= $arResult['FIELDS']['email'];?>" maxlength="255" value="" />
			</div>
		</div>

        <?// CAPTCHA ?>
<!--		--><?// if ($arResult['CAPTCHA_CODE']) { ?>
			<input type="hidden" name="captcha_sid" value="<?= \htmlspecialcharsbx($arResult['CAPTCHA_CODE']);?>" />
			<div class="bx-authform-formgroup-container dbg_captha">
				<div class="bx-authform-label-container">
					<?= Loc::getMessage('MAIN_AUTH_PWD_FIELD_CAPTCHA');?>
				</div>
				<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?= \htmlspecialcharsbx($arResult['CAPTCHA_CODE']);?>" width="180" height="40" alt="CAPTCHA" /></div>
				<div class="bx-authform-input-container">
					<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
				</div>
			</div>
<!--		--><?// } ?>

        <?// SUBMIT ?>
		<div class="bx-form-container">
			<input type="submit" class="btn btn-primary" name="<?= $arResult['FIELDS']['action'];?>" value="<?= Loc::getMessage('MAIN_AUTH_PWD_FIELD_SUBMIT');?>" />
		</div>

        <?// ?? ?>
		<? if ($arResult['AUTH_AUTH_URL'] || $arResult['AUTH_REGISTER_URL']) {?>
			<hr class="bxe-light">

			<noindex>
                <? if ($arResult['AUTH_AUTH_URL']) {?>
                    <div class="bx-authform-link-container">
                        <a href="<?= $arResult['AUTH_AUTH_URL'];?>" rel="nofollow">
                            <?= Loc::getMessage('MAIN_AUTH_PWD_URL_AUTH_URL');?>
                        </a>
                    </div>
                <? } ?>

                <? if ($arResult['AUTH_REGISTER_URL']) {?>
                    <div class="bx-authform-link-container">
                        <a href="<?= $arResult['AUTH_REGISTER_URL'];?>" rel="nofollow">
                            <?= Loc::getMessage('MAIN_AUTH_PWD_URL_REGISTER_URL');?>
                        </a>
                    </div>
                <? } ?>
			</noindex>
		<? } ?>

	</form>
</div>

<script type="text/javascript">
	document.bform.<?= $arResult['FIELDS']['login'];?>.focus();
</script>
