<?php

/**
 * @var array $propertiesData
 */
$fieldSlug = $propertiesData['content']['field']['repeater_field'];
$blockId = $propertiesData['content']['repeated_block']['global_block'];
$postTag = $propertiesData['content']['repeated_block']['tag'] ?? 'article';

/** @var \Breakdance\DynamicData\RepeaterField $field */
$field = \Breakdance\DynamicData\DynamicDataController::getInstance()->getField($fieldSlug);

$layout = (string) ($propertiesData['design']['list']['layout'] ?: '');
if ($layout == "list") {
    $wrapperClass = 'bde-dynamic-repeater-list';
} else if ($layout == "slider") {
    $wrapperClass = 'bde-dynamic-repeater-slider swiper-wrapper';
} else {
    $wrapperClass = 'bde-dynamic-repeater-grid';
}

if ($field) {
    $isOption = $field->field['is_option_page'] ?? false;
    $postId = $isOption ? 'option' : get_the_ID();
    $swiperClass = ($layout == 'slider' ? ' swiper-slide' : '');

    $i = 1;
    echo '<div class="ee-posts bde-dynamic-repeater bde-dynamic-repeater-%%ID%% ' . $wrapperClass . '">';
    while ($field->hasSubFields($postId)) {
        echo '<' . $postTag . ' class="ee-post bde-dynamic-repeater-item'. $swiperClass .'">';
        echo \Breakdance\Render\render($blockId, "{$postId}-{$i}");
        echo '</' . $postTag . '>';
        $i++;
    }
    echo '</div>';
}
