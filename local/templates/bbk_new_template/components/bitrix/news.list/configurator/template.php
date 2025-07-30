<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<? // echo '<pre>' . print_r($arResult['ITEMS'], true) . '</pre>'; ?>


<? if(true) { ?>

    <!-- MAIN VERSION -->
    <div class="main_container">
        <div id="configurator" class="configurator">
            <div class="configurator-sidebar">
<!--                --><?// echo '<pre>' . print_r($arResult["SECTIONS"], true) . '</pre>'; ?>
<!--                --><?// echo '<pre>' . print_r($arResult["ITEMS"], true) . '</pre>'; ?>
                <ul>

                    <?
                    $levelZeroIdx = 0;
                    ?>
                    <?foreach ($arResult["SECTIONS"] as $arIdx => $arSection):?>
<!--                        --><?// echo '<pre>' . print_r($arResult["SECTIONS"], true) . '</pre>'; ?>

                        <?
                        if(!$arSection['IBLOCK_SECTION_ID']) { ?>

                            <? $levelZeroIdx+=1; ?>
                            <!-- X1 LEVEL -->
                            <li>
                                <ul class="configurator-sidebar-option">
                                    <span class="configurator-sidebar-option-title">
                                        <img src="<?= CFile::GetFileArray($arSection['PICTURE'])['SRC'] ?>" alt="">
                                        <span><?= $arSection['NAME'] ?></span>
                                    </span>
                                    <span class="configurator-sidebar-option-dialog"><?= $arSection['DESCRIPTION'] ?></span>
                                    <?foreach ($arSection["ITEMS"] as $arIdxItem => $arItem):?>

                                    <!--    --><?// echo '$arSection["ITEMS"]: <pre>' . print_r($arSection["ITEMS"], true) . '</pre>'; ?>


                                        <li class="lvl-1" <? if($arIdx == '473') echo 'data-num="' . $arIdxItem + 1 . '"' ?>>

                                            <img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt=""><span><?= $arItem['NAME'] ?></span>

                                            <div class="content">
                                                <img src="<?= CFile::GetFileArray($arItem['PROPERTIES']['ITEM_MAIN_PICTURE_1']['VALUE'])['SRC'] ?>" alt="">
                                            </div>
                                            <div class="content-2">
                                                <img src="<?= CFile::GetFileArray($arItem['PROPERTIES']['ITEM_MAIN_PICTURE_2']['VALUE'])['SRC'] ?>" alt="">
                                            </div>


                                            <?foreach ($arResult["SECTIONS"] as $arSection):?>
                                                <? if($arSection['IBLOCK_SECTION_ID'] && $arIdx == $arSection['IBLOCK_SECTION_ID']) { ?>
                                                    <div class="lvl-2-content"  data-idx="<?= $arIdxItem + 1 ?>">
                                                        <ul>
                                                            <li>
                                                                <img src="<?= CFile::GetFileArray($arItem['PROPERTIES']['ITEM_PREVIEW_PICTURE_1']['VALUE'])['SRC'] ?>" alt=""><span>Стены Lvl 2-1 #1</span>
                                                                <div class="content"><img src="<?= CFile::GetFileArray($arItem['PROPERTIES']['ITEM_MAIN_PICTURE_1']['VALUE'])['SRC'] ?>" alt=""></div>
                                                                <div class="content-2"><img src="<?= CFile::GetFileArray($arItem['PROPERTIES']['ITEM_MAIN_PICTURE_2']['VALUE'])['SRC'] ?>" alt=""></div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                <? } ?>
                                            <?endforeach?>


                                        </li>

                                    <?endforeach?>


                                    <!-- TEMPLATE LVL 2 FOREACH -->
                                    <?foreach ($arResult["SECTIONS"] as $arSection):?>
                                        <? if($arSection['IBLOCK_SECTION_ID'] && $arIdx == $arSection['IBLOCK_SECTION_ID']) { ?>

                                            <!-- X2 LEVEL -->
                                            <li>
                                                <ul class="configurator-sidebar-option">
                                                    <span class="configurator-sidebar-option-title">
                                                        <img src="<?= CFile::GetFileArray($arSection['PICTURE'])['SRC'] ?>" alt="">
                                                        <span><?=$arSection["NAME"]?></span>
                                                    </span>
                                                    <span class="configurator-sidebar-option-dialog">Нажмите чтобы выбрать фасад</span>

                                                    <li class="lvl-1">
                                                        <img src="<?= CFile::GetFileArray($arSection['PICTURE'])['SRC'] ?>" alt=""><span><?= $arSection['NAME'] ?></span>

                                                        <div class="lvl-2-content" data-id="<?= $arIdxItem + 1 ?>">
                                                            <ul>
                                                                <?foreach ($arSection["ITEMS"] as $arItem):?>
                                                                    <li
                                                                            <? if(
                                                                                $arItem['PROPERTIES']['PLATE_NAME']['VALUE'] &&
                                                                                $arItem['PROPERTIES']['PLATE_COLOR']['VALUE'] &&
                                                                                $arItem['PROPERTIES']['PLATE_SIZE']['VALUE'] &&
                                                                                $arItem['PROPERTIES']['PLATE_WEIGHT']['VALUE']
                                                                            ) {
                                                                                echo
                                                                                    'class="plate-item"' .
                                                                                    'data-plate-name="' . $arItem['PROPERTIES']['PLATE_NAME']['VALUE'] . '" ' .
                                                                                    'data-plate-color="' . $arItem['PROPERTIES']['PLATE_COLOR']['VALUE'] . '" ' .
                                                                                    'data-plate-size="' . $arItem['PROPERTIES']['PLATE_SIZE']['VALUE'] . '" ' .
                                                                                    'data-plate-weight="' . $arItem['PROPERTIES']['PLATE_WEIGHT']['VALUE'] . '" ' .
                                                                                    'data-plate-picture="' . CFile::GetFileArray($arItem['PROPERTIES']['PLATE_PICTURE']['VALUE'])['SRC'] . '" ';

                                                                            }?>
                                                                    >
                                                                        <? if($levelZeroIdx === 4) { ?>

                                                                            <?// Для плитки ?>
                                                                            <img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt=""><span><?= $arItem["NAME"] ?></span>
                                                                            <div class="content content-2"><img src="<?= CFile::GetFileArray($arItem['PROPERTIES']['ITEM_PLATE_PICTURE']['VALUE'])['SRC'] ?>" alt=""></div>
                                                                            <? if(false) { ?>
                                                                                <div class="content-2"><img src="<?= CFile::GetFileArray($arItem['PROPERTIES']['ITEM_PLATE_PICTURE']['VALUE'])['SRC'] ?>" alt=""></div>
                                                                            <? } ?>

                                                                        <? } else { ?>

                                                                            <?// Для всех свойств кроме плитки ?>
                                                                            <img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt=""><span><?= $arItem["NAME"] ?></span>
                                                                            <div class="content"><img src="<?= CFile::GetFileArray($arItem['PROPERTIES']['ITEM_MAIN_PICTURE_1']['VALUE'])['SRC'] ?>" alt=""></div>
                                                                            <div class="content-2"><img src="<?= CFile::GetFileArray($arItem['PROPERTIES']['ITEM_MAIN_PICTURE_2']['VALUE'])['SRC'] ?>" alt=""></div>

                                                                        <? } ?>
                                                                    </li>
                                                                <?endforeach?>
                                                            </ul>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </li>


                                        <? } ?>
                                    <?endforeach?>



                                </ul>
                            </li>


                        <? } ?>

                    <?endforeach?>

                </ul>
            </div>
            <div
                    class="configurator-scaffold"
                    style="
                            /*background: url(*/<?php //= SITE_TEMPLATE_PATH ?>/*/images/configurator/basement.jpg) center center no-repeat;*/
                            /*background-size: cover;*/
                            "
            >

                <img class="scaffold-bg-img" src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/basement.jpg" alt="">

                <div class="list-1">
                    <ul class="list-1-ul"></ul>
                </div>

                <div class="list-2">
                    <ul class="list-2-ul"></ul>
                </div>

                <ul class="target-ul">
                    <li class="target"></li>
                    <li class="target"></li>
                    <li class="target"></li>
                    <li class="target"></li>
                </ul>
            </div>
        </div>
    </div>


<? }
else if(false)
{ ?>

    <!-- HTML MARKUP VERSION -->
    <br>

    <div class="main_container">
        <div id="configurator" class="configurator">
            <div class="configurator-sidebar">
                <ul>

                    <li>
                        <ul class="configurator-sidebar-option">
                        <span class="configurator-sidebar-option-title">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/option-1.png" alt="">
                            <span>Выберите дом</span>
                        </span>
                            <span class="configurator-sidebar-option-dialog">Выберите дом чтобы начать</span>
                            <li class="lvl-1" data-num="1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Этаж 1</span>
                                <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/house-1-shadow.png" alt=""></div>
                            </li>
                            <li class="lvl-1" data-num="2">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Этаж 2</span>
                                <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/house-2-shadow.png" alt=""></div>
                            </li>
                        </ul>
                    </li>

                    <!-- Крыша -->
                    <li>
                        <ul class="configurator-sidebar-option">
                        <span class="configurator-sidebar-option-title">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/option-2.png" alt="">
                            <span>Выберите цвет крыши</span>
                        </span>
                            <span class="configurator-sidebar-option-dialog">Нажмите чтобы выбрать крышу</span>
                            <li class="lvl-1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Крыша 1</span>
                                <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/2_1.png" alt=""></div>
                                <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/2_1-2.png" alt=""></div>
                            </li>
                            <li class="lvl-1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Крыша 2</span>
                                <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/2_2.png" alt=""></div>
                                <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/2_2-2.png" alt=""></div>
                            </li>
                            <li class="lvl-1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Крыша 2</span>
                                <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/2_3.png" alt=""></div>
                                <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/2_3-2.png" alt=""></div>
                            </li>
                        </ul>
                    </li>

                    <!-- Стены -->
                    <li>
                        <ul class="configurator-sidebar-option">
                        <span class="configurator-sidebar-option-title">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/option-3.png" alt="">
                            <span>Выберите цвет фасада</span>
                        </span>
                            <span class="configurator-sidebar-option-dialog">Нажмите чтобы выбрать фасад</span>
                            <li class="lvl-1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Стены Lvl 1-1</span>
                                <div class="lvl-2-content">
                                    <ul>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Стены Lvl 2-1 #1</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1-2.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-2 #1</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2-2.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-3 #1</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3-2.png" alt=""></div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="lvl-1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 1-2</span>
                                <div class="lvl-2-content">
                                    <ul>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Стены Lvl 2-1 #2</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1-2.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-2 #2</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2-2.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-3 #2</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3-2.png" alt=""></div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="lvl-1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 1-3</span>
                                <div class="lvl-2-content">
                                    <ul>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Стены Lvl 2-1 #3</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1-2.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-2 #3</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2-2.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-2 #3</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3-2.png" alt=""></div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </li>

                    <!-- Плитка -->
                    <li>
                        <ul class="configurator-sidebar-option">
                        <span class="configurator-sidebar-option-title">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/option-4.png" alt="">
                            <span>Выберите плитку</span>
                        </span>
                            <span class="configurator-sidebar-option-dialog">Нажмите чтобы выбрать плитку</span>
                            <li class="lvl-1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Плитка 1</span>
                                <div class="lvl-2-content">
                                    <ul>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Плитка Lvl2-1 #1</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-1.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-1.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Плитка Lvl2-2 #1</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-2.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-2.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Плитка Lvl2-2 #1</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-3.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-3.png" alt=""></div>
                                        </li>


                                    </ul>
                                </div>
                            </li>
                            <li class="lvl-1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Плитка 2</span>
                                <div class="lvl-2-content">
                                    <ul>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Плитка Lvl2-1 #2</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-1.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-1.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Плитка Lvl2-2 #2</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-2.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-2.png" alt=""></div>
                                        </li>
                                        <li>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Плитка Lvl2-2 #2</span>
                                            <div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-3.png" alt=""></div>
                                            <div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/plats-3.png" alt=""></div>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!--						<li class="lvl-1">-->
                            <!--							<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Стены Lvl 1-1</span>-->
                            <!--							<div class="lvl-2-content">-->
                            <!--								<ul>-->
                            <!--									<li>-->
                            <!--										<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Стены Lvl 2-1 #1</span>-->
                            <!--										<div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1.png" alt=""></div>-->
                            <!--										<div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1-2.png" alt=""></div>-->
                            <!--									</li>-->
                            <!--									<li>-->
                            <!--										<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-2 #1</span>-->
                            <!--										<div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2.png" alt=""></div>-->
                            <!--										<div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2-2.png" alt=""></div>-->
                            <!--									</li>-->
                            <!--									<li>-->
                            <!--										<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-3 #1</span>-->
                            <!--										<div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3.png" alt=""></div>-->
                            <!--										<div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3-2.png" alt=""></div>-->
                            <!--									</li>-->
                            <!--								</ul>-->
                            <!--							</div>-->
                            <!--						</li>-->
                            <!--						<li class="lvl-1">-->
                            <!--							<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 1-2</span>-->
                            <!--							<div class="lvl-2-content">-->
                            <!--								<ul>-->
                            <!--									<li>-->
                            <!--										<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Стены Lvl 2-1 #2</span>-->
                            <!--										<div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1.png" alt=""></div>-->
                            <!--										<div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1-2.png" alt=""></div>-->
                            <!--									</li>-->
                            <!--									<li>-->
                            <!--										<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-2 #2</span>-->
                            <!--										<div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2.png" alt=""></div>-->
                            <!--										<div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2-2.png" alt=""></div>-->
                            <!--									</li>-->
                            <!--									<li>-->
                            <!--										<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-3 #2</span>-->
                            <!--										<div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3.png" alt=""></div>-->
                            <!--										<div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3-2.png" alt=""></div>-->
                            <!--									</li>-->
                            <!--								</ul>-->
                            <!--							</div>-->
                            <!--						</li>-->
                            <!--						<li class="lvl-1">-->
                            <!--							<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 1-3</span>-->
                            <!--							<div class="lvl-2-content">-->
                            <!--								<ul>-->
                            <!--									<li>-->
                            <!--										<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-1.png" alt=""><span>Стены Lvl 2-1 #3</span>-->
                            <!--										<div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1.png" alt=""></div>-->
                            <!--										<div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_1-2.png" alt=""></div>-->
                            <!--									</li>-->
                            <!--									<li>-->
                            <!--										<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-2 #3</span>-->
                            <!--										<div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2.png" alt=""></div>-->
                            <!--										<div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_2-2.png" alt=""></div>-->
                            <!--									</li>-->
                            <!--									<li>-->
                            <!--										<img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/floor-option-2.png" alt=""><span>Стены Lvl 2-2 #3</span>-->
                            <!--										<div class="content"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3.png" alt=""></div>-->
                            <!--										<div class="content-2"><img src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/3_3-2.png" alt=""></div>-->
                            <!--									</li>-->
                            <!--								</ul>-->
                            <!--							</div>-->
                            <!--						</li>-->
                        </ul>
                    </li>

                </ul>
            </div>
            <div
                    class="configurator-scaffold"
                    style="
                            /*background: url(*/<?php //= SITE_TEMPLATE_PATH ?>/*/images/configurator/basement.jpg) center center no-repeat;*/
                            /*background-size: cover;*/
                            "
            >

                <img class="scaffold-bg-img" src="<?= SITE_TEMPLATE_PATH ?>/images/configurator/basement.jpg" alt="">

                <div class="list-1">
                    <ul class="list-1-ul"></ul>
                </div>

                <div class="list-2">
                    <ul class="list-2-ul"></ul>
                </div>

                <ul class="target-ul">
                    <li class="target"></li>
                    <li class="target"></li>
                    <li class="target"></li>
                    <li class="target"></li>
                </ul>
            </div>

        </div>

    </div>

    <br>


<? } ?>




