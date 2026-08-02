<?php
/**
 * サイト全体で使用するオリジナルヘッダー（Cocoon本体の header.php を上書き）。
 * オープニング演出のマークアップ・起動スクリプト・画像プリロードは
 * フロントページ表示時のみ出力する。
 *
 * デザイン用のHTML骨格はCocoon本体のheader.phpと別物だが、
 * Cocoon設定（アクセス解析・カスタムヘッドコード・アフィリエイト・PWA・
 * サイト認証・事前接続ドメイン）はCocoon本体のテンプレートパーツを
 * そのまま呼び出し、管理画面の設定が機能しなくならないようにしている。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logo_dir = get_stylesheet_directory_uri() . '/assets/images';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php // Cocoon設定「サイト認証」のGoogle Search Console確認用メタタグ ?>
<?php if ( get_google_search_console_id() ) : ?>
<meta name="google-site-verification" content="<?php echo esc_attr( get_google_search_console_id() ); ?>">
<?php endif; ?>

<?php // Cocoon設定「高速化」で登録した事前接続（preconnect）ドメイン ?>
<?php foreach ( list_text_to_array( get_pre_acquisition_list() ) as $cl_domain ) : ?>
<link rel="preconnect dns-prefetch" href="//<?php echo esc_attr( $cl_domain ); ?>">
<?php endforeach; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php if ( is_front_page() ) : ?>
<script>
(function () {
	var html = document.documentElement;
	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}
	html.classList.add( 'cl-intro-active' );
	window.setTimeout( function () {
		html.classList.add( 'cl-intro-done' );
	}, 1150 );
})();
</script>
<link rel="preload" as="image" href="<?php echo esc_url( $logo_dir . '/illustration_transparent.webp' ); ?>" fetchpriority="high">
<?php endif; ?>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700;900&display=swap" rel="stylesheet">

<?php // Cocoon設定「アクセス解析」（GA4・Googleタグマネージャー・Clarity等） ?>
<?php cocoon_template_part( 'tmp/head-analytics' ); ?>

<?php wp_head(); ?>

<?php // Cocoon設定のカスタムヘッドコード・アフィリエイト（LinkSwitch）・PWAタグ ?>
<?php cocoon_template_part( 'tmp/head-custom-field' ); ?>
<?php cocoon_template_part( 'tmp/head-javascript' ); ?>
<?php cocoon_template_part( 'tmp/head-pwa' ); ?>
</head>
<body <?php body_class( 'cl-page' ); ?>>
<?php wp_body_open(); ?>
<a class="cl-skip-link" href="#main-content">メインコンテンツへスキップ</a>

<?php if ( is_front_page() ) : ?>
<div class="cl-intro" aria-hidden="true">
	<div class="cl-intro-panel cl-intro-panel-left"></div>
	<div class="cl-intro-panel cl-intro-panel-right"></div>
	<div class="cl-intro-beam"></div>
</div>
<?php endif; ?>

<div class="cl-wrap">

	<header class="cl-header">
		<div class="cl-header-inner">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cl-logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' トップページへ' ); ?>">
				<img class="cl-logo-sub" src="<?php echo esc_url( $logo_dir . '/Logo/setA_02.webp' ); ?>" alt="" width="592" height="57" fetchpriority="low">
				<img class="cl-logo-main" src="<?php echo esc_url( $logo_dir . '/Logo/setA_03.webp' ); ?>" alt="" width="816" height="71" fetchpriority="low">
			</a>

			<nav aria-label="メインナビゲーション">
				<?php if ( has_nav_menu( 'cloudlearning_primary' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'cloudlearning_primary',
							'container'      => false,
							'menu_class'     => 'cl-nav',
							'depth'          => 1,
						)
					);
					?>
				<?php else : ?>
					<div class="cl-nav">
						<a href="<?php echo esc_url( home_url( '/#top' ) ); ?>">ホーム</a>
						<a href="<?php echo esc_url( cloudlearning_section_url( 'challenge', 'challenge' ) ); ?>">IT資格</a>
						<a href="<?php echo esc_url( cloudlearning_section_url( 'handson', 'handson' ) ); ?>">アーキテクチャ</a>
						<a href="<?php echo esc_url( cloudlearning_page_url( 'contact', 'contact' ) ); ?>">お問い合わせ</a>
					</div>
				<?php endif; ?>
			</nav>

			<button class="cl-hamburger" type="button" aria-expanded="false" aria-controls="cl-mobile-nav">メニュー</button>
		</div>

		<nav id="cl-mobile-nav" class="cl-mobile-nav" aria-label="モバイルナビゲーション" hidden>
			<a href="<?php echo esc_url( home_url( '/#top' ) ); ?>">ホーム</a>
			<a href="<?php echo esc_url( cloudlearning_section_url( 'challenge', 'challenge' ) ); ?>">IT資格</a>
			<a href="<?php echo esc_url( cloudlearning_section_url( 'handson', 'handson' ) ); ?>">アーキテクチャ</a>
			<a href="<?php echo esc_url( cloudlearning_page_url( 'contact', 'contact' ) ); ?>">お問い合わせ</a>
		</nav>
	</header>

	<main id="main-content" tabindex="-1">
