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



<div class="breadcrumb_block active">
    <a  href="/" class="title_block">
        Главная
    </a>
    <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4.5 4.25L0 7.25L0 0.75L4.5 4.25Z" fill="#999999"></path>
    </svg>
    <a  href="/catalog/" class="title_block">
        Каталог
    </a>
    <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4.5 4.25L0 7.25L0 0.75L4.5 4.25Z" fill="#999999"></path>
    </svg>

    <!--    --><?// echo '1234<pre>' . print_r($arResult['SECTIONS'], true) . '</pre>'; ?>


    <?php foreach ($arResult['SECTIONS'] as $idx => $arSection): ?>


        <?php
        $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
        $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
        ?>
        <? if ($arSection['RELATIVE_DEPTH_LEVEL'] == 1) { ?>
            <script>
                $('.breadcrumb_item_lvl_1').remove();
                $('.breadcrumb_item_lvl_1_arr').remove();
            </script>

<!--            $arSection-->
            <a class="breadcrumb_item_lvl_1" href="<?= $arSection['SECTION_PAGE_URL']; ?>" class="title_block">
                <?php echo $arSection['NAME']; ?>
            </a>

        <? if($APPLICATION->GetCurPage(false) === $arSection['SECTION_PAGE_URL']) {
            break;
        } else { ?>
            <svg class="breadcrumb_item_lvl_1_arr" width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.5 4.25L0 7.25L0 0.75L4.5 4.25Z" fill="#999999"></path>
            </svg>
        <? } ?>

        <?
        } else if (
        //                str_contains($arSection['CODE'], 'kollektsiya-') ||
        str_contains($arSection['CODE'], 'groups')
        ) {
            // Do not show items
        }
        else {
        ?>

        <? if($APPLICATION->GetCurPage(false) === $arSection['SECTION_PAGE_URL']) { ?>
            <a class="catalog_in_block" href="<?php echo $arSection['SECTION_PAGE_URL']; ?>">
                        <span>
                            <?php echo $arSection['NAME']; ?>
                        </span>

            </a>
            <?
            break;
        } ?>
            <?
        }
        ?>
    <?php endforeach; ?>

    <? if($arResult['PRODUCT_CASE']) { ?>
        > <?= $arResult['PRODUCT_CASE'] ?>
    <? } ?>
</div>
