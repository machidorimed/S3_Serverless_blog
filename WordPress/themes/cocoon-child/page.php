<?php
/**
 * 固定ページテンプレート。
 *
 * デザインは single.php と共通。ただし以下は表示しない。
 * ・投稿日（固定ページには本来不要なため）
 * ・カテゴリー／タグ（固定ページには存在しない分類のため）
 * ・関連記事
 * ・コメント（single.php 側にも実装していないため元々対象外）
 *
 * ヘッダー・フッター・サイドバーウィジェット・パンくずは single.php と同様、
 * 子テーマ共通の header.php / footer.php と、Cocoon本体の仕組みをそのまま利用する。
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

					</article>
					<?php
				endwhile;
			endif;
			?>

		</div>

		<aside class="cl-single-sidebar">
			<?php get_sidebar(); ?>
		</aside>

	</div>
</div>
<?php get_footer(); ?>
