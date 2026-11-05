<?php
/** Main template for the validated site and existing WordPress content. */
defined('ABSPATH') || exit;
$view = get_post_meta(get_queried_object_id(), '_hakim_site_view', true);
if (isset(hakim_site_pages()[$view])) {
    hakim_render_site($view);
    return;
}
// Keep existing posts, pages, archives and 404 responses accessible.
?><!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?php echo esc_html(wp_get_document_title()); ?></title><link rel="stylesheet" href="<?php echo esc_url(get_theme_file_uri('site/style.css')); ?>"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<main style="max-width:900px;margin:48px auto;padding:24px"><a href="<?php echo esc_url(home_url('/')); ?>">← Abdelhakim Taleb</a>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article><h1 class="serif"><?php the_title(); ?></h1><?php the_content(); ?></article>
<?php endwhile; the_posts_navigation(); else : ?><h1>Page introuvable</h1><p>Vous pouvez revenir à l’accueil grâce au lien ci-dessus.</p><?php endif; ?>
</main><?php wp_footer(); ?></body></html>
