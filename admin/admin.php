<?php

/**
 * Wp_rocket_page_spider_admin_page
 *
 * @return void
 */
function wp_rocket_page_spider_admin_page() {
	add_options_page( 'Page Spider', 'Page Spider', 'manage_options', 'page-spider', 'wp_rocket_spider_page_content' );
}

add_action( 'admin_menu', 'wp_rocket_page_spider_admin_page' );

/**
 * Wp_rocket_spider_page_content
 *
 * @return void
 */
function wp_rocket_spider_page_content() {
	/* validate if the submit was triggered from the crawl button*/
	if ( isset( $_POST['crawl'] ) ) {
		$nonce = isset( $_POST['spider_crawler_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['spider_crawler_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'spider_crawler_action' ) ) {
			die( 'Sorry, Nonce verification failed!' );
		}

		WP_ROCKET_Crawler_Manager::crawl_page();

		echo '<p>Finished crawling homepage.</p>';
	}
	/* Ensure the submit came from the button*/
	$nonce_field = wp_nonce_field( 'spider_crawler_action', 'spider_crawler_nonce' );
	echo '<p><form method="post" action="">
            <input type="submit" name="crawl" value="' . esc_html__( 'Start Crawl', 'rocket' ) . '">'
		. $nonce_field // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		. '</form></p>';
	/* get all saved hyperlinks and display them in form of a table */
	$links = WP_ROCKET_Crawler_Db::get_saved_links();
	if ( ! empty( $links ) ) {
		echo '</br><p>' . esc_html__( 'View Sitemap List', 'rocket' ) . " <a href='../sitemap.html' target='_blank'>" . esc_html__( 'sitemap.html', 'rocket' ) . '</a>';
		echo '<p>' . esc_html__( 'Last crawled Home page content snapshot. View', 'rocket' ) . " <a href='../homepage.html' target='_blank'>" . esc_html__( 'homepage.html', 'rocket' ) . '</a></p>';
		echo '<h3>' . count( $links ) . esc_html__( ' Found Results', 'rocket' ) . ':</h3>';
		echo '<table>';
		echo '<tr><td>Link</td><td>Modification Date</td></tr>';
		foreach ( $links as $link ) {
			echo '<tr>';
			echo '<td>' . esc_html( $link->link ) . '</td>';
			echo '<td>';
				echo esc_html( $link->created_at ) . '</br>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
}
