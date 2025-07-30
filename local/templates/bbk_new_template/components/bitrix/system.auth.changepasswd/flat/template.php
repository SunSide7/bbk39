<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<div class="bx-auth">

    <? ShowMessage($arParams["~AUTH_RESULT"]); ?>

    <? if($arResult["SHOW_FORM"]) { ?>

        <form id="change-password-form" method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform">

            <?if ($arResult["BACKURL"] <> '') { ?>
                <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
            <? } ?>

            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="CHANGE_PWD">

            <div class="data-table bx-changepass-table">

                <?// TITLE ?>
                <div class="change-pass-title">
                    <?=GetMessage("AUTH_CHANGE_PASSWORD")?>
                </div>

                <?// MIDDLE ?>
                <div class="form-middle-part">

                    <div class="form-row">
                        <td><span class="starrequired">*</span><?=GetMessage("AUTH_LOGIN")?></td>
                        <td><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" class="bx-auth-input" /></td>
                    </div>

                    <? if($arResult["USE_PASSWORD"])
                    { ?>
                        <div class="form-row">
                            <span><span class="starrequired">*</span><?echo GetMessage("sys_auth_changr_pass_current_pass")?></span>
                            <span><input type="password" name="USER_CURRENT_PASSWORD" maxlength="255" value="<?=$arResult["USER_CURRENT_PASSWORD"]?>" class="bx-auth-input" autocomplete="new-password" /></span>
                        </div>
                    <? }
                    else
                    { ?>
                        <div class="form-row">
                            <span><span class="starrequired">*</span><?=GetMessage("AUTH_CHECKWORD")?></span>
                            <span><input type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" class="bx-auth-input" autocomplete="off" /></span>
                        </div>
                    <? } ?>


                    <div class="form-row">
                        <span><span class="starrequired">*</span><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?></span>
                        <span>

                            <input type="password" name="USER_PASSWORD" maxlength="255" value="<?=$arResult["USER_PASSWORD"]?>" class="bx-auth-input" autocomplete="new-password" />

                            <?if($arResult["SECURE_AUTH"]) { ?>
                                <span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
                                    <div class="bx-auth-secure-icon"></div>
                                </span>

                                <noscript>
                                    <span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
                                        <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                                    </span>
                                </noscript>

                                <script type="text/javascript">
                                    document.getElementById('bx_auth_secure').style.display = 'inline-block';
                                </script>
                            <? } ?>

                        </span>
                    </div>

                    <div class="form-row">
                        <span><span class="starrequired">*</span><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?></span>
                        <span><input type="password" name="USER_CONFIRM_PASSWORD" maxlength="255" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" class="bx-auth-input" autocomplete="new-password" /></span>
                    </div>


                    <? if($arResult["USE_CAPTCHA"]) { ?>
                        <div class="form-row">
                            <span></span>
                            <span>
                                <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                                <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
                            </span>
                        </div>
                        <div class="form-row">
                            <span><span class="starrequired">*</span><?echo GetMessage("system_auth_captcha")?></span>
                            <span><input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" /></span>
                        </div>
                    <? } ?>
                </div>

                <?// BOTTOM ?>
                <div class="form-bottom">
                    <div class="form-row">
                        <span></span>
                        <span>
                            <input type="submit" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>" />
                        </span>
                    </div>
                </div>

            </div>
        </form>

        <?// POLICY ?>
        <p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
        <p><span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>

    <? } ?>

    <p>
        <a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a>
    </p>

</div>Z