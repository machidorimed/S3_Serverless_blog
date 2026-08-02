<?php
/**
 * 404（Not Found）テンプレート。
 *
 * ヘッダー・フッター・背景グラデーションはトップページ / single.php / page.php と共通。
 * オープニング演出は header.php 側で is_front_page() 判定済みのため、
 * このページでは自動的に表示されない（追加対応不要）。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="cl-single cl-404">
	<div class="cl-404-inner">
		<p class="cl-404-code">404</p>
		<h1 class="cl-404-title">お探しのページは見つかりませんでした。</h1>
		<p class="cl-404-desc">
			URLが間違っているか、ページが移動・削除された可能性があります。<br>
			下記のリンクから目的のページをお探しください。
		</p>
		<div class="cl-404-actions">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cl-404-btn cl-404-btn-primary">トップページへ戻る</a>
			<a href="<?php echo esc_url( cloudlearning_section_url( 'learning', 'learning' ) ); ?>" class="cl-404-btn">最新記事を見る</a>
			<a href="<?php echo esc_url( cloudlearning_section_url( 'challenge', 'challenge' ) ); ?>" class="cl-404-btn">資格学習を見る（Challenge）</a>
			<a href="<?php echo esc_url( cloudlearning_section_url( 'handson', 'handson' ) ); ?>" class="cl-404-btn">ハンズオンを見る（Hands on）</a>
		</div>
	</div>
</div>
<?php get_footer(); ?>
