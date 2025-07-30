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
<div class="news-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>


<!-- EXAMPLE VERSION -->
<div class="main_container">

        <ul>

            <?foreach ($arResult["SECTIONS"] as $arIdx => $arSection):?>

                <? if(!$arSection['IBLOCK_SECTION_ID']) { ?>

                    <li>

                        <!-- LVL 0 -->
                        <h4>[<?=$arIdx?>]: <?=$arSection["NAME"]?> [LVL 0]</h4>
                        <img style="width: 200px;" src="<?= CFile::GetFileArray($arSection['PICTURE'])['SRC'] ?>" alt="">

                        <!-- LVL 1 -->
                        <ul>
                            <?foreach ($arSection["ITEMS"] as $arItem):?>
                                <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">

                                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?> [LVL 1]</a>

                                    <?
                                    // Custom properties for images:
                                    //      -> PREVIEW_PICTURE
                                    //      -> DETAIL_PICTURE
                                    ?>
                                    <div class="images-wrapper">
                                        <img style="width: 200px;" src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="">
                                        <img style="width: 200px;" src="<?= $arItem['DETAIL_PICTURE']['SRC'] ?>" alt="">
                                    </div>


                                    <?// echo '<pre>' . print_r($arItem, true) . '</pre>'; ?>

                                </li>
                            <?endforeach?>
                        </ul>
                    </li>


                    <?foreach ($arResult["SECTIONS"] as $arSection):?>
                        <? if($arSection['IBLOCK_SECTION_ID'] && $arIdx == $arSection['IBLOCK_SECTION_ID']) { ?>

                            <li>
                                <!-- LVL 1 -->
                                <h4> -> <?=$arSection["NAME"]?> [LVL1]</h4>

                                <ul>
                                    <!-- LVL 2 -->
                                    <?foreach ($arSection["ITEMS"] as $arItem):?>
                                        <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
                                        </li>
                                    <?endforeach?>
                                </ul>
                            </li>

                        <? } ?>
                    
                    <?endforeach?>

                <? } ?>

            <?endforeach?>

        </ul>

</div>


<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
