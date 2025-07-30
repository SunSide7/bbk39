<?

$rsSections = CIBlockSection::GetList(
    Array("SORT" => "ASC"),
    Array(
        "=IBLOCK_ID" => $arParams['IBLOCK_ID'],
        "=ACTIVE"    => "Y",
        'INCLUDE_SUBSECTIONS' =>'Y',
    )
);


//$dbSections = Bitrix\Iblock\SectionTable::getList([
//        'select' => ['ID', 'INNER_ID' => 'INNER_SECTION.ID', 'NAME' => 'NAME'],
//        'filter' => [
//                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
//    ],
//    'runtime' => [
//    'INNER_SECTION' => [
//        'data_type' => Bitrix\Iblock\SectionTable::class,
//        'reference' => [
//            '<this.LEFT_MARGIN' => 'ref.LEFT_MARGIN',
//            '>this.RIGHT_MARGIN' => 'ref.RIGHT_MARGIN',
//            'this.IBLOCK_ID' => 'ref.IBLOCK_ID'
//        ],
//        'join_type' => 'LEFT'
//    ]
//]
//])->fetchAll();

//echo '<pre>' . print_r($dbSections, true) . '</pre>';



// Тут вместо инкрементного индекса, ID раздела
while ($arSection = $rsSections->GetNext())
    $arSections[$arSection['ID']] = $arSection;

// По нему производим неявную фильрацию
foreach($arResult["ITEMS"] as $arItem) {
    $arSections[$arItem['IBLOCK_SECTION_ID']]['ITEMS'][] = $arItem;
}

$arResult["SECTIONS"] = $arSections;

//echo '<pre>' . print_r($arResult["SECTIONS"], true) . '</pre>';
