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

<!--    --><?// echo '<pre>' . print_r($arResult, true) . '</pre>' ?>

    <?foreach ($arResult["SECTIONS"] as $arSection):?>
        <h4><?=$arSection["NAME"]?></h4>
        <ul>
            <?foreach ($arSection["ITEMS"] as $arItem):?>
                <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
                </li>

                <?= $arItem['DISPLAY_PROPERTIES']['DOCUMENT']['FILE_VALUE']['SRC'] ?>
            <?endforeach?>
        </ul>
    <?endforeach?>
</div>
