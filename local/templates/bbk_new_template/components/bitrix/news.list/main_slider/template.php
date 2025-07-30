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
<?//if($arParams["DISPLAY_TOP_PAGER"]):?>
<!--	--><?php //=$arResult["NAV_STRING"]?><!--<br />-->
<?//endif;?>



<div class="swiper home_Swiper">
    <div class="container">
        <button class="home-button-prev home_slider_btn desktop_slider_icon">
            <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/home_slider_left.svg" alt="">
        </button>
        <button class="home-button-next home_slider_btn desktop_slider_icon">
            <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/home_slider_right.svg" alt="">
        </button>
    </div>

    <div class="swiper-wrapper">
    <?foreach($arResult["ITEMS"] as $arItem):?>

            <div class="swiper-slide">
                <img class="slider_img" src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="">
                <div class="main_container">
                    <div class="home_content">
                        <h1><?= $arItem['NAME'] ?></h1>
                        <p><?= $arItem['PREVIEW_TEXT'] ?></p>
                        <div class="home_btn">
                            <a href="<?= $arItem['PROPERTIES']['ATT_LINK']['VALUE'] ?>" class="btn_blue">
                                <span><?= $arItem['PROPERTIES']['ATT_LINK']['DESCRIPTION'] ?></span>
                                <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

    <?endforeach;?>
    </div>

<!--    <button class="home-button-next home_slider_btn desktop_slider_icon">-->
<!--        <img src="--><?php //= SITE_TEMPLATE_PATH ?><!--/images/icons/home_slider_right.svg" alt="">-->
<!--    </button>-->

    <div class="media_slider_btn">
        <button class="home-button-prev home_slider_btn">
            <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/home_slider_left.svg" alt="">
        </button>
        <button class="home-button-next home_slider_btn">
            <img src="<?= SITE_TEMPLATE_PATH ?>/images/icons/home_slider_right.svg" alt="">
        </button>
    </div>
</div>


<?//if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
<!--	<br />--><?php //=$arResult["NAV_STRING"]?>
<?//endif;?>
<!--</div>-->
