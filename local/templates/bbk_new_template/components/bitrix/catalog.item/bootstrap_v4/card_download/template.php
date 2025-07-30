<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var array $actualItem
 * @var array $minOffer
 * @var array $itemIds
 * @var array $price
 * @var array $measureRatio
 * @var bool $haveOffers
 * @var bool $showSubscribe
 * @var array $morePhoto
 * @var bool $showSlider
 * @var bool $itemHasDetailUrl
 * @var string $imgTitle
 * @var string $productTitle
 * @var string $buttonSizeClass
 * @var string $discountPositionClass
 * @var string $labelPositionClass
 * @var CatalogSectionComponent $component
 */
?>

<?// echo '<pre>' . print_r($arResult, true) . '</pre>' ?>
<div class="for_designers_card">

    <?
    $imgSliderArr = [];
    $img = CFile::ResizeImageGet($item["DETAIL_PICTURE"], array(), BX_RESIZE_IMAGE_EXACT, true);
    $imgSliderArr[0] = $img['src']; // [SRC]
    foreach($item['PROPERTIES']['PROP_PICTURES_SLIDER']['VALUE'] as $idx => $id) {
        $imgSliderArr[$idx + 1] = CFile::GetFileArray($id)['SRC'];
    }
    ?>

    <!-- IMAGE -->
	<? if ($itemHasDetailUrl): ?>
	<a class="product-item-image-wrapper" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$imgTitle?>"
		data-entity="image-wrapper">
    <? else: ?>
    <span class="product-item-image-wrapper" data-entity="image-wrapper">
	<? endif; ?>

        <? if (false) { ?>

            <span class="product-item-image-slider-slide-container slide" id="<?=$itemIds['PICT_SLIDER']?>"
                <?=($showSlider ? '' : 'style="display: none;"')?>
                data-slider-interval="<?=$arParams['SLIDER_INTERVAL']?>" data-slider-wrap="true">
                <?
                if ($showSlider)
                {
                    foreach ($morePhoto as $key => $photo)
                    {
                        ?>
                        <span class="product-item-image-slide item <?=($key == 0 ? 'active' : '')?>" style="background-image: url('<?=$photo['SRC']?>');"></span>
                        <?
                    }
                }
                ?>
            </span>
            <span class="product-item-image-original" id="<?=$itemIds['PICT']?>" style="background-image: url('<?=$item['PREVIEW_PICTURE']['SRC']?>'); <?=($showSlider ? 'display: none;' : '')?>"></span>


            <?
            if ($item['SECOND_PICT'])
            {
                $bgImage = !empty($item['PREVIEW_PICTURE_SECOND']) ? $item['PREVIEW_PICTURE_SECOND']['SRC'] : $item['PREVIEW_PICTURE']['SRC'];
                ?>
                <span class="product-item-image-alternative" id="<?=$itemIds['SECOND_PICT']?>" style="background-image: url('<?=$bgImage?>'); <?=($showSlider ? 'display: none;' : '')?>"></span>
                <?
            }

            if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y')
            {
                ?>
                <div class="product-item-label-ring <?=$discountPositionClass?>" id="<?=$itemIds['DSC_PERC']?>"
                    <?=($price['PERCENT'] > 0 ? '' : 'style="display: none;"')?>>
                    <span><?=-$price['PERCENT']?>%</span>
                </div>
                <?
            }

            if ($item['LABEL'])
            {
                ?>
                <div class="product-item-label-text <?=$labelPositionClass?>" id="<?=$itemIds['STICKER_ID']?>">
                    <?
                    if (!empty($item['LABEL_ARRAY_VALUE']))
                    {
                        foreach ($item['LABEL_ARRAY_VALUE'] as $code => $value)
                        {
                            ?>
                            <div<?=(!isset($item['LABEL_PROP_MOBILE'][$code]) ? ' class="d-none d-sm-block"' : '')?>>
                                <span title="<?=$value?>"><?=$value?></span>
                            </div>
                            <?
                        }
                    }
                    ?>
                </div>
                <?
            }
            ?>
            <span class="product-item-image-slider-control-container" id="<?=$itemIds['PICT_SLIDER']?>_indicator"
                <?=($showSlider ? '' : 'style="display: none;"')?>>
                <?
                if ($showSlider)
                {
                    foreach ($morePhoto as $key => $photo)
                    {
                        ?>
                        <span class="product-item-image-slider-control<?=($key == 0 ? ' active' : '')?>" data-go-to="<?=$key?>"></span>
                        <?
                    }
                }
                ?>
            </span>
            <?
            if ($arParams['SLIDER_PROGRESS'] === 'Y')
            {
                ?>
                <span class="product-item-image-slider-progress-bar-container">
                    <span class="product-item-image-slider-progress-bar" id="<?=$itemIds['PICT_SLIDER']?>_progress_bar" style="width: 0;"></span>
                </span>
                <?
            }
            ?>

        <? } else { ?>
            <div class="swiper productSlider">
              <div class="swiper-wrapper">

<!--                  --><?// foreach ($morePhoto as $key => $photo)
//                  { ?>
<!--                        <div class="swiper-slide">-->
<!--                          <img src="--><?php //=$photo['SRC']?><!--" alt="">-->
<!--                        </div>-->
<!--                  --><?// } ?>

                  <? foreach ($imgSliderArr as $sliderImg)
                  { ?>
                        <div class="swiper-slide">
                          <img src="<?=$sliderImg?>" alt="">
                        </div>
                  <? } ?>



<!--                <div class="swiper-slide">-->
<!--                  <img src="/images/test-slider-images/productslider1.png" alt="">-->
<!--                </div>-->
<!--                <div class="swiper-slide">-->
<!--                  <img src="/images/test-slider-images/productslider1.png" alt="">-->
<!--                </div>-->
<!--                <div class="swiper-slide">-->
<!--                  <img src="/images/test-slider-images/productslider1.png" alt="">-->
<!--                </div>-->
<!--                <div class="swiper-slide">-->
<!--                  <img src="/images/test-slider-images/productslider1.png" alt="">-->
<!--                </div>-->
              </div>
              <div class="swiper-pagination"></div>
            </div>
        <? } ?>


    <? if ($itemHasDetailUrl): ?>
        </a>
    <? else: ?>
        </span>
    <? endif; ?>


    <!-- BOTTOM CONTENT -->
    <div class="for_designers_card_text">

        <h6>
            <? if ($itemHasDetailUrl): ?>
            <a href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$productTitle?>">
            <? endif; ?>
                <?=$productTitle?>
            <? if ($itemHasDetailUrl): ?>
            </a>
            <? endif; ?>
        </h6>

        <a href="#" class="btn_blue">
            Подробнее DOWNLOAD
        </a>

    </div>


</div>