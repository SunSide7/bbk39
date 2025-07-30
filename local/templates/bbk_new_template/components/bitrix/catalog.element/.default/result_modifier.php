<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$arResult["MORE_PHOTO"] = array();
if(isset($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"]) && is_array($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"]))
{
    foreach($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $FILE)
    {
        $FILE = CFile::GetFileArray($FILE);
        if(is_array($FILE))
            $arResult["MORE_PHOTO"][]=$FILE;
    }
}

$arResult["DISPLAY_PROPERTIES"] = array();
foreach ($arResult["PROPERTIES"] as $pid => &$arProp)
{
    if((is_array($arProp["VALUE"]) && count($arProp["VALUE"])>0) ||
        (!is_array($arProp["VALUE"]) && strlen($arProp["VALUE"])>0))
    {
        $arResult["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arResult['ITEMS'], $arProp, '');
    }
}

$arResult['SHOW_OFFERS_PROPS'] = true;