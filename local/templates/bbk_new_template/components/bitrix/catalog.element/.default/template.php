<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

global $USER;

$this->setFrameMode(true);
$this->addExternalCss('/bitrix/css/main/bootstrap.css');

$templateLibrary = array('popup', 'fx', 'ui.fonts.opensans');
$currencyList = '';

if (!empty($arResult['CURRENCIES']))
{
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$haveOffers = !empty($arResult['OFFERS']);

$templateData = [
	'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList,
	'ITEM' => [
		'ID' => $arResult['ID'],
		'IBLOCK_ID' => $arResult['IBLOCK_ID'],
	],
];
if ($haveOffers)
{
	$templateData['ITEM']['OFFERS_SELECTED'] = $arResult['OFFERS_SELECTED'];
	$templateData['ITEM']['JS_OFFERS'] = $arResult['JS_OFFERS'];
}
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
	'ID' => $mainId,
	'DISCOUNT_PERCENT_ID' => $mainId.'_dsc_pict',
	'STICKER_ID' => $mainId.'_sticker',
	'BIG_SLIDER_ID' => $mainId.'_big_slider',
	'BIG_IMG_CONT_ID' => $mainId.'_bigimg_cont',
	'SLIDER_CONT_ID' => $mainId.'_slider_cont',
	'OLD_PRICE_ID' => $mainId.'_old_price',
	'PRICE_ID' => $mainId.'_price',
	'DESCRIPTION_ID' => $mainId.'_description',
	'DISCOUNT_PRICE_ID' => $mainId.'_price_discount',
	'PRICE_TOTAL' => $mainId.'_price_total',
	'SLIDER_CONT_OF_ID' => $mainId.'_slider_cont_',
	'QUANTITY_ID' => $mainId.'_quantity',
	'QUANTITY_DOWN_ID' => $mainId.'_quant_down',
	'QUANTITY_UP_ID' => $mainId.'_quant_up',
	'QUANTITY_MEASURE' => $mainId.'_quant_measure',
	'QUANTITY_LIMIT' => $mainId.'_quant_limit',
	'BUY_LINK' => $mainId.'_buy_link',
	'ADD_BASKET_LINK' => $mainId.'_add_basket_link',
	'BASKET_ACTIONS_ID' => $mainId.'_basket_actions',
	'NOT_AVAILABLE_MESS' => $mainId.'_not_avail',
	'COMPARE_LINK' => $mainId.'_compare_link',
	'TREE_ID' => $mainId.'_skudiv',
	'DISPLAY_PROP_DIV' => $mainId.'_sku_prop',
	'DISPLAY_MAIN_PROP_DIV' => $mainId.'_main_sku_prop',
	'OFFER_GROUP' => $mainId.'_set_group_',
	'BASKET_PROP_DIV' => $mainId.'_basket_prop',
	'SUBSCRIBE_LINK' => $mainId.'_subscribe',
	'TABS_ID' => $mainId.'_tabs',
	'TAB_CONTAINERS_ID' => $mainId.'_tab_containers',
	'SMALL_CARD_PANEL_ID' => $mainId.'_small_card_panel',
	'TABS_PANEL_ID' => $mainId.'_tabs_panel'
);
$obName = $templateData['JS_OBJ'] = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
	: $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
	: $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
	: $arResult['NAME'];

if ($haveOffers)
{
	$actualItem = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
	$showSliderControls = false;

	foreach ($arResult['OFFERS'] as $offer)
	{
		if ($offer['MORE_PHOTO_COUNT'] > 1)
		{
			$showSliderControls = true;
			break;
		}
	}
}
else
{
	$actualItem = $arResult;
	$showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

if ($arParams['SHOW_SKU_DESCRIPTION'] === 'Y')
{
	$skuDescription = false;
	foreach ($arResult['OFFERS'] as $offer)
	{
		if ($offer['DETAIL_TEXT'] != '' || $offer['PREVIEW_TEXT'] != '')
		{
			$skuDescription = true;
			break;
		}
	}
	$showDescription = $skuDescription || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}
else
{
	$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}

$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);
$productType = $arResult['PRODUCT']['TYPE'];

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');

if ($arResult['MODULES']['catalog'] && $arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE)
{
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;
}
else
{
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;
}

$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

$positionClassMap = array(
	'left' => 'product-item-label-left',
	'center' => 'product-item-label-center',
	'right' => 'product-item-label-right',
	'bottom' => 'product-item-label-bottom',
	'middle' => 'product-item-label-middle',
	'top' => 'product-item-label-top'
);

$discountPositionClass = 'product-item-label-big';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
	{
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$labelPositionClass = 'product-item-label-big';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
	{
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}
?>

<?
CModule::IncludeModule('highloadblock');
use Bitrix\Highloadblock as HL;

// Получаем список всех Highload блоков
$hlblockList = [];
$hlblockIterator = HL\HighloadBlockTable::getList();
while ($hlblock = $hlblockIterator->fetch()) {
    $hlblockList[] = $hlblock;
}

// Выводим список Highload блоков
foreach ($hlblockList as $hlblock) {
//    echo "ID: {$hlblock['ID']}, Название: {$hlblock['NAME']}" . '<br>';
//    echo '<pre>' . print_r($hlblock, true) . '</pre>';

}
?>


<?
$i = iconv_strlen($APPLICATION->GetCurPage(false)) - 1;
$sectionUrl = null;

while($i !== 0) {

    if ($i !== iconv_strlen($APPLICATION->GetCurPage(false)) - 1 && $APPLICATION->GetCurPage(false)[$i] == '/') {
        $sectionUrl = mb_substr($APPLICATION->GetCurPage(false), 0, $i + 1);
        break;
    }

    $i--;
}

?>
<div class="product_card">
    <div class="main_container">

        <div class="breadcrumb_block active">
            <a  href="/" class="title_block">
                Главная
            </a>
            <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.5 4.25L0 7.25L0 0.75L4.5 4.25Z" fill="#999999"></path>
            </svg>
            <a href="/catalog/" class="title_block">
                Каталог
            </a>
            <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.5 4.25L0 7.25L0 0.75L4.5 4.25Z" fill="#999999"></path>
            </svg>

<!--            --><?// echo '<pre>' . print_r($arResult, true) . '</pre>'; ?>

            <?// Breadcrumbs [Detail]
            $resSection = CIBlockSection::GetNavChain(
                false,
                $arResult['IBLOCK_SECTION_ID'],
            );
            $chainUrl = '';
            while ($arSection = $resSection->GetNext()) {?>
                <? if($arSection['NAME'] != 'Группы эксплуатации') { ?>

                    <? if($arSection['SECTION_PAGE_URL'] && $arSection['SECTION_PAGE_URL'] != '') {
                        $chainUrl = $chainUrl . $arSection['SECTION_PAGE_URL'];
                    } ?>

                    <a href="/catalog/<?=$chainUrl;?>" class="">
                        <?=$arSection['NAME'];?>
                    </a>
                    <svg width="5" height="8" viewBox="0 0 5 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.5 4.25L0 7.25L0 0.75L4.5 4.25Z" fill="#999999"></path>
                    </svg>
                <? } ?>
            <?}?>

            <span class="title_block">
                <?= $arResult['NAME'] ?>
            </span>
        </div>

    </div>




<!--    --><?// include($_SERVER("DOCUMENT_ROOT"). "/include/blocks/sku/properties_in_pict.php"); ?>

    <?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        Array(
            "AREA_FILE_SHOW" => "file",
            "AREA_FILE_SUFFIX" => "inc",
            "EDIT_TEMPLATE" => "",
            "PATH" => "/include/blocks/sku/properties_in_pict.php"
        )
    );?>

    <div class="main_container">
        <h1 class="main_title"><?= $arResult['NAME'] ?></h1>
    </div>

    <div class="row product_card_in" id="<?=$itemIds['ID']?>" itemscope itemtype="http://schema.org/Product">
            <?php
            if (false && $arParams['DISPLAY_NAME'] === 'Y')
            {
                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="bx-title"><?=$name?></h1>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="main_container">
                <? // echo '<pre>' . print_r(CIBlockElement::GetElementGroups($arResult['ELEMENT_ID']), true) . '</pre>' ?>

                <?
                $imgSliderArr = [];
                $img = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array(), BX_RESIZE_IMAGE_EXACT, true);
                $imgSliderArr[0] = $img['src']; // [SRC]
                foreach($arResult['PROPERTIES']['PROP_PICTURES_SLIDER']['VALUE'] as $idx => $id) {
                    $imgSliderArr[$idx + 1] = CFile::GetFileArray($id)['SRC'];
                }
                ?>

                <? /* Picture slider */ ?>
                <div class="col-lg-6 product-picture-slider">

                    <div class="theiaStickySidebar">

                        <div class="product_card_left">
                            <div class="swiper productCard2">
                                <div class="swiper-wrapper">
                                    <? foreach($imgSliderArr as $sliderImg) { ?>
                                        <div class="swiper-slide">
                                            <img src="<?=$sliderImg?>" alt=""/>
                                        </div>
                                    <? } ?>
                                </div>
                            </div>
                            <div thumbsSlider="" class="swiper productCard">
                                <div class="swiper-wrapper">
                                    <? foreach($imgSliderArr as $sliderImg) { ?>
                                        <div class="swiper-slide">
                                            <img src="<?=$sliderImg?>" alt=""/>
                                        </div>
                                    <? } ?>
                                </div>
                            </div>
                        </div>

                        <? if(false) { ?>
                        <div class="product-item-detail-slider-container" id="<?=$itemIds['BIG_SLIDER_ID']?>">
                            <span class="product-item-detail-slider-close" data-entity="close-popup"></span>
                            <div class="product-item-detail-slider-block
                                <?=($arParams['IMAGE_RESOLUTION'] === '1by1' ? 'product-item-detail-slider-block-square' : '')?>"
                                data-entity="images-slider-block">
                                <span class="product-item-detail-slider-left" data-entity="slider-control-left" style="display: none;"></span>
                                <span class="product-item-detail-slider-right" data-entity="slider-control-right" style="display: none;"></span>
                                <div class="product-item-label-text <?=$labelPositionClass?>" id="<?=$itemIds['STICKER_ID']?>"
                                    <?=(!$arResult['LABEL'] ? 'style="display: none;"' : '' )?>>
                                    <?php
                                    if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE']))
                                    {
                                        foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value)
                                        {
                                            ?>
                                            <div<?=(!isset($arParams['LABEL_PROP_MOBILE'][$code]) ? ' class="hidden-xs"' : '')?>>
                                                <span title="<?=$value?>"><?=$value?></span>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php
                                if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y')
                                {
                                    if ($haveOffers)
                                    {
                                        ?>
                                        <div class="product-item-label-ring <?=$discountPositionClass?>" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>"
                                            style="display: none;">
                                        </div>
                                        <?php
                                    }
                                    else
                                    {
                                        if ($price['DISCOUNT'] > 0)
                                        {
                                            ?>
                                            <div class="product-item-label-ring <?=$discountPositionClass?>" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>"
                                                title="<?=-$price['PERCENT']?>%">
                                                <span><?=-$price['PERCENT']?>%</span>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                                <div class="product-item-detail-slider-images-container" data-entity="images-container">
                                    <?php
                                    if (!empty($actualItem['MORE_PHOTO']))
                                    {
                                        foreach ($actualItem['MORE_PHOTO'] as $key => $photo)
                                        {
                                            ?>
                                            <div class="product-item-detail-slider-image<?=($key == 0 ? ' active' : '')?>" data-entity="image" data-id="<?=$photo['ID']?>">
                                                <img src="<?=$photo['SRC']?>" alt="<?=$alt?>" title="<?=$title?>"<?=($key == 0 ? ' itemprop="image"' : '')?>>
                                            </div>
                                            <?php
                                        }
                                    }

                                    if ($arParams['SLIDER_PROGRESS'] === 'Y')
                                    {
                                        ?>
                                        <div class="product-item-detail-slider-progress-bar" data-entity="slider-progress-bar" style="width: 0;"></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            if ($showSliderControls)
                            {
                                if ($haveOffers)
                                {
                                    foreach ($arResult['OFFERS'] as $keyOffer => $offer)
                                    {
                                        if (!isset($offer['MORE_PHOTO_COUNT']) || $offer['MORE_PHOTO_COUNT'] <= 0)
                                            continue;

                                        $strVisible = $arResult['OFFERS_SELECTED'] == $keyOffer ? '' : 'none';
                                        ?>
                                        <div class="product-item-detail-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_OF_ID'].$offer['ID']?>" style="display: <?=$strVisible?>;">
                                            <?php
                                            foreach ($offer['MORE_PHOTO'] as $keyPhoto => $photo)
                                            {
                                                ?>
                                                <div class="product-item-detail-slider-controls-image<?=($keyPhoto == 0 ? ' active' : '')?>"
                                                    data-entity="slider-control" data-value="<?=$offer['ID'].'_'.$photo['ID']?>">
                                                    <img src="<?=$photo['SRC']?>">
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                else
                                {
                                    ?>
                                    <div class="product-item-detail-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_ID']?>">
                                        <?php
                                        if (!empty($actualItem['MORE_PHOTO']))
                                        {
                                            foreach ($actualItem['MORE_PHOTO'] as $key => $photo)
                                            {
                                                ?>
                                                <div class="product-item-detail-slider-controls-image<?=($key == 0 ? ' active' : '')?>"
                                                    data-entity="slider-control" data-value="<?=$photo['ID']?>">
                                                    <img src="<?=$photo['SRC']?>">
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <? } ?>

                    </div>

                </div>

                <div class="col-lg-6">
                    <div class="product_card_right">
                        <?
                        global $USER;
                        if (false && $USER->IsAuthorized()) echo "<h4>Вы авторизованы! " . $USER->GetFullName() . "</h4>";
                        ?>

                        <div>
                            <?
                            $elementSectionNames = [];
                            $ElementId = $arResult['ID'];
                            $db_groups = CIBlockElement::GetElementGroups($ElementId, true);
                            while($ar_group = $db_groups->Fetch()) {
                                $elementSectionNames[] = $ar_group["NAME"];
                            }

                            foreach($elementSectionNames as $elementSectionName) {
                                if (str_contains($elementSectionName, 'Группа эксплуатации А, Б')) { ?>
                                    <?
                                    if ($arResult['PROPERTIES']['EXPLUATATION_GROUPS']['VALUE'] == 'expluatation_group_a') {

                                        ?>
                                        <h4>
                                            Группа эксплуатации А
                                        </h4>
                                        <div class="icons">
                                            <div class="icons">
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M22.5032 19.7228H21.1465V19.0068H22.5032V19.7228Z" fill="#003064"/>
                                                    <path d="M6.306 19.7228H3.33789V19.0068H6.306V19.7228ZM10.7586 19.7228H7.79049V19.0068H10.7586V19.7228ZM15.2112 19.7228H12.2431V19.0068H15.2112V19.7228ZM19.6629 19.7228H16.6948V19.0068H19.6629V19.7228Z" fill="#003064"/>
                                                    <path d="M1.85475 19.7228H0.498047V19.0068H1.85475V19.7228Z" fill="#003064"/>
                                                    <path d="M9.88463 9.92206C9.97741 9.92206 10.0483 9.9667 10.0772 9.98771C10.2514 10.119 10.2076 10.3238 10.1936 10.3912L10.1201 10.814C10.0527 11.2079 9.98529 11.6017 9.90301 11.9921C9.77785 12.5926 9.92927 13.0258 10.3949 13.3996C10.886 13.7926 11.3665 14.1996 11.8462 14.6066C12.0877 14.8114 12.3293 15.0163 12.5726 15.2202C12.6847 15.313 12.7608 15.4206 12.8107 15.5572L13.3989 17.1765C13.6388 17.8329 13.8777 18.4894 14.1149 19.1468C14.3976 19.0382 14.6725 18.9358 14.9491 18.8395C14.521 17.6693 14.1035 16.5165 13.686 15.3637L13.5127 14.8858C13.4996 14.8491 13.4838 14.8254 13.4558 14.8027L12.9394 14.3703C12.7013 14.1707 12.4641 13.9712 12.2225 13.7751C12.0457 13.6315 11.981 13.4495 12.0247 13.2193C12.0799 12.9287 12.1306 12.6372 12.1823 12.3466L12.5867 10.0831L13.0567 10.603C13.9486 11.5842 15.0138 12.1015 16.3093 12.1812C16.3058 11.881 16.3058 11.586 16.3101 11.2919C15.8226 11.2674 15.3587 11.144 14.9176 10.9243C14.1596 10.5453 13.6458 10.0621 13.3482 9.4494C13.2764 9.3006 13.2212 9.14393 13.1661 8.98812C13.1346 8.89447 13.1022 8.80169 13.0654 8.71066C12.893 8.28527 12.4904 7.9439 12.0168 7.82224C11.5801 7.70932 11.1521 7.79773 10.8116 8.07257C10.3547 8.44019 9.8715 8.78243 9.4041 9.11329C9.19666 9.26034 8.98921 9.40651 8.78352 9.55619C8.71087 9.60958 8.66098 9.64722 8.62684 9.69361C8.15681 10.3457 7.68415 10.9969 7.21062 11.6455C7.45395 11.8171 7.69291 11.9921 7.93011 12.1681C8.09204 11.944 8.25134 11.7252 8.40977 11.5072C8.63122 11.2026 8.85267 10.898 9.06974 10.5917C9.24917 10.3378 9.42948 10.1601 9.63868 10.0341C9.72796 9.94919 9.81286 9.92206 9.88463 9.92206ZM14.0099 19.7314C13.9635 19.7314 13.9145 19.7227 13.8637 19.6999C13.7202 19.6352 13.6685 19.4934 13.6501 19.44C13.3998 18.7459 13.1468 18.0535 12.8948 17.3603L12.3057 15.7401C12.2882 15.6946 12.2672 15.6648 12.2287 15.6316C11.9845 15.4276 11.7411 15.2219 11.4987 15.0163C11.0225 14.6119 10.5464 14.2084 10.0588 13.818C9.43298 13.3164 9.20978 12.6827 9.37784 11.8818C9.45224 11.5273 9.51439 11.1693 9.57566 10.8114C9.55377 10.8394 9.53102 10.87 9.50826 10.9015C9.28944 11.2114 9.06711 11.516 8.84391 11.8223C8.65748 12.0788 8.47104 12.3352 8.28635 12.5934C8.25572 12.6372 8.17344 12.751 8.02377 12.7746C7.87322 12.7965 7.76205 12.7134 7.72004 12.6819C7.40931 12.4499 7.09596 12.2197 6.77648 11.9974C6.73972 11.972 6.6268 11.8932 6.6023 11.7453C6.57779 11.6 6.65656 11.4923 6.6942 11.4407C7.19487 10.7553 7.69466 10.0691 8.19095 9.38026C8.2776 9.25947 8.38264 9.18244 8.46754 9.12117C8.67498 8.97062 8.88418 8.82269 9.09337 8.67477C9.55465 8.34829 10.0308 8.0113 10.4737 7.65418C10.9499 7.2708 11.5442 7.14476 12.1516 7.30144C12.7871 7.46599 13.328 7.92902 13.5626 8.50847C13.6029 8.60825 13.6388 8.70978 13.6738 8.81132C13.7219 8.94874 13.7683 9.08528 13.8313 9.21483C14.079 9.72425 14.5 10.1146 15.1583 10.4437C15.567 10.6477 15.9985 10.7536 16.4388 10.758C16.4931 10.7588 16.6375 10.7606 16.7452 10.8709C16.8169 10.9444 16.8519 11.0451 16.8493 11.1711C16.8414 11.5545 16.8423 11.9387 16.8493 12.3212C16.8502 12.3694 16.8528 12.5155 16.7425 12.6241C16.6314 12.7326 16.4878 12.7265 16.4274 12.7247C15.0655 12.6748 13.8891 12.1742 12.9245 11.2394L12.7109 12.4385C12.6593 12.7326 12.6085 13.0267 12.5525 13.3199C12.8055 13.5554 13.0444 13.7576 13.2843 13.9589L13.7981 14.3887C13.9005 14.4727 13.9722 14.5751 14.0178 14.7029L14.1911 15.1808C14.6261 16.3791 15.0593 17.5774 15.4979 18.7739C15.5189 18.8316 15.5688 18.9656 15.5022 19.103C15.4375 19.2413 15.3001 19.2877 15.2493 19.3052C14.8939 19.4251 14.5429 19.5573 14.1937 19.6921C14.1447 19.7113 14.0808 19.7314 14.0099 19.7314Z" fill="#003064"/>
                                                    <path d="M7.59074 18.2423C7.82357 18.4331 8.05027 18.623 8.27347 18.8147L8.44765 18.6038C8.84766 18.1162 9.24854 17.6287 9.66781 17.1587C10.2 16.5608 10.5483 16.0173 10.7628 15.4475C10.7777 15.4089 10.7882 15.3801 10.7969 15.3599C10.7821 15.3468 10.7637 15.3328 10.7435 15.3153L9.97854 14.6746L9.62229 15.7188C9.58028 15.8431 9.52076 15.9499 9.43848 16.047L8.71987 16.9022C8.34437 17.3486 7.96887 17.7959 7.59074 18.2423ZM8.29623 19.4257C8.15706 19.4257 8.05815 19.3399 8.01964 19.3057C7.72991 19.0545 7.43582 18.8077 7.13822 18.567C7.09796 18.5346 6.99117 18.448 6.97804 18.2992C6.96404 18.1512 7.05332 18.0453 7.08745 18.0051C7.49621 17.5237 7.90235 17.0396 8.30936 16.5556L9.02797 15.7022C9.06561 15.6567 9.09274 15.6068 9.11287 15.5472L9.7247 13.7608L11.0893 14.9048C11.3729 15.1437 11.407 15.2663 11.281 15.5954C11.0271 16.2702 10.6464 16.8672 10.0687 17.5158C9.6538 17.9806 9.25817 18.4628 8.86254 18.9451L8.60608 19.2567C8.57107 19.2996 8.47829 19.4117 8.32424 19.4248C8.31461 19.4257 8.30498 19.4257 8.29623 19.4257Z" fill="#003064"/>
                                                    <path d="M12.6434 6.8816C12.9226 6.89998 13.2088 6.8002 13.4355 6.60063C13.6692 6.39318 13.8145 6.09734 13.832 5.79011C13.867 5.19754 13.3751 4.62597 12.7992 4.59009C12.4631 4.56733 12.1462 4.67411 11.9064 4.88681C11.6788 5.08813 11.544 5.36997 11.5274 5.67982C11.4933 6.31266 11.9834 6.83958 12.6434 6.8816ZM12.7099 7.42078C12.6758 7.42078 12.6434 7.4199 12.6093 7.41815C11.6508 7.35776 10.9401 6.5805 10.9908 5.65006C11.0162 5.19666 11.214 4.78352 11.5501 4.48505C11.8976 4.17607 12.3563 4.0229 12.8333 4.05353C13.7034 4.10868 14.4202 4.93407 14.3686 5.82074C14.3423 6.27064 14.1314 6.70129 13.7909 7.00326C13.4837 7.2746 13.1029 7.42078 12.7099 7.42078Z" fill="#003064"/>
                                                    <path d="M0.70811 22.2919H22.2919V0.70722H0.70811V22.2919ZM23 23H0V0H23V23Z" fill="#003064"/>
                                                </svg>
                                                <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M24.0004 19.5927H22.2227V18.7408H24.0004V19.5927Z" fill="#999999"/>
                                                    <path d="M6.62548 19.5927H3.55469V18.7408H6.62548V19.5927ZM11.2312 19.5927H8.16042V18.7408H11.2312V19.5927ZM15.8378 19.5927H12.7671V18.7408H15.8378V19.5927ZM20.4436 19.5927H17.3728V18.7408H20.4436V19.5927Z" fill="#999999"/>
                                                    <path d="M1.77756 19.5927H0.888672V18.7408H1.77756V19.5927Z" fill="#999999"/>
                                                    <path d="M13.1368 15.5908C13.1368 15.3007 12.8816 15.0579 12.5771 15.0562C12.2625 15.0544 12.0027 15.3025 12.0073 15.5987C12.0128 15.8888 12.2743 16.1343 12.5752 16.1308C12.8798 16.1273 13.1368 15.881 13.1368 15.5908ZM10.3116 9.06973C10.0464 9.06973 9.78114 9.06798 9.51591 9.0706C9.30189 9.07236 9.17933 9.17228 9.17842 9.33794C9.1775 9.50359 9.30189 9.60877 9.51408 9.60965C10.0391 9.61315 10.564 9.61228 11.089 9.60965C11.2985 9.60877 11.432 9.50009 11.4329 9.33969C11.4338 9.18017 11.2994 9.07236 11.0909 9.0706C10.8311 9.06798 10.5714 9.06973 10.3116 9.06973ZM8.0434 15.3147H10.1689C10.2284 15.3147 10.2915 15.3305 10.275 15.2306C10.1753 14.6267 9.91193 14.1034 9.45829 13.6704C9.39975 13.6135 9.37048 13.6135 9.3211 13.6775C8.90312 14.2174 8.48241 14.7538 8.0434 15.3147ZM10.768 11.8307C10.436 12.2558 10.125 12.6607 9.80492 13.0595C9.74547 13.1349 9.74364 13.1726 9.81681 13.2392C10.3162 13.6985 10.639 14.2516 10.7936 14.8993C10.8274 15.0369 10.7671 15.235 10.907 15.2998C11.0323 15.3586 11.2116 15.3104 11.3661 15.3165C11.443 15.3182 11.486 15.2998 11.5107 15.2236C11.5701 15.0439 11.678 14.8879 11.8335 14.7713C11.9085 14.7161 11.8994 14.6653 11.8719 14.5978C11.7832 14.3813 11.6963 14.1648 11.6094 13.9483C11.3314 13.2488 11.0534 12.5494 10.768 11.8307ZM11.2829 11.5248C11.2829 11.5449 11.2783 11.5616 11.2829 11.5756C11.6597 12.5257 12.0375 13.4767 12.4143 14.4269C12.4381 14.4856 12.4609 14.5136 12.5442 14.5075C12.6566 14.4978 12.7783 14.5005 12.8844 14.5504C12.9841 14.5969 13.0298 14.5496 13.0838 14.4794C13.8145 13.5425 14.548 12.6073 15.2797 11.6694C15.3136 11.6273 15.3712 11.5923 15.3675 11.5248H11.2829ZM7.42147 12.8851C7.27147 12.8772 7.07758 12.893 6.88917 12.9333C5.32886 13.2655 4.41609 14.6845 4.68498 16.0896C4.95936 17.5244 6.45839 18.5376 7.94554 18.2668C9.16287 18.045 10.0336 17.1957 10.2604 16.0265C10.2906 15.8704 10.2403 15.8643 10.1104 15.8643C9.26896 15.8687 8.42662 15.8669 7.58518 15.8669C7.43793 15.8669 7.2916 15.8669 7.21385 15.7162C7.13886 15.5689 7.21111 15.455 7.30166 15.3393C7.81475 14.688 8.32327 14.0333 8.83727 13.3821C8.90587 13.2953 8.91501 13.2611 8.80069 13.2033C8.38089 12.9947 7.93913 12.8807 7.42147 12.8851ZM14.8489 15.5391C14.8563 15.8126 14.8755 16.0387 14.9349 16.2614C15.2971 17.6208 16.7202 18.5104 18.1433 18.2694C19.7293 18.0012 20.7399 16.562 20.4591 15.0921C20.1783 13.617 18.6272 12.6151 17.1007 12.9403C16.9562 12.971 16.9534 13.0061 16.9992 13.12C17.3056 13.8773 17.6065 14.6363 17.9083 15.3945C17.9339 15.4585 17.9604 15.5216 17.9595 15.5935C17.9568 15.7276 17.8845 15.817 17.7565 15.8512C17.6293 15.8845 17.5132 15.852 17.4391 15.739C17.4071 15.6899 17.3851 15.6347 17.3632 15.5803C17.0623 14.8222 16.7586 14.064 16.4641 13.3032C16.4184 13.1831 16.3827 13.1787 16.2757 13.241C15.3566 13.7765 14.8892 14.5601 14.8489 15.5391ZM21.082 16.0133C20.9896 16.2368 20.9749 16.4805 20.8807 16.704C20.3786 17.8951 19.4805 18.6261 18.1479 18.8163C16.3763 19.0679 14.7108 17.9398 14.3395 16.264C14.0377 14.8984 14.7026 13.4767 15.9703 12.779C16.1989 12.6528 16.1989 12.6528 16.102 12.4083C16.0306 12.2251 15.9593 12.0428 15.887 11.8587C15.8321 11.857 15.8184 11.9034 15.7946 11.9341C15.041 12.8974 14.2883 13.8598 13.5328 14.8213C13.4789 14.8896 13.4716 14.9317 13.5246 15.0124C13.7734 15.391 13.7789 15.7854 13.5328 16.1641C13.2905 16.5383 12.9201 16.7145 12.4646 16.6742C12.0155 16.6339 11.6963 16.3928 11.5244 15.994C11.4786 15.8897 11.4283 15.8582 11.3195 15.8652C11.1768 15.8757 11.0076 15.8284 10.896 15.8871C10.7808 15.9467 10.8366 16.1273 10.8037 16.2526C10.4378 17.6287 9.547 18.5131 8.09187 18.7944C6.48034 19.1056 4.90266 18.2589 4.30542 16.7986C3.67069 15.2446 4.26975 13.6038 5.77793 12.7659C6.82515 12.1839 7.91627 12.169 9.00556 12.6905C9.27171 12.8185 9.26896 12.8255 9.45188 12.5915C9.78389 12.1673 10.1131 11.7404 10.4497 11.3206C10.5082 11.2469 10.5174 11.19 10.4817 11.1067C10.3647 10.8271 10.2549 10.5457 10.147 10.2635C10.1177 10.1855 10.0839 10.1461 9.98418 10.1513C9.79669 10.1618 9.60828 10.1566 9.41987 10.1522C8.96532 10.1399 8.59307 9.7718 8.59307 9.33794C8.59399 8.91021 8.95891 8.53594 9.40433 8.52893C10.0025 8.52017 10.6006 8.52017 11.1988 8.52893C11.6561 8.53594 12.0119 8.90845 12.0064 9.35283C12.0018 9.78319 11.6378 10.139 11.1878 10.1522C11.0662 10.1557 10.9436 10.1575 10.822 10.1531C10.7406 10.1504 10.7268 10.1785 10.7543 10.2451C10.8403 10.4572 10.9281 10.6676 11.0067 10.8814C11.0369 10.963 11.0918 10.9638 11.1613 10.9638C12.5688 10.963 13.9755 10.9621 15.3831 10.9656C15.523 10.9665 15.5175 10.9261 15.4745 10.8218C15.3309 10.4739 15.1956 10.1224 15.0593 9.77092C14.9541 9.50272 15.0739 9.33443 15.3721 9.33005C15.5715 9.32742 15.7709 9.33443 15.9703 9.32654C16.3516 9.31076 16.6242 9.12933 16.7943 8.80327C16.8291 8.73754 16.8538 8.66216 16.905 8.60957C17.0019 8.51228 17.1263 8.49212 17.2525 8.56136C17.3879 8.63586 17.4199 8.75682 17.3797 8.89092C17.2324 9.38264 16.6782 9.8165 16.1413 9.86821C16.0096 9.87961 15.8788 9.90064 15.7206 9.88837C15.8559 10.2285 15.9831 10.5501 16.1111 10.8727C16.305 11.3609 16.5007 11.8473 16.6919 12.3373C16.7257 12.4258 16.7559 12.4556 16.8666 12.4284C18.5019 12.0261 20.1427 12.8702 20.7865 14.2586C20.9192 14.5452 21.0134 14.8423 21.049 15.1543C21.0518 15.1789 21.0454 15.2078 21.082 15.2175V16.0133Z" fill="#999999"/>
                                                    <path d="M0.737986 22.2919H23.2602V0.70722H0.737986V22.2919ZM24 23H0V0H24V23Z" fill="#999999"/>
                                                </svg>
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14.4125 9.14301C14.4125 8.38928 14.4116 7.64257 14.4142 6.89672C14.4142 6.81083 14.3949 6.78191 14.3047 6.78366C13.9795 6.78805 13.6535 6.78805 13.3274 6.78366C13.2433 6.78279 13.2275 6.81171 13.2284 6.88796C13.2319 7.22714 13.2319 7.56807 13.2275 7.90812C13.2275 7.97736 13.2547 8.02118 13.2994 8.06588C13.6412 8.40506 13.9813 8.74687 14.3231 9.08692C14.345 9.10796 14.3599 9.14301 14.4125 9.14301ZM8.27833 16.3552C8.27833 17.0449 8.28096 17.7347 8.2757 18.4244C8.27482 18.5331 8.30024 18.5646 8.41418 18.5646C9.302 18.5594 10.1907 18.5594 11.0785 18.5638C11.1872 18.5646 11.2188 18.541 11.2188 18.427C11.2144 17.0519 11.2144 15.6768 11.2179 14.3026C11.2179 14.1956 11.1968 14.1615 11.082 14.1623C10.1933 14.1667 9.3055 14.1658 8.4168 14.1623C8.30988 14.1623 8.27395 14.1834 8.2757 14.299C8.28096 14.9844 8.27833 15.6689 8.27833 16.3552ZM16.3345 11.8889C16.302 11.8547 16.274 11.8231 16.2451 11.7933C14.1092 9.65835 11.9742 7.52337 9.841 5.38576C9.76037 5.30425 9.72006 5.31301 9.64556 5.38839C9.01628 6.02292 8.3835 6.65308 7.75247 7.28498C6.25202 8.78543 4.75245 10.2868 3.2485 11.7837C3.1556 11.8748 3.16524 11.9195 3.25288 11.9993C3.39661 12.1281 3.53509 12.2657 3.66393 12.4103C3.74719 12.5032 3.79276 12.5059 3.88566 12.4121C5.74983 10.5418 7.61925 8.675 9.48693 6.80733C9.67886 6.61539 9.80945 6.61539 10.0031 6.80908C10.7613 7.56719 11.5194 8.3253 12.2784 9.08517C13.3949 10.2 14.5115 11.3157 15.6254 12.434C15.6903 12.4989 15.7254 12.5164 15.7955 12.4384C15.9453 12.2736 16.1075 12.122 16.2644 11.9651C16.2871 11.9415 16.3082 11.9169 16.3345 11.8889ZM14.4125 15.2859C14.4125 14.2368 14.4116 13.1877 14.4133 12.1387C14.4133 12.0484 14.3941 11.98 14.3266 11.9134C12.8279 10.4182 11.3309 8.92128 9.83662 7.42083C9.75862 7.34283 9.72268 7.35159 9.64994 7.42433C8.15826 8.9204 6.66482 10.4138 5.16876 11.9055C5.10741 11.9686 5.08199 12.0282 5.08199 12.1159C5.08462 14.2193 5.08462 16.3218 5.08111 18.4253C5.08111 18.5331 5.10565 18.5655 5.21784 18.5646C6.0075 18.5585 6.79629 18.5585 7.58595 18.5646C7.70076 18.5655 7.73319 18.5427 7.73319 18.4209C7.72793 16.9467 7.72968 15.4726 7.72968 13.9976C7.72968 13.6855 7.80769 13.6084 8.12583 13.6084H11.3581C11.6859 13.6084 11.763 13.6829 11.763 14.0046C11.763 15.4743 11.7639 16.9441 11.7604 18.4139C11.7595 18.5252 11.777 18.5655 11.9015 18.5646C12.6955 18.5576 13.4896 18.5594 14.2836 18.5638C14.3897 18.5646 14.4151 18.5401 14.4151 18.4332C14.4107 17.3841 14.4125 16.335 14.4125 15.2859ZM4.74982 19.1036C4.57454 19.0432 4.52546 18.9695 4.52546 18.7627C4.52546 16.7539 4.52546 14.746 4.52546 12.7373V12.5471C4.34403 12.7302 4.18803 12.8889 4.0294 13.0475C3.84447 13.2316 3.7095 13.2316 3.5237 13.0458C3.21607 12.739 2.90844 12.4323 2.60169 12.1229C2.43167 11.9528 2.43254 11.8205 2.60257 11.6496C4.90144 9.34985 7.20032 7.05097 9.50007 4.7521C9.67536 4.57769 9.81559 4.57944 9.99613 4.75999C10.8445 5.60925 11.6938 6.45763 12.5422 7.30602C12.5772 7.34195 12.6096 7.37964 12.6692 7.44449C12.6692 7.13073 12.6684 6.85904 12.6692 6.58734C12.6701 6.32617 12.7692 6.22538 13.0268 6.22538C13.5614 6.22538 14.0952 6.2245 14.6289 6.22538C14.8629 6.22625 14.962 6.32704 14.9629 6.5628C14.9629 7.57333 14.9637 8.58473 14.9611 9.59613C14.9602 9.68728 14.9839 9.75126 15.0505 9.81786C15.6412 10.4024 16.2284 10.9914 16.8156 11.5804C16.8805 11.6452 16.9418 11.7153 17.0049 11.7828V11.9809C16.9909 11.9993 16.9795 12.0186 16.9637 12.0352C16.6009 12.3998 16.2407 12.7671 15.8726 13.1264C15.7788 13.2175 15.6289 13.2026 15.5247 13.1097C15.4396 13.0344 15.3625 12.9511 15.2819 12.8713C15.182 12.7714 15.0821 12.6715 14.9629 12.5532V12.7443V18.7802C14.9629 18.9748 14.9111 19.0475 14.7376 19.1036C14.7052 19.1028 14.6719 19.1001 14.6395 19.1001H4.96192C4.89093 19.1001 4.81994 19.1019 4.74982 19.1036Z" fill="#003064"/>
                                                    <path d="M14.7378 19.1041C14.7343 19.1129 14.7343 19.1208 14.7352 19.1287H4.75175C4.75438 19.1208 4.75351 19.112 4.75 19.1041C4.82011 19.1024 4.8911 19.1006 4.9621 19.1006H14.6388C14.6721 19.1006 14.7045 19.1032 14.7378 19.1041Z" fill="#003064"/>
                                                    <path d="M19.8498 17.143C19.6386 17.377 19.3573 17.5295 19.0505 17.5804L19.0129 16.9055L19.5291 16.3902C19.5676 16.3516 19.5676 16.2876 19.5291 16.2491C19.4896 16.2105 19.4265 16.2105 19.388 16.2491L18.998 16.6391L18.9734 16.2017L19.21 15.9651C19.2495 15.9265 19.2495 15.8634 19.21 15.8249C19.1715 15.7854 19.1084 15.7854 19.0698 15.8249L18.9585 15.9353L18.9208 15.2526C18.9182 15.2 18.8744 15.1588 18.8218 15.1588C18.7692 15.1588 18.7254 15.2 18.7228 15.2526L18.6623 16.3376L18.2556 15.9309C18.2162 15.8915 18.1531 15.8915 18.1145 15.9309C18.0759 15.9695 18.0759 16.0326 18.1145 16.0712L18.6474 16.604L18.593 17.5804C17.9278 17.4708 17.4353 16.8959 17.4353 16.2131C17.4353 15.6286 17.6027 14.8643 17.8621 14.2666C18.1461 13.611 18.4958 13.235 18.8218 13.235C19.1469 13.235 19.4966 13.611 19.7806 14.2666C20.04 14.8643 20.2074 15.6286 20.2074 16.2131C20.2074 16.5576 20.0803 16.888 19.8498 17.143ZM18.7149 18.9792L18.8218 17.0554L18.9287 18.9792H18.7149ZM19.9638 14.1877C19.6413 13.4445 19.2363 13.0361 18.8218 13.0361C18.4072 13.0361 18.0015 13.4445 17.6798 14.1877C17.4064 14.8187 17.2363 15.5944 17.2363 16.2131C17.2363 17.001 17.8104 17.6636 18.5816 17.7811L18.515 18.9792H18.3976C18.3424 18.9792 18.2977 19.0238 18.2977 19.0791C18.2977 19.1334 18.3424 19.1781 18.3976 19.1781H19.246C19.3012 19.1781 19.3459 19.1334 19.3459 19.0791C19.3459 19.0238 19.3012 18.9792 19.246 18.9792H19.1285L19.0611 17.7811C19.4213 17.7259 19.7508 17.5488 19.9971 17.2762C20.2618 16.9844 20.4064 16.6075 20.4064 16.2131C20.4064 15.5944 20.2372 14.8187 19.9638 14.1877Z" fill="#003064"/>
                                                    <path d="M18.8213 13.3251C18.5374 13.3251 18.2096 13.6906 17.944 14.3024C17.689 14.8896 17.5251 15.6407 17.5251 16.2139C17.5251 16.8168 17.9335 17.3313 18.5093 17.4724L18.5549 16.6389L18.051 16.135C17.9765 16.0614 17.9765 15.9413 18.051 15.8677C18.1246 15.7932 18.2447 15.7932 18.3183 15.8677L18.5838 16.1323L18.632 15.248C18.6382 15.1481 18.7206 15.0692 18.8213 15.0692C18.9221 15.0692 19.0045 15.1481 19.0107 15.248L19.0369 15.7362C19.1097 15.6889 19.2096 15.6976 19.2736 15.7616C19.3086 15.7967 19.3288 15.8449 19.3288 15.8957C19.3288 15.9457 19.3086 15.993 19.2736 16.0289L19.065 16.2366L19.0764 16.4347L19.3244 16.1867C19.3954 16.1148 19.5199 16.1148 19.5917 16.1867C19.6277 16.2217 19.6469 16.2691 19.6469 16.3199C19.6469 16.3707 19.6277 16.4172 19.5917 16.4531L19.1044 16.9413L19.1334 17.4715C19.3823 17.4102 19.6101 17.2743 19.7828 17.0833C19.9993 16.8449 20.1176 16.5355 20.1176 16.2139C20.1176 15.6407 19.9528 14.8896 19.6978 14.3024C19.4331 13.6906 19.1053 13.3251 18.8213 13.3251ZM18.6767 17.6863L18.5777 17.6696C17.8634 17.5513 17.3454 16.9387 17.3454 16.2139C17.3454 15.617 17.5163 14.8396 17.7801 14.2305C18.0825 13.5311 18.4532 13.1455 18.8213 13.1455C19.1894 13.1455 19.5593 13.5311 19.8625 14.2305C20.1263 14.8396 20.2972 15.617 20.2972 16.2139C20.2972 16.5802 20.1623 16.9316 19.916 17.2033C19.6934 17.4496 19.391 17.6153 19.065 17.6687L18.966 17.6863L18.9204 16.8712L19.4646 16.3269L18.9195 16.8449L18.8818 16.1674L19.1465 15.9018L19.022 15.9991L18.8801 16.1411L18.831 15.2577L18.7398 16.5434L18.1912 15.9947L18.739 16.5688L18.6767 17.6863ZM18.8091 18.8905H18.8336L18.8213 18.6679L18.8091 18.8905ZM19.0229 19.0693H18.6189L18.7319 17.0508H18.9107L19.0229 19.0693ZM18.3971 19.0693L19.2455 19.0885L19.1281 19.0693H19.0431L18.9677 17.7047L19.0475 17.6915C19.3866 17.6407 19.6995 17.4715 19.93 17.2165C20.1798 16.9413 20.3174 16.5855 20.3174 16.2139C20.3174 15.6065 20.15 14.8431 19.8809 14.2226C19.5742 13.5162 19.1982 13.1262 18.8213 13.1262C18.4445 13.1262 18.0685 13.5162 17.7617 14.2226C17.4927 14.8431 17.3262 15.6065 17.3262 16.2139C17.3262 16.9588 17.8599 17.5802 18.5952 17.6915L18.6759 17.7038L18.5996 19.0693H18.3971ZM19.2455 19.2673H18.3971C18.292 19.2673 18.2078 19.1832 18.2078 19.0789C18.2078 18.9746 18.292 18.8905 18.3971 18.8905H18.4296L18.4874 17.8554C17.7056 17.6985 17.1465 17.021 17.1465 16.2139C17.1465 15.5828 17.3191 14.7932 17.597 14.1516C17.9344 13.3751 18.3691 12.9465 18.8213 12.9465C19.2736 12.9465 19.7083 13.3751 20.0457 14.1516C20.3235 14.7932 20.4962 15.5828 20.4962 16.2139C20.4962 16.6302 20.3428 17.0289 20.0632 17.3366C19.824 17.6021 19.5032 17.7844 19.1553 17.8554L19.2122 18.8905H19.2455C19.3498 18.8905 19.4348 18.9746 19.4348 19.0789C19.4348 19.1832 19.3498 19.2673 19.2455 19.2673Z" fill="#003064"/>
                                                    <path d="M0.708115 22.2919H22.2919V0.70722H0.708115V22.2919ZM23 23H0V0H23V23Z" fill="#003064"/>
                                                </svg>
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.67276 16.1942C5.04651 15.9273 5.25482 15.6139 5.20056 15.1701C5.13754 14.6537 4.83031 14.2642 4.5152 13.88C4.46969 13.8248 4.44693 13.8808 4.42417 13.908C4.17034 14.2064 3.94451 14.5224 3.81585 14.8988C3.65567 15.3697 3.78171 15.9001 4.28588 16.1645C4.28588 16.0165 4.28675 15.8712 4.28588 15.7268C4.28588 15.5999 4.35065 15.5211 4.47144 15.5176C4.60273 15.5132 4.67276 15.5929 4.67276 15.7268C4.67276 15.8721 4.67276 16.0165 4.67276 16.1942ZM11.4519 18.7711H18.8787C19.0302 18.7702 19.2376 18.8201 19.3164 18.7431C19.3934 18.6669 19.3295 18.4569 19.347 18.3072C19.3663 18.1417 19.3155 18.0971 19.1431 18.0971C14.1215 18.1032 9.09998 18.1015 4.07843 18.1032C3.92176 18.1032 3.70818 18.0358 3.62416 18.1312C3.54538 18.2214 3.60928 18.4306 3.59265 18.5855C3.57689 18.7369 3.62416 18.7755 3.77908 18.7755C6.33668 18.7693 8.89429 18.7711 11.4519 18.7711ZM7.68201 12.1495C7.68201 13.9421 7.68376 15.7356 7.67938 17.5273C7.67851 17.6603 7.70564 17.7032 7.84744 17.7015C8.63608 17.6936 9.42384 17.6927 10.2125 17.7023C10.3744 17.7041 10.4234 17.6735 10.4225 17.4984C10.4164 14.7946 10.4182 12.0917 10.4182 9.38708C10.4182 9.15163 10.4689 9.10086 10.7035 9.10086C11.2007 9.09998 11.697 9.09561 12.1933 9.10261C12.3167 9.10524 12.3473 9.07022 12.3464 8.94943C12.3403 8.22557 12.3377 7.50083 12.3482 6.77696C12.3508 6.61153 12.2948 6.5914 12.1512 6.5914C10.721 6.59665 9.28992 6.59665 7.85969 6.5914C7.71527 6.59052 7.67763 6.62291 7.67851 6.76996C7.68376 8.56343 7.68201 10.356 7.68201 12.1495ZM15.8248 13.6025C15.8248 12.2887 15.8231 10.974 15.8283 9.66017C15.8283 9.52363 15.7951 9.48686 15.6559 9.48686C14.0909 9.49299 12.5259 9.49299 10.9609 9.48686C10.8226 9.48599 10.8015 9.52888 10.8015 9.65317C10.805 12.2817 10.8059 14.9102 10.8015 17.5378C10.8007 17.6682 10.8296 17.7015 10.9626 17.7015C12.5276 17.6962 14.0926 17.6953 15.6577 17.7015C15.7977 17.7015 15.8283 17.663 15.8283 17.5273C15.8231 16.2196 15.8248 14.911 15.8248 13.6025ZM18.5479 11.0029C18.5479 8.82514 18.547 6.64742 18.5496 4.46882C18.5496 4.36203 18.547 4.30339 18.407 4.30426C16.9478 4.30951 15.4879 4.30864 14.0279 4.30514C13.9132 4.30426 13.8817 4.33402 13.8826 4.45044C13.8931 5.95069 13.8283 7.45181 13.8808 8.95206C13.8852 9.07548 13.915 9.10436 14.0357 9.10261C14.6493 9.09648 15.262 9.10086 15.8756 9.10086C16.1881 9.10086 16.2301 9.14112 16.2301 9.44923C16.2301 12.1346 16.231 14.8218 16.2266 17.5072C16.2266 17.6516 16.2459 17.7059 16.4104 17.7023C17.0643 17.691 17.719 17.6927 18.3728 17.7015C18.5199 17.7041 18.5514 17.6647 18.5514 17.5212C18.5461 15.3478 18.5479 13.1762 18.5479 11.0029ZM11.4711 19.158H3.51737C3.24516 19.158 3.20139 19.1151 3.20139 18.8472C3.20139 18.5558 3.20052 18.2634 3.20227 17.9711C3.20314 17.7671 3.26791 17.7015 3.47448 17.6988C3.69593 17.6953 3.91825 17.6927 4.1397 17.6997C4.24474 17.7032 4.29463 17.6875 4.28938 17.5614C4.27887 17.2997 4.28238 17.0363 4.2885 16.7737C4.29025 16.6861 4.26137 16.6397 4.18609 16.5969C3.97602 16.4796 3.78609 16.336 3.62766 16.1522C3.29242 15.7627 3.2294 15.3207 3.3887 14.8445C3.57689 14.2843 3.94276 13.8388 4.33752 13.4143C4.4163 13.3311 4.52221 13.3119 4.60186 13.3968C5.04651 13.8703 5.46052 14.3666 5.61632 15.0205C5.73536 15.522 5.57343 15.9395 5.20581 16.2844C5.11215 16.371 5.01412 16.4656 4.90121 16.5163C4.70602 16.6047 4.65 16.736 4.66925 16.9408C4.68764 17.1369 4.68151 17.3374 4.67101 17.5352C4.664 17.6612 4.69201 17.7032 4.82943 17.7015C5.59444 17.6936 6.35944 17.6936 7.12445 17.7015C7.26274 17.7032 7.28375 17.656 7.28288 17.5325C7.2785 16.399 7.28025 15.2664 7.28025 14.1338C7.28025 11.6112 7.28025 9.08861 7.28025 6.56602C7.28025 6.22815 7.31439 6.19402 7.64962 6.19402H12.4147C12.679 6.19402 12.7307 6.24566 12.7307 6.50912C12.7307 7.31964 12.7342 8.13191 12.7272 8.94418C12.7263 9.0781 12.7622 9.11311 12.8909 9.10261C13.0423 9.09123 13.1946 9.09298 13.346 9.10174C13.4607 9.10961 13.4913 9.0746 13.4896 8.95906C13.4616 7.42905 13.4799 5.89904 13.4878 4.36903C13.4904 3.91476 13.4904 3.91476 13.9438 3.91476H18.6214C18.8927 3.91476 18.9339 3.9559 18.9339 4.22724C18.9339 8.65446 18.9348 13.0799 18.9295 17.5072C18.9295 17.6717 18.9768 17.7155 19.1308 17.7006C19.2516 17.6875 19.3759 17.6936 19.4976 17.6988C19.67 17.7067 19.7365 17.7776 19.7374 17.9518C19.74 18.2669 19.7391 18.5829 19.7374 18.898C19.7365 19.0949 19.6744 19.1571 19.4766 19.158H15.9378H11.4711Z" fill="#999999"/>
                                                    <path d="M15.4037 5.7584C15.4037 5.44942 15.4037 5.45643 15.0947 5.44855C14.9669 5.44417 14.9144 5.47218 14.9328 5.60785C14.945 5.69976 14.9442 5.79516 14.9328 5.88707C14.9153 6.02624 14.9652 6.063 15.1026 6.05863C15.4037 6.04812 15.4037 6.056 15.4037 5.7584ZM15.809 5.75753C15.809 5.9037 15.8116 6.04987 15.809 6.19517C15.8055 6.37898 15.7372 6.45513 15.5586 6.45864C15.296 6.46389 15.0334 6.46214 14.7709 6.45951C14.6159 6.45776 14.5337 6.37461 14.5328 6.21881C14.531 5.90458 14.5302 5.58947 14.5337 5.27437C14.5354 5.12732 14.6177 5.04767 14.7647 5.04767C15.0326 5.04504 15.3013 5.04241 15.5691 5.04767C15.7372 5.05029 15.8055 5.12819 15.809 5.30238C15.8116 5.45468 15.809 5.6061 15.809 5.75753Z" fill="#999999"/>
                                                    <path d="M17.1005 5.75322C17.1005 6.0552 17.1005 6.04732 17.3928 6.0587C17.5355 6.06395 17.5924 6.02981 17.5714 5.88102C17.5591 5.78998 17.5591 5.69545 17.5714 5.60355C17.5907 5.46613 17.532 5.44337 17.4086 5.446C17.1005 5.45387 17.1005 5.44775 17.1005 5.75322ZM17.9723 5.74184C17.9723 5.89414 17.9758 6.04557 17.9714 6.197C17.967 6.38606 17.9031 6.45521 17.7202 6.45871C17.4637 6.46396 17.2073 6.46396 16.9508 6.45871C16.7696 6.45608 16.6979 6.38081 16.6961 6.2005C16.6926 5.9029 16.6926 5.6053 16.6952 5.3077C16.697 5.12389 16.7679 5.04949 16.9473 5.04686C17.2038 5.04249 17.4611 5.04249 17.7176 5.04686C17.9005 5.04949 17.9662 5.11776 17.9714 5.3042C17.9758 5.45037 17.9723 5.59655 17.9723 5.74184Z" fill="#999999"/>
                                                    <path d="M17.0995 7.66265C17.0995 7.98388 17.0995 7.97688 17.4094 7.98563C17.5354 7.98913 17.5888 7.96112 17.5704 7.82633C17.5582 7.7353 17.5573 7.63989 17.5704 7.54886C17.5923 7.39656 17.5293 7.36767 17.3901 7.37293C17.0995 7.3843 17.0995 7.37643 17.0995 7.66265ZM17.9713 7.69328C17.9713 7.84558 17.973 7.99701 17.9704 8.14843C17.9678 8.30424 17.8838 8.38476 17.7297 8.38651C17.4671 8.38914 17.2054 8.39089 16.9428 8.38564C16.7713 8.38214 16.6969 8.30686 16.6951 8.13793C16.6916 7.8342 16.6951 7.53135 16.6934 7.22763C16.6925 7.0622 16.7748 6.97467 16.9358 6.97117C17.2045 6.96679 17.4724 6.96679 17.7411 6.97204C17.8855 6.97467 17.9669 7.05694 17.9704 7.20399C17.973 7.3668 17.9704 7.53048 17.9713 7.69328Z" fill="#999999"/>
                                                    <path d="M17.5687 9.61493C17.5687 9.57467 17.5625 9.53266 17.5695 9.49327C17.6002 9.32959 17.5258 9.2937 17.3735 9.3007C17.1004 9.31208 17.0995 9.3042 17.0995 9.57204C17.0995 9.63069 17.1074 9.69021 17.0986 9.74623C17.075 9.88977 17.1406 9.91603 17.2702 9.91253C17.5687 9.90378 17.5687 9.91078 17.5687 9.61493ZM17.9713 9.60968C17.9713 9.76111 17.9722 9.91253 17.9713 10.0648C17.9713 10.2285 17.8881 10.3134 17.7245 10.3143C17.4619 10.316 17.1993 10.3178 16.9367 10.3134C16.7748 10.3117 16.6934 10.2233 16.6934 10.0587C16.6951 9.75498 16.6916 9.45213 16.6951 9.14928C16.6969 8.98035 16.773 8.90245 16.9419 8.89894C17.2045 8.89369 17.4671 8.89544 17.7297 8.89894C17.8829 8.90069 17.9687 8.98035 17.9704 9.13702C17.973 9.29458 17.9713 9.45125 17.9713 9.60968Z" fill="#999999"/>
                                                    <path d="M15.4033 7.68052C15.4033 7.628 15.3963 7.57461 15.4041 7.52384C15.4243 7.3978 15.37 7.37067 15.2518 7.37329C14.935 7.37942 14.9341 7.37329 14.9341 7.68752C14.9341 7.73391 14.9411 7.78118 14.9332 7.82669C14.9096 7.96061 14.9674 7.9895 15.0934 7.986C15.4033 7.97724 15.4033 7.98424 15.4033 7.68052ZM15.8085 7.68314C15.8085 7.82844 15.8111 7.97462 15.8085 8.11991C15.805 8.3046 15.7367 8.3825 15.5599 8.386C15.2973 8.39126 15.0348 8.3895 14.773 8.38688C14.619 8.38513 14.5332 8.3046 14.5323 8.1488C14.5306 7.83982 14.5315 7.52997 14.5315 7.22187C14.5323 7.05731 14.6137 6.97328 14.7792 6.97328C15.0418 6.97328 15.3044 6.96978 15.5669 6.97416C15.735 6.97766 15.8041 7.05381 15.8085 7.22712C15.8111 7.37942 15.8085 7.53084 15.8085 7.68314Z" fill="#999999"/>
                                                    <path d="M17.5687 11.8675C17.5687 11.5497 17.5687 11.5576 17.2579 11.548C17.1275 11.5445 17.0837 11.5777 17.0977 11.709C17.11 11.8237 17.103 11.941 17.0986 12.0565C17.096 12.1283 17.1161 12.1537 17.194 12.1589C17.5687 12.1843 17.5687 12.1878 17.5687 11.8675ZM17.9713 11.8631C17.9713 12.0145 17.9757 12.1668 17.9704 12.3182C17.9643 12.4889 17.8916 12.5598 17.7201 12.5624C17.4636 12.5659 17.2063 12.5659 16.9498 12.5624C16.7713 12.5598 16.6969 12.4854 16.6951 12.3051C16.6925 12.0014 16.6934 11.6985 16.6942 11.3948C16.6951 11.2399 16.7695 11.155 16.9288 11.1541C17.1967 11.1541 17.4654 11.155 17.7332 11.1541C17.8943 11.1532 17.9687 11.2364 17.9713 11.3904C17.973 11.548 17.9713 11.7047 17.9713 11.8631Z" fill="#999999"/>
                                                    <path d="M17.5689 16.3594C17.5689 16.278 17.568 16.1957 17.5689 16.1152C17.5707 16.0653 17.5523 16.0487 17.5006 16.0399C17.1549 15.9813 17.0989 16.0242 17.0989 16.3612C17.0989 16.4084 17.105 16.4548 17.098 16.5012C17.0796 16.6281 17.1208 16.6719 17.2573 16.6684C17.5689 16.6579 17.5689 16.6666 17.5689 16.3594ZM16.6945 16.348C16.6945 16.1914 16.691 16.0329 16.6963 15.8754C16.7015 15.7318 16.7724 15.6504 16.9265 15.6522C17.1952 15.6548 17.463 15.6566 17.7317 15.6513C17.8849 15.6487 17.9654 15.7222 17.9681 15.8675C17.9742 16.1887 17.9733 16.51 17.9689 16.8303C17.9672 16.9739 17.8937 17.0561 17.7396 17.0553C17.4709 17.0526 17.203 17.0526 16.9343 17.0553C16.7733 17.057 16.6989 16.973 16.6954 16.8198C16.6928 16.6631 16.6945 16.5056 16.6945 16.348Z" fill="#999999"/>
                                                    <path d="M17.0987 14.0967C17.0987 14.1317 17.1039 14.1676 17.0978 14.2008C17.0646 14.3794 17.1372 14.4293 17.3131 14.4179C17.5678 14.4013 17.5687 14.4135 17.5687 14.158C17.5687 14.0888 17.56 14.0179 17.5705 13.9496C17.5924 13.8131 17.5328 13.7886 17.4085 13.7921C17.0987 13.8 17.0987 13.7938 17.0987 14.0967ZM16.6943 14.1116C16.6943 13.954 16.6926 13.7965 16.6952 13.6398C16.6969 13.4857 16.7713 13.4026 16.9324 13.4043C17.2002 13.407 17.4689 13.407 17.7368 13.4043C17.8908 13.4026 17.967 13.4805 17.9687 13.6258C17.9731 13.9461 17.974 14.2674 17.9679 14.5886C17.9652 14.7339 17.8821 14.81 17.7324 14.81C17.4698 14.8092 17.2081 14.8118 16.9455 14.8083C16.7687 14.8074 16.6978 14.733 16.6952 14.5492C16.6917 14.4039 16.6943 14.2577 16.6943 14.1116Z" fill="#999999"/>
                                                    <path d="M14.4134 15.6122C14.2769 15.6288 14.0887 15.5544 14.0152 15.6446C13.946 15.7295 13.9942 15.9098 13.9898 16.0472C13.9898 16.0656 13.9924 16.0831 13.9898 16.0997C13.9723 16.2039 14.0222 16.2249 14.1167 16.2214C14.2909 16.2144 14.4659 16.21 14.6393 16.2223C14.7898 16.2328 14.8625 16.1995 14.8397 16.0306C14.8222 15.9001 14.88 15.7207 14.8126 15.6446C14.732 15.5553 14.5508 15.6288 14.4134 15.6122ZM14.4152 15.213C14.6077 15.213 14.8003 15.2139 14.9929 15.213C15.1592 15.213 15.2388 15.2979 15.2388 15.4608C15.2397 15.7645 15.2432 16.0682 15.238 16.3702C15.2353 16.554 15.1636 16.6231 14.9797 16.6249C14.6007 16.6284 14.2217 16.6258 13.8427 16.6258C13.6808 16.6258 13.5898 16.5505 13.5889 16.3842C13.5872 16.0752 13.588 15.7662 13.5889 15.4572C13.5889 15.2918 13.6729 15.2122 13.8384 15.213C14.03 15.2139 14.2226 15.213 14.4152 15.213Z" fill="#999999"/>
                                                    <path d="M14.4265 11.2442C14.2873 11.2442 14.1018 11.19 14.0212 11.2591C13.9249 11.3396 14.002 11.5313 13.9888 11.6749C13.988 11.6862 13.9906 11.6994 13.9888 11.7099C13.9722 11.8132 14.0055 11.8543 14.1193 11.8473C14.2934 11.8377 14.4703 11.8333 14.6444 11.8482C14.8081 11.8631 14.8449 11.8062 14.8405 11.6495C14.8291 11.2355 14.837 11.2355 14.4265 11.2442ZM14.4116 10.839C14.6033 10.839 14.7959 10.8407 14.9884 10.8381C15.1556 10.8355 15.2379 10.916 15.2379 11.0805C15.2388 11.3895 15.2396 11.6994 15.237 12.0075C15.2361 12.172 15.146 12.2491 14.9823 12.2491C14.6042 12.2482 14.2252 12.2508 13.8462 12.2482C13.6632 12.2473 13.5923 12.1764 13.5897 11.9926C13.5862 11.6906 13.5888 11.3869 13.5888 11.0832C13.5888 10.9195 13.6659 10.8363 13.8339 10.8381C14.0265 10.8407 14.219 10.839 14.4116 10.839Z" fill="#999999"/>
                                                    <path d="M12.2245 16.2268C12.3628 16.2198 12.544 16.2626 12.6271 16.1917C12.7164 16.1165 12.6464 15.9292 12.6551 15.7917C12.6569 15.769 12.6534 15.7445 12.656 15.7226C12.6683 15.6386 12.6341 15.6106 12.5501 15.6132C12.3584 15.6184 12.165 15.6246 11.9742 15.6114C11.8324 15.6027 11.786 15.6386 11.7895 15.7891C11.8 16.2198 11.7921 16.2198 12.2245 16.2268ZM12.2227 16.625C12.0302 16.625 11.8376 16.6285 11.6459 16.6241C11.4726 16.6198 11.4017 16.548 11.4008 16.3773C11.3973 16.0736 11.3982 15.7707 11.4 15.4679C11.4017 15.2902 11.4656 15.2175 11.638 15.2158C12.0284 15.2114 12.4197 15.2132 12.8101 15.2149C12.9632 15.2158 13.0473 15.2876 13.0455 15.4504C13.0429 15.7602 13.0464 16.0692 13.0446 16.3782C13.0429 16.5506 12.9755 16.6198 12.8004 16.6241C12.6079 16.6285 12.4153 16.625 12.2227 16.625Z" fill="#999999"/>
                                                    <path d="M14.4089 14.0316C14.5472 14.0281 14.7354 14.1016 14.8098 14.0132C14.8798 13.9292 14.8317 13.7471 14.8361 13.608C14.8361 13.5905 14.8334 13.573 14.8361 13.5555C14.8518 13.4513 14.8168 13.4093 14.7039 13.4163C14.535 13.4268 14.3643 13.4303 14.1962 13.4154C14.0326 13.4005 13.9582 13.4425 13.9862 13.6193C14.008 13.7559 13.9258 13.9406 14.0194 14.0176C14.0982 14.0841 14.275 14.0316 14.4089 14.0316ZM14.4116 14.4264C14.219 14.4264 14.0273 14.4272 13.8347 14.4264C13.6536 14.4246 13.5923 14.3695 13.5897 14.1874C13.5844 13.8846 13.5853 13.5808 13.5888 13.2789C13.5914 13.0915 13.6597 13.0277 13.8444 13.0268C14.2234 13.025 14.6015 13.025 14.9805 13.0268C15.1643 13.0277 15.2335 13.0924 15.2361 13.2789C15.2405 13.5817 15.2405 13.8854 15.2361 14.1883C15.2326 14.3677 15.1687 14.4246 14.9892 14.4264C14.7967 14.4272 14.6041 14.4264 14.4116 14.4264Z" fill="#999999"/>
                                                    <path d="M12.2547 11.2358C12.1251 11.2358 12.0192 11.2393 11.9151 11.2341C11.8302 11.2297 11.7847 11.2472 11.7917 11.347C11.7995 11.4739 11.7987 11.6034 11.7917 11.7312C11.7855 11.8301 11.8293 11.8485 11.9151 11.8468C12.1015 11.8415 12.2888 11.8363 12.4753 11.8485C12.6179 11.8581 12.6748 11.8196 12.6582 11.67C12.6424 11.5325 12.6985 11.3513 12.6293 11.2656C12.5549 11.1737 12.3676 11.2498 12.2547 11.2358ZM12.2214 10.8393C12.414 10.8393 12.6066 10.8358 12.7991 10.8402C12.9794 10.8446 13.0433 10.9067 13.0442 11.0809C13.046 11.3907 13.0425 11.6988 13.046 12.0087C13.0468 12.1697 12.9637 12.2468 12.8114 12.2476C12.4201 12.2511 12.0297 12.252 11.6385 12.2476C11.4704 12.2459 11.4013 12.168 11.4004 11.9947C11.3978 11.6918 11.3978 11.3881 11.4004 11.0844C11.4013 10.9111 11.4678 10.8446 11.6437 10.8402C11.8363 10.8358 12.0289 10.8393 12.2214 10.8393Z" fill="#999999"/>
                                                    <path d="M12.1997 14.0401C12.3231 14.0401 12.4229 14.034 12.5209 14.0419C12.6216 14.0506 12.6706 14.027 12.6592 13.9123C12.6478 13.7907 12.6513 13.6673 12.6583 13.5465C12.6644 13.4493 12.6356 13.4125 12.5332 13.4169C12.3537 13.4239 12.1725 13.4292 11.9931 13.416C11.839 13.4038 11.769 13.4449 11.7927 13.6121C11.8119 13.7487 11.7358 13.9281 11.8224 14.0139C11.9082 14.0988 12.0868 14.0296 12.1997 14.0401ZM12.2347 14.4261C12.036 14.4261 11.8382 14.4226 11.6395 14.427C11.4784 14.4305 11.4014 14.3552 11.4014 14.1986C11.4005 13.8826 11.3997 13.5683 11.4014 13.2532C11.4023 13.1044 11.4793 13.0265 11.629 13.0265C12.0255 13.0257 12.422 13.0257 12.8185 13.0265C12.9664 13.0265 13.0461 13.0992 13.0461 13.2506C13.0461 13.5718 13.0469 13.8922 13.0461 14.2126C13.0452 14.3544 12.9734 14.427 12.829 14.4261C12.6312 14.4253 12.4325 14.4261 12.2347 14.4261Z" fill="#999999"/>
                                                    <path d="M9.12196 16.5121C9.12196 16.4596 9.11495 16.4071 9.12283 16.3554C9.14296 16.2303 9.08957 16.2031 8.97053 16.2058C8.64142 16.2119 8.64142 16.2066 8.64142 16.5296C8.64142 16.5646 8.6458 16.6005 8.64055 16.6338C8.61691 16.7738 8.66418 16.8246 8.81473 16.8194C9.12196 16.808 9.12196 16.8167 9.12196 16.5121ZM8.24667 16.4964C8.24667 16.3458 8.24754 16.1944 8.24667 16.0421C8.24491 15.8907 8.32632 15.811 8.46899 15.8075C8.74296 15.8014 9.0178 15.8014 9.29176 15.8075C9.43444 15.8101 9.51584 15.8845 9.51409 16.0386C9.51146 16.3537 9.51146 16.6688 9.51409 16.983C9.51584 17.1476 9.42743 17.2176 9.27688 17.2202C9.01517 17.2229 8.75258 17.2246 8.49 17.2202C8.31844 17.2159 8.25017 17.1415 8.24754 16.969C8.24491 16.8123 8.24667 16.6548 8.24667 16.4964Z" fill="#999999"/>
                                                    <path d="M9.1216 14.2848C9.1216 14.2271 9.1146 14.1676 9.12248 14.1115C9.14086 13.9881 9.09447 13.9566 8.97193 13.9592C8.64107 13.9662 8.64107 13.9592 8.64107 14.2822C8.64107 14.3172 8.64632 14.3531 8.64019 14.3864C8.60868 14.5483 8.68396 14.5781 8.83013 14.5719C9.1216 14.5606 9.1216 14.5684 9.1216 14.2848ZM9.51286 14.2586C9.51286 14.4214 9.51198 14.5851 9.51286 14.7479C9.51373 14.8923 9.43846 14.9632 9.29754 14.9641C9.01832 14.9658 8.73823 14.9676 8.45813 14.9632C8.31809 14.9614 8.24719 14.8809 8.24631 14.7409C8.24631 14.4196 8.24544 14.0993 8.24719 13.7789C8.24806 13.638 8.32947 13.5619 8.46514 13.5592C8.7391 13.5531 9.01307 13.554 9.28703 13.5584C9.43058 13.5601 9.51461 13.6328 9.51286 13.7868C9.51198 13.9435 9.51286 14.101 9.51286 14.2586Z" fill="#999999"/>
                                                    <path d="M8.64021 8.15764C8.64021 8.23291 8.64284 8.30906 8.63933 8.38521C8.63758 8.44211 8.65772 8.46311 8.71724 8.47099C9.08836 8.52351 9.12075 8.49812 9.12075 8.13663C9.12075 8.10162 9.11549 8.06573 9.12162 8.03159C9.14875 7.88192 9.08486 7.84778 8.94131 7.85216C8.64021 7.86354 8.64021 7.85566 8.64021 8.15764ZM9.512 8.16376C9.512 8.32657 9.51288 8.49025 9.51113 8.65305C9.50937 8.78785 9.44023 8.85874 9.30543 8.85874C9.02534 8.85962 8.74612 8.85962 8.46603 8.85962C8.32423 8.85962 8.24808 8.78609 8.24633 8.64605C8.2437 8.31956 8.24283 7.99308 8.2472 7.6666C8.24808 7.5353 8.32598 7.45915 8.45552 7.4574C8.74087 7.45215 9.02709 7.45127 9.31243 7.45828C9.44198 7.4609 9.51113 7.54055 9.51113 7.6736C9.51113 7.83728 9.51113 8.00008 9.512 8.16376Z" fill="#999999"/>
                                                    <path d="M9.12226 12.0109C9.12226 11.7045 9.12226 11.7142 8.82991 11.7019C8.67586 11.6958 8.61984 11.7369 8.6391 11.8927C8.65223 12.0013 8.64523 12.1133 8.63997 12.2236C8.63735 12.2971 8.66098 12.3207 8.73801 12.326C9.12226 12.354 9.12226 12.3566 9.12226 12.0109ZM9.51352 12.0083C9.51352 12.1719 9.51352 12.3347 9.51352 12.4984C9.51264 12.6411 9.44174 12.7146 9.29907 12.7146C9.01898 12.7146 8.73976 12.7155 8.45966 12.7137C8.31612 12.712 8.24784 12.6315 8.24697 12.4923C8.24609 12.1719 8.24522 11.8507 8.24784 11.5304C8.24872 11.3912 8.32662 11.3133 8.46492 11.3115C8.74501 11.308 9.02423 11.3072 9.30432 11.3115C9.43386 11.3142 9.51001 11.3842 9.51177 11.519C9.51439 11.6827 9.51352 11.8463 9.51352 12.0083Z" fill="#999999"/>
                                                    <path d="M9.12226 10.0983C9.12226 9.77877 9.12226 9.77877 8.91394 9.77877C8.64173 9.77877 8.64173 9.77877 8.64173 10.0554C8.64173 10.114 8.64873 10.1727 8.63997 10.2287C8.61897 10.3687 8.67411 10.4046 8.81066 10.4011C9.12226 10.3915 9.12226 10.3985 9.12226 10.0983ZM9.51352 10.0895C9.51352 10.2532 9.51439 10.416 9.51264 10.5797C9.51177 10.7127 9.44349 10.7854 9.30782 10.7862C9.02248 10.7871 8.73713 10.788 8.45179 10.7854C8.31612 10.7836 8.24872 10.704 8.24784 10.5753C8.24522 10.2549 8.24609 9.9337 8.24697 9.61247C8.24784 9.46454 8.32575 9.38577 8.47367 9.38489C8.74764 9.38227 9.0216 9.38139 9.29557 9.38489C9.43299 9.38577 9.51177 9.45754 9.51264 9.60021C9.51352 9.76302 9.51264 9.9267 9.51352 10.0895Z" fill="#999999"/>
                                                    <path d="M10.804 8.14334C10.804 8.20111 10.811 8.26063 10.8022 8.31752C10.7856 8.43831 10.8241 8.47595 10.951 8.47333C11.2915 8.46545 11.2915 8.47245 11.2915 8.12496C11.2915 8.10133 11.288 8.07769 11.2924 8.05406C11.3169 7.90176 11.2661 7.84312 11.099 7.85187C10.804 7.86762 10.804 7.85624 10.804 8.14334ZM10.418 8.15735V7.66718C10.418 7.53064 10.495 7.46061 10.6228 7.45799C10.9029 7.45186 11.1821 7.45361 11.4622 7.45711C11.5987 7.45886 11.6793 7.52976 11.6793 7.67418C11.6793 8.00067 11.6801 8.32715 11.6793 8.65276C11.6784 8.78668 11.6057 8.85758 11.4727 8.85845C11.1874 8.8602 10.902 8.86108 10.6167 8.85758C10.4819 8.85583 10.4171 8.78055 10.418 8.64663C10.418 8.48383 10.418 8.32015 10.418 8.15735Z" fill="#999999"/>
                                                    <path d="M0.708108 22.2919H22.2919V0.70722H0.708108V22.2919ZM23 23H0V0H23V23Z" fill="#999999"/>
                                                </svg>
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M22.1487 19.5927H21.2969V18.7408H22.1487V19.5927Z" fill="#999999"/>
                                                    <path d="M6.35088 19.5927H3.4082V18.7408H6.35088V19.5927ZM10.7653 19.5927H7.82265V18.7408H10.7653V19.5927ZM15.1789 19.5927H12.2362V18.7408H15.1789V19.5927ZM19.5934 19.5927H16.6507V18.7408H19.5934V19.5927Z" fill="#999999"/>
                                                    <path d="M1.70341 19.5927H0.851562V18.7408H1.70341V19.5927Z" fill="#999999"/>
                                                    <path d="M8.28184 6.67969L6.10742 5.78923V5.28879L8.28184 4.40272V4.91193L6.29322 5.71036V5.37819L8.28184 6.17486V6.67969Z" fill="#999999"/>
                                                    <path d="M9.81101 6.85693C9.5919 6.85693 9.38243 6.82713 9.18173 6.76578C8.98103 6.70531 8.81889 6.6203 8.69531 6.51162L8.90741 5.99365C9.04326 6.09356 9.18436 6.16806 9.33072 6.21539C9.47621 6.26184 9.63134 6.28638 9.79348 6.28638C9.97315 6.28638 10.109 6.25307 10.201 6.18559C10.2922 6.11898 10.3377 6.01994 10.3377 5.8876C10.3377 5.75964 10.2948 5.66587 10.2072 5.60364C10.1204 5.54141 9.99243 5.51074 9.82415 5.51074H9.29216V4.96122H9.75755C9.90829 4.96122 10.0266 4.92704 10.1125 4.85955C10.1975 4.79207 10.2405 4.69566 10.2405 4.57208C10.2405 4.45639 10.2001 4.36875 10.1186 4.30828C10.038 4.24693 9.92056 4.21713 9.76719 4.21713C9.46569 4.21713 9.19663 4.31529 8.96087 4.50898L8.74352 4.00416C8.87586 3.89197 9.038 3.80521 9.22818 3.74035C9.41924 3.67725 9.61732 3.6457 9.82415 3.6457C10.1607 3.6457 10.4245 3.72019 10.6147 3.86919C10.8058 4.01905 10.9004 4.22414 10.9004 4.48707C10.9004 4.66761 10.8496 4.82537 10.7479 4.95859C10.6462 5.09356 10.5051 5.1812 10.3246 5.22327V5.16455C10.5376 5.19698 10.7032 5.28374 10.8207 5.42397C10.939 5.5642 10.9977 5.74124 10.9977 5.95333C10.9977 6.23379 10.8925 6.45465 10.6813 6.61592C10.4701 6.7763 10.18 6.85693 9.81101 6.85693Z" fill="#999999"/>
                                                    <path d="M11.7472 7.45093L11.4861 7.24234C11.5921 7.1398 11.6657 7.04076 11.7078 6.94786C11.749 6.85496 11.77 6.7568 11.77 6.65338L11.9155 6.81201H11.4238V6.10386H12.146V6.5596C12.146 6.72875 12.1153 6.88212 12.0557 7.02148C11.9944 7.15995 11.8918 7.30281 11.7472 7.45093Z" fill="#999999"/>
                                                    <path d="M13.691 6.85693C13.5525 6.85693 13.4158 6.84291 13.2817 6.81486C13.1467 6.78682 13.0205 6.74738 12.9031 6.69479C12.7847 6.64396 12.6839 6.58261 12.6016 6.51162L12.8145 5.99365C12.953 6.09093 13.0932 6.16368 13.2352 6.21276C13.3763 6.26096 13.5244 6.28638 13.6778 6.28638C13.8487 6.28638 13.9828 6.24519 14.0783 6.16368C14.1748 6.08217 14.2221 5.97262 14.2221 5.83414C14.2221 5.69216 14.1765 5.57822 14.0854 5.49233C13.9942 5.40732 13.8698 5.36437 13.7138 5.36437C13.5981 5.36437 13.492 5.38541 13.3965 5.42835C13.3001 5.4713 13.2124 5.5344 13.1336 5.61679H12.6945V3.69039H14.6875V4.25218H13.3457V5.09882H13.1818C13.258 5.00416 13.3579 4.93317 13.4806 4.88321C13.6033 4.83501 13.7383 4.81047 13.8855 4.81047C14.0862 4.81047 14.2615 4.85166 14.4114 4.93492C14.5604 5.01731 14.6761 5.133 14.7585 5.28199C14.8408 5.43186 14.8829 5.60802 14.8829 5.81223C14.8829 6.0217 14.8347 6.20487 14.7392 6.36175C14.6428 6.51688 14.5052 6.63958 14.3273 6.72722C14.1476 6.81311 13.9364 6.85693 13.691 6.85693Z" fill="#999999"/>
                                                    <path d="M17.0444 6.81226V5.0892H16.3047V4.57123H18.4572V5.0892H17.7131V6.81226H17.0444Z" fill="#999999"/>
                                                    <path d="M7.91358 12.2978H15.8742L15.2782 10.5669C15.1406 10.1699 15.0889 10.1339 14.6577 10.1339H9.13445C9.07923 10.1339 9.02402 10.1383 8.9688 10.1418L8.89781 10.1471C8.72253 10.1576 8.62524 10.233 8.56214 10.4065C8.48589 10.6169 8.41315 10.8281 8.34128 11.0393L7.91358 12.2978ZM16.6279 12.8351H7.16248L7.83207 10.8666C7.90482 10.6519 7.97931 10.4372 8.05644 10.2233C8.19141 9.84998 8.47888 9.63263 8.86626 9.60984C8.99948 9.60108 9.06696 9.59669 9.13445 9.59669L14.6577 9.59582C15.3115 9.59582 15.5753 9.78162 15.7865 10.3907L16.6279 12.8351ZM7.32374 14.2584C7.12304 14.2584 6.96178 14.3838 6.89166 14.5994C6.83119 14.7869 6.85836 14.9569 6.97843 15.134C7.08623 15.2944 7.26678 15.3557 7.46222 15.2987C7.63488 15.247 7.77949 15.0174 7.77949 14.7948C7.77861 14.5354 7.61034 14.2979 7.40438 14.2654C7.37633 14.2611 7.35004 14.2584 7.32374 14.2584ZM7.32287 15.857C7.00823 15.857 6.71638 15.7063 6.53321 15.4355C6.31848 15.12 6.26765 14.7825 6.37983 14.4337C6.54109 13.9359 6.9872 13.6554 7.48764 13.7343C7.95039 13.8071 8.31499 14.2716 8.31762 14.7922C8.31849 15.2558 8.01612 15.6958 7.61384 15.815C7.51656 15.843 7.4184 15.857 7.32287 15.857ZM16.4667 14.2505C16.4123 14.2505 16.3571 14.2602 16.3054 14.2812C16.1266 14.3557 16.0083 14.552 16.0109 14.7703V14.8097C16.0241 15.0893 16.2072 15.2926 16.4281 15.3215C16.613 15.3461 16.8199 15.198 16.8979 14.9841C16.9794 14.7632 16.9312 14.5652 16.7462 14.3618C16.6805 14.2891 16.5745 14.2505 16.4667 14.2505ZM16.4702 15.8623C16.4334 15.8623 16.3957 15.8597 16.358 15.8553C15.8821 15.7922 15.5017 15.3776 15.4737 14.8886C15.4719 14.8605 15.4728 14.8307 15.4728 14.8001L15.7418 14.7738L15.4728 14.7773C15.4675 14.3347 15.7129 13.9455 16.0994 13.7843C16.4623 13.6327 16.8926 13.7229 17.1441 13.9999C17.4649 14.3531 17.5543 14.7571 17.4036 15.1682C17.2519 15.5853 16.8707 15.8623 16.4702 15.8623ZM17.301 12.4503L17.3194 12.467C17.3133 12.4617 17.3072 12.4556 17.301 12.4503ZM6.71287 12.3049H6.95652L6.99771 12.7229L6.61121 13.077C6.39561 13.2716 6.18088 13.4679 5.97141 13.6677C5.75756 13.8737 5.64801 14.134 5.64626 14.4433C5.63837 15.7352 5.64188 17.0271 5.64451 18.3172C5.64538 18.4521 5.68044 18.6327 5.98193 18.6336C6.30709 18.6362 6.63224 18.6353 6.95827 18.6344C7.22383 18.6336 7.329 18.5336 7.33163 18.2786L7.33426 18.1305C7.33864 17.9096 7.34215 17.6896 7.329 17.4697C7.32637 17.4144 7.31586 17.2444 7.43856 17.1138C7.56213 16.9824 7.75144 16.9824 7.80666 16.9832C10.542 16.9841 13.2773 16.985 16.0127 16.9815C16.1213 16.9841 16.2484 16.9938 16.3545 17.1033C16.4719 17.2243 16.4675 17.3855 16.4658 17.4381L16.4614 17.5661C16.4526 17.85 16.4439 18.1191 16.4833 18.382C16.4965 18.4714 16.5482 18.6213 16.7454 18.6265C17.1284 18.6371 17.5114 18.6379 17.8944 18.6257C18.0372 18.6213 18.1187 18.5538 18.1441 18.4188C18.159 18.3382 18.1687 18.2549 18.1687 18.1726C18.1713 16.9683 18.1713 15.765 18.1696 14.5617C18.1687 14.1454 18.0153 13.8123 17.7007 13.5406C17.5131 13.3794 17.3326 13.2111 17.152 13.0428L16.9776 12.8789C16.7778 12.7326 16.8435 12.531 16.8681 12.4714L16.9382 12.3092L17.3431 12.3101C17.5087 12.3119 17.6753 12.3127 17.8427 12.3084C17.9995 12.304 18.1415 12.2391 18.2441 12.1243C18.3431 12.0121 18.3913 11.8684 18.3773 11.7194C18.3624 11.5538 18.3221 11.5187 18.152 11.5178L17.798 11.5187C17.5762 11.5187 17.3562 11.5196 17.1362 11.517C17.0153 11.5134 16.9873 11.545 16.9426 11.637C16.8935 11.7992 16.7795 11.8544 16.6928 11.8719L16.464 11.9166L16.2186 11.2155C16.0951 10.8588 15.9715 10.5038 15.8383 10.1515C15.6376 9.6186 15.3448 9.42053 14.7602 9.41965C12.847 9.41703 10.9329 9.41703 9.01964 9.41965C8.46398 9.42053 8.14584 9.63876 7.95653 10.1497C7.87852 10.3592 7.80578 10.5695 7.73391 10.7816L7.08799 12.6169L6.87764 11.7264C6.83294 11.5406 6.77335 11.5029 6.55512 11.5134C6.3474 11.5248 6.13881 11.5222 5.93022 11.5205L5.59805 11.5187C5.50077 11.5196 5.4552 11.5546 5.4289 11.6502C5.38946 11.7913 5.42452 11.9499 5.52443 12.0831C5.6261 12.219 5.7786 12.3014 5.93285 12.304C6.11164 12.3075 6.29043 12.3066 6.46835 12.3057L6.71287 12.3049ZM6.43154 19.1743C6.27992 19.1743 6.12917 19.1734 5.97843 19.1726C5.4587 19.1682 5.10813 18.8255 5.10638 18.3189C5.10375 17.0262 5.10024 15.7335 5.10813 14.4398C5.11076 13.9867 5.28079 13.5853 5.59893 13.2795C5.75318 13.1322 5.91006 12.9876 6.06782 12.8439C6.01962 12.843 5.97229 12.843 5.92409 12.8421C5.59981 12.8369 5.29744 12.6782 5.09323 12.4057C4.89341 12.1375 4.8268 11.8097 4.91006 11.5064C5.00033 11.1804 5.25625 10.9841 5.59367 10.9806L5.93548 10.9823C6.13355 10.985 6.33075 10.9876 6.52707 10.9771C6.73829 10.9657 6.91883 11.0016 7.05819 11.0857L7.22471 10.6063C7.29833 10.3907 7.37282 10.176 7.4517 9.96216C7.71726 9.24612 8.24487 8.8824 9.01876 8.88153C10.9338 8.8789 12.847 8.8789 14.7611 8.88153C15.5727 8.88328 16.06 9.21545 16.3413 9.96129C16.4763 10.318 16.6016 10.6782 16.7261 11.0384L16.741 11.0796C16.8549 11.0113 16.9916 10.9779 17.1433 10.9788C17.3606 10.9815 17.5788 10.9815 17.7962 10.9806H18.1538C18.6025 10.9815 18.8725 11.226 18.9128 11.6703C18.9399 11.9701 18.8453 12.2575 18.6455 12.4819C18.4439 12.7089 18.1643 12.8377 17.8567 12.8456C17.8155 12.8474 17.7743 12.8474 17.7331 12.8482C17.8374 12.9447 17.9434 13.0402 18.0512 13.1331C18.486 13.5073 18.7068 13.9876 18.7077 14.5608C18.7094 15.765 18.7086 16.9683 18.7068 18.1726C18.7068 18.2874 18.6945 18.4048 18.6726 18.5187C18.6008 18.9035 18.3089 19.1498 17.9119 19.1629C17.5184 19.1761 17.124 19.1752 16.7313 19.1638C16.3256 19.1524 16.0127 18.8711 15.9513 18.4618C15.904 18.1506 15.9145 17.8439 15.9241 17.5477L15.925 17.5205C13.2239 17.5222 10.5473 17.5222 7.87064 17.5205C7.87852 17.7361 7.8759 17.9386 7.87239 18.1401L7.86976 18.2865C7.86275 18.8308 7.51393 19.1708 6.96003 19.1726C6.78386 19.1734 6.6077 19.1743 6.43154 19.1743Z" fill="#999999"/>
                                                    <path d="M0.707233 22.2918H22.291V0.707247H0.707233V22.2918ZM23 23H0V0H23V23Z" fill="#999999"/>
                                                </svg>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M24.0004 19.9822H22.2227V19.1607H24.0004V19.9822Z" fill="#999999"/>
                                                    <path d="M6.78693 19.9822H3.55469V19.1607H6.78693V19.9822ZM11.6358 19.9822H8.40352V19.1607H11.6358V19.9822ZM16.4846 19.9822H13.2523V19.1607H16.4846V19.9822ZM21.3325 19.9822H18.1002V19.1607H21.3325V19.9822Z" fill="#999999"/>
                                                    <path d="M2.66645 19.9822H0.888672V19.1607H2.66645V19.9822Z" fill="#999999"/>
                                                    <path d="M20.33 18.0546H19.4815C19.5071 17.9128 19.5208 17.7693 19.5208 17.625C19.5208 16.1859 18.254 15.0152 16.6958 15.0152H14.4453V10.3282H18.8933C18.9536 10.3282 19.0121 10.351 19.055 10.3898L19.7711 11.0524H16.4894C16.2437 11.0524 16.0437 11.2373 16.0437 11.4643V13.3702C16.0437 13.598 16.2437 13.7829 16.4894 13.7829H19.0431C19.15 13.7829 19.2541 13.8183 19.3354 13.8833L20.468 14.7789V14.9443H19.8852C19.6934 14.9443 19.5382 15.0886 19.5382 15.2659V16.1757C19.5382 16.3538 19.6934 16.4982 19.8852 16.4982H20.468V17.9271C20.468 17.998 20.4059 18.0546 20.33 18.0546ZM18.3901 18.0546C18.4312 17.9178 18.4531 17.7735 18.4531 17.625C18.4531 16.7303 17.6649 16.001 16.6958 16.001C16.2638 16.001 15.8674 16.1471 15.5614 16.3868H14.4453V15.4887H16.6958C17.9708 15.4887 19.0084 16.4475 19.0084 17.625C19.0084 17.7693 18.9929 17.9128 18.9609 18.0546H18.3901ZM16.6958 18.7754C16.0099 18.7754 15.4509 18.2597 15.4509 17.625C15.4509 16.9911 16.0099 16.4745 16.6958 16.4745C17.3826 16.4745 17.9416 16.9911 17.9416 17.625C17.9416 18.2597 17.3826 18.7754 16.6958 18.7754ZM18.1718 9.5956C18.2229 9.61586 18.2558 9.66228 18.2558 9.71377V9.85473H14.4453V8.11178L18.1718 9.5956ZM20.4013 11.6348C20.4442 11.6736 20.468 11.7268 20.468 11.7834V14.156L19.6688 13.5238C19.4943 13.3862 19.2724 13.3102 19.0431 13.3102H16.5551V11.5259H20.2825L20.4013 11.6348ZM20.468 16.0255H20.0496V15.4178H20.468V16.0255ZM7.51563 18.7754C6.8297 18.7754 6.27072 18.2597 6.27072 17.625C6.27072 16.9911 6.8297 16.4745 7.51563 16.4745C8.20247 16.4745 8.76054 16.9911 8.76054 17.625C8.76054 18.2597 8.20247 18.7754 7.51563 18.7754ZM5.02673 17.6874V16.8603H5.96657C5.83414 17.0882 5.75924 17.349 5.75924 17.625C5.75924 17.6469 5.76015 17.6672 5.76107 17.6874H5.02673ZM20.763 11.2997L19.4167 10.0548C19.2769 9.92647 19.0915 9.85473 18.8933 9.85473H18.7673V9.71377C18.7673 9.47322 18.6129 9.2563 18.3745 9.16092L14.4453 7.59607V7.04407C14.4453 6.82631 14.2544 6.64906 14.0187 6.64906H6.8096C6.66803 6.64906 6.55386 6.75541 6.55386 6.88623C6.55386 7.01706 6.66803 7.12257 6.8096 7.12257H13.9338V16.3868H8.65093C8.34404 16.1471 7.94856 16.001 7.51563 16.001C7.08361 16.001 6.68721 16.1471 6.38032 16.3868H4.01929V7.12257H5.61493C5.7565 7.12257 5.87067 7.01706 5.87067 6.88623C5.87067 6.75541 5.7565 6.64906 5.61493 6.64906H3.93435C3.6987 6.64906 3.50781 6.82631 3.50781 7.04407V16.4661C3.50781 16.6839 3.6987 16.8603 3.93435 16.8603H4.51525V17.7668C4.51525 17.9837 4.70705 18.1609 4.94178 18.1609H5.85788C6.09718 18.794 6.75023 19.2489 7.51563 19.2489C8.28194 19.2489 8.93407 18.794 9.17429 18.1609H12.741C12.8825 18.1609 12.9967 18.0546 12.9967 17.9246C12.9967 17.7929 12.8825 17.6874 12.741 17.6874H9.2711C9.27202 17.6672 9.27293 17.6469 9.27293 17.625C9.27293 17.349 9.19712 17.0882 9.06468 16.8603H15.1467C15.0143 17.0882 14.9394 17.349 14.9394 17.625C14.9394 17.6469 14.9403 17.6672 14.9412 17.6874H13.9356C13.7941 17.6874 13.679 17.7929 13.679 17.9246C13.679 18.0546 13.7941 18.1609 13.9356 18.1609H15.038C15.2783 18.794 15.9304 19.2489 16.6958 19.2489C17.3041 19.2489 17.8402 18.9619 18.1563 18.5281H20.33C20.6881 18.5281 20.9794 18.258 20.9794 17.9271V11.7834C20.9794 11.601 20.9027 11.428 20.763 11.2997Z" fill="#999999"/>
                                                    <path d="M16.6974 17.8667C16.5531 17.8667 16.4362 17.7587 16.4362 17.6253C16.4362 17.4919 16.5531 17.3839 16.6974 17.3839C16.8418 17.3839 16.9587 17.4919 16.9587 17.6253C16.9587 17.7587 16.8418 17.8667 16.6974 17.8667ZM16.6974 16.9104C16.2709 16.9104 15.9238 17.2311 15.9238 17.6253C15.9238 18.0195 16.2709 18.3402 16.6974 18.3402C17.124 18.3402 17.4711 18.0195 17.4711 17.6253C17.4711 17.2311 17.124 16.9104 16.6974 16.9104Z" fill="#999999"/>
                                                    <path d="M7.5158 17.8667C7.37149 17.8667 7.25458 17.7587 7.25458 17.6253C7.25458 17.4919 7.37149 17.3839 7.5158 17.3839C7.66011 17.3839 7.77702 17.4919 7.77702 17.6253C7.77702 17.7587 7.66011 17.8667 7.5158 17.8667ZM7.5158 16.9104C7.08926 16.9104 6.74219 17.2311 6.74219 17.6253C6.74219 18.0195 7.08926 18.3402 7.5158 18.3402C7.94234 18.3402 8.28942 18.0195 8.28942 17.6253C8.28942 17.2311 7.94234 16.9104 7.5158 16.9104Z" fill="#999999"/>
                                                    <path d="M5.61512 8.16019C5.47354 8.16019 5.35938 8.2657 5.35938 8.39652V15.1126C5.35938 15.2442 5.47354 15.3497 5.61512 15.3497C5.75669 15.3497 5.87086 15.2442 5.87086 15.1126V8.39652C5.87086 8.2657 5.75669 8.16019 5.61512 8.16019Z" fill="#999999"/>
                                                    <path d="M8.11304 15.1136V8.39676C8.11304 8.26593 7.99887 8.16043 7.8573 8.16043C7.71573 8.16043 7.60156 8.26593 7.60156 8.39676V15.1128C7.60156 15.2436 7.71573 15.3491 7.8573 15.3491C7.99887 15.3491 8.11304 15.2436 8.11304 15.1136Z" fill="#999999"/>
                                                    <path d="M10.3542 15.1136V8.39676C10.3542 8.26593 10.24 8.16043 10.0985 8.16043C9.95688 8.16043 9.8418 8.26593 9.8418 8.39676V15.1128C9.8418 15.2436 9.95688 15.3491 10.0985 15.3491C10.24 15.3491 10.3542 15.2436 10.3542 15.1136Z" fill="#999999"/>
                                                    <path d="M12.5964 15.1136V8.39676C12.5964 8.26593 12.4822 8.16043 12.3406 8.16043C12.1991 8.16043 12.084 8.26593 12.084 8.39676V15.1128C12.084 15.2436 12.1991 15.3491 12.3406 15.3491C12.4822 15.3491 12.5964 15.2436 12.5964 15.1136Z" fill="#999999"/>
                                                    <path d="M0.738902 23.2765H23.2611V1.69187H0.738902V23.2765ZM24 23.9846H0V0.984619H24V23.9846Z" fill="#999999"/>
                                                </svg>
                                                <svg width="23" height="24" viewBox="0 0 23 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16.452 18.5354H15.1005V13.2953L16.9901 13.8202L18.6667 14.2856L16.452 18.5354ZM11.0093 14.2856L12.6842 13.8202H12.686L14.5755 13.2953V18.5354H13.2215L11.0093 14.2856ZM12.8779 12.276H13.7841H15.8902H16.7982V13.2225L14.9077 12.6975C14.8621 12.6844 14.8139 12.6844 14.7675 12.6975L12.8779 13.2225V12.276ZM14.0462 10.8728H15.6273V11.7528H14.0462V10.8728ZM19.1181 13.8667L17.3223 13.368V12.0148C17.3223 11.8702 17.2048 11.7528 17.0602 11.7528H16.1522V10.6108C16.1522 10.4662 16.0348 10.3487 15.8902 10.3487H13.7841C13.6395 10.3487 13.5221 10.4662 13.5221 10.6108V11.7528H12.6158C12.4712 11.7528 12.3538 11.8702 12.3538 12.0148V13.368L10.5571 13.8667C10.4809 13.8877 10.4186 13.9429 10.3871 14.0157C10.3555 14.0876 10.3582 14.1708 10.395 14.2409L12.8297 18.9184C12.8753 19.0052 12.9647 19.0604 13.0628 19.0604H16.6106C16.7088 19.0604 16.7982 19.0052 16.8437 18.9184L19.2811 14.2409C19.3179 14.1708 19.3205 14.0876 19.289 14.0157C19.2574 13.9429 19.1952 13.8877 19.1181 13.8667Z" fill="#999999"/>
                                                    <path d="M10.4437 18.3018H4.18426V17.4227H6.49541H8.36659H10.4437V18.3018ZM6.75746 7.80304V5.93274V3.92922L8.10453 4.26577V5.93274V7.80304V16.8978H6.75746V7.80304ZM5.42792 7.54187L5.76446 6.19479H6.23335V7.54187H5.42792ZM17.7751 6.19479L18.2808 7.54187H8.62864V6.19479H17.7751ZM10.7058 16.8978H8.62864V8.06597H18.6585C18.7444 8.06597 18.825 8.02303 18.8741 7.95291C18.9232 7.88192 18.9346 7.79253 18.9048 7.71189L18.2028 5.83984C18.1642 5.73817 18.0669 5.67069 17.9574 5.67069H8.62864V4.06069C8.62864 3.94062 8.54625 3.83544 8.42969 3.80652L6.55851 3.33938C6.48138 3.31923 6.39812 3.33675 6.33414 3.38671C6.27016 3.43667 6.23335 3.51292 6.23335 3.59355V5.67069H5.55938C5.43931 5.67069 5.33414 5.7522 5.30522 5.86876L4.8372 7.73994C4.81792 7.81882 4.83545 7.90208 4.88453 7.96518C4.93449 8.02829 5.01073 8.06597 5.09137 8.06597H6.23335V16.8978H3.92221C3.7776 16.8978 3.66016 17.0152 3.66016 17.1607V18.5639C3.66016 18.7093 3.7776 18.8268 3.92221 18.8268H10.7058C10.8504 18.8268 10.9678 18.7093 10.9678 18.5639V17.1607C10.9678 17.0152 10.8504 16.8978 10.7058 16.8978Z" fill="#999999"/>
                                                    <path d="M0.708102 22.3225H22.2919V0.737886H0.708102V22.3225ZM23 23.0306H0V0.0306396H23V23.0306Z" fill="#999999"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <?
                                    } else if ($arResult['PROPERTIES']['EXPLUATATION_GROUPS']['VALUE'] == 'expluatation_group_b') {
                                        ?>
                                        <h4>
                                            Группа эксплуатации Б
                                        </h4>
                                        <div class="icons">
                                            <div class="icons">
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M22.5032 19.7228H21.1465V19.0068H22.5032V19.7228Z" fill="#003064"/>
                                                    <path d="M6.306 19.7228H3.33789V19.0068H6.306V19.7228ZM10.7586 19.7228H7.79049V19.0068H10.7586V19.7228ZM15.2112 19.7228H12.2431V19.0068H15.2112V19.7228ZM19.6629 19.7228H16.6948V19.0068H19.6629V19.7228Z" fill="#003064"/>
                                                    <path d="M1.85475 19.7228H0.498047V19.0068H1.85475V19.7228Z" fill="#003064"/>
                                                    <path d="M9.88463 9.92206C9.97741 9.92206 10.0483 9.9667 10.0772 9.98771C10.2514 10.119 10.2076 10.3238 10.1936 10.3912L10.1201 10.814C10.0527 11.2079 9.98529 11.6017 9.90301 11.9921C9.77785 12.5926 9.92927 13.0258 10.3949 13.3996C10.886 13.7926 11.3665 14.1996 11.8462 14.6066C12.0877 14.8114 12.3293 15.0163 12.5726 15.2202C12.6847 15.313 12.7608 15.4206 12.8107 15.5572L13.3989 17.1765C13.6388 17.8329 13.8777 18.4894 14.1149 19.1468C14.3976 19.0382 14.6725 18.9358 14.9491 18.8395C14.521 17.6693 14.1035 16.5165 13.686 15.3637L13.5127 14.8858C13.4996 14.8491 13.4838 14.8254 13.4558 14.8027L12.9394 14.3703C12.7013 14.1707 12.4641 13.9712 12.2225 13.7751C12.0457 13.6315 11.981 13.4495 12.0247 13.2193C12.0799 12.9287 12.1306 12.6372 12.1823 12.3466L12.5867 10.0831L13.0567 10.603C13.9486 11.5842 15.0138 12.1015 16.3093 12.1812C16.3058 11.881 16.3058 11.586 16.3101 11.2919C15.8226 11.2674 15.3587 11.144 14.9176 10.9243C14.1596 10.5453 13.6458 10.0621 13.3482 9.4494C13.2764 9.3006 13.2212 9.14393 13.1661 8.98812C13.1346 8.89447 13.1022 8.80169 13.0654 8.71066C12.893 8.28527 12.4904 7.9439 12.0168 7.82224C11.5801 7.70932 11.1521 7.79773 10.8116 8.07257C10.3547 8.44019 9.8715 8.78243 9.4041 9.11329C9.19666 9.26034 8.98921 9.40651 8.78352 9.55619C8.71087 9.60958 8.66098 9.64722 8.62684 9.69361C8.15681 10.3457 7.68415 10.9969 7.21062 11.6455C7.45395 11.8171 7.69291 11.9921 7.93011 12.1681C8.09204 11.944 8.25134 11.7252 8.40977 11.5072C8.63122 11.2026 8.85267 10.898 9.06974 10.5917C9.24917 10.3378 9.42948 10.1601 9.63868 10.0341C9.72796 9.94919 9.81286 9.92206 9.88463 9.92206ZM14.0099 19.7314C13.9635 19.7314 13.9145 19.7227 13.8637 19.6999C13.7202 19.6352 13.6685 19.4934 13.6501 19.44C13.3998 18.7459 13.1468 18.0535 12.8948 17.3603L12.3057 15.7401C12.2882 15.6946 12.2672 15.6648 12.2287 15.6316C11.9845 15.4276 11.7411 15.2219 11.4987 15.0163C11.0225 14.6119 10.5464 14.2084 10.0588 13.818C9.43298 13.3164 9.20978 12.6827 9.37784 11.8818C9.45224 11.5273 9.51439 11.1693 9.57566 10.8114C9.55377 10.8394 9.53102 10.87 9.50826 10.9015C9.28944 11.2114 9.06711 11.516 8.84391 11.8223C8.65748 12.0788 8.47104 12.3352 8.28635 12.5934C8.25572 12.6372 8.17344 12.751 8.02377 12.7746C7.87322 12.7965 7.76205 12.7134 7.72004 12.6819C7.40931 12.4499 7.09596 12.2197 6.77648 11.9974C6.73972 11.972 6.6268 11.8932 6.6023 11.7453C6.57779 11.6 6.65656 11.4923 6.6942 11.4407C7.19487 10.7553 7.69466 10.0691 8.19095 9.38026C8.2776 9.25947 8.38264 9.18244 8.46754 9.12117C8.67498 8.97062 8.88418 8.82269 9.09337 8.67477C9.55465 8.34829 10.0308 8.0113 10.4737 7.65418C10.9499 7.2708 11.5442 7.14476 12.1516 7.30144C12.7871 7.46599 13.328 7.92902 13.5626 8.50847C13.6029 8.60825 13.6388 8.70978 13.6738 8.81132C13.7219 8.94874 13.7683 9.08528 13.8313 9.21483C14.079 9.72425 14.5 10.1146 15.1583 10.4437C15.567 10.6477 15.9985 10.7536 16.4388 10.758C16.4931 10.7588 16.6375 10.7606 16.7452 10.8709C16.8169 10.9444 16.8519 11.0451 16.8493 11.1711C16.8414 11.5545 16.8423 11.9387 16.8493 12.3212C16.8502 12.3694 16.8528 12.5155 16.7425 12.6241C16.6314 12.7326 16.4878 12.7265 16.4274 12.7247C15.0655 12.6748 13.8891 12.1742 12.9245 11.2394L12.7109 12.4385C12.6593 12.7326 12.6085 13.0267 12.5525 13.3199C12.8055 13.5554 13.0444 13.7576 13.2843 13.9589L13.7981 14.3887C13.9005 14.4727 13.9722 14.5751 14.0178 14.7029L14.1911 15.1808C14.6261 16.3791 15.0593 17.5774 15.4979 18.7739C15.5189 18.8316 15.5688 18.9656 15.5022 19.103C15.4375 19.2413 15.3001 19.2877 15.2493 19.3052C14.8939 19.4251 14.5429 19.5573 14.1937 19.6921C14.1447 19.7113 14.0808 19.7314 14.0099 19.7314Z" fill="#003064"/>
                                                    <path d="M7.59074 18.2423C7.82357 18.4331 8.05027 18.623 8.27347 18.8147L8.44765 18.6038C8.84766 18.1162 9.24854 17.6287 9.66781 17.1587C10.2 16.5608 10.5483 16.0173 10.7628 15.4475C10.7777 15.4089 10.7882 15.3801 10.7969 15.3599C10.7821 15.3468 10.7637 15.3328 10.7435 15.3153L9.97854 14.6746L9.62229 15.7188C9.58028 15.8431 9.52076 15.9499 9.43848 16.047L8.71987 16.9022C8.34437 17.3486 7.96887 17.7959 7.59074 18.2423ZM8.29623 19.4257C8.15706 19.4257 8.05815 19.3399 8.01964 19.3057C7.72991 19.0545 7.43582 18.8077 7.13822 18.567C7.09796 18.5346 6.99117 18.448 6.97804 18.2992C6.96404 18.1512 7.05332 18.0453 7.08745 18.0051C7.49621 17.5237 7.90235 17.0396 8.30936 16.5556L9.02797 15.7022C9.06561 15.6567 9.09274 15.6068 9.11287 15.5472L9.7247 13.7608L11.0893 14.9048C11.3729 15.1437 11.407 15.2663 11.281 15.5954C11.0271 16.2702 10.6464 16.8672 10.0687 17.5158C9.6538 17.9806 9.25817 18.4628 8.86254 18.9451L8.60608 19.2567C8.57107 19.2996 8.47829 19.4117 8.32424 19.4248C8.31461 19.4257 8.30498 19.4257 8.29623 19.4257Z" fill="#003064"/>
                                                    <path d="M12.6434 6.8816C12.9226 6.89998 13.2088 6.8002 13.4355 6.60063C13.6692 6.39318 13.8145 6.09734 13.832 5.79011C13.867 5.19754 13.3751 4.62597 12.7992 4.59009C12.4631 4.56733 12.1462 4.67411 11.9064 4.88681C11.6788 5.08813 11.544 5.36997 11.5274 5.67982C11.4933 6.31266 11.9834 6.83958 12.6434 6.8816ZM12.7099 7.42078C12.6758 7.42078 12.6434 7.4199 12.6093 7.41815C11.6508 7.35776 10.9401 6.5805 10.9908 5.65006C11.0162 5.19666 11.214 4.78352 11.5501 4.48505C11.8976 4.17607 12.3563 4.0229 12.8333 4.05353C13.7034 4.10868 14.4202 4.93407 14.3686 5.82074C14.3423 6.27064 14.1314 6.70129 13.7909 7.00326C13.4837 7.2746 13.1029 7.42078 12.7099 7.42078Z" fill="#003064"/>
                                                    <path d="M0.70811 22.2919H22.2919V0.70722H0.70811V22.2919ZM23 23H0V0H23V23Z" fill="#003064"/>
                                                </svg>
                                                <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M24.0004 19.5927H22.2227V18.7408H24.0004V19.5927Z" fill="#003064"/>
                                                    <path d="M6.62548 19.5927H3.55469V18.7408H6.62548V19.5927ZM11.2312 19.5927H8.16042V18.7408H11.2312V19.5927ZM15.8378 19.5927H12.7671V18.7408H15.8378V19.5927ZM20.4436 19.5927H17.3728V18.7408H20.4436V19.5927Z" fill="#003064"/>
                                                    <path d="M1.77756 19.5927H0.888672V18.7408H1.77756V19.5927Z" fill="#003064"/>
                                                    <path d="M13.1368 15.5908C13.1368 15.3007 12.8816 15.0579 12.5771 15.0562C12.2625 15.0544 12.0027 15.3025 12.0073 15.5987C12.0128 15.8888 12.2743 16.1343 12.5752 16.1308C12.8798 16.1273 13.1368 15.881 13.1368 15.5908ZM10.3116 9.06973C10.0464 9.06973 9.78114 9.06798 9.51591 9.0706C9.30189 9.07236 9.17933 9.17228 9.17842 9.33794C9.1775 9.50359 9.30189 9.60877 9.51408 9.60965C10.0391 9.61315 10.564 9.61228 11.089 9.60965C11.2985 9.60877 11.432 9.50009 11.4329 9.33969C11.4338 9.18017 11.2994 9.07236 11.0909 9.0706C10.8311 9.06798 10.5714 9.06973 10.3116 9.06973ZM8.0434 15.3147H10.1689C10.2284 15.3147 10.2915 15.3305 10.275 15.2306C10.1753 14.6267 9.91193 14.1034 9.45829 13.6704C9.39975 13.6135 9.37048 13.6135 9.3211 13.6775C8.90312 14.2174 8.48241 14.7538 8.0434 15.3147ZM10.768 11.8307C10.436 12.2558 10.125 12.6607 9.80492 13.0595C9.74547 13.1349 9.74364 13.1726 9.81681 13.2392C10.3162 13.6985 10.639 14.2516 10.7936 14.8993C10.8274 15.0369 10.7671 15.235 10.907 15.2998C11.0323 15.3586 11.2116 15.3104 11.3661 15.3165C11.443 15.3182 11.486 15.2998 11.5107 15.2236C11.5701 15.0439 11.678 14.8879 11.8335 14.7713C11.9085 14.7161 11.8994 14.6653 11.8719 14.5978C11.7832 14.3813 11.6963 14.1648 11.6094 13.9483C11.3314 13.2488 11.0534 12.5494 10.768 11.8307ZM11.2829 11.5248C11.2829 11.5449 11.2783 11.5616 11.2829 11.5756C11.6597 12.5257 12.0375 13.4767 12.4143 14.4269C12.4381 14.4856 12.4609 14.5136 12.5442 14.5075C12.6566 14.4978 12.7783 14.5005 12.8844 14.5504C12.9841 14.5969 13.0298 14.5496 13.0838 14.4794C13.8145 13.5425 14.548 12.6073 15.2797 11.6694C15.3136 11.6273 15.3712 11.5923 15.3675 11.5248H11.2829ZM7.42147 12.8851C7.27147 12.8772 7.07758 12.893 6.88917 12.9333C5.32886 13.2655 4.41609 14.6845 4.68498 16.0896C4.95936 17.5244 6.45839 18.5376 7.94554 18.2668C9.16287 18.045 10.0336 17.1957 10.2604 16.0265C10.2906 15.8704 10.2403 15.8643 10.1104 15.8643C9.26896 15.8687 8.42662 15.8669 7.58518 15.8669C7.43793 15.8669 7.2916 15.8669 7.21385 15.7162C7.13886 15.5689 7.21111 15.455 7.30166 15.3393C7.81475 14.688 8.32327 14.0333 8.83727 13.3821C8.90587 13.2953 8.91501 13.2611 8.80069 13.2033C8.38089 12.9947 7.93913 12.8807 7.42147 12.8851ZM14.8489 15.5391C14.8563 15.8126 14.8755 16.0387 14.9349 16.2614C15.2971 17.6208 16.7202 18.5104 18.1433 18.2694C19.7293 18.0012 20.7399 16.562 20.4591 15.0921C20.1783 13.617 18.6272 12.6151 17.1007 12.9403C16.9562 12.971 16.9534 13.0061 16.9992 13.12C17.3056 13.8773 17.6065 14.6363 17.9083 15.3945C17.9339 15.4585 17.9604 15.5216 17.9595 15.5935C17.9568 15.7276 17.8845 15.817 17.7565 15.8512C17.6293 15.8845 17.5132 15.852 17.4391 15.739C17.4071 15.6899 17.3851 15.6347 17.3632 15.5803C17.0623 14.8222 16.7586 14.064 16.4641 13.3032C16.4184 13.1831 16.3827 13.1787 16.2757 13.241C15.3566 13.7765 14.8892 14.5601 14.8489 15.5391ZM21.082 16.0133C20.9896 16.2368 20.9749 16.4805 20.8807 16.704C20.3786 17.8951 19.4805 18.6261 18.1479 18.8163C16.3763 19.0679 14.7108 17.9398 14.3395 16.264C14.0377 14.8984 14.7026 13.4767 15.9703 12.779C16.1989 12.6528 16.1989 12.6528 16.102 12.4083C16.0306 12.2251 15.9593 12.0428 15.887 11.8587C15.8321 11.857 15.8184 11.9034 15.7946 11.9341C15.041 12.8974 14.2883 13.8598 13.5328 14.8213C13.4789 14.8896 13.4716 14.9317 13.5246 15.0124C13.7734 15.391 13.7789 15.7854 13.5328 16.1641C13.2905 16.5383 12.9201 16.7145 12.4646 16.6742C12.0155 16.6339 11.6963 16.3928 11.5244 15.994C11.4786 15.8897 11.4283 15.8582 11.3195 15.8652C11.1768 15.8757 11.0076 15.8284 10.896 15.8871C10.7808 15.9467 10.8366 16.1273 10.8037 16.2526C10.4378 17.6287 9.547 18.5131 8.09187 18.7944C6.48034 19.1056 4.90266 18.2589 4.30542 16.7986C3.67069 15.2446 4.26975 13.6038 5.77793 12.7659C6.82515 12.1839 7.91627 12.169 9.00556 12.6905C9.27171 12.8185 9.26896 12.8255 9.45188 12.5915C9.78389 12.1673 10.1131 11.7404 10.4497 11.3206C10.5082 11.2469 10.5174 11.19 10.4817 11.1067C10.3647 10.8271 10.2549 10.5457 10.147 10.2635C10.1177 10.1855 10.0839 10.1461 9.98418 10.1513C9.79669 10.1618 9.60828 10.1566 9.41987 10.1522C8.96532 10.1399 8.59307 9.7718 8.59307 9.33794C8.59399 8.91021 8.95891 8.53594 9.40433 8.52893C10.0025 8.52017 10.6006 8.52017 11.1988 8.52893C11.6561 8.53594 12.0119 8.90845 12.0064 9.35283C12.0018 9.78319 11.6378 10.139 11.1878 10.1522C11.0662 10.1557 10.9436 10.1575 10.822 10.1531C10.7406 10.1504 10.7268 10.1785 10.7543 10.2451C10.8403 10.4572 10.9281 10.6676 11.0067 10.8814C11.0369 10.963 11.0918 10.9638 11.1613 10.9638C12.5688 10.963 13.9755 10.9621 15.3831 10.9656C15.523 10.9665 15.5175 10.9261 15.4745 10.8218C15.3309 10.4739 15.1956 10.1224 15.0593 9.77092C14.9541 9.50272 15.0739 9.33443 15.3721 9.33005C15.5715 9.32742 15.7709 9.33443 15.9703 9.32654C16.3516 9.31076 16.6242 9.12933 16.7943 8.80327C16.8291 8.73754 16.8538 8.66216 16.905 8.60957C17.0019 8.51228 17.1263 8.49212 17.2525 8.56136C17.3879 8.63586 17.4199 8.75682 17.3797 8.89092C17.2324 9.38264 16.6782 9.8165 16.1413 9.86821C16.0096 9.87961 15.8788 9.90064 15.7206 9.88837C15.8559 10.2285 15.9831 10.5501 16.1111 10.8727C16.305 11.3609 16.5007 11.8473 16.6919 12.3373C16.7257 12.4258 16.7559 12.4556 16.8666 12.4284C18.5019 12.0261 20.1427 12.8702 20.7865 14.2586C20.9192 14.5452 21.0134 14.8423 21.049 15.1543C21.0518 15.1789 21.0454 15.2078 21.082 15.2175V16.0133Z" fill="#003064"/>
                                                    <path d="M0.737986 22.2919H23.2602V0.70722H0.737986V22.2919ZM24 23H0V0H24V23Z" fill="#003064"/>
                                                </svg>
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14.4125 9.14301C14.4125 8.38928 14.4116 7.64257 14.4142 6.89672C14.4142 6.81083 14.3949 6.78191 14.3047 6.78366C13.9795 6.78805 13.6535 6.78805 13.3274 6.78366C13.2433 6.78279 13.2275 6.81171 13.2284 6.88796C13.2319 7.22714 13.2319 7.56807 13.2275 7.90812C13.2275 7.97736 13.2547 8.02118 13.2994 8.06588C13.6412 8.40506 13.9813 8.74687 14.3231 9.08692C14.345 9.10796 14.3599 9.14301 14.4125 9.14301ZM8.27833 16.3552C8.27833 17.0449 8.28096 17.7347 8.2757 18.4244C8.27482 18.5331 8.30024 18.5646 8.41418 18.5646C9.302 18.5594 10.1907 18.5594 11.0785 18.5638C11.1872 18.5646 11.2188 18.541 11.2188 18.427C11.2144 17.0519 11.2144 15.6768 11.2179 14.3026C11.2179 14.1956 11.1968 14.1615 11.082 14.1623C10.1933 14.1667 9.3055 14.1658 8.4168 14.1623C8.30988 14.1623 8.27395 14.1834 8.2757 14.299C8.28096 14.9844 8.27833 15.6689 8.27833 16.3552ZM16.3345 11.8889C16.302 11.8547 16.274 11.8231 16.2451 11.7933C14.1092 9.65835 11.9742 7.52337 9.841 5.38576C9.76037 5.30425 9.72006 5.31301 9.64556 5.38839C9.01628 6.02292 8.3835 6.65308 7.75247 7.28498C6.25202 8.78543 4.75245 10.2868 3.2485 11.7837C3.1556 11.8748 3.16524 11.9195 3.25288 11.9993C3.39661 12.1281 3.53509 12.2657 3.66393 12.4103C3.74719 12.5032 3.79276 12.5059 3.88566 12.4121C5.74983 10.5418 7.61925 8.675 9.48693 6.80733C9.67886 6.61539 9.80945 6.61539 10.0031 6.80908C10.7613 7.56719 11.5194 8.3253 12.2784 9.08517C13.3949 10.2 14.5115 11.3157 15.6254 12.434C15.6903 12.4989 15.7254 12.5164 15.7955 12.4384C15.9453 12.2736 16.1075 12.122 16.2644 11.9651C16.2871 11.9415 16.3082 11.9169 16.3345 11.8889ZM14.4125 15.2859C14.4125 14.2368 14.4116 13.1877 14.4133 12.1387C14.4133 12.0484 14.3941 11.98 14.3266 11.9134C12.8279 10.4182 11.3309 8.92128 9.83662 7.42083C9.75862 7.34283 9.72268 7.35159 9.64994 7.42433C8.15826 8.9204 6.66482 10.4138 5.16876 11.9055C5.10741 11.9686 5.08199 12.0282 5.08199 12.1159C5.08462 14.2193 5.08462 16.3218 5.08111 18.4253C5.08111 18.5331 5.10565 18.5655 5.21784 18.5646C6.0075 18.5585 6.79629 18.5585 7.58595 18.5646C7.70076 18.5655 7.73319 18.5427 7.73319 18.4209C7.72793 16.9467 7.72968 15.4726 7.72968 13.9976C7.72968 13.6855 7.80769 13.6084 8.12583 13.6084H11.3581C11.6859 13.6084 11.763 13.6829 11.763 14.0046C11.763 15.4743 11.7639 16.9441 11.7604 18.4139C11.7595 18.5252 11.777 18.5655 11.9015 18.5646C12.6955 18.5576 13.4896 18.5594 14.2836 18.5638C14.3897 18.5646 14.4151 18.5401 14.4151 18.4332C14.4107 17.3841 14.4125 16.335 14.4125 15.2859ZM4.74982 19.1036C4.57454 19.0432 4.52546 18.9695 4.52546 18.7627C4.52546 16.7539 4.52546 14.746 4.52546 12.7373V12.5471C4.34403 12.7302 4.18803 12.8889 4.0294 13.0475C3.84447 13.2316 3.7095 13.2316 3.5237 13.0458C3.21607 12.739 2.90844 12.4323 2.60169 12.1229C2.43167 11.9528 2.43254 11.8205 2.60257 11.6496C4.90144 9.34985 7.20032 7.05097 9.50007 4.7521C9.67536 4.57769 9.81559 4.57944 9.99613 4.75999C10.8445 5.60925 11.6938 6.45763 12.5422 7.30602C12.5772 7.34195 12.6096 7.37964 12.6692 7.44449C12.6692 7.13073 12.6684 6.85904 12.6692 6.58734C12.6701 6.32617 12.7692 6.22538 13.0268 6.22538C13.5614 6.22538 14.0952 6.2245 14.6289 6.22538C14.8629 6.22625 14.962 6.32704 14.9629 6.5628C14.9629 7.57333 14.9637 8.58473 14.9611 9.59613C14.9602 9.68728 14.9839 9.75126 15.0505 9.81786C15.6412 10.4024 16.2284 10.9914 16.8156 11.5804C16.8805 11.6452 16.9418 11.7153 17.0049 11.7828V11.9809C16.9909 11.9993 16.9795 12.0186 16.9637 12.0352C16.6009 12.3998 16.2407 12.7671 15.8726 13.1264C15.7788 13.2175 15.6289 13.2026 15.5247 13.1097C15.4396 13.0344 15.3625 12.9511 15.2819 12.8713C15.182 12.7714 15.0821 12.6715 14.9629 12.5532V12.7443V18.7802C14.9629 18.9748 14.9111 19.0475 14.7376 19.1036C14.7052 19.1028 14.6719 19.1001 14.6395 19.1001H4.96192C4.89093 19.1001 4.81994 19.1019 4.74982 19.1036Z" fill="#003064"/>
                                                    <path d="M14.7378 19.1041C14.7343 19.1129 14.7343 19.1208 14.7352 19.1287H4.75175C4.75438 19.1208 4.75351 19.112 4.75 19.1041C4.82011 19.1024 4.8911 19.1006 4.9621 19.1006H14.6388C14.6721 19.1006 14.7045 19.1032 14.7378 19.1041Z" fill="#003064"/>
                                                    <path d="M19.8498 17.143C19.6386 17.377 19.3573 17.5295 19.0505 17.5804L19.0129 16.9055L19.5291 16.3902C19.5676 16.3516 19.5676 16.2876 19.5291 16.2491C19.4896 16.2105 19.4265 16.2105 19.388 16.2491L18.998 16.6391L18.9734 16.2017L19.21 15.9651C19.2495 15.9265 19.2495 15.8634 19.21 15.8249C19.1715 15.7854 19.1084 15.7854 19.0698 15.8249L18.9585 15.9353L18.9208 15.2526C18.9182 15.2 18.8744 15.1588 18.8218 15.1588C18.7692 15.1588 18.7254 15.2 18.7228 15.2526L18.6623 16.3376L18.2556 15.9309C18.2162 15.8915 18.1531 15.8915 18.1145 15.9309C18.0759 15.9695 18.0759 16.0326 18.1145 16.0712L18.6474 16.604L18.593 17.5804C17.9278 17.4708 17.4353 16.8959 17.4353 16.2131C17.4353 15.6286 17.6027 14.8643 17.8621 14.2666C18.1461 13.611 18.4958 13.235 18.8218 13.235C19.1469 13.235 19.4966 13.611 19.7806 14.2666C20.04 14.8643 20.2074 15.6286 20.2074 16.2131C20.2074 16.5576 20.0803 16.888 19.8498 17.143ZM18.7149 18.9792L18.8218 17.0554L18.9287 18.9792H18.7149ZM19.9638 14.1877C19.6413 13.4445 19.2363 13.0361 18.8218 13.0361C18.4072 13.0361 18.0015 13.4445 17.6798 14.1877C17.4064 14.8187 17.2363 15.5944 17.2363 16.2131C17.2363 17.001 17.8104 17.6636 18.5816 17.7811L18.515 18.9792H18.3976C18.3424 18.9792 18.2977 19.0238 18.2977 19.0791C18.2977 19.1334 18.3424 19.1781 18.3976 19.1781H19.246C19.3012 19.1781 19.3459 19.1334 19.3459 19.0791C19.3459 19.0238 19.3012 18.9792 19.246 18.9792H19.1285L19.0611 17.7811C19.4213 17.7259 19.7508 17.5488 19.9971 17.2762C20.2618 16.9844 20.4064 16.6075 20.4064 16.2131C20.4064 15.5944 20.2372 14.8187 19.9638 14.1877Z" fill="#003064"/>
                                                    <path d="M18.8213 13.3251C18.5374 13.3251 18.2096 13.6906 17.944 14.3024C17.689 14.8896 17.5251 15.6407 17.5251 16.2139C17.5251 16.8168 17.9335 17.3313 18.5093 17.4724L18.5549 16.6389L18.051 16.135C17.9765 16.0614 17.9765 15.9413 18.051 15.8677C18.1246 15.7932 18.2447 15.7932 18.3183 15.8677L18.5838 16.1323L18.632 15.248C18.6382 15.1481 18.7206 15.0692 18.8213 15.0692C18.9221 15.0692 19.0045 15.1481 19.0107 15.248L19.0369 15.7362C19.1097 15.6889 19.2096 15.6976 19.2736 15.7616C19.3086 15.7967 19.3288 15.8449 19.3288 15.8957C19.3288 15.9457 19.3086 15.993 19.2736 16.0289L19.065 16.2366L19.0764 16.4347L19.3244 16.1867C19.3954 16.1148 19.5199 16.1148 19.5917 16.1867C19.6277 16.2217 19.6469 16.2691 19.6469 16.3199C19.6469 16.3707 19.6277 16.4172 19.5917 16.4531L19.1044 16.9413L19.1334 17.4715C19.3823 17.4102 19.6101 17.2743 19.7828 17.0833C19.9993 16.8449 20.1176 16.5355 20.1176 16.2139C20.1176 15.6407 19.9528 14.8896 19.6978 14.3024C19.4331 13.6906 19.1053 13.3251 18.8213 13.3251ZM18.6767 17.6863L18.5777 17.6696C17.8634 17.5513 17.3454 16.9387 17.3454 16.2139C17.3454 15.617 17.5163 14.8396 17.7801 14.2305C18.0825 13.5311 18.4532 13.1455 18.8213 13.1455C19.1894 13.1455 19.5593 13.5311 19.8625 14.2305C20.1263 14.8396 20.2972 15.617 20.2972 16.2139C20.2972 16.5802 20.1623 16.9316 19.916 17.2033C19.6934 17.4496 19.391 17.6153 19.065 17.6687L18.966 17.6863L18.9204 16.8712L19.4646 16.3269L18.9195 16.8449L18.8818 16.1674L19.1465 15.9018L19.022 15.9991L18.8801 16.1411L18.831 15.2577L18.7398 16.5434L18.1912 15.9947L18.739 16.5688L18.6767 17.6863ZM18.8091 18.8905H18.8336L18.8213 18.6679L18.8091 18.8905ZM19.0229 19.0693H18.6189L18.7319 17.0508H18.9107L19.0229 19.0693ZM18.3971 19.0693L19.2455 19.0885L19.1281 19.0693H19.0431L18.9677 17.7047L19.0475 17.6915C19.3866 17.6407 19.6995 17.4715 19.93 17.2165C20.1798 16.9413 20.3174 16.5855 20.3174 16.2139C20.3174 15.6065 20.15 14.8431 19.8809 14.2226C19.5742 13.5162 19.1982 13.1262 18.8213 13.1262C18.4445 13.1262 18.0685 13.5162 17.7617 14.2226C17.4927 14.8431 17.3262 15.6065 17.3262 16.2139C17.3262 16.9588 17.8599 17.5802 18.5952 17.6915L18.6759 17.7038L18.5996 19.0693H18.3971ZM19.2455 19.2673H18.3971C18.292 19.2673 18.2078 19.1832 18.2078 19.0789C18.2078 18.9746 18.292 18.8905 18.3971 18.8905H18.4296L18.4874 17.8554C17.7056 17.6985 17.1465 17.021 17.1465 16.2139C17.1465 15.5828 17.3191 14.7932 17.597 14.1516C17.9344 13.3751 18.3691 12.9465 18.8213 12.9465C19.2736 12.9465 19.7083 13.3751 20.0457 14.1516C20.3235 14.7932 20.4962 15.5828 20.4962 16.2139C20.4962 16.6302 20.3428 17.0289 20.0632 17.3366C19.824 17.6021 19.5032 17.7844 19.1553 17.8554L19.2122 18.8905H19.2455C19.3498 18.8905 19.4348 18.9746 19.4348 19.0789C19.4348 19.1832 19.3498 19.2673 19.2455 19.2673Z" fill="#003064"/>
                                                    <path d="M0.708115 22.2919H22.2919V0.70722H0.708115V22.2919ZM23 23H0V0H23V23Z" fill="#003064"/>
                                                </svg>
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.67276 16.1942C5.04651 15.9273 5.25482 15.6139 5.20056 15.1701C5.13754 14.6537 4.83031 14.2642 4.5152 13.88C4.46969 13.8248 4.44693 13.8808 4.42417 13.908C4.17034 14.2064 3.94451 14.5224 3.81585 14.8988C3.65567 15.3697 3.78171 15.9001 4.28588 16.1645C4.28588 16.0165 4.28675 15.8712 4.28588 15.7268C4.28588 15.5999 4.35065 15.5211 4.47144 15.5176C4.60273 15.5132 4.67276 15.5929 4.67276 15.7268C4.67276 15.8721 4.67276 16.0165 4.67276 16.1942ZM11.4519 18.7711H18.8787C19.0302 18.7702 19.2376 18.8201 19.3164 18.7431C19.3934 18.6669 19.3295 18.4569 19.347 18.3072C19.3663 18.1417 19.3155 18.0971 19.1431 18.0971C14.1215 18.1032 9.09998 18.1015 4.07843 18.1032C3.92176 18.1032 3.70818 18.0358 3.62416 18.1312C3.54538 18.2214 3.60928 18.4306 3.59265 18.5855C3.57689 18.7369 3.62416 18.7755 3.77908 18.7755C6.33668 18.7693 8.89429 18.7711 11.4519 18.7711ZM7.68201 12.1495C7.68201 13.9421 7.68376 15.7356 7.67938 17.5273C7.67851 17.6603 7.70564 17.7032 7.84744 17.7015C8.63608 17.6936 9.42384 17.6927 10.2125 17.7023C10.3744 17.7041 10.4234 17.6735 10.4225 17.4984C10.4164 14.7946 10.4182 12.0917 10.4182 9.38708C10.4182 9.15163 10.4689 9.10086 10.7035 9.10086C11.2007 9.09998 11.697 9.09561 12.1933 9.10261C12.3167 9.10524 12.3473 9.07022 12.3464 8.94943C12.3403 8.22557 12.3377 7.50083 12.3482 6.77696C12.3508 6.61153 12.2948 6.5914 12.1512 6.5914C10.721 6.59665 9.28992 6.59665 7.85969 6.5914C7.71527 6.59052 7.67763 6.62291 7.67851 6.76996C7.68376 8.56343 7.68201 10.356 7.68201 12.1495ZM15.8248 13.6025C15.8248 12.2887 15.8231 10.974 15.8283 9.66017C15.8283 9.52363 15.7951 9.48686 15.6559 9.48686C14.0909 9.49299 12.5259 9.49299 10.9609 9.48686C10.8226 9.48599 10.8015 9.52888 10.8015 9.65317C10.805 12.2817 10.8059 14.9102 10.8015 17.5378C10.8007 17.6682 10.8296 17.7015 10.9626 17.7015C12.5276 17.6962 14.0926 17.6953 15.6577 17.7015C15.7977 17.7015 15.8283 17.663 15.8283 17.5273C15.8231 16.2196 15.8248 14.911 15.8248 13.6025ZM18.5479 11.0029C18.5479 8.82514 18.547 6.64742 18.5496 4.46882C18.5496 4.36203 18.547 4.30339 18.407 4.30426C16.9478 4.30951 15.4879 4.30864 14.0279 4.30514C13.9132 4.30426 13.8817 4.33402 13.8826 4.45044C13.8931 5.95069 13.8283 7.45181 13.8808 8.95206C13.8852 9.07548 13.915 9.10436 14.0357 9.10261C14.6493 9.09648 15.262 9.10086 15.8756 9.10086C16.1881 9.10086 16.2301 9.14112 16.2301 9.44923C16.2301 12.1346 16.231 14.8218 16.2266 17.5072C16.2266 17.6516 16.2459 17.7059 16.4104 17.7023C17.0643 17.691 17.719 17.6927 18.3728 17.7015C18.5199 17.7041 18.5514 17.6647 18.5514 17.5212C18.5461 15.3478 18.5479 13.1762 18.5479 11.0029ZM11.4711 19.158H3.51737C3.24516 19.158 3.20139 19.1151 3.20139 18.8472C3.20139 18.5558 3.20052 18.2634 3.20227 17.9711C3.20314 17.7671 3.26791 17.7015 3.47448 17.6988C3.69593 17.6953 3.91825 17.6927 4.1397 17.6997C4.24474 17.7032 4.29463 17.6875 4.28938 17.5614C4.27887 17.2997 4.28238 17.0363 4.2885 16.7737C4.29025 16.6861 4.26137 16.6397 4.18609 16.5969C3.97602 16.4796 3.78609 16.336 3.62766 16.1522C3.29242 15.7627 3.2294 15.3207 3.3887 14.8445C3.57689 14.2843 3.94276 13.8388 4.33752 13.4143C4.4163 13.3311 4.52221 13.3119 4.60186 13.3968C5.04651 13.8703 5.46052 14.3666 5.61632 15.0205C5.73536 15.522 5.57343 15.9395 5.20581 16.2844C5.11215 16.371 5.01412 16.4656 4.90121 16.5163C4.70602 16.6047 4.65 16.736 4.66925 16.9408C4.68764 17.1369 4.68151 17.3374 4.67101 17.5352C4.664 17.6612 4.69201 17.7032 4.82943 17.7015C5.59444 17.6936 6.35944 17.6936 7.12445 17.7015C7.26274 17.7032 7.28375 17.656 7.28288 17.5325C7.2785 16.399 7.28025 15.2664 7.28025 14.1338C7.28025 11.6112 7.28025 9.08861 7.28025 6.56602C7.28025 6.22815 7.31439 6.19402 7.64962 6.19402H12.4147C12.679 6.19402 12.7307 6.24566 12.7307 6.50912C12.7307 7.31964 12.7342 8.13191 12.7272 8.94418C12.7263 9.0781 12.7622 9.11311 12.8909 9.10261C13.0423 9.09123 13.1946 9.09298 13.346 9.10174C13.4607 9.10961 13.4913 9.0746 13.4896 8.95906C13.4616 7.42905 13.4799 5.89904 13.4878 4.36903C13.4904 3.91476 13.4904 3.91476 13.9438 3.91476H18.6214C18.8927 3.91476 18.9339 3.9559 18.9339 4.22724C18.9339 8.65446 18.9348 13.0799 18.9295 17.5072C18.9295 17.6717 18.9768 17.7155 19.1308 17.7006C19.2516 17.6875 19.3759 17.6936 19.4976 17.6988C19.67 17.7067 19.7365 17.7776 19.7374 17.9518C19.74 18.2669 19.7391 18.5829 19.7374 18.898C19.7365 19.0949 19.6744 19.1571 19.4766 19.158H15.9378H11.4711Z" fill="#003064"/>
                                                    <path d="M15.4037 5.7584C15.4037 5.44942 15.4037 5.45643 15.0947 5.44855C14.9669 5.44417 14.9144 5.47218 14.9328 5.60785C14.945 5.69976 14.9442 5.79516 14.9328 5.88707C14.9153 6.02624 14.9652 6.063 15.1026 6.05863C15.4037 6.04812 15.4037 6.056 15.4037 5.7584ZM15.809 5.75753C15.809 5.9037 15.8116 6.04987 15.809 6.19517C15.8055 6.37898 15.7372 6.45513 15.5586 6.45864C15.296 6.46389 15.0334 6.46214 14.7709 6.45951C14.6159 6.45776 14.5337 6.37461 14.5328 6.21881C14.531 5.90458 14.5302 5.58947 14.5337 5.27437C14.5354 5.12732 14.6177 5.04767 14.7647 5.04767C15.0326 5.04504 15.3013 5.04241 15.5691 5.04767C15.7372 5.05029 15.8055 5.12819 15.809 5.30238C15.8116 5.45468 15.809 5.6061 15.809 5.75753Z" fill="#003064"/>
                                                    <path d="M17.1005 5.75322C17.1005 6.0552 17.1005 6.04732 17.3928 6.0587C17.5355 6.06395 17.5924 6.02981 17.5714 5.88102C17.5591 5.78998 17.5591 5.69545 17.5714 5.60355C17.5907 5.46613 17.532 5.44337 17.4086 5.446C17.1005 5.45387 17.1005 5.44775 17.1005 5.75322ZM17.9723 5.74184C17.9723 5.89414 17.9758 6.04557 17.9714 6.197C17.967 6.38606 17.9031 6.45521 17.7202 6.45871C17.4637 6.46396 17.2073 6.46396 16.9508 6.45871C16.7696 6.45608 16.6979 6.38081 16.6961 6.2005C16.6926 5.9029 16.6926 5.6053 16.6952 5.3077C16.697 5.12389 16.7679 5.04949 16.9473 5.04686C17.2038 5.04249 17.4611 5.04249 17.7176 5.04686C17.9005 5.04949 17.9662 5.11776 17.9714 5.3042C17.9758 5.45037 17.9723 5.59655 17.9723 5.74184Z" fill="#003064"/>
                                                    <path d="M17.0995 7.66265C17.0995 7.98388 17.0995 7.97688 17.4094 7.98563C17.5354 7.98913 17.5888 7.96112 17.5704 7.82633C17.5582 7.7353 17.5573 7.63989 17.5704 7.54886C17.5923 7.39656 17.5293 7.36767 17.3901 7.37293C17.0995 7.3843 17.0995 7.37643 17.0995 7.66265ZM17.9713 7.69328C17.9713 7.84558 17.973 7.99701 17.9704 8.14843C17.9678 8.30424 17.8838 8.38476 17.7297 8.38651C17.4671 8.38914 17.2054 8.39089 16.9428 8.38564C16.7713 8.38214 16.6969 8.30686 16.6951 8.13793C16.6916 7.8342 16.6951 7.53135 16.6934 7.22763C16.6925 7.0622 16.7748 6.97467 16.9358 6.97117C17.2045 6.96679 17.4724 6.96679 17.7411 6.97204C17.8855 6.97467 17.9669 7.05694 17.9704 7.20399C17.973 7.3668 17.9704 7.53048 17.9713 7.69328Z" fill="#003064"/>
                                                    <path d="M17.5687 9.61493C17.5687 9.57467 17.5625 9.53266 17.5695 9.49327C17.6002 9.32959 17.5258 9.2937 17.3735 9.3007C17.1004 9.31208 17.0995 9.3042 17.0995 9.57204C17.0995 9.63069 17.1074 9.69021 17.0986 9.74623C17.075 9.88977 17.1406 9.91603 17.2702 9.91253C17.5687 9.90378 17.5687 9.91078 17.5687 9.61493ZM17.9713 9.60968C17.9713 9.76111 17.9722 9.91253 17.9713 10.0648C17.9713 10.2285 17.8881 10.3134 17.7245 10.3143C17.4619 10.316 17.1993 10.3178 16.9367 10.3134C16.7748 10.3117 16.6934 10.2233 16.6934 10.0587C16.6951 9.75498 16.6916 9.45213 16.6951 9.14928C16.6969 8.98035 16.773 8.90245 16.9419 8.89894C17.2045 8.89369 17.4671 8.89544 17.7297 8.89894C17.8829 8.90069 17.9687 8.98035 17.9704 9.13702C17.973 9.29458 17.9713 9.45125 17.9713 9.60968Z" fill="#003064"/>
                                                    <path d="M15.4033 7.68052C15.4033 7.628 15.3963 7.57461 15.4041 7.52384C15.4243 7.3978 15.37 7.37067 15.2518 7.37329C14.935 7.37942 14.9341 7.37329 14.9341 7.68752C14.9341 7.73391 14.9411 7.78118 14.9332 7.82669C14.9096 7.96061 14.9674 7.9895 15.0934 7.986C15.4033 7.97724 15.4033 7.98424 15.4033 7.68052ZM15.8085 7.68314C15.8085 7.82844 15.8111 7.97462 15.8085 8.11991C15.805 8.3046 15.7367 8.3825 15.5599 8.386C15.2973 8.39126 15.0348 8.3895 14.773 8.38688C14.619 8.38513 14.5332 8.3046 14.5323 8.1488C14.5306 7.83982 14.5315 7.52997 14.5315 7.22187C14.5323 7.05731 14.6137 6.97328 14.7792 6.97328C15.0418 6.97328 15.3044 6.96978 15.5669 6.97416C15.735 6.97766 15.8041 7.05381 15.8085 7.22712C15.8111 7.37942 15.8085 7.53084 15.8085 7.68314Z" fill="#003064"/>
                                                    <path d="M17.5687 11.8675C17.5687 11.5497 17.5687 11.5576 17.2579 11.548C17.1275 11.5445 17.0837 11.5777 17.0977 11.709C17.11 11.8237 17.103 11.941 17.0986 12.0565C17.096 12.1283 17.1161 12.1537 17.194 12.1589C17.5687 12.1843 17.5687 12.1878 17.5687 11.8675ZM17.9713 11.8631C17.9713 12.0145 17.9757 12.1668 17.9704 12.3182C17.9643 12.4889 17.8916 12.5598 17.7201 12.5624C17.4636 12.5659 17.2063 12.5659 16.9498 12.5624C16.7713 12.5598 16.6969 12.4854 16.6951 12.3051C16.6925 12.0014 16.6934 11.6985 16.6942 11.3948C16.6951 11.2399 16.7695 11.155 16.9288 11.1541C17.1967 11.1541 17.4654 11.155 17.7332 11.1541C17.8943 11.1532 17.9687 11.2364 17.9713 11.3904C17.973 11.548 17.9713 11.7047 17.9713 11.8631Z" fill="#003064"/>
                                                    <path d="M17.5689 16.3594C17.5689 16.278 17.568 16.1957 17.5689 16.1152C17.5707 16.0653 17.5523 16.0487 17.5006 16.0399C17.1549 15.9813 17.0989 16.0242 17.0989 16.3612C17.0989 16.4084 17.105 16.4548 17.098 16.5012C17.0796 16.6281 17.1208 16.6719 17.2573 16.6684C17.5689 16.6579 17.5689 16.6666 17.5689 16.3594ZM16.6945 16.348C16.6945 16.1914 16.691 16.0329 16.6963 15.8754C16.7015 15.7318 16.7724 15.6504 16.9265 15.6522C17.1952 15.6548 17.463 15.6566 17.7317 15.6513C17.8849 15.6487 17.9654 15.7222 17.9681 15.8675C17.9742 16.1887 17.9733 16.51 17.9689 16.8303C17.9672 16.9739 17.8937 17.0561 17.7396 17.0553C17.4709 17.0526 17.203 17.0526 16.9343 17.0553C16.7733 17.057 16.6989 16.973 16.6954 16.8198C16.6928 16.6631 16.6945 16.5056 16.6945 16.348Z" fill="#003064"/>
                                                    <path d="M17.0987 14.0967C17.0987 14.1317 17.1039 14.1676 17.0978 14.2008C17.0646 14.3794 17.1372 14.4293 17.3131 14.4179C17.5678 14.4013 17.5687 14.4135 17.5687 14.158C17.5687 14.0888 17.56 14.0179 17.5705 13.9496C17.5924 13.8131 17.5328 13.7886 17.4085 13.7921C17.0987 13.8 17.0987 13.7938 17.0987 14.0967ZM16.6943 14.1116C16.6943 13.954 16.6926 13.7965 16.6952 13.6398C16.6969 13.4857 16.7713 13.4026 16.9324 13.4043C17.2002 13.407 17.4689 13.407 17.7368 13.4043C17.8908 13.4026 17.967 13.4805 17.9687 13.6258C17.9731 13.9461 17.974 14.2674 17.9679 14.5886C17.9652 14.7339 17.8821 14.81 17.7324 14.81C17.4698 14.8092 17.2081 14.8118 16.9455 14.8083C16.7687 14.8074 16.6978 14.733 16.6952 14.5492C16.6917 14.4039 16.6943 14.2577 16.6943 14.1116Z" fill="#003064"/>
                                                    <path d="M14.4134 15.6122C14.2769 15.6288 14.0887 15.5544 14.0152 15.6446C13.946 15.7295 13.9942 15.9098 13.9898 16.0472C13.9898 16.0656 13.9924 16.0831 13.9898 16.0997C13.9723 16.2039 14.0222 16.2249 14.1167 16.2214C14.2909 16.2144 14.4659 16.21 14.6393 16.2223C14.7898 16.2328 14.8625 16.1995 14.8397 16.0306C14.8222 15.9001 14.88 15.7207 14.8126 15.6446C14.732 15.5553 14.5508 15.6288 14.4134 15.6122ZM14.4152 15.213C14.6077 15.213 14.8003 15.2139 14.9929 15.213C15.1592 15.213 15.2388 15.2979 15.2388 15.4608C15.2397 15.7645 15.2432 16.0682 15.238 16.3702C15.2353 16.554 15.1636 16.6231 14.9797 16.6249C14.6007 16.6284 14.2217 16.6258 13.8427 16.6258C13.6808 16.6258 13.5898 16.5505 13.5889 16.3842C13.5872 16.0752 13.588 15.7662 13.5889 15.4572C13.5889 15.2918 13.6729 15.2122 13.8384 15.213C14.03 15.2139 14.2226 15.213 14.4152 15.213Z" fill="#003064"/>
                                                    <path d="M14.4265 11.2442C14.2873 11.2442 14.1018 11.19 14.0212 11.2591C13.9249 11.3396 14.002 11.5313 13.9888 11.6749C13.988 11.6862 13.9906 11.6994 13.9888 11.7099C13.9722 11.8132 14.0055 11.8543 14.1193 11.8473C14.2934 11.8377 14.4703 11.8333 14.6444 11.8482C14.8081 11.8631 14.8449 11.8062 14.8405 11.6495C14.8291 11.2355 14.837 11.2355 14.4265 11.2442ZM14.4116 10.839C14.6033 10.839 14.7959 10.8407 14.9884 10.8381C15.1556 10.8355 15.2379 10.916 15.2379 11.0805C15.2388 11.3895 15.2396 11.6994 15.237 12.0075C15.2361 12.172 15.146 12.2491 14.9823 12.2491C14.6042 12.2482 14.2252 12.2508 13.8462 12.2482C13.6632 12.2473 13.5923 12.1764 13.5897 11.9926C13.5862 11.6906 13.5888 11.3869 13.5888 11.0832C13.5888 10.9195 13.6659 10.8363 13.8339 10.8381C14.0265 10.8407 14.219 10.839 14.4116 10.839Z" fill="#003064"/>
                                                    <path d="M12.2245 16.2268C12.3628 16.2198 12.544 16.2626 12.6271 16.1917C12.7164 16.1165 12.6464 15.9292 12.6551 15.7917C12.6569 15.769 12.6534 15.7445 12.656 15.7226C12.6683 15.6386 12.6341 15.6106 12.5501 15.6132C12.3584 15.6184 12.165 15.6246 11.9742 15.6114C11.8324 15.6027 11.786 15.6386 11.7895 15.7891C11.8 16.2198 11.7921 16.2198 12.2245 16.2268ZM12.2227 16.625C12.0302 16.625 11.8376 16.6285 11.6459 16.6241C11.4726 16.6198 11.4017 16.548 11.4008 16.3773C11.3973 16.0736 11.3982 15.7707 11.4 15.4679C11.4017 15.2902 11.4656 15.2175 11.638 15.2158C12.0284 15.2114 12.4197 15.2132 12.8101 15.2149C12.9632 15.2158 13.0473 15.2876 13.0455 15.4504C13.0429 15.7602 13.0464 16.0692 13.0446 16.3782C13.0429 16.5506 12.9755 16.6198 12.8004 16.6241C12.6079 16.6285 12.4153 16.625 12.2227 16.625Z" fill="#003064"/>
                                                    <path d="M14.4089 14.0316C14.5472 14.0281 14.7354 14.1016 14.8098 14.0132C14.8798 13.9292 14.8317 13.7471 14.8361 13.608C14.8361 13.5905 14.8334 13.573 14.8361 13.5555C14.8518 13.4513 14.8168 13.4093 14.7039 13.4163C14.535 13.4268 14.3643 13.4303 14.1962 13.4154C14.0326 13.4005 13.9582 13.4425 13.9862 13.6193C14.008 13.7559 13.9258 13.9406 14.0194 14.0176C14.0982 14.0841 14.275 14.0316 14.4089 14.0316ZM14.4116 14.4264C14.219 14.4264 14.0273 14.4272 13.8347 14.4264C13.6536 14.4246 13.5923 14.3695 13.5897 14.1874C13.5844 13.8846 13.5853 13.5808 13.5888 13.2789C13.5914 13.0915 13.6597 13.0277 13.8444 13.0268C14.2234 13.025 14.6015 13.025 14.9805 13.0268C15.1643 13.0277 15.2335 13.0924 15.2361 13.2789C15.2405 13.5817 15.2405 13.8854 15.2361 14.1883C15.2326 14.3677 15.1687 14.4246 14.9892 14.4264C14.7967 14.4272 14.6041 14.4264 14.4116 14.4264Z" fill="#003064"/>
                                                    <path d="M12.2547 11.2358C12.1251 11.2358 12.0192 11.2393 11.9151 11.2341C11.8302 11.2297 11.7847 11.2472 11.7917 11.347C11.7995 11.4739 11.7987 11.6034 11.7917 11.7312C11.7855 11.8301 11.8293 11.8485 11.9151 11.8468C12.1015 11.8415 12.2888 11.8363 12.4753 11.8485C12.6179 11.8581 12.6748 11.8196 12.6582 11.67C12.6424 11.5325 12.6985 11.3513 12.6293 11.2656C12.5549 11.1737 12.3676 11.2498 12.2547 11.2358ZM12.2214 10.8393C12.414 10.8393 12.6066 10.8358 12.7991 10.8402C12.9794 10.8446 13.0433 10.9067 13.0442 11.0809C13.046 11.3907 13.0425 11.6988 13.046 12.0087C13.0468 12.1697 12.9637 12.2468 12.8114 12.2476C12.4201 12.2511 12.0297 12.252 11.6385 12.2476C11.4704 12.2459 11.4013 12.168 11.4004 11.9947C11.3978 11.6918 11.3978 11.3881 11.4004 11.0844C11.4013 10.9111 11.4678 10.8446 11.6437 10.8402C11.8363 10.8358 12.0289 10.8393 12.2214 10.8393Z" fill="#003064"/>
                                                    <path d="M12.1997 14.0401C12.3231 14.0401 12.4229 14.034 12.5209 14.0419C12.6216 14.0506 12.6706 14.027 12.6592 13.9123C12.6478 13.7907 12.6513 13.6673 12.6583 13.5465C12.6644 13.4493 12.6356 13.4125 12.5332 13.4169C12.3537 13.4239 12.1725 13.4292 11.9931 13.416C11.839 13.4038 11.769 13.4449 11.7927 13.6121C11.8119 13.7487 11.7358 13.9281 11.8224 14.0139C11.9082 14.0988 12.0868 14.0296 12.1997 14.0401ZM12.2347 14.4261C12.036 14.4261 11.8382 14.4226 11.6395 14.427C11.4784 14.4305 11.4014 14.3552 11.4014 14.1986C11.4005 13.8826 11.3997 13.5683 11.4014 13.2532C11.4023 13.1044 11.4793 13.0265 11.629 13.0265C12.0255 13.0257 12.422 13.0257 12.8185 13.0265C12.9664 13.0265 13.0461 13.0992 13.0461 13.2506C13.0461 13.5718 13.0469 13.8922 13.0461 14.2126C13.0452 14.3544 12.9734 14.427 12.829 14.4261C12.6312 14.4253 12.4325 14.4261 12.2347 14.4261Z" fill="#003064"/>
                                                    <path d="M9.12196 16.5121C9.12196 16.4596 9.11495 16.4071 9.12283 16.3554C9.14296 16.2303 9.08957 16.2031 8.97053 16.2058C8.64142 16.2119 8.64142 16.2066 8.64142 16.5296C8.64142 16.5646 8.6458 16.6005 8.64055 16.6338C8.61691 16.7738 8.66418 16.8246 8.81473 16.8194C9.12196 16.808 9.12196 16.8167 9.12196 16.5121ZM8.24667 16.4964C8.24667 16.3458 8.24754 16.1944 8.24667 16.0421C8.24491 15.8907 8.32632 15.811 8.46899 15.8075C8.74296 15.8014 9.0178 15.8014 9.29176 15.8075C9.43444 15.8101 9.51584 15.8845 9.51409 16.0386C9.51146 16.3537 9.51146 16.6688 9.51409 16.983C9.51584 17.1476 9.42743 17.2176 9.27688 17.2202C9.01517 17.2229 8.75258 17.2246 8.49 17.2202C8.31844 17.2159 8.25017 17.1415 8.24754 16.969C8.24491 16.8123 8.24667 16.6548 8.24667 16.4964Z" fill="#003064"/>
                                                    <path d="M9.1216 14.2848C9.1216 14.2271 9.1146 14.1676 9.12248 14.1115C9.14086 13.9881 9.09447 13.9566 8.97193 13.9592C8.64107 13.9662 8.64107 13.9592 8.64107 14.2822C8.64107 14.3172 8.64632 14.3531 8.64019 14.3864C8.60868 14.5483 8.68396 14.5781 8.83013 14.5719C9.1216 14.5606 9.1216 14.5684 9.1216 14.2848ZM9.51286 14.2586C9.51286 14.4214 9.51198 14.5851 9.51286 14.7479C9.51373 14.8923 9.43846 14.9632 9.29754 14.9641C9.01832 14.9658 8.73823 14.9676 8.45813 14.9632C8.31809 14.9614 8.24719 14.8809 8.24631 14.7409C8.24631 14.4196 8.24544 14.0993 8.24719 13.7789C8.24806 13.638 8.32947 13.5619 8.46514 13.5592C8.7391 13.5531 9.01307 13.554 9.28703 13.5584C9.43058 13.5601 9.51461 13.6328 9.51286 13.7868C9.51198 13.9435 9.51286 14.101 9.51286 14.2586Z" fill="#003064"/>
                                                    <path d="M8.64021 8.15764C8.64021 8.23291 8.64284 8.30906 8.63933 8.38521C8.63758 8.44211 8.65772 8.46311 8.71724 8.47099C9.08836 8.52351 9.12075 8.49812 9.12075 8.13663C9.12075 8.10162 9.11549 8.06573 9.12162 8.03159C9.14875 7.88192 9.08486 7.84778 8.94131 7.85216C8.64021 7.86354 8.64021 7.85566 8.64021 8.15764ZM9.512 8.16376C9.512 8.32657 9.51288 8.49025 9.51113 8.65305C9.50937 8.78785 9.44023 8.85874 9.30543 8.85874C9.02534 8.85962 8.74612 8.85962 8.46603 8.85962C8.32423 8.85962 8.24808 8.78609 8.24633 8.64605C8.2437 8.31956 8.24283 7.99308 8.2472 7.6666C8.24808 7.5353 8.32598 7.45915 8.45552 7.4574C8.74087 7.45215 9.02709 7.45127 9.31243 7.45828C9.44198 7.4609 9.51113 7.54055 9.51113 7.6736C9.51113 7.83728 9.51113 8.00008 9.512 8.16376Z" fill="#003064"/>
                                                    <path d="M9.12226 12.0109C9.12226 11.7045 9.12226 11.7142 8.82991 11.7019C8.67586 11.6958 8.61984 11.7369 8.6391 11.8927C8.65223 12.0013 8.64523 12.1133 8.63997 12.2236C8.63735 12.2971 8.66098 12.3207 8.73801 12.326C9.12226 12.354 9.12226 12.3566 9.12226 12.0109ZM9.51352 12.0083C9.51352 12.1719 9.51352 12.3347 9.51352 12.4984C9.51264 12.6411 9.44174 12.7146 9.29907 12.7146C9.01898 12.7146 8.73976 12.7155 8.45966 12.7137C8.31612 12.712 8.24784 12.6315 8.24697 12.4923C8.24609 12.1719 8.24522 11.8507 8.24784 11.5304C8.24872 11.3912 8.32662 11.3133 8.46492 11.3115C8.74501 11.308 9.02423 11.3072 9.30432 11.3115C9.43386 11.3142 9.51001 11.3842 9.51177 11.519C9.51439 11.6827 9.51352 11.8463 9.51352 12.0083Z" fill="#003064"/>
                                                    <path d="M9.12226 10.0983C9.12226 9.77877 9.12226 9.77877 8.91394 9.77877C8.64173 9.77877 8.64173 9.77877 8.64173 10.0554C8.64173 10.114 8.64873 10.1727 8.63997 10.2287C8.61897 10.3687 8.67411 10.4046 8.81066 10.4011C9.12226 10.3915 9.12226 10.3985 9.12226 10.0983ZM9.51352 10.0895C9.51352 10.2532 9.51439 10.416 9.51264 10.5797C9.51177 10.7127 9.44349 10.7854 9.30782 10.7862C9.02248 10.7871 8.73713 10.788 8.45179 10.7854C8.31612 10.7836 8.24872 10.704 8.24784 10.5753C8.24522 10.2549 8.24609 9.9337 8.24697 9.61247C8.24784 9.46454 8.32575 9.38577 8.47367 9.38489C8.74764 9.38227 9.0216 9.38139 9.29557 9.38489C9.43299 9.38577 9.51177 9.45754 9.51264 9.60021C9.51352 9.76302 9.51264 9.9267 9.51352 10.0895Z" fill="#003064"/>
                                                    <path d="M10.804 8.14334C10.804 8.20111 10.811 8.26063 10.8022 8.31752C10.7856 8.43831 10.8241 8.47595 10.951 8.47333C11.2915 8.46545 11.2915 8.47245 11.2915 8.12496C11.2915 8.10133 11.288 8.07769 11.2924 8.05406C11.3169 7.90176 11.2661 7.84312 11.099 7.85187C10.804 7.86762 10.804 7.85624 10.804 8.14334ZM10.418 8.15735V7.66718C10.418 7.53064 10.495 7.46061 10.6228 7.45799C10.9029 7.45186 11.1821 7.45361 11.4622 7.45711C11.5987 7.45886 11.6793 7.52976 11.6793 7.67418C11.6793 8.00067 11.6801 8.32715 11.6793 8.65276C11.6784 8.78668 11.6057 8.85758 11.4727 8.85845C11.1874 8.8602 10.902 8.86108 10.6167 8.85758C10.4819 8.85583 10.4171 8.78055 10.418 8.64663C10.418 8.48383 10.418 8.32015 10.418 8.15735Z" fill="#003064"/>
                                                    <path d="M0.708108 22.2919H22.2919V0.70722H0.708108V22.2919ZM23 23H0V0H23V23Z" fill="#003064"/>
                                                </svg>
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M22.1487 19.5927H21.2969V18.7408H22.1487V19.5927Z" fill="#999999"/>
                                                    <path d="M6.35088 19.5927H3.4082V18.7408H6.35088V19.5927ZM10.7653 19.5927H7.82265V18.7408H10.7653V19.5927ZM15.1789 19.5927H12.2362V18.7408H15.1789V19.5927ZM19.5934 19.5927H16.6507V18.7408H19.5934V19.5927Z" fill="#999999"/>
                                                    <path d="M1.70341 19.5927H0.851562V18.7408H1.70341V19.5927Z" fill="#999999"/>
                                                    <path d="M8.28184 6.67969L6.10742 5.78923V5.28879L8.28184 4.40272V4.91193L6.29322 5.71036V5.37819L8.28184 6.17486V6.67969Z" fill="#999999"/>
                                                    <path d="M9.81101 6.85693C9.5919 6.85693 9.38243 6.82713 9.18173 6.76578C8.98103 6.70531 8.81889 6.6203 8.69531 6.51162L8.90741 5.99365C9.04326 6.09356 9.18436 6.16806 9.33072 6.21539C9.47621 6.26184 9.63134 6.28638 9.79348 6.28638C9.97315 6.28638 10.109 6.25307 10.201 6.18559C10.2922 6.11898 10.3377 6.01994 10.3377 5.8876C10.3377 5.75964 10.2948 5.66587 10.2072 5.60364C10.1204 5.54141 9.99243 5.51074 9.82415 5.51074H9.29216V4.96122H9.75755C9.90829 4.96122 10.0266 4.92704 10.1125 4.85955C10.1975 4.79207 10.2405 4.69566 10.2405 4.57208C10.2405 4.45639 10.2001 4.36875 10.1186 4.30828C10.038 4.24693 9.92056 4.21713 9.76719 4.21713C9.46569 4.21713 9.19663 4.31529 8.96087 4.50898L8.74352 4.00416C8.87586 3.89197 9.038 3.80521 9.22818 3.74035C9.41924 3.67725 9.61732 3.6457 9.82415 3.6457C10.1607 3.6457 10.4245 3.72019 10.6147 3.86919C10.8058 4.01905 10.9004 4.22414 10.9004 4.48707C10.9004 4.66761 10.8496 4.82537 10.7479 4.95859C10.6462 5.09356 10.5051 5.1812 10.3246 5.22327V5.16455C10.5376 5.19698 10.7032 5.28374 10.8207 5.42397C10.939 5.5642 10.9977 5.74124 10.9977 5.95333C10.9977 6.23379 10.8925 6.45465 10.6813 6.61592C10.4701 6.7763 10.18 6.85693 9.81101 6.85693Z" fill="#999999"/>
                                                    <path d="M11.7472 7.45093L11.4861 7.24234C11.5921 7.1398 11.6657 7.04076 11.7078 6.94786C11.749 6.85496 11.77 6.7568 11.77 6.65338L11.9155 6.81201H11.4238V6.10386H12.146V6.5596C12.146 6.72875 12.1153 6.88212 12.0557 7.02148C11.9944 7.15995 11.8918 7.30281 11.7472 7.45093Z" fill="#999999"/>
                                                    <path d="M13.691 6.85693C13.5525 6.85693 13.4158 6.84291 13.2817 6.81486C13.1467 6.78682 13.0205 6.74738 12.9031 6.69479C12.7847 6.64396 12.6839 6.58261 12.6016 6.51162L12.8145 5.99365C12.953 6.09093 13.0932 6.16368 13.2352 6.21276C13.3763 6.26096 13.5244 6.28638 13.6778 6.28638C13.8487 6.28638 13.9828 6.24519 14.0783 6.16368C14.1748 6.08217 14.2221 5.97262 14.2221 5.83414C14.2221 5.69216 14.1765 5.57822 14.0854 5.49233C13.9942 5.40732 13.8698 5.36437 13.7138 5.36437C13.5981 5.36437 13.492 5.38541 13.3965 5.42835C13.3001 5.4713 13.2124 5.5344 13.1336 5.61679H12.6945V3.69039H14.6875V4.25218H13.3457V5.09882H13.1818C13.258 5.00416 13.3579 4.93317 13.4806 4.88321C13.6033 4.83501 13.7383 4.81047 13.8855 4.81047C14.0862 4.81047 14.2615 4.85166 14.4114 4.93492C14.5604 5.01731 14.6761 5.133 14.7585 5.28199C14.8408 5.43186 14.8829 5.60802 14.8829 5.81223C14.8829 6.0217 14.8347 6.20487 14.7392 6.36175C14.6428 6.51688 14.5052 6.63958 14.3273 6.72722C14.1476 6.81311 13.9364 6.85693 13.691 6.85693Z" fill="#999999"/>
                                                    <path d="M17.0444 6.81226V5.0892H16.3047V4.57123H18.4572V5.0892H17.7131V6.81226H17.0444Z" fill="#999999"/>
                                                    <path d="M7.91358 12.2978H15.8742L15.2782 10.5669C15.1406 10.1699 15.0889 10.1339 14.6577 10.1339H9.13445C9.07923 10.1339 9.02402 10.1383 8.9688 10.1418L8.89781 10.1471C8.72253 10.1576 8.62524 10.233 8.56214 10.4065C8.48589 10.6169 8.41315 10.8281 8.34128 11.0393L7.91358 12.2978ZM16.6279 12.8351H7.16248L7.83207 10.8666C7.90482 10.6519 7.97931 10.4372 8.05644 10.2233C8.19141 9.84998 8.47888 9.63263 8.86626 9.60984C8.99948 9.60108 9.06696 9.59669 9.13445 9.59669L14.6577 9.59582C15.3115 9.59582 15.5753 9.78162 15.7865 10.3907L16.6279 12.8351ZM7.32374 14.2584C7.12304 14.2584 6.96178 14.3838 6.89166 14.5994C6.83119 14.7869 6.85836 14.9569 6.97843 15.134C7.08623 15.2944 7.26678 15.3557 7.46222 15.2987C7.63488 15.247 7.77949 15.0174 7.77949 14.7948C7.77861 14.5354 7.61034 14.2979 7.40438 14.2654C7.37633 14.2611 7.35004 14.2584 7.32374 14.2584ZM7.32287 15.857C7.00823 15.857 6.71638 15.7063 6.53321 15.4355C6.31848 15.12 6.26765 14.7825 6.37983 14.4337C6.54109 13.9359 6.9872 13.6554 7.48764 13.7343C7.95039 13.8071 8.31499 14.2716 8.31762 14.7922C8.31849 15.2558 8.01612 15.6958 7.61384 15.815C7.51656 15.843 7.4184 15.857 7.32287 15.857ZM16.4667 14.2505C16.4123 14.2505 16.3571 14.2602 16.3054 14.2812C16.1266 14.3557 16.0083 14.552 16.0109 14.7703V14.8097C16.0241 15.0893 16.2072 15.2926 16.4281 15.3215C16.613 15.3461 16.8199 15.198 16.8979 14.9841C16.9794 14.7632 16.9312 14.5652 16.7462 14.3618C16.6805 14.2891 16.5745 14.2505 16.4667 14.2505ZM16.4702 15.8623C16.4334 15.8623 16.3957 15.8597 16.358 15.8553C15.8821 15.7922 15.5017 15.3776 15.4737 14.8886C15.4719 14.8605 15.4728 14.8307 15.4728 14.8001L15.7418 14.7738L15.4728 14.7773C15.4675 14.3347 15.7129 13.9455 16.0994 13.7843C16.4623 13.6327 16.8926 13.7229 17.1441 13.9999C17.4649 14.3531 17.5543 14.7571 17.4036 15.1682C17.2519 15.5853 16.8707 15.8623 16.4702 15.8623ZM17.301 12.4503L17.3194 12.467C17.3133 12.4617 17.3072 12.4556 17.301 12.4503ZM6.71287 12.3049H6.95652L6.99771 12.7229L6.61121 13.077C6.39561 13.2716 6.18088 13.4679 5.97141 13.6677C5.75756 13.8737 5.64801 14.134 5.64626 14.4433C5.63837 15.7352 5.64188 17.0271 5.64451 18.3172C5.64538 18.4521 5.68044 18.6327 5.98193 18.6336C6.30709 18.6362 6.63224 18.6353 6.95827 18.6344C7.22383 18.6336 7.329 18.5336 7.33163 18.2786L7.33426 18.1305C7.33864 17.9096 7.34215 17.6896 7.329 17.4697C7.32637 17.4144 7.31586 17.2444 7.43856 17.1138C7.56213 16.9824 7.75144 16.9824 7.80666 16.9832C10.542 16.9841 13.2773 16.985 16.0127 16.9815C16.1213 16.9841 16.2484 16.9938 16.3545 17.1033C16.4719 17.2243 16.4675 17.3855 16.4658 17.4381L16.4614 17.5661C16.4526 17.85 16.4439 18.1191 16.4833 18.382C16.4965 18.4714 16.5482 18.6213 16.7454 18.6265C17.1284 18.6371 17.5114 18.6379 17.8944 18.6257C18.0372 18.6213 18.1187 18.5538 18.1441 18.4188C18.159 18.3382 18.1687 18.2549 18.1687 18.1726C18.1713 16.9683 18.1713 15.765 18.1696 14.5617C18.1687 14.1454 18.0153 13.8123 17.7007 13.5406C17.5131 13.3794 17.3326 13.2111 17.152 13.0428L16.9776 12.8789C16.7778 12.7326 16.8435 12.531 16.8681 12.4714L16.9382 12.3092L17.3431 12.3101C17.5087 12.3119 17.6753 12.3127 17.8427 12.3084C17.9995 12.304 18.1415 12.2391 18.2441 12.1243C18.3431 12.0121 18.3913 11.8684 18.3773 11.7194C18.3624 11.5538 18.3221 11.5187 18.152 11.5178L17.798 11.5187C17.5762 11.5187 17.3562 11.5196 17.1362 11.517C17.0153 11.5134 16.9873 11.545 16.9426 11.637C16.8935 11.7992 16.7795 11.8544 16.6928 11.8719L16.464 11.9166L16.2186 11.2155C16.0951 10.8588 15.9715 10.5038 15.8383 10.1515C15.6376 9.6186 15.3448 9.42053 14.7602 9.41965C12.847 9.41703 10.9329 9.41703 9.01964 9.41965C8.46398 9.42053 8.14584 9.63876 7.95653 10.1497C7.87852 10.3592 7.80578 10.5695 7.73391 10.7816L7.08799 12.6169L6.87764 11.7264C6.83294 11.5406 6.77335 11.5029 6.55512 11.5134C6.3474 11.5248 6.13881 11.5222 5.93022 11.5205L5.59805 11.5187C5.50077 11.5196 5.4552 11.5546 5.4289 11.6502C5.38946 11.7913 5.42452 11.9499 5.52443 12.0831C5.6261 12.219 5.7786 12.3014 5.93285 12.304C6.11164 12.3075 6.29043 12.3066 6.46835 12.3057L6.71287 12.3049ZM6.43154 19.1743C6.27992 19.1743 6.12917 19.1734 5.97843 19.1726C5.4587 19.1682 5.10813 18.8255 5.10638 18.3189C5.10375 17.0262 5.10024 15.7335 5.10813 14.4398C5.11076 13.9867 5.28079 13.5853 5.59893 13.2795C5.75318 13.1322 5.91006 12.9876 6.06782 12.8439C6.01962 12.843 5.97229 12.843 5.92409 12.8421C5.59981 12.8369 5.29744 12.6782 5.09323 12.4057C4.89341 12.1375 4.8268 11.8097 4.91006 11.5064C5.00033 11.1804 5.25625 10.9841 5.59367 10.9806L5.93548 10.9823C6.13355 10.985 6.33075 10.9876 6.52707 10.9771C6.73829 10.9657 6.91883 11.0016 7.05819 11.0857L7.22471 10.6063C7.29833 10.3907 7.37282 10.176 7.4517 9.96216C7.71726 9.24612 8.24487 8.8824 9.01876 8.88153C10.9338 8.8789 12.847 8.8789 14.7611 8.88153C15.5727 8.88328 16.06 9.21545 16.3413 9.96129C16.4763 10.318 16.6016 10.6782 16.7261 11.0384L16.741 11.0796C16.8549 11.0113 16.9916 10.9779 17.1433 10.9788C17.3606 10.9815 17.5788 10.9815 17.7962 10.9806H18.1538C18.6025 10.9815 18.8725 11.226 18.9128 11.6703C18.9399 11.9701 18.8453 12.2575 18.6455 12.4819C18.4439 12.7089 18.1643 12.8377 17.8567 12.8456C17.8155 12.8474 17.7743 12.8474 17.7331 12.8482C17.8374 12.9447 17.9434 13.0402 18.0512 13.1331C18.486 13.5073 18.7068 13.9876 18.7077 14.5608C18.7094 15.765 18.7086 16.9683 18.7068 18.1726C18.7068 18.2874 18.6945 18.4048 18.6726 18.5187C18.6008 18.9035 18.3089 19.1498 17.9119 19.1629C17.5184 19.1761 17.124 19.1752 16.7313 19.1638C16.3256 19.1524 16.0127 18.8711 15.9513 18.4618C15.904 18.1506 15.9145 17.8439 15.9241 17.5477L15.925 17.5205C13.2239 17.5222 10.5473 17.5222 7.87064 17.5205C7.87852 17.7361 7.8759 17.9386 7.87239 18.1401L7.86976 18.2865C7.86275 18.8308 7.51393 19.1708 6.96003 19.1726C6.78386 19.1734 6.6077 19.1743 6.43154 19.1743Z" fill="#999999"/>
                                                    <path d="M0.707233 22.2918H22.291V0.707247H0.707233V22.2918ZM23 23H0V0H23V23Z" fill="#999999"/>
                                                </svg>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M24.0004 19.9822H22.2227V19.1607H24.0004V19.9822Z" fill="#999999"/>
                                                    <path d="M6.78693 19.9822H3.55469V19.1607H6.78693V19.9822ZM11.6358 19.9822H8.40352V19.1607H11.6358V19.9822ZM16.4846 19.9822H13.2523V19.1607H16.4846V19.9822ZM21.3325 19.9822H18.1002V19.1607H21.3325V19.9822Z" fill="#999999"/>
                                                    <path d="M2.66645 19.9822H0.888672V19.1607H2.66645V19.9822Z" fill="#999999"/>
                                                    <path d="M20.33 18.0546H19.4815C19.5071 17.9128 19.5208 17.7693 19.5208 17.625C19.5208 16.1859 18.254 15.0152 16.6958 15.0152H14.4453V10.3282H18.8933C18.9536 10.3282 19.0121 10.351 19.055 10.3898L19.7711 11.0524H16.4894C16.2437 11.0524 16.0437 11.2373 16.0437 11.4643V13.3702C16.0437 13.598 16.2437 13.7829 16.4894 13.7829H19.0431C19.15 13.7829 19.2541 13.8183 19.3354 13.8833L20.468 14.7789V14.9443H19.8852C19.6934 14.9443 19.5382 15.0886 19.5382 15.2659V16.1757C19.5382 16.3538 19.6934 16.4982 19.8852 16.4982H20.468V17.9271C20.468 17.998 20.4059 18.0546 20.33 18.0546ZM18.3901 18.0546C18.4312 17.9178 18.4531 17.7735 18.4531 17.625C18.4531 16.7303 17.6649 16.001 16.6958 16.001C16.2638 16.001 15.8674 16.1471 15.5614 16.3868H14.4453V15.4887H16.6958C17.9708 15.4887 19.0084 16.4475 19.0084 17.625C19.0084 17.7693 18.9929 17.9128 18.9609 18.0546H18.3901ZM16.6958 18.7754C16.0099 18.7754 15.4509 18.2597 15.4509 17.625C15.4509 16.9911 16.0099 16.4745 16.6958 16.4745C17.3826 16.4745 17.9416 16.9911 17.9416 17.625C17.9416 18.2597 17.3826 18.7754 16.6958 18.7754ZM18.1718 9.5956C18.2229 9.61586 18.2558 9.66228 18.2558 9.71377V9.85473H14.4453V8.11178L18.1718 9.5956ZM20.4013 11.6348C20.4442 11.6736 20.468 11.7268 20.468 11.7834V14.156L19.6688 13.5238C19.4943 13.3862 19.2724 13.3102 19.0431 13.3102H16.5551V11.5259H20.2825L20.4013 11.6348ZM20.468 16.0255H20.0496V15.4178H20.468V16.0255ZM7.51563 18.7754C6.8297 18.7754 6.27072 18.2597 6.27072 17.625C6.27072 16.9911 6.8297 16.4745 7.51563 16.4745C8.20247 16.4745 8.76054 16.9911 8.76054 17.625C8.76054 18.2597 8.20247 18.7754 7.51563 18.7754ZM5.02673 17.6874V16.8603H5.96657C5.83414 17.0882 5.75924 17.349 5.75924 17.625C5.75924 17.6469 5.76015 17.6672 5.76107 17.6874H5.02673ZM20.763 11.2997L19.4167 10.0548C19.2769 9.92647 19.0915 9.85473 18.8933 9.85473H18.7673V9.71377C18.7673 9.47322 18.6129 9.2563 18.3745 9.16092L14.4453 7.59607V7.04407C14.4453 6.82631 14.2544 6.64906 14.0187 6.64906H6.8096C6.66803 6.64906 6.55386 6.75541 6.55386 6.88623C6.55386 7.01706 6.66803 7.12257 6.8096 7.12257H13.9338V16.3868H8.65093C8.34404 16.1471 7.94856 16.001 7.51563 16.001C7.08361 16.001 6.68721 16.1471 6.38032 16.3868H4.01929V7.12257H5.61493C5.7565 7.12257 5.87067 7.01706 5.87067 6.88623C5.87067 6.75541 5.7565 6.64906 5.61493 6.64906H3.93435C3.6987 6.64906 3.50781 6.82631 3.50781 7.04407V16.4661C3.50781 16.6839 3.6987 16.8603 3.93435 16.8603H4.51525V17.7668C4.51525 17.9837 4.70705 18.1609 4.94178 18.1609H5.85788C6.09718 18.794 6.75023 19.2489 7.51563 19.2489C8.28194 19.2489 8.93407 18.794 9.17429 18.1609H12.741C12.8825 18.1609 12.9967 18.0546 12.9967 17.9246C12.9967 17.7929 12.8825 17.6874 12.741 17.6874H9.2711C9.27202 17.6672 9.27293 17.6469 9.27293 17.625C9.27293 17.349 9.19712 17.0882 9.06468 16.8603H15.1467C15.0143 17.0882 14.9394 17.349 14.9394 17.625C14.9394 17.6469 14.9403 17.6672 14.9412 17.6874H13.9356C13.7941 17.6874 13.679 17.7929 13.679 17.9246C13.679 18.0546 13.7941 18.1609 13.9356 18.1609H15.038C15.2783 18.794 15.9304 19.2489 16.6958 19.2489C17.3041 19.2489 17.8402 18.9619 18.1563 18.5281H20.33C20.6881 18.5281 20.9794 18.258 20.9794 17.9271V11.7834C20.9794 11.601 20.9027 11.428 20.763 11.2997Z" fill="#999999"/>
                                                    <path d="M16.6974 17.8667C16.5531 17.8667 16.4362 17.7587 16.4362 17.6253C16.4362 17.4919 16.5531 17.3839 16.6974 17.3839C16.8418 17.3839 16.9587 17.4919 16.9587 17.6253C16.9587 17.7587 16.8418 17.8667 16.6974 17.8667ZM16.6974 16.9104C16.2709 16.9104 15.9238 17.2311 15.9238 17.6253C15.9238 18.0195 16.2709 18.3402 16.6974 18.3402C17.124 18.3402 17.4711 18.0195 17.4711 17.6253C17.4711 17.2311 17.124 16.9104 16.6974 16.9104Z" fill="#999999"/>
                                                    <path d="M7.5158 17.8667C7.37149 17.8667 7.25458 17.7587 7.25458 17.6253C7.25458 17.4919 7.37149 17.3839 7.5158 17.3839C7.66011 17.3839 7.77702 17.4919 7.77702 17.6253C7.77702 17.7587 7.66011 17.8667 7.5158 17.8667ZM7.5158 16.9104C7.08926 16.9104 6.74219 17.2311 6.74219 17.6253C6.74219 18.0195 7.08926 18.3402 7.5158 18.3402C7.94234 18.3402 8.28942 18.0195 8.28942 17.6253C8.28942 17.2311 7.94234 16.9104 7.5158 16.9104Z" fill="#999999"/>
                                                    <path d="M5.61512 8.16019C5.47354 8.16019 5.35938 8.2657 5.35938 8.39652V15.1126C5.35938 15.2442 5.47354 15.3497 5.61512 15.3497C5.75669 15.3497 5.87086 15.2442 5.87086 15.1126V8.39652C5.87086 8.2657 5.75669 8.16019 5.61512 8.16019Z" fill="#999999"/>
                                                    <path d="M8.11304 15.1136V8.39676C8.11304 8.26593 7.99887 8.16043 7.8573 8.16043C7.71573 8.16043 7.60156 8.26593 7.60156 8.39676V15.1128C7.60156 15.2436 7.71573 15.3491 7.8573 15.3491C7.99887 15.3491 8.11304 15.2436 8.11304 15.1136Z" fill="#999999"/>
                                                    <path d="M10.3542 15.1136V8.39676C10.3542 8.26593 10.24 8.16043 10.0985 8.16043C9.95688 8.16043 9.8418 8.26593 9.8418 8.39676V15.1128C9.8418 15.2436 9.95688 15.3491 10.0985 15.3491C10.24 15.3491 10.3542 15.2436 10.3542 15.1136Z" fill="#999999"/>
                                                    <path d="M12.5964 15.1136V8.39676C12.5964 8.26593 12.4822 8.16043 12.3406 8.16043C12.1991 8.16043 12.084 8.26593 12.084 8.39676V15.1128C12.084 15.2436 12.1991 15.3491 12.3406 15.3491C12.4822 15.3491 12.5964 15.2436 12.5964 15.1136Z" fill="#999999"/>
                                                    <path d="M0.738902 23.2765H23.2611V1.69187H0.738902V23.2765ZM24 23.9846H0V0.984619H24V23.9846Z" fill="#999999"/>
                                                </svg>
                                                <svg width="23" height="24" viewBox="0 0 23 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16.452 18.5354H15.1005V13.2953L16.9901 13.8202L18.6667 14.2856L16.452 18.5354ZM11.0093 14.2856L12.6842 13.8202H12.686L14.5755 13.2953V18.5354H13.2215L11.0093 14.2856ZM12.8779 12.276H13.7841H15.8902H16.7982V13.2225L14.9077 12.6975C14.8621 12.6844 14.8139 12.6844 14.7675 12.6975L12.8779 13.2225V12.276ZM14.0462 10.8728H15.6273V11.7528H14.0462V10.8728ZM19.1181 13.8667L17.3223 13.368V12.0148C17.3223 11.8702 17.2048 11.7528 17.0602 11.7528H16.1522V10.6108C16.1522 10.4662 16.0348 10.3487 15.8902 10.3487H13.7841C13.6395 10.3487 13.5221 10.4662 13.5221 10.6108V11.7528H12.6158C12.4712 11.7528 12.3538 11.8702 12.3538 12.0148V13.368L10.5571 13.8667C10.4809 13.8877 10.4186 13.9429 10.3871 14.0157C10.3555 14.0876 10.3582 14.1708 10.395 14.2409L12.8297 18.9184C12.8753 19.0052 12.9647 19.0604 13.0628 19.0604H16.6106C16.7088 19.0604 16.7982 19.0052 16.8437 18.9184L19.2811 14.2409C19.3179 14.1708 19.3205 14.0876 19.289 14.0157C19.2574 13.9429 19.1952 13.8877 19.1181 13.8667Z" fill="#999999"/>
                                                    <path d="M10.4437 18.3018H4.18426V17.4227H6.49541H8.36659H10.4437V18.3018ZM6.75746 7.80304V5.93274V3.92922L8.10453 4.26577V5.93274V7.80304V16.8978H6.75746V7.80304ZM5.42792 7.54187L5.76446 6.19479H6.23335V7.54187H5.42792ZM17.7751 6.19479L18.2808 7.54187H8.62864V6.19479H17.7751ZM10.7058 16.8978H8.62864V8.06597H18.6585C18.7444 8.06597 18.825 8.02303 18.8741 7.95291C18.9232 7.88192 18.9346 7.79253 18.9048 7.71189L18.2028 5.83984C18.1642 5.73817 18.0669 5.67069 17.9574 5.67069H8.62864V4.06069C8.62864 3.94062 8.54625 3.83544 8.42969 3.80652L6.55851 3.33938C6.48138 3.31923 6.39812 3.33675 6.33414 3.38671C6.27016 3.43667 6.23335 3.51292 6.23335 3.59355V5.67069H5.55938C5.43931 5.67069 5.33414 5.7522 5.30522 5.86876L4.8372 7.73994C4.81792 7.81882 4.83545 7.90208 4.88453 7.96518C4.93449 8.02829 5.01073 8.06597 5.09137 8.06597H6.23335V16.8978H3.92221C3.7776 16.8978 3.66016 17.0152 3.66016 17.1607V18.5639C3.66016 18.7093 3.7776 18.8268 3.92221 18.8268H10.7058C10.8504 18.8268 10.9678 18.7093 10.9678 18.5639V17.1607C10.9678 17.0152 10.8504 16.8978 10.7058 16.8978Z" fill="#999999"/>
                                                    <path d="M0.708102 22.3225H22.2919V0.737886H0.708102V22.3225ZM23 23.0306H0V0.0306396H23V23.0306Z" fill="#999999"/>
                                                </svg>
                                            </div>
                                        </div>

                                    <? } ?>

                                <? } else if (str_contains($elementSectionName, 'Группа эксплуатации В')) { ?>

                                    <h4>
                                        Группа эксплуатации В
                                    </h4>
                                    <div class="icons">
                                        <svg style="width: 60%;height:auto" viewBox="0 0 193 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22.5032 20.7228H21.1465V20.0068H22.5032V20.7228Z" fill="#003064"/>
                                            <path d="M6.306 20.7228H3.33789V20.0068H6.306V20.7228ZM10.7586 20.7228H7.79049V20.0068H10.7586V20.7228ZM15.2112 20.7228H12.2431V20.0068H15.2112V20.7228ZM19.6629 20.7228H16.6948V20.0068H19.6629V20.7228Z" fill="#003064"/>
                                            <path d="M1.85475 20.7228H0.498047V20.0068H1.85475V20.7228Z" fill="#003064"/>
                                            <path d="M9.88463 10.9221C9.97741 10.9221 10.0483 10.9667 10.0772 10.9877C10.2514 11.119 10.2076 11.3238 10.1936 11.3912L10.1201 11.814C10.0527 12.2079 9.98529 12.6017 9.90301 12.9921C9.77785 13.5926 9.92927 14.0258 10.3949 14.3996C10.886 14.7926 11.3665 15.1996 11.8462 15.6066C12.0877 15.8114 12.3293 16.0163 12.5726 16.2202C12.6847 16.313 12.7608 16.4206 12.8107 16.5572L13.3989 18.1765C13.6388 18.8329 13.8777 19.4894 14.1149 20.1468C14.3976 20.0382 14.6725 19.9358 14.9491 19.8395C14.521 18.6693 14.1035 17.5165 13.686 16.3637L13.5127 15.8858C13.4996 15.8491 13.4838 15.8254 13.4558 15.8027L12.9394 15.3703C12.7013 15.1707 12.4641 14.9712 12.2225 14.7751C12.0457 14.6315 11.981 14.4495 12.0247 14.2193C12.0799 13.9287 12.1306 13.6372 12.1823 13.3466L12.5867 11.0831L13.0567 11.603C13.9486 12.5842 15.0138 13.1015 16.3093 13.1812C16.3058 12.881 16.3058 12.586 16.3101 12.2919C15.8226 12.2674 15.3587 12.144 14.9176 11.9243C14.1596 11.5453 13.6458 11.0621 13.3482 10.4494C13.2764 10.3006 13.2212 10.1439 13.1661 9.98812C13.1346 9.89447 13.1022 9.80169 13.0654 9.71066C12.893 9.28527 12.4904 8.9439 12.0168 8.82224C11.5801 8.70932 11.1521 8.79773 10.8116 9.07257C10.3547 9.44019 9.8715 9.78243 9.4041 10.1133C9.19666 10.2603 8.98921 10.4065 8.78352 10.5562C8.71087 10.6096 8.66098 10.6472 8.62684 10.6936C8.15681 11.3457 7.68415 11.9969 7.21062 12.6455C7.45395 12.8171 7.69291 12.9921 7.93011 13.1681C8.09204 12.944 8.25134 12.7252 8.40977 12.5072C8.63122 12.2026 8.85267 11.898 9.06974 11.5917C9.24917 11.3378 9.42948 11.1601 9.63868 11.0341C9.72796 10.9492 9.81286 10.9221 9.88463 10.9221ZM14.0099 20.7314C13.9635 20.7314 13.9145 20.7227 13.8637 20.6999C13.7202 20.6352 13.6685 20.4934 13.6501 20.44C13.3998 19.7459 13.1468 19.0535 12.8948 18.3603L12.3057 16.7401C12.2882 16.6946 12.2672 16.6648 12.2287 16.6316C11.9845 16.4276 11.7411 16.2219 11.4987 16.0163C11.0225 15.6119 10.5464 15.2084 10.0588 14.818C9.43298 14.3164 9.20978 13.6827 9.37784 12.8818C9.45224 12.5273 9.51439 12.1693 9.57566 11.8114C9.55377 11.8394 9.53102 11.87 9.50826 11.9015C9.28944 12.2114 9.06711 12.516 8.84391 12.8223C8.65748 13.0788 8.47104 13.3352 8.28635 13.5934C8.25572 13.6372 8.17344 13.751 8.02377 13.7746C7.87322 13.7965 7.76205 13.7134 7.72004 13.6819C7.40931 13.4499 7.09596 13.2197 6.77648 12.9974C6.73972 12.972 6.6268 12.8932 6.6023 12.7453C6.57779 12.6 6.65656 12.4923 6.6942 12.4407C7.19487 11.7553 7.69466 11.0691 8.19095 10.3803C8.2776 10.2595 8.38264 10.1824 8.46754 10.1212C8.67498 9.97062 8.88418 9.82269 9.09337 9.67477C9.55465 9.34829 10.0308 9.0113 10.4737 8.65418C10.9499 8.2708 11.5442 8.14476 12.1516 8.30144C12.7871 8.46599 13.328 8.92902 13.5626 9.50847C13.6029 9.60825 13.6388 9.70978 13.6738 9.81132C13.7219 9.94874 13.7683 10.0853 13.8313 10.2148C14.079 10.7242 14.5 11.1146 15.1583 11.4437C15.567 11.6477 15.9985 11.7536 16.4388 11.758C16.4931 11.7588 16.6375 11.7606 16.7452 11.8709C16.8169 11.9444 16.8519 12.0451 16.8493 12.1711C16.8414 12.5545 16.8423 12.9387 16.8493 13.3212C16.8502 13.3694 16.8528 13.5155 16.7425 13.6241C16.6314 13.7326 16.4878 13.7265 16.4274 13.7247C15.0655 13.6748 13.8891 13.1742 12.9245 12.2394L12.7109 13.4385C12.6593 13.7326 12.6085 14.0267 12.5525 14.3199C12.8055 14.5554 13.0444 14.7576 13.2843 14.9589L13.7981 15.3887C13.9005 15.4727 13.9722 15.5751 14.0178 15.7029L14.1911 16.1808C14.6261 17.3791 15.0593 18.5774 15.4979 19.7739C15.5189 19.8316 15.5688 19.9656 15.5022 20.103C15.4375 20.2413 15.3001 20.2877 15.2493 20.3052C14.8939 20.4251 14.5429 20.5573 14.1937 20.6921C14.1447 20.7113 14.0808 20.7314 14.0099 20.7314Z" fill="#003064"/>
                                            <path d="M7.59074 19.2423C7.82357 19.4331 8.05027 19.623 8.27347 19.8147L8.44765 19.6038C8.84766 19.1162 9.24854 18.6287 9.66781 18.1587C10.2 17.5608 10.5483 17.0173 10.7628 16.4475C10.7777 16.4089 10.7882 16.3801 10.7969 16.3599C10.7821 16.3468 10.7637 16.3328 10.7435 16.3153L9.97854 15.6746L9.62229 16.7188C9.58028 16.8431 9.52076 16.9499 9.43848 17.047L8.71987 17.9022C8.34437 18.3486 7.96887 18.7959 7.59074 19.2423ZM8.29623 20.4257C8.15706 20.4257 8.05815 20.3399 8.01964 20.3057C7.72991 20.0545 7.43582 19.8077 7.13822 19.567C7.09796 19.5346 6.99117 19.448 6.97804 19.2992C6.96404 19.1512 7.05332 19.0453 7.08745 19.0051C7.49621 18.5237 7.90235 18.0396 8.30936 17.5556L9.02797 16.7022C9.06561 16.6567 9.09274 16.6068 9.11287 16.5472L9.7247 14.7608L11.0893 15.9048C11.3729 16.1437 11.407 16.2663 11.281 16.5954C11.0271 17.2702 10.6464 17.8672 10.0687 18.5158C9.6538 18.9806 9.25817 19.4628 8.86254 19.9451L8.60608 20.2567C8.57107 20.2996 8.47829 20.4117 8.32424 20.4248C8.31461 20.4257 8.30498 20.4257 8.29623 20.4257Z" fill="#003064"/>
                                            <path d="M12.6434 7.8816C12.9226 7.89998 13.2088 7.8002 13.4355 7.60063C13.6692 7.39318 13.8145 7.09734 13.832 6.79011C13.867 6.19754 13.3751 5.62597 12.7992 5.59009C12.4631 5.56733 12.1462 5.67411 11.9064 5.88681C11.6788 6.08813 11.544 6.36997 11.5274 6.67982C11.4933 7.31266 11.9834 7.83958 12.6434 7.8816ZM12.7099 8.42078C12.6758 8.42078 12.6434 8.4199 12.6093 8.41815C11.6508 8.35776 10.9401 7.5805 10.9908 6.65006C11.0162 6.19666 11.214 5.78352 11.5501 5.48505C11.8976 5.17607 12.3563 5.0229 12.8333 5.05353C13.7034 5.10868 14.4202 5.93407 14.3686 6.82074C14.3423 7.27064 14.1314 7.70129 13.7909 8.00326C13.4837 8.2746 13.1029 8.42078 12.7099 8.42078Z" fill="#003064"/>
                                            <path d="M0.70811 23.2919H22.2919V1.70722H0.70811V23.2919ZM23 24H0V1H23V24Z" fill="#003064"/>
                                            <path d="M52.0004 20.5927H50.2227V19.7408H52.0004V20.5927Z" fill="#003064"/>
                                            <path d="M34.6255 20.5927H31.5547V19.7408H34.6255V20.5927ZM39.2312 20.5927H36.1604V19.7408H39.2312V20.5927ZM43.8378 20.5927H40.7671V19.7408H43.8378V20.5927ZM48.4436 20.5927H45.3728V19.7408H48.4436V20.5927Z" fill="#003064"/>
                                            <path d="M29.7776 20.5927H28.8887V19.7408H29.7776V20.5927Z" fill="#003064"/>
                                            <path d="M41.1368 16.5908C41.1368 16.3007 40.8816 16.0579 40.5771 16.0562C40.2625 16.0544 40.0027 16.3025 40.0073 16.5987C40.0128 16.8888 40.2743 17.1343 40.5752 17.1308C40.8798 17.1273 41.1368 16.881 41.1368 16.5908ZM38.3116 10.0697C38.0464 10.0697 37.7811 10.068 37.5159 10.0706C37.3019 10.0724 37.1793 10.1723 37.1784 10.3379C37.1775 10.5036 37.3019 10.6088 37.5141 10.6096C38.0391 10.6132 38.564 10.6123 39.089 10.6096C39.2985 10.6088 39.432 10.5001 39.4329 10.3397C39.4338 10.1802 39.2994 10.0724 39.0909 10.0706C38.8311 10.068 38.5714 10.0697 38.3116 10.0697ZM36.0434 16.3147H38.1689C38.2284 16.3147 38.2915 16.3305 38.275 16.2306C38.1753 15.6267 37.9119 15.1034 37.4583 14.6704C37.3998 14.6135 37.3705 14.6135 37.3211 14.6775C36.9031 15.2174 36.4824 15.7538 36.0434 16.3147ZM38.768 12.8307C38.436 13.2558 38.125 13.6607 37.8049 14.0595C37.7455 14.1349 37.7436 14.1726 37.8168 14.2392C38.3162 14.6985 38.639 15.2516 38.7936 15.8993C38.8274 16.0369 38.7671 16.235 38.907 16.2998C39.0323 16.3586 39.2116 16.3104 39.3661 16.3165C39.443 16.3182 39.486 16.2998 39.5107 16.2236C39.5701 16.0439 39.678 15.8879 39.8335 15.7713C39.9085 15.7161 39.8994 15.6653 39.8719 15.5978C39.7832 15.3813 39.6963 15.1648 39.6094 14.9483C39.3314 14.2488 39.0534 13.5494 38.768 12.8307ZM39.2829 12.5248C39.2829 12.5449 39.2783 12.5616 39.2829 12.5756C39.6597 13.5257 40.0375 14.4767 40.4143 15.4269C40.4381 15.4856 40.4609 15.5136 40.5442 15.5075C40.6566 15.4978 40.7783 15.5005 40.8844 15.5504C40.9841 15.5969 41.0298 15.5496 41.0838 15.4794C41.8145 14.5425 42.548 13.6073 43.2797 12.6694C43.3136 12.6273 43.3712 12.5923 43.3675 12.5248H39.2829ZM35.4215 13.8851C35.2715 13.8772 35.0776 13.893 34.8892 13.9333C33.3289 14.2655 32.4161 15.6845 32.685 17.0896C32.9594 18.5244 34.4584 19.5376 35.9455 19.2668C37.1629 19.045 38.0336 18.1957 38.2604 17.0265C38.2906 16.8704 38.2403 16.8643 38.1104 16.8643C37.269 16.8687 36.4266 16.8669 35.5852 16.8669C35.4379 16.8669 35.2916 16.8669 35.2139 16.7162C35.1389 16.5689 35.2111 16.455 35.3017 16.3393C35.8147 15.688 36.3233 15.0333 36.8373 14.3821C36.9059 14.2953 36.915 14.2611 36.8007 14.2033C36.3809 13.9947 35.9391 13.8807 35.4215 13.8851ZM42.8489 16.5391C42.8563 16.8126 42.8755 17.0387 42.9349 17.2614C43.2971 18.6208 44.7202 19.5104 46.1433 19.2694C47.7293 19.0012 48.7399 17.562 48.4591 16.0921C48.1783 14.617 46.6272 13.6151 45.1007 13.9403C44.9562 13.971 44.9534 14.0061 44.9992 14.12C45.3056 14.8773 45.6065 15.6363 45.9083 16.3945C45.9339 16.4585 45.9604 16.5216 45.9595 16.5935C45.9568 16.7276 45.8845 16.817 45.7565 16.8512C45.6293 16.8845 45.5132 16.852 45.4391 16.739C45.4071 16.6899 45.3851 16.6347 45.3632 16.5803C45.0623 15.8222 44.7586 15.064 44.4641 14.3032C44.4184 14.1831 44.3827 14.1787 44.2757 14.241C43.3566 14.7765 42.8892 15.5601 42.8489 16.5391ZM49.082 17.0133C48.9896 17.2368 48.9749 17.4805 48.8807 17.704C48.3786 18.8951 47.4805 19.6261 46.1479 19.8163C44.3763 20.0679 42.7108 18.9398 42.3395 17.264C42.0377 15.8984 42.7026 14.4767 43.9703 13.779C44.1989 13.6528 44.1989 13.6528 44.102 13.4083C44.0306 13.2251 43.9593 13.0428 43.887 12.8587C43.8321 12.857 43.8184 12.9034 43.7946 12.9341C43.041 13.8974 42.2883 14.8598 41.5328 15.8213C41.4789 15.8896 41.4716 15.9317 41.5246 16.0124C41.7734 16.391 41.7789 16.7854 41.5328 17.1641C41.2905 17.5383 40.9201 17.7145 40.4646 17.6742C40.0155 17.6339 39.6963 17.3928 39.5244 16.994C39.4786 16.8897 39.4283 16.8582 39.3195 16.8652C39.1768 16.8757 39.0076 16.8284 38.896 16.8871C38.7808 16.9467 38.8366 17.1273 38.8037 17.2526C38.4378 18.6287 37.547 19.5131 36.0919 19.7944C34.4803 20.1056 32.9027 19.2589 32.3054 17.7986C31.6707 16.2446 32.2698 14.6038 33.7779 13.7659C34.8251 13.1839 35.9163 13.169 37.0056 13.6905C37.2717 13.8185 37.269 13.8255 37.4519 13.5915C37.7839 13.1673 38.1131 12.7404 38.4497 12.3206C38.5082 12.2469 38.5174 12.19 38.4817 12.1067C38.3647 11.8271 38.2549 11.5457 38.147 11.2635C38.1177 11.1855 38.0839 11.1461 37.9842 11.1513C37.7967 11.1618 37.6083 11.1566 37.4199 11.1522C36.9653 11.1399 36.5931 10.7718 36.5931 10.3379C36.594 9.91021 36.9589 9.53594 37.4043 9.52893C38.0025 9.52017 38.6006 9.52017 39.1988 9.52893C39.6561 9.53594 40.0119 9.90845 40.0064 10.3528C40.0018 10.7832 39.6378 11.139 39.1878 11.1522C39.0662 11.1557 38.9436 11.1575 38.822 11.1531C38.7406 11.1504 38.7268 11.1785 38.7543 11.2451C38.8403 11.4572 38.9281 11.6676 39.0067 11.8814C39.0369 11.963 39.0918 11.9638 39.1613 11.9638C40.5688 11.963 41.9755 11.9621 43.3831 11.9656C43.523 11.9665 43.5175 11.9261 43.4745 11.8218C43.3309 11.4739 43.1956 11.1224 43.0593 10.7709C42.9541 10.5027 43.0739 10.3344 43.3721 10.33C43.5715 10.3274 43.7709 10.3344 43.9703 10.3265C44.3516 10.3108 44.6242 10.1293 44.7943 9.80327C44.8291 9.73754 44.8538 9.66216 44.905 9.60957C45.0019 9.51228 45.1263 9.49212 45.2525 9.56136C45.3879 9.63586 45.4199 9.75682 45.3797 9.89092C45.2324 10.3826 44.6782 10.8165 44.1413 10.8682C44.0096 10.8796 43.8788 10.9006 43.7206 10.8884C43.8559 11.2285 43.9831 11.5501 44.1111 11.8727C44.305 12.3609 44.5007 12.8473 44.6919 13.3373C44.7257 13.4258 44.7559 13.4556 44.8666 13.4284C46.5019 13.0261 48.1427 13.8702 48.7865 15.2586C48.9192 15.5452 49.0134 15.8423 49.049 16.1543C49.0518 16.1789 49.0454 16.2078 49.082 16.2175V17.0133Z" fill="#003064"/>
                                            <path d="M28.738 23.2919H51.2602V1.70722H28.738V23.2919ZM52 24H28V1H52V24Z" fill="#003064"/>
                                            <path d="M71.4125 10.143C71.4125 9.38928 71.4116 8.64257 71.4142 7.89672C71.4142 7.81083 71.3949 7.78191 71.3047 7.78366C70.9795 7.78805 70.6535 7.78805 70.3274 7.78366C70.2433 7.78279 70.2275 7.81171 70.2284 7.88796C70.2319 8.22714 70.2319 8.56807 70.2275 8.90812C70.2275 8.97736 70.2547 9.02118 70.2994 9.06588C70.6412 9.40506 70.9813 9.74687 71.3231 10.0869C71.345 10.108 71.3599 10.143 71.4125 10.143ZM65.2783 17.3552C65.2783 18.0449 65.281 18.7347 65.2757 19.4244C65.2748 19.5331 65.3002 19.5646 65.4142 19.5646C66.302 19.5594 67.1907 19.5594 68.0785 19.5638C68.1872 19.5646 68.2188 19.541 68.2188 19.427C68.2144 18.0519 68.2144 16.6768 68.2179 15.3026C68.2179 15.1956 68.1968 15.1615 68.082 15.1623C67.1933 15.1667 66.3055 15.1658 65.4168 15.1623C65.3099 15.1623 65.2739 15.1834 65.2757 15.299C65.281 15.9844 65.2783 16.6689 65.2783 17.3552ZM73.3345 12.8889C73.302 12.8547 73.274 12.8231 73.2451 12.7933C71.1092 10.6584 68.9742 8.52337 66.841 6.38576C66.7604 6.30425 66.7201 6.31301 66.6456 6.38839C66.0163 7.02292 65.3835 7.65308 64.7525 8.28498C63.252 9.78543 61.7525 11.2868 60.2485 12.7837C60.1556 12.8748 60.1652 12.9195 60.2529 12.9993C60.3966 13.1281 60.5351 13.2657 60.6639 13.4103C60.7472 13.5032 60.7928 13.5059 60.8857 13.4121C62.7498 11.5418 64.6193 9.675 66.4869 7.80733C66.6789 7.61539 66.8095 7.61539 67.0031 7.80908C67.7613 8.56719 68.5194 9.3253 69.2784 10.0852C70.3949 11.2 71.5115 12.3157 72.6254 13.434C72.6903 13.4989 72.7254 13.5164 72.7955 13.4384C72.9453 13.2736 73.1075 13.122 73.2644 12.9651C73.2871 12.9415 73.3082 12.9169 73.3345 12.8889ZM71.4125 16.2859C71.4125 15.2368 71.4116 14.1877 71.4133 13.1387C71.4133 13.0484 71.3941 12.98 71.3266 12.9134C69.8279 11.4182 68.3309 9.92128 66.8366 8.42083C66.7586 8.34283 66.7227 8.35159 66.6499 8.42433C65.1583 9.9204 63.6648 11.4138 62.1688 12.9055C62.1074 12.9686 62.082 13.0282 62.082 13.1159C62.0846 15.2193 62.0846 17.3218 62.0811 19.4253C62.0811 19.5331 62.1057 19.5655 62.2178 19.5646C63.0075 19.5585 63.7963 19.5585 64.5859 19.5646C64.7008 19.5655 64.7332 19.5427 64.7332 19.4209C64.7279 17.9467 64.7297 16.4726 64.7297 14.9976C64.7297 14.6855 64.8077 14.6084 65.1258 14.6084H68.3581C68.6859 14.6084 68.763 14.6829 68.763 15.0046C68.763 16.4743 68.7639 17.9441 68.7604 19.4139C68.7595 19.5252 68.777 19.5655 68.9015 19.5646C69.6955 19.5576 70.4896 19.5594 71.2836 19.5638C71.3897 19.5646 71.4151 19.5401 71.4151 19.4332C71.4107 18.3841 71.4125 17.335 71.4125 16.2859ZM61.7498 20.1036C61.5745 20.0432 61.5255 19.9695 61.5255 19.7627C61.5255 17.7539 61.5255 15.746 61.5255 13.7373V13.5471C61.344 13.7302 61.188 13.8889 61.0294 14.0475C60.8445 14.2316 60.7095 14.2316 60.5237 14.0458C60.2161 13.739 59.9084 13.4323 59.6017 13.1229C59.4317 12.9528 59.4325 12.8205 59.6026 12.6496C61.9014 10.3499 64.2003 8.05097 66.5001 5.7521C66.6754 5.57769 66.8156 5.57944 66.9961 5.75999C67.8445 6.60925 68.6938 7.45763 69.5422 8.30602C69.5772 8.34195 69.6096 8.37964 69.6692 8.44449C69.6692 8.13073 69.6684 7.85904 69.6692 7.58734C69.6701 7.32617 69.7692 7.22538 70.0268 7.22538C70.5614 7.22538 71.0952 7.2245 71.6289 7.22538C71.8629 7.22625 71.962 7.32704 71.9629 7.5628C71.9629 8.57333 71.9637 9.58473 71.9611 10.5961C71.9602 10.6873 71.9839 10.7513 72.0505 10.8179C72.6412 11.4024 73.2284 11.9914 73.8156 12.5804C73.8805 12.6452 73.9418 12.7153 74.0049 12.7828V12.9809C73.9909 12.9993 73.9795 13.0186 73.9637 13.0352C73.6009 13.3998 73.2407 13.7671 72.8726 14.1264C72.7788 14.2175 72.6289 14.2026 72.5247 14.1097C72.4396 14.0344 72.3625 13.9511 72.2819 13.8713C72.182 13.7714 72.0821 13.6715 71.9629 13.5532V13.7443V19.7802C71.9629 19.9748 71.9111 20.0475 71.7376 20.1036C71.7052 20.1028 71.6719 20.1001 71.6395 20.1001H61.9619C61.8909 20.1001 61.8199 20.1019 61.7498 20.1036Z" fill="#003064"/>
                                            <path d="M71.7378 20.1041C71.7343 20.1129 71.7343 20.1208 71.7352 20.1287H61.7518C61.7544 20.1208 61.7535 20.112 61.75 20.1041C61.8201 20.1024 61.8911 20.1006 61.9621 20.1006H71.6388C71.6721 20.1006 71.7045 20.1032 71.7378 20.1041Z" fill="#003064"/>
                                            <path d="M76.8498 18.143C76.6386 18.377 76.3573 18.5295 76.0505 18.5804L76.0129 17.9055L76.5291 17.3902C76.5676 17.3516 76.5676 17.2876 76.5291 17.2491C76.4896 17.2105 76.4265 17.2105 76.388 17.2491L75.998 17.6391L75.9734 17.2017L76.21 16.9651C76.2495 16.9265 76.2495 16.8634 76.21 16.8249C76.1715 16.7854 76.1084 16.7854 76.0698 16.8249L75.9585 16.9353L75.9208 16.2526C75.9182 16.2 75.8744 16.1588 75.8218 16.1588C75.7692 16.1588 75.7254 16.2 75.7228 16.2526L75.6623 17.3376L75.2556 16.9309C75.2162 16.8915 75.1531 16.8915 75.1145 16.9309C75.0759 16.9695 75.0759 17.0326 75.1145 17.0712L75.6474 17.604L75.593 18.5804C74.9278 18.4708 74.4353 17.8959 74.4353 17.2131C74.4353 16.6286 74.6027 15.8643 74.8621 15.2666C75.1461 14.611 75.4958 14.235 75.8218 14.235C76.1469 14.235 76.4966 14.611 76.7806 15.2666C77.04 15.8643 77.2074 16.6286 77.2074 17.2131C77.2074 17.5576 77.0803 17.888 76.8498 18.143ZM75.7149 19.9792L75.8218 18.0554L75.9287 19.9792H75.7149ZM76.9638 15.1877C76.6413 14.4445 76.2363 14.0361 75.8218 14.0361C75.4072 14.0361 75.0015 14.4445 74.6798 15.1877C74.4064 15.8187 74.2363 16.5944 74.2363 17.2131C74.2363 18.001 74.8104 18.6636 75.5816 18.7811L75.515 19.9792H75.3976C75.3424 19.9792 75.2977 20.0238 75.2977 20.0791C75.2977 20.1334 75.3424 20.1781 75.3976 20.1781H76.246C76.3012 20.1781 76.3459 20.1334 76.3459 20.0791C76.3459 20.0238 76.3012 19.9792 76.246 19.9792H76.1285L76.0611 18.7811C76.4213 18.7259 76.7508 18.5488 76.9971 18.2762C77.2618 17.9844 77.4064 17.6075 77.4064 17.2131C77.4064 16.5944 77.2372 15.8187 76.9638 15.1877Z" fill="#003064"/>
                                            <path d="M75.8213 14.3251C75.5374 14.3251 75.2096 14.6906 74.944 15.3024C74.689 15.8896 74.5251 16.6407 74.5251 17.2139C74.5251 17.8168 74.9335 18.3313 75.5093 18.4724L75.5549 17.6389L75.051 17.135C74.9765 17.0614 74.9765 16.9413 75.051 16.8677C75.1246 16.7932 75.2447 16.7932 75.3183 16.8677L75.5838 17.1323L75.632 16.248C75.6382 16.1481 75.7206 16.0692 75.8213 16.0692C75.9221 16.0692 76.0045 16.1481 76.0107 16.248L76.0369 16.7362C76.1097 16.6889 76.2096 16.6976 76.2736 16.7616C76.3086 16.7967 76.3288 16.8449 76.3288 16.8957C76.3288 16.9457 76.3086 16.993 76.2736 17.0289L76.065 17.2366L76.0764 17.4347L76.3244 17.1867C76.3954 17.1148 76.5199 17.1148 76.5917 17.1867C76.6277 17.2217 76.6469 17.2691 76.6469 17.3199C76.6469 17.3707 76.6277 17.4172 76.5917 17.4531L76.1044 17.9413L76.1334 18.4715C76.3823 18.4102 76.6101 18.2743 76.7828 18.0833C76.9993 17.8449 77.1176 17.5355 77.1176 17.2139C77.1176 16.6407 76.9528 15.8896 76.6978 15.3024C76.4331 14.6906 76.1053 14.3251 75.8213 14.3251ZM75.6767 18.6863L75.5777 18.6696C74.8634 18.5513 74.3454 17.9387 74.3454 17.2139C74.3454 16.617 74.5163 15.8396 74.7801 15.2305C75.0825 14.5311 75.4532 14.1455 75.8213 14.1455C76.1894 14.1455 76.5593 14.5311 76.8625 15.2305C77.1263 15.8396 77.2972 16.617 77.2972 17.2139C77.2972 17.5802 77.1623 17.9316 76.916 18.2033C76.6934 18.4496 76.391 18.6153 76.065 18.6687L75.966 18.6863L75.9204 17.8712L76.4646 17.3269L75.9195 17.8449L75.8818 17.1674L76.1465 16.9018L76.022 16.9991L75.8801 17.1411L75.831 16.2577L75.7398 17.5434L75.1912 16.9947L75.739 17.5688L75.6767 18.6863ZM75.8091 19.8905H75.8336L75.8213 19.6679L75.8091 19.8905ZM76.0229 20.0693H75.6189L75.7319 18.0508H75.9107L76.0229 20.0693ZM75.3971 20.0693L76.2455 20.0885L76.1281 20.0693H76.0431L75.9677 18.7047L76.0475 18.6915C76.3866 18.6407 76.6995 18.4715 76.93 18.2165C77.1798 17.9413 77.3174 17.5855 77.3174 17.2139C77.3174 16.6065 77.15 15.8431 76.8809 15.2226C76.5742 14.5162 76.1982 14.1262 75.8213 14.1262C75.4445 14.1262 75.0685 14.5162 74.7617 15.2226C74.4927 15.8431 74.3262 16.6065 74.3262 17.2139C74.3262 17.9588 74.8599 18.5802 75.5952 18.6915L75.6759 18.7038L75.5996 20.0693H75.3971ZM76.2455 20.2673H75.3971C75.292 20.2673 75.2078 20.1832 75.2078 20.0789C75.2078 19.9746 75.292 19.8905 75.3971 19.8905H75.4296L75.4874 18.8554C74.7056 18.6985 74.1465 18.021 74.1465 17.2139C74.1465 16.5828 74.3191 15.7932 74.597 15.1516C74.9344 14.3751 75.3691 13.9465 75.8213 13.9465C76.2736 13.9465 76.7083 14.3751 77.0457 15.1516C77.3235 15.7932 77.4962 16.5828 77.4962 17.2139C77.4962 17.6302 77.3428 18.0289 77.0632 18.3366C76.824 18.6021 76.5032 18.7844 76.1553 18.8554L76.2122 19.8905H76.2455C76.3498 19.8905 76.4348 19.9746 76.4348 20.0789C76.4348 20.1832 76.3498 20.2673 76.2455 20.2673Z" fill="#003064"/>
                                            <path d="M57.7081 23.2919H79.2919V1.70722H57.7081V23.2919ZM80 24H57V1H80V24Z" fill="#003064"/>
                                            <path d="M89.6728 17.1942C90.0465 16.9273 90.2548 16.6139 90.2006 16.1701C90.1375 15.6537 89.8303 15.2642 89.5152 14.88C89.4697 14.8248 89.4469 14.8808 89.4242 14.908C89.1703 15.2064 88.9445 15.5224 88.8158 15.8988C88.6557 16.3697 88.7817 16.9001 89.2859 17.1645C89.2859 17.0165 89.2868 16.8712 89.2859 16.7268C89.2859 16.5999 89.3506 16.5211 89.4714 16.5176C89.6027 16.5132 89.6728 16.5929 89.6728 16.7268C89.6728 16.8721 89.6728 17.0165 89.6728 17.1942ZM96.4519 19.7711H103.879C104.03 19.7702 104.238 19.8201 104.316 19.7431C104.393 19.6669 104.33 19.4569 104.347 19.3072C104.366 19.1417 104.316 19.0971 104.143 19.0971C99.1215 19.1032 94.1 19.1015 89.0784 19.1032C88.9218 19.1032 88.7082 19.0358 88.6242 19.1312C88.5454 19.2214 88.6093 19.4306 88.5926 19.5855C88.5769 19.7369 88.6242 19.7755 88.7791 19.7755C91.3367 19.7693 93.8943 19.7711 96.4519 19.7711ZM92.682 13.1495C92.682 14.9421 92.6838 16.7356 92.6794 18.5273C92.6785 18.6603 92.7056 18.7032 92.8474 18.7015C93.6361 18.6936 94.4238 18.6927 95.2125 18.7023C95.3744 18.7041 95.4234 18.6735 95.4225 18.4984C95.4164 15.7946 95.4182 13.0917 95.4182 10.3871C95.4182 10.1516 95.4689 10.1009 95.7035 10.1009C96.2007 10.1 96.697 10.0956 97.1933 10.1026C97.3167 10.1052 97.3473 10.0702 97.3464 9.94943C97.3403 9.22557 97.3377 8.50083 97.3482 7.77696C97.3508 7.61153 97.2948 7.5914 97.1512 7.5914C95.721 7.59665 94.2899 7.59665 92.8597 7.5914C92.7153 7.59052 92.6776 7.62291 92.6785 7.76996C92.6838 9.56343 92.682 11.356 92.682 13.1495ZM100.825 14.6025C100.825 13.2887 100.823 11.974 100.828 10.6602C100.828 10.5236 100.795 10.4869 100.656 10.4869C99.0909 10.493 97.5259 10.493 95.9609 10.4869C95.8226 10.486 95.8015 10.5289 95.8015 10.6532C95.805 13.2817 95.8059 15.9102 95.8015 18.5378C95.8007 18.6682 95.8296 18.7015 95.9626 18.7015C97.5276 18.6962 99.0926 18.6953 100.658 18.7015C100.798 18.7015 100.828 18.663 100.828 18.5273C100.823 17.2196 100.825 15.911 100.825 14.6025ZM103.548 12.0029C103.548 9.82514 103.547 7.64742 103.55 5.46882C103.55 5.36203 103.547 5.30339 103.407 5.30426C101.948 5.30951 100.488 5.30864 99.0279 5.30514C98.9132 5.30426 98.8817 5.33402 98.8826 5.45044C98.8931 6.95069 98.8283 8.45181 98.8808 9.95206C98.8852 10.0755 98.915 10.1044 99.0357 10.1026C99.6493 10.0965 100.262 10.1009 100.876 10.1009C101.188 10.1009 101.23 10.1411 101.23 10.4492C101.23 13.1346 101.231 15.8218 101.227 18.5072C101.227 18.6516 101.246 18.7059 101.41 18.7023C102.064 18.691 102.719 18.6927 103.373 18.7015C103.52 18.7041 103.551 18.6647 103.551 18.5212C103.546 16.3478 103.548 14.1762 103.548 12.0029ZM96.4711 20.158H88.5174C88.2452 20.158 88.2014 20.1151 88.2014 19.8472C88.2014 19.5558 88.2005 19.2634 88.2023 18.9711C88.2031 18.7671 88.2679 18.7015 88.4745 18.6988C88.6959 18.6953 88.9183 18.6927 89.1397 18.6997C89.2447 18.7032 89.2946 18.6875 89.2894 18.5614C89.2789 18.2997 89.2824 18.0363 89.2885 17.7737C89.2903 17.6861 89.2614 17.6397 89.1861 17.5969C88.976 17.4796 88.7861 17.336 88.6277 17.1522C88.2924 16.7627 88.2294 16.3207 88.3887 15.8445C88.5769 15.2843 88.9428 14.8388 89.3375 14.4143C89.4163 14.3311 89.5222 14.3119 89.6019 14.3968C90.0465 14.8703 90.4605 15.3666 90.6163 16.0205C90.7354 16.522 90.5734 16.9395 90.2058 17.2844C90.1122 17.371 90.0141 17.4656 89.9012 17.5163C89.706 17.6047 89.65 17.736 89.6693 17.9408C89.6876 18.1369 89.6815 18.3374 89.671 18.5352C89.664 18.6612 89.692 18.7032 89.8294 18.7015C90.5944 18.6936 91.3594 18.6936 92.1244 18.7015C92.2627 18.7032 92.2838 18.656 92.2829 18.5325C92.2785 17.399 92.2802 16.2664 92.2802 15.1338C92.2802 12.6112 92.2802 10.0886 92.2802 7.56602C92.2802 7.22815 92.3144 7.19402 92.6496 7.19402H97.4147C97.679 7.19402 97.7307 7.24566 97.7307 7.50912C97.7307 8.31964 97.7342 9.13191 97.7272 9.94418C97.7263 10.0781 97.7622 10.1131 97.8909 10.1026C98.0423 10.0912 98.1946 10.093 98.346 10.1017C98.4607 10.1096 98.4913 10.0746 98.4896 9.95906C98.4616 8.42905 98.4799 6.89904 98.4878 5.36903C98.4904 4.91476 98.4904 4.91476 98.9438 4.91476H103.621C103.893 4.91476 103.934 4.9559 103.934 5.22724C103.934 9.65446 103.935 14.0799 103.93 18.5072C103.93 18.6717 103.977 18.7155 104.131 18.7006C104.252 18.6875 104.376 18.6936 104.498 18.6988C104.67 18.7067 104.737 18.7776 104.737 18.9518C104.74 19.2669 104.739 19.5829 104.737 19.898C104.737 20.0949 104.674 20.1571 104.477 20.158H100.938H96.4711Z" fill="#003064"/>
                                            <path d="M100.404 6.7584C100.404 6.44942 100.404 6.45643 100.095 6.44855C99.9669 6.44417 99.9144 6.47218 99.9328 6.60785C99.945 6.69976 99.9442 6.79516 99.9328 6.88707C99.9153 7.02624 99.9652 7.063 100.103 7.05863C100.404 7.04812 100.404 7.056 100.404 6.7584ZM100.809 6.75753C100.809 6.9037 100.812 7.04987 100.809 7.19517C100.805 7.37898 100.737 7.45513 100.559 7.45864C100.296 7.46389 100.033 7.46214 99.7709 7.45951C99.6159 7.45776 99.5337 7.37461 99.5328 7.21881C99.531 6.90458 99.5302 6.58947 99.5337 6.27437C99.5354 6.12732 99.6177 6.04767 99.7647 6.04767C100.033 6.04504 100.301 6.04241 100.569 6.04767C100.737 6.05029 100.805 6.12819 100.809 6.30238C100.812 6.45468 100.809 6.6061 100.809 6.75753Z" fill="#003064"/>
                                            <path d="M102.1 6.75322C102.1 7.0552 102.1 7.04732 102.393 7.0587C102.536 7.06395 102.592 7.02981 102.571 6.88102C102.559 6.78998 102.559 6.69545 102.571 6.60355C102.591 6.46613 102.532 6.44337 102.409 6.446C102.1 6.45387 102.1 6.44775 102.1 6.75322ZM102.972 6.74184C102.972 6.89414 102.976 7.04557 102.971 7.197C102.967 7.38606 102.903 7.45521 102.72 7.45871C102.464 7.46396 102.207 7.46396 101.951 7.45871C101.77 7.45608 101.698 7.38081 101.696 7.2005C101.693 6.9029 101.693 6.6053 101.695 6.3077C101.697 6.12389 101.768 6.04949 101.947 6.04686C102.204 6.04249 102.461 6.04249 102.718 6.04686C102.901 6.04949 102.966 6.11776 102.971 6.3042C102.976 6.45037 102.972 6.59655 102.972 6.74184Z" fill="#003064"/>
                                            <path d="M102.1 8.66265C102.1 8.98388 102.1 8.97688 102.409 8.98563C102.535 8.98913 102.589 8.96112 102.57 8.82633C102.558 8.7353 102.557 8.63989 102.57 8.54886C102.592 8.39656 102.529 8.36767 102.39 8.37293C102.1 8.3843 102.1 8.37643 102.1 8.66265ZM102.971 8.69328C102.971 8.84558 102.973 8.99701 102.97 9.14843C102.968 9.30424 102.884 9.38476 102.73 9.38651C102.467 9.38914 102.205 9.39089 101.943 9.38564C101.771 9.38214 101.697 9.30686 101.695 9.13793C101.692 8.8342 101.695 8.53135 101.693 8.22763C101.692 8.0622 101.775 7.97467 101.936 7.97117C102.205 7.96679 102.472 7.96679 102.741 7.97204C102.886 7.97467 102.967 8.05694 102.97 8.20399C102.973 8.3668 102.97 8.53048 102.971 8.69328Z" fill="#003064"/>
                                            <path d="M102.569 10.6149C102.569 10.5747 102.563 10.5327 102.57 10.4933C102.6 10.3296 102.526 10.2937 102.373 10.3007C102.1 10.3121 102.099 10.3042 102.099 10.572C102.099 10.6307 102.107 10.6902 102.099 10.7462C102.075 10.8898 102.141 10.916 102.27 10.9125C102.569 10.9038 102.569 10.9108 102.569 10.6149ZM102.971 10.6097C102.971 10.7611 102.972 10.9125 102.971 11.0648C102.971 11.2285 102.888 11.3134 102.724 11.3143C102.462 11.316 102.199 11.3178 101.937 11.3134C101.775 11.3117 101.693 11.2233 101.693 11.0587C101.695 10.755 101.692 10.4521 101.695 10.1493C101.697 9.98035 101.773 9.90245 101.942 9.89894C102.205 9.89369 102.467 9.89544 102.73 9.89894C102.883 9.90069 102.969 9.98035 102.97 10.137C102.973 10.2946 102.971 10.4513 102.971 10.6097Z" fill="#003064"/>
                                            <path d="M100.403 8.68052C100.403 8.628 100.396 8.57461 100.404 8.52384C100.424 8.3978 100.37 8.37067 100.252 8.37329C99.935 8.37942 99.9341 8.37329 99.9341 8.68752C99.9341 8.73391 99.9411 8.78118 99.9332 8.82669C99.9096 8.96061 99.9674 8.9895 100.093 8.986C100.403 8.97724 100.403 8.98424 100.403 8.68052ZM100.809 8.68314C100.809 8.82844 100.811 8.97462 100.809 9.11991C100.805 9.3046 100.737 9.3825 100.56 9.386C100.297 9.39126 100.035 9.3895 99.773 9.38688C99.619 9.38513 99.5332 9.3046 99.5323 9.1488C99.5306 8.83982 99.5315 8.52997 99.5315 8.22187C99.5323 8.05731 99.6137 7.97328 99.7792 7.97328C100.042 7.97328 100.304 7.96978 100.567 7.97416C100.735 7.97766 100.804 8.05381 100.809 8.22712C100.811 8.37942 100.809 8.53084 100.809 8.68314Z" fill="#003064"/>
                                            <path d="M102.569 12.8675C102.569 12.5497 102.569 12.5576 102.258 12.548C102.128 12.5445 102.084 12.5777 102.098 12.709C102.11 12.8237 102.103 12.941 102.099 13.0565C102.096 13.1283 102.116 13.1537 102.194 13.1589C102.569 13.1843 102.569 13.1878 102.569 12.8675ZM102.971 12.8631C102.971 13.0145 102.976 13.1668 102.97 13.3182C102.964 13.4889 102.892 13.5598 102.72 13.5624C102.464 13.5659 102.206 13.5659 101.95 13.5624C101.771 13.5598 101.697 13.4854 101.695 13.3051C101.692 13.0014 101.693 12.6985 101.694 12.3948C101.695 12.2399 101.77 12.155 101.929 12.1541C102.197 12.1541 102.465 12.155 102.733 12.1541C102.894 12.1532 102.969 12.2364 102.971 12.3904C102.973 12.548 102.971 12.7047 102.971 12.8631Z" fill="#003064"/>
                                            <path d="M102.569 17.3594C102.569 17.278 102.568 17.1957 102.569 17.1152C102.571 17.0653 102.552 17.0487 102.501 17.0399C102.155 16.9813 102.099 17.0242 102.099 17.3612C102.099 17.4084 102.105 17.4548 102.098 17.5012C102.08 17.6281 102.121 17.6719 102.257 17.6684C102.569 17.6579 102.569 17.6666 102.569 17.3594ZM101.695 17.348C101.695 17.1914 101.691 17.0329 101.696 16.8754C101.702 16.7318 101.772 16.6504 101.926 16.6522C102.195 16.6548 102.463 16.6566 102.732 16.6513C102.885 16.6487 102.965 16.7222 102.968 16.8675C102.974 17.1887 102.973 17.51 102.969 17.8303C102.967 17.9739 102.894 18.0561 102.74 18.0553C102.471 18.0526 102.203 18.0526 101.934 18.0553C101.773 18.057 101.699 17.973 101.695 17.8198C101.693 17.6631 101.695 17.5056 101.695 17.348Z" fill="#003064"/>
                                            <path d="M102.099 15.0967C102.099 15.1317 102.104 15.1676 102.098 15.2008C102.065 15.3794 102.137 15.4293 102.313 15.4179C102.568 15.4013 102.569 15.4135 102.569 15.158C102.569 15.0888 102.56 15.0179 102.57 14.9496C102.592 14.8131 102.533 14.7886 102.409 14.7921C102.099 14.8 102.099 14.7938 102.099 15.0967ZM101.694 15.1116C101.694 14.954 101.693 14.7965 101.695 14.6398C101.697 14.4857 101.771 14.4026 101.932 14.4043C102.2 14.407 102.469 14.407 102.737 14.4043C102.891 14.4026 102.967 14.4805 102.969 14.6258C102.973 14.9461 102.974 15.2674 102.968 15.5886C102.965 15.7339 102.882 15.81 102.732 15.81C102.47 15.8092 102.208 15.8118 101.946 15.8083C101.769 15.8074 101.698 15.733 101.695 15.5492C101.692 15.4039 101.694 15.2577 101.694 15.1116Z" fill="#003064"/>
                                            <path d="M99.4134 16.6122C99.2769 16.6288 99.0887 16.5544 99.0152 16.6446C98.946 16.7295 98.9942 16.9098 98.9898 17.0472C98.9898 17.0656 98.9924 17.0831 98.9898 17.0997C98.9723 17.2039 99.0222 17.2249 99.1167 17.2214C99.2909 17.2144 99.4659 17.21 99.6393 17.2223C99.7898 17.2328 99.8625 17.1995 99.8397 17.0306C99.8222 16.9001 99.88 16.7207 99.8126 16.6446C99.732 16.5553 99.5508 16.6288 99.4134 16.6122ZM99.4152 16.213C99.6077 16.213 99.8003 16.2139 99.9929 16.213C100.159 16.213 100.239 16.2979 100.239 16.4608C100.24 16.7645 100.243 17.0682 100.238 17.3702C100.235 17.554 100.164 17.6231 99.9797 17.6249C99.6007 17.6284 99.2217 17.6258 98.8427 17.6258C98.6808 17.6258 98.5898 17.5505 98.5889 17.3842C98.5872 17.0752 98.588 16.7662 98.5889 16.4572C98.5889 16.2918 98.6729 16.2122 98.8384 16.213C99.03 16.2139 99.2226 16.213 99.4152 16.213Z" fill="#003064"/>
                                            <path d="M99.4265 12.2442C99.2873 12.2442 99.1018 12.19 99.0212 12.2591C98.9249 12.3396 99.002 12.5313 98.9888 12.6749C98.988 12.6862 98.9906 12.6994 98.9888 12.7099C98.9722 12.8132 99.0055 12.8543 99.1193 12.8473C99.2934 12.8377 99.4703 12.8333 99.6444 12.8482C99.8081 12.8631 99.8449 12.8062 99.8405 12.6495C99.8291 12.2355 99.837 12.2355 99.4265 12.2442ZM99.4116 11.839C99.6033 11.839 99.7959 11.8407 99.9884 11.8381C100.156 11.8355 100.238 11.916 100.238 12.0805C100.239 12.3895 100.24 12.6994 100.237 13.0075C100.236 13.172 100.146 13.2491 99.9823 13.2491C99.6042 13.2482 99.2252 13.2508 98.8462 13.2482C98.6632 13.2473 98.5923 13.1764 98.5897 12.9926C98.5862 12.6906 98.5888 12.3869 98.5888 12.0832C98.5888 11.9195 98.6659 11.8363 98.8339 11.8381C99.0265 11.8407 99.219 11.839 99.4116 11.839Z" fill="#003064"/>
                                            <path d="M97.2245 17.2268C97.3628 17.2198 97.544 17.2626 97.6271 17.1917C97.7164 17.1165 97.6464 16.9292 97.6551 16.7917C97.6569 16.769 97.6534 16.7445 97.656 16.7226C97.6683 16.6386 97.6341 16.6106 97.5501 16.6132C97.3584 16.6184 97.165 16.6246 96.9742 16.6114C96.8324 16.6027 96.786 16.6386 96.7895 16.7891C96.8 17.2198 96.7921 17.2198 97.2245 17.2268ZM97.2227 17.625C97.0302 17.625 96.8376 17.6285 96.6459 17.6241C96.4726 17.6198 96.4017 17.548 96.4008 17.3773C96.3973 17.0736 96.3982 16.7707 96.4 16.4679C96.4017 16.2902 96.4656 16.2175 96.638 16.2158C97.0284 16.2114 97.4197 16.2132 97.8101 16.2149C97.9632 16.2158 98.0473 16.2876 98.0455 16.4504C98.0429 16.7602 98.0464 17.0692 98.0446 17.3782C98.0429 17.5506 97.9755 17.6198 97.8004 17.6241C97.6079 17.6285 97.4153 17.625 97.2227 17.625Z" fill="#003064"/>
                                            <path d="M99.4089 15.0316C99.5472 15.0281 99.7354 15.1016 99.8098 15.0132C99.8798 14.9292 99.8317 14.7471 99.8361 14.608C99.8361 14.5905 99.8334 14.573 99.8361 14.5555C99.8518 14.4513 99.8168 14.4093 99.7039 14.4163C99.535 14.4268 99.3643 14.4303 99.1962 14.4154C99.0326 14.4005 98.9582 14.4425 98.9862 14.6193C99.008 14.7559 98.9258 14.9406 99.0194 15.0176C99.0982 15.0841 99.275 15.0316 99.4089 15.0316ZM99.4116 15.4264C99.219 15.4264 99.0273 15.4272 98.8347 15.4264C98.6536 15.4246 98.5923 15.3695 98.5897 15.1874C98.5844 14.8846 98.5853 14.5808 98.5888 14.2789C98.5914 14.0915 98.6597 14.0277 98.8444 14.0268C99.2234 14.025 99.6015 14.025 99.9805 14.0268C100.164 14.0277 100.233 14.0924 100.236 14.2789C100.24 14.5817 100.24 14.8854 100.236 15.1883C100.233 15.3677 100.169 15.4246 99.9892 15.4264C99.7967 15.4272 99.6041 15.4264 99.4116 15.4264Z" fill="#003064"/>
                                            <path d="M97.2547 12.2358C97.1251 12.2358 97.0192 12.2393 96.9151 12.2341C96.8302 12.2297 96.7847 12.2472 96.7917 12.347C96.7995 12.4739 96.7987 12.6034 96.7917 12.7312C96.7855 12.8301 96.8293 12.8485 96.9151 12.8468C97.1015 12.8415 97.2888 12.8363 97.4753 12.8485C97.6179 12.8581 97.6748 12.8196 97.6582 12.67C97.6424 12.5325 97.6985 12.3513 97.6293 12.2656C97.5549 12.1737 97.3676 12.2498 97.2547 12.2358ZM97.2214 11.8393C97.414 11.8393 97.6066 11.8358 97.7991 11.8402C97.9794 11.8446 98.0433 11.9067 98.0442 12.0809C98.046 12.3907 98.0425 12.6988 98.046 13.0087C98.0468 13.1697 97.9637 13.2468 97.8114 13.2476C97.4201 13.2511 97.0297 13.252 96.6385 13.2476C96.4704 13.2459 96.4013 13.168 96.4004 12.9947C96.3978 12.6918 96.3978 12.3881 96.4004 12.0844C96.4013 11.9111 96.4678 11.8446 96.6437 11.8402C96.8363 11.8358 97.0289 11.8393 97.2214 11.8393Z" fill="#003064"/>
                                            <path d="M97.1997 15.0401C97.3231 15.0401 97.4229 15.034 97.5209 15.0419C97.6216 15.0506 97.6706 15.027 97.6592 14.9123C97.6478 14.7907 97.6513 14.6673 97.6583 14.5465C97.6644 14.4493 97.6356 14.4125 97.5332 14.4169C97.3537 14.4239 97.1725 14.4292 96.9931 14.416C96.839 14.4038 96.769 14.4449 96.7927 14.6121C96.8119 14.7487 96.7358 14.9281 96.8224 15.0139C96.9082 15.0988 97.0868 15.0296 97.1997 15.0401ZM97.2347 15.4261C97.036 15.4261 96.8382 15.4226 96.6395 15.427C96.4784 15.4305 96.4014 15.3552 96.4014 15.1986C96.4005 14.8826 96.3997 14.5683 96.4014 14.2532C96.4023 14.1044 96.4793 14.0265 96.629 14.0265C97.0255 14.0257 97.422 14.0257 97.8185 14.0265C97.9664 14.0265 98.0461 14.0992 98.0461 14.2506C98.0461 14.5718 98.0469 14.8922 98.0461 15.2126C98.0452 15.3544 97.9734 15.427 97.829 15.4261C97.6312 15.4253 97.4325 15.4261 97.2347 15.4261Z" fill="#003064"/>
                                            <path d="M94.122 17.5121C94.122 17.4596 94.115 17.4071 94.1228 17.3554C94.143 17.2303 94.0896 17.2031 93.9705 17.2058C93.6414 17.2119 93.6414 17.2066 93.6414 17.5296C93.6414 17.5646 93.6458 17.6005 93.6405 17.6338C93.6169 17.7738 93.6642 17.8246 93.8147 17.8194C94.122 17.808 94.122 17.8167 94.122 17.5121ZM93.2467 17.4964C93.2467 17.3458 93.2475 17.1944 93.2467 17.0421C93.2449 16.8907 93.3263 16.811 93.469 16.8075C93.743 16.8014 94.0178 16.8014 94.2918 16.8075C94.4344 16.8101 94.5158 16.8845 94.5141 17.0386C94.5115 17.3537 94.5115 17.6688 94.5141 17.983C94.5158 18.1476 94.4274 18.2176 94.2769 18.2202C94.0152 18.2229 93.7526 18.2246 93.49 18.2202C93.3184 18.2159 93.2502 18.1415 93.2475 17.969C93.2449 17.8123 93.2467 17.6548 93.2467 17.4964Z" fill="#003064"/>
                                            <path d="M94.1216 15.2848C94.1216 15.2271 94.1146 15.1676 94.1225 15.1115C94.1409 14.9881 94.0945 14.9566 93.9719 14.9592C93.6411 14.9662 93.6411 14.9592 93.6411 15.2822C93.6411 15.3172 93.6463 15.3531 93.6402 15.3864C93.6087 15.5483 93.684 15.5781 93.8301 15.5719C94.1216 15.5606 94.1216 15.5684 94.1216 15.2848ZM94.5129 15.2586C94.5129 15.4214 94.512 15.5851 94.5129 15.7479C94.5137 15.8923 94.4385 15.9632 94.2975 15.9641C94.0183 15.9658 93.7382 15.9676 93.4581 15.9632C93.3181 15.9614 93.2472 15.8809 93.2463 15.7409C93.2463 15.4196 93.2454 15.0993 93.2472 14.7789C93.2481 14.638 93.3295 14.5619 93.4651 14.5592C93.7391 14.5531 94.0131 14.554 94.287 14.5584C94.4306 14.5601 94.5146 14.6328 94.5129 14.7868C94.512 14.9435 94.5129 15.101 94.5129 15.2586Z" fill="#003064"/>
                                            <path d="M93.6402 9.15764C93.6402 9.23291 93.6428 9.30906 93.6393 9.38521C93.6376 9.44211 93.6577 9.46311 93.7172 9.47099C94.0884 9.52351 94.1207 9.49812 94.1207 9.13663C94.1207 9.10162 94.1155 9.06573 94.1216 9.03159C94.1488 8.88192 94.0849 8.84778 93.9413 8.85216C93.6402 8.86354 93.6402 8.85566 93.6402 9.15764ZM94.512 9.16376C94.512 9.32657 94.5129 9.49025 94.5111 9.65305C94.5094 9.78785 94.4402 9.85874 94.3054 9.85874C94.0253 9.85962 93.7461 9.85962 93.466 9.85962C93.3242 9.85962 93.2481 9.78609 93.2463 9.64605C93.2437 9.31956 93.2428 8.99308 93.2472 8.6666C93.2481 8.5353 93.326 8.45915 93.4555 8.4574C93.7409 8.45215 94.0271 8.45127 94.3124 8.45828C94.442 8.4609 94.5111 8.54055 94.5111 8.6736C94.5111 8.83728 94.5111 9.00008 94.512 9.16376Z" fill="#003064"/>
                                            <path d="M94.1223 13.0109C94.1223 12.7045 94.1223 12.7142 93.8299 12.7019C93.6759 12.6958 93.6198 12.7369 93.6391 12.8927C93.6522 13.0013 93.6452 13.1133 93.64 13.2236C93.6373 13.2971 93.661 13.3207 93.738 13.326C94.1223 13.354 94.1223 13.3566 94.1223 13.0109ZM94.5135 13.0083C94.5135 13.1719 94.5135 13.3347 94.5135 13.4984C94.5126 13.6411 94.4417 13.7146 94.2991 13.7146C94.019 13.7146 93.7398 13.7155 93.4597 13.7137C93.3161 13.712 93.2478 13.6315 93.247 13.4923C93.2461 13.1719 93.2452 12.8507 93.2478 12.5304C93.2487 12.3912 93.3266 12.3133 93.4649 12.3115C93.745 12.308 94.0242 12.3072 94.3043 12.3115C94.4339 12.3142 94.51 12.3842 94.5118 12.519C94.5144 12.6827 94.5135 12.8463 94.5135 13.0083Z" fill="#003064"/>
                                            <path d="M94.1223 11.0983C94.1223 10.7788 94.1223 10.7788 93.9139 10.7788C93.6417 10.7788 93.6417 10.7788 93.6417 11.0554C93.6417 11.114 93.6487 11.1727 93.64 11.2287C93.619 11.3687 93.6741 11.4046 93.8107 11.4011C94.1223 11.3915 94.1223 11.3985 94.1223 11.0983ZM94.5135 11.0895C94.5135 11.2532 94.5144 11.416 94.5126 11.5797C94.5118 11.7127 94.4435 11.7854 94.3078 11.7862C94.0225 11.7871 93.7371 11.788 93.4518 11.7854C93.3161 11.7836 93.2487 11.704 93.2478 11.5753C93.2452 11.2549 93.2461 10.9337 93.247 10.6125C93.2478 10.4645 93.3257 10.3858 93.4737 10.3849C93.7476 10.3823 94.0216 10.3814 94.2956 10.3849C94.433 10.3858 94.5118 10.4575 94.5126 10.6002C94.5135 10.763 94.5126 10.9267 94.5135 11.0895Z" fill="#003064"/>
                                            <path d="M95.804 9.14334C95.804 9.20111 95.811 9.26063 95.8022 9.31752C95.7856 9.43831 95.8241 9.47595 95.951 9.47333C96.2915 9.46545 96.2915 9.47245 96.2915 9.12496C96.2915 9.10133 96.288 9.07769 96.2924 9.05406C96.3169 8.90176 96.2661 8.84312 96.099 8.85187C95.804 8.86762 95.804 8.85624 95.804 9.14334ZM95.418 9.15735V8.66718C95.418 8.53064 95.495 8.46061 95.6228 8.45799C95.9029 8.45186 96.1821 8.45361 96.4622 8.45711C96.5987 8.45886 96.6793 8.52976 96.6793 8.67418C96.6793 9.00067 96.6801 9.32715 96.6793 9.65276C96.6784 9.78668 96.6057 9.85758 96.4727 9.85845C96.1874 9.8602 95.902 9.86108 95.6167 9.85758C95.4819 9.85583 95.4171 9.78055 95.418 9.64663C95.418 9.48383 95.418 9.32015 95.418 9.15735Z" fill="#003064"/>
                                            <path d="M85.7081 23.2919H107.292V1.70722H85.7081V23.2919ZM108 24H85V1H108V24Z" fill="#003064"/>
                                            <path d="M135.149 20.5927H134.297V19.7408H135.149V20.5927Z" fill="#003064"/>
                                            <path d="M119.351 20.5927H116.408V19.7408H119.351V20.5927ZM123.765 20.5927H120.823V19.7408H123.765V20.5927ZM128.179 20.5927H125.236V19.7408H128.179V20.5927ZM132.593 20.5927H129.651V19.7408H132.593V20.5927Z" fill="#003064"/>
                                            <path d="M114.703 20.5927H113.852V19.7408H114.703V20.5927Z" fill="#003064"/>
                                            <path d="M121.282 7.67969L119.107 6.78923V6.28879L121.282 5.40272V5.91193L119.293 6.71036V6.37819L121.282 7.17486V7.67969Z" fill="#003064"/>
                                            <path d="M122.811 7.85693C122.592 7.85693 122.382 7.82713 122.182 7.76578C121.981 7.70531 121.819 7.6203 121.695 7.51162L121.907 6.99365C122.043 7.09356 122.184 7.16806 122.331 7.21539C122.476 7.26184 122.631 7.28638 122.793 7.28638C122.973 7.28638 123.109 7.25307 123.201 7.18559C123.292 7.11898 123.338 7.01994 123.338 6.8876C123.338 6.75964 123.295 6.66587 123.207 6.60364C123.12 6.54141 122.992 6.51074 122.824 6.51074H122.292V5.96122H122.758C122.908 5.96122 123.027 5.92704 123.112 5.85955C123.198 5.79207 123.24 5.69566 123.24 5.57208C123.24 5.45639 123.2 5.36875 123.119 5.30828C123.038 5.24693 122.921 5.21713 122.767 5.21713C122.466 5.21713 122.197 5.31529 121.961 5.50898L121.744 5.00416C121.876 4.89197 122.038 4.80521 122.228 4.74035C122.419 4.67725 122.617 4.6457 122.824 4.6457C123.161 4.6457 123.425 4.72019 123.615 4.86919C123.806 5.01905 123.9 5.22414 123.9 5.48707C123.9 5.66761 123.85 5.82537 123.748 5.95859C123.646 6.09356 123.505 6.1812 123.325 6.22327V6.16455C123.538 6.19698 123.703 6.28374 123.821 6.42397C123.939 6.5642 123.998 6.74124 123.998 6.95333C123.998 7.23379 123.893 7.45465 123.681 7.61592C123.47 7.7763 123.18 7.85693 122.811 7.85693Z" fill="#003064"/>
                                            <path d="M124.747 8.45093L124.486 8.24234C124.592 8.1398 124.666 8.04076 124.708 7.94786C124.749 7.85496 124.77 7.7568 124.77 7.65338L124.916 7.81201H124.424V7.10386H125.146V7.5596C125.146 7.72875 125.115 7.88212 125.056 8.02148C124.994 8.15995 124.892 8.30281 124.747 8.45093Z" fill="#003064"/>
                                            <path d="M126.691 7.85693C126.552 7.85693 126.416 7.84291 126.282 7.81486C126.147 7.78682 126.02 7.74738 125.903 7.69479C125.785 7.64396 125.684 7.58261 125.602 7.51162L125.815 6.99365C125.953 7.09093 126.093 7.16368 126.235 7.21276C126.376 7.26096 126.524 7.28638 126.678 7.28638C126.849 7.28638 126.983 7.24519 127.078 7.16368C127.175 7.08217 127.222 6.97262 127.222 6.83414C127.222 6.69216 127.177 6.57822 127.085 6.49233C126.994 6.40732 126.87 6.36437 126.714 6.36437C126.598 6.36437 126.492 6.38541 126.396 6.42835C126.3 6.4713 126.212 6.5344 126.134 6.61679H125.694V4.69039H127.687V5.25218H126.346V6.09882H126.182C126.258 6.00416 126.358 5.93317 126.481 5.88321C126.603 5.83501 126.738 5.81047 126.886 5.81047C127.086 5.81047 127.262 5.85166 127.411 5.93492C127.56 6.01731 127.676 6.133 127.758 6.28199C127.841 6.43186 127.883 6.60802 127.883 6.81223C127.883 7.0217 127.835 7.20487 127.739 7.36175C127.643 7.51688 127.505 7.63958 127.327 7.72722C127.148 7.81311 126.936 7.85693 126.691 7.85693Z" fill="#003064"/>
                                            <path d="M130.044 7.81226V6.0892H129.305V5.57123H131.457V6.0892H130.713V7.81226H130.044Z" fill="#003064"/>
                                            <path d="M120.914 13.2978H128.874L128.278 11.5669C128.141 11.1699 128.089 11.1339 127.658 11.1339H122.134C122.079 11.1339 122.024 11.1383 121.969 11.1418L121.898 11.1471C121.723 11.1576 121.625 11.233 121.562 11.4065C121.486 11.6169 121.413 11.8281 121.341 12.0393L120.914 13.2978ZM129.628 13.8351H120.162L120.832 11.8666C120.905 11.6519 120.979 11.4372 121.056 11.2233C121.191 10.85 121.479 10.6326 121.866 10.6098C121.999 10.6011 122.067 10.5967 122.134 10.5967L127.658 10.5958C128.312 10.5958 128.575 10.7816 128.787 11.3907L129.628 13.8351ZM120.324 15.2584C120.123 15.2584 119.962 15.3838 119.892 15.5994C119.831 15.7869 119.858 15.9569 119.978 16.134C120.086 16.2944 120.267 16.3557 120.462 16.2987C120.635 16.247 120.779 16.0174 120.779 15.7948C120.779 15.5354 120.61 15.2979 120.404 15.2654C120.376 15.2611 120.35 15.2584 120.324 15.2584ZM120.323 16.857C120.008 16.857 119.716 16.7063 119.533 16.4355C119.318 16.12 119.268 15.7825 119.38 15.4337C119.541 14.9359 119.987 14.6554 120.488 14.7343C120.95 14.8071 121.315 15.2716 121.318 15.7922C121.318 16.2558 121.016 16.6958 120.614 16.815C120.517 16.843 120.418 16.857 120.323 16.857ZM129.467 15.2505C129.412 15.2505 129.357 15.2602 129.305 15.2812C129.127 15.3557 129.008 15.552 129.011 15.7703V15.8097C129.024 16.0893 129.207 16.2926 129.428 16.3215C129.613 16.3461 129.82 16.198 129.898 15.9841C129.979 15.7632 129.931 15.5652 129.746 15.3618C129.681 15.2891 129.574 15.2505 129.467 15.2505ZM129.47 16.8623C129.433 16.8623 129.396 16.8597 129.358 16.8553C128.882 16.7922 128.502 16.3776 128.474 15.8886C128.472 15.8605 128.473 15.8307 128.473 15.8001L128.742 15.7738L128.473 15.7773C128.468 15.3347 128.713 14.9455 129.099 14.7843C129.462 14.6327 129.893 14.7229 130.144 14.9999C130.465 15.3531 130.554 15.7571 130.404 16.1682C130.252 16.5853 129.871 16.8623 129.47 16.8623ZM130.301 13.4503L130.319 13.467C130.313 13.4617 130.307 13.4556 130.301 13.4503ZM119.713 13.3049H119.957L119.998 13.7229L119.611 14.077C119.396 14.2716 119.181 14.4679 118.971 14.6677C118.758 14.8737 118.648 15.134 118.646 15.4433C118.638 16.7352 118.642 18.0271 118.645 19.3172C118.645 19.4521 118.68 19.6327 118.982 19.6336C119.307 19.6362 119.632 19.6353 119.958 19.6344C120.224 19.6336 120.329 19.5336 120.332 19.2786L120.334 19.1305C120.339 18.9096 120.342 18.6896 120.329 18.4697C120.326 18.4144 120.316 18.2444 120.439 18.1138C120.562 17.9824 120.751 17.9824 120.807 17.9832C123.542 17.9841 126.277 17.985 129.013 17.9815C129.121 17.9841 129.248 17.9938 129.354 18.1033C129.472 18.2243 129.468 18.3855 129.466 18.4381L129.461 18.5661C129.453 18.85 129.444 19.1191 129.483 19.382C129.496 19.4714 129.548 19.6213 129.745 19.6265C130.128 19.6371 130.511 19.6379 130.894 19.6257C131.037 19.6213 131.119 19.5538 131.144 19.4188C131.159 19.3382 131.169 19.2549 131.169 19.1726C131.171 17.9683 131.171 16.765 131.17 15.5617C131.169 15.1454 131.015 14.8123 130.701 14.5406C130.513 14.3794 130.333 14.2111 130.152 14.0428L129.978 13.8789C129.778 13.7326 129.844 13.531 129.868 13.4714L129.938 13.3092L130.343 13.3101C130.509 13.3119 130.675 13.3127 130.843 13.3084C131 13.304 131.142 13.2391 131.244 13.1243C131.343 13.0121 131.391 12.8684 131.377 12.7194C131.362 12.5538 131.322 12.5187 131.152 12.5178L130.798 12.5187C130.576 12.5187 130.356 12.5196 130.136 12.517C130.015 12.5134 129.987 12.545 129.943 12.637C129.893 12.7992 129.78 12.8544 129.693 12.8719L129.464 12.9166L129.219 12.2155C129.095 11.8588 128.971 11.5038 128.838 11.1515C128.638 10.6186 128.345 10.4205 127.76 10.4197C125.847 10.417 123.933 10.417 122.02 10.4197C121.464 10.4205 121.146 10.6388 120.957 11.1497C120.879 11.3592 120.806 11.5695 120.734 11.7816L120.088 13.6169L119.878 12.7264C119.833 12.5406 119.773 12.5029 119.555 12.5134C119.347 12.5248 119.139 12.5222 118.93 12.5205L118.598 12.5187C118.501 12.5196 118.455 12.5546 118.429 12.6502C118.389 12.7913 118.425 12.9499 118.524 13.0831C118.626 13.219 118.779 13.3014 118.933 13.304C119.112 13.3075 119.29 13.3066 119.468 13.3057L119.713 13.3049ZM119.432 20.1743C119.28 20.1743 119.129 20.1734 118.978 20.1726C118.459 20.1682 118.108 19.8255 118.106 19.3189C118.104 18.0262 118.1 16.7335 118.108 15.4398C118.111 14.9867 118.281 14.5853 118.599 14.2795C118.753 14.1322 118.91 13.9876 119.068 13.8439C119.02 13.843 118.972 13.843 118.924 13.8421C118.6 13.8369 118.297 13.6782 118.093 13.4057C117.893 13.1375 117.827 12.8097 117.91 12.5064C118 12.1804 118.256 11.9841 118.594 11.9806L118.935 11.9823C119.134 11.985 119.331 11.9876 119.527 11.9771C119.738 11.9657 119.919 12.0016 120.058 12.0857L120.225 11.6063C120.298 11.3907 120.373 11.176 120.452 10.9622C120.717 10.2461 121.245 9.8824 122.019 9.88153C123.934 9.8789 125.847 9.8789 127.761 9.88153C128.573 9.88328 129.06 10.2154 129.341 10.9613C129.476 11.318 129.602 11.6782 129.726 12.0384L129.741 12.0796C129.855 12.0113 129.992 11.9779 130.143 11.9788C130.361 11.9815 130.579 11.9815 130.796 11.9806H131.154C131.603 11.9815 131.872 12.226 131.913 12.6703C131.94 12.9701 131.845 13.2575 131.645 13.4819C131.444 13.7089 131.164 13.8377 130.857 13.8456C130.815 13.8474 130.774 13.8474 130.733 13.8482C130.837 13.9447 130.943 14.0402 131.051 14.1331C131.486 14.5073 131.707 14.9876 131.708 15.5608C131.709 16.765 131.709 17.9683 131.707 19.1726C131.707 19.2874 131.695 19.4048 131.673 19.5187C131.601 19.9035 131.309 20.1498 130.912 20.1629C130.518 20.1761 130.124 20.1752 129.731 20.1638C129.326 20.1524 129.013 19.8711 128.951 19.4618C128.904 19.1506 128.915 18.8439 128.924 18.5477L128.925 18.5205C126.224 18.5222 123.547 18.5222 120.871 18.5205C120.879 18.7361 120.876 18.9386 120.872 19.1401L120.87 19.2865C120.863 19.8308 120.514 20.1708 119.96 20.1726C119.784 20.1734 119.608 20.1743 119.432 20.1743Z" fill="#003064"/>
                                            <path d="M113.707 23.2918H135.291V1.70725H113.707V23.2918ZM136 24H113V1H136V24Z" fill="#003064"/>
                                            <path d="M165 19.9822H163.223V19.1607H165V19.9822Z" fill="#999999"/>
                                            <path d="M147.787 19.9822H144.555V19.1607H147.787V19.9822ZM152.636 19.9822H149.404V19.1607H152.636V19.9822ZM157.485 19.9822H154.252V19.1607H157.485V19.9822ZM162.332 19.9822H159.1V19.1607H162.332V19.9822Z" fill="#999999"/>
                                            <path d="M143.666 19.9822H141.889V19.1607H143.666V19.9822Z" fill="#999999"/>
                                            <path d="M161.33 18.0546H160.482C160.507 17.9128 160.521 17.7693 160.521 17.625C160.521 16.1859 159.254 15.0152 157.696 15.0152H155.445V10.3282H159.893C159.954 10.3282 160.012 10.351 160.055 10.3898L160.771 11.0524H157.489C157.244 11.0524 157.044 11.2373 157.044 11.4643V13.3702C157.044 13.598 157.244 13.7829 157.489 13.7829H160.043C160.15 13.7829 160.254 13.8183 160.335 13.8833L161.468 14.7789V14.9443H160.885C160.693 14.9443 160.538 15.0886 160.538 15.2659V16.1757C160.538 16.3538 160.693 16.4982 160.885 16.4982H161.468V17.9271C161.468 17.998 161.406 18.0546 161.33 18.0546ZM159.39 18.0546C159.431 17.9178 159.453 17.7735 159.453 17.625C159.453 16.7303 158.665 16.001 157.696 16.001C157.264 16.001 156.867 16.1471 156.561 16.3868H155.445V15.4887H157.696C158.971 15.4887 160.008 16.4475 160.008 17.625C160.008 17.7693 159.993 17.9128 159.961 18.0546H159.39ZM157.696 18.7754C157.01 18.7754 156.451 18.2597 156.451 17.625C156.451 16.9911 157.01 16.4745 157.696 16.4745C158.383 16.4745 158.942 16.9911 158.942 17.625C158.942 18.2597 158.383 18.7754 157.696 18.7754ZM159.172 9.5956C159.223 9.61586 159.256 9.66228 159.256 9.71377V9.85473H155.445V8.11178L159.172 9.5956ZM161.401 11.6348C161.444 11.6736 161.468 11.7268 161.468 11.7834V14.156L160.669 13.5238C160.494 13.3862 160.272 13.3102 160.043 13.3102H157.555V11.5259H161.283L161.401 11.6348ZM161.468 16.0255H161.05V15.4178H161.468V16.0255ZM148.516 18.7754C147.83 18.7754 147.271 18.2597 147.271 17.625C147.271 16.9911 147.83 16.4745 148.516 16.4745C149.202 16.4745 149.761 16.9911 149.761 17.625C149.761 18.2597 149.202 18.7754 148.516 18.7754ZM146.027 17.6874V16.8603H146.967C146.834 17.0882 146.759 17.349 146.759 17.625C146.759 17.6469 146.76 17.6672 146.761 17.6874H146.027ZM161.763 11.2997L160.417 10.0548C160.277 9.92647 160.092 9.85473 159.893 9.85473H159.767V9.71377C159.767 9.47322 159.613 9.2563 159.375 9.16092L155.445 7.59607V7.04407C155.445 6.82631 155.254 6.64906 155.019 6.64906H147.81C147.668 6.64906 147.554 6.75541 147.554 6.88623C147.554 7.01706 147.668 7.12257 147.81 7.12257H154.934V16.3868H149.651C149.344 16.1471 148.949 16.001 148.516 16.001C148.084 16.001 147.687 16.1471 147.38 16.3868H145.019V7.12257H146.615C146.756 7.12257 146.871 7.01706 146.871 6.88623C146.871 6.75541 146.756 6.64906 146.615 6.64906H144.934C144.699 6.64906 144.508 6.82631 144.508 7.04407V16.4661C144.508 16.6839 144.699 16.8603 144.934 16.8603H145.515V17.7668C145.515 17.9837 145.707 18.1609 145.942 18.1609H146.858C147.097 18.794 147.75 19.2489 148.516 19.2489C149.282 19.2489 149.934 18.794 150.174 18.1609H153.741C153.883 18.1609 153.997 18.0546 153.997 17.9246C153.997 17.7929 153.883 17.6874 153.741 17.6874H150.271C150.272 17.6672 150.273 17.6469 150.273 17.625C150.273 17.349 150.197 17.0882 150.065 16.8603H156.147C156.014 17.0882 155.939 17.349 155.939 17.625C155.939 17.6469 155.94 17.6672 155.941 17.6874H154.936C154.794 17.6874 154.679 17.7929 154.679 17.9246C154.679 18.0546 154.794 18.1609 154.936 18.1609H156.038C156.278 18.794 156.93 19.2489 157.696 19.2489C158.304 19.2489 158.84 18.9619 159.156 18.5281H161.33C161.688 18.5281 161.979 18.258 161.979 17.9271V11.7834C161.979 11.601 161.903 11.428 161.763 11.2997Z" fill="#999999"/>
                                            <path d="M157.697 17.8667C157.553 17.8667 157.436 17.7587 157.436 17.6253C157.436 17.4919 157.553 17.3839 157.697 17.3839C157.842 17.3839 157.959 17.4919 157.959 17.6253C157.959 17.7587 157.842 17.8667 157.697 17.8667ZM157.697 16.9104C157.271 16.9104 156.924 17.2311 156.924 17.6253C156.924 18.0195 157.271 18.3402 157.697 18.3402C158.124 18.3402 158.471 18.0195 158.471 17.6253C158.471 17.2311 158.124 16.9104 157.697 16.9104Z" fill="#999999"/>
                                            <path d="M148.516 17.8667C148.371 17.8667 148.255 17.7587 148.255 17.6253C148.255 17.4919 148.371 17.3839 148.516 17.3839C148.66 17.3839 148.777 17.4919 148.777 17.6253C148.777 17.7587 148.66 17.8667 148.516 17.8667ZM148.516 16.9104C148.089 16.9104 147.742 17.2311 147.742 17.6253C147.742 18.0195 148.089 18.3402 148.516 18.3402C148.942 18.3402 149.289 18.0195 149.289 17.6253C149.289 17.2311 148.942 16.9104 148.516 16.9104Z" fill="#999999"/>
                                            <path d="M146.615 8.16019C146.474 8.16019 146.359 8.2657 146.359 8.39652V15.1126C146.359 15.2442 146.474 15.3497 146.615 15.3497C146.757 15.3497 146.871 15.2442 146.871 15.1126V8.39652C146.871 8.2657 146.757 8.16019 146.615 8.16019Z" fill="#999999"/>
                                            <path d="M149.113 15.1136V8.39676C149.113 8.26593 148.999 8.16043 148.857 8.16043C148.716 8.16043 148.602 8.26593 148.602 8.39676V15.1128C148.602 15.2436 148.716 15.3491 148.857 15.3491C148.999 15.3491 149.113 15.2436 149.113 15.1136Z" fill="#999999"/>
                                            <path d="M151.354 15.1136V8.39676C151.354 8.26593 151.24 8.16043 151.098 8.16043C150.957 8.16043 150.842 8.26593 150.842 8.39676V15.1128C150.842 15.2436 150.957 15.3491 151.098 15.3491C151.24 15.3491 151.354 15.2436 151.354 15.1136Z" fill="#999999"/>
                                            <path d="M153.596 15.1136V8.39676C153.596 8.26593 153.482 8.16043 153.341 8.16043C153.199 8.16043 153.084 8.26593 153.084 8.39676V15.1128C153.084 15.2436 153.199 15.3491 153.341 15.3491C153.482 15.3491 153.596 15.2436 153.596 15.1136Z" fill="#999999"/>
                                            <path d="M141.739 23.2765H164.261V1.69187H141.739V23.2765ZM165 23.9846H141V0.984619H165V23.9846Z" fill="#999999"/>
                                            <path d="M186.452 19.5354H185.101V14.2953L186.99 14.8202L188.667 15.2856L186.452 19.5354ZM181.009 15.2856L182.684 14.8202H182.686L184.576 14.2953V19.5354H183.221L181.009 15.2856ZM182.878 13.276H183.784H185.89H186.798V14.2225L184.908 13.6975C184.862 13.6844 184.814 13.6844 184.767 13.6975L182.878 14.2225V13.276ZM184.046 11.8728H185.627V12.7528H184.046V11.8728ZM189.118 14.8667L187.322 14.368V13.0148C187.322 12.8702 187.205 12.7528 187.06 12.7528H186.152V11.6108C186.152 11.4662 186.035 11.3487 185.89 11.3487H183.784C183.64 11.3487 183.522 11.4662 183.522 11.6108V12.7528H182.616C182.471 12.7528 182.354 12.8702 182.354 13.0148V14.368L180.557 14.8667C180.481 14.8877 180.419 14.9429 180.387 15.0157C180.356 15.0876 180.358 15.1708 180.395 15.2409L182.83 19.9184C182.875 20.0052 182.965 20.0604 183.063 20.0604H186.611C186.709 20.0604 186.798 20.0052 186.844 19.9184L189.281 15.2409C189.318 15.1708 189.321 15.0876 189.289 15.0157C189.257 14.9429 189.195 14.8877 189.118 14.8667Z" fill="#999999"/>
                                            <path d="M180.444 19.3018H174.184V18.4227H176.495H178.367H180.444V19.3018ZM176.757 8.80304V6.93274V4.92922L178.105 5.26577V6.93274V8.80304V17.8978H176.757V8.80304ZM175.428 8.54187L175.764 7.19479H176.233V8.54187H175.428ZM187.775 7.19479L188.281 8.54187H178.629V7.19479H187.775ZM180.706 17.8978H178.629V9.06597H188.659C188.744 9.06597 188.825 9.02303 188.874 8.95291C188.923 8.88192 188.935 8.79253 188.905 8.71189L188.203 6.83984C188.164 6.73817 188.067 6.67069 187.957 6.67069H178.629V5.06069C178.629 4.94062 178.546 4.83544 178.43 4.80652L176.559 4.33938C176.481 4.31923 176.398 4.33675 176.334 4.38671C176.27 4.43667 176.233 4.51292 176.233 4.59355V6.67069H175.559C175.439 6.67069 175.334 6.7522 175.305 6.86876L174.837 8.73994C174.818 8.81882 174.835 8.90208 174.885 8.96518C174.934 9.02829 175.011 9.06597 175.091 9.06597H176.233V17.8978H173.922C173.778 17.8978 173.66 18.0152 173.66 18.1607V19.5639C173.66 19.7093 173.778 19.8268 173.922 19.8268H180.706C180.85 19.8268 180.968 19.7093 180.968 19.5639V18.1607C180.968 18.0152 180.85 17.8978 180.706 17.8978Z" fill="#999999"/>
                                            <path d="M170.708 23.3225H192.292V1.73789H170.708V23.3225ZM193 24.0306H170V1.03064H193V24.0306Z" fill="#999999"/>
                                        </svg>

                                    </div>

                                <? } else if (str_contains($elementSectionName, 'Группа эксплуатации Г')) { ?>

                                    <h4>
                                        Группа эксплуатации Г
                                    </h4>
                                    <div class="icons">
                                        <svg style="width: 60%;height:auto" viewBox="0 0 193 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22.5032 20.7228H21.1465V20.0068H22.5032V20.7228Z" fill="#003064"/>
                                            <path d="M6.306 20.7228H3.33789V20.0068H6.306V20.7228ZM10.7586 20.7228H7.79049V20.0068H10.7586V20.7228ZM15.2112 20.7228H12.2431V20.0068H15.2112V20.7228ZM19.6629 20.7228H16.6948V20.0068H19.6629V20.7228Z" fill="#003064"/>
                                            <path d="M1.85475 20.7228H0.498047V20.0068H1.85475V20.7228Z" fill="#003064"/>
                                            <path d="M9.88463 10.9221C9.97741 10.9221 10.0483 10.9667 10.0772 10.9877C10.2514 11.119 10.2076 11.3238 10.1936 11.3912L10.1201 11.814C10.0527 12.2079 9.98529 12.6017 9.90301 12.9921C9.77785 13.5926 9.92927 14.0258 10.3949 14.3996C10.886 14.7926 11.3665 15.1996 11.8462 15.6066C12.0877 15.8114 12.3293 16.0163 12.5726 16.2202C12.6847 16.313 12.7608 16.4206 12.8107 16.5572L13.3989 18.1765C13.6388 18.8329 13.8777 19.4894 14.1149 20.1468C14.3976 20.0382 14.6725 19.9358 14.9491 19.8395C14.521 18.6693 14.1035 17.5165 13.686 16.3637L13.5127 15.8858C13.4996 15.8491 13.4838 15.8254 13.4558 15.8027L12.9394 15.3703C12.7013 15.1707 12.4641 14.9712 12.2225 14.7751C12.0457 14.6315 11.981 14.4495 12.0247 14.2193C12.0799 13.9287 12.1306 13.6372 12.1823 13.3466L12.5867 11.0831L13.0567 11.603C13.9486 12.5842 15.0138 13.1015 16.3093 13.1812C16.3058 12.881 16.3058 12.586 16.3101 12.2919C15.8226 12.2674 15.3587 12.144 14.9176 11.9243C14.1596 11.5453 13.6458 11.0621 13.3482 10.4494C13.2764 10.3006 13.2212 10.1439 13.1661 9.98812C13.1346 9.89447 13.1022 9.80169 13.0654 9.71066C12.893 9.28527 12.4904 8.9439 12.0168 8.82224C11.5801 8.70932 11.1521 8.79773 10.8116 9.07257C10.3547 9.44019 9.8715 9.78243 9.4041 10.1133C9.19666 10.2603 8.98921 10.4065 8.78352 10.5562C8.71087 10.6096 8.66098 10.6472 8.62684 10.6936C8.15681 11.3457 7.68415 11.9969 7.21062 12.6455C7.45395 12.8171 7.69291 12.9921 7.93011 13.1681C8.09204 12.944 8.25134 12.7252 8.40977 12.5072C8.63122 12.2026 8.85267 11.898 9.06974 11.5917C9.24917 11.3378 9.42948 11.1601 9.63868 11.0341C9.72796 10.9492 9.81286 10.9221 9.88463 10.9221ZM14.0099 20.7314C13.9635 20.7314 13.9145 20.7227 13.8637 20.6999C13.7202 20.6352 13.6685 20.4934 13.6501 20.44C13.3998 19.7459 13.1468 19.0535 12.8948 18.3603L12.3057 16.7401C12.2882 16.6946 12.2672 16.6648 12.2287 16.6316C11.9845 16.4276 11.7411 16.2219 11.4987 16.0163C11.0225 15.6119 10.5464 15.2084 10.0588 14.818C9.43298 14.3164 9.20978 13.6827 9.37784 12.8818C9.45224 12.5273 9.51439 12.1693 9.57566 11.8114C9.55377 11.8394 9.53102 11.87 9.50826 11.9015C9.28944 12.2114 9.06711 12.516 8.84391 12.8223C8.65748 13.0788 8.47104 13.3352 8.28635 13.5934C8.25572 13.6372 8.17344 13.751 8.02377 13.7746C7.87322 13.7965 7.76205 13.7134 7.72004 13.6819C7.40931 13.4499 7.09596 13.2197 6.77648 12.9974C6.73972 12.972 6.6268 12.8932 6.6023 12.7453C6.57779 12.6 6.65656 12.4923 6.6942 12.4407C7.19487 11.7553 7.69466 11.0691 8.19095 10.3803C8.2776 10.2595 8.38264 10.1824 8.46754 10.1212C8.67498 9.97062 8.88418 9.82269 9.09337 9.67477C9.55465 9.34829 10.0308 9.0113 10.4737 8.65418C10.9499 8.2708 11.5442 8.14476 12.1516 8.30144C12.7871 8.46599 13.328 8.92902 13.5626 9.50847C13.6029 9.60825 13.6388 9.70978 13.6738 9.81132C13.7219 9.94874 13.7683 10.0853 13.8313 10.2148C14.079 10.7242 14.5 11.1146 15.1583 11.4437C15.567 11.6477 15.9985 11.7536 16.4388 11.758C16.4931 11.7588 16.6375 11.7606 16.7452 11.8709C16.8169 11.9444 16.8519 12.0451 16.8493 12.1711C16.8414 12.5545 16.8423 12.9387 16.8493 13.3212C16.8502 13.3694 16.8528 13.5155 16.7425 13.6241C16.6314 13.7326 16.4878 13.7265 16.4274 13.7247C15.0655 13.6748 13.8891 13.1742 12.9245 12.2394L12.7109 13.4385C12.6593 13.7326 12.6085 14.0267 12.5525 14.3199C12.8055 14.5554 13.0444 14.7576 13.2843 14.9589L13.7981 15.3887C13.9005 15.4727 13.9722 15.5751 14.0178 15.7029L14.1911 16.1808C14.6261 17.3791 15.0593 18.5774 15.4979 19.7739C15.5189 19.8316 15.5688 19.9656 15.5022 20.103C15.4375 20.2413 15.3001 20.2877 15.2493 20.3052C14.8939 20.4251 14.5429 20.5573 14.1937 20.6921C14.1447 20.7113 14.0808 20.7314 14.0099 20.7314Z" fill="#003064"/>
                                            <path d="M7.59074 19.2423C7.82357 19.4331 8.05027 19.623 8.27347 19.8147L8.44765 19.6038C8.84766 19.1162 9.24854 18.6287 9.66781 18.1587C10.2 17.5608 10.5483 17.0173 10.7628 16.4475C10.7777 16.4089 10.7882 16.3801 10.7969 16.3599C10.7821 16.3468 10.7637 16.3328 10.7435 16.3153L9.97854 15.6746L9.62229 16.7188C9.58028 16.8431 9.52076 16.9499 9.43848 17.047L8.71987 17.9022C8.34437 18.3486 7.96887 18.7959 7.59074 19.2423ZM8.29623 20.4257C8.15706 20.4257 8.05815 20.3399 8.01964 20.3057C7.72991 20.0545 7.43582 19.8077 7.13822 19.567C7.09796 19.5346 6.99117 19.448 6.97804 19.2992C6.96404 19.1512 7.05332 19.0453 7.08745 19.0051C7.49621 18.5237 7.90235 18.0396 8.30936 17.5556L9.02797 16.7022C9.06561 16.6567 9.09274 16.6068 9.11287 16.5472L9.7247 14.7608L11.0893 15.9048C11.3729 16.1437 11.407 16.2663 11.281 16.5954C11.0271 17.2702 10.6464 17.8672 10.0687 18.5158C9.6538 18.9806 9.25817 19.4628 8.86254 19.9451L8.60608 20.2567C8.57107 20.2996 8.47829 20.4117 8.32424 20.4248C8.31461 20.4257 8.30498 20.4257 8.29623 20.4257Z" fill="#003064"/>
                                            <path d="M12.6434 7.8816C12.9226 7.89998 13.2088 7.8002 13.4355 7.60063C13.6692 7.39318 13.8145 7.09734 13.832 6.79011C13.867 6.19754 13.3751 5.62597 12.7992 5.59009C12.4631 5.56733 12.1462 5.67411 11.9064 5.88681C11.6788 6.08813 11.544 6.36997 11.5274 6.67982C11.4933 7.31266 11.9834 7.83958 12.6434 7.8816ZM12.7099 8.42078C12.6758 8.42078 12.6434 8.4199 12.6093 8.41815C11.6508 8.35776 10.9401 7.5805 10.9908 6.65006C11.0162 6.19666 11.214 5.78352 11.5501 5.48505C11.8976 5.17607 12.3563 5.0229 12.8333 5.05353C13.7034 5.10868 14.4202 5.93407 14.3686 6.82074C14.3423 7.27064 14.1314 7.70129 13.7909 8.00326C13.4837 8.2746 13.1029 8.42078 12.7099 8.42078Z" fill="#003064"/>
                                            <path d="M0.70811 23.2919H22.2919V1.70722H0.70811V23.2919ZM23 24H0V1H23V24Z" fill="#003064"/>
                                            <path d="M52.0004 20.5927H50.2227V19.7408H52.0004V20.5927Z" fill="#003064"/>
                                            <path d="M34.6255 20.5927H31.5547V19.7408H34.6255V20.5927ZM39.2312 20.5927H36.1604V19.7408H39.2312V20.5927ZM43.8378 20.5927H40.7671V19.7408H43.8378V20.5927ZM48.4436 20.5927H45.3728V19.7408H48.4436V20.5927Z" fill="#003064"/>
                                            <path d="M29.7776 20.5927H28.8887V19.7408H29.7776V20.5927Z" fill="#003064"/>
                                            <path d="M41.1368 16.5908C41.1368 16.3007 40.8816 16.0579 40.5771 16.0562C40.2625 16.0544 40.0027 16.3025 40.0073 16.5987C40.0128 16.8888 40.2743 17.1343 40.5752 17.1308C40.8798 17.1273 41.1368 16.881 41.1368 16.5908ZM38.3116 10.0697C38.0464 10.0697 37.7811 10.068 37.5159 10.0706C37.3019 10.0724 37.1793 10.1723 37.1784 10.3379C37.1775 10.5036 37.3019 10.6088 37.5141 10.6096C38.0391 10.6132 38.564 10.6123 39.089 10.6096C39.2985 10.6088 39.432 10.5001 39.4329 10.3397C39.4338 10.1802 39.2994 10.0724 39.0909 10.0706C38.8311 10.068 38.5714 10.0697 38.3116 10.0697ZM36.0434 16.3147H38.1689C38.2284 16.3147 38.2915 16.3305 38.275 16.2306C38.1753 15.6267 37.9119 15.1034 37.4583 14.6704C37.3998 14.6135 37.3705 14.6135 37.3211 14.6775C36.9031 15.2174 36.4824 15.7538 36.0434 16.3147ZM38.768 12.8307C38.436 13.2558 38.125 13.6607 37.8049 14.0595C37.7455 14.1349 37.7436 14.1726 37.8168 14.2392C38.3162 14.6985 38.639 15.2516 38.7936 15.8993C38.8274 16.0369 38.7671 16.235 38.907 16.2998C39.0323 16.3586 39.2116 16.3104 39.3661 16.3165C39.443 16.3182 39.486 16.2998 39.5107 16.2236C39.5701 16.0439 39.678 15.8879 39.8335 15.7713C39.9085 15.7161 39.8994 15.6653 39.8719 15.5978C39.7832 15.3813 39.6963 15.1648 39.6094 14.9483C39.3314 14.2488 39.0534 13.5494 38.768 12.8307ZM39.2829 12.5248C39.2829 12.5449 39.2783 12.5616 39.2829 12.5756C39.6597 13.5257 40.0375 14.4767 40.4143 15.4269C40.4381 15.4856 40.4609 15.5136 40.5442 15.5075C40.6566 15.4978 40.7783 15.5005 40.8844 15.5504C40.9841 15.5969 41.0298 15.5496 41.0838 15.4794C41.8145 14.5425 42.548 13.6073 43.2797 12.6694C43.3136 12.6273 43.3712 12.5923 43.3675 12.5248H39.2829ZM35.4215 13.8851C35.2715 13.8772 35.0776 13.893 34.8892 13.9333C33.3289 14.2655 32.4161 15.6845 32.685 17.0896C32.9594 18.5244 34.4584 19.5376 35.9455 19.2668C37.1629 19.045 38.0336 18.1957 38.2604 17.0265C38.2906 16.8704 38.2403 16.8643 38.1104 16.8643C37.269 16.8687 36.4266 16.8669 35.5852 16.8669C35.4379 16.8669 35.2916 16.8669 35.2139 16.7162C35.1389 16.5689 35.2111 16.455 35.3017 16.3393C35.8147 15.688 36.3233 15.0333 36.8373 14.3821C36.9059 14.2953 36.915 14.2611 36.8007 14.2033C36.3809 13.9947 35.9391 13.8807 35.4215 13.8851ZM42.8489 16.5391C42.8563 16.8126 42.8755 17.0387 42.9349 17.2614C43.2971 18.6208 44.7202 19.5104 46.1433 19.2694C47.7293 19.0012 48.7399 17.562 48.4591 16.0921C48.1783 14.617 46.6272 13.6151 45.1007 13.9403C44.9562 13.971 44.9534 14.0061 44.9992 14.12C45.3056 14.8773 45.6065 15.6363 45.9083 16.3945C45.9339 16.4585 45.9604 16.5216 45.9595 16.5935C45.9568 16.7276 45.8845 16.817 45.7565 16.8512C45.6293 16.8845 45.5132 16.852 45.4391 16.739C45.4071 16.6899 45.3851 16.6347 45.3632 16.5803C45.0623 15.8222 44.7586 15.064 44.4641 14.3032C44.4184 14.1831 44.3827 14.1787 44.2757 14.241C43.3566 14.7765 42.8892 15.5601 42.8489 16.5391ZM49.082 17.0133C48.9896 17.2368 48.9749 17.4805 48.8807 17.704C48.3786 18.8951 47.4805 19.6261 46.1479 19.8163C44.3763 20.0679 42.7108 18.9398 42.3395 17.264C42.0377 15.8984 42.7026 14.4767 43.9703 13.779C44.1989 13.6528 44.1989 13.6528 44.102 13.4083C44.0306 13.2251 43.9593 13.0428 43.887 12.8587C43.8321 12.857 43.8184 12.9034 43.7946 12.9341C43.041 13.8974 42.2883 14.8598 41.5328 15.8213C41.4789 15.8896 41.4716 15.9317 41.5246 16.0124C41.7734 16.391 41.7789 16.7854 41.5328 17.1641C41.2905 17.5383 40.9201 17.7145 40.4646 17.6742C40.0155 17.6339 39.6963 17.3928 39.5244 16.994C39.4786 16.8897 39.4283 16.8582 39.3195 16.8652C39.1768 16.8757 39.0076 16.8284 38.896 16.8871C38.7808 16.9467 38.8366 17.1273 38.8037 17.2526C38.4378 18.6287 37.547 19.5131 36.0919 19.7944C34.4803 20.1056 32.9027 19.2589 32.3054 17.7986C31.6707 16.2446 32.2698 14.6038 33.7779 13.7659C34.8251 13.1839 35.9163 13.169 37.0056 13.6905C37.2717 13.8185 37.269 13.8255 37.4519 13.5915C37.7839 13.1673 38.1131 12.7404 38.4497 12.3206C38.5082 12.2469 38.5174 12.19 38.4817 12.1067C38.3647 11.8271 38.2549 11.5457 38.147 11.2635C38.1177 11.1855 38.0839 11.1461 37.9842 11.1513C37.7967 11.1618 37.6083 11.1566 37.4199 11.1522C36.9653 11.1399 36.5931 10.7718 36.5931 10.3379C36.594 9.91021 36.9589 9.53594 37.4043 9.52893C38.0025 9.52017 38.6006 9.52017 39.1988 9.52893C39.6561 9.53594 40.0119 9.90845 40.0064 10.3528C40.0018 10.7832 39.6378 11.139 39.1878 11.1522C39.0662 11.1557 38.9436 11.1575 38.822 11.1531C38.7406 11.1504 38.7268 11.1785 38.7543 11.2451C38.8403 11.4572 38.9281 11.6676 39.0067 11.8814C39.0369 11.963 39.0918 11.9638 39.1613 11.9638C40.5688 11.963 41.9755 11.9621 43.3831 11.9656C43.523 11.9665 43.5175 11.9261 43.4745 11.8218C43.3309 11.4739 43.1956 11.1224 43.0593 10.7709C42.9541 10.5027 43.0739 10.3344 43.3721 10.33C43.5715 10.3274 43.7709 10.3344 43.9703 10.3265C44.3516 10.3108 44.6242 10.1293 44.7943 9.80327C44.8291 9.73754 44.8538 9.66216 44.905 9.60957C45.0019 9.51228 45.1263 9.49212 45.2525 9.56136C45.3879 9.63586 45.4199 9.75682 45.3797 9.89092C45.2324 10.3826 44.6782 10.8165 44.1413 10.8682C44.0096 10.8796 43.8788 10.9006 43.7206 10.8884C43.8559 11.2285 43.9831 11.5501 44.1111 11.8727C44.305 12.3609 44.5007 12.8473 44.6919 13.3373C44.7257 13.4258 44.7559 13.4556 44.8666 13.4284C46.5019 13.0261 48.1427 13.8702 48.7865 15.2586C48.9192 15.5452 49.0134 15.8423 49.049 16.1543C49.0518 16.1789 49.0454 16.2078 49.082 16.2175V17.0133Z" fill="#003064"/>
                                            <path d="M28.738 23.2919H51.2602V1.70722H28.738V23.2919ZM52 24H28V1H52V24Z" fill="#003064"/>
                                            <path d="M71.4125 10.143C71.4125 9.38928 71.4116 8.64257 71.4142 7.89672C71.4142 7.81083 71.3949 7.78191 71.3047 7.78366C70.9795 7.78805 70.6535 7.78805 70.3274 7.78366C70.2433 7.78279 70.2275 7.81171 70.2284 7.88796C70.2319 8.22714 70.2319 8.56807 70.2275 8.90812C70.2275 8.97736 70.2547 9.02118 70.2994 9.06588C70.6412 9.40506 70.9813 9.74687 71.3231 10.0869C71.345 10.108 71.3599 10.143 71.4125 10.143ZM65.2783 17.3552C65.2783 18.0449 65.281 18.7347 65.2757 19.4244C65.2748 19.5331 65.3002 19.5646 65.4142 19.5646C66.302 19.5594 67.1907 19.5594 68.0785 19.5638C68.1872 19.5646 68.2188 19.541 68.2188 19.427C68.2144 18.0519 68.2144 16.6768 68.2179 15.3026C68.2179 15.1956 68.1968 15.1615 68.082 15.1623C67.1933 15.1667 66.3055 15.1658 65.4168 15.1623C65.3099 15.1623 65.2739 15.1834 65.2757 15.299C65.281 15.9844 65.2783 16.6689 65.2783 17.3552ZM73.3345 12.8889C73.302 12.8547 73.274 12.8231 73.2451 12.7933C71.1092 10.6584 68.9742 8.52337 66.841 6.38576C66.7604 6.30425 66.7201 6.31301 66.6456 6.38839C66.0163 7.02292 65.3835 7.65308 64.7525 8.28498C63.252 9.78543 61.7525 11.2868 60.2485 12.7837C60.1556 12.8748 60.1652 12.9195 60.2529 12.9993C60.3966 13.1281 60.5351 13.2657 60.6639 13.4103C60.7472 13.5032 60.7928 13.5059 60.8857 13.4121C62.7498 11.5418 64.6193 9.675 66.4869 7.80733C66.6789 7.61539 66.8095 7.61539 67.0031 7.80908C67.7613 8.56719 68.5194 9.3253 69.2784 10.0852C70.3949 11.2 71.5115 12.3157 72.6254 13.434C72.6903 13.4989 72.7254 13.5164 72.7955 13.4384C72.9453 13.2736 73.1075 13.122 73.2644 12.9651C73.2871 12.9415 73.3082 12.9169 73.3345 12.8889ZM71.4125 16.2859C71.4125 15.2368 71.4116 14.1877 71.4133 13.1387C71.4133 13.0484 71.3941 12.98 71.3266 12.9134C69.8279 11.4182 68.3309 9.92128 66.8366 8.42083C66.7586 8.34283 66.7227 8.35159 66.6499 8.42433C65.1583 9.9204 63.6648 11.4138 62.1688 12.9055C62.1074 12.9686 62.082 13.0282 62.082 13.1159C62.0846 15.2193 62.0846 17.3218 62.0811 19.4253C62.0811 19.5331 62.1057 19.5655 62.2178 19.5646C63.0075 19.5585 63.7963 19.5585 64.5859 19.5646C64.7008 19.5655 64.7332 19.5427 64.7332 19.4209C64.7279 17.9467 64.7297 16.4726 64.7297 14.9976C64.7297 14.6855 64.8077 14.6084 65.1258 14.6084H68.3581C68.6859 14.6084 68.763 14.6829 68.763 15.0046C68.763 16.4743 68.7639 17.9441 68.7604 19.4139C68.7595 19.5252 68.777 19.5655 68.9015 19.5646C69.6955 19.5576 70.4896 19.5594 71.2836 19.5638C71.3897 19.5646 71.4151 19.5401 71.4151 19.4332C71.4107 18.3841 71.4125 17.335 71.4125 16.2859ZM61.7498 20.1036C61.5745 20.0432 61.5255 19.9695 61.5255 19.7627C61.5255 17.7539 61.5255 15.746 61.5255 13.7373V13.5471C61.344 13.7302 61.188 13.8889 61.0294 14.0475C60.8445 14.2316 60.7095 14.2316 60.5237 14.0458C60.2161 13.739 59.9084 13.4323 59.6017 13.1229C59.4317 12.9528 59.4325 12.8205 59.6026 12.6496C61.9014 10.3499 64.2003 8.05097 66.5001 5.7521C66.6754 5.57769 66.8156 5.57944 66.9961 5.75999C67.8445 6.60925 68.6938 7.45763 69.5422 8.30602C69.5772 8.34195 69.6096 8.37964 69.6692 8.44449C69.6692 8.13073 69.6684 7.85904 69.6692 7.58734C69.6701 7.32617 69.7692 7.22538 70.0268 7.22538C70.5614 7.22538 71.0952 7.2245 71.6289 7.22538C71.8629 7.22625 71.962 7.32704 71.9629 7.5628C71.9629 8.57333 71.9637 9.58473 71.9611 10.5961C71.9602 10.6873 71.9839 10.7513 72.0505 10.8179C72.6412 11.4024 73.2284 11.9914 73.8156 12.5804C73.8805 12.6452 73.9418 12.7153 74.0049 12.7828V12.9809C73.9909 12.9993 73.9795 13.0186 73.9637 13.0352C73.6009 13.3998 73.2407 13.7671 72.8726 14.1264C72.7788 14.2175 72.6289 14.2026 72.5247 14.1097C72.4396 14.0344 72.3625 13.9511 72.2819 13.8713C72.182 13.7714 72.0821 13.6715 71.9629 13.5532V13.7443V19.7802C71.9629 19.9748 71.9111 20.0475 71.7376 20.1036C71.7052 20.1028 71.6719 20.1001 71.6395 20.1001H61.9619C61.8909 20.1001 61.8199 20.1019 61.7498 20.1036Z" fill="#003064"/>
                                            <path d="M71.7378 20.1041C71.7343 20.1129 71.7343 20.1208 71.7352 20.1287H61.7518C61.7544 20.1208 61.7535 20.112 61.75 20.1041C61.8201 20.1024 61.8911 20.1006 61.9621 20.1006H71.6388C71.6721 20.1006 71.7045 20.1032 71.7378 20.1041Z" fill="#003064"/>
                                            <path d="M76.8498 18.143C76.6386 18.377 76.3573 18.5295 76.0505 18.5804L76.0129 17.9055L76.5291 17.3902C76.5676 17.3516 76.5676 17.2876 76.5291 17.2491C76.4896 17.2105 76.4265 17.2105 76.388 17.2491L75.998 17.6391L75.9734 17.2017L76.21 16.9651C76.2495 16.9265 76.2495 16.8634 76.21 16.8249C76.1715 16.7854 76.1084 16.7854 76.0698 16.8249L75.9585 16.9353L75.9208 16.2526C75.9182 16.2 75.8744 16.1588 75.8218 16.1588C75.7692 16.1588 75.7254 16.2 75.7228 16.2526L75.6623 17.3376L75.2556 16.9309C75.2162 16.8915 75.1531 16.8915 75.1145 16.9309C75.0759 16.9695 75.0759 17.0326 75.1145 17.0712L75.6474 17.604L75.593 18.5804C74.9278 18.4708 74.4353 17.8959 74.4353 17.2131C74.4353 16.6286 74.6027 15.8643 74.8621 15.2666C75.1461 14.611 75.4958 14.235 75.8218 14.235C76.1469 14.235 76.4966 14.611 76.7806 15.2666C77.04 15.8643 77.2074 16.6286 77.2074 17.2131C77.2074 17.5576 77.0803 17.888 76.8498 18.143ZM75.7149 19.9792L75.8218 18.0554L75.9287 19.9792H75.7149ZM76.9638 15.1877C76.6413 14.4445 76.2363 14.0361 75.8218 14.0361C75.4072 14.0361 75.0015 14.4445 74.6798 15.1877C74.4064 15.8187 74.2363 16.5944 74.2363 17.2131C74.2363 18.001 74.8104 18.6636 75.5816 18.7811L75.515 19.9792H75.3976C75.3424 19.9792 75.2977 20.0238 75.2977 20.0791C75.2977 20.1334 75.3424 20.1781 75.3976 20.1781H76.246C76.3012 20.1781 76.3459 20.1334 76.3459 20.0791C76.3459 20.0238 76.3012 19.9792 76.246 19.9792H76.1285L76.0611 18.7811C76.4213 18.7259 76.7508 18.5488 76.9971 18.2762C77.2618 17.9844 77.4064 17.6075 77.4064 17.2131C77.4064 16.5944 77.2372 15.8187 76.9638 15.1877Z" fill="#003064"/>
                                            <path d="M75.8213 14.3251C75.5374 14.3251 75.2096 14.6906 74.944 15.3024C74.689 15.8896 74.5251 16.6407 74.5251 17.2139C74.5251 17.8168 74.9335 18.3313 75.5093 18.4724L75.5549 17.6389L75.051 17.135C74.9765 17.0614 74.9765 16.9413 75.051 16.8677C75.1246 16.7932 75.2447 16.7932 75.3183 16.8677L75.5838 17.1323L75.632 16.248C75.6382 16.1481 75.7206 16.0692 75.8213 16.0692C75.9221 16.0692 76.0045 16.1481 76.0107 16.248L76.0369 16.7362C76.1097 16.6889 76.2096 16.6976 76.2736 16.7616C76.3086 16.7967 76.3288 16.8449 76.3288 16.8957C76.3288 16.9457 76.3086 16.993 76.2736 17.0289L76.065 17.2366L76.0764 17.4347L76.3244 17.1867C76.3954 17.1148 76.5199 17.1148 76.5917 17.1867C76.6277 17.2217 76.6469 17.2691 76.6469 17.3199C76.6469 17.3707 76.6277 17.4172 76.5917 17.4531L76.1044 17.9413L76.1334 18.4715C76.3823 18.4102 76.6101 18.2743 76.7828 18.0833C76.9993 17.8449 77.1176 17.5355 77.1176 17.2139C77.1176 16.6407 76.9528 15.8896 76.6978 15.3024C76.4331 14.6906 76.1053 14.3251 75.8213 14.3251ZM75.6767 18.6863L75.5777 18.6696C74.8634 18.5513 74.3454 17.9387 74.3454 17.2139C74.3454 16.617 74.5163 15.8396 74.7801 15.2305C75.0825 14.5311 75.4532 14.1455 75.8213 14.1455C76.1894 14.1455 76.5593 14.5311 76.8625 15.2305C77.1263 15.8396 77.2972 16.617 77.2972 17.2139C77.2972 17.5802 77.1623 17.9316 76.916 18.2033C76.6934 18.4496 76.391 18.6153 76.065 18.6687L75.966 18.6863L75.9204 17.8712L76.4646 17.3269L75.9195 17.8449L75.8818 17.1674L76.1465 16.9018L76.022 16.9991L75.8801 17.1411L75.831 16.2577L75.7398 17.5434L75.1912 16.9947L75.739 17.5688L75.6767 18.6863ZM75.8091 19.8905H75.8336L75.8213 19.6679L75.8091 19.8905ZM76.0229 20.0693H75.6189L75.7319 18.0508H75.9107L76.0229 20.0693ZM75.3971 20.0693L76.2455 20.0885L76.1281 20.0693H76.0431L75.9677 18.7047L76.0475 18.6915C76.3866 18.6407 76.6995 18.4715 76.93 18.2165C77.1798 17.9413 77.3174 17.5855 77.3174 17.2139C77.3174 16.6065 77.15 15.8431 76.8809 15.2226C76.5742 14.5162 76.1982 14.1262 75.8213 14.1262C75.4445 14.1262 75.0685 14.5162 74.7617 15.2226C74.4927 15.8431 74.3262 16.6065 74.3262 17.2139C74.3262 17.9588 74.8599 18.5802 75.5952 18.6915L75.6759 18.7038L75.5996 20.0693H75.3971ZM76.2455 20.2673H75.3971C75.292 20.2673 75.2078 20.1832 75.2078 20.0789C75.2078 19.9746 75.292 19.8905 75.3971 19.8905H75.4296L75.4874 18.8554C74.7056 18.6985 74.1465 18.021 74.1465 17.2139C74.1465 16.5828 74.3191 15.7932 74.597 15.1516C74.9344 14.3751 75.3691 13.9465 75.8213 13.9465C76.2736 13.9465 76.7083 14.3751 77.0457 15.1516C77.3235 15.7932 77.4962 16.5828 77.4962 17.2139C77.4962 17.6302 77.3428 18.0289 77.0632 18.3366C76.824 18.6021 76.5032 18.7844 76.1553 18.8554L76.2122 19.8905H76.2455C76.3498 19.8905 76.4348 19.9746 76.4348 20.0789C76.4348 20.1832 76.3498 20.2673 76.2455 20.2673Z" fill="#003064"/>
                                            <path d="M57.7081 23.2919H79.2919V1.70722H57.7081V23.2919ZM80 24H57V1H80V24Z" fill="#003064"/>
                                            <path d="M89.6728 17.1942C90.0465 16.9273 90.2548 16.6139 90.2006 16.1701C90.1375 15.6537 89.8303 15.2642 89.5152 14.88C89.4697 14.8248 89.4469 14.8808 89.4242 14.908C89.1703 15.2064 88.9445 15.5224 88.8158 15.8988C88.6557 16.3697 88.7817 16.9001 89.2859 17.1645C89.2859 17.0165 89.2868 16.8712 89.2859 16.7268C89.2859 16.5999 89.3506 16.5211 89.4714 16.5176C89.6027 16.5132 89.6728 16.5929 89.6728 16.7268C89.6728 16.8721 89.6728 17.0165 89.6728 17.1942ZM96.4519 19.7711H103.879C104.03 19.7702 104.238 19.8201 104.316 19.7431C104.393 19.6669 104.33 19.4569 104.347 19.3072C104.366 19.1417 104.316 19.0971 104.143 19.0971C99.1215 19.1032 94.1 19.1015 89.0784 19.1032C88.9218 19.1032 88.7082 19.0358 88.6242 19.1312C88.5454 19.2214 88.6093 19.4306 88.5926 19.5855C88.5769 19.7369 88.6242 19.7755 88.7791 19.7755C91.3367 19.7693 93.8943 19.7711 96.4519 19.7711ZM92.682 13.1495C92.682 14.9421 92.6838 16.7356 92.6794 18.5273C92.6785 18.6603 92.7056 18.7032 92.8474 18.7015C93.6361 18.6936 94.4238 18.6927 95.2125 18.7023C95.3744 18.7041 95.4234 18.6735 95.4225 18.4984C95.4164 15.7946 95.4182 13.0917 95.4182 10.3871C95.4182 10.1516 95.4689 10.1009 95.7035 10.1009C96.2007 10.1 96.697 10.0956 97.1933 10.1026C97.3167 10.1052 97.3473 10.0702 97.3464 9.94943C97.3403 9.22557 97.3377 8.50083 97.3482 7.77696C97.3508 7.61153 97.2948 7.5914 97.1512 7.5914C95.721 7.59665 94.2899 7.59665 92.8597 7.5914C92.7153 7.59052 92.6776 7.62291 92.6785 7.76996C92.6838 9.56343 92.682 11.356 92.682 13.1495ZM100.825 14.6025C100.825 13.2887 100.823 11.974 100.828 10.6602C100.828 10.5236 100.795 10.4869 100.656 10.4869C99.0909 10.493 97.5259 10.493 95.9609 10.4869C95.8226 10.486 95.8015 10.5289 95.8015 10.6532C95.805 13.2817 95.8059 15.9102 95.8015 18.5378C95.8007 18.6682 95.8296 18.7015 95.9626 18.7015C97.5276 18.6962 99.0926 18.6953 100.658 18.7015C100.798 18.7015 100.828 18.663 100.828 18.5273C100.823 17.2196 100.825 15.911 100.825 14.6025ZM103.548 12.0029C103.548 9.82514 103.547 7.64742 103.55 5.46882C103.55 5.36203 103.547 5.30339 103.407 5.30426C101.948 5.30951 100.488 5.30864 99.0279 5.30514C98.9132 5.30426 98.8817 5.33402 98.8826 5.45044C98.8931 6.95069 98.8283 8.45181 98.8808 9.95206C98.8852 10.0755 98.915 10.1044 99.0357 10.1026C99.6493 10.0965 100.262 10.1009 100.876 10.1009C101.188 10.1009 101.23 10.1411 101.23 10.4492C101.23 13.1346 101.231 15.8218 101.227 18.5072C101.227 18.6516 101.246 18.7059 101.41 18.7023C102.064 18.691 102.719 18.6927 103.373 18.7015C103.52 18.7041 103.551 18.6647 103.551 18.5212C103.546 16.3478 103.548 14.1762 103.548 12.0029ZM96.4711 20.158H88.5174C88.2452 20.158 88.2014 20.1151 88.2014 19.8472C88.2014 19.5558 88.2005 19.2634 88.2023 18.9711C88.2031 18.7671 88.2679 18.7015 88.4745 18.6988C88.6959 18.6953 88.9183 18.6927 89.1397 18.6997C89.2447 18.7032 89.2946 18.6875 89.2894 18.5614C89.2789 18.2997 89.2824 18.0363 89.2885 17.7737C89.2903 17.6861 89.2614 17.6397 89.1861 17.5969C88.976 17.4796 88.7861 17.336 88.6277 17.1522C88.2924 16.7627 88.2294 16.3207 88.3887 15.8445C88.5769 15.2843 88.9428 14.8388 89.3375 14.4143C89.4163 14.3311 89.5222 14.3119 89.6019 14.3968C90.0465 14.8703 90.4605 15.3666 90.6163 16.0205C90.7354 16.522 90.5734 16.9395 90.2058 17.2844C90.1122 17.371 90.0141 17.4656 89.9012 17.5163C89.706 17.6047 89.65 17.736 89.6693 17.9408C89.6876 18.1369 89.6815 18.3374 89.671 18.5352C89.664 18.6612 89.692 18.7032 89.8294 18.7015C90.5944 18.6936 91.3594 18.6936 92.1244 18.7015C92.2627 18.7032 92.2838 18.656 92.2829 18.5325C92.2785 17.399 92.2802 16.2664 92.2802 15.1338C92.2802 12.6112 92.2802 10.0886 92.2802 7.56602C92.2802 7.22815 92.3144 7.19402 92.6496 7.19402H97.4147C97.679 7.19402 97.7307 7.24566 97.7307 7.50912C97.7307 8.31964 97.7342 9.13191 97.7272 9.94418C97.7263 10.0781 97.7622 10.1131 97.8909 10.1026C98.0423 10.0912 98.1946 10.093 98.346 10.1017C98.4607 10.1096 98.4913 10.0746 98.4896 9.95906C98.4616 8.42905 98.4799 6.89904 98.4878 5.36903C98.4904 4.91476 98.4904 4.91476 98.9438 4.91476H103.621C103.893 4.91476 103.934 4.9559 103.934 5.22724C103.934 9.65446 103.935 14.0799 103.93 18.5072C103.93 18.6717 103.977 18.7155 104.131 18.7006C104.252 18.6875 104.376 18.6936 104.498 18.6988C104.67 18.7067 104.737 18.7776 104.737 18.9518C104.74 19.2669 104.739 19.5829 104.737 19.898C104.737 20.0949 104.674 20.1571 104.477 20.158H100.938H96.4711Z" fill="#003064"/>
                                            <path d="M100.404 6.7584C100.404 6.44942 100.404 6.45643 100.095 6.44855C99.9669 6.44417 99.9144 6.47218 99.9328 6.60785C99.945 6.69976 99.9442 6.79516 99.9328 6.88707C99.9153 7.02624 99.9652 7.063 100.103 7.05863C100.404 7.04812 100.404 7.056 100.404 6.7584ZM100.809 6.75753C100.809 6.9037 100.812 7.04987 100.809 7.19517C100.805 7.37898 100.737 7.45513 100.559 7.45864C100.296 7.46389 100.033 7.46214 99.7709 7.45951C99.6159 7.45776 99.5337 7.37461 99.5328 7.21881C99.531 6.90458 99.5302 6.58947 99.5337 6.27437C99.5354 6.12732 99.6177 6.04767 99.7647 6.04767C100.033 6.04504 100.301 6.04241 100.569 6.04767C100.737 6.05029 100.805 6.12819 100.809 6.30238C100.812 6.45468 100.809 6.6061 100.809 6.75753Z" fill="#003064"/>
                                            <path d="M102.1 6.75322C102.1 7.0552 102.1 7.04732 102.393 7.0587C102.536 7.06395 102.592 7.02981 102.571 6.88102C102.559 6.78998 102.559 6.69545 102.571 6.60355C102.591 6.46613 102.532 6.44337 102.409 6.446C102.1 6.45387 102.1 6.44775 102.1 6.75322ZM102.972 6.74184C102.972 6.89414 102.976 7.04557 102.971 7.197C102.967 7.38606 102.903 7.45521 102.72 7.45871C102.464 7.46396 102.207 7.46396 101.951 7.45871C101.77 7.45608 101.698 7.38081 101.696 7.2005C101.693 6.9029 101.693 6.6053 101.695 6.3077C101.697 6.12389 101.768 6.04949 101.947 6.04686C102.204 6.04249 102.461 6.04249 102.718 6.04686C102.901 6.04949 102.966 6.11776 102.971 6.3042C102.976 6.45037 102.972 6.59655 102.972 6.74184Z" fill="#003064"/>
                                            <path d="M102.1 8.66265C102.1 8.98388 102.1 8.97688 102.409 8.98563C102.535 8.98913 102.589 8.96112 102.57 8.82633C102.558 8.7353 102.557 8.63989 102.57 8.54886C102.592 8.39656 102.529 8.36767 102.39 8.37293C102.1 8.3843 102.1 8.37643 102.1 8.66265ZM102.971 8.69328C102.971 8.84558 102.973 8.99701 102.97 9.14843C102.968 9.30424 102.884 9.38476 102.73 9.38651C102.467 9.38914 102.205 9.39089 101.943 9.38564C101.771 9.38214 101.697 9.30686 101.695 9.13793C101.692 8.8342 101.695 8.53135 101.693 8.22763C101.692 8.0622 101.775 7.97467 101.936 7.97117C102.205 7.96679 102.472 7.96679 102.741 7.97204C102.886 7.97467 102.967 8.05694 102.97 8.20399C102.973 8.3668 102.97 8.53048 102.971 8.69328Z" fill="#003064"/>
                                            <path d="M102.569 10.6149C102.569 10.5747 102.563 10.5327 102.57 10.4933C102.6 10.3296 102.526 10.2937 102.373 10.3007C102.1 10.3121 102.099 10.3042 102.099 10.572C102.099 10.6307 102.107 10.6902 102.099 10.7462C102.075 10.8898 102.141 10.916 102.27 10.9125C102.569 10.9038 102.569 10.9108 102.569 10.6149ZM102.971 10.6097C102.971 10.7611 102.972 10.9125 102.971 11.0648C102.971 11.2285 102.888 11.3134 102.724 11.3143C102.462 11.316 102.199 11.3178 101.937 11.3134C101.775 11.3117 101.693 11.2233 101.693 11.0587C101.695 10.755 101.692 10.4521 101.695 10.1493C101.697 9.98035 101.773 9.90245 101.942 9.89894C102.205 9.89369 102.467 9.89544 102.73 9.89894C102.883 9.90069 102.969 9.98035 102.97 10.137C102.973 10.2946 102.971 10.4513 102.971 10.6097Z" fill="#003064"/>
                                            <path d="M100.403 8.68052C100.403 8.628 100.396 8.57461 100.404 8.52384C100.424 8.3978 100.37 8.37067 100.252 8.37329C99.935 8.37942 99.9341 8.37329 99.9341 8.68752C99.9341 8.73391 99.9411 8.78118 99.9332 8.82669C99.9096 8.96061 99.9674 8.9895 100.093 8.986C100.403 8.97724 100.403 8.98424 100.403 8.68052ZM100.809 8.68314C100.809 8.82844 100.811 8.97462 100.809 9.11991C100.805 9.3046 100.737 9.3825 100.56 9.386C100.297 9.39126 100.035 9.3895 99.773 9.38688C99.619 9.38513 99.5332 9.3046 99.5323 9.1488C99.5306 8.83982 99.5315 8.52997 99.5315 8.22187C99.5323 8.05731 99.6137 7.97328 99.7792 7.97328C100.042 7.97328 100.304 7.96978 100.567 7.97416C100.735 7.97766 100.804 8.05381 100.809 8.22712C100.811 8.37942 100.809 8.53084 100.809 8.68314Z" fill="#003064"/>
                                            <path d="M102.569 12.8675C102.569 12.5497 102.569 12.5576 102.258 12.548C102.128 12.5445 102.084 12.5777 102.098 12.709C102.11 12.8237 102.103 12.941 102.099 13.0565C102.096 13.1283 102.116 13.1537 102.194 13.1589C102.569 13.1843 102.569 13.1878 102.569 12.8675ZM102.971 12.8631C102.971 13.0145 102.976 13.1668 102.97 13.3182C102.964 13.4889 102.892 13.5598 102.72 13.5624C102.464 13.5659 102.206 13.5659 101.95 13.5624C101.771 13.5598 101.697 13.4854 101.695 13.3051C101.692 13.0014 101.693 12.6985 101.694 12.3948C101.695 12.2399 101.77 12.155 101.929 12.1541C102.197 12.1541 102.465 12.155 102.733 12.1541C102.894 12.1532 102.969 12.2364 102.971 12.3904C102.973 12.548 102.971 12.7047 102.971 12.8631Z" fill="#003064"/>
                                            <path d="M102.569 17.3594C102.569 17.278 102.568 17.1957 102.569 17.1152C102.571 17.0653 102.552 17.0487 102.501 17.0399C102.155 16.9813 102.099 17.0242 102.099 17.3612C102.099 17.4084 102.105 17.4548 102.098 17.5012C102.08 17.6281 102.121 17.6719 102.257 17.6684C102.569 17.6579 102.569 17.6666 102.569 17.3594ZM101.695 17.348C101.695 17.1914 101.691 17.0329 101.696 16.8754C101.702 16.7318 101.772 16.6504 101.926 16.6522C102.195 16.6548 102.463 16.6566 102.732 16.6513C102.885 16.6487 102.965 16.7222 102.968 16.8675C102.974 17.1887 102.973 17.51 102.969 17.8303C102.967 17.9739 102.894 18.0561 102.74 18.0553C102.471 18.0526 102.203 18.0526 101.934 18.0553C101.773 18.057 101.699 17.973 101.695 17.8198C101.693 17.6631 101.695 17.5056 101.695 17.348Z" fill="#003064"/>
                                            <path d="M102.099 15.0967C102.099 15.1317 102.104 15.1676 102.098 15.2008C102.065 15.3794 102.137 15.4293 102.313 15.4179C102.568 15.4013 102.569 15.4135 102.569 15.158C102.569 15.0888 102.56 15.0179 102.57 14.9496C102.592 14.8131 102.533 14.7886 102.409 14.7921C102.099 14.8 102.099 14.7938 102.099 15.0967ZM101.694 15.1116C101.694 14.954 101.693 14.7965 101.695 14.6398C101.697 14.4857 101.771 14.4026 101.932 14.4043C102.2 14.407 102.469 14.407 102.737 14.4043C102.891 14.4026 102.967 14.4805 102.969 14.6258C102.973 14.9461 102.974 15.2674 102.968 15.5886C102.965 15.7339 102.882 15.81 102.732 15.81C102.47 15.8092 102.208 15.8118 101.946 15.8083C101.769 15.8074 101.698 15.733 101.695 15.5492C101.692 15.4039 101.694 15.2577 101.694 15.1116Z" fill="#003064"/>
                                            <path d="M99.4134 16.6122C99.2769 16.6288 99.0887 16.5544 99.0152 16.6446C98.946 16.7295 98.9942 16.9098 98.9898 17.0472C98.9898 17.0656 98.9924 17.0831 98.9898 17.0997C98.9723 17.2039 99.0222 17.2249 99.1167 17.2214C99.2909 17.2144 99.4659 17.21 99.6393 17.2223C99.7898 17.2328 99.8625 17.1995 99.8397 17.0306C99.8222 16.9001 99.88 16.7207 99.8126 16.6446C99.732 16.5553 99.5508 16.6288 99.4134 16.6122ZM99.4152 16.213C99.6077 16.213 99.8003 16.2139 99.9929 16.213C100.159 16.213 100.239 16.2979 100.239 16.4608C100.24 16.7645 100.243 17.0682 100.238 17.3702C100.235 17.554 100.164 17.6231 99.9797 17.6249C99.6007 17.6284 99.2217 17.6258 98.8427 17.6258C98.6808 17.6258 98.5898 17.5505 98.5889 17.3842C98.5872 17.0752 98.588 16.7662 98.5889 16.4572C98.5889 16.2918 98.6729 16.2122 98.8384 16.213C99.03 16.2139 99.2226 16.213 99.4152 16.213Z" fill="#003064"/>
                                            <path d="M99.4265 12.2442C99.2873 12.2442 99.1018 12.19 99.0212 12.2591C98.9249 12.3396 99.002 12.5313 98.9888 12.6749C98.988 12.6862 98.9906 12.6994 98.9888 12.7099C98.9722 12.8132 99.0055 12.8543 99.1193 12.8473C99.2934 12.8377 99.4703 12.8333 99.6444 12.8482C99.8081 12.8631 99.8449 12.8062 99.8405 12.6495C99.8291 12.2355 99.837 12.2355 99.4265 12.2442ZM99.4116 11.839C99.6033 11.839 99.7959 11.8407 99.9884 11.8381C100.156 11.8355 100.238 11.916 100.238 12.0805C100.239 12.3895 100.24 12.6994 100.237 13.0075C100.236 13.172 100.146 13.2491 99.9823 13.2491C99.6042 13.2482 99.2252 13.2508 98.8462 13.2482C98.6632 13.2473 98.5923 13.1764 98.5897 12.9926C98.5862 12.6906 98.5888 12.3869 98.5888 12.0832C98.5888 11.9195 98.6659 11.8363 98.8339 11.8381C99.0265 11.8407 99.219 11.839 99.4116 11.839Z" fill="#003064"/>
                                            <path d="M97.2245 17.2268C97.3628 17.2198 97.544 17.2626 97.6271 17.1917C97.7164 17.1165 97.6464 16.9292 97.6551 16.7917C97.6569 16.769 97.6534 16.7445 97.656 16.7226C97.6683 16.6386 97.6341 16.6106 97.5501 16.6132C97.3584 16.6184 97.165 16.6246 96.9742 16.6114C96.8324 16.6027 96.786 16.6386 96.7895 16.7891C96.8 17.2198 96.7921 17.2198 97.2245 17.2268ZM97.2227 17.625C97.0302 17.625 96.8376 17.6285 96.6459 17.6241C96.4726 17.6198 96.4017 17.548 96.4008 17.3773C96.3973 17.0736 96.3982 16.7707 96.4 16.4679C96.4017 16.2902 96.4656 16.2175 96.638 16.2158C97.0284 16.2114 97.4197 16.2132 97.8101 16.2149C97.9632 16.2158 98.0473 16.2876 98.0455 16.4504C98.0429 16.7602 98.0464 17.0692 98.0446 17.3782C98.0429 17.5506 97.9755 17.6198 97.8004 17.6241C97.6079 17.6285 97.4153 17.625 97.2227 17.625Z" fill="#003064"/>
                                            <path d="M99.4089 15.0316C99.5472 15.0281 99.7354 15.1016 99.8098 15.0132C99.8798 14.9292 99.8317 14.7471 99.8361 14.608C99.8361 14.5905 99.8334 14.573 99.8361 14.5555C99.8518 14.4513 99.8168 14.4093 99.7039 14.4163C99.535 14.4268 99.3643 14.4303 99.1962 14.4154C99.0326 14.4005 98.9582 14.4425 98.9862 14.6193C99.008 14.7559 98.9258 14.9406 99.0194 15.0176C99.0982 15.0841 99.275 15.0316 99.4089 15.0316ZM99.4116 15.4264C99.219 15.4264 99.0273 15.4272 98.8347 15.4264C98.6536 15.4246 98.5923 15.3695 98.5897 15.1874C98.5844 14.8846 98.5853 14.5808 98.5888 14.2789C98.5914 14.0915 98.6597 14.0277 98.8444 14.0268C99.2234 14.025 99.6015 14.025 99.9805 14.0268C100.164 14.0277 100.233 14.0924 100.236 14.2789C100.24 14.5817 100.24 14.8854 100.236 15.1883C100.233 15.3677 100.169 15.4246 99.9892 15.4264C99.7967 15.4272 99.6041 15.4264 99.4116 15.4264Z" fill="#003064"/>
                                            <path d="M97.2547 12.2358C97.1251 12.2358 97.0192 12.2393 96.9151 12.2341C96.8302 12.2297 96.7847 12.2472 96.7917 12.347C96.7995 12.4739 96.7987 12.6034 96.7917 12.7312C96.7855 12.8301 96.8293 12.8485 96.9151 12.8468C97.1015 12.8415 97.2888 12.8363 97.4753 12.8485C97.6179 12.8581 97.6748 12.8196 97.6582 12.67C97.6424 12.5325 97.6985 12.3513 97.6293 12.2656C97.5549 12.1737 97.3676 12.2498 97.2547 12.2358ZM97.2214 11.8393C97.414 11.8393 97.6066 11.8358 97.7991 11.8402C97.9794 11.8446 98.0433 11.9067 98.0442 12.0809C98.046 12.3907 98.0425 12.6988 98.046 13.0087C98.0468 13.1697 97.9637 13.2468 97.8114 13.2476C97.4201 13.2511 97.0297 13.252 96.6385 13.2476C96.4704 13.2459 96.4013 13.168 96.4004 12.9947C96.3978 12.6918 96.3978 12.3881 96.4004 12.0844C96.4013 11.9111 96.4678 11.8446 96.6437 11.8402C96.8363 11.8358 97.0289 11.8393 97.2214 11.8393Z" fill="#003064"/>
                                            <path d="M97.1997 15.0401C97.3231 15.0401 97.4229 15.034 97.5209 15.0419C97.6216 15.0506 97.6706 15.027 97.6592 14.9123C97.6478 14.7907 97.6513 14.6673 97.6583 14.5465C97.6644 14.4493 97.6356 14.4125 97.5332 14.4169C97.3537 14.4239 97.1725 14.4292 96.9931 14.416C96.839 14.4038 96.769 14.4449 96.7927 14.6121C96.8119 14.7487 96.7358 14.9281 96.8224 15.0139C96.9082 15.0988 97.0868 15.0296 97.1997 15.0401ZM97.2347 15.4261C97.036 15.4261 96.8382 15.4226 96.6395 15.427C96.4784 15.4305 96.4014 15.3552 96.4014 15.1986C96.4005 14.8826 96.3997 14.5683 96.4014 14.2532C96.4023 14.1044 96.4793 14.0265 96.629 14.0265C97.0255 14.0257 97.422 14.0257 97.8185 14.0265C97.9664 14.0265 98.0461 14.0992 98.0461 14.2506C98.0461 14.5718 98.0469 14.8922 98.0461 15.2126C98.0452 15.3544 97.9734 15.427 97.829 15.4261C97.6312 15.4253 97.4325 15.4261 97.2347 15.4261Z" fill="#003064"/>
                                            <path d="M94.122 17.5121C94.122 17.4596 94.115 17.4071 94.1228 17.3554C94.143 17.2303 94.0896 17.2031 93.9705 17.2058C93.6414 17.2119 93.6414 17.2066 93.6414 17.5296C93.6414 17.5646 93.6458 17.6005 93.6405 17.6338C93.6169 17.7738 93.6642 17.8246 93.8147 17.8194C94.122 17.808 94.122 17.8167 94.122 17.5121ZM93.2467 17.4964C93.2467 17.3458 93.2475 17.1944 93.2467 17.0421C93.2449 16.8907 93.3263 16.811 93.469 16.8075C93.743 16.8014 94.0178 16.8014 94.2918 16.8075C94.4344 16.8101 94.5158 16.8845 94.5141 17.0386C94.5115 17.3537 94.5115 17.6688 94.5141 17.983C94.5158 18.1476 94.4274 18.2176 94.2769 18.2202C94.0152 18.2229 93.7526 18.2246 93.49 18.2202C93.3184 18.2159 93.2502 18.1415 93.2475 17.969C93.2449 17.8123 93.2467 17.6548 93.2467 17.4964Z" fill="#003064"/>
                                            <path d="M94.1216 15.2848C94.1216 15.2271 94.1146 15.1676 94.1225 15.1115C94.1409 14.9881 94.0945 14.9566 93.9719 14.9592C93.6411 14.9662 93.6411 14.9592 93.6411 15.2822C93.6411 15.3172 93.6463 15.3531 93.6402 15.3864C93.6087 15.5483 93.684 15.5781 93.8301 15.5719C94.1216 15.5606 94.1216 15.5684 94.1216 15.2848ZM94.5129 15.2586C94.5129 15.4214 94.512 15.5851 94.5129 15.7479C94.5137 15.8923 94.4385 15.9632 94.2975 15.9641C94.0183 15.9658 93.7382 15.9676 93.4581 15.9632C93.3181 15.9614 93.2472 15.8809 93.2463 15.7409C93.2463 15.4196 93.2454 15.0993 93.2472 14.7789C93.2481 14.638 93.3295 14.5619 93.4651 14.5592C93.7391 14.5531 94.0131 14.554 94.287 14.5584C94.4306 14.5601 94.5146 14.6328 94.5129 14.7868C94.512 14.9435 94.5129 15.101 94.5129 15.2586Z" fill="#003064"/>
                                            <path d="M93.6402 9.15764C93.6402 9.23291 93.6428 9.30906 93.6393 9.38521C93.6376 9.44211 93.6577 9.46311 93.7172 9.47099C94.0884 9.52351 94.1207 9.49812 94.1207 9.13663C94.1207 9.10162 94.1155 9.06573 94.1216 9.03159C94.1488 8.88192 94.0849 8.84778 93.9413 8.85216C93.6402 8.86354 93.6402 8.85566 93.6402 9.15764ZM94.512 9.16376C94.512 9.32657 94.5129 9.49025 94.5111 9.65305C94.5094 9.78785 94.4402 9.85874 94.3054 9.85874C94.0253 9.85962 93.7461 9.85962 93.466 9.85962C93.3242 9.85962 93.2481 9.78609 93.2463 9.64605C93.2437 9.31956 93.2428 8.99308 93.2472 8.6666C93.2481 8.5353 93.326 8.45915 93.4555 8.4574C93.7409 8.45215 94.0271 8.45127 94.3124 8.45828C94.442 8.4609 94.5111 8.54055 94.5111 8.6736C94.5111 8.83728 94.5111 9.00008 94.512 9.16376Z" fill="#003064"/>
                                            <path d="M94.1223 13.0109C94.1223 12.7045 94.1223 12.7142 93.8299 12.7019C93.6759 12.6958 93.6198 12.7369 93.6391 12.8927C93.6522 13.0013 93.6452 13.1133 93.64 13.2236C93.6373 13.2971 93.661 13.3207 93.738 13.326C94.1223 13.354 94.1223 13.3566 94.1223 13.0109ZM94.5135 13.0083C94.5135 13.1719 94.5135 13.3347 94.5135 13.4984C94.5126 13.6411 94.4417 13.7146 94.2991 13.7146C94.019 13.7146 93.7398 13.7155 93.4597 13.7137C93.3161 13.712 93.2478 13.6315 93.247 13.4923C93.2461 13.1719 93.2452 12.8507 93.2478 12.5304C93.2487 12.3912 93.3266 12.3133 93.4649 12.3115C93.745 12.308 94.0242 12.3072 94.3043 12.3115C94.4339 12.3142 94.51 12.3842 94.5118 12.519C94.5144 12.6827 94.5135 12.8463 94.5135 13.0083Z" fill="#003064"/>
                                            <path d="M94.1223 11.0983C94.1223 10.7788 94.1223 10.7788 93.9139 10.7788C93.6417 10.7788 93.6417 10.7788 93.6417 11.0554C93.6417 11.114 93.6487 11.1727 93.64 11.2287C93.619 11.3687 93.6741 11.4046 93.8107 11.4011C94.1223 11.3915 94.1223 11.3985 94.1223 11.0983ZM94.5135 11.0895C94.5135 11.2532 94.5144 11.416 94.5126 11.5797C94.5118 11.7127 94.4435 11.7854 94.3078 11.7862C94.0225 11.7871 93.7371 11.788 93.4518 11.7854C93.3161 11.7836 93.2487 11.704 93.2478 11.5753C93.2452 11.2549 93.2461 10.9337 93.247 10.6125C93.2478 10.4645 93.3257 10.3858 93.4737 10.3849C93.7476 10.3823 94.0216 10.3814 94.2956 10.3849C94.433 10.3858 94.5118 10.4575 94.5126 10.6002C94.5135 10.763 94.5126 10.9267 94.5135 11.0895Z" fill="#003064"/>
                                            <path d="M95.804 9.14334C95.804 9.20111 95.811 9.26063 95.8022 9.31752C95.7856 9.43831 95.8241 9.47595 95.951 9.47333C96.2915 9.46545 96.2915 9.47245 96.2915 9.12496C96.2915 9.10133 96.288 9.07769 96.2924 9.05406C96.3169 8.90176 96.2661 8.84312 96.099 8.85187C95.804 8.86762 95.804 8.85624 95.804 9.14334ZM95.418 9.15735V8.66718C95.418 8.53064 95.495 8.46061 95.6228 8.45799C95.9029 8.45186 96.1821 8.45361 96.4622 8.45711C96.5987 8.45886 96.6793 8.52976 96.6793 8.67418C96.6793 9.00067 96.6801 9.32715 96.6793 9.65276C96.6784 9.78668 96.6057 9.85758 96.4727 9.85845C96.1874 9.8602 95.902 9.86108 95.6167 9.85758C95.4819 9.85583 95.4171 9.78055 95.418 9.64663C95.418 9.48383 95.418 9.32015 95.418 9.15735Z" fill="#003064"/>
                                            <path d="M85.7081 23.2919H107.292V1.70722H85.7081V23.2919ZM108 24H85V1H108V24Z" fill="#003064"/>
                                            <path d="M135.149 20.5927H134.297V19.7408H135.149V20.5927Z" fill="#003064"/>
                                            <path d="M119.351 20.5927H116.408V19.7408H119.351V20.5927ZM123.765 20.5927H120.823V19.7408H123.765V20.5927ZM128.179 20.5927H125.236V19.7408H128.179V20.5927ZM132.593 20.5927H129.651V19.7408H132.593V20.5927Z" fill="#003064"/>
                                            <path d="M114.703 20.5927H113.852V19.7408H114.703V20.5927Z" fill="#003064"/>
                                            <path d="M121.282 7.67969L119.107 6.78923V6.28879L121.282 5.40272V5.91193L119.293 6.71036V6.37819L121.282 7.17486V7.67969Z" fill="#003064"/>
                                            <path d="M122.811 7.85693C122.592 7.85693 122.382 7.82713 122.182 7.76578C121.981 7.70531 121.819 7.6203 121.695 7.51162L121.907 6.99365C122.043 7.09356 122.184 7.16806 122.331 7.21539C122.476 7.26184 122.631 7.28638 122.793 7.28638C122.973 7.28638 123.109 7.25307 123.201 7.18559C123.292 7.11898 123.338 7.01994 123.338 6.8876C123.338 6.75964 123.295 6.66587 123.207 6.60364C123.12 6.54141 122.992 6.51074 122.824 6.51074H122.292V5.96122H122.758C122.908 5.96122 123.027 5.92704 123.112 5.85955C123.198 5.79207 123.24 5.69566 123.24 5.57208C123.24 5.45639 123.2 5.36875 123.119 5.30828C123.038 5.24693 122.921 5.21713 122.767 5.21713C122.466 5.21713 122.197 5.31529 121.961 5.50898L121.744 5.00416C121.876 4.89197 122.038 4.80521 122.228 4.74035C122.419 4.67725 122.617 4.6457 122.824 4.6457C123.161 4.6457 123.425 4.72019 123.615 4.86919C123.806 5.01905 123.9 5.22414 123.9 5.48707C123.9 5.66761 123.85 5.82537 123.748 5.95859C123.646 6.09356 123.505 6.1812 123.325 6.22327V6.16455C123.538 6.19698 123.703 6.28374 123.821 6.42397C123.939 6.5642 123.998 6.74124 123.998 6.95333C123.998 7.23379 123.893 7.45465 123.681 7.61592C123.47 7.7763 123.18 7.85693 122.811 7.85693Z" fill="#003064"/>
                                            <path d="M124.747 8.45093L124.486 8.24234C124.592 8.1398 124.666 8.04076 124.708 7.94786C124.749 7.85496 124.77 7.7568 124.77 7.65338L124.916 7.81201H124.424V7.10386H125.146V7.5596C125.146 7.72875 125.115 7.88212 125.056 8.02148C124.994 8.15995 124.892 8.30281 124.747 8.45093Z" fill="#003064"/>
                                            <path d="M126.691 7.85693C126.552 7.85693 126.416 7.84291 126.282 7.81486C126.147 7.78682 126.02 7.74738 125.903 7.69479C125.785 7.64396 125.684 7.58261 125.602 7.51162L125.815 6.99365C125.953 7.09093 126.093 7.16368 126.235 7.21276C126.376 7.26096 126.524 7.28638 126.678 7.28638C126.849 7.28638 126.983 7.24519 127.078 7.16368C127.175 7.08217 127.222 6.97262 127.222 6.83414C127.222 6.69216 127.177 6.57822 127.085 6.49233C126.994 6.40732 126.87 6.36437 126.714 6.36437C126.598 6.36437 126.492 6.38541 126.396 6.42835C126.3 6.4713 126.212 6.5344 126.134 6.61679H125.694V4.69039H127.687V5.25218H126.346V6.09882H126.182C126.258 6.00416 126.358 5.93317 126.481 5.88321C126.603 5.83501 126.738 5.81047 126.886 5.81047C127.086 5.81047 127.262 5.85166 127.411 5.93492C127.56 6.01731 127.676 6.133 127.758 6.28199C127.841 6.43186 127.883 6.60802 127.883 6.81223C127.883 7.0217 127.835 7.20487 127.739 7.36175C127.643 7.51688 127.505 7.63958 127.327 7.72722C127.148 7.81311 126.936 7.85693 126.691 7.85693Z" fill="#003064"/>
                                            <path d="M130.044 7.81226V6.0892H129.305V5.57123H131.457V6.0892H130.713V7.81226H130.044Z" fill="#003064"/>
                                            <path d="M120.914 13.2978H128.874L128.278 11.5669C128.141 11.1699 128.089 11.1339 127.658 11.1339H122.134C122.079 11.1339 122.024 11.1383 121.969 11.1418L121.898 11.1471C121.723 11.1576 121.625 11.233 121.562 11.4065C121.486 11.6169 121.413 11.8281 121.341 12.0393L120.914 13.2978ZM129.628 13.8351H120.162L120.832 11.8666C120.905 11.6519 120.979 11.4372 121.056 11.2233C121.191 10.85 121.479 10.6326 121.866 10.6098C121.999 10.6011 122.067 10.5967 122.134 10.5967L127.658 10.5958C128.312 10.5958 128.575 10.7816 128.787 11.3907L129.628 13.8351ZM120.324 15.2584C120.123 15.2584 119.962 15.3838 119.892 15.5994C119.831 15.7869 119.858 15.9569 119.978 16.134C120.086 16.2944 120.267 16.3557 120.462 16.2987C120.635 16.247 120.779 16.0174 120.779 15.7948C120.779 15.5354 120.61 15.2979 120.404 15.2654C120.376 15.2611 120.35 15.2584 120.324 15.2584ZM120.323 16.857C120.008 16.857 119.716 16.7063 119.533 16.4355C119.318 16.12 119.268 15.7825 119.38 15.4337C119.541 14.9359 119.987 14.6554 120.488 14.7343C120.95 14.8071 121.315 15.2716 121.318 15.7922C121.318 16.2558 121.016 16.6958 120.614 16.815C120.517 16.843 120.418 16.857 120.323 16.857ZM129.467 15.2505C129.412 15.2505 129.357 15.2602 129.305 15.2812C129.127 15.3557 129.008 15.552 129.011 15.7703V15.8097C129.024 16.0893 129.207 16.2926 129.428 16.3215C129.613 16.3461 129.82 16.198 129.898 15.9841C129.979 15.7632 129.931 15.5652 129.746 15.3618C129.681 15.2891 129.574 15.2505 129.467 15.2505ZM129.47 16.8623C129.433 16.8623 129.396 16.8597 129.358 16.8553C128.882 16.7922 128.502 16.3776 128.474 15.8886C128.472 15.8605 128.473 15.8307 128.473 15.8001L128.742 15.7738L128.473 15.7773C128.468 15.3347 128.713 14.9455 129.099 14.7843C129.462 14.6327 129.893 14.7229 130.144 14.9999C130.465 15.3531 130.554 15.7571 130.404 16.1682C130.252 16.5853 129.871 16.8623 129.47 16.8623ZM130.301 13.4503L130.319 13.467C130.313 13.4617 130.307 13.4556 130.301 13.4503ZM119.713 13.3049H119.957L119.998 13.7229L119.611 14.077C119.396 14.2716 119.181 14.4679 118.971 14.6677C118.758 14.8737 118.648 15.134 118.646 15.4433C118.638 16.7352 118.642 18.0271 118.645 19.3172C118.645 19.4521 118.68 19.6327 118.982 19.6336C119.307 19.6362 119.632 19.6353 119.958 19.6344C120.224 19.6336 120.329 19.5336 120.332 19.2786L120.334 19.1305C120.339 18.9096 120.342 18.6896 120.329 18.4697C120.326 18.4144 120.316 18.2444 120.439 18.1138C120.562 17.9824 120.751 17.9824 120.807 17.9832C123.542 17.9841 126.277 17.985 129.013 17.9815C129.121 17.9841 129.248 17.9938 129.354 18.1033C129.472 18.2243 129.468 18.3855 129.466 18.4381L129.461 18.5661C129.453 18.85 129.444 19.1191 129.483 19.382C129.496 19.4714 129.548 19.6213 129.745 19.6265C130.128 19.6371 130.511 19.6379 130.894 19.6257C131.037 19.6213 131.119 19.5538 131.144 19.4188C131.159 19.3382 131.169 19.2549 131.169 19.1726C131.171 17.9683 131.171 16.765 131.17 15.5617C131.169 15.1454 131.015 14.8123 130.701 14.5406C130.513 14.3794 130.333 14.2111 130.152 14.0428L129.978 13.8789C129.778 13.7326 129.844 13.531 129.868 13.4714L129.938 13.3092L130.343 13.3101C130.509 13.3119 130.675 13.3127 130.843 13.3084C131 13.304 131.142 13.2391 131.244 13.1243C131.343 13.0121 131.391 12.8684 131.377 12.7194C131.362 12.5538 131.322 12.5187 131.152 12.5178L130.798 12.5187C130.576 12.5187 130.356 12.5196 130.136 12.517C130.015 12.5134 129.987 12.545 129.943 12.637C129.893 12.7992 129.78 12.8544 129.693 12.8719L129.464 12.9166L129.219 12.2155C129.095 11.8588 128.971 11.5038 128.838 11.1515C128.638 10.6186 128.345 10.4205 127.76 10.4197C125.847 10.417 123.933 10.417 122.02 10.4197C121.464 10.4205 121.146 10.6388 120.957 11.1497C120.879 11.3592 120.806 11.5695 120.734 11.7816L120.088 13.6169L119.878 12.7264C119.833 12.5406 119.773 12.5029 119.555 12.5134C119.347 12.5248 119.139 12.5222 118.93 12.5205L118.598 12.5187C118.501 12.5196 118.455 12.5546 118.429 12.6502C118.389 12.7913 118.425 12.9499 118.524 13.0831C118.626 13.219 118.779 13.3014 118.933 13.304C119.112 13.3075 119.29 13.3066 119.468 13.3057L119.713 13.3049ZM119.432 20.1743C119.28 20.1743 119.129 20.1734 118.978 20.1726C118.459 20.1682 118.108 19.8255 118.106 19.3189C118.104 18.0262 118.1 16.7335 118.108 15.4398C118.111 14.9867 118.281 14.5853 118.599 14.2795C118.753 14.1322 118.91 13.9876 119.068 13.8439C119.02 13.843 118.972 13.843 118.924 13.8421C118.6 13.8369 118.297 13.6782 118.093 13.4057C117.893 13.1375 117.827 12.8097 117.91 12.5064C118 12.1804 118.256 11.9841 118.594 11.9806L118.935 11.9823C119.134 11.985 119.331 11.9876 119.527 11.9771C119.738 11.9657 119.919 12.0016 120.058 12.0857L120.225 11.6063C120.298 11.3907 120.373 11.176 120.452 10.9622C120.717 10.2461 121.245 9.8824 122.019 9.88153C123.934 9.8789 125.847 9.8789 127.761 9.88153C128.573 9.88328 129.06 10.2154 129.341 10.9613C129.476 11.318 129.602 11.6782 129.726 12.0384L129.741 12.0796C129.855 12.0113 129.992 11.9779 130.143 11.9788C130.361 11.9815 130.579 11.9815 130.796 11.9806H131.154C131.603 11.9815 131.872 12.226 131.913 12.6703C131.94 12.9701 131.845 13.2575 131.645 13.4819C131.444 13.7089 131.164 13.8377 130.857 13.8456C130.815 13.8474 130.774 13.8474 130.733 13.8482C130.837 13.9447 130.943 14.0402 131.051 14.1331C131.486 14.5073 131.707 14.9876 131.708 15.5608C131.709 16.765 131.709 17.9683 131.707 19.1726C131.707 19.2874 131.695 19.4048 131.673 19.5187C131.601 19.9035 131.309 20.1498 130.912 20.1629C130.518 20.1761 130.124 20.1752 129.731 20.1638C129.326 20.1524 129.013 19.8711 128.951 19.4618C128.904 19.1506 128.915 18.8439 128.924 18.5477L128.925 18.5205C126.224 18.5222 123.547 18.5222 120.871 18.5205C120.879 18.7361 120.876 18.9386 120.872 19.1401L120.87 19.2865C120.863 19.8308 120.514 20.1708 119.96 20.1726C119.784 20.1734 119.608 20.1743 119.432 20.1743Z" fill="#003064"/>
                                            <path d="M113.707 23.2918H135.291V1.70725H113.707V23.2918ZM136 24H113V1H136V24Z" fill="#003064"/>
                                            <path d="M165 19.9822H163.223V19.1607H165V19.9822Z" fill="#003064"/>
                                            <path d="M147.787 19.9822H144.555V19.1607H147.787V19.9822ZM152.636 19.9822H149.404V19.1607H152.636V19.9822ZM157.485 19.9822H154.252V19.1607H157.485V19.9822ZM162.332 19.9822H159.1V19.1607H162.332V19.9822Z" fill="#003064"/>
                                            <path d="M143.666 19.9822H141.889V19.1607H143.666V19.9822Z" fill="#003064"/>
                                            <path d="M161.33 18.0546H160.482C160.507 17.9128 160.521 17.7693 160.521 17.625C160.521 16.1859 159.254 15.0152 157.696 15.0152H155.445V10.3282H159.893C159.954 10.3282 160.012 10.351 160.055 10.3898L160.771 11.0524H157.489C157.244 11.0524 157.044 11.2373 157.044 11.4643V13.3702C157.044 13.598 157.244 13.7829 157.489 13.7829H160.043C160.15 13.7829 160.254 13.8183 160.335 13.8833L161.468 14.7789V14.9443H160.885C160.693 14.9443 160.538 15.0886 160.538 15.2659V16.1757C160.538 16.3538 160.693 16.4982 160.885 16.4982H161.468V17.9271C161.468 17.998 161.406 18.0546 161.33 18.0546ZM159.39 18.0546C159.431 17.9178 159.453 17.7735 159.453 17.625C159.453 16.7303 158.665 16.001 157.696 16.001C157.264 16.001 156.867 16.1471 156.561 16.3868H155.445V15.4887H157.696C158.971 15.4887 160.008 16.4475 160.008 17.625C160.008 17.7693 159.993 17.9128 159.961 18.0546H159.39ZM157.696 18.7754C157.01 18.7754 156.451 18.2597 156.451 17.625C156.451 16.9911 157.01 16.4745 157.696 16.4745C158.383 16.4745 158.942 16.9911 158.942 17.625C158.942 18.2597 158.383 18.7754 157.696 18.7754ZM159.172 9.5956C159.223 9.61586 159.256 9.66228 159.256 9.71377V9.85473H155.445V8.11178L159.172 9.5956ZM161.401 11.6348C161.444 11.6736 161.468 11.7268 161.468 11.7834V14.156L160.669 13.5238C160.494 13.3862 160.272 13.3102 160.043 13.3102H157.555V11.5259H161.283L161.401 11.6348ZM161.468 16.0255H161.05V15.4178H161.468V16.0255ZM148.516 18.7754C147.83 18.7754 147.271 18.2597 147.271 17.625C147.271 16.9911 147.83 16.4745 148.516 16.4745C149.202 16.4745 149.761 16.9911 149.761 17.625C149.761 18.2597 149.202 18.7754 148.516 18.7754ZM146.027 17.6874V16.8603H146.967C146.834 17.0882 146.759 17.349 146.759 17.625C146.759 17.6469 146.76 17.6672 146.761 17.6874H146.027ZM161.763 11.2997L160.417 10.0548C160.277 9.92647 160.092 9.85473 159.893 9.85473H159.767V9.71377C159.767 9.47322 159.613 9.2563 159.375 9.16092L155.445 7.59607V7.04407C155.445 6.82631 155.254 6.64906 155.019 6.64906H147.81C147.668 6.64906 147.554 6.75541 147.554 6.88623C147.554 7.01706 147.668 7.12257 147.81 7.12257H154.934V16.3868H149.651C149.344 16.1471 148.949 16.001 148.516 16.001C148.084 16.001 147.687 16.1471 147.38 16.3868H145.019V7.12257H146.615C146.756 7.12257 146.871 7.01706 146.871 6.88623C146.871 6.75541 146.756 6.64906 146.615 6.64906H144.934C144.699 6.64906 144.508 6.82631 144.508 7.04407V16.4661C144.508 16.6839 144.699 16.8603 144.934 16.8603H145.515V17.7668C145.515 17.9837 145.707 18.1609 145.942 18.1609H146.858C147.097 18.794 147.75 19.2489 148.516 19.2489C149.282 19.2489 149.934 18.794 150.174 18.1609H153.741C153.883 18.1609 153.997 18.0546 153.997 17.9246C153.997 17.7929 153.883 17.6874 153.741 17.6874H150.271C150.272 17.6672 150.273 17.6469 150.273 17.625C150.273 17.349 150.197 17.0882 150.065 16.8603H156.147C156.014 17.0882 155.939 17.349 155.939 17.625C155.939 17.6469 155.94 17.6672 155.941 17.6874H154.936C154.794 17.6874 154.679 17.7929 154.679 17.9246C154.679 18.0546 154.794 18.1609 154.936 18.1609H156.038C156.278 18.794 156.93 19.2489 157.696 19.2489C158.304 19.2489 158.84 18.9619 159.156 18.5281H161.33C161.688 18.5281 161.979 18.258 161.979 17.9271V11.7834C161.979 11.601 161.903 11.428 161.763 11.2997Z" fill="#003064"/>
                                            <path d="M157.697 17.8667C157.553 17.8667 157.436 17.7587 157.436 17.6253C157.436 17.4919 157.553 17.3839 157.697 17.3839C157.842 17.3839 157.959 17.4919 157.959 17.6253C157.959 17.7587 157.842 17.8667 157.697 17.8667ZM157.697 16.9104C157.271 16.9104 156.924 17.2311 156.924 17.6253C156.924 18.0195 157.271 18.3402 157.697 18.3402C158.124 18.3402 158.471 18.0195 158.471 17.6253C158.471 17.2311 158.124 16.9104 157.697 16.9104Z" fill="#003064"/>
                                            <path d="M148.516 17.8667C148.371 17.8667 148.255 17.7587 148.255 17.6253C148.255 17.4919 148.371 17.3839 148.516 17.3839C148.66 17.3839 148.777 17.4919 148.777 17.6253C148.777 17.7587 148.66 17.8667 148.516 17.8667ZM148.516 16.9104C148.089 16.9104 147.742 17.2311 147.742 17.6253C147.742 18.0195 148.089 18.3402 148.516 18.3402C148.942 18.3402 149.289 18.0195 149.289 17.6253C149.289 17.2311 148.942 16.9104 148.516 16.9104Z" fill="#003064"/>
                                            <path d="M146.615 8.16019C146.474 8.16019 146.359 8.2657 146.359 8.39652V15.1126C146.359 15.2442 146.474 15.3497 146.615 15.3497C146.757 15.3497 146.871 15.2442 146.871 15.1126V8.39652C146.871 8.2657 146.757 8.16019 146.615 8.16019Z" fill="#003064"/>
                                            <path d="M149.113 15.1136V8.39676C149.113 8.26593 148.999 8.16043 148.857 8.16043C148.716 8.16043 148.602 8.26593 148.602 8.39676V15.1128C148.602 15.2436 148.716 15.3491 148.857 15.3491C148.999 15.3491 149.113 15.2436 149.113 15.1136Z" fill="#003064"/>
                                            <path d="M151.354 15.1136V8.39676C151.354 8.26593 151.24 8.16043 151.098 8.16043C150.957 8.16043 150.842 8.26593 150.842 8.39676V15.1128C150.842 15.2436 150.957 15.3491 151.098 15.3491C151.24 15.3491 151.354 15.2436 151.354 15.1136Z" fill="#003064"/>
                                            <path d="M153.596 15.1136V8.39676C153.596 8.26593 153.482 8.16043 153.341 8.16043C153.199 8.16043 153.084 8.26593 153.084 8.39676V15.1128C153.084 15.2436 153.199 15.3491 153.341 15.3491C153.482 15.3491 153.596 15.2436 153.596 15.1136Z" fill="#003064"/>
                                            <path d="M141.739 23.2765H164.261V1.69187H141.739V23.2765ZM165 23.9846H141V0.984619H165V23.9846Z" fill="#003064"/>
                                            <path d="M186.452 19.5354H185.101V14.2953L186.99 14.8202L188.667 15.2856L186.452 19.5354ZM181.009 15.2856L182.684 14.8202H182.686L184.576 14.2953V19.5354H183.221L181.009 15.2856ZM182.878 13.276H183.784H185.89H186.798V14.2225L184.908 13.6975C184.862 13.6844 184.814 13.6844 184.767 13.6975L182.878 14.2225V13.276ZM184.046 11.8728H185.627V12.7528H184.046V11.8728ZM189.118 14.8667L187.322 14.368V13.0148C187.322 12.8702 187.205 12.7528 187.06 12.7528H186.152V11.6108C186.152 11.4662 186.035 11.3487 185.89 11.3487H183.784C183.64 11.3487 183.522 11.4662 183.522 11.6108V12.7528H182.616C182.471 12.7528 182.354 12.8702 182.354 13.0148V14.368L180.557 14.8667C180.481 14.8877 180.419 14.9429 180.387 15.0157C180.356 15.0876 180.358 15.1708 180.395 15.2409L182.83 19.9184C182.875 20.0052 182.965 20.0604 183.063 20.0604H186.611C186.709 20.0604 186.798 20.0052 186.844 19.9184L189.281 15.2409C189.318 15.1708 189.321 15.0876 189.289 15.0157C189.257 14.9429 189.195 14.8877 189.118 14.8667Z" fill="#003064"/>
                                            <path d="M180.444 19.3018H174.184V18.4227H176.495H178.367H180.444V19.3018ZM176.757 8.80304V6.93274V4.92922L178.105 5.26577V6.93274V8.80304V17.8978H176.757V8.80304ZM175.428 8.54187L175.764 7.19479H176.233V8.54187H175.428ZM187.775 7.19479L188.281 8.54187H178.629V7.19479H187.775ZM180.706 17.8978H178.629V9.06597H188.659C188.744 9.06597 188.825 9.02303 188.874 8.95291C188.923 8.88192 188.935 8.79253 188.905 8.71189L188.203 6.83984C188.164 6.73817 188.067 6.67069 187.957 6.67069H178.629V5.06069C178.629 4.94062 178.546 4.83544 178.43 4.80652L176.559 4.33938C176.481 4.31923 176.398 4.33675 176.334 4.38671C176.27 4.43667 176.233 4.51292 176.233 4.59355V6.67069H175.559C175.439 6.67069 175.334 6.7522 175.305 6.86876L174.837 8.73994C174.818 8.81882 174.835 8.90208 174.885 8.96518C174.934 9.02829 175.011 9.06597 175.091 9.06597H176.233V17.8978H173.922C173.778 17.8978 173.66 18.0152 173.66 18.1607V19.5639C173.66 19.7093 173.778 19.8268 173.922 19.8268H180.706C180.85 19.8268 180.968 19.7093 180.968 19.5639V18.1607C180.968 18.0152 180.85 17.8978 180.706 17.8978Z" fill="#003064"/>
                                            <path d="M170.708 23.3225H192.292V1.73789H170.708V23.3225ZM193 24.0306H170V1.03064H193V24.0306Z" fill="#003064"/>
                                        </svg>
                                    </div>
                                <? }
                            }
                            ?>
                        </div>
                        <div class="settings">
                            <h4 class="one_title">
                                Параметры
                            </h4>
                            <div class="settings_blocks">
                                <div class="settings_block">
                                    <ul>
                                        <li>
                                            <? is_array($arResult['PROPERTIES']['PROP_2033']['VALUE'])
                                                ? $heightProp = $arResult['PROPERTIES']['PROP_2033']['VALUE'][0]
                                                : $heightProp = $arResult['PROPERTIES']['PROP_2033']['VALUE'];
                                            ?>
                                            <h6>
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="14.8625" cy="14.8625" r="14.3625" fill="white" stroke="#003064"/>
                                                    <g clip-path="url(#clip0_310_141)">
                                                        <path d="M24.7112 13.5828H4.28906V17.5323H24.7112V13.5828Z" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                                        <path d="M15.7948 21.2679L13.2733 21.2624L14.5928 19.3295L15.7948 21.2679Z" fill="#1B355E" stroke="#003064" stroke-width="1.45856"/>
                                                        <path d="M13.2099 9.73219L15.7315 9.73776L14.4176 11.6707L13.2099 9.73219Z" fill="#1B355E" stroke="#003064" stroke-width="1.45856"/>
                                                        <path d="M24.699 15.0568L22.8613 17.6245" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                                        <path d="M9.58161 13.5713L6.61133 17.6244" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                                        <path d="M7.25934 13.5713L4.28906 17.6244" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                                        <path d="M12.2867 13.5713L9.31641 17.6244" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                                        <path d="M14.691 13.5713L11.7207 17.6244" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                                        <path d="M17.5679 13.5713L14.5977 17.6244" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                                        <path d="M20.5152 13.5713L17.5449 17.6244" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                                        <path d="M23.1968 13.5713L20.2266 17.6244" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_310_141">
                                                            <rect width="21" height="13" fill="white" transform="translate(4 9)"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                Высота: <?= $heightProp ?>мм
                                            </h6>
                                            <span>
                                        <?= $heightProp ?>мм
                                      </span>
                                        </li>
                                        <li>
                                            <h6>
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="14.8625" cy="14.8625" r="14.3625" fill="white" stroke="#003064"/>
                                                    <g clip-path="url(#clip0_310_165)">
                                                        <path d="M10.8287 7.64136L7.22656 12.7714" stroke="#003064" stroke-width="0.889559" stroke-miterlimit="10"/>
                                                        <path d="M14.1825 7.65186L7.30078 16.7557" stroke="#003064" stroke-width="0.889559" stroke-miterlimit="10"/>
                                                        <path d="M17.3546 8.14587L7.2793 21.5178" stroke="#003064" stroke-width="0.889559" stroke-miterlimit="10"/>
                                                        <path d="M19.3325 10.1012L9.83789 22.7582" stroke="#003064" stroke-width="0.889559" stroke-miterlimit="10"/>
                                                        <path d="M22.7205 11.184L13.9355 22.6741" stroke="#003064" stroke-width="0.889559" stroke-miterlimit="10"/>
                                                        <path d="M22.7633 15.7045L18.1074 22.5796" stroke="#003064" stroke-width="0.889559" stroke-miterlimit="10"/>
                                                        <path d="M22.7309 22.7267V10.4796H19.9997L16.8599 7.52563H7.30078V22.7267H22.7309Z" stroke="#003064" stroke-width="0.889559" stroke-miterlimit="10"/>
                                                        <path d="M7.08594 7.54666L16.9031 7.52563L19.9354 10.4902H22.9462" stroke="#003064" stroke-width="1.77912" stroke-miterlimit="10"/>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_310_165">
                                                            <rect width="16" height="16" fill="white" transform="translate(7 7)"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                <?
                                                $arResult['PROPERTIES']['FACET_SIZE']['VALUE']
//                                                  ? $isFacetProp = 'с фаской, ' . $arResult['PROPERTIES']['FACET_SIZE']['VALUE']
//                                                  ? $isFacetProp = 'микрофаска'
                                                  ? $isFacetProp = $arResult['PROPERTIES']['FACET_SIZE']['VALUE']
                                                  : $isFacetProp = 'без фаски';
                                                ?>
                                                Наличие фаски: <?= $isFacetProp ?>
                                            </h6>
                                            <span>
                                            <? if($arResult['PROPERTIES']['FACET_IMG']['VALUE']) { ?>
                                                    <img class="facet-img" src="<?= CFile::GetFileArray($arResult['PROPERTIES']['FACET_IMG']['VALUE'])['SRC'] ?>" alt="">
                                            <? } ?>

                                      </span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="settings_block plate_size">
                                    <h6>
                                        Размер:
                                    </h6>
                                    <div class="buttons">
                                        <? foreach($arResult['PROPERTIES']['PLATE_SIZE']['VALUE'] as $plateSizeImg) { ?>
                                            <button>
                                                <img src="<?= CFile::GetFileArray($plateSizeImg)['SRC'] ?>" alt="">
                                            </button>
                                        <? } ?>
                                    </div>
                                </div>



                                <? // COLORS IMG PANEL
                                $productID = $arParams['PRODUCT_ID'];
                                $resOffersList = CCatalogSKU::getOffersList($arResult['ID'], 0, array(), array("NAME", "DETAIL_PICTURE"), array());

                                foreach($resOffersList[$arResult['ID']] as $idx =>$value){
                                    $arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_*");
                                    $arFilter = Array("IBLOCK_ID"=>IntVal($value['IBLOCK_ID']),'ID'=>$value['ID'], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
                                    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);

                                    while($ob = $res->GetNextElement()){
                                        $arProps = $ob->GetProperties();
                                        $resOffersList[$arResult['ID']][$idx]['PROPS'] = $arProps;
                                    }
                                }
                                $RESULT_1 = $resOffersList;


                                $COLOR_RANGED_RESULT = [];
                                foreach($RESULT_1[$arResult['ID']] as $offer) {
                                    if ($offer['PROPS']['COLORMIX']['VALUE'] != '') {
                                        $COLOR_RANGED_RESULT['COLORMIX_OFFERS'][] = $offer;
                                    }

                                    if ($offer['PROPS']['COLOR_MONOWHITE']['VALUE'] != '') {
                                        $COLOR_RANGED_RESULT['MONOWHITE_OFFERS'][] = $offer;
                                    }

                                    if ($offer['PROPS']['COLOR_OTMYV']['VALUE'] != '') {
                                        $COLOR_RANGED_RESULT['OTMYV_OFFERS'][] = $offer;
                                    }
                                }

                                ?>


                                <? foreach($COLOR_RANGED_RESULT as $colorTypeName => $colorType) { ?>

                                <div class="settings_block">
                                    <h6>
                                        <? if($colorTypeName == 'COLORMIX_OFFERS') { ?>
                                            Colormix:
                                        <? } else if($colorTypeName == 'MONOWHITE_OFFERS') { ?>
<!--                                        Моноцвет Белый:-->
<!--                                        Моноцвет (на белом цементе):-->
                                            Моноцвет:
                                        <? } else if($colorTypeName == 'OTMYV_OFFERS') { ?>
<!--                                        Моноцвет Серый:-->
<!--                                        Моноцвет (на сером цементе):-->
                                            Отмыв:
                                        <? } ?>

<!--                                        <span class="not-styled">Везувий</span>-->
                                    </h6>

                                    <? if(true) { ?>
                                    <div class="colors">
                                        <? foreach($colorType as $idx => $colorOffer) { ?>
                                            <div class="color">
                                                <a data-fancybox="gallery-<?= $idx . '_' . $colorTypeName ?>" href="<?= CFile::GetFileArray($colorOffer['PROPS']['COLOR_IMG_PREVIEW']['VALUE'])['SRC'] ?>">
                                                    <img src="<?= CFile::GetFileArray($colorOffer['PROPS']['COLOR_IMG_PREVIEW']['VALUE'])['SRC'] ?>" alt="">
                                                </a>

                                                <?
                                                // Hardcode libs
                                                $colormix_names = [
                                                        'belomorie' => 'Беломорье',
                                                        'belgorod' => 'Белгород',
                                                        'vezuviy' => 'Везувий',
                                                        'karakum' => 'Каракум',
                                                        'toscana' => 'Тоскана',
                                                        'praga' => 'Прага',
                                                        'kareliya' => 'Карелия',
                                                        'rodonit' => 'Родонит',
                                                        'turmalin' => 'Турмалин',
                                                        'argentina' => 'Аргентина',
                                                        'golstadt' => 'Гольштадт',
                                                        'andy' => 'Анды',
                                                        'klinker' => 'Клинкер',
                                                        'verona' => 'Верона',
                                                        'cherny_na_belom' => 'Черный',
                                                        'etna' => 'Этна',
                                                ];
                                                $otmyv_names = [
                                                    'bely_granit' => 'Белый гранит',
                                                    'cherny_granit' => 'Черный гранит',
                                                    'bronza' => 'Бронза',
                                                    'bazalt' => 'Базальт',
                                                ];
                                                $monowhite_names = [
                                                        'white' => 'Белый',
                                                        'red' => 'Красный',
                                                        'yellow' => 'Желтый',
                                                        'orange' => 'Оранжевый',
                                                        'kras_na_bel' => 'Красный',
                                                        'sin_na_bel' => 'Синий',
                                                        'pesochniy' => 'Песочный',
                                                        'zhel_na_bel' => 'Желтый',
                                                        'brown' => 'Коричневый',
                                                        'cherny_na_belom' => 'Черный',
                                                        'grafit' => 'Графит',
                                                        'sery' => 'Серый',
                                                        'bely_granit' => 'Белый гранит',
                                                        'cherny_granit' => 'Черный гранит',
                                                ];

                                                // Conditions
                                                if($colorOffer['PROPS']['COLORMIX']['VALUE']) {
                                                    $color_preview_name = $colormix_names[$colorOffer['PROPS']['COLORMIX']['VALUE']];
                                                } elseif($colorOffer['PROPS']['COLOR_OTMYV']['VALUE']) {
                                                    $color_preview_name = $otmyv_names[$colorOffer['PROPS']['COLOR_OTMYV']['VALUE']];
                                                } elseif($colorOffer['PROPS']['COLOR_MONOWHITE']['VALUE']) {
                                                    $color_preview_name = $monowhite_names[$colorOffer['PROPS']['COLOR_MONOWHITE']['VALUE']];
                                                }

                                                // View
                                                if($color_preview_name) { ?>
                                                    <div class="color-preview-name"><?= $color_preview_name ?></div>
                                                <? } ?>
                                            </div>

                                            <? foreach($colorOffer['PROPS']['MORE_PHOTO']['VALUE'] as $colorPhoto) { ?>
                                                <? if(CFile::GetFileArray($colorPhoto)['SRC']) { ?>
                                                <div class="color" style="display:none">
                                                    <a data-fancybox="gallery-<?= $idx . '_' . $colorTypeName ?>" href="<?= CFile::GetFileArray($colorPhoto)['SRC'] ?>">
                                                    </a>
                                                </div>
                                                <? } ?>
                                            <? } ?>

                                        <? } ?>
                                    </div>
                                    <? } ?>

                                </div>

                                <? } ?>



                                <? if(false) { ?>
                                <div class="settings_block">
                                    <h6>
                                        Colormix: Везувий
                                    </h6>
                                    <div class="colors">
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color1.png" alt="">
                                        </div>
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color2.png" alt="">
                                        </div>
                                        <div class="color active">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color3.png" alt="">
                                        </div>
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color4.png" alt="">
                                        </div>
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color5.png" alt="">
                                        </div>
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color6.png" alt="">
                                        </div>
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color7.png" alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="settings_block">
                                    <h6>
                                        Monocolor (на сером цементе)
                                    </h6>
                                    <div class="colors">
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color8.png" alt="">
                                        </div>
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color9.png" alt="">
                                        </div>
                                        <div class="color active">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color10.png" alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="settings_block">
                                    <h6>
                                        Monocolor (на белом цементе)
                                    </h6>
                                    <div class="colors">
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color11.png" alt="">
                                        </div>
                                        <div class="color">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/color12.png" alt="">
                                        </div>
                                    </div>
                                </div>
                                <? } ?>

                            </div>
                        </div>

                        <style>
                            .prop-img-wrap {
                                display: flex !important;
                                juctify-content: center !important;
                                width: 40px;
                            }
                        </style>
                        <div class="technical">
                            <h4>
                                Технические характеристики
                            </h4>
                            <ul>
                                <? if($arResult['PROPERTIES']['MIN_THICKNESS']['VALUE'] !== '') { ?>
                                <li>
                                  <span>
                                      <span class="prop-img-wrap">
                                        <svg width="31" height="20" viewBox="0 0 31 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <g clip-path="url(#clip0_300_2129)">
                                          <path d="M30.5727 6.76826H0.425781V12.5406H30.5727V6.76826Z" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                          <path d="M11.6523 19.0536L15.6788 13.2139L19.3469 19.0704L11.6523 19.0536Z" fill="#003064"/>
                                          <path d="M19.3469 0.0871416L15.3375 5.92682L11.6523 0.0703125L19.3469 0.0871416Z" fill="#003064"/>
                                          <path d="M30.5565 8.92247L27.8438 12.6753" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                          <path d="M8.24017 6.75143L3.85547 12.6753" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                          <path d="M4.81048 6.75143L0.425781 12.6753" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                          <path d="M12.2324 6.75143L7.84766 12.6753" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                          <path d="M15.7812 6.75143L11.3965 12.6753" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                          <path d="M20.0292 6.75143L15.6445 12.6753" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                          <path d="M24.3788 6.75143L19.9941 12.6753" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                          <path d="M28.3378 6.75143L23.9531 12.6753" stroke="#003064" stroke-width="0.729281" stroke-miterlimit="10"/>
                                          </g>
                                          <defs>
                                          <clipPath id="clip0_300_2129">
                                          <rect width="31" height="19" fill="white" transform="translate(0 0.0703125)"/>
                                          </clipPath>
                                          </defs>
                                        </svg>
                                      </span>
                                    <span class="prop-title">Толщина, мм</span>
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['MIN_THICKNESS']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>


                                <? if($arResult['PROPERTIES']['SIDES_RATIO']['VALUE'] !== '') { ?>
                                <li>
                                  <span>
                                      <span class="prop-img-wrap">
                                        <svg width="30" height="28" viewBox="0 0 30 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M20.9896 26.1385H1.87357C1.45557 26.1385 1.11719 25.8074 1.11719 25.3993V6.69685C1.11719 6.28872 1.45557 5.95768 1.87357 5.95768H20.9896C21.4067 5.95768 21.7451 6.28872 21.7451 6.69685V25.3993C21.7451 25.8074 21.4067 26.1385 20.9896 26.1385Z" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M2.45741 7.16043H0V4.75549H2.45741V7.16043Z" fill="#003064"/>
                                          <path d="M2.45741 27.0703H0V24.6662H2.45741V27.0703Z" fill="#003064"/>
                                          <path d="M22.975 7.16046H20.5176V4.75552H22.975V7.16046Z" fill="#003064"/>
                                          <path d="M22.975 27.0703H20.5176V24.6662H22.975V27.0703Z" fill="#003064"/>
                                          <path d="M27.4082 6.51466V25.2269" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10"/>
                                          <path d="M26.3331 6.31123H28.4844L27.4087 5.14283L26.3331 6.31123ZM28.8419 6.6228H25.9756C25.9126 6.6228 25.8562 6.5871 25.8305 6.53112C25.8048 6.47432 25.8147 6.4086 25.857 6.36235L27.2901 4.8053C27.3507 4.73957 27.466 4.73957 27.5265 4.8053L28.9597 6.36235C29.002 6.4086 29.0127 6.47432 28.987 6.53112C28.9613 6.5871 28.9049 6.6228 28.8419 6.6228Z" fill="#003064"/>
                                          <path d="M26.3331 25.4089H28.4844L27.4087 26.5773L26.3331 25.4089ZM28.8419 25.0973H25.9756C25.9126 25.0973 25.8562 25.1338 25.8305 25.1898C25.8048 25.2458 25.8147 25.3115 25.857 25.3578L27.2901 26.9148C27.3507 26.9805 27.466 26.9805 27.5265 26.9148L28.9597 25.3578C29.002 25.3115 29.0127 25.2458 28.987 25.1898C28.9613 25.1338 28.9049 25.0973 28.8419 25.0973Z" fill="#003064"/>
                                          <path d="M1.79883 1.62811H20.9256" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10"/>
                                          <path d="M1.58968 2.6797V0.575776L0.39457 1.62814L1.58968 2.6797ZM1.90816 0.226068V3.03022C1.90816 3.09107 1.87084 3.14706 1.81361 3.1714C1.75639 3.19737 1.68921 3.18682 1.64276 3.14544L0.0503839 1.74417C-0.0167946 1.68413 -0.0167946 1.57135 0.0503839 1.51212L1.64276 0.110039C1.68921 0.0686588 1.75639 0.0589223 1.81361 0.0840755C1.87084 0.108417 1.90816 0.164403 1.90816 0.226068Z" fill="#003064"/>
                                          <path d="M21.1114 2.67967V0.575745L22.3066 1.62811L21.1114 2.67967ZM20.793 0.226038V3.03019C20.793 3.09104 20.8303 3.14703 20.8875 3.17137C20.9447 3.19734 21.0119 3.18679 21.0592 3.14541L22.6507 1.74414C22.7179 1.6841 22.7179 1.57132 22.6507 1.51209L21.0592 0.110009C21.0119 0.0686283 20.9447 0.0588918 20.8875 0.0840449C20.8303 0.108387 20.793 0.164372 20.793 0.226038Z" fill="#003064"/>
                                        </svg>
                                      </span>
                                    <span class="prop-title">Соотношение габаритов не более</span>
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['SIDES_RATIO']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>


                                <? if($arResult['PROPERTIES']['STRENGTH_CLASS_CONCRATE_COMPR']['VALUE'] !== '') { ?>
                                <li>
                                  <span>
                                      <span class="prop-img-wrap">
                                        <svg width="37" height="29"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.24 13.24"><defs><style>.cls-1{fill:#1e355e;}</style></defs><title>давление</title><g id="Слой_2" data-name="Слой 2"><g id="Слой_1-2" data-name="Слой 1"><path class="cls-1" d="M9.15,6.55A.18.18,0,0,1,9,6.37V3.07a.18.18,0,0,1,.18-.18.18.18,0,0,1,.17.18v3.3A.18.18,0,0,1,9.15,6.55Z"/><path class="cls-1" d="M10.5,6.25H7.8a.14.14,0,0,0-.14.09.14.14,0,0,0,0,.16L9,8a.14.14,0,0,0,.22,0l1.35-1.5a.16.16,0,0,0,0-.16A.15.15,0,0,0,10.5,6.25Zm-2.37.3h2l-1,1.13Z"/><path class="cls-1" d="M13.62,9.33H4.68a.83.83,0,0,1-.82-1L5.29,2.42a.85.85,0,0,1,.82-.64h6.14a.84.84,0,0,1,.82.65L14.44,8.3a.82.82,0,0,1-.16.71A.81.81,0,0,1,13.62,9.33ZM6.11,2.25a.39.39,0,0,0-.37.29L4.31,8.4a.36.36,0,0,0,.07.32.36.36,0,0,0,.3.15h8.94a.39.39,0,0,0,.3-.14A.41.41,0,0,0,14,8.41L12.61,2.54a.37.37,0,0,0-.36-.29Z"/><path class="cls-1" d="M11,1.94h-.47a1.48,1.48,0,0,0-3,0H7.12a1.95,1.95,0,0,1,3.89,0Z"/><path class="cls-1" d="M18,10.51H.24A.23.23,0,0,1,0,10.28.24.24,0,0,1,.22,10H18a.24.24,0,1,1,0,.47Z"/><path class="cls-1" d="M18,11.64H.24a.11.11,0,0,1-.12-.11.1.1,0,0,1,.11-.12H18a.11.11,0,0,1,.12.11A.12.12,0,0,1,18,11.64Z"/><path class="cls-1" d="M18,13.24H.24q-.12,0-.12-.12c0-.06,0-.11.11-.12H18a.12.12,0,0,1,.12.12A.12.12,0,0,1,18,13.24Z"/><path class="cls-1" d="M.87,13.24a.18.18,0,0,1-.09,0,.12.12,0,0,1,0-.17l1.68-1.55a.12.12,0,0,1,.17,0,.11.11,0,0,1,0,.16L1,13.2A.09.09,0,0,1,.87,13.24Z"/><path class="cls-1" d="M3,13.24a.13.13,0,0,1-.09,0,.12.12,0,0,1,0-.17l1.68-1.55a.11.11,0,0,1,.16,0,.1.1,0,0,1,0,.16L3.06,13.2A.09.09,0,0,1,3,13.24Z"/><path class="cls-1" d="M5.1,13.24A.13.13,0,0,1,5,13l1.69-1.55a.11.11,0,1,1,.15.17L5.18,13.2A.09.09,0,0,1,5.1,13.24Z"/><path class="cls-1" d="M7.22,13.24a.18.18,0,0,1-.09,0,.12.12,0,0,1,0-.17l1.68-1.55a.12.12,0,0,1,.17,0,.11.11,0,0,1,0,.16L7.3,13.2A.1.1,0,0,1,7.22,13.24Z"/><path class="cls-1" d="M9.33,13.24A.13.13,0,0,1,9.25,13l1.69-1.55a.12.12,0,0,1,.16.17L9.41,13.2A.09.09,0,0,1,9.33,13.24Z"/><path class="cls-1" d="M11.45,13.24a.18.18,0,0,1-.09,0,.12.12,0,0,1,0-.17l1.68-1.55a.12.12,0,0,1,.17,0,.11.11,0,0,1,0,.16L11.53,13.2A.09.09,0,0,1,11.45,13.24Z"/><path class="cls-1" d="M13.57,13.24a.13.13,0,0,1-.09,0,.12.12,0,0,1,0-.17l1.68-1.55a.12.12,0,0,1,.17,0,.11.11,0,0,1,0,.16L13.64,13.2A.09.09,0,0,1,13.57,13.24Z"/><path class="cls-1" d="M15.68,13.24A.13.13,0,0,1,15.6,13l1.69-1.55a.11.11,0,0,1,.16,0,.1.1,0,0,1,0,.16L15.76,13.2A.09.09,0,0,1,15.68,13.24Z"/></g></g></svg>
                                      </span>
                                    <span class="prop-title">Класс по прочности на сжатие бетона</span>
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['STRENGTH_CLASS_CONCRATE_COMPR']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>


                                <? if($arResult['PROPERTIES']['STRENGTH_CLASS_STRETCHING_BEND']['VALUE'] !== '') { ?>
                                <li>
                                  <span>
                                      <span class="prop-img-wrap">
                                        <svg width="37" height="29" viewBox="0 0 37 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M1 23.3657C13.262 16.2231 25.4014 16.9091 36 23.3657" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M5.66211 27.0016L6.44601 23.7201" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M29.6836 26.269L31.3666 24.1436" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M2.14062 25.8052C14.6953 18.8916 23.1065 19.4883 34.0434 25.3157" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M3.70312 28.0703C15.0276 21.8345 22.614 22.372 32.4793 27.6295" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M9.23438 25.3605L10.1196 22.3315" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M13.0625 24.2275L13.8014 21.296" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M16.7871 23.4767L17.6299 20.8147" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M20.5645 23.5402L21.5641 20.9804" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M23.7012 24.1435L24.8848 21.8054" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M26.8496 24.9704L28.3749 22.7614" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M18.6426 5.81916V11.3649" stroke="#003064" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M16.9287 11.6589H20.3553L18.642 13.5489L16.9287 11.6589ZM20.925 11.1547H16.359C16.2592 11.1547 16.1685 11.2132 16.1276 11.3041C16.0868 11.395 16.1031 11.5014 16.171 11.5745L18.4532 14.0944C18.5496 14.2008 18.7344 14.2008 18.8308 14.0944L21.1139 11.5745C21.1801 11.5014 21.1972 11.395 21.1564 11.3041C21.1155 11.2132 21.0256 11.1547 20.925 11.1547Z" fill="#003064"/>
                                          <path d="M10.0962 14.6735L12.5157 4.83718C12.6285 4.38093 13.0397 4.06106 13.5122 4.06106H23.8737C24.3502 4.06106 24.7639 4.3858 24.8726 4.84693L27.1899 14.6832C27.3403 15.3222 26.8523 15.9335 26.1919 15.9335H11.0918C10.4272 15.9335 9.93843 15.3148 10.0962 14.6735Z" stroke="#003064" stroke-miterlimit="10"/>
                                          <path d="M15.6113 3.93857C15.6113 2.35466 16.9045 1.07032 18.5001 1.07032C20.0948 1.07032 21.388 2.35466 21.388 3.93857" stroke="#003064" stroke-miterlimit="10"/>
                                        </svg>
                                      </span>
                                    <span class="prop-title">Класс по прочности на растяжение при изгибе</span>
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['STRENGTH_CLASS_STRETCHING_BEND']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>


                                <? if($arResult['PROPERTIES']['FARM_GATE_STRENGTH']['VALUE'] !== '') { ?>
                                <li>
                                  <span>
                                      <span class="prop-img-wrap">
                                        <svg width="30" height="28" viewBox="0 0 30 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M12.2446 17.2755C12.1178 17.2755 11.9899 17.2755 11.8631 17.1487C11.4816 16.8939 11.1 16.6392 10.8453 16.3845C10.209 15.7482 9.70072 14.9864 9.44598 14.2222C9.3192 13.9686 9.3192 13.7139 9.19124 13.3324C9.06445 12.9497 9.19124 13.3324 9.19124 13.2045C9.19124 12.9497 9.06445 12.5682 9.06445 12.3135C9.06445 10.7886 9.70072 9.38819 10.7185 8.24484L17.2056 1.75684C19.4948 -0.405436 23.0565 -0.405436 25.3468 1.75684C26.3646 2.77459 27.0009 4.30062 27.0009 5.82784C27.0009 7.35387 26.3646 8.75313 25.3468 9.89766L21.4036 13.8407C21.1489 14.0954 20.6406 14.0954 20.3858 13.8407C20.1311 13.586 20.1311 13.0777 20.3858 12.8229L24.329 8.88108C25.0921 8.11689 25.6004 6.97236 25.6004 5.95344C25.6004 4.93686 25.2189 3.79234 24.329 3.02815C22.675 1.37533 20.0043 1.37533 18.3502 3.02815L11.8631 9.51615C11.1 10.2792 10.5906 11.2969 10.5906 12.4414V13.0777C10.5906 13.3324 10.7185 13.4592 10.7185 13.7139C10.9733 14.3502 11.2268 14.8584 11.7363 15.3667C11.9899 15.6215 12.2446 15.8762 12.4994 16.003C12.8809 16.2577 13.0077 16.6392 12.7529 17.0207C12.7529 17.1487 12.4994 17.2755 12.2446 17.2755Z" fill="#003064"/>
                                          <path d="M5.75532 27.0706C4.22922 27.0706 2.8299 26.5611 1.68531 25.4166C0.539561 24.272 0.03125 22.8728 0.03125 21.3456C0.03125 19.8195 0.667519 18.4203 1.68531 17.2758L5.62854 13.3327C5.88211 13.078 6.39159 13.078 6.64634 13.3327C6.8999 13.5874 6.8999 14.0957 6.64634 14.3505L2.70194 18.2923C1.93888 19.0565 1.43057 20.201 1.43057 21.22C1.43057 22.2365 1.8121 23.3811 2.70194 24.1453C4.356 25.7981 7.02786 25.7981 8.68075 24.1453L15.1691 17.6573C15.9321 16.8942 16.4404 15.7485 16.4404 14.732V14.0957C16.4404 13.841 16.3136 13.7142 16.3136 13.4595C16.0589 12.8233 15.8042 12.315 15.2958 11.8067C15.0411 11.5519 14.7875 11.2972 14.5328 11.1704C14.1513 10.9157 14.0233 10.5342 14.278 10.1527C14.5328 9.77118 14.9143 9.64323 15.2958 9.89796C15.6774 10.1527 16.0589 10.4074 16.3136 10.7889C16.9499 11.4252 17.4582 12.187 17.713 12.9512C17.8397 13.2048 17.8397 13.4595 17.9665 13.841V13.969C17.9665 14.2237 18.0945 14.6052 18.0945 14.8599C18.0945 16.3848 17.4582 17.7852 16.4404 18.9286L9.95329 25.4166C8.68075 26.5611 7.28143 27.0706 5.75532 27.0706Z" fill="#003064"/>
                                          <path d="M10.8464 4.55603C10.4649 4.55603 10.2102 4.3013 10.0834 4.04657L9.32031 1.12128C9.32031 0.612988 9.4471 0.230305 9.95541 0.103527C10.3369 -0.0232515 10.7196 0.230305 10.8464 0.612988L11.6095 3.53828C11.7374 3.91979 11.4827 4.3013 11.1012 4.42925C10.9732 4.55603 10.9732 4.55603 10.8464 4.55603Z" fill="#003064"/>
                                          <path d="M16.9521 27.0706C16.5706 27.0706 16.3158 26.8158 16.189 26.5611L15.426 23.6358C15.298 23.2543 15.5528 22.8728 15.9343 22.7448C16.3158 22.6181 16.6973 22.8728 16.8253 23.2543L17.5884 26.1796C17.7151 26.5611 17.4604 26.9438 17.0789 27.0706H16.9521Z" fill="#003064"/>
                                          <path d="M26.2382 17.785H25.9834L23.058 17.022C22.6765 16.8941 22.4217 16.5126 22.5485 16.131C22.6765 15.7495 23.058 15.4948 23.4395 15.6216L26.3649 16.3858C26.7465 16.5126 27.0012 16.8941 26.8744 17.2756C26.8744 17.5303 26.6197 17.785 26.2382 17.785Z" fill="#003064"/>
                                          <path d="M21.9125 22.7454C21.6578 22.7454 21.531 22.6186 21.4042 22.4906L19.2418 20.3284C18.9871 20.0748 18.9871 19.5653 19.2418 19.3106C19.4954 19.0559 20.0049 19.0559 20.2585 19.3106L22.422 21.4729C22.6756 21.7276 22.6756 22.2359 22.422 22.4906C22.2941 22.6186 22.0393 22.7454 21.9125 22.7454Z" fill="#003064"/>
                                          <path d="M7.28557 8.1178C7.15879 8.1178 6.90405 8.1178 6.77726 7.99102L4.61488 5.82874C4.36014 5.44724 4.36014 4.93777 4.61488 4.68304C4.86845 4.42831 5.37794 4.42831 5.63268 4.68304L7.79506 6.84532C8.04863 7.10005 8.04863 7.60834 7.79506 7.86307C7.6671 8.1178 7.54031 8.1178 7.28557 8.1178Z" fill="#003064"/>
                                          <path d="M3.72188 11.6789H3.46714L0.541707 10.9159C0.160181 10.788 -0.0945616 10.4065 0.0333966 10.025C0.160181 9.64344 0.541707 9.38871 0.923234 9.51549L3.84866 10.2797C4.35815 10.2797 4.61289 10.788 4.48493 11.1695C4.35815 11.5522 4.10341 11.6789 3.72188 11.6789Z" fill="#003064"/>
                                        </svg>
                                      </span>
                                    <span class="prop-title">Отпускная прочность</span>
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['FARM_GATE_STRENGTH']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>


                                <? if($arResult['PROPERTIES']['WATER_ABSORB_BY_MASS']['VALUE'] !== '') { ?>
                                <li>
                                  <span>
                                      <span class="prop-img-wrap">
                                        <svg width="40" height="28" viewBox="0 0 40 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M23.9603 7.91698C24.823 9.39022 24.871 11.1852 24.0867 12.718C23.3394 14.1791 22.0039 15.0926 20.4234 15.2215C20.2818 15.2336 20.1369 15.2402 19.9931 15.2402C19.8493 15.2402 19.7055 15.2336 19.565 15.2215C17.9844 15.0926 16.6479 14.1791 15.9017 12.718C15.1174 11.1852 15.1642 9.39022 16.027 7.91808L19.9931 1.15019L23.9603 7.91698ZM19.6837 0.248831L15.4072 7.54563C14.4159 9.23595 14.3614 11.2943 15.2612 13.0529C16.1108 14.7179 17.699 15.8 19.5073 15.9498C19.8275 15.9741 20.1598 15.9741 20.4822 15.9487C22.2883 15.8 23.8754 14.7179 24.7261 13.0529C25.6259 11.2943 25.5714 9.23595 24.5801 7.54563L20.3035 0.248831C20.2393 0.13864 20.1205 0.070323 19.9931 0.070323C19.8667 0.070323 19.748 0.13864 19.6837 0.248831Z" fill="#003064"/>
                                          <path d="M19.7604 12.9875L19.7909 13.522V12.9887C19.9761 12.9887 20.134 12.8399 20.1493 12.6526C20.1656 12.4531 20.0175 12.2757 19.8181 12.2603C19.1929 12.2085 18.6646 11.846 18.3683 11.2664C18.0556 10.6537 18.0731 9.93748 18.4184 9.35017C18.5197 9.17717 18.463 8.95348 18.292 8.85101C18.2343 8.81574 18.1711 8.80032 18.109 8.80032C17.9859 8.80032 17.865 8.86423 17.7975 8.97772C17.3258 9.78322 17.2986 10.7639 17.7278 11.6003C18.1341 12.398 18.8944 12.9159 19.7604 12.9875Z" fill="#003064"/>
                                          <path d="M1 19.7842C1.35138 19.7677 39 19.7842 39 19.7842" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M1 23.0777C1.35138 23.0611 39 23.0777 39 23.0777" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M1 27.0703C1.35138 27.0538 39 27.0703 39 27.0703" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M1.61523 27.0703L5.87984 23.0891" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M6.97266 27.0703L11.2373 23.0891" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M12.332 27.0703L16.5966 23.0891" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M17.6875 27.0703L21.9521 23.0891" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M23.0469 27.0703L27.3115 23.0891" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M28.4043 27.0703L32.6689 23.0891" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M33.7637 27.0703L38.0283 23.0891" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M5.52539 3.20068V11.6743" stroke="#003064" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M2.95394 12.1247H8.09434L5.52469 15.0139L2.95394 12.1247ZM8.94944 11.3545H2.09993C1.94961 11.3545 1.81345 11.4438 1.75136 11.5826C1.69036 11.7225 1.7165 11.8834 1.81672 11.9969L5.24147 15.847C5.38526 16.0089 5.66303 16.0089 5.80791 15.847L9.23266 11.9969C9.33287 11.8845 9.35793 11.7225 9.29693 11.5826C9.23593 11.4438 9.09977 11.3545 8.94944 11.3545Z" fill="#003064"/>
                                          <path d="M34.4492 3.20068V11.6743" stroke="#003064" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M31.8778 12.1247H37.0193L34.4485 15.0139L31.8778 12.1247ZM37.8733 11.3545H31.0238C30.8734 11.3545 30.7373 11.4438 30.6752 11.5826C30.6142 11.7225 30.6403 11.8834 30.7405 11.9969L34.1653 15.847C34.3102 16.0089 34.5869 16.0089 34.7317 15.847L38.1565 11.9969C38.2567 11.8845 38.2828 11.7225 38.2208 11.5826C38.1598 11.4438 38.0247 11.3545 37.8733 11.3545Z" fill="#003064"/>
                                        </svg>
                                      </span>
                                    <span class="prop-title">Водопоглощение по массе</span>
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['WATER_ABSORB_BY_MASS']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>


                                <? if($arResult['PROPERTIES']['CONCRATE_FREEZE_STRENGTH']['VALUE'] !== '') { ?>
                                <li>
                                  <span>
                                      <span class="prop-img-wrap">
                                        <svg width="34" height="25" viewBox="0 0 34 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M1 19.3491C1.2959 19.3372 33 19.3491 33 19.3491" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M1 21.6404C1.2959 21.6286 33 21.6404 33 21.6404" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M1 24.5703C1.2959 24.5585 33 24.5703 33 24.5703" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M2.14844 24.5703L5.18104 21.717" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M5.95703 24.5703L8.98964 21.717" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M9.76953 24.5703L12.8021 21.717" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M13.5781 24.5703L16.6107 21.717" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M17.3887 24.5703L20.4213 21.717" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M21.2012 24.5703L24.2338 21.717" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M25.0098 24.5703L28.0424 21.717" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M28.8203 24.5703L31.8529 21.717" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M10.8252 2.6519C11.778 2.8746 15.0655 3.42189 17.3521 1.09927C19.6341 3.42189 22.9208 2.8746 23.8736 2.6519H23.8751V7.69518C23.8806 9.10486 23.5173 10.4901 22.8232 11.7094C21.8828 13.3379 20.1717 15.3991 17.349 15.9953C14.5271 15.3991 12.816 13.3379 11.8756 11.711C11.1808 10.4916 10.8175 9.10486 10.8237 7.69518L10.8252 2.6519ZM17.2971 16.5276C17.3304 16.5347 17.3653 16.5347 17.3994 16.5276C20.4428 15.9069 22.2725 13.7122 23.2725 11.9787C24.0146 10.678 24.4026 9.19963 24.3972 7.69518V2.6519C24.3972 2.35732 24.1633 2.11882 23.8751 2.11882C23.8356 2.11882 23.7954 2.12356 23.7566 2.13304C22.8689 2.33995 19.8131 2.8517 17.7216 0.722559C17.5133 0.519596 17.1848 0.519596 16.9772 0.722559C14.8858 2.8517 11.8291 2.33995 10.9422 2.13304C10.661 2.0667 10.3806 2.24439 10.3148 2.53106C10.3055 2.56976 10.3008 2.61083 10.3008 2.6519V7.69518C10.2954 9.19963 10.6843 10.6788 11.4256 11.9803C12.4256 13.7122 14.256 15.9069 17.2971 16.5276Z" fill="#003064"/>
                                          <path d="M11.8698 3.89676C12.1758 3.92519 12.4833 3.94019 12.7862 3.94019C14.4206 3.97652 16.0225 3.4703 17.3517 2.49971C18.681 3.4703 20.2829 3.97573 21.9173 3.94019C22.221 3.94019 22.5277 3.92519 22.8337 3.89676V7.69541C22.8391 8.91476 22.5254 10.1152 21.925 11.1703C21.1226 12.5594 19.681 14.3284 17.3517 14.9042C15.0225 14.3284 13.5802 12.5586 12.7777 11.1695C12.1781 10.1144 11.8644 8.91476 11.8698 7.69541V3.89676ZM17.2921 15.4372C17.3316 15.4467 17.3719 15.4467 17.4106 15.4372C19.9552 14.8339 21.5145 12.9298 22.3751 11.4404C23.0219 10.3023 23.3604 9.01032 23.3558 7.69541V3.59902C23.3558 3.52242 23.3232 3.45055 23.2675 3.40001C23.2117 3.34947 23.1365 3.32498 23.063 3.33525C22.6826 3.38342 22.3 3.40712 21.9173 3.40712C20.3301 3.44739 18.777 2.93485 17.5136 1.95557C17.4191 1.87897 17.2843 1.87897 17.1898 1.95557C15.9257 2.93564 14.3734 3.44818 12.7862 3.40791C12.4027 3.40791 12.0209 3.38422 11.6405 3.33604C11.5662 3.32577 11.4918 3.35026 11.436 3.4008C11.3803 3.45134 11.3477 3.52321 11.3477 3.59902V7.69541C11.3423 9.00953 11.6808 10.3015 12.3284 11.4396C13.189 12.9298 14.7483 14.8339 17.2921 15.4372Z" fill="#003064"/>
                                          <path d="M14.8464 9.71566L13.7665 9.24498C13.6333 9.18733 13.4799 9.25051 13.4234 9.38555C13.3668 9.52139 13.428 9.67697 13.5613 9.73462L14.2809 10.0489L13.8293 10.3151C13.7038 10.3877 13.6604 10.5504 13.7309 10.6776C13.8022 10.8055 13.9617 10.8505 14.0872 10.7779C14.088 10.7771 14.0888 10.7763 14.0903 10.7763L14.5419 10.5094L14.449 11.3023C14.4312 11.4484 14.5334 11.581 14.6767 11.5984C14.6868 11.6 14.6976 11.6008 14.7085 11.6008C14.8402 11.6 14.9509 11.4997 14.9672 11.3662L15.1074 10.1769L17.0896 9.00963V11.3441L16.15 12.0628C16.0346 12.1504 16.0106 12.3171 16.0974 12.4347C16.1841 12.5524 16.3476 12.5761 16.463 12.4884L17.0896 12.0091V12.5414C17.0896 12.6882 17.2066 12.8075 17.3507 12.8075C17.4948 12.8075 17.6117 12.6882 17.6117 12.5414V12.0091L18.2384 12.4884C18.3538 12.5761 18.5172 12.5532 18.604 12.4347C18.69 12.3179 18.6667 12.1512 18.5513 12.0628L17.6117 11.3441V9.00963L19.594 10.1769L19.7342 11.3662C19.7497 11.4997 19.8612 11.6 19.9929 11.6008C20.0037 11.6008 20.0138 11.6 20.0246 11.5984C20.1679 11.581 20.2694 11.4484 20.2524 11.303L20.1594 10.5094L20.611 10.7763C20.7357 10.8505 20.8953 10.8079 20.9681 10.6807C21.0409 10.5544 20.9991 10.3909 20.8752 10.3167C20.8736 10.3159 20.8728 10.3159 20.8721 10.3151L20.4205 10.0489L21.1401 9.73462C21.2725 9.67697 21.3345 9.52139 21.278 9.38555C21.2206 9.25051 21.0673 9.18733 20.9348 9.24498L19.855 9.71566L17.8728 8.54922L19.855 7.38277L20.9348 7.85346C21.0673 7.91111 21.2206 7.84793 21.278 7.71288C21.3345 7.57784 21.2725 7.42147 21.1401 7.36303L20.4205 7.0495L20.8721 6.78336C20.996 6.70912 21.0378 6.54644 20.965 6.41929C20.893 6.29372 20.735 6.25028 20.611 6.32294L20.1594 6.58908L20.2524 5.79618C20.2694 5.65087 20.1679 5.51819 20.0246 5.50003C19.8813 5.48266 19.7512 5.58611 19.7342 5.733L19.594 6.92156L17.6117 8.08801V5.75433L18.5513 5.03645C18.6667 4.948 18.69 4.78137 18.6032 4.6637C18.5172 4.54602 18.3538 4.52233 18.2384 4.60999L17.6117 5.09016V4.55708C17.6117 4.41019 17.4948 4.29094 17.3507 4.29094C17.2066 4.29094 17.0896 4.41019 17.0896 4.55708V5.09016L16.463 4.60999C16.3476 4.52233 16.1841 4.54602 16.0974 4.6637C16.0114 4.78137 16.0346 4.948 16.15 5.03645L17.0896 5.75433V8.08801L15.1074 6.92156L14.9672 5.733C14.9502 5.58611 14.82 5.48266 14.6767 5.50003C14.5334 5.51819 14.4312 5.65087 14.449 5.79618L14.5419 6.58908L14.0903 6.32294C13.9656 6.24791 13.8061 6.29056 13.7332 6.41771C13.6604 6.54407 13.7015 6.70754 13.8262 6.78178C13.827 6.78257 13.8285 6.78336 13.8293 6.78336L14.2809 7.0495L13.5613 7.36303C13.428 7.42147 13.3668 7.57784 13.4234 7.71288C13.4799 7.84793 13.6333 7.91111 13.7665 7.85346L14.8464 7.38277L16.8286 8.54922L14.8464 9.71566Z" fill="#003064"/>
                                        </svg>
                                      </span>
                                    <span class="prop-title">Марка бетона по морозостойкости</span>
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['CONCRATE_FREEZE_STRENGTH']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>


                                <? if($arResult['PROPERTIES']['MARK_BY_ABRASION']['VALUE'] !== '') { ?>
                                <li>
                                  <span>
                                      <span class="prop-img-wrap">
                                        <svg width="39" height="30" viewBox="0 0 39 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M1 23.7109C1.34214 23.6976 38 23.7109 38 23.7109" stroke="#003064" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M1 26.2823C1.34214 26.269 38 26.2823 38 26.2823" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M1 29.5703C1.34214 29.557 38 29.5703 38 29.5703" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M2.32812 29.5703L5.83458 26.3682" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M6.73242 29.5703L10.2389 26.3682" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M11.1387 29.5703L14.6451 26.3682" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M15.543 29.5703L19.0494 26.3682" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M19.9512 29.5703L23.4576 26.3682" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M24.3574 29.5703L27.8639 26.3682" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M28.7617 29.5703L32.2682 26.3682" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M33.168 29.5703L36.6744 26.3682" stroke="#003064" stroke-width="0.5" stroke-miterlimit="10" stroke-linecap="round"/>
                                          <path d="M17.6915 10.978C17.6915 10.0634 18.4375 9.3251 19.3627 9.3251C20.287 9.3251 21.034 10.0634 21.034 10.978C21.034 11.8926 20.287 12.6318 19.3627 12.6318C18.4375 12.6318 17.6915 11.8926 17.6915 10.978ZM26.4043 9.3251C26.3792 9.21698 26.3479 9.11506 26.3192 9.01136C26.3103 8.97591 26.3013 8.94135 26.2923 8.90501C27.3841 8.07901 27.7191 6.54666 27.0053 5.33337L26.7267 4.8601L24.6327 6.06099C24.5324 5.95109 24.4205 5.85183 24.3201 5.75257C24.3354 5.71712 24.3488 5.68167 24.3622 5.64622C24.4142 5.50441 24.4536 5.35907 24.4814 5.21195C24.502 5.10116 24.5154 4.99038 24.5226 4.87871C24.5243 4.84149 24.5261 4.80427 24.527 4.76704C24.5288 4.6926 24.527 4.61726 24.5226 4.54282C24.5109 4.3567 24.4805 4.17059 24.4321 3.98802C24.3255 3.58565 24.1303 3.22051 23.866 2.91386C23.7774 2.81194 23.6815 2.71623 23.5785 2.6276C23.5436 2.59747 23.5087 2.56911 23.4729 2.54075C23.4003 2.48491 23.3251 2.43262 23.2472 2.38388C23.2086 2.35906 23.1683 2.33513 23.128 2.31298L22.6489 2.03735L21.4353 4.12096C21.2902 4.07664 21.1567 4.04385 21.0116 4.01106C20.9785 3.76468 20.9131 3.52805 20.8209 3.30471C20.7868 3.22051 20.7483 3.13809 20.7062 3.05833C20.6641 2.97945 20.6184 2.90146 20.5701 2.82612C20.4877 2.70027 20.3963 2.58152 20.2969 2.47162C20.2369 2.40603 20.1742 2.34311 20.1079 2.28196C20.064 2.24296 20.0193 2.20485 19.9727 2.16763C19.8804 2.09318 19.7828 2.02494 19.6807 1.96379C19.6296 1.93366 19.5777 1.9053 19.5249 1.87782C19.472 1.85035 19.4174 1.82553 19.3627 1.8016C19.0287 1.65803 18.6498 1.57029 18.2486 1.57029H17.6915V4.01106C17.5464 4.04385 17.4129 4.08816 17.2678 4.12096C16.4322 3.0406 14.8836 2.71002 13.6584 3.41549L13.1792 3.69023L14.3937 5.7632C14.2817 5.86335 14.1823 5.97325 14.0811 6.07251C13.5133 5.84031 12.9007 5.80752 12.2988 5.96173C11.5751 6.14962 10.9732 6.61225 10.6051 7.25213L10.3275 7.72628L12.4331 8.92805C12.3884 9.07163 12.3552 9.2028 12.3212 9.34637C10.9508 9.52274 9.85547 10.6802 9.85547 12.0805V12.6318H12.3212C12.3552 12.7753 12.3991 12.9074 12.4331 13.051C11.3404 13.877 11.0064 15.4102 11.7202 16.6226L11.9979 17.0968L14.0928 15.895C14.1931 16.0058 14.3041 16.1041 14.4045 16.2034C14.1707 16.7662 14.1375 17.3839 14.2934 17.968C14.4824 18.6841 14.9508 19.2796 15.5966 19.643L16.0757 19.9195L17.2902 17.835C17.4353 17.8793 17.5688 17.9121 17.7139 17.9458C17.8473 18.9269 18.4823 19.7795 19.3627 20.1544C19.6968 20.2979 20.0757 20.3857 20.4769 20.3857H21.034V17.9458C21.1791 17.9121 21.3126 17.8687 21.4577 17.835C22.2924 18.9163 23.841 19.2353 25.0671 18.5405L25.5463 18.2657L24.3318 16.1928C24.3587 16.1688 24.3846 16.1449 24.4097 16.1201C24.4912 16.0412 24.5673 15.957 24.6435 15.8728C25.6752 16.3035 26.862 16.0634 27.6475 15.3083C27.8284 15.1337 27.9878 14.9316 28.1195 14.7047L28.398 14.2306L26.2923 13.0279C26.3371 12.8852 26.3703 12.7532 26.4043 12.6096C26.9184 12.544 27.3931 12.3393 27.7863 12.0362C27.789 12.0335 27.7917 12.0318 27.7952 12.03C27.8382 11.9963 27.8794 11.9609 27.9206 11.9254C28.0863 11.7809 28.2359 11.6179 28.364 11.4388C28.381 11.4131 28.3989 11.3883 28.4159 11.3635C28.5879 11.1056 28.7169 10.8185 28.793 10.5118C28.8431 10.3071 28.87 10.0944 28.87 9.87547V9.3251H26.4043Z" stroke="#003064" stroke-width="0.75" stroke-miterlimit="10"/>
                                        </svg>
                                      </span>
                                    <span class="prop-title">Марка по истираемости</span>
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['MARK_BY_ABRASION']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>

                                <? if($arResult['PROPERTIES']['CONCRATE_CLASS']['VALUE'] !== '') { ?>
                                <li>
                                  <span>
                                    Класс бетона
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['CONCRATE_CLASS']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>




                                <? if(false) { ?>
                                <li>
                                  <span>
                                    <svg width="30" height="28" viewBox="0 0 30 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M17.0182 1.31207C15.7952 1.31735 14.7231 1.42306 13.69 1.77181C13.3115 1.89863 12.9438 2.04655 12.6448 2.32C12.4899 2.46135 12.494 2.59352 12.6475 2.73355C12.828 2.89867 13.0381 3.01089 13.2617 3.11129C14.0968 3.48514 14.9857 3.6331 15.8909 3.69915C17.3105 3.80483 18.714 3.72556 20.0758 3.27642C20.4448 3.15356 20.8017 3.00433 21.0967 2.74145C21.2637 2.59217 21.2583 2.45478 21.094 2.30815C20.9107 2.15114 20.7023 2.0248 20.4771 1.93428C19.3295 1.44286 18.1133 1.33326 17.0182 1.31344M4.9863 13.1563L4.98495 10.6028C4.9836 10.1338 4.74924 9.89739 4.27108 9.88286C4.03941 9.87626 3.80503 9.88816 3.57066 9.87891C3.51322 9.87587 3.45585 9.88572 3.40287 9.90769C3.3499 9.92967 3.30272 9.96312 3.26493 10.0057C2.64804 10.6173 2.02578 11.2276 1.40216 11.8313C1.35345 11.8765 1.31543 11.9317 1.29076 11.9928C1.2661 12.054 1.2554 12.1197 1.2594 12.1854C1.26344 13.2092 1.25804 14.233 1.26343 15.2567C1.26612 15.7904 1.47892 15.9965 2.02846 15.9992C2.93628 16.0031 3.84274 15.9938 4.74921 16.0044C4.94182 16.007 4.99168 15.9542 4.99033 15.7679C4.97955 14.8974 4.9863 14.0268 4.9863 13.1563ZM5.60587 25.8063C6.01955 25.8168 6.43119 25.7463 6.81672 25.5988C7.20226 25.4513 7.55391 25.2299 7.85112 24.9475C8.14833 24.665 8.38514 24.3274 8.54763 23.9541C8.71013 23.5809 8.79505 23.1795 8.79747 22.7736C8.79989 22.3678 8.71977 21.9655 8.56173 21.5904C8.4037 21.2153 8.17092 20.875 7.8771 20.5892C7.58327 20.3034 7.2343 20.0779 6.85055 19.926C6.46681 19.7741 6.05602 19.6989 5.64225 19.7046C4.8195 19.6997 4.02828 20.0147 3.44184 20.5807C2.85541 21.1467 2.52155 21.9175 2.51337 22.7245C2.50911 23.1263 2.58583 23.5251 2.7392 23.8978C2.89257 24.2705 3.11956 24.6098 3.40707 24.8962C3.69457 25.1826 4.03696 25.4104 4.41454 25.5666C4.79212 25.7228 5.19744 25.8042 5.60723 25.8063M17.9584 20.9186C20.6657 20.9186 23.3703 20.908 26.0749 20.9212C26.9531 20.9252 27.5053 20.3426 27.4972 19.5368C27.4703 17.0559 27.4891 14.5751 27.4864 12.0943C27.4864 11.7812 27.5982 11.3558 27.4353 11.1828C27.2521 10.9846 26.8116 11.147 26.4857 11.1166C26.2877 11.0981 26.2459 11.1709 26.2473 11.3545C26.2567 12.2739 26.2473 13.192 26.2527 14.1114C26.2554 14.5024 26.1584 14.867 25.9793 15.2118C25.5334 16.0797 24.8142 16.6914 24.002 17.2C22.0692 18.4179 19.9128 18.9358 17.6486 19.0639C16.2935 19.1418 14.9338 19.0306 13.6106 18.7337C11.7963 18.3242 10.1005 17.6637 8.70782 16.4047C7.93334 15.7059 7.42016 14.8829 7.49289 13.7891C7.53061 13.2276 7.50232 12.6596 7.49828 12.0943C7.49693 11.7799 7.4875 11.4654 7.48077 11.1497C7.14404 11.0836 6.80461 11.1431 6.46923 11.118C6.29817 11.1048 6.26046 11.1669 6.26046 11.3254C6.2645 13.653 6.2645 15.9807 6.26046 18.307C6.26046 18.4695 6.30087 18.5329 6.47597 18.5685C7.78921 18.838 8.78458 19.546 9.43783 20.6967C9.53751 20.871 9.64931 20.9212 9.84596 20.9212C12.5506 20.9146 15.2551 20.9172 17.9597 20.9172M24.9893 11.155C24.9893 10.3598 24.9825 9.56444 24.9893 8.77051C25 7.84712 24.7859 6.9832 24.2336 6.23419C23.5777 5.33986 22.7857 4.55255 22.0745 3.70181C21.9843 3.59349 21.9318 3.66219 21.8671 3.71504C21.2462 4.2223 20.5068 4.47594 19.739 4.66089C18.2277 5.01944 16.6601 5.08941 15.1218 4.86697C13.95 4.70845 12.8011 4.4627 11.8569 3.68859C11.7572 3.60537 11.7141 3.66215 11.6535 3.73084C11.0029 4.48117 10.3362 5.21969 9.70049 5.98456C9.12132 6.6847 8.79131 7.4984 8.78053 8.40462C8.76168 10.2038 8.78458 12.0057 8.75225 13.8049C8.74148 14.4404 8.97855 14.9304 9.4163 15.3611C10.2298 16.1642 11.2198 16.6861 12.2893 17.0665C14.7918 17.9595 17.3523 18.0546 19.937 17.5104C21.4145 17.1986 22.7951 16.6438 23.9777 15.69C24.6512 15.1484 25.0741 14.5143 25.0014 13.5949C24.9394 12.7864 24.9879 11.9674 24.9879 11.1536M0 11.4918C0.587253 10.7758 1.28767 10.1668 1.94765 9.51955C3.06424 8.42311 2.61169 8.65165 4.14313 8.61466C5.03209 8.59484 5.69208 8.92777 6.08268 9.72435C6.1406 9.84324 6.22139 9.83529 6.31702 9.83529C6.6497 9.83529 6.98242 9.82603 7.31376 9.83924C7.47539 9.84585 7.49961 9.78374 7.50231 9.64372C7.51578 9.05983 7.47 8.47725 7.54542 7.89336C7.69359 6.74804 8.17846 5.76129 8.93677 4.89471C9.67757 4.04133 10.4237 3.19318 11.1646 2.34113C11.2325 2.27303 11.2873 2.19337 11.3262 2.10602C11.5552 1.48514 12.0441 1.12452 12.6219 0.856358C13.9931 0.222271 15.4626 0.0716335 16.9496 0.0703125C18.12 0.0703125 19.2824 0.194507 20.4017 0.568354C20.8812 0.728196 21.3499 0.913151 21.7527 1.22491C22.0409 1.44684 22.2806 1.70444 22.3965 2.05716C22.4369 2.18662 22.5352 2.28303 22.6241 2.38475C23.4228 3.30285 24.235 4.20777 25.0122 5.14304C25.8322 6.11679 26.2715 7.34527 26.2513 8.60805C26.2513 8.95283 26.258 9.29893 26.2486 9.64372C26.2446 9.78639 26.2756 9.84717 26.4372 9.83924C26.7201 9.82603 27.0056 9.82603 27.2884 9.83924C27.4635 9.84849 27.4972 9.77851 27.4905 9.62791C27.4851 9.44561 27.4689 9.26591 27.5268 9.08625C27.6171 8.80884 27.842 8.62394 28.1195 8.61998C28.2564 8.62113 28.3892 8.66595 28.4979 8.74769C28.6065 8.82944 28.685 8.94362 28.7215 9.07303C28.7579 9.18532 28.766 9.29763 28.766 9.41256C28.7647 12.8036 28.77 16.1947 28.7633 19.5844C28.7606 20.5804 28.3269 21.3545 27.4245 21.838C27.2117 21.953 27.3289 22.0375 27.3962 22.1445L29.7896 25.9107C29.8578 26.0149 29.9151 26.1256 29.9607 26.241C30.0096 26.3667 30.013 26.5051 29.9703 26.633C29.9276 26.7609 29.8415 26.8706 29.7263 26.9437C29.6145 27.0234 29.4779 27.0626 29.3399 27.0547C29.2019 27.0467 29.071 26.9921 28.9694 26.9001C28.8563 26.805 28.7862 26.6783 28.7081 26.5567C27.8178 25.1564 26.9234 23.7574 26.0385 22.3519C25.9415 22.1973 25.8432 22.1326 25.6479 22.1326C20.5121 22.1379 15.3764 22.1366 10.2392 22.1379C10.0911 22.1379 9.93213 22.0877 9.96311 22.3545C10.122 23.6755 9.68969 24.8169 8.7509 25.7456C7.52386 26.9623 6.01938 27.3612 4.35325 26.8579C2.66692 26.3493 1.62978 25.1801 1.30922 23.4774C0.880901 21.192 2.42041 19.0269 4.75595 18.5579C4.91758 18.5249 5.0065 18.4721 4.99169 18.2818C4.97171 18.0048 4.97125 17.7267 4.99033 17.4496C5.00245 17.2646 4.93644 17.229 4.76134 17.2304C3.87238 17.2396 2.98344 17.2356 2.09448 17.2356C1.04389 17.2356 0.443159 16.834 0.0673702 15.8802C0.0525542 15.8446 0.045795 15.8089 0 15.8023V11.4918Z" fill="#003064"/>
                                      <path d="M6.87906 22.7616C6.87745 23.0878 6.74377 23.4 6.50747 23.6296C6.27117 23.8591 5.95157 23.9871 5.619 23.9855C5.28642 23.9839 4.96813 23.8528 4.7341 23.6211C4.50007 23.3893 4.3695 23.0759 4.37111 22.7497C4.37272 22.4237 4.50627 22.1117 4.74245 21.8823C4.97862 21.6528 5.29807 21.5249 5.63047 21.5264C5.96286 21.528 6.28101 21.6591 6.51491 21.8907C6.74882 22.1224 6.87931 22.4356 6.8777 22.7616" fill="#003064"/>
                                    </svg>
                                    Класс бетона по прочности
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['CONCRETE_STRENGTH_CLASS']['VALUE'] ?>
                                    </b>
                                </li>
                                <li>
                                  <span>
                                    Кол-во рядов на транспортном поддоне
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['S_RYADA']['VALUE'] ?>
                                    </b>
                                </li>
                                <li>
                                  <span>
                                    Масса паллета, кг 
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['PALLET_QUANTITY']['VALUE'] ?>
                                    </b>
                                </li>
                                <li>
                                  <span>
                                    Кол-во на поддоне, м² 
                                  </span>
                                    <div class="doted"></div>
                                    <b>
                                        <?= $arResult['PROPERTIES']['RYAD_PODDON']['VALUE'] ?>
                                    </b>
                                </li>
                                <? } ?>
                            </ul>
                            <div class="technical_btns">

                                <? if(false) { ?>
                                    <a href="/services/calc/" target="_blank" class="btn_blue">
                                        КАЛЬКУЛЯТОР
                                        <svg width="41" height="16" viewBox="0 0 41 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M40.7071 8.77742C41.0976 8.3869 41.0976 7.75373 40.7071 7.36321L34.3431 0.999245C33.9526 0.60872 33.3195 0.60872 32.9289 0.999245C32.5384 1.38977 32.5384 2.02293 32.9289 2.41346L38.5858 8.07031L32.9289 13.7272C32.5384 14.1177 32.5384 14.7509 32.9289 15.1414C33.3195 15.5319 33.9526 15.5319 34.3431 15.1414L40.7071 8.77742ZM0 9.07031H40V7.07031H0L0 9.07031Z" fill="white"/>
                                        </svg>
                                    </a>
                                <? } ?>

                                <? if($USER->IsAuthorized()) { ?>
                                <a download href="<?= CFile::GetPath($arResult['PROPERTIES']['DESIGNER_PROJECT_FILES']['VALUE']) ?>" class="btn_white">
                                    СКАЧАТЬ ТЕКСТУРЫ
                                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.5 1.07031C12.5 0.518028 12.0523 0.0703124 11.5 0.0703124C10.9477 0.0703124 10.5 0.518028 10.5 1.07031H12.5ZM10.7929 16.7774C11.1834 17.1679 11.8166 17.1679 12.2071 16.7774L18.5711 10.4135C18.9616 10.0229 18.9616 9.38977 18.5711 8.99924C18.1805 8.60872 17.5474 8.60872 17.1569 8.99924L11.5 14.6561L5.84315 8.99924C5.45262 8.60872 4.81946 8.60872 4.42893 8.99924C4.03841 9.38977 4.03841 10.0229 4.42893 10.4135L10.7929 16.7774ZM10.5 1.07031V16.0703H12.5V1.07031H10.5Z" fill="#003064"/>
                                        <path d="M1.5 21.0703H21.5" stroke="#003064" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </a>
                                <? } else { ?>
                                <a data-toggle="modal" data-target="#authModal" class="btn_white">
                                    СКАЧАТЬ ТЕКСТУРЫ
                                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.5 1.07031C12.5 0.518028 12.0523 0.0703124 11.5 0.0703124C10.9477 0.0703124 10.5 0.518028 10.5 1.07031H12.5ZM10.7929 16.7774C11.1834 17.1679 11.8166 17.1679 12.2071 16.7774L18.5711 10.4135C18.9616 10.0229 18.9616 9.38977 18.5711 8.99924C18.1805 8.60872 17.5474 8.60872 17.1569 8.99924L11.5 14.6561L5.84315 8.99924C5.45262 8.60872 4.81946 8.60872 4.42893 8.99924C4.03841 9.38977 4.03841 10.0229 4.42893 10.4135L10.7929 16.7774ZM10.5 1.07031V16.0703H12.5V1.07031H10.5Z" fill="#003064"/>
                                        <path d="M1.5 21.0703H21.5" stroke="#003064" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </a>
                                <? } ?>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="main_container">
            <? /* Info */ ?>

                <? if(false) { ?>
                <div class="col-lg-6">

                    <div class="row">

                        <div class="col-sm-6">
                            <div class="product-item-detail-info-section">
                                <?php
                                foreach ($arParams['PRODUCT_INFO_BLOCK_ORDER'] as $blockName)
                                {
                                    switch ($blockName)
                                    {
                                        case 'sku':
                                            if ($haveOffers && !empty($arResult['OFFERS_PROP']))
                                            {
                                                ?>
                                                <div id="<?=$itemIds['TREE_ID']?>">
                                                    <?php
                                                    foreach ($arResult['SKU_PROPS'] as $skuProperty)
                                                    {
                                                        if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
                                                            continue;

                                                        $propertyId = $skuProperty['ID'];
                                                        $skuProps[] = array(
                                                            'ID' => $propertyId,
                                                            'SHOW_MODE' => $skuProperty['SHOW_MODE'],
                                                            'VALUES' => $skuProperty['VALUES'],
                                                            'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
                                                        );
                                                        ?>
                                                        <div class="product-item-detail-info-container" data-entity="sku-line-block">
                                                            <div class="product-item-detail-info-container-title"><?=htmlspecialcharsEx($skuProperty['NAME'])?></div>
                                                            <div class="product-item-scu-container">
                                                                <div class="product-item-scu-block">
                                                                    <div class="product-item-scu-list">
                                                                        <ul class="product-item-scu-item-list">
                                                                            <?php
                                                                            foreach ($skuProperty['VALUES'] as &$value)
                                                                            {
                                                                                $value['NAME'] = htmlspecialcharsbx($value['NAME']);

                                                                                if ($skuProperty['SHOW_MODE'] === 'PICT')
                                                                                {
                                                                                    ?>
                                                                                    <li class="product-item-scu-item-color-container" title="<?=$value['NAME']?>"
                                                                                        data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
                                                                                        data-onevalue="<?=$value['ID']?>">
                                                                                        <div class="product-item-scu-item-color-block">
                                                                                            <div class="product-item-scu-item-color" title="<?=$value['NAME']?>"
                                                                                                style="background-image: url('<?=$value['PICT']['SRC']?>');">
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                    <?php
                                                                                }
                                                                                else
                                                                                {
                                                                                    ?>
                                                                                    <li class="product-item-scu-item-text-container" title="<?=$value['NAME']?>"
                                                                                        data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
                                                                                        data-onevalue="<?=$value['ID']?>">
                                                                                        <div class="product-item-scu-item-text-block">
                                                                                            <div class="product-item-scu-item-text"><?=$value['NAME']?></div>
                                                                                        </div>
                                                                                    </li>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </ul>
                                                                        <div style="clear: both;"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }

                                            break;

                                        case 'props':
                                            if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
                                            {
                                                ?>
                                                <div class="product-item-detail-info-container">
                                                    <?php
                                                    if (!empty($arResult['DISPLAY_PROPERTIES']))
                                                    {
                                                        ?>
                                                        <dl class="product-item-detail-properties">
                                                            <?php
                                                            foreach ($arResult['DISPLAY_PROPERTIES'] as $property)
                                                            {
                                                                if (isset($arParams['MAIN_BLOCK_PROPERTY_CODE'][$property['CODE']]))
                                                                {
                                                                    ?>
                                                                    <dt><?=$property['NAME']?></dt>
                                                                    <dd><?=(is_array($property['DISPLAY_VALUE'])
                                                                            ? implode(' / ', $property['DISPLAY_VALUE'])
                                                                            : $property['DISPLAY_VALUE'])?>
                                                                    </dd>
                                                                    <?php
                                                                }
                                                            }
                                                            unset($property);
                                                            ?>
                                                        </dl>
                                                        <?php
                                                    }

                                                    if ($arResult['SHOW_OFFERS_PROPS'])
                                                    {
                                                        ?>
                                                        <dl class="product-item-detail-properties" id="<?=$itemIds['DISPLAY_MAIN_PROP_DIV']?>"></dl>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }

                                            break;
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <? if (/* Payblock */false) { ?>
                            <div class="product-item-detail-pay-block">
                                <?php
                                foreach ($arParams['PRODUCT_PAY_BLOCK_ORDER'] as $blockName)
                                {
                                    switch ($blockName)
                                    {
                                        case 'rating':
                                            if ($arParams['USE_VOTE_RATING'] === 'Y')
                                            {
                                                ?>
                                                <div class="product-item-detail-info-container">
                                                    <?php
                                                    $APPLICATION->IncludeComponent(
                                                        'bitrix:iblock.vote',
                                                        'stars',
                                                        array(
                                                            'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                                            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                                            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                                            'ELEMENT_ID' => $arResult['ID'],
                                                            'ELEMENT_CODE' => '',
                                                            'MAX_VOTE' => '5',
                                                            'VOTE_NAMES' => array('1', '2', '3', '4', '5'),
                                                            'SET_STATUS_404' => 'N',
                                                            'DISPLAY_AS_RATING' => $arParams['VOTE_DISPLAY_AS_RATING'],
                                                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                                            'CACHE_TIME' => $arParams['CACHE_TIME']
                                                        ),
                                                        $component,
                                                        array('HIDE_ICONS' => 'Y')
                                                    );
                                                    ?>
                                                </div>
                                                <?php
                                            }

                                            break;

                                        case 'price':
                                            ?>
                                            <div class="product-item-detail-info-container">
                                                <?php
                                                if ($arParams['SHOW_OLD_PRICE'] === 'Y')
                                                {
                                                    ?>
                                                    <div class="product-item-detail-price-old" id="<?=$itemIds['OLD_PRICE_ID']?>"
                                                        style="display: <?=($showDiscount ? '' : 'none')?>;">
                                                        <?=($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '')?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                                <div class="product-item-detail-price-current" id="<?=$itemIds['PRICE_ID']?>">
                                                    <?=$price['PRINT_RATIO_PRICE']?>
                                                </div>
                                                <?php
                                                if ($arParams['SHOW_OLD_PRICE'] === 'Y')
                                                {
                                                    ?>
                                                    <div class="item_economy_price" id="<?=$itemIds['DISCOUNT_PRICE_ID']?>"
                                                        style="display: <?=($showDiscount ? '' : 'none')?>;">
                                                        <?php
                                                        if ($showDiscount)
                                                        {
                                                            echo Loc::getMessage('CT_BCE_CATALOG_ECONOMY_INFO2', array('#ECONOMY#' => $price['PRINT_RATIO_DISCOUNT']));
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            break;

                                        case 'priceRanges':
                                            if ($arParams['USE_PRICE_COUNT'])
                                            {
                                                $showRanges = !$haveOffers && count($actualItem['ITEM_QUANTITY_RANGES']) > 1;
                                                $useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';
                                                ?>
                                                <div class="product-item-detail-info-container"
                                                    <?=$showRanges ? '' : 'style="display: none;"'?>
                                                    data-entity="price-ranges-block">
                                                    <div class="product-item-detail-info-container-title">
                                                        <?=$arParams['MESS_PRICE_RANGES_TITLE']?>
                                                        <span data-entity="price-ranges-ratio-header">
                                                            (<?=(Loc::getMessage(
                                                                'CT_BCE_CATALOG_RATIO_PRICE',
                                                                array('#RATIO#' => ($useRatio ? $measureRatio : '1').' '.$actualItem['ITEM_MEASURE']['TITLE'])
                                                            ))?>)
                                                        </span>
                                                    </div>
                                                    <dl class="product-item-detail-properties" data-entity="price-ranges-body">
                                                        <?php
                                                        if ($showRanges)
                                                        {
                                                            foreach ($actualItem['ITEM_QUANTITY_RANGES'] as $range)
                                                            {
                                                                if ($range['HASH'] !== 'ZERO-INF')
                                                                {
                                                                    $itemPrice = false;

                                                                    foreach ($arResult['ITEM_PRICES'] as $itemPrice)
                                                                    {
                                                                        if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
                                                                        {
                                                                            break;
                                                                        }
                                                                    }

                                                                    if ($itemPrice)
                                                                    {
                                                                        ?>
                                                                        <dt>
                                                                            <?php
                                                                            echo Loc::getMessage(
                                                                                    'CT_BCE_CATALOG_RANGE_FROM',
                                                                                    array('#FROM#' => $range['SORT_FROM'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
                                                                                ).' ';

                                                                            if (is_infinite($range['SORT_TO']))
                                                                            {
                                                                                echo Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
                                                                            }
                                                                            else
                                                                            {
                                                                                echo Loc::getMessage(
                                                                                    'CT_BCE_CATALOG_RANGE_TO',
                                                                                    array('#TO#' => $range['SORT_TO'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
                                                                                );
                                                                            }
                                                                            ?>
                                                                        </dt>
                                                                        <dd><?=($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE'])?></dd>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </dl>
                                                </div>
                                                <?php
                                                unset($showRanges, $useRatio, $itemPrice, $range);
                                            }

                                            break;

                                        case 'quantityLimit':
                                            if ($arParams['SHOW_MAX_QUANTITY'] !== 'N')
                                            {
                                                if ($haveOffers)
                                                {
                                                    ?>
                                                    <div class="product-item-detail-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>" style="display: none;">
                                                        <div class="product-item-detail-info-container-title">
                                                            <?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
                                                            <span class="product-item-quantity" data-entity="quantity-limit-value"></span>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                else
                                                {
                                                    if (
                                                        $measureRatio
                                                        && (float)$actualItem['PRODUCT']['QUANTITY'] > 0
                                                        && $actualItem['CHECK_QUANTITY']
                                                    )
                                                    {
                                                        ?>
                                                        <div class="product-item-detail-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>">
                                                            <div class="product-item-detail-info-container-title">
                                                                <?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
                                                                <span class="product-item-quantity" data-entity="quantity-limit-value">
                                                                    <?php
                                                                    if ($arParams['SHOW_MAX_QUANTITY'] === 'M')
                                                                    {
                                                                        if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR'])
                                                                        {
                                                                            echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
                                                                        }
                                                                        else
                                                                        {
                                                                            echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        echo $actualItem['PRODUCT']['QUANTITY'].' '.$actualItem['ITEM_MEASURE']['TITLE'];
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }

                                            break;

                                        case 'quantity':
                                            if ($arParams['USE_PRODUCT_QUANTITY'])
                                            {
                                                ?>
                                                <div class="product-item-detail-info-container" style="<?=(!$actualItem['CAN_BUY'] ? 'display: none;' : '')?>"
                                                    data-entity="quantity-block">
                                                    <div class="product-item-detail-info-container-title"><?=Loc::getMessage('CATALOG_QUANTITY')?></div>
                                                    <div class="product-item-amount">
                                                        <div class="product-item-amount-field-container">
                                                            <span class="product-item-amount-field-btn-minus no-select" id="<?=$itemIds['QUANTITY_DOWN_ID']?>"></span>
                                                            <input class="product-item-amount-field" id="<?=$itemIds['QUANTITY_ID']?>" type="number"
                                                                value="<?=$price['MIN_QUANTITY']?>">
                                                            <span class="product-item-amount-field-btn-plus no-select" id="<?=$itemIds['QUANTITY_UP_ID']?>"></span>
                                                            <span class="product-item-amount-description-container">
                                                                <span id="<?=$itemIds['QUANTITY_MEASURE']?>">
                                                                    <?=$actualItem['ITEM_MEASURE']['TITLE']?>
                                                                </span>
                                                                <span id="<?=$itemIds['PRICE_TOTAL']?>"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }

                                            break;

                                        case 'buttons':
                                            ?>
                                            <div data-entity="main-button-container">
                                                <div id="<?=$itemIds['BASKET_ACTIONS_ID']?>" style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;">
                                                    <?php
                                                    if ($showAddBtn)
                                                    {
                                                        ?>
                                                        <div class="product-item-detail-info-container">
                                                            <a class="btn <?=$showButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['ADD_BASKET_LINK']?>"
                                                                href="javascript:void(0);">
                                                                <span><?=$arParams['MESS_BTN_ADD_TO_BASKET']?></span>
                                                            </a>
                                                        </div>
                                                        <?php
                                                    }

                                                    if ($showBuyBtn)
                                                    {
                                                        ?>
                                                        <div class="product-item-detail-info-container">
                                                            <a class="btn <?=$buyButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['BUY_LINK']?>"
                                                                href="javascript:void(0);">
                                                                <span><?=$arParams['MESS_BTN_BUY']?></span>
                                                            </a>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                                if ($showSubscribe)
                                                {
                                                    ?>
                                                    <div class="product-item-detail-info-container">
                                                        <?php
                                                        $APPLICATION->IncludeComponent(
                                                            'bitrix:catalog.product.subscribe',
                                                            '',
                                                            array(
                                                                'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                                                'PRODUCT_ID' => $arResult['ID'],
                                                                'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                                                'BUTTON_CLASS' => 'btn btn-default product-item-detail-buy-button',
                                                                'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
                                                                'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                                            ),
                                                            $component,
                                                            array('HIDE_ICONS' => 'Y')
                                                        );
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                                <div class="product-item-detail-info-container">
                                                    <a class="btn btn-link product-item-detail-buy-button" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>"
                                                        href="javascript:void(0)"
                                                        rel="nofollow" style="display: <?=(!$actualItem['CAN_BUY'] ? '' : 'none')?>;">
                                                        <?=$arParams['MESS_NOT_AVAILABLE']?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                            break;
                                    }
                                }

                                if ($arParams['DISPLAY_COMPARE'])
                                {
                                    ?>
                                    <div class="product-item-detail-compare-container">
                                        <div class="product-item-detail-compare">
                                            <div class="checkbox">
                                                <label id="<?=$itemIds['COMPARE_LINK']?>">
                                                    <input type="checkbox" data-entity="compare-checkbox">
                                                    <span data-entity="compare-title"><?=$arParams['MESS_BTN_COMPARE']?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <? } ?>
                        </div>

                    </div>

                </div>
                <? } ?>


            </div>

            <div class="main_container">
                <div class="col-xs-12">
                    <?php
                    if ($haveOffers)
                    {
                        if ($arResult['OFFER_GROUP'])
                        {
                            foreach ($arResult['OFFER_GROUP_VALUES'] as $offerId)
                            {
                                ?>
                                <span id="<?=$itemIds['OFFER_GROUP'].$offerId?>" style="display: none;">
                                    <?php
                                    $APPLICATION->IncludeComponent(
                                        'bitrix:catalog.set.constructor',
                                        '.default',
                                        array(
                                            'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                            'IBLOCK_ID' => $arResult['OFFERS_IBLOCK'],
                                            'ELEMENT_ID' => $offerId,
                                            'PRICE_CODE' => $arParams['PRICE_CODE'],
                                            'BASKET_URL' => $arParams['BASKET_URL'],
                                            'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
                                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                            'CACHE_TIME' => $arParams['CACHE_TIME'],
                                            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                            'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
                                            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                            'CURRENCY_ID' => $arParams['CURRENCY_ID']
                                        ),
                                        $component,
                                        array('HIDE_ICONS' => 'Y')
                                    );
                                    ?>
                                </span>
                                <?php
                            }
                        }
                    }
                    else
                    {
                        if ($arResult['MODULES']['catalog'] && $arResult['OFFER_GROUP'])
                        {
                            $APPLICATION->IncludeComponent(
                                'bitrix:catalog.set.constructor',
                                '.default',
                                array(
                                    'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                    'ELEMENT_ID' => $arResult['ID'],
                                    'PRICE_CODE' => $arParams['PRICE_CODE'],
                                    'BASKET_URL' => $arParams['BASKET_URL'],
                                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                                    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                    'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
                                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                    'CURRENCY_ID' => $arParams['CURRENCY_ID']
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                        }
                    }
                    ?>
                </div>
            </div>

            <?/* Characteristics Tab */?>
            <div class="main_container">

                <? if (false) { ?>
                    <div class="col-sm-8 col-md-9">
                        <div class="row" id="<?=$itemIds['TABS_ID']?>">
                            <div class="col-xs-12">
                                <div class="product-item-detail-tabs-container">
                                    <ul class="product-item-detail-tabs-list">
                                        <?php
                                        if ($showDescription)
                                        {
                                            ?>
                                            <li class="product-item-detail-tab active" data-entity="tab" data-value="description">
                                                <a href="javascript:void(0);" class="product-item-detail-tab-link">
                                                    <span><?=$arParams['MESS_DESCRIPTION_TAB']?></span>
                                                </a>
                                            </li>
                                            <?php
                                        }

                                        if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
                                        {
                                            ?>
                                            <li class="product-item-detail-tab" data-entity="tab" data-value="properties">
                                                <a href="javascript:void(0);" class="product-item-detail-tab-link">
                                                    <span><?=$arParams['MESS_PROPERTIES_TAB']?></span>
                                                </a>
                                            </li>
                                            <?php
                                        }

                                        if ($arParams['USE_COMMENTS'] === 'Y')
                                        {
                                            ?>
                                            <li class="product-item-detail-tab" data-entity="tab" data-value="comments">
                                                <a href="javascript:void(0);" class="product-item-detail-tab-link">
                                                    <span><?=$arParams['MESS_COMMENTS_TAB']?></span>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="<?=$itemIds['TAB_CONTAINERS_ID']?>">
                            <div class="col-xs-12">
                                <?php
                                if ($showDescription)
                                {
                                    ?>
                                    <div class="product-item-detail-tab-content active" data-entity="tab-container" data-value="description"
                                        itemprop="description" id="<?=$itemIds['DESCRIPTION_ID']?>">
                                        <?php
                                        if (
                                            $arResult['PREVIEW_TEXT'] != ''
                                            && (
                                                $arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'S'
                                                || ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'E' && $arResult['DETAIL_TEXT'] == '')
                                            )
                                        )
                                        {
                                            echo $arResult['PREVIEW_TEXT_TYPE'] === 'html' ? $arResult['PREVIEW_TEXT'] : '<p>'.$arResult['PREVIEW_TEXT'].'</p>';
                                        }

                                        if ($arResult['DETAIL_TEXT'] != '')
                                        {
                                            echo $arResult['DETAIL_TEXT_TYPE'] === 'html' ? $arResult['DETAIL_TEXT'] : '<p>'.$arResult['DETAIL_TEXT'].'</p>';
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }

                                if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
                                {
                                    ?>
                                    <div class="product-item-detail-tab-content" data-entity="tab-container" data-value="properties">
                                        <?php
                                        if (!empty($arResult['DISPLAY_PROPERTIES']))
                                        {
                                            ?>
                                            <dl class="product-item-detail-properties">
                                                <?php
                                                foreach ($arResult['DISPLAY_PROPERTIES'] as $property)
                                                {
                                                    ?>
                                                    <dt><?=$property['NAME']?></dt>
                                                    <dd><?=(
                                                        is_array($property['DISPLAY_VALUE'])
                                                            ? implode(' / ', $property['DISPLAY_VALUE'])
                                                            : $property['DISPLAY_VALUE']
                                                        )?>
                                                    </dd>
                                                    <?php
                                                }
                                                unset($property);
                                                ?>
                                            </dl>
                                            <?php
                                        }

                                        if ($arResult['SHOW_OFFERS_PROPS'])
                                        {
                                            ?>
                                            <dl class="product-item-detail-properties" id="<?=$itemIds['DISPLAY_PROP_DIV']?>"></dl>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }

                                if ($arParams['USE_COMMENTS'] === 'Y')
                                {
                                    ?>
                                    <div class="product-item-detail-tab-content" data-entity="tab-container" data-value="comments" style="display: none;">
                                        <?php
                                        $componentCommentsParams = array(
                                            'ELEMENT_ID' => $arResult['ID'],
                                            'ELEMENT_CODE' => '',
                                            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                            'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
                                            'URL_TO_COMMENT' => '',
                                            'WIDTH' => '',
                                            'COMMENTS_COUNT' => '5',
                                            'BLOG_USE' => $arParams['BLOG_USE'],
                                            'FB_USE' => $arParams['FB_USE'],
                                            'FB_APP_ID' => $arParams['FB_APP_ID'],
                                            'VK_USE' => $arParams['VK_USE'],
                                            'VK_API_ID' => $arParams['VK_API_ID'],
                                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                            'CACHE_TIME' => $arParams['CACHE_TIME'],
                                            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                            'BLOG_TITLE' => '',
                                            'BLOG_URL' => $arParams['BLOG_URL'],
                                            'PATH_TO_SMILE' => '',
                                            'EMAIL_NOTIFY' => $arParams['BLOG_EMAIL_NOTIFY'],
                                            'AJAX_POST' => 'Y',
                                            'SHOW_SPAM' => 'Y',
                                            'SHOW_RATING' => 'N',
                                            'FB_TITLE' => '',
                                            'FB_USER_ADMIN_ID' => '',
                                            'FB_COLORSCHEME' => 'light',
                                            'FB_ORDER_BY' => 'reverse_time',
                                            'VK_TITLE' => '',
                                            'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME']
                                        );
                                        if(isset($arParams["USER_CONSENT"]))
                                            $componentCommentsParams["USER_CONSENT"] = $arParams["USER_CONSENT"];
                                        if(isset($arParams["USER_CONSENT_ID"]))
                                            $componentCommentsParams["USER_CONSENT_ID"] = $arParams["USER_CONSENT_ID"];
                                        if(isset($arParams["USER_CONSENT_IS_CHECKED"]))
                                            $componentCommentsParams["USER_CONSENT_IS_CHECKED"] = $arParams["USER_CONSENT_IS_CHECKED"];
                                        if(isset($arParams["USER_CONSENT_IS_LOADED"]))
                                            $componentCommentsParams["USER_CONSENT_IS_LOADED"] = $arParams["USER_CONSENT_IS_LOADED"];
                                        $APPLICATION->IncludeComponent(
                                            'bitrix:catalog.comments',
                                            '',
                                            $componentCommentsParams,
                                            $component,
                                            array('HIDE_ICONS' => 'Y')
                                        );
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-3">
                    <div>
                        <?php
                        if ($arParams['BRAND_USE'] === 'Y')
                        {
                            $APPLICATION->IncludeComponent(
                                'bitrix:catalog.brandblock',
                                '.default',
                                array(
                                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                    'ELEMENT_ID' => $arResult['ID'],
                                    'ELEMENT_CODE' => '',
                                    'PROP_CODE' => $arParams['BRAND_PROP_CODE'],
                                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                                    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                    'WIDTH' => '',
                                    'HEIGHT' => ''
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                        }
                        ?>
                    </div>
                </div>
                <? } ?>

                <script>
                    $(document).ready(function(){
                        $('.product_card_tabs').on("click", "a", function(e){
                            var anchor = $(this);
                            $('html, body').animate({
                                scrollTop: $(anchor.attr('href')).offset().top - 146 - 80
                            }, 0);
                            e.preventDefault();
                            return false;
                        });
                    });
                </script>

                <nav class="product_card_tabs">

                    <div class="nav nav-tabs">
                        <a href="#product_description" class="nav-link active">
                            описание
                        </a>
                        <a href="#product_schemes" class="nav-link">
                            Варианты раскладки
                        </a>

                        <? if(false) { ?>
                            <a href="#product_calc" class="nav-link">
                                Калькулятор
                            </a>
                            <a href="#product_to_designer" class="nav-link">
                                Дизайнеру
                            </a>
                        <? } ?>

                        <a href="/portfolio/" class="nav-link">
                            ПОРТФОЛИО
                        </a>
                    </div>

                </nav>

                <div id="product_description" class="product_card_content">
                    <div class="tab-pane">
                        <?= $arResult['DETAIL_TEXT'] ?>
                    </div>
                </div>
            </div>

            <div class="row p-0 gray-field-container">
                <div class="product_card_end">
                    <div class="main_container">
                        <div id="product_schemes" class="swiper portfolio_patterns swiper-initialized swiper-horizontal swiper-backface-hidden">
                            <div class="portfolio_head">
                                <h4>
                                    Варианты раскладки
                                </h4>
                                <div class="slide_btns">
                                    <button class="patterns-button-prev swiper-button-disabled" disabled="" tabindex="-1" aria-label="Previous slide" aria-controls="swiper-wrapper-5453bea2910d55b6b" aria-disabled="true">
                                        <svg width="41" height="15" viewBox="0 0 41 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.292892 6.86321C-0.0976295 7.25373 -0.0976295 7.8869 0.292892 8.27742L6.65685 14.6414C7.04738 15.0319 7.68054 15.0319 8.07107 14.6414C8.46159 14.2509 8.46159 13.6177 8.07107 13.2272L2.41422 7.57031L8.07107 1.91346C8.46159 1.52293 8.46159 0.889769 8.07107 0.499245C7.68054 0.10872 7.04738 0.10872 6.65685 0.499245L0.292892 6.86321ZM41 6.57031L1 6.57031V8.57031L41 8.57031V6.57031Z" fill="#003064"></path>
                                        </svg>
                                    </button>
                                    <button class="patterns-button-next" tabindex="0" aria-label="Next slide" aria-controls="swiper-wrapper-5453bea2910d55b6b" aria-disabled="false">
                                        <svg width="41" height="15" viewBox="0 0 41 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M40.7071 8.27742C41.0976 7.8869 41.0976 7.25373 40.7071 6.86321L34.3431 0.499245C33.9526 0.10872 33.3195 0.10872 32.9289 0.499245C32.5384 0.889769 32.5384 1.52293 32.9289 1.91346L38.5858 7.57031L32.9289 13.2272C32.5384 13.6177 32.5384 14.2509 32.9289 14.6414C33.3195 15.0319 33.9526 15.0319 34.3431 14.6414L40.7071 8.27742ZM0 8.57031H40V6.57031H0L0 8.57031Z" fill="#003064"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="swiper-wrapper">
                                <? foreach($arResult['PROPERTIES']['LAY_TILES_SCHEMES']['VALUE'] as $layTilesImg) { ?>
                                <div class="swiper-slide">
                                    <a href="<?= CFile::GetFileArray($layTilesImg)['SRC'] ?>" data-fancybox class="portfolio_block">
                                        <div class="portfolio_block_in">
                                            <button>
                                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/zoom_icon.svg" alt="">
                                            </button>
                                        </div>
                                        <img src="<?= CFile::GetFileArray($layTilesImg)['SRC'] ?>" alt="">
                                    </a>
                                </div>
                                <? } ?>
                            </div>
                            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>

                        <? if(false) { // Рассчитать необходимый объем (закомментировано по просьбе заказчика) ?>
                        <div id="product_calc" class="calculate">
                            <h4>
                                Рассчитать необходимый объем
                            </h4>
                            <div class="calculate_text">
                                <img class="calculate_left" src="<?= SITE_TEMPLATE_PATH ?>/images/icons/calculate_icon.svg" alt="">
                                <div class="calculate_right">
                                    <h5>
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/calculate_icon.svg" alt="">
                                        Расчет количества и стоимости материала
                                    </h5>
                                    <p>
                                        Расчитайте объем и стоимость необходимого материала под ваши параметры  с помощью нашего калькулятора!
                                    </p>
                                    <a href="/services/calc/" class="btn_blue">
                                        РАСЧИТАТЬ
                                        <svg width="41" height="16" viewBox="0 0 41 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M40.7071 8.77742C41.0976 8.3869 41.0976 7.75373 40.7071 7.36321L34.3431 0.999245C33.9526 0.60872 33.3195 0.60872 32.9289 0.999245C32.5384 1.38977 32.5384 2.02293 32.9289 2.41346L38.5858 8.07031L32.9289 13.7272C32.5384 14.1177 32.5384 14.7509 32.9289 15.1414C33.3195 15.5319 33.9526 15.5319 34.3431 15.1414L40.7071 8.77742ZM0 9.07031H40V7.07031H0L0 9.07031Z" fill="white"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <? } ?>


                        <? if(false) { // Дизайнеру-проектировщику (закомментировано по просьбе заказчика) ?>
                        <div id="product_to_designer" class="planner">
                            <h4>
                                Дизайнеру/ проектировщику
                            </h4>
                            <ul>
                                <li>
                                    <div class="planner_left">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/pdf.svg" alt="">
                                        <div class="planner_text">
                                            <h6>
<!--                                                Текстуры форм и палитр ББК-->
                                                <?= $arResult['DISPLAY_PROPERTIES']['DESIGNER_PROJECT_FILES']['FILE_VALUE']['ORIGINAL_NAME'] ?>
                                            </h6>
                                            <span>
                                                <?= round((int)$arResult['DISPLAY_PROPERTIES']['DESIGNER_PROJECT_FILES']['FILE_VALUE']['FILE_SIZE'] / (1024 * 1024), 2) ?> Мб
                                            </span>
                                        </div>
                                    </div>

                                    <? if($USER->IsAuthorized()) { ?>
                                        <a download href="<?= CFile::GetPath($arResult['PROPERTIES']['DESIGNER_PROJECT_FILES']['VALUE']) ?>" class="btn_blue">
                                            <span>
                                              СКАЧАТЬ МАТЕРИАЛЫ
                                            </span>
                                            <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 1.07031C12 0.518028 11.5523 0.0703124 11 0.0703124C10.4477 0.0703124 10 0.518028 10 1.07031H12ZM10.2929 16.7774C10.6834 17.1679 11.3166 17.1679 11.7071 16.7774L18.0711 10.4135C18.4616 10.0229 18.4616 9.38977 18.0711 8.99924C17.6805 8.60872 17.0474 8.60872 16.6569 8.99924L11 14.6561L5.34315 8.99924C4.95262 8.60872 4.31946 8.60872 3.92893 8.99924C3.53841 9.38977 3.53841 10.0229 3.92893 10.4135L10.2929 16.7774ZM10 1.07031V16.0703H12V1.07031H10Z" fill="white"></path>
                                                <path d="M1 21.0703H21" stroke="white" stroke-width="2" stroke-linecap="round"></path>
                                            </svg>
                                        </a>
                                    <? } else { ?>
                                        <a data-toggle="modal" data-target="#authModal" class="btn_blue">
                                            <span>
                                              СКАЧАТЬ МАТЕРИАЛЫ
                                            </span>
                                            <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 1.07031C12 0.518028 11.5523 0.0703124 11 0.0703124C10.4477 0.0703124 10 0.518028 10 1.07031H12ZM10.2929 16.7774C10.6834 17.1679 11.3166 17.1679 11.7071 16.7774L18.0711 10.4135C18.4616 10.0229 18.4616 9.38977 18.0711 8.99924C17.6805 8.60872 17.0474 8.60872 16.6569 8.99924L11 14.6561L5.34315 8.99924C4.95262 8.60872 4.31946 8.60872 3.92893 8.99924C3.53841 9.38977 3.53841 10.0229 3.92893 10.4135L10.2929 16.7774ZM10 1.07031V16.0703H12V1.07031H10Z" fill="white"></path>
                                                <path d="M1 21.0703H21" stroke="white" stroke-width="2" stroke-linecap="round"></path>
                                            </svg>
                                        </a>
                                    <? } ?>

                                </li>
                            </ul>
                        </div>
                        <? } ?>
                    </div>

                </div>
            </div>

            <? if(!empty($arResult['PROPERTIES']['PORTFOLIO_PHOTO']['VALUE'])) { ?>
                <div class="main_container">
                    <div class="swiper portfolio_endSwiper">
                        <div class="portfolio_head">
                            <h4>
                                Портфолио
                            </h4>
                            <a href="/portfolio/">
                                СМОТРЕТЬ
                                <svg width="41" height="15" viewBox="0 0 41 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M40.7071 8.27742C41.0976 7.8869 41.0976 7.25373 40.7071 6.86321L34.3431 0.499245C33.9526 0.10872 33.3195 0.10872 32.9289 0.499245C32.5384 0.889769 32.5384 1.52293 32.9289 1.91346L38.5858 7.57031L32.9289 13.2272C32.5384 13.6177 32.5384 14.2509 32.9289 14.6414C33.3195 15.0319 33.9526 15.0319 34.3431 14.6414L40.7071 8.27742ZM0 8.57031H40V6.57031H0L0 8.57031Z" fill="#003064"/>
                                </svg>
                            </a>
                        </div>
                        <div class="swiper-wrapper">

                            <? foreach($arResult['PROPERTIES']['PORTFOLIO_PHOTO']['VALUE'] as $imgCodeId) { ?>
                                <div class="swiper-slide">
                                    <div class="portfolio_block">
                                        <div class="portfolio_block_in">
                                            <button>
                                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/zoom_icon.svg" alt="">
                                            </button>
                                        </div>
                                        <img src="<?= CFile::GetFileArray($imgCodeId)['SRC'] ?>" alt="">
                                    </div>
                                </div>
                            <? } ?>

                        </div>
                    </div>
                </div>
            <? } ?>


            <? if(false) { ?>
                <div class="main_container">
                    <div class="col-xs-12">
                        <?php
                        if ($arResult['CATALOG'] && $actualItem['CAN_BUY'] && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'))
                        {
                            $APPLICATION->IncludeComponent(
                                'bitrix:sale.prediction.product.detail',
                                '.default',
                                array(
                                    'BUTTON_ID' => $showBuyBtn ? $itemIds['BUY_LINK'] : $itemIds['ADD_BASKET_LINK'],
                                    'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                    'POTENTIAL_PRODUCT_TO_BUY' => array(
                                        'ID' => $arResult['ID'] ?? null,
                                        'MODULE' => $arResult['MODULE'] ?? 'catalog',
                                        'PRODUCT_PROVIDER_CLASS' => $arResult['~PRODUCT_PROVIDER_CLASS'] ?? \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
                                        'QUANTITY' => $arResult['QUANTITY'] ?? null,
                                        'IBLOCK_ID' => $arResult['IBLOCK_ID'] ?? null,

                                        'PRIMARY_OFFER_ID' => $arResult['OFFERS'][0]['ID'] ?? null,
                                        'SECTION' => array(
                                            'ID' => $arResult['SECTION']['ID'] ?? null,
                                            'IBLOCK_ID' => $arResult['SECTION']['IBLOCK_ID'] ?? null,
                                            'LEFT_MARGIN' => $arResult['SECTION']['LEFT_MARGIN'] ?? null,
                                            'RIGHT_MARGIN' => $arResult['SECTION']['RIGHT_MARGIN'] ?? null,
                                        ),
                                    )
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                        }

                        if ($arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'))
                        {
                            ?>
                            <div data-entity="parent-container">
                                <?php
                                if (!isset($arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y')
                                {
                                    ?>
                                    <div class="catalog-block-header" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
                                        <?=($arParams['GIFTS_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFT_BLOCK_TITLE_DEFAULT'))?>
                                    </div>
                                    <?php
                                }

                                CBitrixComponent::includeComponentClass('bitrix:sale.products.gift');
                                $APPLICATION->IncludeComponent(
                                    'bitrix:sale.products.gift',
                                    '.default',
                                    array(
                                        'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                        'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                                        'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],

                                        'PRODUCT_ROW_VARIANTS' => "",
                                        'PAGE_ELEMENT_COUNT' => 0,
                                        'DEFERRED_PRODUCT_ROW_VARIANTS' => \Bitrix\Main\Web\Json::encode(
                                            SaleProductsGiftComponent::predictRowVariants(
                                                $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
                                                $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT']
                                            )
                                        ),
                                        'DEFERRED_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],

                                        'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                                        'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
                                        'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
                                        'PRODUCT_DISPLAY_MODE' => 'Y',
                                        'PRODUCT_BLOCKS_ORDER' => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],
                                        'SHOW_SLIDER' => $arParams['GIFTS_SHOW_SLIDER'],
                                        'SLIDER_INTERVAL' => $arParams['GIFTS_SLIDER_INTERVAL'] ?? '',
                                        'SLIDER_PROGRESS' => $arParams['GIFTS_SLIDER_PROGRESS'] ?? '',

                                        'TEXT_LABEL_GIFT' => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],

                                        'LABEL_PROP_'.$arParams['IBLOCK_ID'] => array(),
                                        'LABEL_PROP_MOBILE_'.$arParams['IBLOCK_ID'] => array(),
                                        'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],

                                        'ADD_TO_BASKET_ACTION' => ($arParams['ADD_TO_BASKET_ACTION'] ?? ''),
                                        'MESS_BTN_BUY' => $arParams['~GIFTS_MESS_BTN_BUY'],
                                        'MESS_BTN_ADD_TO_BASKET' => $arParams['~GIFTS_MESS_BTN_BUY'],
                                        'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
                                        'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                        'MESS_BTN_COMPARE' => $arParams['~MESS_BTN_COMPARE'],
                                        'MESS_NOT_AVAILABLE' => $arParams['~MESS_NOT_AVAILABLE'],
                                        'MESS_SHOW_MAX_QUANTITY' => $arParams['~MESS_SHOW_MAX_QUANTITY'],
                                        'MESS_RELATIVE_QUANTITY_MANY' => $arParams['~MESS_RELATIVE_QUANTITY_MANY'],
                                        'MESS_RELATIVE_QUANTITY_FEW' => $arParams['~MESS_RELATIVE_QUANTITY_FEW'],

                                        'SHOW_PRODUCTS_'.$arParams['IBLOCK_ID'] => 'Y',
                                        'PROPERTY_CODE_'.$arParams['IBLOCK_ID'] => [],
                                        'PROPERTY_CODE_MOBILE'.$arParams['IBLOCK_ID'] => [],
                                        'PROPERTY_CODE_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
                                        'OFFER_TREE_PROPS_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
                                        'CART_PROPERTIES_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFERS_CART_PROPERTIES'],
                                        'ADDITIONAL_PICT_PROP_'.$arParams['IBLOCK_ID'] => ($arParams['ADD_PICT_PROP'] ?? ''),
                                        'ADDITIONAL_PICT_PROP_'.$arResult['OFFERS_IBLOCK'] => ($arParams['OFFER_ADD_PICT_PROP'] ?? ''),

                                        'HIDE_NOT_AVAILABLE' => 'Y',
                                        'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
                                        'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                                        'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
                                        'PRICE_CODE' => $arParams['PRICE_CODE'],
                                        'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
                                        'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                        'BASKET_URL' => $arParams['BASKET_URL'],
                                        'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
                                        'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
                                        'PARTIAL_PRODUCT_PROPERTIES' => $arParams['PARTIAL_PRODUCT_PROPERTIES'],
                                        'USE_PRODUCT_QUANTITY' => 'N',
                                        'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                                        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                        'POTENTIAL_PRODUCT_TO_BUY' => array(
                                            'ID' => $arResult['ID'] ?? null,
                                            'MODULE' => $arResult['MODULE'] ?? 'catalog',
                                            'PRODUCT_PROVIDER_CLASS' => $arResult['~PRODUCT_PROVIDER_CLASS'] ?? \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
                                            'QUANTITY' => $arResult['QUANTITY'] ?? null,
                                            'IBLOCK_ID' => $arResult['IBLOCK_ID'] ?? null,

                                            'PRIMARY_OFFER_ID' => $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'] ?? null,
                                            'SECTION' => array(
                                                'ID' => $arResult['SECTION']['ID'] ?? null,
                                                'IBLOCK_ID' => $arResult['SECTION']['IBLOCK_ID'] ?? null,
                                                'LEFT_MARGIN' => $arResult['SECTION']['LEFT_MARGIN'] ?? null,
                                                'RIGHT_MARGIN' => $arResult['SECTION']['RIGHT_MARGIN'] ?? null,
                                            ),
                                        ),

                                        'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
                                        'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
                                        'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
                                    ),
                                    $component,
                                    array('HIDE_ICONS' => 'Y')
                                );
                                ?>
                            </div>
                            <?php
                        }

                        if ($arResult['CATALOG'] && $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'))
                        {
                            ?>
                            <div data-entity="parent-container">
                                <?php
                                if (!isset($arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y')
                                {
                                    ?>
                                    <div class="catalog-block-header" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
                                        <?=($arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFTS_MAIN_BLOCK_TITLE_DEFAULT'))?>
                                    </div>
                                    <?php
                                }

                                $APPLICATION->IncludeComponent(
                                    'bitrix:sale.gift.main.products',
                                    '.default',
                                    array(
                                        'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                        'PAGE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
                                        'LINE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
                                        'HIDE_BLOCK_TITLE' => 'Y',
                                        'BLOCK_TITLE' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

                                        'OFFERS_FIELD_CODE' => $arParams['OFFERS_FIELD_CODE'],
                                        'OFFERS_PROPERTY_CODE' => $arParams['OFFERS_PROPERTY_CODE'],

                                        'AJAX_MODE' => $arParams['AJAX_MODE'] ?? '',
                                        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                        'IBLOCK_ID' => $arParams['IBLOCK_ID'],

                                        'ELEMENT_SORT_FIELD' => 'ID',
                                        'ELEMENT_SORT_ORDER' => 'DESC',
                                        'FILTER_NAME' => 'searchFilter',
                                        'SECTION_URL' => $arParams['SECTION_URL'],
                                        'DETAIL_URL' => $arParams['DETAIL_URL'],
                                        'BASKET_URL' => $arParams['BASKET_URL'],
                                        'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                                        'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                                        'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],

                                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                        'CACHE_TIME' => $arParams['CACHE_TIME'],

                                        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                        'SET_TITLE' => $arParams['SET_TITLE'],
                                        'PROPERTY_CODE' => $arParams['PROPERTY_CODE'],
                                        'PRICE_CODE' => $arParams['PRICE_CODE'],
                                        'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                                        'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

                                        'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                                        'HIDE_NOT_AVAILABLE' => 'Y',
                                        'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
                                        'TEMPLATE_THEME' => ($arParams['TEMPLATE_THEME'] ?? ''),
                                        'PRODUCT_BLOCKS_ORDER' => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],

                                        'SHOW_SLIDER' => $arParams['GIFTS_SHOW_SLIDER'],
                                        'SLIDER_INTERVAL' => $arParams['GIFTS_SLIDER_INTERVAL'] ?? '',
                                        'SLIDER_PROGRESS' => $arParams['GIFTS_SLIDER_PROGRESS'] ?? '',

                                        'ADD_PICT_PROP' => ($arParams['ADD_PICT_PROP'] ?? ''),
                                        'LABEL_PROP' => ($arParams['LABEL_PROP'] ?? ''),
                                        'LABEL_PROP_MOBILE' => ($arParams['LABEL_PROP_MOBILE'] ?? ''),
                                        'LABEL_PROP_POSITION' => ($arParams['LABEL_PROP_POSITION'] ?? ''),
                                        'OFFER_ADD_PICT_PROP' => ($arParams['OFFER_ADD_PICT_PROP'] ?? ''),
                                        'OFFER_TREE_PROPS' => ($arParams['OFFER_TREE_PROPS'] ?? ''),
                                        'SHOW_DISCOUNT_PERCENT' => ($arParams['SHOW_DISCOUNT_PERCENT'] ?? ''),
                                        'DISCOUNT_PERCENT_POSITION' => ($arParams['DISCOUNT_PERCENT_POSITION'] ?? ''),
                                        'SHOW_OLD_PRICE' => ($arParams['SHOW_OLD_PRICE'] ?? ''),
                                        'MESS_BTN_BUY' => ($arParams['~MESS_BTN_BUY'] ?? ''),
                                        'MESS_BTN_ADD_TO_BASKET' => ($arParams['~MESS_BTN_ADD_TO_BASKET'] ?? ''),
                                        'MESS_BTN_DETAIL' => ($arParams['~MESS_BTN_DETAIL'] ?? ''),
                                        'MESS_NOT_AVAILABLE' => ($arParams['~MESS_NOT_AVAILABLE'] ?? ''),
                                        'ADD_TO_BASKET_ACTION' => ($arParams['ADD_TO_BASKET_ACTION'] ?? ''),
                                        'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] ?? ''),
                                        'DISPLAY_COMPARE' => ($arParams['DISPLAY_COMPARE'] ?? ''),
                                        'COMPARE_PATH' => ($arParams['COMPARE_PATH'] ?? ''),
                                    )
                                    + array(
                                        'OFFER_ID' => empty($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'])
                                            ? $arResult['ID']
                                            : $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'],
                                        'SECTION_ID' => $arResult['SECTION']['ID'],
                                        'ELEMENT_ID' => $arResult['ID'],

                                        'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
                                        'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
                                        'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
                                    ),
                                    $component,
                                    array('HIDE_ICONS' => 'Y')
                                );
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            <? } ?>


        <!--Small Card-->
        <!--Top tabs-->

        <meta itemprop="name" content="<?=$name?>" />
        <meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />
        <?php
        if ($haveOffers)
        {
            foreach ($arResult['JS_OFFERS'] as $offer)
            {
                $currentOffersList = array();

                if (!empty($offer['TREE']) && is_array($offer['TREE']))
                {
                    foreach ($offer['TREE'] as $propName => $skuId)
                    {
                        $propId = (int)mb_substr($propName, 5);

                        foreach ($skuProps as $prop)
                        {
                            if ($prop['ID'] == $propId)
                            {
                                foreach ($prop['VALUES'] as $propId => $propValue)
                                {
                                    if ($propId == $skuId)
                                    {
                                        $currentOffersList[] = $propValue['NAME'];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                $offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
                ?>
                <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <meta itemprop="sku" content="<?=htmlspecialcharsbx(implode('/', $currentOffersList))?>" />
                    <meta itemprop="price" content="<?=$offerPrice['RATIO_PRICE']?>" />
                    <meta itemprop="priceCurrency" content="<?=$offerPrice['CURRENCY']?>" />
                    <link itemprop="availability" href="http://schema.org/<?=($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
                </span>
                <?php
            }

            unset($offerPrice, $currentOffersList);
        }
        else
        {
            ?>
            <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                <meta itemprop="price" content="<?=$price['RATIO_PRICE']?>" />
                <meta itemprop="priceCurrency" content="<?=$price['CURRENCY']?>" />
                <link itemprop="availability" href="http://schema.org/<?=($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
            </span>
            <?php
        }
        ?>
    </div>

</div>

<?php
if ($haveOffers)
{
	$offerIds = array();
	$offerCodes = array();

	$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

	foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer)
	{
		$offerIds[] = (int)$jsOffer['ID'];
		$offerCodes[] = $jsOffer['CODE'];

		$fullOffer = $arResult['OFFERS'][$ind];
		$measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

		$strAllProps = '';
		$strMainProps = '';
		$strPriceRangesRatio = '';
		$strPriceRanges = '';

		if ($arResult['SHOW_OFFERS_PROPS'])
		{
			if (!empty($jsOffer['DISPLAY_PROPERTIES']))
			{
				foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property)
				{
					$current = '<dt>'.$property['NAME'].'</dt><dd>'.(
						is_array($property['VALUE'])
							? implode(' / ', $property['VALUE'])
							: $property['VALUE']
						).'</dd>';
					$strAllProps .= $current;

					if (isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'][$property['CODE']]))
					{
						$strMainProps .= $current;
					}
				}

				unset($current);
			}
		}

		if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1)
		{
			$strPriceRangesRatio = '('.Loc::getMessage(
					'CT_BCE_CATALOG_RATIO_PRICE',
					array('#RATIO#' => ($useRatio
							? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
							: '1'
						).' '.$measureName)
				).')';

			foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range)
			{
				if ($range['HASH'] !== 'ZERO-INF')
				{
					$itemPrice = false;

					foreach ($jsOffer['ITEM_PRICES'] as $itemPrice)
					{
						if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
						{
							break;
						}
					}

					if ($itemPrice)
					{
						$strPriceRanges .= '<dt>'.Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_FROM',
								array('#FROM#' => $range['SORT_FROM'].' '.$measureName)
							).' ';

						if (is_infinite($range['SORT_TO']))
						{
							$strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
						}
						else
						{
							$strPriceRanges .= Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_TO',
								array('#TO#' => $range['SORT_TO'].' '.$measureName)
							);
						}

						$strPriceRanges .= '</dt><dd>'.($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']).'</dd>';
					}
				}
			}

			unset($range, $itemPrice);
		}

		$jsOffer['DISPLAY_PROPERTIES'] = $strAllProps;
		$jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps;
		$jsOffer['PRICE_RANGES_RATIO_HTML'] = $strPriceRangesRatio;
		$jsOffer['PRICE_RANGES_HTML'] = $strPriceRanges;
	}

	$templateData['OFFER_IDS'] = $offerIds;
	$templateData['OFFER_CODES'] = $offerCodes;
	unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => true,
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
			'OFFER_GROUP' => $arResult['OFFER_GROUP'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null,
			'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'],
			'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DISPLAY_PREVIEW_TEXT_MODE']
		),
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'VISUAL' => $itemIds,
		'DEFAULT_PICTURE' => array(
			'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
			'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
		),
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'NAME' => $arResult['~NAME'],
			'CATEGORY' => $arResult['CATEGORY_PATH'],
			'DETAIL_TEXT' => $arResult['DETAIL_TEXT'],
			'DETAIL_TEXT_TYPE' => $arResult['DETAIL_TEXT_TYPE'],
			'PREVIEW_TEXT' => $arResult['PREVIEW_TEXT'],
			'PREVIEW_TEXT_TYPE' => $arResult['PREVIEW_TEXT_TYPE']
		),
		'BASKET' => array(
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'BASKET_URL' => $arParams['BASKET_URL'],
			'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		),
		'OFFERS' => $arResult['JS_OFFERS'],
		'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
		'TREE_PROPS' => $skuProps
	);
}
else
{
	$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
	if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties)
	{
		?>
		<div id="<?=$itemIds['BASKET_PROP_DIV']?>" style="display: none;">
			<?php
			if (!empty($arResult['PRODUCT_PROPERTIES_FILL']))
			{
				foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo)
				{
					?>
					<input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>">
					<?php
					unset($arResult['PRODUCT_PROPERTIES'][$propId]);
				}
			}

			$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
			if (!$emptyProductProperties)
			{
				?>
				<table>
					<?php
					foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo)
					{
						?>
						<tr>
							<td><?=$arResult['PROPERTIES'][$propId]['NAME']?></td>
							<td>
								<?php
								if (
									$arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
									&& $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
								)
								{
									foreach ($propInfo['VALUES'] as $valueId => $value)
									{
										?>
										<label>
											<input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]"
												value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? '"checked"' : '')?>>
											<?=$value?>
										</label>
										<br>
										<?php
									}
								}
								else
								{
									?>
									<select name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]">
										<?php
										foreach ($propInfo['VALUES'] as $valueId => $value)
										{
											?>
											<option value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? '"selected"' : '')?>>
												<?=$value?>
											</option>
											<?php
										}
										?>
									</select>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</table>
				<?php
			}
			?>
		</div>
		<?php
	}

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null
		),
		'VISUAL' => $itemIds,
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'PICT' => reset($arResult['MORE_PHOTO']),
			'NAME' => $arResult['~NAME'],
			'SUBSCRIPTION' => true,
			'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'],
			'ITEM_PRICES' => $arResult['ITEM_PRICES'],
			'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'],
			'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'],
			'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
			'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'],
			'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
			'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
			'SLIDER' => $arResult['MORE_PHOTO'],
			'CAN_BUY' => $arResult['CAN_BUY'],
			'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
			'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
			'MAX_QUANTITY' => $arResult['PRODUCT']['QUANTITY'],
			'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
			'CATEGORY' => $arResult['CATEGORY_PATH']
		),
		'BASKET' => array(
			'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
			'EMPTY_PROPS' => $emptyProductProperties,
			'BASKET_URL' => $arParams['BASKET_URL'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		)
	);
	unset($emptyProductProperties);
}

if ($arParams['DISPLAY_COMPARE'])
{
	$jsParams['COMPARE'] = array(
		'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
		'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
		'COMPARE_PATH' => $arParams['COMPARE_PATH']
	);
}

$jsParams["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"] =
	$arResult["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"]
;

?>
<script>
	BX.message({
		ECONOMY_INFO_MESSAGE: '<?=GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2')?>',
		TITLE_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR')?>',
		TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS')?>',
		BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR')?>',
		BTN_SEND_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS')?>',
		BTN_MESSAGE_DETAIL_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
		BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE')?>',
		BTN_MESSAGE_DETAIL_CLOSE_POPUP: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
		TITLE_SUCCESSFUL: '<?=GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK')?>',
		COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK')?>',
		COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
		COMPARE_TITLE: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE')?>',
		BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
		PRODUCT_GIFT_LABEL: '<?=GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL')?>',
		PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX')?>',
		RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
		RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
		SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
	});

	var <?=$obName?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
</script>
<?php
unset($actualItem, $itemIds, $jsParams);
