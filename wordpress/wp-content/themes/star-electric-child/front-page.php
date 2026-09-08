<?php
/**
 * The homepage.
 *
 * A section-by-section port of index.html. Every product shown is queried live
 * through the Star Electric Core plugin, so nothing on this page is a pasted
 * card that will go stale the moment a price changes.
 *
 * The copy is the approved copy. It claims no discount, delivery term,
 * warranty or dealership, because none of those is on record - and the store
 * band deliberately carries no phone number or opening hours for the same
 * reason.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$star_art = get_stylesheet_directory_uri() . '/assets/images';

/*
 * The campaign banner. One structured list, one renderer: a campaign is added
 * by adding a row here and a composed image beside it, never by pasting
 * another block of markup.
 */
$star_campaigns = array(
	array(
		'tone'    => 'navy',
		'art'     => 'protection',
		'eyebrow' => __( 'Circuit Protection', 'star-electric-child' ),
		'title'   => __( 'Built to Protect Every Circuit', 'star-electric-child' ),
		'text'    => __( 'MCBs, RCCBs, changeover gear and distribution boards, quoted against the rating you specify.', 'star-electric-child' ),
		'cta'     => __( 'Shop Circuit Protection', 'star-electric-child' ),
		'sel'     => array( 'cat' => 'circuit-protection' ),
		'cta2'    => __( 'Request a Quote', 'star-electric-child' ),
		'href2'   => Star_Electric_Shell::url( 'quote' ),
	),
	array(
		'tone'    => 'deep',
		'art'     => 'cables',
		'eyebrow' => __( 'Wires & Cables', 'star-electric-child' ),
		'title'   => __( 'Power Every Connection', 'star-electric-child' ),
		'text'    => __( 'Building wire, armoured power cable, LSZH, medium voltage and solar from the Pakistan Cables catalogue.', 'star-electric-child' ),
		'cta'     => __( 'Explore Cables', 'star-electric-child' ),
		'sel'     => array( 'cat' => 'wires-cables' ),
		'cta2'    => __( 'Bulk Enquiry', 'star-electric-child' ),
		'href2'   => Star_Electric_Shell::url( 'quote' ),
	),
	array(
		'tone'    => 'navy',
		'art'     => 'switches',
		'eyebrow' => __( 'Switches & Sockets', 'star-electric-child' ),
		'title'   => __( 'Control, Beautifully Finished', 'star-electric-child' ),
		'text'    => __( 'Modular switches, sockets and data outlets across the Aqua and Panasonic ranges.', 'star-electric-child' ),
		'cta'     => __( 'Shop Switches & Sockets', 'star-electric-child' ),
		'sel'     => array( 'cat' => 'switches-sockets' ),
	),
	array(
		'tone'    => 'ink',
		'art'     => 'lighting',
		'eyebrow' => __( 'Lighting & Fixtures', 'star-electric-child' ),
		'title'   => __( 'Light Designed Around Your Space', 'star-electric-child' ),
		'text'    => __( 'Panels, downlights, floodlights and highbay fittings from the Coarts lighting catalogue.', 'star-electric-child' ),
		'cta'     => __( 'Browse Lighting', 'star-electric-child' ),
		'sel'     => array( 'cat' => 'lighting' ),
	),
	array(
		'tone'    => 'deep',
		'art'     => 'smart',
		'eyebrow' => __( 'Fans & Smart', 'star-electric-child' ),
		'title'   => __( 'Smarter Control Starts Here', 'star-electric-child' ),
		'text'    => __( 'Inverter and AC/DC ceiling fans alongside Wi-Fi switches, dimmers and curtain controls.', 'star-electric-child' ),
		'cta'     => __( 'Shop Fans', 'star-electric-child' ),
		'sel'     => array( 'cat' => 'fans-ventilation' ),
		'cta2'    => __( 'Smart Home', 'star-electric-child' ),
		'href2'   => Star_Electric_Navigation::url( array( 'cat' => 'smart-home' ) ),
	),
);
?>

