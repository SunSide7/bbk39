<? if (!defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED) die(); ?>

        </main>

        <footer class="footer">

            <div class="footer_top">
                <div class="main_container">

                    <div class="footer_block footer_block_two">
                        <h6 class="footer_block_title">
                            КАТАЛОГ
                            <svg width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.5 6L0.468913 1.5L6.53109 1.5L3.5 6Z" fill="white"/>
                            </svg>
                        </h6>
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "footer_menu",
                            array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "left",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "1",
                                "MENU_CACHE_GET_VARS" => array(
                                ),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "ROOT_MENU_TYPE" => "bottom2",
                                "USE_EXT" => "Y",
                                "COMPONENT_TEMPLATE" => "footer_menu"
                            ),
                            false
                        );?>
                    </div>

                    <div class="footer_block footer_block_three">
                        <h6 class="footer_block_title">
                            КОМПАНИЯ
                            <svg width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.5 6L0.468913 1.5L6.53109 1.5L3.5 6Z" fill="white"/>
                            </svg>
                        </h6>
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "footer_menu",
                            array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "left",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "1",
                                "MENU_CACHE_GET_VARS" => array(
                                ),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "ROOT_MENU_TYPE" => "bottom1",
                                "USE_EXT" => "Y",
                                "COMPONENT_TEMPLATE" => "footer_menu"
                            ),
                            false
                        );?>
                    </div>


                    <div class="footer_block footer_block_four">
                        <h6 class="footer_block_title">
                            ИНФОРМАЦИЯ
                            <svg width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.5 6L0.468913 1.5L6.53109 1.5L3.5 6Z" fill="white"/>
                            </svg>
                        </h6>
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "footer_menu",
                            array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "left",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "1",
                                "MENU_CACHE_GET_VARS" => array(
                                ),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "ROOT_MENU_TYPE" => "bottom5",
                                "USE_EXT" => "Y",
                                "COMPONENT_TEMPLATE" => "footer_menu"
                            ),
                            false
                        );?>
                    </div>
                    <div class="footer_block footer_block_five">
                        <h6 class="footer_block_title">
                            ПОМОЩЬ
                            <svg width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.5 6L0.468913 1.5L6.53109 1.5L3.5 6Z" fill="white"/>
                            </svg>
                        </h6>
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "footer_menu",
                            array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "left",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "1",
                                "MENU_CACHE_GET_VARS" => array(
                                ),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "ROOT_MENU_TYPE" => "bottom4",
                                "USE_EXT" => "Y",
                                "COMPONENT_TEMPLATE" => "footer_menu"
                            ),
                            false
                        );?>
                    </div>
                    <div class="footer_block footer_block_one">
                        <h6>
                            КОНТАКТЫ
                        </h6>
                        <ul class="contact_in">
                            <li>
                                <address>
                                    г. Калининград, Советский<br> проспект, 187
                                </address>
                            </li>
                            <li>
                                <address>
                                    Пн. – Сб.: с 8:00 до 17:00, без&nbsp;перерыва
                                </address>
                            </li>
                            <li>
                                <a href="tel:+74012337177">
                                    +7 4012 33-71-77
                                </a><br>
                                <a href="tel:+74012332233">
                                    +7 4012 33-22-33
                                </a><br>
                                <a href="tel:+74012573257">
                                    + 7 4012 57-32-57
                                </a><br>

                            </li>
                            <li>
                                <a href="mailto:plitka@bbk39.ru">
                                    Розница: plitka@bbk39.ru
                                </a><br>
                                <a href="mailto:vpi@bbk39.ru">
                                    Корпоратив: vpi@bbk39.ru
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <script>

                document.addEventListener('DOMContentLoaded', function() {

                    console.log('BX:', BX);

                    // BX.addCustomEvent(document, "b24-sitebutton-load", function (e){
                    //     e.buttons.add({
                    //         'id': 'my-mail',
                    //         'href': 'mailto: andrey@bitrix.ru',
                    //         'title': 'Email me',
                    //         'target': '_self',
                    //         'sort': 1000,
                    //         'icon': 'http://sparkysite.ru/medium/mails/mails01/mmail01.png',
                    //         'onclick': function() {console.log('button my-mail clicked!!!');},
                    //     });
                    // });


                    // $( document ).on( "b24-sitebutton-load", function (e, instance){
                    //     instance.buttons.add({
                    //         'id': 'my-mail',
                    //         'href': 'mailto: andrey@bitrix.ru',
                    //         'title': 'Email me',
                    //         'target': '_self',
                    //         'sort': 1000,
                    //         'icon': 'http://sparkysite.ru/medium/mails/mails01/mmail01.png',
                    //         'onclick': function() {console.log('button my-mail clicked!!!');},
                    //     });
                    // });

                })

            </script>
            <?
            ?>

            <div class="footer_bottom">
                <div class="main_container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                                    <span>
                                        © 2024 Балтийская Бетонная Компания
                                    </span>
                        </div>
                        <div class="col-lg-6">
                            <div class="d-flex align-items-center footer_social">
                                <a href="https://t.me/bbk39ru_bot" target="_blank">
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/telegram_icon.svg" alt="">
                                </a>
                                <a href="https://wa.me/79666662506" target="_blank">
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/whatsapp_icon.svg" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Auth Modal -->
            <div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="
                            position: absolute;
                            top: 0;
                            left: 0;
                            right: 0;
                            margin: auto;
                            padding-bottom: 0;
                            padding-top: 5px;
                            padding-right: 12px;
                            border: none;
                            z-index: 200;
                        ">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" style="font-size: 30px;">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                                <style>
                                    .modal-body {
                                        padding: 40px 38px;
                                    }
                                    #authModal .bx-authform-link-container {
                                        margin: 0;
                                    }
                                    #authModal .bx-authform-link-container .btn_blue,
                                    #authModal .bx-authform-formgroup-container .btn_blue {
                                        width: 100%;
                                    }

                                    h2.bx-title {
                                        margin-bottom: 25px;
                                        font-size: 28px;
                                    }

                                    .modal-body .bx-authform-input-container input {
                                        background: #ffffff;
                                        border-radius: 8px;
                                        padding: 12px;
                                        height: 42px;
                                    }

                                    .modal-body .btn_blue.btn_blue_lighten {
                                        background: #e8ebf2;
                                        color: #003064;
                                    }

                                    .bx-filter-param-label {
                                        display: flex;
                                        padding: 0 !important;
                                    }
                                    .bx-filter-param-label .bx-filter-param-text {
                                        color: #888888;
                                        font-size: 12px;
                                        line-height: 14px;
                                    }

                                    /* custom checkbox */
                                    .bx-filter-param-label input[type="checkbox"] {
                                        height: 18px;
                                        width: 18px;
                                        margin: 0;
                                        padding: 0;
                                        opacity: 1;
                                        appearance: none;
                                        border: 1px solid #ccd5db;
                                        border-radius: 3px;
                                        background: #fff;
                                        position: relative;
                                        margin-right: 10px;
                                    }

                                    .bx-filter-param-label input[type="checkbox"]:checked {
                                        border: 2px solid #3f51b5;
                                        background: #3f51b5;
                                    }

                                    .bx-filter-param-label input[type="checkbox"]:checked:before, input[type="checkbox"]:checked:after {
                                        content: "";
                                        position: absolute;
                                        height: 2px;
                                        background: #fff;
                                    }

                                    input[type="checkbox"]:checked:before {
                                        width: 8px;
                                        top: calc(11px - 2px);
                                        left: calc(2px - 2px);
                                        transform: rotate(44deg);
                                    }

                                    input[type="checkbox"]:checked:after {
                                        width: 14px;
                                        top: calc(8px - 2px);
                                        left: calc(5px - 2px);
                                        transform: rotate(-55deg);
                                    }

                                    input[type="checkbox"]:focus {
                                        outline: none;
                                    }

                                </style>

                                <div class="modal-auth">
                                    <? if(true) { ?>
                                        <?$APPLICATION->IncludeComponent(
                                            "bitrix:main.auth.form",
                                            "",
                                            Array(
                                                "AUTH_FORGOT_PASSWORD_URL" => "",
                                                "AUTH_REGISTER_URL" => "",
                                                "AUTH_SUCCESS_URL" => $APPLICATION->GetCurPage(false),
                                                "SHOW_ERRORS" => "Y",
                                                "AJAX_MODE" => "Y",
                                            )
                                        );?>
                                    <? } else { ?>
                                        <?$APPLICATION->IncludeComponent(
                                            "bitrix:system.auth.form",
                                            "",
                                            Array(
                                                "REGISTER_URL" => "",
                                                "FORGOT_PASSWORD_URL" => "",
                                                "PROFILE_URL" => "profile.php",
                                                "SHOW_ERRORS" => "Y",
                                                "AJAX_MODE" => "Y"
                                            )
                                        );?>
                                    <? } ?>


                                </div>

                                <div class="modal-forgot-pass" style="display:none">
                                    <h2 class="bx-title">Восстановление пароля</h2>
                                    <?$APPLICATION->IncludeComponent(
                                        "bitrix:system.auth.forgotpasswd",
                                        "flat",
                                        Array(
//                                            "AUTH_AUTH_URL" => "",
//                                            "AUTH_REGISTER_URL" => "",
//                                            "USE_CAPTCHA" => "Y"
                                        )
                                    );?>

                                    <div class="bx-authform-link-container p-0" style="display:none">
                                        <a href="javascript:void(0);">Авторизоваться</a>
                                    </div>
                                    <br>

                                    <div class="bx-authform-link-container p-0" style="display:none">
                                        <a href="javascript:void(0);">Зарегистрироваться</a>
                                    </div>

                                </div>

                                <div class="modal-register" style="display:none">
                                    <h2 class="bx-title">Регистрация</h2>
