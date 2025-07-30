<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

    <ul class="header_menu_link" style="padding-left: 0;">
        <li>
<!--            <a href="#" class="catalog_btn">-->
<!--                Каталог-->
<!--                <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                    <path d="M6 3.99805L1.5 7.02913L1.5 0.966958L6 3.99805Z" fill="#003064"/>-->
<!--                </svg>-->
<!--            </a>-->

            <a href="/catalog/" class="catalog_btn disabled">
                Каталог
                <svg width="6" height="8" viewBox="0 0 6 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 3.99805L1.5 7.02913L1.5 0.966958L6 3.99805Z" fill="#003064"/>
                </svg>
            </a>
        </li>




        <?
        foreach($arResult as $arItem):
            if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
                continue;
        ?>
            <?if($arItem["SELECTED"]):?>
                <li><a href="<?=$arItem["LINK"]?>" class="selected"><?=$arItem["TEXT"]?></a></li>
            <?else:?>
                <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
            <?endif?>

        <?endforeach?>
    </ul>
<?endif?>