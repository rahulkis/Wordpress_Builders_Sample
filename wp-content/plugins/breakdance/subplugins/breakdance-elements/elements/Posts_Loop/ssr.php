<?php

/**
 * @var array $propertiesData
 * @var boolean $renderOnlyIndividualPosts this one is used for "load more" ajax and comes from pagination.php
 */

use function Breakdance\Util\WP\isAnyArchive;
use function Breakdance\WpQueryControl\setupIsotopeFilterBar;
use function Breakdance\WpQueryControl\getFilterAttributesForPost;

showWarningInBuilderForImproperUseOfPaginationAndCustomQueriesOnArchives(
    $propertiesData['content']['query']['query'],
    $propertiesData['content']['pagination']['pagination'],
    isAnyArchive()
);

$paged = ($propertiesData['content']['pagination']['pagination'] ?? false) ? \Breakdance\WpQueryControl\getPage() : 0;

global $post;
$initialGlobalPost = $post;

/** @var array $contentProps */
$contentProps = $propertiesData['content'];

$postTag = $contentProps['repeated_block']['tag'] ?? 'article';

$argsFromQuery = \Breakdance\WpQueryControl\getWpQueryArgumentsFromWpQueryControlProperties(
    $contentProps['query']['query'],
    [
        'paged' => $paged > 1 ? $paged : null,
    ]
);

// Layout
$layout = (string) ($propertiesData['design']['list']['layout'] ?: '');

$loop = new WP_Query($argsFromQuery);

// Filter Bar
$filterbar = setupIsotopeFilterBar([
    'settings' => $propertiesData['content']['filter_bar'],
    'design' => $propertiesData['design']['filter_bar'],
    'query' => $loop
]);

// used in JS too
$wrapperClass = 'ee-posts';

$actionData = ['propertiesData' => $propertiesData];

do_action("breakdance_posts_loop_before_loop", $actionData);

if (!$renderOnlyIndividualPosts) {
    \Breakdance\WpQueryControl\renderIsotoperFilterBar($filterbar);

    if ($filterbar['enable']) {
        $wrapperClass .= ' ee-posts-isotope';
    }

    if ($layout == "slider") {
        $wrapperClass .= ' swiper-wrapper';

        \Breakdance\WpQueryControl\renderSwiperContainer();
    }

    echo "<div class=\"{$wrapperClass} ee-posts-{$layout}\">";
}

while ($loop->have_posts()) : $loop->the_post();
    $itemClasses = 'ee-post';

    if ($layout == 'slider') {
        $itemClasses .= ' swiper-slide';
    }

    $attrs = getFilterAttributesForPost($filterbar, $itemClasses);

    $blockId = $propertiesData['content']['repeated_block']['global_block'] ?? false;

    do_action("breakdance_posts_loop_before_post", $actionData);
?>
    <<?php echo $postTag; ?> <?php echo $attrs; ?>>
        <?php
        if ($blockId) {
            $postId = get_the_ID();
            echo \Breakdance\Render\render($blockId, $postId);
        } else {
            if ($_REQUEST['triggeringDocument'] ?? true) {
                echo '<div class="breakdance-empty-ssr-message">Choose a Global Block from the dropdown.</div>';
            } else {
                echo "<!-- Breakdance error: $blockId not found -->";
            }
        }
        ?>
    </<?php echo $postTag; ?>>
<?php
    do_action("breakdance_posts_loop_after_post", $actionData);
endwhile;

if (!$renderOnlyIndividualPosts) {
    \Breakdance\WpQueryControl\renderIsotopeFooter($filterbar);

    echo "</div>"; // close wrapper

    if ($layout == "slider") {
        \Breakdance\WpQueryControl\closeSwiperContainer($propertiesData['design']['list']['slider']);
    }
}

do_action("breakdance_posts_loop_after_loop", $actionData);

\Breakdance\EssentialElements\Lib\PostsPagination\getPostsPaginationFromProperties(
    $propertiesData,
    $loop->max_num_pages,
    $layout,
    \Breakdance\Util\getDirectoryPathRelativeToPluginFolder(__FILE__)
);

do_action("breakdance_posts_loop_after_pagination", $actionData);

wp_reset_postdata();

// If these IDs don't match after resetting the postdata,
// this is a nested post loop, so we need to set the
// post data back to the original value
if ($post->ID !== $initialGlobalPost->ID) {
    $GLOBALS['post'] = $initialGlobalPost;
}
