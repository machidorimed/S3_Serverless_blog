<?php
/**
 * アーカイブページテンプレート（カテゴリー一覧を主な対象とする）。
 *
 * ヘッダー・フッター・背景グラデーション・右サイドバーはトップページ / single.php と共通。
 * パンくず・サイドバーウィジェット・ページネーションはCocoon本体の仕組みをそのまま利用する。
 * カード自体は template-parts/card.php（トップページ Learning セクションと同一部品）を再利用し、
 * グリッドの列数だけこのページ専用に定義する。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="cl-single cl-archive">
	<div class="cl-single-grid">

		<div class="cl-single-main">

			<?php cocoon_template_part( 'tmp/breadcrumbs' ); ?>

			<header class="cl-archive-header">
				<h1 class="cl-archive-title">
					<?php
					if ( is_category() ) {
						echo esc_html( single_cat_title( '', false ) );
					} else {
						the_archive_title();
					}
					?>
				</h1>
				<?php
				$cl_term_description = term_description();
				if ( $cl_term_description ) :
					?>
					<div class="cl-archive-desc"><?php echo wp_kses_post( $cl_term_description ); ?></div>
				<?php endif; ?>
			</header>

			<?php if ( have_posts() ) : ?>

				<div class="cl-archive-grid">
					<?php
					$cl_archive_category = is_category() ? single_cat_title( '', false ) : '';
					$i                   = 0;
					while ( have_posts() ) :
						the_post();
						get_template_part(
							'template-parts/card',
							null,
							array(
								'index'             => $i,
								'category_override' => $cl_archive_category,
							)
						);
						$i++;
					endwhile;
					?>
				</div>

				<?php cocoon_template_part( 'tmp/pagination' ); ?>

			<?php else : ?>

				<p class="cl-card-empty">まだ記事がありません。</p>

			<?php endif; ?>

		</div>

		<aside class="cl-single-sidebar">
			<?php get_sidebar(); ?>
		</aside>

	</div>
</div>
<?php get_footer(); ?>
