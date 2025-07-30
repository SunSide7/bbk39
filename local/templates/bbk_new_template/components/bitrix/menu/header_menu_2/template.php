<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

    <ul class="catalog_absolute_left">

        <li>

            <button style="display:none">
                Каталог
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 0.999999L7 7L1 13" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <a href="/catalog/" class="not_show_sub_menu catalog">
                Каталог
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 0.999999L7 7L1 13" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/>
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