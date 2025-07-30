<?php
/*
 * Файл local/templates/voguis_index/components/bitrix/catalog.section.list/blog_ctgs/template.php
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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

// Редактировать или удалить раздел с морды сайта
$strSectionEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_EDIT');
$strSectionDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_DELETE');
$arSectionDeleteParams = array('CONFIRM' => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
?>


<!-- http://bbk-dev.twc1.net/catalog/trotuarnaya-plitka/kollektsiya-fridrikhshtadt/ -->
<div class="1234_1 catalog_content active">

    <? // TODO -> AR_RESULT SECTIONS IS EMPTY IF CHPU IS ACTIVE ?>
    <? echo '<pre style="display:none">' . print_r($arResult['SECTIONS'], true) . '</pre>'; ?>

    <?php foreach ($arResult['SECTIONS'] as $idx => $arSection): ?>

        <?php
        $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
        $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
        ?>
        <? if ($arSection['RELATIVE_DEPTH_LEVEL'] == 1) { ?>
            <?// echo '<pre>' . print_r($arSection, true) . '</pre>' ?>

                <!-- button -->
                <a href="<?= $arSection['SECTION_PAGE_URL']; ?>" class="title_block">
                    <img style="max-width: 23px;max-height:23px;" src="<?= $arSection['PICTURE']['SRC'] ?>" alt="">
                    <?php echo $arSection['NAME']; ?>
                </a>

            <?
        } else if (
                str_contains($arSection['CODE'], 'kollektsiya-') ||
                str_contains($arSection['CODE'], 'groups')
        ) {
            // Do not show items
        }
        else {
            ?>
                <a class="catalog_in_block" href="<?php echo $arSection['SECTION_PAGE_URL']; ?>">
                    <span>
                        <?php echo $arSection['NAME']; ?>
                    </span>

                    <? if ($arSection['DESCRIPTION'] != '') { ?>
                    <span class="icons">
                        <?= $arSection['DESCRIPTION'] ?>
                    </span>
                    <? } ?>
                </a>
            <?
        }
        ?>
    <?php endforeach; ?>
</div>
