<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * カテゴリーアーカイブの1ページあたりの表示件数を2列×5件（10件）に設定。
 */
function cloudlearning_archive_posts_per_page( $query ) {
	if ( ! is_admin() && $query->is_main_query() && is_category() ) {
		$query->set( 'posts_per_page', 10 );
	}
}
add_action( 'pre_get_posts', 'cloudlearning_archive_posts_per_page' );

/**
 * スタイル・スクリプトの読み込み
 */
function cloudlearning_enqueue_assets() {
	wp_enqueue_style( 'cocoon-parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style(
		'cloudlearning-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'cocoon-parent-style' ),
		wp_get_theme()->get( 'Version' )
	);

	// ヘッダー・フッター（.cl-wrap 配下）は全ページ共通なので常に読み込む。
	// バージョンはファイル更新日時から自動生成し、CloudFront/ブラウザに
	// 更新後も古いキャッシュが返され続けないようにする（手動バージョン管理は
	// 更新のたびに書き換え忘れるリスクがあるため避ける）。
	$cl_front_css_path = get_stylesheet_directory() . '/assets/css/front-page.css';
	$cl_front_js_path  = get_stylesheet_directory() . '/assets/js/front-page.js';

	wp_enqueue_style(
		'cloudlearning-front-page',
		get_stylesheet_directory_uri() . '/assets/css/front-page.css',
		array( 'cloudlearning-child-style' ),
		file_exists( $cl_front_css_path ) ? filemtime( $cl_front_css_path ) : '1.0.0'
	);
	wp_enqueue_script(
		'cloudlearning-front-page',
		get_stylesheet_directory_uri() . '/assets/js/front-page.js',
		array(),
		file_exists( $cl_front_js_path ) ? filemtime( $cl_front_js_path ) : '1.0.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'cloudlearning_enqueue_assets' );

/**
 * グローバルナビのメニュー位置を登録
 * 管理画面「外観 > メニュー」で未設定の場合はフォールバックのリンクを表示
 */
function cloudlearning_register_menus() {
	register_nav_menus(
		array(
			'cloudlearning_primary' => __( 'Cloud学習室 グローバルナビ', 'cocoon-child' ),
		)
	);
}
add_action( 'after_setup_theme', 'cloudlearning_register_menus' );

/**
 * カテゴリースラッグからアーカイブURLを取得。
 * カテゴリー未作成時はトップページ内の該当セクションへのアンカーにフォールバック。
 * header.php・footer.php・front-page.php から同じスラッグで複数回呼ばれるため、
 * リクエスト内で結果をメモ化して重複クエリを避ける。
 */
function cloudlearning_section_url( $slug, $anchor ) {
	static $cache = array();

	if ( ! array_key_exists( $slug, $cache ) ) {
		$term          = get_category_by_slug( $slug );
		$cache[ $slug ] = ( $term && ! is_wp_error( $term ) ) ? get_category_link( $term ) : home_url( '/#' . $anchor );
	}

	return $cache[ $slug ];
}

/**
 * 固定ページのスラッグからURLを取得。
 * ページ未作成時はアンカー（指定があれば）または '#' にフォールバック。
 * header.php・footer.php から同じスラッグで複数回呼ばれるため、
 * リクエスト内で結果をメモ化して重複クエリを避ける。
 */
function cloudlearning_page_url( $slug, $anchor = '' ) {
	static $cache = array();

	if ( ! array_key_exists( $slug, $cache ) ) {
		$page          = get_page_by_path( $slug );
		$cache[ $slug ] = $page ? get_permalink( $page ) : ( $anchor ? home_url( '/#' . $anchor ) : '#' );
	}

	return $cache[ $slug ];
}

/**
 * カードアイコンの形状・背景を、投稿タグのスラッグに応じて返す。
 * 未知のタグは index に応じて既定パレットを巡回して割り当てる。
 */
function cloudlearning_card_icon( $tag_slug, $index ) {
	$icons = array(
		'aws'        => array(
			'shape' => 'width:70px;height:70px;background:#3b6ff0;border-radius:50% 50% 50% 0;',
			'bg'    => 'linear-gradient(135deg,#eaf1ff,#dbe8ff)',
		),
		'ec2'        => array(
			'shape' => 'width:64px;height:64px;background:#f7941e;border-radius:12px;',
			'bg'    => 'linear-gradient(135deg,#fff3e2,#ffe6c2)',
		),
		'vpc'        => array(
			'shape' => 'width:64px;height:64px;background:#3b6ff0;border-radius:50%;',
			'bg'    => 'linear-gradient(135deg,#eaf1ff,#dbe8ff)',
		),
		's3'         => array(
			'shape' => 'width:60px;height:60px;background:#2fa96b;border-radius:0 0 30px 30px;',
			'bg'    => 'linear-gradient(135deg,#e9f8ef,#d3f0de)',
		),
		'iam'        => array(
			'shape' => 'width:60px;height:60px;background:#2fa96b;border-radius:16px;',
			'bg'    => 'linear-gradient(135deg,#e9f8ef,#d3f0de)',
		),
		'cloudwatch' => array(
			'shape' => 'width:64px;height:64px;background:#e0447b;border-radius:16px;',
			'bg'    => 'linear-gradient(135deg,#fdeaf1,#fbd8e6)',
		),
		'web'        => array(
			'shape' => 'width:64px;height:44px;background:#12172b;border-radius:8px;',
			'bg'    => 'linear-gradient(135deg,#eef1fb,#dfe4f5)',
		),
		'alb'        => array(
			'shape' => 'width:64px;height:64px;background:#3b6ff0;border-radius:12px;',
			'bg'    => 'linear-gradient(135deg,#eaf1ff,#dbe8ff)',
		),
		'cicd'       => array(
			'shape' => 'width:64px;height:64px;background:#2fa96b;border-radius:12px;',
			'bg'    => 'linear-gradient(135deg,#e9f8ef,#d3f0de)',
		),
	);

	if ( isset( $icons[ $tag_slug ] ) ) {
		return $icons[ $tag_slug ];
	}

	$fallback = array(
		array(
			'shape' => 'width:70px;height:70px;background:#3b6ff0;border-radius:50% 50% 50% 0;',
			'bg'    => 'linear-gradient(135deg,#eaf1ff,#dbe8ff)',
		),
		array(
			'shape' => 'width:64px;height:64px;background:#f7941e;border-radius:50%;',
			'bg'    => 'linear-gradient(135deg,#fff3e2,#ffe6c2)',
		),
		array(
			'shape' => 'width:64px;height:64px;background:#2fa96b;border-radius:16px;',
			'bg'    => 'linear-gradient(135deg,#e9f8ef,#d3f0de)',
		),
		array(
			'shape' => 'width:64px;height:64px;background:#e0447b;border-radius:16px;',
			'bg'    => 'linear-gradient(135deg,#fdeaf1,#fbd8e6)',
		),
	);

	return $fallback[ $index % count( $fallback ) ];
}

/**
 * 資格カードのバッジ背景色を index に応じて巡回。
 */
function cloudlearning_badge_color( $index ) {
	$colors = array( '#232f65', '#3b6ff0', '#f7941e' );
	return $colors[ $index % count( $colors ) ];
}

/**
 * カードに表示するタグ文字列（投稿の先頭タグ、無ければフォールバック文字列）。
 */
function cloudlearning_card_tag( $post_id, $fallback ) {
	$tags = get_the_tags( $post_id );
	if ( $tags && ! is_wp_error( $tags ) ) {
		return $tags[0]->name;
	}
	return $fallback;
}

/**
 * アイコン判定用の投稿タグスラッグ（先頭タグ、無ければ空文字）。
 */
function cloudlearning_card_tag_slug( $post_id ) {
	$tags = get_the_tags( $post_id );
	if ( $tags && ! is_wp_error( $tags ) ) {
		return $tags[0]->slug;
	}
	return '';
}

/**
 * カードに表示するカテゴリー名（投稿の先頭カテゴリー、無ければフォールバック文字列）。
 */
function cloudlearning_card_category( $post_id, $fallback ) {
	$categories = get_the_category( $post_id );
	if ( $categories && ! is_wp_error( $categories ) ) {
		return $categories[0]->name;
	}
	return $fallback;
}
