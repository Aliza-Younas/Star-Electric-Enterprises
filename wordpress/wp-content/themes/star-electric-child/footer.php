<?php
/**
 * The global footer.
 *
 * A direct port of SEE_UI.renderFooter() from the approved storefront.
 *
 * Two omissions are deliberate and must stay: there are no payment-method
 * badges, because the store has not confirmed which methods it accepts, and
 * there is no phone number or opening time, because none is on record. An
 * invented business fact is worse than a missing one.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$star_tree = array_slice( Star_Electric_Shell::category_tree(), 0, 6 );
?>
</main>

<div id="siteFooter">
	<footer class="footer">
		<div class="container footer__grid">

			<div class="footer__col footer__col--about">
				<?php echo Star_Electric_Shell::logo( 'logo--footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="footer__about"><?php esc_html_e( 'Star Electric Enterprises is an electrical products store based in Saddar, Rawalpindi, supplying wiring, protection, lighting, fans and power equipment to homes, offices, commercial projects, electricians and contractors.', 'star-electric-child' ); ?></p>
			</div>

			<div class="footer__col">
				<h2 class="footer__title"><?php esc_html_e( 'Shop', 'star-electric-child' ); ?></h2>
				<ul class="footer__links">
					<?php foreach ( $star_tree as $star_branch ) : ?>
						<li>
							<a href="<?php echo esc_url( (string) get_term_link( $star_branch['term'] ) ); ?>">
								<?php echo esc_html( $star_branch['term']->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'brands' ) ); ?>"><?php esc_html_e( 'Brands', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'deals' ) ); ?>"><?php esc_html_e( 'Deals', 'star-electric-child' ); ?></a></li>
				</ul>
			</div>

			<div class="footer__col">
				<h2 class="footer__title"><?php esc_html_e( 'Customer Service', 'star-electric-child' ); ?></h2>
				<ul class="footer__links">
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact Us', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'faq' ) ); ?>"><?php esc_html_e( 'FAQs', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'track-order' ) ); ?>"><?php esc_html_e( 'Track Order', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'returns' ) ); ?>"><?php esc_html_e( 'Returns', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'complaint' ) ); ?>"><?php esc_html_e( 'Submit a Complaint', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'account' ) ); ?>"><?php esc_html_e( 'My Account', 'star-electric-child' ); ?></a></li>
				</ul>
			</div>

			<div class="footer__col footer__col--business">
				<h2 class="footer__title"><?php esc_html_e( 'Business', 'star-electric-child' ); ?></h2>
				<ul class="footer__links">
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'about' ) ); ?>"><?php esc_html_e( 'About Us', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Bulk / Project Orders', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'shipping' ) ); ?>"><?php esc_html_e( 'Shipping', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'privacy' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'terms' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'star-electric-child' ); ?></a></li>
				</ul>
			</div>

			<div class="footer__col">
				<h2 class="footer__title"><?php esc_html_e( 'Contact', 'star-electric-child' ); ?></h2>
				<ul class="footer__contact">
					<li><span><?php esc_html_e( 'Store', 'star-electric-child' ); ?></span> <?php bloginfo( 'name' ); ?></li>
					<li><span><?php esc_html_e( 'Area', 'star-electric-child' ); ?></span> <?php esc_html_e( 'Saddar, Rawalpindi', 'star-electric-child' ); ?></li>
					<li><span><?php esc_html_e( 'Enquiries', 'star-electric-child' ); ?></span> <a href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact form', 'star-electric-child' ); ?></a></li>
					<li><span><?php esc_html_e( 'Quotations', 'star-electric-child' ); ?></span> <a href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Request a quotation', 'star-electric-child' ); ?></a></li>
				</ul>
			</div>
		</div>

		<div class="footer__bar">
			<div class="container footer__barinner">
				<p>
					&copy; <span data-year><?php echo esc_html( gmdate( 'Y' ) ); ?></span>
					<?php esc_html_e( 'Star Electric Enterprises, Saddar, Rawalpindi. All rights reserved.', 'star-electric-child' ); ?>
				</p>
				<ul class="footer__legal">
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'privacy' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'terms' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'shipping' ) ); ?>"><?php esc_html_e( 'Shipping', 'star-electric-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'returns' ) ); ?>"><?php esc_html_e( 'Returns', 'star-electric-child' ); ?></a></li>
				</ul>
			</div>
		</div>
	</footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
