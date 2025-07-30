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


<?// echo '<pre>' . print_r($arResult["SECTIONS"], true) . '</pre>' ?>

<div class="main_container">
    <div class="category_product_title">
        <h3 class="main_title mb-0">
            Коллекции  и Технологии
        </h3>
        <? if(false) { ?>
            <p>
                Идейные соображения высшего порядка, а также разбавленное изрядной долей эмпатии, рациональное мышление предоставляет широкие возможности для приоретизации разума над эмоциями.
            </p>
        <? } ?>
    </div>

    <?
    $sectionsArr = [];
    foreach($arResult["SECTIONS"] as $arSection) {
        if (
                $arSection["NAME"] === 'Тротуарная плитка' ||
                $arSection["NAME"] === 'Коллекция «ГРАНИТ»' ||
                $arSection["NAME"] === 'Коллекция «Фридрихштадт»' ||
                $arSection["NAME"] === 'Бордюрный камень'
        ) {
            $sectionsArr[] = $arSection;

            // trotuarnaya-plitka
            // collection-1 - ГРАНИТ
            // knaypkhof-b
            // bordyurnyy-kamen
        }
    } ?>
    <? echo '<pre class="sections-print" style="display:none">' . print_r($sectionsArr, true) . '</pre>'; ?>



    <?
    $idx = 0;
    foreach ($arResult["SECTIONS"] as $arSection):?>

        <? $idx++;

        if(/* Max sections count */ $idx <= 6) { ?>

                <?
                if (false) {
                    // Simplier structure code | UNACTIVE

                    /*
                     * STRUCTURE
                     *      rowX2
                     *          COL-7 + COL-5
                     *      rowX3
                     *          COL-4 * 3
                     */
                    $rowType = $idx < 3
                        ? "category_product_top"
                        : "category_product_bottom";
                    $colLg = null;
                    $isWrapper = false;
                    if($idx == 1) {
                        $isWrapper = true; $colLg = 7;
                    } elseif($idx == 2) {
                        $isWrapper = true; $colLg = 5;
                    } elseif ($idx == 5) {
                        $isWrapper = true; $colLg = 4;
                    } else {
                        $colLg = 4;
                    }
                ?>


                <? if($isWrapper) { ?>
                    <div class="row <?= $rowType ?>">
                <? } ?>

                    <div class="col-lg-7">
                        <div class="category_product_card">
                            <h6><?= $arSection['NAME'] ?></h6>
                            <div class="card_block">
                                <div class="card-inner" style="--clr:#fff;">
                                    <div class="box">
                                        <div class="imgBox">
                                            <img src="<?=CFile::GetPath($arSection['DETAIL_PICTURE'])?>" alt="">
                                        </div>
                                        <div class="icon btn_blue">
                                            <a href="<?= $arSection["SECTION_PAGE_URL"]  ?>" class="iconBox">
                                                <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <? if($isWrapper) { ?>
                    </div>
                <? } ?>


            <?
            }
                else
                {
                    /* Sections BY SWITCH */
                    switch($idx) {

                        // X2 row start
                        case 1:
                            ?>
                            <div class="row category_product_top">
                                <div class="col-lg-7">
<!--                                    <a href="--><?php //= $arSection["SECTION_PAGE_URL"]  ?><!--" class="category_product_card">-->
                                    <a href="/catalog/trotuarnaya-plitka/" class="category_product_card">
                                        <h6><?= $sectionsArr[$idx - 1]['NAME'] ?></h6>
                                        <div class="card_block">
                                            <div class="card-inner" style="--clr:#fff;">
                                                <div class="box">
                                                    <div class="imgBox">
                                                        <img src="<?=CFile::GetPath($sectionsArr[$idx - 1]['DETAIL_PICTURE'])?>" alt="">
                                                    </div>
                                                    <div class="icon btn_blue">
                                                        <div class="iconBox">
                                                            <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?
                            break;

                        // X2 row end | X3 row start
                        case 2:
                            break;
                        case 3:
                            ?>
                                <div class="col-lg-5">
<!--                                    --><?// echo '<pre style="display: none;">' . print_r($arSection, true) . '</pre>'; ?>
<!--                                    <a href="--><?php //= $arSection["SECTION_PAGE_URL"]  ?><!--" class="category_product_card">-->
                                    <a href="/catalog/trotuarnaya-plitka/granit/" class="category_product_card">
                                        <h6><?= $sectionsArr[$idx - 2]['NAME'] ?></h6>
                                        <div class="card_block">
                                            <div class="card-inner" style="--clr:#fff;">
                                                <div class="box">
                                                    <div class="imgBox">
                                                        <img src="<?=CFile::GetPath($sectionsArr[$idx - 2]['DETAIL_PICTURE'])?>" alt="">
                                                    </div>
                                                    <div class="icon btn_blue">
                                                        <div class="iconBox">
                                                            <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="row category_product_bottom">
                            <?
                            break;

                        // Close tag final
//                        case 5:
                        case 6:
                            ?>
<!--                                <div class="col-lg-4">-->
<!--                                    <div class="category_product_card">-->
<!--                                        <h6>--><?php //= $arSection['NAME'] ?><!--</h6>-->
<!--                                        <div class="card_block">-->
<!--                                            <div class="card-inner" style="--clr:#fff;">-->
<!--                                                <div class="box">-->
<!--                                                    <div class="imgBox">-->
<!--                                                        <img src="--><?php //=CFile::GetPath($arSection['DETAIL_PICTURE'])?><!--" alt="">-->
<!--                                                    </div>-->
<!--                                                    <div class="icon btn_blue">-->
<!--                                                        <a href="--><?php //= $arSection["SECTION_PAGE_URL"]  ?><!--" class="iconBox">-->
<!--                                                            <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                                                                <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>-->
<!--                                                            </svg>-->
<!--                                                        </a>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->

                                <div class="col-lg-4">
                                    <a href="/tekhnologiya-long-life/" class="category_product_card">
                                        <h6>Технология Long Life</h6>
                                        <div class="card_block">
                                            <div class="card-inner" style="--clr:#fff;">
                                                <div class="box">
                                                    <div class="imgBox">
<!--                                                        <img src="/upload/iblock/a9e/l9d501ydt83xduer2zq1qc370bkwwkgg.png" alt="">-->
                                                        <img src="/upload/iblock/a9e/ll.jpg" alt="">
                                                    </div>
                                                    <div class="icon btn_blue">
                                                        <div class="iconBox">
                                                            <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>


                            </div>
                            <?

                            break;

                        case 4:
                            ?>
                            <div class="col-lg-4">
                                <a href="/catalog/trotuarnaya-plitka/kollektsiya-fridrikhshtadt/" class="category_product_card">
                                    <h6><?= $sectionsArr[$idx - 2]['NAME'] ?></h6>
                                    <div class="card_block">
                                        <div class="card-inner" style="--clr:#fff;">
                                            <div class="box">
                                                <div class="imgBox">
                                                    <img src="<?=CFile::GetPath($sectionsArr[$idx - 2]['DETAIL_PICTURE'])?>" alt="">
                                                </div>
                                                <div class="icon btn_blue">
                                                    <div class="iconBox">
                                                        <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?
                            break;

                        case 5:
                            ?>
                            <div class="col-lg-4">
                                <a href="/catalog/bordyurnyy-kamen/" class="category_product_card">
                                    <h6><?= $sectionsArr[$idx - 2]['NAME'] ?></h6>
                                    <div class="card_block">
                                        <div class="card-inner" style="--clr:#fff;">
                                            <div class="box">
                                                <div class="imgBox">
                                                    <img src="<?=CFile::GetPath($sectionsArr[$idx - 2]['DETAIL_PICTURE'])?>" alt="">
                                                </div>
                                                <div class="icon btn_blue">
                                                    <div class="iconBox">
                                                        <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?
                            break;





                        // Regular | X3 row items
                        default:
                            ?>
                                <div class="col-lg-4">
                                    <a href="<?= $arSection["SECTION_PAGE_URL"]  ?>" class="category_product_card">
                                        <h6><?= $arSection['NAME'] ?></h6>
                                        <div class="card_block">
                                            <div class="card-inner" style="--clr:#fff;">
                                                <div class="box">
                                                    <div class="imgBox">
                                                        <img src="<?=CFile::GetPath($arSection['DETAIL_PICTURE'])?>" alt="">
                                                    </div>
                                                    <div class="icon btn_blue">
                                                        <div class="iconBox">
                                                            <svg width="42" height="16" viewBox="0 0 42 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M41.2071 8.70711C41.5976 8.31658 41.5976 7.68342 41.2071 7.29289L34.8431 0.928932C34.4526 0.538408 33.8195 0.538408 33.4289 0.928932C33.0384 1.31946 33.0384 1.95262 33.4289 2.34315L39.0858 8L33.4289 13.6569C33.0384 14.0474 33.0384 14.6805 33.4289 15.0711C33.8195 15.4616 34.4526 15.4616 34.8431 15.0711L41.2071 8.70711ZM0.5 9H40.5V7H0.5V9Z" fill="white"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?
                            break;
                            ?>
                        <? }
                }
            ?>




            <? if (/* Subsections if needed */ false) { ?>
                <!-- Подразделы -->
                <ul>
                    <?foreach ($arSection["ITEMS"] as $arItem):?>
                        <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
                        </li>
                    <?endforeach?>
                </ul>
            <? } ?>

        <? } ?>
    <?endforeach?>

</div>



<?//if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
<!--	<br />--><?php //=$arResult["NAV_STRING"]?>
<?//endif;?>
<!--</div>-->
