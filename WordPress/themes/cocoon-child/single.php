<?php
/**
 * 投稿詳細ページテンプレート。
 *
 * ヘッダー・フッターは子テーマ共通の header.php / footer.php を使用する。
 * パンくず（tmp/breadcrumbs）・サイドバーウィジェット（get_sidebar）・
 * 関連記事（tmp/related-entries）はCocoon本体の仕組みをそのまま再利用し、
 * 記事本文まわりのみ独自のシンプルなレイアウトで実装する。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="cl-single">
	<div class="cl-single-grid">

		<div class="cl-single-main">

			<?php cocoon_template_part( 'tmp/breadcrumbs' ); ?>

			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cl-article' ); ?>>

						<header class="cl-article-header">
							<h1 class="cl-article-title"><?php the_title(); ?></h1>
							<div class="cl-article-meta">
								<time class="cl-article-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<?php echo esc_html( get_the_date() ); ?>
								</time>
								<?php
								$cl_categories = get_the_category();
								if ( $cl_categories && ! is_wp_error( $cl_categories ) ) :
									?>
									<span class="cl-article-cats">
										<?php foreach ( $cl_categories as $cl_category ) : ?>
											<a href="<?php echo esc_url( get_category_link( $cl_category ) ); ?>"><?php echo esc_html( $cl_category->name ); ?></a>
										<?php endforeach; ?>
									</span>
								<?php endif; ?>
							</div>
						</header>

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="cl-article-thumb">
								<?php
								the_post_thumbnail(
									'large',
									array(
										'class'         => 'cl-article-thumb-img',
										'loading'       => 'eager',
										'fetchpriority' => 'high',
									)
								);
								?>
							</div>
						<?php endif; ?>

						<div class="cl-article-content">
							<?php the_content(); ?>
						</div>

						<?php
						$cl_tags = get_the_tags();
						if ( $cl_tags && ! is_wp_error( $cl_tags ) ) :
							?>
							<ul class="cl-article-tags">
								<?php foreach ( $cl_tags as $cl_tag ) : ?>
									<li><a href="<?php echo esc_url( get_tag_link( $cl_tag ) ); ?>">#<?php echo esc_html( $cl_tag->name ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

					</article>
					<?php
				endwhile;
			endif;
			?>

			<?php cocoon_template_part( 'tmp/related-entries' ); ?>

		</div>

		<aside class="cl-single-sidebar">
			<?php get_sidebar(); ?>
		</aside>

	</div>
</div>
<?php get_footer(); ?>
