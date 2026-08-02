<?php
/**
 * ループ用カード1件分のテンプレートパーツ（Learning / Hands on 共通）。
 * WP_Query の while ループ内、the_post() の直後に呼び出す想定。
 *
 * @param array $args {
 *     @type string $category_fallback カテゴリー未設定時に表示するフォールバック文字列。
 *     @type string $category_override バッジに表示するカテゴリー名を強制指定（アーカイブページ等、
 *                                      投稿の先頭カテゴリーではなく「今表示している」カテゴリーを
 *                                      確実に出したい場合に使用）。
 *     @type int    $index             ループ内インデックス（アイコンパレット選択に使用）。
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$category_fallback = isset( $args['category_fallback'] ) ? $args['category_fallback'] : '';
$category_override = isset( $args['category_override'] ) ? $args['category_override'] : '';
$index              = isset( $args['index'] ) ? absint( $args['index'] ) : 0;

$has_thumb      = has_post_thumbnail();
$tag_slug       = cloudlearning_card_tag_slug( get_the_ID() );
$icon           = cloudlearning_card_icon( $tag_slug, $index );
$category_label = '' !== $category_override ? $category_override : cloudlearning_card_category( get_the_ID(), $category_fallback );
?>
<article>
<a href="<?php the_permalink(); ?>" class="cl-card">
	<div class="cl-card-thumb"<?php echo $has_thumb ? '' : ' style="background:' . esc_attr( $icon['bg'] ) . ';"'; ?>>
		<?php if ( $has_thumb ) : ?>
			<?php the_post_thumbnail( 'medium_large', array( 'class' => 'cl-card-thumb-img', 'loading' => 'lazy' ) ); ?>
		<?php endif; ?>
		<span class="cl-card-tag"><?php echo esc_html( $category_label ); ?></span>
		<?php if ( ! $has_thumb ) : ?>
			<div class="cl-card-icon" style="<?php echo esc_attr( $icon['shape'] ); ?>"></div>
		<?php endif; ?>
	</div>
	<div class="cl-card-body">
		<h3><?php the_title(); ?></h3>
		<p class="cl-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></p>
		<span class="cl-card-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
	</div>
</a>
</article>