<!--                                    --><?//$APPLICATION->IncludeComponent(
//                                        "bitrix:main.register",
//                                        "",
//                                        Array(
//                                            "AUTH" => "Y",
//                                            "REQUIRED_FIELDS" => array(),
//                                            "SET_TITLE" => "Y",
//                                            "SHOW_FIELDS" => array(),
//                                            "SUCCESS_PAGE" => "/personal/",
//                                            "USER_PROPERTY" => array(),
//                                            "USER_PROPERTY_NAME" => "",
//                                            "USE_BACKURL" => "Y"
//                                        )
//                                    );?>

                                    <?$APPLICATION->IncludeComponent(
                                        "bitrix:main.register",
                                        "",
                                        Array(
                                            "AUTH" => "Y",
                                            "REQUIRED_FIELDS" => array(),
                                            "SET_TITLE" => "Y",
                                            "SHOW_FIELDS" => array("EMAIL", "PERSONAL_PHONE", "NAME"),
                                            "SUCCESS_PAGE" => "/catalog/to-projectors/",
                                            "USER_PROPERTY" => array(),
                                            "USER_PROPERTY_NAME" => "",
                                            "USE_BACKURL" => "Y",
                                            "AJAX_MODE" => "Y",
                                        )
                                    );?>
                                </div>

                        </div>
                    </div>
                </div>
            </div>


            <!-- Feedback Modal -->
            <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="
                            position: absolute;
                            top: 0;
                            left: 0;
                            right: 0;
                            margin: auto;
                            padding-bottom: 0;
                            padding-top: 5px;
                            padding-right: 12px;
                            border: none;
                            z-index: 200;
                        ">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" style="font-size: 30px;">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">


                            <h2 class="bx-title">Обратная связь</h2>

                            <?$APPLICATION->IncludeComponent(
                                "bitrix:main.feedback",
                                "feedback-form",
                                Array(
                                    "USE_CAPTCHA" => "N",
                                    "OK_TEXT" => "Спасибо, ваше сообщение принято.",
//                                    "EMAIL_TO" => "AscendedM@yandex.ru", /* #EMAIL_TO# */
                                    "EMAIL_TO" => "plitka@bbk39.ru", /* #EMAIL_TO# */
                                    "REQUIRED_FIELDS" => Array("NAME","EMAIL","MESSAGE"),
                                    "EVENT_MESSAGE_ID" => Array("7")
                                )
                            );?>

                        </div>
                    </div>
                </div>
            </div>


            <? if($_GET['registered'] == 'Y') { ?>
                <script>
                    $(document).ready(function() {
                        var galleryModal = new bootstrap.Modal(document.getElementById('registerEndModal'), {
                            keyboard: false
                        });


                        galleryModal.show();

                    })
                </script>
                <div class="modal fade" id="registerEndModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="
                            position: absolute;
                            top: 0;
                            left: 0;
                            right: 0;
                            margin: auto;
                            padding-bottom: 0;
                            padding-top: 5px;
                            padding-right: 12px;
                            border: none;
                            z-index: 200;
                        ">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" style="font-size: 30px;">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">


                                <h2 class="bx-title">Завершение регистрации</h2>

<!--                                <p>Проверьте вашу электронную почту, вам уже были высланы данные для активации вашей учетной записи</p>-->
                                <p>Вам были высланы данные по вашей учетной записи по email</p>
                                <p>Авторизуйтесь, чтобы скачать материалы</p>



                            </div>
                        </div>
                    </div>
                </div>
            <? } ?>
        </footer>
    </body>
</html>