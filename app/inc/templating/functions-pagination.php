<?php
/**
 * Helper functions to generate pagination links.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

namespace RecyleBin\PhpSSS;

// Exit if accessed directly.
defined('ABSPATH') || exit;

if (!function_exists('emi_generate_paging')) :
    /**
     * Prints pagination links for given parameters.
     *
     * If your theme uses twitter bootstrap styles, define a constant :
     * define('BOOTSTRAP_ACTIVE', true)
     * and this function will generate the bootstrap-pagination-compliant html.
     *
     * @author @ckchaudhary
     * @param  int    $total_items        total number of items(grand total)
     * @param  int    $items_per_page     number of items displayed per page
     * @param  int    $curr_paged         current page number, 1 based index
     * @param  string $base_url           part of url before the '/page/2/' part. If not supplied, current url is considered.
     * @param  string $hashlink           Optional, the '#' link to be appended ot url, optional
     * @param  int    $links_on_each_side Optional, how many links to be displayed on each side of current active page. Default 2.
     *
     * @return void
     */
    function emi_generate_paging($total_items, $items_per_page, $curr_paged, $base_url = '', $links_on_each_side = 2, $hashlink = "")
    {
        $use_bootstrap = false;
        if (defined('BOOTSTRAP_ACTIVE')) {
            $use_bootstrap = true;
        }
    
        $s = $links_on_each_side; //no of tabs to show for previos/next paged links
        if ($curr_paged == 0) {
            $curr_paged=1;
        }
    
        /*$elements : an array of arrays; each child array will have following structure
        $child[0] = text of the link
        $child[1] = page no of target page
        $child[2] = link type :: link|current|nolink
        */
        $elements = array();
    
        $no_of_pages = ceil($total_items/$items_per_page);
    
        //prev lik
        if ($curr_paged > 1) {
            $elements[] = array('&laquo; Prev', $curr_paged-1, 'link');
        }
    
        //generating $s(2) links before the current one
        if ($curr_paged > 1) {
            $rev_array = array();//paged in reverse order
            $i = $curr_paged-1;
            $counter = 0;
            while ($counter<$s && $i>0) {
                $rev_array[] = $i;
                $i--;
                $counter++;
            }
            $arr = array_reverse($rev_array);
            if ($counter==$s) {
                $elements[] = array(' ... ', '', 'nolink');
            }
            foreach ($arr as $el) {
                $elements[] = array($el, $el, 'link');
            }
            unset($rev_array);
            unset($arr);
            unset($i);
            unset($counter);
        }
    
        //generating $s+1(3) links after the current one (includes current)
        if ($curr_paged <= $no_of_pages) {
            $i = $curr_paged;
            $counter = 0;
            while ($counter<$s+1 && $i<=$no_of_pages) {
                if ($i==$curr_paged) {
                    $elements[] = array($i, $i, 'current');
                } else {
                    $elements[] = array($i, $i, 'link');
                }
                $counter++;
                $i++;
            }
            if ($counter==$s+1) {
                $elements[] = array(' ... ', '', 'nolink');
            }
            unset($i);
            unset($counter);
        }
        //next link
        if ($curr_paged < $no_of_pages) {
            $elements[] = array('Next &raquo;', $curr_paged+1, 'link');
        }
    
        /*enough php, lets echo some html*/
        if (isset($elements) && count($elements) > 1) {?>
        <div class="navigation">
            <?php if ($use_bootstrap) :?>
                <ul class='pagination'>
            <?php else : ?>
                <div class="wp-paginate">
            <?php endif;?>
                        <?php
                        foreach ($elements as $e) {
                            $link_html = "";
                            $class = "";
                            switch ($e[2]) {
                                case 'link':
                                    $base_link = trailingslashit($base_url) . "page/$e[1]/";
                            
                                    if (!empty($_GET)) {
                                           $base_link .= '?';
                                        foreach ($_GET as $k => $v) {
                                            $base_link .= "$k=$v&";
                                        }
                                    }
                            
                                    $base_link = trim($base_link, "&");
                                    if (isset($hashlink) && $hashlink!="") {
                                        $base_link .="#$hashlink";
                                    }
                                    $link_html = "<a href='$base_link' title='$e[0]' class='page-numbers'>$e[0]</a>";
                                    break;
                                case 'current':
                                    $class = "active";
                                    if ($use_bootstrap) {
                                        $link_html = "<span>$e[0] <span class='sr-only'>(current)</span></span>";
                                    } else {
                                        $link_html = "<span class='page-numbers current'>$e[0]</span>";
                                    }
                                    break;
                                default:
                                    if ($use_bootstrap) {
                                        $link_html = "<span>$e[0]</span>";
                                    } else {
                                        $link_html = "<span class='page-numbers'>$e[0]</span>";
                                    }
                                    break;
                            }

                            if ($use_bootstrap) {
                                $link_html = "<li class='". esc_attr($class) ."'>" . $link_html . "</li>";
                            } else {
                                $link_html = "<span class='". esc_attr($class) ."'>" . $link_html . "</span>";
                            }
                    
                            echo $link_html;
                        }
                        ?>
            <?php if ($use_bootstrap) :?>
                </ul>
            <?php else :?>
                </div>
            <?php endif;?>
        </div>
        
            <?php
        }
    }
endif;
