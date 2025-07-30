<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

    <ul>
        <li>
<!--            <button class="catalog_absolute_btn">-->
<!--                <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                    <ellipse cx="1.75" cy="1" rx="1.5" ry="1" fill="white"/>-->
<!--                    <path d="M6.25 1H18.75" stroke="white" stroke-width="2" stroke-linecap="round"/>-->
<!--                    <ellipse cx="1.75" cy="7" rx="1.5" ry="1" fill="white"/>-->
<!--                    <path d="M6.25 7H18.75" stroke="white" stroke-width="2" stroke-linecap="round"/>-->
<!--                    <ellipse cx="1.75" cy="13" rx="1.5" ry="1" fill="white"/>-->
<!--                    <path d="M6.25 13H18.75" stroke="white" stroke-width="2" stroke-linecap="round"/>-->
<!--                </svg>-->
<!--                Каталог-->
<!--            </button>-->
            <a href="/catalog/" class="catalog_absolute_btn not_show_sub_menu">
                <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="1.75" cy="1" rx="1.5" ry="1" fill="white"/>
                    <path d="M6.25 1H18.75" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    <ellipse cx="1.75" cy="7" rx="1.5" ry="1" fill="white"/>
                    <path d="M6.25 7H18.75" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    <ellipse cx="1.75" cy="13" rx="1.5" ry="1" fill="white"/>
                    <path d="M6.25 13H18.75" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Каталог
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