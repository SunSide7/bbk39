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

<? if ($APPLICATION->GetCurPage(false) === '/catalog/') { ?>
    <style>
        .techtype-icons-catalog-sections-list {display:block !important}
        .techtype-icons-catalog-filter {display:none !important}
    </style>
<? } ?>

<div class="main_container">

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
    </div>


    <h1 class="main_title">
        Каталог
    </h1>

    <?php foreach ($arResult['SECTIONS'] as $idx => $arSection): ?>

    <!--        <h2> DEPTH:-->
    <!--            --><?php //= $arSection['RELATIVE_DEPTH_LEVEL'] ?>
    <!--        </h2>-->

    <?php
    $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
    $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
    ?>
    <? if ($arSection['RELATIVE_DEPTH_LEVEL'] == 1) { ?>

        <? if ($idx != 0) { ?>
            </div>
        <? } ?>

        <div class="catalog_title">
            <h6>
                <svg width="56" height="55" viewBox="0 0 56 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0.5" y="0.5" width="55" height="54" stroke="#003064"/>
                    <rect x="8" y="5" width="18" height="25" rx="3" fill="#003064"/>
                    <rect x="30" y="25" width="18" height="25" rx="3" fill="#003064"/>
                    <rect x="30" y="5" width="18" height="17" rx="3" fill="#003064"/>
                    <rect x="8" y="33" width="18" height="17" rx="3" fill="#003064"/>
                </svg>
                <?= $arSection['NAME'] ?>
            </h6>
            <a href="<?= $arSection['SECTION_PAGE_URL'] ?>">
                СМОТРЕТЬ ВСЕ
                <svg width="41" height="15" viewBox="0 0 41 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40.7071 8.20711C41.0976 7.81658 41.0976 7.18342 40.7071 6.79289L34.3431 0.428932C33.9526 0.0384078 33.3195 0.0384078 32.9289 0.428932C32.5384 0.819457 32.5384 1.45262 32.9289 1.84315L38.5858 7.5L32.9289 13.1569C32.5384 13.5474 32.5384 14.1805 32.9289 14.5711C33.3195 14.9616 33.9526 14.9616 34.3431 14.5711L40.7071 8.20711ZM0 8.5H40V6.5H0L0 8.5Z" fill="#003064"/>
                </svg>
            </a>
        </div>
        <div class="row catalog_card_in">

    <?
    }
    else if (
            str_contains($arSection['CODE'], 'granit') &&
            !str_contains($arSection['CODE'], 'granit-')
    )
    {
    ?>

        </div>
        <div class="catalog_title">
            <h6>
                <?= $arSection['NAME'] ?>
            </h6>
            <a href="<?= $arSection['SECTION_PAGE_URL'] ?>">
                СМОТРЕТЬ ВСЕ
                <svg width="41" height="15" viewBox="0 0 41 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40.7071 8.20711C41.0976 7.81658 41.0976 7.18342 40.7071 6.79289L34.3431 0.428932C33.9526 0.0384078 33.3195 0.0384078 32.9289 0.428932C32.5384 0.819457 32.5384 1.45262 32.9289 1.84315L38.5858 7.5L32.9289 13.1569C32.5384 13.5474 32.5384 14.1805 32.9289 14.5711C33.3195 14.9616 33.9526 14.9616 34.3431 14.5711L40.7071 8.20711ZM0 8.5H40V6.5H0L0 8.5Z" fill="#003064"/>
                </svg>
            </a>
        </div>
        <div class="row catalog_card_in">


    <?
    }
    else if (
            str_contains($arSection['CODE'], 'kollektsiya-fridrikhshtadt') &&
            !str_contains($arSection['CODE'], 'kollektsiya-fridrikhshtadt-')
    )
    {
    ?>

        </div>
        <div class="catalog_title">
            <h6>
                <?= $arSection['NAME'] ?>
            </h6>
            <a href="<?= $arSection['SECTION_PAGE_URL'] ?>">
                СМОТРЕТЬ ВСЕ
                <svg width="41" height="15" viewBox="0 0 41 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40.7071 8.20711C41.0976 7.81658 41.0976 7.18342 40.7071 6.79289L34.3431 0.428932C33.9526 0.0384078 33.3195 0.0384078 32.9289 0.428932C32.5384 0.819457 32.5384 1.45262 32.9289 1.84315L38.5858 7.5L32.9289 13.1569C32.5384 13.5474 32.5384 14.1805 32.9289 14.5711C33.3195 14.9616 33.9526 14.9616 34.3431 14.5711L40.7071 8.20711ZM0 8.5H40V6.5H0L0 8.5Z" fill="#003064"/>
                </svg>
            </a>
        </div>
        <div class="row catalog_card_in">


    <?
    }

    else if (str_contains($arSection['CODE'], 'gruppa'))
    {
        ?>


        <a href="<?= $arSection['SECTION_PAGE_URL'] ?>" class="col-lg-3 col-6">
            <div class="catalog_card">
                <div class="catalog_card_content">
                    <h6><?= $arSection['NAME'] ?></h6>
                    <div class="icons">
                        <?= $arSection['DESCRIPTION'] ?>
                    </div>
                </div>

                <div class="card_block">
                    <div class="card-inner" style="--clr:#fff;">
                        <div class="box">
                            <div class="imgBox">
                                <img src="<?= $arSection['PICTURE']['SRC'] ?>" alt="">
                            </div>
                            <div class="icon btn_blue">
                                <div  class="iconBox">
                                    <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>



        <?
    }
    else
    {
        ?>


        <a href="<?= $arSection['SECTION_PAGE_URL'] ?>" class="col-lg-3 col-6">
            <div class="catalog_card">
                <div class="catalog_card_content">
                    <h6><?= $arSection['NAME'] ?></h6>
                    <div class="icons">
                        <?= $arSection['DESCRIPTION'] ?>
                    </div>
                </div>

                <div class="card_block">
                    <div class="card-inner" style="--clr:#fff;">
                        <div class="box">
                            <div class="imgBox">
                                <img src="<?= $arSection['PICTURE']['SRC'] ?>" alt="">
                            </div>
                            <div class="icon btn_blue">
                                <div  class="iconBox">
                                    <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>



        <?
    }
    ?>

    <?php endforeach; ?>

</div>
