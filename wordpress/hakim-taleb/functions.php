<?php
/** WordPress integration; the validated HTML/CSS remains the source of the design. */
defined('ABSPATH') || exit;

function hakim_site_pages() {
    return array(
        'index.html' => 'Accueil — Abdelhakim Taleb',
        'kinesiologie.html' => 'La kinésiologie',
        'blog/index.html' => 'Le carnet des passages',
        'blog/le-sac.html' => 'Le sac',
        'blog/le-masque.html' => 'Le masque',
        'blog/le-miroir.html' => 'Le miroir',
        'blog/lacher-prise.html' => 'Le lâcher-prise',
        'mentions-legales.html' => 'Mentions légales',
        'confidentialite.html' => 'Confidentialité',
    );
}

function hakim_prepare_pages() {
    $ids = array();
    foreach (hakim_site_pages() as $view => $title) {
        $existing = get_posts(array('post_type' => 'page', 'post_status' => array('publish', 'draft', 'private', 'pending', 'future'), 'meta_key' => '_hakim_site_view', 'meta_value' => $view, 'numberposts' => 1));
        if ($existing) {
            $id = $existing[0]->ID;
        } else {
            $id = wp_insert_post(array('post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $view === 'index.html' ? 'accueil-hakim' : ($view === 'blog/index.html' ? 'le-carnet' : basename($view, '.html')), 'post_content' => '', 'meta_input' => array('_hakim_site_view' => $view)), true);
        }
        if (!is_wp_error($id) && $id) {
            $ids[$view] = (int) $id;
        }
    }
    update_option('hakim_site_pages', $ids);
    if (isset($ids['index.html'])) {
        if (get_option('hakim_previous_home', null) === null) {
            add_option('hakim_previous_home', array('show_on_front' => get_option('show_on_front'), 'page_on_front' => get_option('page_on_front'), 'page_for_posts' => get_option('page_for_posts')), '', false);
        }
        update_option('show_on_front', 'page');
        update_option('page_on_front', $ids['index.html']);
        // The old posts page remains a page, without taking precedence over the new home.
        if ((int) get_option('page_for_posts') === $ids['index.html']) {
            update_option('page_for_posts', 0);
        }
    }
}
add_action('after_switch_theme', 'hakim_prepare_pages');

function hakim_restore_previous_home($new_name, $new_theme, $old_theme) {
    if (!$old_theme || $old_theme->get_stylesheet() !== 'hakim-taleb' || $new_theme->get_stylesheet() === 'hakim-taleb') { return; }
    $previous = get_option('hakim_previous_home');
    $ids = get_option('hakim_site_pages', array());
    if (is_array($previous) && isset($ids['index.html']) && (int) get_option('page_on_front') === (int) $ids['index.html']) {
        foreach ($previous as $key => $value) { update_option($key, $value); }
        delete_option('hakim_previous_home');
    }
}
add_action('switch_theme', 'hakim_restore_previous_home', 10, 3);

function hakim_view_url($view) {
    $ids = get_option('hakim_site_pages', array());
    return isset($ids[$view]) ? get_permalink($ids[$view]) : home_url('/');
}

function hakim_resolve_path($view, $relative) {
    $parts = explode('/', dirname($view) . '/' . $relative);
    $clean = array();
    foreach ($parts as $part) {
        if ($part === '' || $part === '.') { continue; }
        if ($part === '..') { array_pop($clean); } else { $clean[] = $part; }
    }
    $path = implode('/', $clean);
    return substr($relative, -1) === '/' ? $path . '/index.html' : $path;
}

function hakim_render_site($view) {
    $html = file_get_contents(get_theme_file_path('site/' . $view));
    // WordPress controls indexing through Settings > Reading (and SEO plugins).
    $html = str_replace('<meta name="robots" content="noindex">', '', $html);
    $html = preg_replace_callback('~\b(href|src|data-home)="([^"]*)"~', function ($m) use ($view) {
        $url = html_entity_decode($m[2], ENT_QUOTES, 'UTF-8');
        if ($url === '' || preg_match('~^(?:[a-z][a-z0-9+.-]*:|//|\#)~i', $url)) { return $m[0]; }
        $parts = wp_parse_url($url);
        if (!$parts || empty($parts['path'])) { return $m[0]; }
        $path = hakim_resolve_path($view, $parts['path']);
        $extra = '';
        if (isset(hakim_site_pages()[$path])) {
            $target = hakim_view_url($path);
            if ($m[1] === 'href' && $path === 'index.html') { $extra = ' data-site-home'; }
            if ($m[1] === 'href' && $path === 'kinesiologie.html') { $extra = ' data-site-kine'; }
        } else {
            $target = get_theme_file_uri('site/' . $path);
        }
        if (!empty($parts['query'])) { parse_str($parts['query'], $query); $target = add_query_arg($query, $target); }
        if (isset($parts['fragment'])) { $target .= '#' . $parts['fragment']; }
        return $m[1] . '="' . esc_url($target) . '"' . $extra;
    }, $html);
    // Preserve the kinésiologie modal with native WordPress URLs (including plain permalinks).
    $html = str_replace("cadre.src='kinesiologie.html?fenetre=1'", 'cadre.src=' . wp_json_encode(add_query_arg('fenetre', '1', hakim_view_url('kinesiologie.html'))), $html);
    $html = str_replace('a[href$="kinesiologie.html"],a[data-goto^="kinesiologie.html"]', 'a[data-site-kine]', $html);
    $html = str_replace('/index\\.html/.test(g)', "a.hasAttribute('data-site-home')", $html);
    ob_start(); wp_head(); $head = ob_get_clean();
    $html = str_replace('</head>', $head . '</head>', $html);
    ob_start(); wp_body_open(); $body = ob_get_clean();
    $html = preg_replace('~<body([^>]*)>~', '<body$1>' . $body, $html, 1);
    ob_start(); wp_footer(); $footer = ob_get_clean();
    $html = str_replace('</body>', $footer . '</body>', $html);
    echo $html; // Trusted theme templates; dynamic URLs are escaped above.
}
