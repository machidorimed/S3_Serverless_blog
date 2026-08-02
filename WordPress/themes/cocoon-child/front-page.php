<?php
/**
 * フロントページ（サイトトップ）専用テンプレート。
 * Learning / Challenge / Hands on の各セクションは、それぞれ
 * カテゴリースラッグ learning / challenge / handson の投稿と連動する。
 *
 * ヘッダー・フッターは子テーマ共通の header.php / footer.php
 * （get_header() / get_footer()）を使用する。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$logo_dir = get_stylesheet_directory_uri() . '/assets/images';
?>
	<section id="top" class="cl-hero">
		<div class="cl-hero-grid">
			<div class="cl-hero-left">
				<h1 class="cl-hero-title">
					<span>クラウドの力で、</span><br><span>未来の自分へステップアップ。</span>
				</h1>
				<p class="cl-hero-desc">
					AWSを中心に、インフラの基礎から実践までを<br>
					やさしく・わかりやすく解説する技術ブログです。<br>
					未経験からでも、着実にスキルを身につけられる<br>
					学びの道しるべになります。
				</p>
				<a class="cl-hero-cta" href="#learning">
					<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_10.webp' ); ?>" alt="最新記事をチェックする" width="385" height="78">
				</a>
			</div>
			<div class="cl-hero-right">
				<img class="cl-hero-illustration" src="<?php echo esc_url( $logo_dir . '/illustration_transparent.webp' ); ?>" alt="" width="623" height="423" fetchpriority="high" loading="eager">
			</div>
		</div>
	</section>

	<div class="cl-sections">

		<!-- Learning -->
		<section id="learning" class="cl-section cl-section-learning cl-reveal" aria-labelledby="cl-learning-heading">
			<div class="cl-section-head">
				<img class="cl-section-icon" src="<?php echo esc_url( $logo_dir . '/Logo/setA_06.webp' ); ?>" alt="" width="104" height="97" loading="lazy">
				<div>
					<h2 id="cl-learning-heading" class="cl-section-titles">
						<img src="<?php echo esc_url( $logo_dir . '/setA_08_en_Learning.webp' ); ?>" alt="Learning" style="height:37px;width:auto;" width="195" height="56" loading="lazy">
						<img src="<?php echo esc_url( $logo_dir . '/setA_08_ja.webp' ); ?>" alt="学習トピック" style="height:16px;width:auto;" width="136" height="28" loading="lazy">
					</h2>
					<p class="cl-section-desc">AWSやインフラの基礎から応用までをやさしく解説</p>
				</div>
			</div>

			<div class="cl-card-grid">
				<?php
				$learning_query = new WP_Query(
					array(
						'category_name'       => 'learning',
						'posts_per_page'      => 6,
						'orderby'             => 'date',
						'order'               => 'DESC',
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					)
				);
				// アイキャッチ（添付ファイル投稿）をまとめて1クエリでキャッシュし、
				// the_post_thumbnail() のループ内N+1クエリを防ぐ。
				update_post_thumbnail_cache( $learning_query );

				if ( $learning_query->have_posts() ) :
					$i = 0;
					while ( $learning_query->have_posts() ) :
						$learning_query->the_post();
						get_template_part(
							'template-parts/card',
							null,
							array(
								'index'             => $i,
								'category_fallback' => 'Learning',
							)
						);
						$i++;
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<p class="cl-card-empty">まだ記事がありません。「learning」カテゴリーに記事を投稿すると、ここに表示されます。</p>
					<?php
				endif;
				?>
			</div>

			<div class="cl-section-more">
				<a href="<?php echo esc_url( cloudlearning_section_url( 'learning', 'learning' ) ); ?>">
					<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_11.webp' ); ?>" alt="記事一覧へ" width="288" height="80" loading="lazy">
				</a>
			</div>
		</section>

		<!-- Challenge -->
		<section id="challenge" class="cl-section cl-section-challenge cl-reveal" aria-labelledby="cl-challenge-heading">
			<div class="cl-section-head">
				<img class="cl-section-icon" src="<?php echo esc_url( $logo_dir . '/Logo/setA_05.webp' ); ?>" alt="" width="108" height="107" loading="lazy">
				<div>
					<h2 id="cl-challenge-heading" class="cl-section-titles">
						<img src="<?php echo esc_url( $logo_dir . '/setA_07_en_Challenge.webp' ); ?>" alt="Challenge" style="height:37px;width:auto;" width="229" height="59" loading="lazy">
						<img src="<?php echo esc_url( $logo_dir . '/setA_07_ja.webp' ); ?>" alt="資格に挑戦してスキルを証明しよう" style="height:16px;width:auto;" width="311" height="26" loading="lazy">
					</h2>
					<p class="cl-section-desc">AWS認定資格の学習ポイントや対策をわかりやすく紹介</p>
				</div>
			</div>

			<div class="cl-card-grid">
				<?php
				$challenge_query = new WP_Query(
					array(
						'category_name'       => 'challenge',
						'posts_per_page'      => 3,
						'orderby'             => 'date',
						'order'               => 'DESC',
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					)
				);
				// Challenge カードはアイキャッチを表示しないため update_post_thumbnail_cache() は不要。
				if ( $challenge_query->have_posts() ) :
					$i = 0;
					while ( $challenge_query->have_posts() ) :
						$challenge_query->the_post();
						?>
						<article>
						<a href="<?php the_permalink(); ?>" class="cl-badge-card">
							<div class="cl-badge" style="background:<?php echo esc_attr( cloudlearning_badge_color( $i ) ); ?>;">
								<?php echo esc_html( cloudlearning_card_tag( get_the_ID(), get_the_title() ) ); ?>
							</div>
							<div>
								<h3><?php the_title(); ?></h3>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28, '…' ) ); ?></p>
								<span class="cl-badge-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
							</div>
						</a>
						</article>
						<?php
						$i++;
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<p class="cl-card-empty">まだ記事がありません。「challenge」カテゴリーに記事を投稿すると、ここに表示されます。</p>
					<?php
				endif;
				?>
			</div>

			<div class="cl-section-more">
				<a href="<?php echo esc_url( cloudlearning_section_url( 'challenge', 'challenge' ) ); ?>">
					<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_12.webp' ); ?>" alt="資格記事一覧へ" width="297" height="78" loading="lazy">
				</a>
			</div>
		</section>

		<!-- Hands on -->
		<section id="handson" class="cl-section cl-section-handson cl-reveal" aria-labelledby="cl-handson-heading">
			<div class="cl-section-head">
				<img class="cl-section-icon" src="<?php echo esc_url( $logo_dir . '/Logo/setA_04.webp' ); ?>" alt="" width="103" height="108" loading="lazy">
				<div>
					<h2 id="cl-handson-heading" class="cl-section-titles">
						<img src="<?php echo esc_url( $logo_dir . '/setA_09_en_Handson.webp' ); ?>" alt="Hands on" style="height:38px;width:auto;" width="210" height="45" loading="lazy">
						<img src="<?php echo esc_url( $logo_dir . '/setA_09_ja.webp' ); ?>" alt="実際に手を動かしてインフラを作ろう" style="height:16px;width:auto;" width="313" height="26" loading="lazy">
					</h2>
					<p class="cl-section-desc">構築手順をステップごとに解説し、実践力を身につける</p>
				</div>
			</div>

			<div class="cl-card-grid">
				<?php
				$handson_query = new WP_Query(
					array(
						'category_name'       => 'handson',
						'posts_per_page'      => 3,
						'orderby'             => 'date',
						'order'               => 'DESC',
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					)
				);
				// アイキャッチをまとめて1クエリでキャッシュし、N+1クエリを防ぐ。
				update_post_thumbnail_cache( $handson_query );

				if ( $handson_query->have_posts() ) :
					$i = 0;
					while ( $handson_query->have_posts() ) :
						$handson_query->the_post();
						get_template_part(
							'template-parts/card',
							null,
							array(
								'index'             => $i,
								'category_fallback' => 'Hands on',
							)
						);
						$i++;
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<p class="cl-card-empty">まだ記事がありません。「handson」カテゴリーに記事を投稿すると、ここに表示されます。</p>
					<?php
				endif;
				?>
			</div>

			<div class="cl-section-more">
				<a href="<?php echo esc_url( cloudlearning_section_url( 'handson', 'handson' ) ); ?>">
					<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_13.webp' ); ?>" alt="ハンズオン一覧へ" width="320" height="80" loading="lazy">
				</a>
			</div>
		</section>

		<!-- Features -->
		<section class="cl-feature-row cl-reveal">
			<h2 class="sr-only">このブログの特徴</h2>
			<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_14.webp' ); ?>" alt="初心者にやさしい解説：専門用語も丁寧に解説するので、初学者でも安心です。" width="329" height="124" loading="lazy">
			<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_15.webp' ); ?>" alt="AWSに特化した情報：AWSを中心とした最新のサービスやベストプラクティスを発信。" width="346" height="122" loading="lazy">
			<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_16.webp' ); ?>" alt="実践的なハンズオン：実際に手を動かして学べる構成でスキルが身につきます。" width="316" height="123" loading="lazy">
			<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_17.webp' ); ?>" alt="継続的に成長できる：学習ロードマップやキャリアのヒントも紹介していきます。" width="315" height="122" loading="lazy">
		</section>

		<!-- Closing -->
		<section class="cl-closing-row cl-reveal">
			<h2 class="sr-only">まとめ</h2>
			<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_18.webp' ); ?>" alt="" width="305" height="213" loading="lazy">
			<img class="cl-closing-caption" src="<?php echo esc_url( $logo_dir . '/Logo/setA_20.webp' ); ?>" alt="このブログが、あなたの「わからない」を「できた！」に変え、クラウドの世界への第一歩を後押しします。一緒に、未来のインフラをつくるエンジニアを目指しましょう！" width="660" height="118" loading="lazy">
			<img src="<?php echo esc_url( $logo_dir . '/Logo/setA_19.webp' ); ?>" alt="" width="250" height="120" loading="lazy">
		</section>
	</div>

<?php get_footer(); ?>