<section class="hcom" aria-label="<?php esc_attr_e( 'Departments and featured campaigns', 'star-electric-child' ); ?>">
	<div class="container hcom__grid">

		<h1 class="sr-only"><?php esc_html_e( 'Star Electric Enterprises — electrical supplies, wiring, protection, lighting and fans', 'star-electric-child' ); ?></h1>

		<div class="hcom__rail" id="heroRail">
			<?php echo do_shortcode( '[star_hero_rail]' ); ?>
		</div>

		<div>
			<section class="hb" id="heroBanner" aria-roledescription="carousel"
				aria-label="<?php esc_attr_e( 'Featured campaigns', 'star-electric-child' ); ?>">
				<div class="hb__track" id="heroTrack">
					<?php foreach ( $star_campaigns as $star_i => $star_slide ) : ?>
						<article class="hs" data-tone="<?php echo esc_attr( $star_slide['tone'] ); ?>"
							role="group" aria-roledescription="slide"
							aria-label="<?php echo esc_attr( $star_slide['eyebrow'] ); ?>">
							<span class="hs__panel" aria-hidden="true"></span>
							<div class="hs__copy">
								<p class="hs__eyebrow"><?php echo esc_html( $star_slide['eyebrow'] ); ?></p>
								<h2 class="hs__title"><?php echo esc_html( $star_slide['title'] ); ?></h2>
								<p class="hs__text"><?php echo esc_html( $star_slide['text'] ); ?></p>
								<div class="hs__cta">
									<a class="btn btn--accent btn--lg" href="<?php echo esc_url( Star_Electric_Navigation::url( $star_slide['sel'] ) ); ?>">
										<?php echo esc_html( $star_slide['cta'] ); ?>
									</a>
									<?php if ( ! empty( $star_slide['cta2'] ) ) : ?>
										<a class="btn btn--outline btn--lg" href="<?php echo esc_url( $star_slide['href2'] ); ?>">
											<?php echo esc_html( $star_slide['cta2'] ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>
							<div class="hs__stage" aria-hidden="true">
								<?php
								/*
								 * The track is translated rather than scrolled, so a lazy
								 * image on an off-screen slide is not reliably fetched
								 * before that slide arrives - it showed an empty
								 * merchandising zone from slide two onwards. They load
								 * eagerly at low priority instead.
								 */
								?>
								<img class="hs__art" src="<?php echo esc_url( $star_art . '/hero/hero-' . $star_slide['art'] . '.webp' ); ?>"
									alt="" width="1200" height="860" decoding="async"
									fetchpriority="<?php echo esc_attr( 0 === $star_i ? 'high' : 'low' ); ?>">
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<button class="hb__arrow hb__arrow--prev" type="button" id="heroPrev"
					aria-label="<?php esc_attr_e( 'Previous campaign', 'star-electric-child' ); ?>"></button>
				<button class="hb__arrow hb__arrow--next" type="button" id="heroNext"
					aria-label="<?php esc_attr_e( 'Next campaign', 'star-electric-child' ); ?>"></button>

				<div class="hb__nav" id="heroNav" aria-label="<?php esc_attr_e( 'Choose a campaign', 'star-electric-child' ); ?>">
					<?php foreach ( $star_campaigns as $star_i => $star_slide ) : ?>
						<button class="hnav" type="button" data-slide="<?php echo esc_attr( (string) $star_i ); ?>"
							aria-label="<?php echo esc_attr( $star_slide['eyebrow'] ); ?>"
							<?php echo 0 === $star_i ? 'aria-current="true"' : ''; ?>>
							<span class="hnav__num"><?php echo esc_html( substr( '0' . ( $star_i + 1 ), -2 ) ); ?></span>
							<span class="hnav__label"><?php echo esc_html( $star_slide['eyebrow'] ); ?></span>
							<span class="hnav__bar"><span class="hnav__fill"></span></span>
						</button>
					<?php endforeach; ?>
				</div>

				<p class="sr-only" aria-live="polite" id="heroStatus"></p>
			</section>

			<div class="hpromos" id="heroPromos">
				<?php
				$star_promos = array(
					array(
						'kicker' => __( 'Protection', 'star-electric-child' ),
						'title'  => __( 'Breakers & Distribution', 'star-electric-child' ),
						'cta'    => __( 'Shop now', 'star-electric-child' ),
						'sel'    => array( 'cat' => 'circuit-protection' ),
						'term'   => 'circuit-protection',
					),
					array(
						'kicker' => __( 'Smart electrical', 'star-electric-child' ),
						'title'  => __( 'Wi-Fi Switches & Devices', 'star-electric-child' ),
						'cta'    => __( 'Explore', 'star-electric-child' ),
						'sel'    => array( 'cat' => 'smart-home' ),
						'term'   => 'smart-home',
					),
					array(
						'kicker' => __( 'Projects', 'star-electric-child' ),
						'title'  => __( 'Bulk & Contractor Orders', 'star-electric-child' ),
						'cta'    => __( 'Request a quote', 'star-electric-child' ),
						'url'    => Star_Electric_Shell::url( 'quote' ),
						'term'   => 'wires-cables',
					),
				);

				foreach ( $star_promos as $star_promo ) :
					$star_href = $star_promo['url'] ?? Star_Electric_Navigation::url( $star_promo['sel'] );
					?>
					<a class="ptile" href="<?php echo esc_url( $star_href ); ?>">
						<span class="ptile__body">
							<span class="ptile__kicker"><?php echo esc_html( $star_promo['kicker'] ); ?></span>
							<span class="ptile__title"><?php echo esc_html( $star_promo['title'] ); ?></span>
							<span class="ptile__link">
								<?php echo esc_html( $star_promo['cta'] ); ?>
								<?php echo Star_Electric_Shell::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						</span>
						<span class="ptile__media">
							<?php echo Star_Electric_Shell::category_image( $star_promo['term'], 300, 240 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<div class="container">
	<ul class="trust-strip">
		<li class="trust-item">
			<span class="trust-item__ico"><?php echo Star_Electric_Shell::icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span><strong><?php esc_html_e( 'Genuine Products', 'star-electric-child' ); ?></strong><small><?php esc_html_e( 'Sourced through our supply channels', 'star-electric-child' ); ?></small></span>
		</li>
		<li class="trust-item">
			<span class="trust-item__ico"><?php echo Star_Electric_Shell::icon( 'tag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span><strong><?php esc_html_e( 'Retail & Bulk Pricing', 'star-electric-child' ); ?></strong><small><?php esc_html_e( 'Quotations available on request', 'star-electric-child' ); ?></small></span>
		</li>
		<li class="trust-item">
			<span class="trust-item__ico"><?php echo Star_Electric_Shell::icon( 'headset' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span><strong><?php esc_html_e( 'Expert Assistance', 'star-electric-child' ); ?></strong><small><?php esc_html_e( 'Ratings, sizes and specifications', 'star-electric-child' ); ?></small></span>
		</li>
	</ul>
</div>

<section class="section section--sm" aria-labelledby="deptTitle">
	<div class="container">
		<div class="rail__head">
			<h2 class="rail__title" id="deptTitle"><?php esc_html_e( 'Shop by Department', 'star-electric-child' ); ?></h2>
			<div class="rail__tools">
				<a class="rail__all" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>"><?php esc_html_e( 'View All Categories', 'star-electric-child' ); ?></a>
				<div class="rail__arrows">
					<button class="rail__arrow" type="button" data-rail-prev aria-label="<?php esc_attr_e( 'Previous departments', 'star-electric-child' ); ?>">
						<?php echo Star_Electric_Shell::icon( 'chevleft' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<button class="rail__arrow" type="button" data-rail-next aria-label="<?php esc_attr_e( 'More departments', 'star-electric-child' ); ?>">
						<?php echo Star_Electric_Shell::icon( 'chevright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
			</div>
		</div>
		<?php echo do_shortcode( '[star_departments]' ); ?>
	</div>
</section>

<section class="section section--sm section--tint">
	<div class="container" id="homeRails">
		<?php
		foreach ( Star_Electric_Navigation::rails() as $star_i => $star_rail ) {
			echo Star_Electric_Shortcodes::rail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'rail-' . $star_i,
				$star_rail['title'],
				Star_Electric_Navigation::products( $star_rail['sel'], 10 ),
				Star_Electric_Navigation::url( $star_rail['sel'] ),
				Star_Electric_Navigation::count( $star_rail['sel'] )
			);
		}
		?>
	</div>
</section>

<section class="section section--tint" aria-labelledby="dealTitle">
	<div class="container">
		<div class="section__head">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Current offers', 'star-electric-child' ); ?></p>
				<h2 class="section__title" id="dealTitle"><?php esc_html_e( 'Deals & Promotions', 'star-electric-child' ); ?></h2>
				<p class="section__sub"><?php esc_html_e( 'Every reduction here is one the product’s own source publishes. Nothing is marked down by us.', 'star-electric-child' ); ?></p>
			</div>
			<a class="link-more" href="<?php echo esc_url( Star_Electric_Shell::url( 'deals' ) ); ?>">
				<?php esc_html_e( 'All deals', 'star-electric-child' ); ?>
				<?php echo Star_Electric_Shell::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>

		<div class="promo-grid" style="margin-bottom:24px">
			<article class="promo-card promo-card--dark">
				<?php echo Star_Electric_Shell::category_image( 'fans-ventilation', 1200, 620 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<h3><?php esc_html_e( 'Fans & Ventilation', 'star-electric-child' ); ?></h3>
				<p><?php esc_html_e( 'Ceiling, bracket, pedestal and exhaust fans.', 'star-electric-child' ); ?></p>
				<a class="btn btn--accent btn--sm" href="<?php echo esc_url( Star_Electric_Navigation::url( array( 'cat' => 'fans-ventilation' ) ) ); ?>"><?php esc_html_e( 'Shop Fans', 'star-electric-child' ); ?></a>
			</article>
			<article class="promo-card">
				<img src="<?php echo esc_url( $star_art . '/banners/promo-lighting.webp' ); ?>" alt="" width="1200" height="620" loading="lazy" decoding="async">
				<h3><?php esc_html_e( 'Lighting & Fixtures', 'star-electric-child' ); ?></h3>
				<p><?php esc_html_e( 'LED bulbs, panels, downlights and outdoor fittings.', 'star-electric-child' ); ?></p>
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( Star_Electric_Navigation::url( array( 'cat' => 'lighting' ) ) ); ?>"><?php esc_html_e( 'Shop Lighting', 'star-electric-child' ); ?></a>
			</article>
			<article class="promo-card promo-card--dark">
				<img src="<?php echo esc_url( $star_art . '/banners/promo-protection.webp' ); ?>" alt="" width="1200" height="620" loading="lazy" decoding="async">
				<h3><?php esc_html_e( 'Circuit Protection', 'star-electric-child' ); ?></h3>
				<p><?php esc_html_e( 'Breakers, boards, fuses and changeover gear.', 'star-electric-child' ); ?></p>
				<a class="btn btn--accent btn--sm" href="<?php echo esc_url( Star_Electric_Navigation::url( array( 'cat' => 'circuit-protection' ) ) ); ?>"><?php esc_html_e( 'Shop Protection', 'star-electric-child' ); ?></a>
			</article>
		</div>

		<div id="dealsRail">
			<?php echo do_shortcode( '[star_products on_sale="yes" limit="10" title="Discounted at source" id="rail-deals"]' ); ?>
		</div>
	</div>
</section>

<section class="section" aria-labelledby="brandTitle">
	<div class="container">
		<div class="section__head">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Manufacturers', 'star-electric-child' ); ?></p>
				<h2 class="section__title" id="brandTitle"><?php esc_html_e( 'Shop by Brand', 'star-electric-child' ); ?></h2>
				<p class="section__sub"><?php esc_html_e( 'Brand names come from the approved product sources. No dealership, distribution or authorisation relationship is implied.', 'star-electric-child' ); ?></p>
			</div>
			<a class="link-more" href="<?php echo esc_url( Star_Electric_Shell::url( 'brands' ) ); ?>">
				<?php esc_html_e( 'All brands', 'star-electric-child' ); ?>
				<?php echo Star_Electric_Shell::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
		<?php echo do_shortcode( '[star_brands]' ); ?>
	</div>
</section>

<section class="section bulk" aria-labelledby="bulkTitle">
	<div class="container bulk__inner">
		<div>
			<h2 class="bulk__title" id="bulkTitle"><?php esc_html_e( 'Buying for a Project?', 'star-electric-child' ); ?></h2>
			<p class="bulk__text"><?php esc_html_e( 'Bulk electrical requirements for contractors, electricians, builders and commercial projects. Send us your item list and we will prepare a written quotation.', 'star-electric-child' ); ?></p>
			<div class="btn-row">
				<a class="btn btn--accent btn--lg" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Request Bulk Quote', 'star-electric-child' ); ?></a>
				<a class="btn btn--light btn--lg" href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>">
					<?php echo Star_Electric_Shell::icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Contact the Store', 'star-electric-child' ); ?>
				</a>
			</div>
		</div>
		<ol class="bulk__steps">
			<li><b>01</b> <?php esc_html_e( 'Share your item list or bill of quantities', 'star-electric-child' ); ?></li>
			<li><b>02</b> <?php esc_html_e( 'Receive a written quotation', 'star-electric-child' ); ?></li>
			<li><b>03</b> <?php esc_html_e( 'Arrange collection or delivery', 'star-electric-child' ); ?></li>
		</ol>
	</div>
</section>

<section class="section" aria-labelledby="whyTitle">
	<div class="container">
		<div class="section__head section__head--center">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Our approach', 'star-electric-child' ); ?></p>
				<h2 class="section__title" id="whyTitle"><?php esc_html_e( 'Why Choose Star Electric Enterprises', 'star-electric-child' ); ?></h2>
				<p class="section__sub"><?php esc_html_e( 'How we work with retail customers and trade buyers.', 'star-electric-child' ); ?></p>
			</div>
		</div>
		<ul class="why-grid">
			<?php
			$star_why = array(
				array( 'shield', __( 'Genuine Products', 'star-electric-child' ), __( 'Stock sourced through our regular supply channels.', 'star-electric-child' ) ),
				array( 'bolt', __( 'Competitive Pricing', 'star-electric-child' ), __( 'Retail and bulk pricing available on request.', 'star-electric-child' ) ),
				array( 'headset', __( 'Expert Assistance', 'star-electric-child' ), __( 'Help selecting the right rating, size and specification.', 'star-electric-child' ) ),
				array( 'box', __( 'Trade & Project Supply', 'star-electric-child' ), __( 'Quotations for contractors and commercial requirements.', 'star-electric-child' ) ),
				array( 'pin', __( 'Local Presence', 'star-electric-child' ), __( 'A physical store in Saddar, Rawalpindi you can visit.', 'star-electric-child' ) ),
			);
			foreach ( $star_why as $star_card ) :
				?>
				<li class="why-card">
					<span class="why-card__ico"><?php echo Star_Electric_Shell::icon( $star_card[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<h3><?php echo esc_html( $star_card[1] ); ?></h3>
					<p><?php echo esc_html( $star_card[2] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<?php
/*
 * Only facts on record appear in this band. The shop address beyond the area,
 * the phone number, WhatsApp and opening hours have not been supplied, so those
 * rows are absent rather than shown as empty brackets on a live site.
 */
$star_total = wp_count_posts( 'product' );
$star_total = isset( $star_total->publish ) ? (int) $star_total->publish : 0;
?>
<section class="section section--tint" aria-labelledby="storeTitle">
	<div class="container store-band">
		<div class="store-band__copy">
			<p class="section__eyebrow"><?php esc_html_e( 'Visit us', 'star-electric-child' ); ?></p>
			<h2 class="section__title" id="storeTitle"><?php bloginfo( 'name' ); ?></h2>
			<p class="t-lead" style="margin-top:8px"><?php esc_html_e( 'Saddar, Rawalpindi', 'star-electric-child' ); ?></p>
			<p class="store-band__text"><?php esc_html_e( 'An electrical supplies store serving householders, electricians and contractors. Send us your item list or bill of quantities and we will come back with a written quotation for the ratings and sizes you need.', 'star-electric-child' ); ?></p>
			<div class="btn-row">
				<a class="btn btn--accent" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Request a Quotation', 'star-electric-child' ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact the Store', 'star-electric-child' ); ?></a>
			</div>
		</div>

		<ul class="store-band__facts">
			<li>
				<strong data-product-count><?php echo esc_html( number_format_i18n( $star_total ) ); ?></strong>
				<span><?php esc_html_e( 'individual products listed from seven supplier sources', 'star-electric-child' ); ?></span>
			</li>
			<li>
				<strong><?php esc_html_e( 'Retail & bulk', 'star-electric-child' ); ?></strong>
				<span><?php esc_html_e( 'quotations prepared for project and contractor orders', 'star-electric-child' ); ?></span>
			</li>
			<li>
				<strong><?php esc_html_e( 'Source-checked', 'star-electric-child' ); ?></strong>
				<span><?php esc_html_e( 'every price and specification taken from the supplier’s own catalogue', 'star-electric-child' ); ?></span>
			</li>
		</ul>
	</div>
</section>

<?php
get_footer();
