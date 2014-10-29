<?php
/*********************************************************************************************

Pagination

*********************************************************************************************/
function wpthemess_paginate($args = null) {
    $defaults = array(
        'page' => null, 'pages' => null,
        'range' => 3, 'gap' => 3, 'anchor' => 1,
        'before' => '<div id="pagination">', 'after' => '</div>',
        //'title' => __('&nbsp;'),
        'nextpage' => __('&raquo;', 'site5framework'), 'previouspage' => __('&laquo;', 'site5framework'),
        'echo' => 1
    );

    $r = wp_parse_args($args, $defaults);
    extract($r, EXTR_SKIP);

    if (!$page && !$pages) {
        global $wp_query;

        $page = get_query_var('paged');
        $page = !empty($page) ? intval($page) : 1;

        $posts_per_page = intval(get_query_var('posts_per_page'));
        $pages = intval(ceil($wp_query->found_posts / $posts_per_page));
        
    }
   // pr($wp_query);

    $output = "";
    if ($pages > 1) {
        //$output .= "$before<span class='page-title'>$title</span>";
        $output .= "$before";
        $ellipsis = "<span class='blacxy-gap'>...</span>";

        if ($page > 1 && !empty($previouspage)) {
            $output .= "<a href='" . get_pagenum_link($page - 1) . "' class='prev' title='previous page'>$previouspage</a>";
        }

        $min_links = $range * 2 + 1;
        $block_min = min($page - $range, $pages - $min_links);
        $block_high = max($page + $range, $min_links);
        $left_gap = (($block_min - $anchor - $gap) > 0) ? true : false;
        $right_gap = (($block_high + $anchor + $gap) < $pages) ? true : false;

        if ($left_gap && !$right_gap) {
            $output .= sprintf('%s%s%s',
                wpthemess_paginate_loop(1, $anchor),
                $ellipsis,
                wpthemess_paginate_loop($block_min, $pages, $page)
            );
        }
        else if ($left_gap && $right_gap) {
            $output .= sprintf('%s%s%s%s%s',
                wpthemess_paginate_loop(1, $anchor),
                $ellipsis,
                wpthemess_paginate_loop($block_min, $block_high, $page),
                $ellipsis,
                wpthemess_paginate_loop(($pages - $anchor + 1), $pages)
            );
        }
        else if ($right_gap && !$left_gap) {
            $output .= sprintf('%s%s%s',
                wpthemess_paginate_loop(1, $block_high, $page),
                $ellipsis,
                wpthemess_paginate_loop(($pages - $anchor + 1), $pages)
            );
        }
        else {
            $output .= wpthemess_paginate_loop(1, $pages, $page);
        }

        if ($page < $pages && !empty($nextpage)) {
            $output .= "<a href='" . get_pagenum_link($page + 1) . "' class='next' title='next page'>$nextpage</a>";
        }

        $output .= $after;
    }

    if ($echo) {
        echo $output;
    }

    return $output;
}

function wpthemess_paginate_loop($start, $max, $page = 0) {
    $output = "";
    for ($i = $start; $i <= $max; $i++) {
        $output .= ($page === intval($i))
            ? "<span class='current tooltip' title='page $i'>$i</span>"
            : "<a href='" . get_pagenum_link($i) . "' class='pages' title='page $i'>$i</a>";
    }
    return $output;
}
?>