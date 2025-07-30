<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arResult['SHOW_FIELDS'] = array(
    'NAME',
//    'LAST_NAME',
    'PERSONAL_PHONE',
    'EMAIL',
    'LOGIN',
    'PASSWORD',
    'CONFIRM_PASSWORD',
);

if ($arResult["SHOW_SMS_FIELD"] == true) CJSCore::Init('phone_auth');?>


<div class="bx-auth-reg">

    <?if($USER->IsAuthorized()) { ?>

        <p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

    <? }
    else // Not authorized
    { ?>

        <? if (!empty($arResult["ERRORS"])) {
            foreach ($arResult["ERRORS"] as $key => $error)
                if (intval($key) == 0 && $key !== 0)
                    $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;" . GetMessage("REGISTER_FIELD_" . $key) . "&quot;", $error);

            ShowError(implode("<br />", $arResult["ERRORS"]));

        } elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y") { ?>
            <p><?echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT")?></p>
        <? } ?>


    <? if($arResult["SHOW_SMS_FIELD"] == true) { ?>

        <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform">

            <? if($arResult["BACKURL"] <> '') { ?>
                <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
            <? } ?>

            <input type="hidden" name="SIGNED_DATA" value="<?=htmlspecialcharsbx($arResult["SIGNED_DATA"])?>" />
            <table>
                <tbody>
                    <tr>
                        <td><?echo GetMessage("main_register_sms")?> <span class="req-red">*</span></td>
                        <td><input size="30" type="text" name="SMS_CODE" value="<?=htmlspecialcharsbx($arResult["SMS_CODE"])?>" autocomplete="off" /></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="code_submit_button" value="<?echo GetMessage("main_register_sms_send")?>" /></td>
                    </tr>
                </tfoot>
            </table>
        </form>

        <script>
        new BX.PhoneAuth({
            containerId: 'bx_register_resend',
            errorContainerId: 'bx_register_error',
            interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
            data:
                <?=CUtil::PhpToJSObject([
                    'signedData' => $arResult["SIGNED_DATA"],
                ])?>,
            onError:
                function(response)
                {
                    var errorDiv = BX('bx_register_error');
                    var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                    errorNode.innerHTML = '';
                    for(var i = 0; i < response.errors.length; i++)
                    {
                        errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                    }
                    errorDiv.style.display = '';
                }
        });
        </script>

        <div id="bx_register_error" style="display:none"><?ShowError("error")?></div>

        <div id="bx_register_resend"></div>

    <? }
    else
    { ?>

        <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
            <?
            if($arResult["BACKURL"] <> '') {
            ?>
                <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
            <? } ?>

            <div class="reg-form">
                <script>
                    $(document).ready(function(){
                        $('.date').mask('00/00/0000');
                        $('.time').mask('00:00:00');
                        $('.date_time').mask('00/00/0000 00:00:00');
                        $('.cep').mask('00000-000');
                        // $('.phone').mask('0000-0000');
                        $('.phone_with_ddd').mask('(00) 0000-0000');
                        $('.phone_us').mask('+7 (000) 000-0000');
                        $('.mixed').mask('AAA 000-S0S');
                        $('.cpf').mask('000.000.000-00', {reverse: true});
                        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
                        $('.money').mask('000.000.000.000.000,00', {reverse: true});
                        $('.money2').mask("#.##0,00", {reverse: true});
                        $('.ip_address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                            translation: {
                                'Z': {
                                    pattern: /[0-9]/, optional: true
                                }
                            }
                        });
                        $('.ip_address').mask('099.099.099.099');
                        $('.percent').mask('##0,00%', {reverse: true});
                        $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
                        $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
                        $('.fallback').mask("00r00r0000", {
                            translation: {
                                'r': {
                                    pattern: /[\/]/,
                                    fallback: '/'
                                },
                                placeholder: "__/__/____"
                            }
                        });
                        $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});
                    });

                    var options = {
                        onKeyPress: function (cep, e, field, options) {
                            var masks = ["00000-000", "0-00-00-00"];
                            var mask = cep.length > 7 ? masks[1] : masks[0];
                            $(".crazy_cep").mask(mask, options);
                        },
                    };

                    $(".crazy_cep").mask("00000-000", options);
                </script>

                <div class="reg-fom-top">

                    <? foreach ($arResult["SHOW_FIELDS"] as $FIELD) { ?>

                        <? if(false) { ?>
                            <?if($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true) {?>
                            <? } else { ?>
                                <tr>
                                    <td><?=GetMessage("REGISTER_FIELD_".$FIELD)?>:<?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?> <span class="req-red">*</span><?endif?></td>
                                    <td>
                                        <? switch ($FIELD)
                                        {
                                            case "PASSWORD":
                                                ?><input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="bx-auth-input" />
                                    <?if($arResult["SECURE_AUTH"]):?>
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
                                    <?endif?>
                                    <?
                                                break;
                                            case "CONFIRM_PASSWORD":
                                                ?><input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" /><?
                                                break;

                                            case "PERSONAL_GENDER":
                                                ?><select name="REGISTER[<?=$FIELD?>]">
                                                    <option value=""><?=GetMessage("USER_DONT_KNOW")?></option>
                                                    <option value="M"<?=$arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_MALE")?></option>
                                                    <option value="F"<?=$arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
                                                </select><?
                                                break;

                                            case "PERSONAL_COUNTRY":
                                            case "WORK_COUNTRY":
                                                ?><select name="REGISTER[<?=$FIELD?>]"><?
                                                foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value)
                                                {
                                                    ?><option value="<?=$value?>"<?if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<?endif?>><?=$arResult["COUNTRIES"]["reference"][$key]?></option>
                                                <?
                                                }
                                                ?></select><?
                                                break;

                                            case "PERSONAL_PHOTO":
                                            case "WORK_LOGO":
                                                ?><input size="30" type="file" name="REGISTER_FILES_<?=$FIELD?>" /><?
                                                break;

                                            case "PERSONAL_NOTES":
                                            case "WORK_NOTES":
                                                ?><textarea cols="30" rows="5" name="REGISTER[<?=$FIELD?>]"><?=$arResult["VALUES"][$FIELD]?></textarea><?
                                                break;
                                            default:
                                                if ($FIELD == "PERSONAL_BIRTHDAY"):?><small><?=$arResult["DATE_FORMAT"]?></small><br /><?endif;
                                                ?><input size="30" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" /><?
                                                    if ($FIELD == "PERSONAL_BIRTHDAY")
                                                        $APPLICATION->IncludeComponent(
                                                            'bitrix:main.calendar',
                                                            '',
                                                            array(
                                                                'SHOW_INPUT' => 'N',
                                                                'FORM_NAME' => 'regform',
                                                                'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                                                                'SHOW_TIME' => 'N'
                                                            ),
                                                            null,
                                                            array("HIDE_ICONS"=>"Y")
                                                        );
                                                    ?><?
                                        } ?>
                                    </td>
                                </tr>
                            <? } // if($FIELD == "AUTO_TIME_ZONE" ..) ?>
                        <? } ?>

                        <div class="reg-field">

                            <?// Названия полей ?>
                            <? if ($FIELD !== 'LOGIN' && $FIELD !== 'PHONE' && $FIELD !== 'NAME') { ?>
                                <div class="reg-field-name">
                                    <label>
                                        <?=GetMessage("REGISTER_FIELD_".$FIELD)?>:<?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?> <span class="req-red">*</span><?endif?>
                                    </label>
                                </div>
                            <? } elseif ($FIELD == 'PHONE') {?>
                                <div class="reg-field-name">
                                    <label>
                                        Номер телефона
                                    </label>
                                </div>
                            <? } elseif ($FIELD == 'NAME') {?>
                                <div class="reg-field-name">
                                    <label>
                                        <?=GetMessage("REGISTER_FIELD_".$FIELD)?>:<span class="req-red">*</span>
                                    </label>
                                </div>

                            <? } ?>

                            <?// Содержимое полей ?>
                            <? switch ($FIELD)
                            {
                                case "PASSWORD":
                                    ?><input required size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="form-control" />
                                        <? if($arResult["SECURE_AUTH"]) { ?>
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
                                    <?
                                    break;
                                case "CONFIRM_PASSWORD":
                                    ?><input required size="30" type="password" name="REGISTER[<?=$FIELD?>]" class="form-control" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" /><?
                                    break;

                                default:

                                    // Копирование значения поля EMAIL в поле LOGIN
                                    if ($FIELD == 'EMAIL') { ?>

                                        <input required class="form-control" size="30" type="email" name="REGISTER[<?= $FIELD ?>]"
                                               onkeyup="document.getElementById('login-field').value = this.value"
                                               value="<?= $arResult["VALUES"][$FIELD] ?>"/>

                                    <? } elseif ($FIELD == 'LOGIN') { // Скрываем поле LOGIN ?>

                                        <input required id="login-field" size="30" type="text" style="display: none;" name="REGISTER[<?= $FIELD ?>]"
                                               value="<?= $arResult["VALUES"][$FIELD] ?>"/>

                                    <? } elseif ($FIELD == 'PERSONAL_PHONE') {?>
                                    <input required class="form-control crazy_cep phone_us" size="30" type="tel" name="REGISTER[<?= $FIELD ?>]"
                                           value="<?= $arResult["VALUES"][$FIELD] ?>"/>
                                    <? } elseif ($FIELD == 'NAME')  { ?>
                                        <input required class="form-control" size="30" type="text" name="REGISTER[<?= $FIELD ?>]"
                                               value="<?= $arResult["VALUES"][$FIELD] ?>"/>
                                    <? } else { ?>
                                    <input class="form-control" size="30" type="text" name="REGISTER[<?= $FIELD ?>]"
                                           value="<?= $arResult["VALUES"][$FIELD] ?>"/>
                                    <? } ?>


                                    <?

                                    break;
                            } ?>

                        </div>

                    <? } ?>

                    <p>* <?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
                    <br>

                    <?
                    /* CAPTCHA */
                    if ($arResult["USE_CAPTCHA"] == "Y")
                    {
                        ?>

                            <div class="captcha-top">
                                <div class="captcha-title">
                                    <b><?=GetMessage("REGISTER_CAPTCHA_TITLE")?></b>
                                </div>
                            </div>

                            <div class="captcha-image">
                                <br>
                                <div class="captcha-image-inner">
                                    <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
                                </div>
                            </div>

                            <div class="captcha-bottom">
                                <div class="captcha-input-title"><?=GetMessage("REGISTER_CAPTCHA_PROMT")?>: <span class="req-red">*</span></div>
                                <div class="captcha-input"><input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" /></div>
                            </div>

                        <?
                    }
                    /* !CAPTCHA */
                    ?>

                </div>

                <div class="reg-form-bottom">
                    <div class="bx-authform-formgroup-container">
                        <div class="checkbox">
                            <label class="bx-filter-param-label">
                                <input type="checkbox" required id="USER_AGREEMENT" name="" value="Y" />
                                <span class="bx-filter-param-text">Согласие на использование персональных данных *</span>
                            </label>
                        </div>
                    </div>
                    <div class="reg-submit">
                        <input class="btn_blue" type="submit" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER")?>" />
                    </div>
                </div>

            </div>
        </form>


    <? } //$arResult["SHOW_SMS_FIELD"] == true ?>


    <br>
    <p> <span class="req-red">*</span> <?=GetMessage("AUTH_REQ")?></p>

    <? } ?>

</div>

<script>
    $(document).ready(function() {
        $('form[name="regform"]').submit(function(e) {

            // e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    // alert("form submitted successfully");
                    // $('#form_1').hide();
                    // $('#form_2').show();

                    setTimeout(function() {

                        location.reload();

                    }, 2000)


                },
                error:function(data){
                    alert("there is an error kindly check it now");
                }

            });

            // return false;

        });
    })
</script>