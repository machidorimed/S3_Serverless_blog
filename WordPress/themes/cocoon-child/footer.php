<?php
/**
 * サイト全体で使用するオリジナルフッター（Cocoon本体の footer.php を上書き）。
 *
 * Cocoon設定（アクセス解析フッタータグ・AdSense・カスタムフッターコード）と
 * ログイン中の管理者向け管理パネルは、Cocoon本体のテンプレートパーツを
 * そのまま呼び出して機能を維持している。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

	</main>

	<?php // プラグイン等がフックできるよう、Cocoon本体と同じアクションフックを発火 ?>
	<?php do_action( 'cocoon_footer_before' ); ?>

	<footer id="contact" class="cl-footer">
		<div class="cl-footer-inner">
			<nav class="cl-footer-nav" aria-label="フッターナビゲーション">
				<a href="<?php echo esc_url( cloudlearning_page_url( 'profile' ) ); ?>">プロフィール</a>
				<a href="<?php echo esc_url( cloudlearning_page_url( 'privacy-policy' ) ); ?>">プライバシーポリシー</a>
				<a href="<?php echo esc_url( cloudlearning_page_url( 'specified-commercial-transaction-law' ) ); ?>">特定商取引法に基づく表記</a>
				<a href="<?php echo esc_url( cloudlearning_page_url( 'contact', 'contact' ) ); ?>">お問い合わせ</a>
			</nav>
			<span class="cl-copyright">© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
		</div>
	</footer>

	<?php do_action( 'cocoon_footer_after' ); ?>
</div>

<?php // ログイン中の管理者のみに表示される、Cocoon本体のクイック編集パネル ?>
<?php cocoon_template_part( 'tmp/admin-panel' ); ?>

<?php wp_footer(); ?>

<?php // Cocoon設定のアクセス解析フッタータグ・AdSense・カスタムフッターコード ?>
<?php cocoon_template_part( 'tmp/footer-scripts' ); ?>
</body>
</html>
