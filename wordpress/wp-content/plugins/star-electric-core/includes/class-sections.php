<?php
/**
 * The approved page sections, as one renderer each.
 *
 * Every section of the approved storefront that carries editable content lives
 * here, and here only. The child theme's templates call these, and so do the
 * Elementor widgets in elementor/ - which is the whole point: one piece of
 * markup, two callers, so a page built in Elementor and a page rendered by PHP
 * cannot drift apart.
 *
 * The markup is the approved storefront's own, moved rather than rewritten. The
 * design system's stylesheets target these exact class names, so the structure
 * is not negotiable; what a section says is.
 *
 * Every method takes an $args array whose defaults reproduce the approved page
 * exactly. An Elementor widget passes the editor's values in their place.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Section renderers shared by the theme and by Elementor.
 */
class Star_Electric_Sections {

	/**
	 * Where the theme keeps the approved artwork.
	 *
	 * The images belong to the child theme, because they are part of the
	 * approved design rather than part of the catalogue. A plugin asking the
	 * active stylesheet for its own URI is the documented way to reach them.
	 */
	public static function art(): string {
		return get_stylesheet_directory_uri() . '/assets/images';
	}

	/**
	 * An icon from the shell, or nothing when the theme is not the child theme.
	 *
	 * @param string $name Icon name.
	 */
	private static function icon( string $name ): string {
		return class_exists( 'Star_Electric_Shell' ) ? Star_Electric_Shell::icon( $name ) : '';
	}

	/**
	 * A shell URL, falling back to the site root.
	 *
	 * @param string $key Page key.
	 */
	private static function url( string $key ): string {
		return class_exists( 'Star_Electric_Shell' ) ? Star_Electric_Shell::url( $key ) : home_url( '/' );
	}

	/**
	 * A category picture from the shell.
	 *
	 * @param string $slug   Category slug.
	 * @param int    $width  Display width.
	 * @param int    $height Display height.
	 */
	private static function category_image( string $slug, int $width, int $height ): string {
		return class_exists( 'Star_Electric_Shell' )
			? Star_Electric_Shell::category_image( $slug, $width, $height )
			: '';
	}

	/* --------------------------------------------------------------------- *
	 * The campaign banner
	 * --------------------------------------------------------------------- */

	/**
	 * The approved campaign slides.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function default_campaigns(): array {
		return array(
			array(
				'tone'    => 'navy',
				'art'     => 'protection',
				'eyebrow' => __( 'Circuit Protection', 'star-electric' ),
				'title'   => __( 'Built to Protect Every Circuit', 'star-electric' ),
				'text'    => __( 'MCBs, RCCBs, changeover gear and distribution boards, quoted against the rating you specify.', 'star-electric' ),
				'cta'     => __( 'Shop Circuit Protection', 'star-electric' ),
				'href'    => Star_Electric_Navigation::url( array( 'cat' => 'circuit-protection' ) ),
				'cta2'    => __( 'Request a Quote', 'star-electric' ),
				'href2'   => self::url( 'quote' ),
			),
			array(
				'tone'    => 'deep',
				'art'     => 'cables',
				'eyebrow' => __( 'Wires & Cables', 'star-electric' ),
				'title'   => __( 'Power Every Connection', 'star-electric' ),
				'text'    => __( 'Building wire, armoured power cable, LSZH, medium voltage and solar from the Pakistan Cables catalogue.', 'star-electric' ),
				'cta'     => __( 'Explore Cables', 'star-electric' ),
				'href'    => Star_Electric_Navigation::url( array( 'cat' => 'wires-cables' ) ),
				'cta2'    => __( 'Bulk Enquiry', 'star-electric' ),
				'href2'   => self::url( 'quote' ),
			),
			array(
				'tone'    => 'navy',
				'art'     => 'switches',
				'eyebrow' => __( 'Switches & Sockets', 'star-electric' ),
				'title'   => __( 'Control, Beautifully Finished', 'star-electric' ),
				'text'    => __( 'Modular switches, sockets and data outlets across the Aqua and Panasonic ranges.', 'star-electric' ),
				'cta'     => __( 'Shop Switches & Sockets', 'star-electric' ),
				'href'    => Star_Electric_Navigation::url( array( 'cat' => 'switches-sockets' ) ),
			),
			array(
				'tone'    => 'ink',
				'art'     => 'lighting',
				'eyebrow' => __( 'Lighting & Fixtures', 'star-electric' ),
				'title'   => __( 'Light Designed Around Your Space', 'star-electric' ),
				'text'    => __( 'Panels, downlights, floodlights and highbay fittings from the Coarts lighting catalogue.', 'star-electric' ),
				'cta'     => __( 'Browse Lighting', 'star-electric' ),
				'href'    => Star_Electric_Navigation::url( array( 'cat' => 'lighting' ) ),
			),
			array(
				'tone'    => 'deep',
				'art'     => 'smart',
				'eyebrow' => __( 'Fans & Smart', 'star-electric' ),
				'title'   => __( 'Smarter Control Starts Here', 'star-electric' ),
				'text'    => __( 'Inverter and AC/DC ceiling fans alongside Wi-Fi switches, dimmers and curtain controls.', 'star-electric' ),
				'cta'     => __( 'Shop Fans', 'star-electric' ),
				'href'    => Star_Electric_Navigation::url( array( 'cat' => 'fans-ventilation' ) ),
				'cta2'    => __( 'Smart Home', 'star-electric' ),
				'href2'   => Star_Electric_Navigation::url( array( 'cat' => 'smart-home' ) ),
			),
		);
	}

	/**
	 * The approved promo tiles beneath the campaign banner.
	 *
	 * @return array<int,array<string,string>>
	 */
	public static function default_promo_tiles(): array {
		return array(
			array(
				'kicker' => __( 'Protection', 'star-electric' ),
				'title'  => __( 'Breakers & Distribution', 'star-electric' ),
				'cta'    => __( 'Shop now', 'star-electric' ),
				'href'   => Star_Electric_Navigation::url( array( 'cat' => 'circuit-protection' ) ),
				'term'   => 'circuit-protection',
			),
			array(
				'kicker' => __( 'Smart electrical', 'star-electric' ),
				'title'  => __( 'Wi-Fi Switches & Devices', 'star-electric' ),
				'cta'    => __( 'Explore', 'star-electric' ),
				'href'   => Star_Electric_Navigation::url( array( 'cat' => 'smart-home' ) ),
				'term'   => 'smart-home',
			),
			array(
				'kicker' => __( 'Projects', 'star-electric' ),
				'title'  => __( 'Bulk & Contractor Orders', 'star-electric' ),
				'cta'    => __( 'Request a quote', 'star-electric' ),
				'href'   => self::url( 'quote' ),
				'term'   => 'wires-cables',
			),
		);
	}

	/**
	 * The campaign banner: department rail, carousel and promo tiles.
	 *
	 * These three are one section rather than three because the approved
	 * stylesheet lays them out as a single grid - .hcom__grid puts the rail in
	 * the first column and the banner and tiles, stacked, in the second. Split
	 * into separate top-level sections they would not be that grid any more.
	 *
	 * @param array $args Section arguments.
	 */
	public static function campaign_banner( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'heading'   => __( 'Star Electric Enterprises — electrical supplies, wiring, protection, lighting and fans', 'star-electric' ),
				'campaigns' => self::default_campaigns(),
				'tiles'     => self::default_promo_tiles(),
			)
		);

		$art       = self::art();
		$campaigns = array_values( (array) $args['campaigns'] );
		?>
		<section class="hcom" aria-label="<?php esc_attr_e( 'Departments and featured campaigns', 'star-electric' ); ?>">
			<div class="container hcom__grid">

				<h1 class="sr-only"><?php echo esc_html( (string) $args['heading'] ); ?></h1>

				<div class="hcom__rail" id="heroRail">
					<?php echo do_shortcode( '[star_hero_rail]' ); ?>
				</div>

				<div>
					<section class="hb" id="heroBanner" aria-roledescription="carousel"
						aria-label="<?php esc_attr_e( 'Featured campaigns', 'star-electric' ); ?>">
						<div class="hb__track" id="heroTrack">
							<?php foreach ( $campaigns as $i => $slide ) : ?>
								<article class="hs" data-tone="<?php echo esc_attr( (string) $slide['tone'] ); ?>"
									role="group" aria-roledescription="slide"
									aria-label="<?php echo esc_attr( (string) $slide['eyebrow'] ); ?>">
									<span class="hs__panel" aria-hidden="true"></span>
									<div class="hs__copy">
										<p class="hs__eyebrow"><?php echo esc_html( (string) $slide['eyebrow'] ); ?></p>
										<h2 class="hs__title"><?php echo esc_html( (string) $slide['title'] ); ?></h2>
										<p class="hs__text"><?php echo esc_html( (string) $slide['text'] ); ?></p>
										<div class="hs__cta">
											<a class="btn btn--accent btn--lg" href="<?php echo esc_url( (string) $slide['href'] ); ?>">
												<?php echo esc_html( (string) $slide['cta'] ); ?>
											</a>
											<?php if ( ! empty( $slide['cta2'] ) ) : ?>
												<a class="btn btn--outline btn--lg" href="<?php echo esc_url( (string) ( $slide['href2'] ?? '' ) ); ?>">
													<?php echo esc_html( (string) $slide['cta2'] ); ?>
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
										$src = ! empty( $slide['image'] )
											? (string) $slide['image']
											: $art . '/hero/hero-' . (string) $slide['art'] . '.webp';
										?>
										<img class="hs__art" src="<?php echo esc_url( $src ); ?>"
											alt="" width="1200" height="860" decoding="async"
											fetchpriority="<?php echo esc_attr( 0 === $i ? 'high' : 'low' ); ?>">
									</div>
								</article>
							<?php endforeach; ?>
						</div>

						<button class="hb__arrow hb__arrow--prev" type="button" id="heroPrev"
							aria-label="<?php esc_attr_e( 'Previous campaign', 'star-electric' ); ?>"></button>
						<button class="hb__arrow hb__arrow--next" type="button" id="heroNext"
							aria-label="<?php esc_attr_e( 'Next campaign', 'star-electric' ); ?>"></button>

						<div class="hb__nav" id="heroNav" aria-label="<?php esc_attr_e( 'Choose a campaign', 'star-electric' ); ?>">
							<?php foreach ( $campaigns as $i => $slide ) : ?>
								<button class="hnav" type="button" data-slide="<?php echo esc_attr( (string) $i ); ?>"
									aria-label="<?php echo esc_attr( (string) $slide['eyebrow'] ); ?>"
									<?php echo 0 === $i ? 'aria-current="true"' : ''; ?>>
									<span class="hnav__num"><?php echo esc_html( substr( '0' . ( $i + 1 ), -2 ) ); ?></span>
									<span class="hnav__label"><?php echo esc_html( (string) $slide['eyebrow'] ); ?></span>
									<span class="hnav__bar"><span class="hnav__fill"></span></span>
								</button>
							<?php endforeach; ?>
						</div>

						<p class="sr-only" aria-live="polite" id="heroStatus"></p>
					</section>

					<div class="hpromos" id="heroPromos">
						<?php foreach ( (array) $args['tiles'] as $tile ) : ?>
							<a class="ptile" href="<?php echo esc_url( (string) $tile['href'] ); ?>">
								<span class="ptile__body">
									<span class="ptile__kicker"><?php echo esc_html( (string) $tile['kicker'] ); ?></span>
									<span class="ptile__title"><?php echo esc_html( (string) $tile['title'] ); ?></span>
									<span class="ptile__link">
										<?php echo esc_html( (string) $tile['cta'] ); ?>
										<?php echo self::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
								</span>
								<span class="ptile__media">
									<?php
									if ( ! empty( $tile['image'] ) ) {
										printf(
											'<img src="%s" alt="" width="300" height="240" loading="lazy" decoding="async">',
											esc_url( (string) $tile['image'] )
										);
									} else {
										echo self::category_image( (string) $tile['term'], 300, 240 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									?>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Trust strip
	 * --------------------------------------------------------------------- */

	/**
	 * The approved trust strip items.
	 *
	 * @return array<int,array<string,string>>
	 */
	public static function default_trust_items(): array {
		return array(
			array(
				'icon'  => 'shield',
				'title' => __( 'Genuine Products', 'star-electric' ),
				'text'  => __( 'Sourced through our supply channels', 'star-electric' ),
			),
			array(
				'icon'  => 'tag',
				'title' => __( 'Retail & Bulk Pricing', 'star-electric' ),
				'text'  => __( 'Quotations available on request', 'star-electric' ),
			),
			array(
				'icon'  => 'headset',
				'title' => __( 'Expert Assistance', 'star-electric' ),
				'text'  => __( 'Ratings, sizes and specifications', 'star-electric' ),
			),
		);
	}

	/**
	 * The three-up strip under the campaign banner.
	 *
	 * @param array $args Section arguments.
	 */
	public static function trust_strip( array $args = array() ): void {
		$args  = wp_parse_args( $args, array( 'items' => self::default_trust_items() ) );
		$items = (array) $args['items'];
		?>
		<div class="container">
			<ul class="trust-strip">
				<?php foreach ( $items as $item ) : ?>
					<li class="trust-item">
						<span class="trust-item__ico"><?php echo self::icon( (string) $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span><strong><?php echo esc_html( (string) $item['title'] ); ?></strong><small><?php echo esc_html( (string) $item['text'] ); ?></small></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Departments
	 * --------------------------------------------------------------------- */

	/**
	 * The department strip, with its rail head and arrows.
	 *
	 * @param array $args Section arguments.
	 */
	public static function department_strip( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'title'        => __( 'Shop by Department', 'star-electric' ),
				'view_all'     => __( 'View All Categories', 'star-electric' ),
				'view_all_url' => self::url( 'shop' ),
			)
		);
		?>
		<section class="section section--sm" aria-labelledby="deptTitle">
			<div class="container">
				<div class="rail__head">
					<h2 class="rail__title" id="deptTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
					<div class="rail__tools">
						<a class="rail__all" href="<?php echo esc_url( (string) $args['view_all_url'] ); ?>"><?php echo esc_html( (string) $args['view_all'] ); ?></a>
						<div class="rail__arrows">
							<button class="rail__arrow" type="button" data-rail-prev aria-label="<?php esc_attr_e( 'Previous departments', 'star-electric' ); ?>">
								<?php echo self::icon( 'chevleft' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</button>
							<button class="rail__arrow" type="button" data-rail-next aria-label="<?php esc_attr_e( 'More departments', 'star-electric' ); ?>">
								<?php echo self::icon( 'chevright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</button>
						</div>
					</div>
				</div>
				<?php echo do_shortcode( '[star_departments]' ); ?>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Product rails
	 * --------------------------------------------------------------------- */

	/**
	 * One product rail.
	 *
	 * The products are queried live through the navigation class, so a rail is
	 * never a pasted list of cards that goes stale when a price changes.
	 *
	 * @param array $args Section arguments.
	 */
	public static function product_rail( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'id'    => '',
				'title' => '',
				'sel'   => array(),
				'limit' => 10,
			)
		);

		$sel = (array) $args['sel'];
		if ( empty( $sel ) ) {
			return;
		}

		echo Star_Electric_Shortcodes::rail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			(string) $args['id'],
			(string) $args['title'],
			Star_Electric_Navigation::products( $sel, (int) $args['limit'] ),
			Star_Electric_Navigation::url( $sel ),
			Star_Electric_Navigation::count( $sel )
		);
	}

	/**
	 * Every approved homepage rail, in order.
	 *
	 * @param array $args Section arguments.
	 */
	public static function product_rails( array $args = array() ): void {
		$args = wp_parse_args( $args, array( 'limit' => 10 ) );
		?>
		<section class="section section--sm section--tint">
			<div class="container" id="homeRails">
				<?php foreach ( Star_Electric_Navigation::rails() as $i => $rail ) : ?>
					<?php
					self::product_rail(
						array(
							'id'    => 'rail-' . $i,
							'title' => (string) $rail['title'],
							'sel'   => (array) $rail['sel'],
							'limit' => (int) $args['limit'],
						)
					);
					?>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Deals
	 * --------------------------------------------------------------------- */

	/**
	 * The approved deals promo cards.
	 *
	 * @return array<int,array<string,string>>
	 */
	public static function default_deal_cards(): array {
		return array(
			array(
				'variant' => 'dark',
				'term'    => 'fans-ventilation',
				'title'   => __( 'Fans & Ventilation', 'star-electric' ),
				'text'    => __( 'Ceiling, bracket, pedestal and exhaust fans.', 'star-electric' ),
				'cta'     => __( 'Shop Fans', 'star-electric' ),
				'button'  => 'btn--accent',
				'href'    => Star_Electric_Navigation::url( array( 'cat' => 'fans-ventilation' ) ),
			),
			array(
				'variant' => '',
				'image'   => 'promo-lighting',
				'title'   => __( 'Lighting & Fixtures', 'star-electric' ),
				'text'    => __( 'LED bulbs, panels, downlights and outdoor fittings.', 'star-electric' ),
				'cta'     => __( 'Shop Lighting', 'star-electric' ),
				'button'  => 'btn--primary',
				'href'    => Star_Electric_Navigation::url( array( 'cat' => 'lighting' ) ),
			),
			array(
				'variant' => 'dark',
				'image'   => 'promo-protection',
				'title'   => __( 'Circuit Protection', 'star-electric' ),
				'text'    => __( 'Breakers, boards, fuses and changeover gear.', 'star-electric' ),
				'cta'     => __( 'Shop Protection', 'star-electric' ),
				'button'  => 'btn--accent',
				'href'    => Star_Electric_Navigation::url( array( 'cat' => 'circuit-protection' ) ),
			),
		);
	}

	/**
	 * Deals and promotions: three promo cards above the discount rail.
	 *
	 * @param array $args Section arguments.
	 */
	public static function deals( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow'    => __( 'Current offers', 'star-electric' ),
				'title'      => __( 'Deals & Promotions', 'star-electric' ),
				'sub'        => __( 'Every reduction here is one the product’s own source publishes. Nothing is marked down by us.', 'star-electric' ),
				'link_label' => __( 'All deals', 'star-electric' ),
				'cards'      => self::default_deal_cards(),
				'rail_title' => __( 'Discounted at source', 'star-electric' ),
				'rail_limit' => 10,
			)
		);

		$art  = self::art();
		$more = self::url( 'deals' );
		?>
		<section class="section section--tint" aria-labelledby="dealTitle">
			<div class="container">
				<div class="section__head">
					<div>
						<p class="section__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>
						<h2 class="section__title" id="dealTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
						<p class="section__sub"><?php echo esc_html( (string) $args['sub'] ); ?></p>
					</div>
					<a class="link-more" href="<?php echo esc_url( $more ); ?>">
						<?php echo esc_html( (string) $args['link_label'] ); ?>
						<?php echo self::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>

				<div class="promo-grid" style="margin-bottom:24px">
					<?php foreach ( (array) $args['cards'] as $card ) : ?>
						<article class="promo-card<?php echo esc_attr( 'dark' === ( $card['variant'] ?? '' ) ? ' promo-card--dark' : '' ); ?>">
							<?php
							if ( ! empty( $card['term'] ) ) {
								echo self::category_image( (string) $card['term'], 1200, 620 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								printf(
									'<img src="%s" alt="" width="1200" height="620" loading="lazy" decoding="async">',
									esc_url( $art . '/banners/' . (string) ( $card['image'] ?? '' ) . '.webp' )
								);
							}
							?>
							<h3><?php echo esc_html( (string) $card['title'] ); ?></h3>
							<p><?php echo esc_html( (string) $card['text'] ); ?></p>
							<a class="btn <?php echo esc_attr( (string) ( $card['button'] ?? 'btn--accent' ) ); ?> btn--sm" href="<?php echo esc_url( (string) $card['href'] ); ?>"><?php echo esc_html( (string) $card['cta'] ); ?></a>
						</article>
					<?php endforeach; ?>
				</div>

				<div id="dealsRail">
					<?php
					echo do_shortcode(
						sprintf(
							'[star_products on_sale="yes" limit="%d" title="%s" id="rail-deals" view_all="%s"]',
							(int) $args['rail_limit'],
							esc_attr( (string) $args['rail_title'] ),
							esc_url( $more )
						)
					);
					?>
				</div>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Brands
	 * --------------------------------------------------------------------- */

	/**
	 * The brand grid. The brands themselves come from the taxonomy.
	 *
	 * @param array $args Section arguments.
	 */
	public static function brand_grid( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow'    => __( 'Manufacturers', 'star-electric' ),
				'title'      => __( 'Shop by Brand', 'star-electric' ),
				'sub'        => __( 'Brand names come from the approved product sources. No dealership, distribution or authorisation relationship is implied.', 'star-electric' ),
				'link_label' => __( 'All brands', 'star-electric' ),
			)
		);
		?>
		<section class="section" aria-labelledby="brandTitle">
			<div class="container">
				<div class="section__head">
					<div>
						<?php if ( '' !== (string) $args['eyebrow'] ) : ?>
							<p class="section__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>
						<?php endif; ?>
						<h2 class="section__title" id="brandTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
						<?php if ( '' !== (string) $args['sub'] ) : ?>
							<p class="section__sub"><?php echo esc_html( (string) $args['sub'] ); ?></p>
						<?php endif; ?>
					</div>
					<a class="link-more" href="<?php echo esc_url( self::url( 'brands' ) ); ?>">
						<?php echo esc_html( (string) $args['link_label'] ); ?>
						<?php echo self::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>
				<?php echo do_shortcode( '[star_brands]' ); ?>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Bulk / project call to action
	 * --------------------------------------------------------------------- */

	/**
	 * The approved bulk enquiry steps.
	 *
	 * @return string[]
	 */
	public static function default_bulk_steps(): array {
		return array(
			__( 'Share your item list or bill of quantities', 'star-electric' ),
			__( 'Receive a written quotation', 'star-electric' ),
			__( 'Arrange collection or delivery', 'star-electric' ),
		);
	}

	/**
	 * The bulk / project call to action.
	 *
	 * @param array $args Section arguments.
	 */
	public static function bulk_cta( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'title'          => __( 'Buying for a Project?', 'star-electric' ),
				'text'           => __( 'Bulk electrical requirements for contractors, electricians, builders and commercial projects. Send us your item list and we will prepare a written quotation.', 'star-electric' ),
				'primary_label'  => __( 'Request Bulk Quote', 'star-electric' ),
				'primary_url'    => self::url( 'quote' ),
				'second_label'   => __( 'Contact the Store', 'star-electric' ),
				'second_url'     => self::url( 'contact' ),
				'second_icon'    => 'mail',
				'steps'          => self::default_bulk_steps(),
				'heading_id'     => 'bulkTitle',
			)
		);
		?>
		<section class="section bulk" aria-labelledby="<?php echo esc_attr( (string) $args['heading_id'] ); ?>">
			<div class="container bulk__inner">
				<div>
					<h2 class="bulk__title" id="<?php echo esc_attr( (string) $args['heading_id'] ); ?>"><?php echo esc_html( (string) $args['title'] ); ?></h2>
					<p class="bulk__text"><?php echo esc_html( (string) $args['text'] ); ?></p>
					<div class="btn-row">
						<a class="btn btn--accent btn--lg" href="<?php echo esc_url( (string) $args['primary_url'] ); ?>"><?php echo esc_html( (string) $args['primary_label'] ); ?></a>
						<?php if ( '' !== (string) $args['second_label'] ) : ?>
							<a class="btn btn--light btn--lg" href="<?php echo esc_url( (string) $args['second_url'] ); ?>">
								<?php if ( '' !== (string) $args['second_icon'] ) : ?>
									<?php echo self::icon( (string) $args['second_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php endif; ?>
								<?php echo esc_html( (string) $args['second_label'] ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
				<ol class="bulk__steps">
					<?php foreach ( array_values( (array) $args['steps'] ) as $i => $step ) : ?>
						<li><b><?php echo esc_html( substr( '0' . ( $i + 1 ), -2 ) ); ?></b> <?php echo esc_html( (string) $step ); ?></li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Why choose us
	 * --------------------------------------------------------------------- */

	/**
	 * The approved "why choose us" cards.
	 *
	 * @return array<int,array<string,string>>
	 */
	public static function default_why_cards(): array {
		return array(
			array( 'icon' => 'shield', 'title' => __( 'Genuine Products', 'star-electric' ), 'text' => __( 'Stock sourced through our regular supply channels.', 'star-electric' ) ),
			array( 'icon' => 'bolt', 'title' => __( 'Competitive Pricing', 'star-electric' ), 'text' => __( 'Retail and bulk pricing available on request.', 'star-electric' ) ),
			array( 'icon' => 'headset', 'title' => __( 'Expert Assistance', 'star-electric' ), 'text' => __( 'Help selecting the right rating, size and specification.', 'star-electric' ) ),
			array( 'icon' => 'box', 'title' => __( 'Trade & Project Supply', 'star-electric' ), 'text' => __( 'Quotations for contractors and commercial requirements.', 'star-electric' ) ),
			array( 'icon' => 'pin', 'title' => __( 'Local Presence', 'star-electric' ), 'text' => __( 'A physical store in Saddar, Rawalpindi you can visit.', 'star-electric' ) ),
		);
	}

	/**
	 * The "why choose us" card grid.
	 *
	 * @param array $args Section arguments.
	 */
	public static function why_choose_us( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow' => __( 'Our approach', 'star-electric' ),
				'title'   => __( 'Why Choose Star Electric Enterprises', 'star-electric' ),
				'sub'     => __( 'How we work with retail customers and trade buyers.', 'star-electric' ),
				'cards'   => self::default_why_cards(),
			)
		);
		?>
		<section class="section" aria-labelledby="whyTitle">
			<div class="container">
				<div class="section__head section__head--center">
					<div>
						<p class="section__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>
						<h2 class="section__title" id="whyTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
						<p class="section__sub"><?php echo esc_html( (string) $args['sub'] ); ?></p>
					</div>
				</div>
				<ul class="why-grid">
					<?php foreach ( (array) $args['cards'] as $card ) : ?>
						<li class="why-card">
							<span class="why-card__ico"><?php echo self::icon( (string) $card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3><?php echo esc_html( (string) $card['title'] ); ?></h3>
							<p><?php echo esc_html( (string) $card['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Store band
	 * --------------------------------------------------------------------- */

	/**
	 * The store band.
	 *
	 * Only facts on record appear here. The shop address beyond the area, the
	 * phone number, WhatsApp and opening hours have not been supplied, so those
	 * rows are absent rather than shown as empty brackets on a live site.
	 *
	 * @param array $args Section arguments.
	 */
	public static function store_band( array $args = array() ): void {
		$count = wp_count_posts( 'product' );
		$count = isset( $count->publish ) ? (int) $count->publish : 0;

		$args = wp_parse_args(
			$args,
			array(
				'eyebrow'       => __( 'Visit us', 'star-electric' ),
				'title'         => get_bloginfo( 'name' ),
				'lead'          => __( 'Saddar, Rawalpindi', 'star-electric' ),
				'text'          => __( 'An electrical supplies store serving householders, electricians and contractors. Send us your item list or bill of quantities and we will come back with a written quotation for the ratings and sizes you need.', 'star-electric' ),
				'primary_label' => __( 'Request a Quotation', 'star-electric' ),
				'second_label'  => __( 'Contact the Store', 'star-electric' ),
				'fact_products' => __( 'individual products listed from seven supplier sources', 'star-electric' ),
				'fact2_title'   => __( 'Retail & bulk', 'star-electric' ),
				'fact2_text'    => __( 'quotations prepared for project and contractor orders', 'star-electric' ),
				'fact3_title'   => __( 'Source-checked', 'star-electric' ),
				'fact3_text'    => __( 'every price and specification taken from the supplier’s own catalogue', 'star-electric' ),
			)
		);
		?>
		<section class="section section--tint" aria-labelledby="storeTitle">
			<div class="container store-band">
				<div class="store-band__copy">
					<p class="section__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>
					<h2 class="section__title" id="storeTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
					<p class="t-lead" style="margin-top:8px"><?php echo esc_html( (string) $args['lead'] ); ?></p>
					<p class="store-band__text"><?php echo esc_html( (string) $args['text'] ); ?></p>
					<div class="btn-row">
						<a class="btn btn--accent" href="<?php echo esc_url( self::url( 'quote' ) ); ?>"><?php echo esc_html( (string) $args['primary_label'] ); ?></a>
						<a class="btn btn--ghost" href="<?php echo esc_url( self::url( 'contact' ) ); ?>"><?php echo esc_html( (string) $args['second_label'] ); ?></a>
					</div>
				</div>

				<ul class="store-band__facts">
					<li>
						<strong data-product-count><?php echo esc_html( number_format_i18n( $count ) ); ?></strong>
						<span><?php echo esc_html( (string) $args['fact_products'] ); ?></span>
					</li>
					<li>
						<strong><?php echo esc_html( (string) $args['fact2_title'] ); ?></strong>
						<span><?php echo esc_html( (string) $args['fact2_text'] ); ?></span>
					</li>
					<li>
						<strong><?php echo esc_html( (string) $args['fact3_title'] ); ?></strong>
						<span><?php echo esc_html( (string) $args['fact3_text'] ); ?></span>
					</li>
				</ul>
			</div>
		</section>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Page bodies
	 * --------------------------------------------------------------------- */

	/**
	 * The department tree, or nothing when the child theme is not active.
	 *
	 * @return array
	 */
	private static function tree(): array {
		return class_exists( 'Star_Electric_Shell' ) ? Star_Electric_Shell::category_tree() : array();
	}

	/**
	 * Fill the tokens an editor may use in body copy.
	 *
	 * A page that says how many departments the catalogue has must never be
	 * able to disagree with the catalogue, so that number is not typed: the
	 * editor writes {departments} and it is filled in when the page renders.
	 * {site} does the same for the business name, and the link tokens resolve
	 * to whatever page currently holds that role, so a link cannot rot when a
	 * page is renamed.
	 *
	 * @param string $text Copy as the editor wrote it.
	 */
	public static function tokens( string $text ): string {
		if ( false === strpos( $text, '{' ) ) {
			return $text;
		}

		$map = array(
			'{site}'    => get_bloginfo( 'name' ),
			'{shop}'    => self::url( 'shop' ),
			'{quote}'   => self::url( 'quote' ),
			'{contact}' => self::url( 'contact' ),
			'{privacy}' => self::url( 'privacy' ),
			'{faq}'     => self::url( 'faq' ),
		);

		if ( false !== strpos( $text, '{departments}' ) ) {
			$map['{departments}'] = number_format_i18n( count( self::tree() ) );
		}

		if ( false !== strpos( $text, '{products}' ) ) {
			$total = 0;
			foreach ( self::tree() as $node ) {
				$total += (int) $node['term']->count;
			}
			$map['{products}'] = number_format_i18n( $total );
		}

		return strtr( $text, $map );
	}

	/**
	 * Escaped copy with [text](url) links put back.
	 *
	 * The one piece of markup an editor is trusted to write, because a consent
	 * line and an FAQ answer both need a link inside a sentence and neither is
	 * worth a rich-text editor.
	 *
	 * @param string $text Copy as the editor wrote it.
	 */
	public static function rich( string $text ): string {
		return (string) preg_replace(
			'/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/',
			'<a class="link-inline" href="$2">$1</a>',
			esc_html( self::tokens( $text ) )
		);
	}

	/**
	 * The sidebar of small cards that sits beside a page's main column.
	 *
	 * A card is built from whichever parts it has: an intro line, a list of
	 * rows, a closing hint, and one or more buttons. A card with no heading is
	 * dropped, which is how an editor removes one.
	 *
	 * A row shows a label and a value; give the row a link and the value becomes
	 * that link, or - when there is no value - the label itself does.
	 *
	 * The wrapper is an <aside> beside a main column by default. The track
	 * order page puts the same cards in a two-column grid of their own and gives
	 * them a row of small buttons instead of stacked block ones, which is what
	 * the options are for.
	 *
	 * @param array $cards   Card definitions.
	 * @param array $options wrapper, class, style, buttons.
	 */
	public static function info_cards( array $cards, array $options = array() ): void {
		$options = wp_parse_args(
			$options,
			array(
				'wrapper' => 'aside',
				'class'   => '',
				'style'   => '',
				'buttons' => 'block',
			)
		);

		$star_open  = 'aside' === $options['wrapper'] ? '<aside>' : sprintf(
			'<div class="%s"%s>',
			esc_attr( (string) $options['class'] ),
			'' !== (string) $options['style'] ? ' style="' . esc_attr( (string) $options['style'] ) . '"' : ''
		);
		$star_close = 'aside' === $options['wrapper'] ? '</aside>' : '</div>';
		$star_row   = 'row' === $options['buttons'];

		echo $star_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
			<?php
			foreach ( $cards as $star_card ) :
				$star_title = trim( (string) ( $star_card['title'] ?? '' ) );
				if ( '' === $star_title ) {
					continue;
				}
				$star_text    = (string) ( $star_card['text'] ?? '' );
				$star_rows    = (array) ( $star_card['rows'] ?? array() );
				$star_hint    = (string) ( $star_card['hint'] ?? '' );
				$star_buttons = (array) ( $star_card['buttons'] ?? array() );
				?>
				<div class="info-card">
					<h3><?php echo esc_html( self::tokens( $star_title ) ); ?></h3>
					<?php if ( '' !== $star_text ) : ?>
						<p class="t-sm t-muted" style="margin-bottom:16px"><?php echo esc_html( self::tokens( $star_text ) ); ?></p>
					<?php endif; ?>

					<?php if ( $star_rows ) : ?>
						<ul class="info-list">
							<?php
							foreach ( $star_rows as $star_row ) :
								$star_label = self::tokens( (string) ( $star_row['label'] ?? '' ) );
								$star_value = self::tokens( (string) ( $star_row['value'] ?? '' ) );
								$star_url   = (string) ( $star_row['url'] ?? '' );
								?>
								<li>
									<?php echo self::icon( (string) ( $star_row['icon'] ?? 'info' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php if ( '' !== $star_url && '' === $star_value ) : ?>
										<span><a class="link-inline" href="<?php echo esc_url( $star_url ); ?>"><?php echo esc_html( $star_label ); ?></a></span>
									<?php elseif ( '' !== $star_url ) : ?>
										<span><strong><?php echo esc_html( $star_label ); ?></strong><a class="link-inline" href="<?php echo esc_url( $star_url ); ?>"><?php echo esc_html( $star_value ); ?></a></span>
									<?php else : ?>
										<span><strong><?php echo esc_html( $star_label ); ?></strong><?php echo esc_html( $star_value ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( '' !== $star_hint ) : ?>
						<p class="field__hint" style="margin-top:16px">
							<?php echo esc_html( self::tokens( $star_hint ) ); ?>
						</p>
					<?php endif; ?>

					<?php
					/*
					 * One button stands on its own; several are stacked. A stack of
					 * one is not a stack, and the approved pages draw it both ways
					 * for exactly that reason. The top margin is only needed when a
					 * list sits above the button - an intro line brings its own.
					 */
					$star_gap = $star_rows ? ' style="margin-top:16px"' : '';
					?>
					<?php if ( $star_row && $star_buttons ) : ?>
						<div class="btn-row">
							<?php foreach ( $star_buttons as $star_button ) : ?>
								<a class="btn btn--<?php echo esc_attr( (string) ( $star_button['style'] ?? 'ghost' ) ); ?> btn--sm" href="<?php echo esc_url( (string) ( $star_button['url'] ?? '' ) ); ?>"><?php echo esc_html( (string) ( $star_button['label'] ?? '' ) ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php elseif ( count( $star_buttons ) > 1 ) : ?>
						<div class="stack" style="gap:8px">
							<?php foreach ( $star_buttons as $star_button ) : ?>
								<a class="btn btn--<?php echo esc_attr( (string) ( $star_button['style'] ?? 'ghost' ) ); ?> btn--block btn--sm" href="<?php echo esc_url( (string) ( $star_button['url'] ?? '' ) ); ?>"><?php echo esc_html( (string) ( $star_button['label'] ?? '' ) ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php elseif ( $star_buttons ) : ?>
						<?php $star_button = reset( $star_buttons ); ?>
						<a class="btn btn--<?php echo esc_attr( (string) ( $star_button['style'] ?? 'ghost' ) ); ?> btn--block btn--sm"<?php echo $star_gap; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> href="<?php echo esc_url( (string) ( $star_button['url'] ?? '' ) ); ?>">
							<?php echo esc_html( (string) ( $star_button['label'] ?? '' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php
		echo $star_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * An article beside a sidebar of small cards - the About page's body.
	 *
	 * The article is a list of blocks rather than one block of HTML, so that
	 * headings and paragraphs stay separately editable and the department list
	 * can be a block that reads the live taxonomy instead of a written list
	 * that would drift from it.
	 *
	 * @param array $args blocks, facts_title, facts, links_title, links_text, links.
	 */
	public static function article_aside( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'blocks' => array(),
				'cards'  => array(),
			)
		);

		$star_tree = self::tree();
		?>
		<section class="section section--sm">
			<div class="container form-layout">

				<div class="prose">
					<?php
					foreach ( (array) $args['blocks'] as $star_block ) :
						$star_kind = (string) ( $star_block['kind'] ?? 'text' );
						$star_text = self::tokens( (string) ( $star_block['text'] ?? '' ) );

						if ( 'heading' === $star_kind ) :
							?>
							<h2><?php echo esc_html( $star_text ); ?></h2>
							<?php
						elseif ( 'departments' === $star_kind ) :
							?>
							<ul>
								<?php foreach ( $star_tree as $star_node ) : ?>
									<?php $star_names = wp_list_pluck( $star_node['children'], 'name' ); ?>
									<li>
										<strong><?php echo esc_html( $star_node['term']->name ); ?></strong>
										<?php if ( ! empty( $star_names ) ) : ?>
											&mdash; <?php echo esc_html( strtolower( implode( ', ', array_slice( $star_names, 0, 5 ) ) ) ); ?>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php
						elseif ( '' !== $star_text ) :
							?>
							<p><?php echo esc_html( $star_text ); ?></p>
							<?php
						endif;
					endforeach;
					?>
				</div>

				<?php self::info_cards( (array) $args['cards'] ); ?>
			</div>
		</section>
		<?php
	}

	/**
	 * A tinted band of icon cards, with an optional standing note beneath it.
	 *
	 * The note is where the About page records what it deliberately does not
	 * claim. It is content, not decoration - please leave it in place until the
	 * business confirms the facts it is holding open.
	 *
	 * @param array $args eyebrow, title, sub, cards, note.
	 */
	public static function value_grid( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow' => '',
				'title'   => '',
				'sub'     => '',
				'cards'   => array(),
				'note'    => '',
			)
		);
		?>
		<section class="section section--tint" aria-labelledby="valTitle">
			<div class="container">
				<div class="section__head section__head--center">
					<div>
						<p class="section__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>
						<h2 class="section__title" id="valTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
						<p class="section__sub"><?php echo esc_html( (string) $args['sub'] ); ?></p>
					</div>
				</div>

				<ul class="value-grid">
					<?php foreach ( (array) $args['cards'] as $star_card ) : ?>
						<li class="value-card">
							<span class="value-card__ico"><?php echo self::icon( (string) ( $star_card['icon'] ?? 'info' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3><?php echo esc_html( (string) ( $star_card['title'] ?? '' ) ); ?></h3>
							<p><?php echo esc_html( (string) ( $star_card['text'] ?? '' ) ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( '' !== (string) $args['note'] ) : ?>
					<p class="placeholder-note" style="margin-top:32px">
						<?php echo self::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( (string) $args['note'] ); ?></span>
					</p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/**
	 * The department cards, exactly as the approved storefront draws them.
	 *
	 * Five pages of the approved site use this list, so it is rendered in one
	 * place. A department with no picture gets the text half of the card rather
	 * than an empty frame - there is no stand-in photograph to put there.
	 *
	 * @param string $modifier Grid modifier class, e.g. cat-grid--4.
	 */
	public static function category_grid( string $modifier = 'cat-grid--4' ): void {
		$star_tree = self::tree();
		if ( empty( $star_tree ) ) {
			return;
		}
		?>
		<ul class="cat-grid <?php echo esc_attr( $modifier ); ?>">
			<?php foreach ( $star_tree as $star_node ) : ?>
				<?php
				$star_term  = $star_node['term'];
				$star_image = self::category_image( $star_term->slug, 900, 560 );
				$star_subs  = count( $star_node['children'] );
				?>
				<li>
					<a class="cat-card" href="<?php echo esc_url( (string) get_term_link( $star_term ) ); ?>">
						<?php if ( '' !== $star_image ) : ?>
							<span class="cat-card__photo"><?php echo wp_kses_post( $star_image ); ?></span>
						<?php endif; ?>
						<span class="cat-card__txt">
							<span class="cat-card__name"><?php echo esc_html( $star_term->name ); ?></span>
							<span class="cat-card__meta">
								<?php
								printf(
									/* translators: 1: product count, 2: subcategory count */
									esc_html( _n( '%1$s product', '%1$s products', (int) $star_term->count, 'star-electric-child' ) ) . ' &middot; ' .
									esc_html( _n( '%2$s subcategory', '%2$s subcategories', $star_subs, 'star-electric-child' ) ),
									esc_html( number_format_i18n( (int) $star_term->count ) ),
									esc_html( number_format_i18n( $star_subs ) )
								);
								?>
							</span>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * A headed section wrapping the department cards.
	 *
	 * @param array $args eyebrow, title, sub, link_label, link_url, modifier.
	 */
	public static function department_grid( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow'       => '',
				'title'         => '',
				'sub'           => '',
				'link_label'    => '',
				'link_url'      => self::url( 'shop' ),
				'modifier'      => 'cat-grid--4',
				'section_class' => 'section',
				'heading_id'    => 'deptTitle',
			)
		);

		$star_head = '' !== (string) $args['title'];
		?>
		<section class="<?php echo esc_attr( (string) $args['section_class'] ); ?>"<?php echo $star_head ? ' aria-labelledby="' . esc_attr( (string) $args['heading_id'] ) . '"' : ''; ?>>
			<div class="container">
				<?php if ( $star_head ) : ?>
					<div class="section__head">
						<div>
							<?php if ( '' !== (string) $args['eyebrow'] ) : ?>
								<p class="section__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>
							<?php endif; ?>
							<h2 class="section__title" id="<?php echo esc_attr( (string) $args['heading_id'] ); ?>"><?php echo esc_html( (string) $args['title'] ); ?></h2>
							<p class="section__sub"><?php echo esc_html( self::tokens( (string) $args['sub'] ) ); ?></p>
						</div>
						<?php if ( '' !== (string) $args['link_label'] ) : ?>
							<a class="link-more" href="<?php echo esc_url( (string) $args['link_url'] ); ?>">
								<?php echo esc_html( (string) $args['link_label'] ); ?>
								<?php echo self::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php self::category_grid( (string) $args['modifier'] ); ?>
			</div>
		</section>
		<?php
	}
	/**
	 * The contact page's body: the enquiry form beside its sidebar.
	 *
	 * The form itself belongs to Star_Electric_Forms - the nonce, the honeypot,
	 * where it posts, what it accepts and what it does with a submission are all
	 * decided there. Everything this renders is wording.
	 *
	 * @param array $args Panel wording, field labels, subjects and sidebar.
	 */
	public static function contact_form( array $args = array() ): void {
		if ( ! class_exists( 'Star_Electric_Forms' ) ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'heading'        => __( 'Send us a message', 'star-electric' ),
				'note'           => __( 'We reply to the email address you give us.', 'star-electric' ),
				'required_label' => __( 'required', 'star-electric' ),
				'label_name'     => __( 'Full name', 'star-electric' ),
				'label_phone'    => __( 'Phone', 'star-electric' ),
				'label_email'    => __( 'Email', 'star-electric' ),
				'label_subject'  => __( 'Subject', 'star-electric' ),
				'label_message'  => __( 'Message', 'star-electric' ),
				'subject_prompt' => __( 'Select a subject…', 'star-electric' ),
				'subjects'       => self::default_contact_subjects(),
				'placeholder'    => __( 'Tell us what you need — include ratings, sizes or model numbers where you know them.', 'star-electric' ),
				'consent'        => __( 'I agree that my details may be used to respond to this message, as described in the [Privacy Policy]({privacy}).', 'star-electric' ),
				'submit'         => __( 'Send Message', 'star-electric' ),
				'cards'          => array(),
			)
		);

		list( $star_notice_type, $star_notice ) = Star_Electric_Forms::notice();
		?>
		<section class="section section--sm">
			<div class="container form-layout">

				<div class="panel">
					<div class="panel__head">
						<div>
							<h2 class="panel__title"><?php echo esc_html( (string) $args['heading'] ); ?></h2>
							<p class="t-sm t-muted" style="margin-top:4px"><?php echo esc_html( (string) $args['note'] ); ?></p>
						</div>
						<span class="t-xs t-faint"><span class="t-accent">*</span> <?php echo esc_html( (string) $args['required_label'] ); ?></span>
					</div>

					<?php if ( '' !== $star_notice ) : ?>
						<div class="notice notice--<?php echo esc_attr( $star_notice_type ); ?>" role="status" style="margin-bottom:20px">
							<p><?php echo esc_html( $star_notice ); ?></p>
						</div>
					<?php endif; ?>

					<form class="stack-5" method="post" action="<?php echo esc_url( Star_Electric_Forms::action() ); ?>">
						<?php Star_Electric_Forms::fields( 'contact' ); ?>

						<div class="form-grid">
							<div class="field">
								<label class="field__label" for="cName"><?php echo esc_html( (string) $args['label_name'] ); ?> <span class="req">*</span></label>
								<input class="input" id="cName" name="name" type="text" autocomplete="name" required>
							</div>
							<div class="field">
								<label class="field__label" for="cPhone"><?php echo esc_html( (string) $args['label_phone'] ); ?> <span class="req">*</span></label>
								<input class="input" id="cPhone" name="phone" type="tel" autocomplete="tel" required>
							</div>
							<div class="field">
								<label class="field__label" for="cEmail"><?php echo esc_html( (string) $args['label_email'] ); ?> <span class="req">*</span></label>
								<input class="input" id="cEmail" name="email" type="email" autocomplete="email" required>
							</div>
							<div class="field">
								<label class="field__label" for="cSubject"><?php echo esc_html( (string) $args['label_subject'] ); ?> <span class="req">*</span></label>
								<select class="select" id="cSubject" name="subject" required>
									<option value=""><?php echo esc_html( (string) $args['subject_prompt'] ); ?></option>
									<?php foreach ( (array) $args['subjects'] as $star_subject ) : ?>
										<?php printf( '<option>%s</option>', esc_html( (string) $star_subject ) ); ?>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="field span-2">
								<label class="field__label" for="cMessage"><?php echo esc_html( (string) $args['label_message'] ); ?> <span class="req">*</span></label>
								<textarea class="textarea" id="cMessage" name="message" required
									placeholder="<?php echo esc_attr( (string) $args['placeholder'] ); ?>"></textarea>
							</div>
						</div>

						<label class="check">
							<input type="checkbox" name="consent" value="yes" required>
							<span>
								<?php echo self::rich( (string) $args['consent'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="req">*</span>
							</span>
						</label>

						<button class="btn btn--accent btn--lg" type="submit" style="justify-self:start">
							<?php echo esc_html( (string) $args['submit'] ); ?>
						</button>
					</form>
				</div>

				<?php self::info_cards( (array) $args['cards'] ); ?>
			</div>
		</section>
		<?php
	}

	/**
	 * The subjects the approved contact form offers.
	 *
	 * @return string[]
	 */
	public static function default_contact_subjects(): array {
		return array(
			__( 'Product enquiry', 'star-electric' ),
			__( 'Stock availability', 'star-electric' ),
			__( 'Order status', 'star-electric' ),
			__( 'Bulk / project quotation', 'star-electric' ),
			__( 'Delivery question', 'star-electric' ),
			__( 'Returns or replacement', 'star-electric' ),
			__( 'Other', 'star-electric' ),
		);
	}

	/**
	 * A band of copy with a row of buttons under it.
	 *
	 * The Contact page's closing band is this shape, and it says what it says
	 * deliberately: there is no map, street address, opening hours or
	 * collection process on it, because none is on record. Please do not add
	 * one until the business supplies it.
	 *
	 * @param array $args eyebrow, title, sub, buttons, tint, heading_id.
	 */
	public static function text_band( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'eyebrow'    => '',
				'title'      => '',
				'sub'        => '',
				'buttons'    => array(),
				'tint'       => true,
				'heading_id' => 'bandTitle',
			)
		);

		$star_class = $args['tint'] ? 'section section--tint' : 'section';
		?>
		<section class="<?php echo esc_attr( $star_class ); ?>" aria-labelledby="<?php echo esc_attr( (string) $args['heading_id'] ); ?>">
			<div class="container">
				<div>
					<p class="section__eyebrow"><?php echo esc_html( self::tokens( (string) $args['eyebrow'] ) ); ?></p>
					<h2 class="section__title" id="<?php echo esc_attr( (string) $args['heading_id'] ); ?>"><?php echo esc_html( self::tokens( (string) $args['title'] ) ); ?></h2>
					<p class="section__sub" style="margin-bottom:24px">
						<?php echo esc_html( self::tokens( (string) $args['sub'] ) ); ?>
					</p>

					<div class="btn-row">
						<?php foreach ( (array) $args['buttons'] as $star_button ) : ?>
							<a class="btn btn--<?php echo esc_attr( (string) ( $star_button['style'] ?? 'accent' ) ); ?>" href="<?php echo esc_url( (string) ( $star_button['url'] ?? '' ) ); ?>">
								<?php echo esc_html( (string) ( $star_button['label'] ?? '' ) ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
	/**
	 * The navy hero the quote page opens with.
	 *
	 * Its breadcrumb is drawn light on the dark band rather than reusing the
	 * page head, which is the approved page's own treatment.
	 *
	 * @param array $args crumbs, title, text, tags, steps, heading_id.
	 */
	public static function bulk_hero( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'crumbs'     => array(),
				'title'      => '',
				'text'       => '',
				'tags'       => array(),
				'steps'      => array(),
				'heading_id' => 'quoteTitle',
			)
		);
		?>
		<section class="section bulk section--sm" aria-labelledby="<?php echo esc_attr( (string) $args['heading_id'] ); ?>">
			<div class="container">
				<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'star-electric' ); ?>" style="margin-bottom:16px">
					<ol class="breadcrumb">
						<?php
						$star_last = count( (array) $args['crumbs'] ) - 1;
						$star_i    = 0;
						foreach ( (array) $args['crumbs'] as $star_crumb ) {
							if ( $star_i > 0 ) {
								echo '<li class="sep" aria-hidden="true" style="color:rgba(255,255,255,.35)">/</li>';
							}
							$star_label = (string) ( $star_crumb['label'] ?? '' );
							$star_url   = (string) ( $star_crumb['url'] ?? '' );
							if ( $star_i === $star_last || '' === $star_url ) {
								printf( '<li aria-current="page" style="color:#fff">%s</li>', esc_html( $star_label ) );
							} else {
								printf(
									'<li><a href="%s" style="color:rgba(255,255,255,.6)">%s</a></li>',
									esc_url( $star_url ),
									esc_html( $star_label )
								);
							}
							++$star_i;
						}
						?>
					</ol>
				</nav>

				<div class="bulk__inner">
					<div>
						<h1 class="bulk__title" id="<?php echo esc_attr( (string) $args['heading_id'] ); ?>"><?php echo esc_html( (string) $args['title'] ); ?></h1>
						<p class="bulk__text"><?php echo esc_html( (string) $args['text'] ); ?></p>
						<ul class="hero__cta" style="gap:8px;flex-wrap:wrap">
							<?php foreach ( (array) $args['tags'] as $star_tag ) : ?>
								<li class="hero__eyebrow" style="margin:0"><?php echo esc_html( (string) $star_tag ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<ol class="bulk__steps">
						<?php foreach ( array_values( (array) $args['steps'] ) as $star_i => $star_step ) : ?>
							<li><b><?php echo esc_html( substr( '0' . ( $star_i + 1 ), -2 ) ); ?></b> <?php echo esc_html( (string) $star_step ); ?></li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * The project types the approved quote form offers.
	 *
	 * @return string[]
	 */
	public static function default_project_types(): array {
		return array(
			__( 'Residential — new build', 'star-electric' ),
			__( 'Residential — renovation / rewiring', 'star-electric' ),
			__( 'Commercial fit-out', 'star-electric' ),
			__( 'Office installation', 'star-electric' ),
			__( 'Industrial / factory', 'star-electric' ),
			__( 'Shop or retail unit', 'star-electric' ),
			__( 'Maintenance / repair contract', 'star-electric' ),
			__( 'Other', 'star-electric' ),
		);
	}

	/**
	 * The quote request form and its sidebar.
	 *
	 * A "Request a Quote" button on a product page arrives here with that
	 * product's id, which is put into a hidden field and named above the form so
	 * the enquiry says what it is about. Resolving that id, and everything else
	 * the submission does, belongs to Star_Electric_Forms.
	 *
	 * @param array $args Wording, field labels, project types and sidebar.
	 */
	public static function quote_form( array $args = array() ): void {
		if ( ! class_exists( 'Star_Electric_Forms' ) ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'heading'         => __( 'Quote Request', 'star-electric' ),
				'note'            => __( 'The more detail you provide, the more accurate the quotation.', 'star-electric' ),
				'required_label'  => __( 'required', 'star-electric' ),
				'quoting_for'     => __( 'Quoting for:', 'star-electric' ),
				'legend_you'      => __( 'Your details', 'star-electric' ),
				'legend_project'  => __( 'Project details', 'star-electric' ),
				'label_name'      => __( 'Full name', 'star-electric' ),
				'label_company'   => __( 'Company / Contractor name', 'star-electric' ),
				'label_phone'     => __( 'Phone', 'star-electric' ),
				'label_email'     => __( 'Email', 'star-electric' ),
				'label_type'      => __( 'Project type', 'star-electric' ),
				'type_prompt'     => __( 'Select a project type…', 'star-electric' ),
				'types'           => self::default_project_types(),
				'label_date'      => __( 'Required delivery date', 'star-electric' ),
				'hint_date'       => __( 'Approximate is fine.', 'star-electric' ),
				'label_items'     => __( 'Product requirements', 'star-electric' ),
				'placeholder_items' => __( "List the products you need — for example:\n\n1.5mm single core copper wire — 20 coils\n32A MCB single pole — 40 units\n12W LED bulb B22 — 150 units", 'star-electric' ),
				'hint_items'      => __( 'Include ratings, sizes and any brand preference where it matters.', 'star-electric' ),
				'label_qty'       => __( 'Estimated total quantities / order value', 'star-electric' ),
				'placeholder_qty' => __( 'e.g. approx. 400 items, or an approximate budget range', 'star-electric' ),
				'label_notes'     => __( 'Additional notes', 'star-electric' ),
				'placeholder_notes' => __( 'Site location, phased delivery, access restrictions, anything else we should know…', 'star-electric' ),
				'consent'         => __( 'I agree that my details may be used to respond to this quote request, as described in the [Privacy Policy]({privacy}).', 'star-electric' ),
				'submit'          => __( 'Submit Quote Request', 'star-electric' ),
				'cards'           => array(),
			)
		);

		list( $star_notice_type, $star_notice ) = Star_Electric_Forms::notice();

		// The link says quote_product, not product: WordPress owns "product" as
		// WooCommerce's post-type query var and answers ?product=2333 with a 404.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$star_product_id = isset( $_GET['quote_product'] ) ? absint( $_GET['quote_product'] ) : 0;
		$star_product    = $star_product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $star_product_id ) : null;
		?>
		<section class="section section--sm">
			<div class="container form-layout">

				<div class="panel">
					<div class="panel__head">
						<div>
							<h2 class="panel__title"><?php echo esc_html( (string) $args['heading'] ); ?></h2>
							<p class="t-sm t-muted" style="margin-top:4px"><?php echo esc_html( (string) $args['note'] ); ?></p>
						</div>
						<span class="t-xs t-faint"><span class="t-accent">*</span> <?php echo esc_html( (string) $args['required_label'] ); ?></span>
					</div>

					<?php if ( '' !== $star_notice ) : ?>
						<div class="notice notice--<?php echo esc_attr( $star_notice_type ); ?>" role="status" style="margin-bottom:20px">
							<p><?php echo esc_html( $star_notice ); ?></p>
						</div>
					<?php endif; ?>

					<form class="stack-5" method="post" action="<?php echo esc_url( Star_Electric_Forms::action() ); ?>">
						<?php Star_Electric_Forms::fields( 'quote' ); ?>

						<?php if ( $star_product instanceof WC_Product ) : ?>
							<input type="hidden" name="product" value="<?php echo esc_attr( (string) $star_product->get_id() ); ?>">
							<div class="info-note" style="margin-bottom:4px">
								<p>
									<?php echo esc_html( (string) $args['quoting_for'] ); ?>
									<strong><?php echo esc_html( $star_product->get_name() ); ?></strong>
								</p>
							</div>
						<?php endif; ?>

						<fieldset style="border:0;padding:0;margin:0">
							<legend class="field__label" style="margin-bottom:16px;font-size:14px"><?php echo esc_html( (string) $args['legend_you'] ); ?></legend>
							<div class="form-grid">
								<div class="field">
									<label class="field__label" for="qName"><?php echo esc_html( (string) $args['label_name'] ); ?> <span class="req">*</span></label>
									<input class="input" id="qName" name="name" type="text" autocomplete="name" required>
								</div>
								<div class="field">
									<label class="field__label" for="qCompany"><?php echo esc_html( (string) $args['label_company'] ); ?></label>
									<input class="input" id="qCompany" name="company" type="text" autocomplete="organization">
								</div>
								<div class="field">
									<label class="field__label" for="qPhone"><?php echo esc_html( (string) $args['label_phone'] ); ?> <span class="req">*</span></label>
									<input class="input" id="qPhone" name="phone" type="tel" autocomplete="tel" required>
								</div>
								<div class="field">
									<label class="field__label" for="qEmail"><?php echo esc_html( (string) $args['label_email'] ); ?> <span class="req">*</span></label>
									<input class="input" id="qEmail" name="email" type="email" autocomplete="email" required>
								</div>
							</div>
						</fieldset>

						<hr>

						<fieldset style="border:0;padding:0;margin:0">
							<legend class="field__label" style="margin-bottom:16px;font-size:14px"><?php echo esc_html( (string) $args['legend_project'] ); ?></legend>
							<div class="form-grid">
								<div class="field">
									<label class="field__label" for="qType"><?php echo esc_html( (string) $args['label_type'] ); ?> <span class="req">*</span></label>
									<select class="select" id="qType" name="project_type" required>
										<option value=""><?php echo esc_html( (string) $args['type_prompt'] ); ?></option>
										<?php foreach ( (array) $args['types'] as $star_type ) : ?>
											<?php printf( '<option>%s</option>', esc_html( (string) $star_type ) ); ?>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="field">
									<label class="field__label" for="qDate"><?php echo esc_html( (string) $args['label_date'] ); ?></label>
									<input class="input" id="qDate" name="required_date" type="date">
									<p class="field__hint"><?php echo esc_html( (string) $args['hint_date'] ); ?></p>
								</div>
								<div class="field span-2">
									<label class="field__label" for="qItems"><?php echo esc_html( (string) $args['label_items'] ); ?> <span class="req">*</span></label>
									<textarea class="textarea" id="qItems" name="requirements" required
										placeholder="<?php echo esc_attr( (string) $args['placeholder_items'] ); ?>"><?php
										echo $star_product instanceof WC_Product
											? esc_textarea( $star_product->get_name() . "\n" )
											: '';
									?></textarea>
									<p class="field__hint"><?php echo esc_html( (string) $args['hint_items'] ); ?></p>
								</div>
								<div class="field span-2">
									<label class="field__label" for="qQty"><?php echo esc_html( (string) $args['label_qty'] ); ?></label>
									<input class="input" id="qQty" name="quantities" type="text"
										placeholder="<?php echo esc_attr( (string) $args['placeholder_qty'] ); ?>">
								</div>
								<div class="field span-2">
									<label class="field__label" for="qMessage"><?php echo esc_html( (string) $args['label_notes'] ); ?></label>
									<textarea class="textarea" id="qMessage" name="message" style="min-height:110px"
										placeholder="<?php echo esc_attr( (string) $args['placeholder_notes'] ); ?>"></textarea>
								</div>
							</div>
						</fieldset>

						<hr>

						<label class="check">
							<input type="checkbox" name="consent" value="yes" required>
							<span>
								<?php echo self::rich( (string) $args['consent'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="req">*</span>
							</span>
						</label>

						<button class="btn btn--accent btn--lg" type="submit" style="justify-self:start">
							<?php echo esc_html( (string) $args['submit'] ); ?>
						</button>
					</form>
				</div>

				<?php self::info_cards( (array) $args['cards'] ); ?>
			</div>
		</section>
		<?php
	}

	/**
	 * The complaint types the approved form offers.
	 *
	 * @return string[]
	 */
	public static function default_complaint_types(): array {
		return array(
			__( 'Wrong item received', 'star-electric' ),
			__( 'Item damaged on arrival', 'star-electric' ),
			__( 'Item faulty or not working', 'star-electric' ),
			__( 'Missing item from the order', 'star-electric' ),
			__( 'Delivery problem or delay', 'star-electric' ),
			__( 'Billing or pricing issue', 'star-electric' ),
			__( 'Service at the store', 'star-electric' ),
			__( 'Other', 'star-electric' ),
		);
	}

	/**
	 * The complaint form and its sidebar.
	 *
	 * The page promises no resolution time, exactly as the approved page does:
	 * the store has supplied no complaints procedure or turnaround, and stating
	 * one would be inventing a commitment.
	 *
	 * @param array $args Wording, field labels, complaint types and sidebar.
	 */
	public static function complaint_form( array $args = array() ): void {
		if ( ! class_exists( 'Star_Electric_Forms' ) ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'heading'            => __( 'Complaint Details', 'star-electric' ),
				'note'               => __( 'The more detail you give, the faster we can look into it.', 'star-electric' ),
				'required_label'     => __( 'required', 'star-electric' ),
				'legend_you'         => __( 'Your details', 'star-electric' ),
				'legend_problem'     => __( 'What went wrong', 'star-electric' ),
				'label_name'         => __( 'Full name', 'star-electric' ),
				'label_order'        => __( 'Order number', 'star-electric' ),
				'placeholder_order'  => __( 'The reference on your order confirmation', 'star-electric' ),
				'label_phone'        => __( 'Phone', 'star-electric' ),
				'label_email'        => __( 'Email', 'star-electric' ),
				'label_type'         => __( 'Complaint type', 'star-electric' ),
				'type_prompt'        => __( 'Select the type of problem…', 'star-electric' ),
				'types'              => self::default_complaint_types(),
				'label_subject'      => __( 'Subject', 'star-electric' ),
				'placeholder_subject' => __( 'A short summary of the problem', 'star-electric' ),
				'label_description'  => __( 'Description', 'star-electric' ),
				'placeholder_description' => __( 'What was ordered, what arrived, when it happened, and what you would like us to do.', 'star-electric' ),
				'consent'            => __( 'I agree that my details may be used to investigate and respond to this complaint, as described in the [Privacy Policy]({privacy}).', 'star-electric' ),
				'submit'             => __( 'Submit Complaint', 'star-electric' ),
				'cards'              => array(),
			)
		);

		list( $star_notice_type, $star_notice ) = Star_Electric_Forms::notice();
		?>
		<section class="section section--sm">
			<div class="container form-layout">

				<div class="panel">
					<div class="panel__head">
						<div>
							<h2 class="panel__title"><?php echo esc_html( (string) $args['heading'] ); ?></h2>
							<p class="t-sm t-muted" style="margin-top:4px"><?php echo esc_html( (string) $args['note'] ); ?></p>
						</div>
						<span class="t-xs t-faint"><span class="t-accent">*</span> <?php echo esc_html( (string) $args['required_label'] ); ?></span>
					</div>

					<?php if ( '' !== $star_notice ) : ?>
						<div class="notice notice--<?php echo esc_attr( $star_notice_type ); ?>" role="status" style="margin-bottom:20px">
							<p><?php echo esc_html( $star_notice ); ?></p>
						</div>
					<?php endif; ?>

					<form class="stack-5" method="post" action="<?php echo esc_url( Star_Electric_Forms::action() ); ?>">
						<?php Star_Electric_Forms::fields( 'complaint' ); ?>

						<fieldset style="border:0;padding:0;margin:0">
							<legend class="field__label" style="margin-bottom:16px;font-size:14px"><?php echo esc_html( (string) $args['legend_you'] ); ?></legend>
							<div class="form-grid">
								<div class="field">
									<label class="field__label" for="xName"><?php echo esc_html( (string) $args['label_name'] ); ?> <span class="req">*</span></label>
									<input class="input" id="xName" name="name" type="text" autocomplete="name" required>
								</div>
								<div class="field">
									<label class="field__label" for="xOrder"><?php echo esc_html( (string) $args['label_order'] ); ?></label>
									<input class="input" id="xOrder" name="order" type="text"
										placeholder="<?php echo esc_attr( (string) $args['placeholder_order'] ); ?>">
								</div>
								<div class="field">
									<label class="field__label" for="xPhone"><?php echo esc_html( (string) $args['label_phone'] ); ?> <span class="req">*</span></label>
									<input class="input" id="xPhone" name="phone" type="tel" autocomplete="tel" required>
								</div>
								<div class="field">
									<label class="field__label" for="xEmail"><?php echo esc_html( (string) $args['label_email'] ); ?> <span class="req">*</span></label>
									<input class="input" id="xEmail" name="email" type="email" autocomplete="email" required>
								</div>
							</div>
						</fieldset>

						<hr>

						<fieldset style="border:0;padding:0;margin:0">
							<legend class="field__label" style="margin-bottom:16px;font-size:14px"><?php echo esc_html( (string) $args['legend_problem'] ); ?></legend>
							<div class="form-grid">
								<div class="field">
									<label class="field__label" for="xType"><?php echo esc_html( (string) $args['label_type'] ); ?> <span class="req">*</span></label>
									<select class="select" id="xType" name="type" required>
										<option value=""><?php echo esc_html( (string) $args['type_prompt'] ); ?></option>
										<?php foreach ( (array) $args['types'] as $star_type ) : ?>
											<?php printf( '<option>%s</option>', esc_html( (string) $star_type ) ); ?>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="field">
									<label class="field__label" for="xSubject"><?php echo esc_html( (string) $args['label_subject'] ); ?> <span class="req">*</span></label>
									<input class="input" id="xSubject" name="subject" type="text" required
										placeholder="<?php echo esc_attr( (string) $args['placeholder_subject'] ); ?>">
								</div>
								<div class="field span-2">
									<label class="field__label" for="xDesc"><?php echo esc_html( (string) $args['label_description'] ); ?> <span class="req">*</span></label>
									<textarea class="textarea" id="xDesc" name="description" required style="min-height:160px"
										placeholder="<?php echo esc_attr( (string) $args['placeholder_description'] ); ?>"></textarea>
								</div>
							</div>
						</fieldset>

						<label class="check">
							<input type="checkbox" name="consent" value="yes" required>
							<span>
								<?php echo self::rich( (string) $args['consent'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="req">*</span>
							</span>
						</label>

						<button class="btn btn--accent btn--lg" type="submit" style="justify-self:start">
							<?php echo esc_html( (string) $args['submit'] ); ?>
						</button>
					</form>
				</div>

				<?php self::info_cards( (array) $args['cards'] ); ?>
			</div>
		</section>
		<?php
	}
	/**
	 * The order tracking panel and the two cards under it.
	 *
	 * The tracking form itself is WooCommerce's own shortcode, so an order
	 * lookup keeps working exactly as WooCommerce means it to.
	 *
	 * @param array $args hint, cards.
	 */
	public static function track_order( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'hint'  => '',
				'cards' => array(),
			)
		);
		?>
		<section class="section section--sm">
			<div class="container container--mid">

				<div class="panel" style="margin-bottom:32px">
					<?php echo do_shortcode( '[woocommerce_order_tracking]' ); ?>

					<p class="field__hint" style="margin-top:16px">
						<?php echo self::rich( (string) $args['hint'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</p>
				</div>

				<?php
				self::info_cards(
					(array) $args['cards'],
					array(
						'wrapper' => 'div',
						'class'   => 'form-grid',
						'style'   => 'margin-top:32px',
						'buttons' => 'row',
					)
				);
				?>

			</div>
		</section>
		<?php
	}

	/**
	 * One tinted-and-plain band per department, listing its subcategories.
	 *
	 * Departments, subcategories, their pictures and their counts all come from
	 * the catalogue; a department with no subcategories is skipped, and the
	 * bands alternate shade so the page reads as a list rather than a wall.
	 *
	 * @param array $args link_label.
	 */
	public static function subcategory_sections( array $args = array() ): void {
		$args = wp_parse_args( $args, array( 'link_label' => __( 'Browse department', 'star-electric' ) ) );

		foreach ( self::tree() as $star_i => $star_node ) :
			$star_term = $star_node['term'];
			$star_subs = $star_node['children'];
			if ( empty( $star_subs ) ) {
				continue;
			}
			?>
			<section class="section section--sm<?php echo esc_attr( 0 === $star_i % 2 ? ' section--tint' : '' ); ?>"
				aria-labelledby="dept-<?php echo esc_attr( $star_term->slug ); ?>">
				<div class="container">
					<div class="section__head">
						<div>
							<h2 class="section__title" style="font-size:20px" id="dept-<?php echo esc_attr( $star_term->slug ); ?>">
								<?php echo esc_html( $star_term->name ); ?>
							</h2>
							<p class="section__sub">
								<?php
								printf(
									/* translators: %s: product count */
									esc_html( _n( '%s product', '%s products', (int) $star_term->count, 'star-electric-child' ) ),
									esc_html( number_format_i18n( (int) $star_term->count ) )
								);
								?>
							</p>
						</div>
						<a class="link-more" href="<?php echo esc_url( (string) get_term_link( $star_term ) ); ?>">
							<?php echo esc_html( (string) $args['link_label'] ); ?>
							<?php echo self::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</div>

					<ul class="subcat-grid">
						<?php foreach ( $star_subs as $star_sub ) : ?>
							<?php $star_art = class_exists( 'Star_Electric_Shell' ) ? Star_Electric_Shell::category_icon( $star_sub ) : ''; ?>
							<li>
								<a class="subcat-card" href="<?php echo esc_url( (string) get_term_link( $star_sub ) ); ?>">
									<?php echo wp_kses_post( $star_art ); ?>
									<span><?php echo esc_html( $star_sub->name ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
			<?php
		endforeach;
	}

	/**
	 * Every brand, in the catalogue's own order, with the A-Z filter above it.
	 *
	 * The tiles are monograms because no dealership, distribution or
	 * authorisation relationship is on record. That is a fact about the
	 * business, not a design choice, and the note above the grid says so.
	 *
	 * A brand whose source publishes ranges rather than individual products has
	 * nothing to link to, so it is kept out of this grid and listed by
	 * brand_families() instead.
	 *
	 * @param array $args Wording for the note, the search box and the empty state.
	 */
	public static function brand_directory( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'note_strong'  => __( 'Brand names come from the approved product sources; the logo tiles are placeholders.', 'star-electric' ),
				'note'         => __( 'Star Electric Enterprises has not stated any dealership, distribution or authorisation relationship, so none is claimed here. Supplied logo files will replace these tiles.', 'star-electric' ),
				'search_label' => __( 'Search brands', 'star-electric' ),
				'search_hint'  => __( 'Start typing a brand name…', 'star-electric' ),
				'all_label'    => __( 'All', 'star-electric' ),
				'empty_title'  => __( 'No brands match that search', 'star-electric' ),
				'empty_text'   => __( 'Try a different letter or clear the search box to see the whole directory.', 'star-electric' ),
			)
		);

		list( $star_listed, $star_family ) = self::brands();
		$star_available = array();
		foreach ( array_merge( $star_listed, $star_family ) as $star_term ) {
			$star_available[ self::brand_letter( $star_term->name ) ] = true;
		}
		?>
		<section class="section section--sm">
			<div class="container">

				<div class="placeholder-note" style="margin-bottom:32px">
					<?php echo self::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span>
						<strong><?php echo esc_html( (string) $args['note_strong'] ); ?></strong>
						<?php echo esc_html( (string) $args['note'] ); ?>
					</span>
				</div>

				<div class="panel panel--tint" style="margin-bottom:32px">
					<div class="form-grid" style="grid-template-columns:minmax(0,1fr) auto;align-items:end">
						<div class="field">
							<label class="field__label" for="brandSearch"><?php echo esc_html( (string) $args['search_label'] ); ?></label>
							<input class="input" id="brandSearch" type="search" autocomplete="off"
								placeholder="<?php echo esc_attr( (string) $args['search_hint'] ); ?>">
						</div>
						<p class="t-sm t-muted" id="brandCount" style="padding-bottom:12px">
							<?php
							printf(
								/* translators: %s: number of brands with individual products */
								esc_html__( '%s brands with individual products', 'star-electric-child' ),
								esc_html( (string) count( $star_listed ) )
							);
							if ( ! empty( $star_family ) ) {
								printf(
									/* translators: %s: number of brands listed at family level */
									esc_html__( ' · %s listed at family level', 'star-electric-child' ),
									esc_html( (string) count( $star_family ) )
								);
							}
							?>
						</p>
					</div>

					<div class="az-bar" id="azBar" role="group" aria-label="<?php esc_attr_e( 'Filter brands by first letter', 'star-electric-child' ); ?>" style="margin-top:20px">
						<button type="button" data-letter="" aria-pressed="true"><?php echo esc_html( (string) $args['all_label'] ); ?></button>
						<?php foreach ( str_split( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' ) as $star_letter ) : ?>
							<button type="button" data-letter="<?php echo esc_attr( $star_letter ); ?>" aria-pressed="false"
								<?php disabled( isset( $star_available[ $star_letter ] ), false ); ?>><?php echo esc_html( $star_letter ); ?></button>
						<?php endforeach; ?>
					</div>
				</div>

				<ul class="brand-grid" id="brandDirectory">
					<?php foreach ( $star_listed as $star_term ) : ?>
						<?php
						$star_mark = (string) get_term_meta( $star_term->term_id, '_star_electric_mark', true );
						if ( '' === $star_mark ) {
							$star_mark = strtoupper( substr( $star_term->name, 0, 1 ) );
						}
						?>
						<li data-brand="<?php echo esc_attr( strtolower( $star_term->name ) ); ?>" data-letter="<?php echo esc_attr( self::brand_letter( $star_term->name ) ); ?>">
							<a class="brand-card" href="<?php echo esc_url( Star_Electric_Navigation::brand_url( $star_term ) ); ?>">
								<span class="brand-card__mark" aria-hidden="true"><?php echo esc_html( $star_mark ); ?></span>
								<span class="brand-card__name"><?php echo esc_html( $star_term->name ); ?></span>
								<span class="brand-card__note">
									<?php
									printf(
										/* translators: %s: product count */
										esc_html( _n( '%s product', '%s products', (int) $star_term->count, 'star-electric-child' ) ),
										esc_html( (string) (int) $star_term->count )
									);
									?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>

				<div id="brandEmpty" hidden>
					<div class="empty-state">
						<span class="empty-state__ico">
							<?php echo self::icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<h2><?php echo esc_html( (string) $args['empty_title'] ); ?></h2>
						<p><?php echo esc_html( (string) $args['empty_text'] ); ?></p>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * The brands whose source publishes ranges rather than products.
	 *
	 * Ranges are shown for navigation and enquiry only. They are not sellable
	 * products and they carry no price, and the section says so - please leave
	 * that sentence in place.
	 *
	 * @param array $args title, sub.
	 */
	public static function brand_families( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'title' => __( 'Listed at product-family level', 'star-electric' ),
				'sub'   => __( 'For these brands the approved source does not publish individual product pages, so no individual products are listed. The ranges below are shown for navigation and enquiry only — they are not sellable products and carry no price.', 'star-electric' ),
			)
		);

		list( , $star_family ) = self::brands();
		if ( empty( $star_family ) ) {
			return;
		}
		?>
		<section class="section section--sm" id="familySection" aria-labelledby="familyTitle">
			<div class="container">
				<div class="section__head">
					<div>
						<h2 class="section__title" id="familyTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
						<p class="section__sub">
							<?php echo esc_html( (string) $args['sub'] ); ?>
						</p>
					</div>
				</div>
				<div id="familyDirectory">
					<?php foreach ( $star_family as $star_term ) : ?>
						<?php
						$star_ranges = do_shortcode( '[star_ranges brand="' . esc_attr( $star_term->slug ) . '" limit="60"]' );
						if ( '' === trim( $star_ranges ) ) {
							continue;
						}
						?>
						<div data-brand="<?php echo esc_attr( strtolower( $star_term->name ) ); ?>" data-letter="<?php echo esc_attr( self::brand_letter( $star_term->name ) ); ?>">
							<?php echo $star_ranges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Every brand, split into those with products and those without.
	 *
	 * The order is the catalogue's own - Pakistan Cables first, then Aqua - and
	 * not alphabetical. The importer records it on the term; a brand that
	 * somehow has none sorts last rather than disappearing.
	 *
	 * @return array{0:WP_Term[],1:WP_Term[]}
	 */
	private static function brands(): array {
		static $split = null;
		if ( null !== $split ) {
			return $split;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'star_brand',
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
		$terms = is_wp_error( $terms ) ? array() : $terms;

		usort(
			$terms,
			static function ( WP_Term $a, WP_Term $b ): int {
				$oa = get_term_meta( $a->term_id, '_star_electric_order', true );
				$ob = get_term_meta( $b->term_id, '_star_electric_order', true );
				$oa = '' === $oa ? PHP_INT_MAX : (int) $oa;
				$ob = '' === $ob ? PHP_INT_MAX : (int) $ob;
				return $oa === $ob ? strcmp( $a->name, $b->name ) : ( $oa <=> $ob );
			}
		);

		$listed = array();
		$family = array();
		foreach ( $terms as $term ) {
			if ( (int) $term->count > 0 ) {
				$listed[] = $term;
			} else {
				$family[] = $term;
			}
		}

		$split = array( $listed, $family );
		return $split;
	}

	/**
	 * The letter a brand files under in the A-Z bar.
	 *
	 * @param string $name Brand name.
	 */
	private static function brand_letter( string $name ): string {
		$letter = strtoupper( substr( remove_accents( $name ), 0, 1 ) );
		return preg_match( '/[A-Z]/', $letter ) ? $letter : '#';
	}
	/* --------------------------------------------------------------------- *
	 * The catalogue
	 * --------------------------------------------------------------------- */

	/**
	 * The three wide promo cards the deals page opens with.
	 *
	 * A card takes its picture from a department, from the approved banner
	 * artwork, or from an image chosen in Elementor - in that order of
	 * preference, so the approved artwork stays unless someone replaces it
	 * deliberately.
	 *
	 * @param array $args cards.
	 */
	public static function promo_grid( array $args = array() ): void {
		$args = wp_parse_args( $args, array( 'cards' => array() ) );
		?>
		<section class="section section--sm">
			<div class="container">
				<div class="promo-grid">
					<?php foreach ( (array) $args['cards'] as $star_card ) : ?>
						<article class="promo-card<?php echo ! empty( $star_card['dark'] ) ? ' promo-card--dark' : ''; ?>">
							<?php
							$star_dept   = (string) ( $star_card['department'] ?? '' );
							$star_banner = (string) ( $star_card['banner'] ?? '' );
							$star_image  = (string) ( $star_card['image'] ?? '' );

							if ( '' !== $star_dept ) {
								echo self::category_image( $star_dept, 1200, 620 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} elseif ( '' !== $star_banner || '' !== $star_image ) {
								printf(
									'<img src="%s" alt="" width="1200" height="620" loading="lazy" decoding="async">',
									esc_url( '' !== $star_image ? $star_image : self::art() . '/banners/' . $star_banner . '.webp' )
								);
							}
							?>
							<h2 style="font-size:19px"><?php echo esc_html( (string) ( $star_card['title'] ?? '' ) ); ?></h2>
							<p><?php echo esc_html( (string) ( $star_card['text'] ?? '' ) ); ?></p>
							<a class="btn btn--<?php echo esc_attr( (string) ( $star_card['style'] ?? 'accent' ) ); ?> btn--sm" href="<?php echo esc_url( (string) ( $star_card['url'] ?? '' ) ); ?>">
								<?php echo esc_html( (string) ( $star_card['label'] ?? '' ) ); ?>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * The products a supplier source currently lists below its previous price.
	 *
	 * A discount exists only where the importer recorded one, which it does only
	 * where the source published both a previous and a current price. A product
	 * priced on enquiry can never qualify, so nothing on the deals page can show
	 * a saving the source did not publish.
	 *
	 * @param int $paged Page number.
	 */
	public static function deals_query( int $paged ): WP_Query {
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => 12,
			'paged'               => max( 1, $paged ),
			'ignore_sticky_posts' => true,
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'     => '_star_electric_discount',
					'value'   => 0,
					'type'    => 'NUMERIC',
					'compare' => '>',
				),
			),
		);

		if ( class_exists( 'Star_Electric_Filters' ) ) {
			$args = Star_Electric_Filters::query_args( $args, 'discount' );
		} else {
			$args['meta_key'] = '_star_electric_discount'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
		}

		return new WP_Query( $args );
	}

	/**
	 * The filter sidebar, toolbar, product grid and pager.
	 *
	 * One renderer for the shop, the department and brand archives and the
	 * deals page, because on the approved storefront they are one component.
	 * They differ in what they carry through the filter form, which sorts they
	 * offer, what they say when nothing matches and how their pager is built -
	 * and in nothing else.
	 *
	 * @param array $args Query, wording and the handful of documented variations.
	 */
	public static function catalogue( array $args = array() ): void {
		global $wp_query;

		$args = wp_parse_args(
			$args,
			array(
				'query'          => null,
				'prefix'         => 'f',
				'carry'          => array( 's', 'post_type', 'product_cat', 'star_brand', 'star_department', 'view' ),
				'section_style'  => '',
				'filters_label'  => __( 'Product filters', 'star-electric' ),
				'filters_button' => __( 'Filters', 'star-electric' ),
				'count_none'     => __( 'No products found', 'star-electric' ),
				'sort_label'     => __( 'Sort products', 'star-electric' ),
				'sorts'          => null,
				'sort_default'   => '',
				'pager_label'    => __( 'Product pages', 'star-electric' ),
				'prev'           => __( 'Prev', 'star-electric' ),
				'next'           => __( 'Next', 'star-electric' ),
				'pager_edges'    => true,
				'pager_args'     => array(),
				'empty_icon'     => 'search',
				'empty_title'    => __( 'No products match those filters', 'star-electric' ),
				'empty_text'     => __( 'Try removing a filter or widening the price range to see more of the catalogue.', 'star-electric' ),
				'empty_button'   => __( 'Clear all filters', 'star-electric' ),
				'empty_url'      => '',
				'show_results'   => __( 'Show results', 'star-electric' ),
				'after'          => '',
			)
		);

		$star_query   = $args['query'] instanceof WP_Query ? $args['query'] : $wp_query;
		$star_own     = $args['query'] instanceof WP_Query;
		$star_filters = class_exists( 'Star_Electric_Filters' );
		$star_active  = $star_filters ? Star_Electric_Filters::active() : array( 'sort' => '' );
		$star_found   = (int) $star_query->found_posts;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$star_view = ( isset( $_GET['view'] ) && 'list' === $_GET['view'] ) ? 'list' : 'grid';
		$star_page = max( 1, (int) get_query_var( 'paged' ), $star_own ? (int) get_query_var( 'page' ) : 1 );
		?>
		<section class="section section--sm"<?php echo '' !== (string) $args['section_style'] ? ' style="' . esc_attr( (string) $args['section_style'] ) . '"' : ''; ?>>
			<form class="container shop-layout" method="get" id="shopFilters">
				<?php
				/*
				 * A GET form replaces the whole query string, so anything the
				 * current view depends on has to be carried through explicitly.
				 */
				// phpcs:disable WordPress.Security.NonceVerification.Recommended
				foreach ( (array) $args['carry'] as $star_keep ) {
					if ( isset( $_GET[ $star_keep ] ) && '' !== $_GET[ $star_keep ] ) {
						printf(
							'<input type="hidden" name="%s" value="%s">',
							esc_attr( $star_keep ),
							esc_attr( sanitize_text_field( wp_unslash( $_GET[ $star_keep ] ) ) )
						);
					}
				}
				// phpcs:enable WordPress.Security.NonceVerification.Recommended
				?>

				<aside class="filters" data-filters aria-label="<?php echo esc_attr( (string) $args['filters_label'] ); ?>">
					<?php
					if ( $star_filters ) {
						echo Star_Electric_Filters::render( $args['prefix'] . '0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</aside>

				<div>
					<div class="shop-toolbar">
						<div class="shop-toolbar__left">
							<button class="btn btn--ghost btn--sm filter-open" type="button" id="filterOpen">
								<?php echo self::icon( 'filter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span><?php echo esc_html( (string) $args['filters_button'] ); ?></span>
							</button>
							<span id="resultCount">
								<?php
								if ( $star_found > 0 ) {
									$star_per   = max( 1, (int) $star_query->get( 'posts_per_page' ) );
									$star_first = ( ( $star_page - 1 ) * $star_per ) + 1;
									$star_last  = min( $star_found, $star_page * $star_per );
									printf(
										/* translators: 1: first result, 2: last result, 3: total */
										esc_html__( 'Showing %1$d–%2$d of %3$d products', 'star-electric-child' ),
										(int) $star_first,
										(int) $star_last,
										(int) $star_found
									);
								} else {
									echo esc_html( (string) $args['count_none'] );
								}
								?>
							</span>
						</div>

						<div class="shop-toolbar__right">
							<label class="sr-only" for="sortSelect"><?php echo esc_html( (string) $args['sort_label'] ); ?></label>
							<select class="select" id="sortSelect" name="sort">
								<?php
								$star_sorts = is_array( $args['sorts'] ) ? $args['sorts'] : ( $star_filters ? Star_Electric_Filters::sorts() : array() );
								$star_now   = '' !== $star_active['sort'] ? $star_active['sort'] : (string) $args['sort_default'];
								foreach ( $star_sorts as $star_key => $star_label ) :
									?>
									<option value="<?php echo esc_attr( $star_key ); ?>" <?php selected( $star_now, $star_key ); ?>>
										<?php echo esc_html( $star_label ); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<div class="view-toggle" role="group" aria-label="<?php esc_attr_e( 'Product view', 'star-electric-child' ); ?>">
								<button type="button" data-view="grid" aria-pressed="<?php echo esc_attr( 'grid' === $star_view ? 'true' : 'false' ); ?>"
									aria-label="<?php esc_attr_e( 'Grid view', 'star-electric-child' ); ?>">
									<?php echo self::icon( 'grid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</button>
								<button type="button" data-view="list" aria-pressed="<?php echo esc_attr( 'list' === $star_view ? 'true' : 'false' ); ?>"
									aria-label="<?php esc_attr_e( 'List view', 'star-electric-child' ); ?>">
									<?php echo self::icon( 'list' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</button>
							</div>
						</div>
					</div>

					<?php
					if ( $star_filters ) {
						get_template_part( 'template-parts/filter-chips' );
					}
					?>

					<?php if ( $star_query->have_posts() ) : ?>

						<ul class="product-grid<?php echo esc_attr( 'list' === $star_view ? ' product-grid--list' : '' ); ?>">
							<?php
							while ( $star_query->have_posts() ) {
								$star_query->the_post();
								wc_get_template_part( 'content', 'product' );
							}
							if ( $star_own ) {
								wp_reset_postdata();
							}
							?>
						</ul>

						<?php
						$star_total = (int) $star_query->max_num_pages;
						$star_pages = paginate_links(
							array_merge(
								array(
									'total'     => $star_total,
									'current'   => $star_page,
									'type'      => 'array',
									'prev_text' => (string) $args['prev'],
									'next_text' => (string) $args['next'],
								),
								(array) $args['pager_args']
							)
						);

						/*
						 * The approved pager always shows Prev and Next, greyed
						 * out at the ends. paginate_links() drops them instead,
						 * which made the bar a different width on the first and
						 * last page.
						 */
						if ( $args['pager_edges'] && is_array( $star_pages ) && $star_total > 1 ) {
							if ( 1 === $star_page ) {
								array_unshift( $star_pages, '<span class="is-gap">' . esc_html( (string) $args['prev'] ) . '</span>' );
							}
							if ( $star_page === $star_total ) {
								$star_pages[] = '<span class="is-gap">' . esc_html( (string) $args['next'] ) . '</span>';
							}
						}
						?>
						<?php if ( $star_pages ) : ?>
							<nav class="pagination" id="pagination" aria-label="<?php echo esc_attr( (string) $args['pager_label'] ); ?>" style="margin-top:32px">
								<?php foreach ( $star_pages as $star_link ) : ?>
									<?php echo wp_kses_post( $star_link ); ?>
								<?php endforeach; ?>
							</nav>
						<?php endif; ?>

					<?php else : ?>

						<div id="catalogEmpty">
							<div class="empty-state">
								<span class="empty-state__ico">
									<?php echo self::icon( (string) $args['empty_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
								<h2><?php echo esc_html( (string) $args['empty_title'] ); ?></h2>
								<p><?php echo esc_html( (string) $args['empty_text'] ); ?></p>
								<a class="btn btn--accent" href="<?php echo esc_url( (string) $args['empty_url'] ); ?>">
									<?php echo esc_html( (string) $args['empty_button'] ); ?>
								</a>
							</div>
						</div>

					<?php endif; ?>

					<?php echo $args['after']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>

				<div class="drawer drawer--right" id="filterDrawer" hidden>
					<div class="drawer__scrim" data-side-close></div>
					<div class="drawer__panel" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( (string) $args['filters_label'] ); ?>">
						<div class="drawer__head">
							<span class="drawer__title"><?php echo esc_html( (string) $args['filters_button'] ); ?></span>
							<button class="icon-btn" type="button" data-side-close aria-label="<?php esc_attr_e( 'Close filters', 'star-electric-child' ); ?>">
								<?php echo self::icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</button>
						</div>
						<div class="drawer__body" data-filters>
							<?php
							if ( $star_filters ) {
								echo Star_Electric_Filters::render( $args['prefix'] . '1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
						<div class="drawer__foot">
							<button class="btn btn--accent btn--block" type="submit">
								<?php echo esc_html( (string) $args['show_results'] ); ?>
							</button>
						</div>
					</div>
				</div>
			</form>
		</section>
		<?php
	}

	/**
	 * The standing note that closes the deals page.
	 *
	 * No countdown, no urgency, no offer period: every discount shown is one the
	 * product's own source publishes. Please leave this note in place - it is
	 * what keeps the page honest about where its prices come from.
	 *
	 * @param array $args strong, text.
	 */
	public static function deals_note( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'strong' => __( 'No countdown timers or urgency claims are used on this page.', 'star-electric' ),
				'text'   => __( 'Every discount shown is one the product’s own source publishes: the previous price and the current price are both taken from that source on the date recorded against the product. Nothing is marked down here, and no offer period is claimed.', 'star-electric' ),
			)
		);
		?>
		<section class="section section--tint section--sm">
			<div class="container">
				<div class="placeholder-note">
					<?php echo self::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span>
						<strong><?php echo esc_html( (string) $args['strong'] ); ?></strong>
						<?php echo esc_html( (string) $args['text'] ); ?>
					</span>
				</div>
			</div>
		</section>
		<?php
	}
	/* --------------------------------------------------------------------- *
	 * The product archive
	 * --------------------------------------------------------------------- */

	/**
	 * Whether the archive being viewed is a department or a category.
	 *
	 * The approved storefront has two archive treatments, not one. shop.html
	 * leads with a page head; category.html leads with a hero - eyebrow, title,
	 * blurb, two buttons and the department's own photograph - then a row of
	 * subcategory tiles. A department or category view gets that page; the shop,
	 * a brand view and a search keep the shop's head.
	 */
	private static function is_department(): bool {
		$object = get_queried_object();
		return $object instanceof WP_Term
			&& class_exists( 'Star_Electric_Taxonomies' )
			&& in_array( $object->taxonomy, array( 'product_cat', Star_Electric_Taxonomies::DEPARTMENT ), true );
	}

	/**
	 * The archive's supporting line.
	 *
	 * A term's own description where there is one, and the approved shop copy
	 * otherwise. Nothing is invented to fill the space.
	 *
	 * @param string $shop_sub Copy for the shop itself.
	 */
	private static function archive_sub( string $shop_sub ): string {
		$object = get_queried_object();
		if ( $object instanceof WP_Term ) {
			return wp_strip_all_tags( (string) term_description( $object ) );
		}
		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return $shop_sub;
		}
		return '';
	}

	/**
	 * The head of a product archive: a page head, or a department hero.
	 *
	 * @param array $args Wording only; which of the two is shown is the query's decision.
	 */
	public static function archive_head( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'crumb_home'   => __( 'Home', 'star-electric' ),
				'crumb_shop'   => __( 'Shop', 'star-electric' ),
				'shop_title'   => __( 'Shop All Products', 'star-electric' ),
				'shop_sub'     => __( 'The complete Star Electric Enterprises catalogue. Filter by category, brand, price, availability or product type to narrow the range.', 'star-electric' ),
				'eyebrow'      => __( 'Department', 'star-electric' ),
				'quote_label'  => __( 'Request a Quote', 'star-electric' ),
				'all_label'    => __( 'All departments', 'star-electric' ),
				'subcat_title' => __( 'Browse subcategories', 'star-electric' ),
			)
		);

		$star_object = get_queried_object();
		$star_dept   = self::is_department();
		$star_sub    = self::archive_sub( (string) $args['shop_sub'] );
		?>
		<div class="page-head">
			<div class="container">
				<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'star-electric-child' ); ?>">
					<ol class="breadcrumb">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( (string) $args['crumb_home'] ); ?></a></li>
						<li class="sep" aria-hidden="true">/</li>
						<?php if ( $star_object instanceof WP_Term ) : ?>
							<li><a href="<?php echo esc_url( self::url( 'shop' ) ); ?>"><?php echo esc_html( (string) $args['crumb_shop'] ); ?></a></li>
							<li class="sep" aria-hidden="true">/</li>
							<li aria-current="page"><?php echo esc_html( $star_object->name ); ?></li>
						<?php else : ?>
							<li aria-current="page"><?php echo esc_html( (string) $args['crumb_shop'] ); ?></li>
						<?php endif; ?>
					</ol>
				</nav>
				<?php if ( ! $star_dept ) : ?>
					<h1 class="page-head__title">
						<?php
						echo $star_object instanceof WP_Term
							? esc_html( $star_object->name )
							: esc_html( (string) $args['shop_title'] );
						?>
					</h1>
					<?php if ( '' !== $star_sub ) : ?>
						<p class="page-head__sub"><?php echo esc_html( $star_sub ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $star_dept ) : ?>
			<?php
			/*
			 * A subcategory view keeps its department's photograph, exactly as
			 * the approved page does - the picture belongs to the department,
			 * and only departments have one.
			 */
			$star_art_slug = '';
			$star_children = array();
			if ( 'product_cat' === $star_object->taxonomy ) {
				$star_art_slug = $star_object->slug;
				if ( $star_object->parent ) {
					$star_parent = get_term( (int) $star_object->parent, 'product_cat' );
					if ( $star_parent instanceof WP_Term ) {
						$star_art_slug = $star_parent->slug;
					}
				}

				$star_children = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'parent'     => $star_object->term_id,
						'hide_empty' => true,
					)
				);
				$star_children = Star_Electric_Taxonomies::in_catalogue_order(
					is_wp_error( $star_children ) ? array() : (array) $star_children
				);
			}
			?>
			<section class="section section--sm" style="padding-bottom:0">
				<div class="container">
					<div class="cat-hero">
						<div>
							<p class="section__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>
							<h1 class="section__title"><?php echo esc_html( $star_object->name ); ?></h1>
							<?php if ( '' !== $star_sub ) : ?>
								<p class="section__sub"><?php echo esc_html( $star_sub ); ?></p>
							<?php endif; ?>
							<div class="btn-row" style="margin-top:20px">
								<a class="btn btn--accent btn--sm" href="<?php echo esc_url( self::url( 'quote' ) ); ?>">
									<?php echo esc_html( (string) $args['quote_label'] ); ?>
								</a>
								<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( self::url( 'shop' ) ); ?>">
									<?php echo esc_html( (string) $args['all_label'] ); ?>
								</a>
							</div>
						</div>
						<?php $star_art_html = self::category_image( $star_art_slug, 900, 560 ); ?>
						<?php if ( '' !== $star_art_html ) : ?>
							<div class="cat-hero__art"><?php echo wp_kses_post( $star_art_html ); ?></div>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $star_children ) ) : ?>
						<h2 class="section__title" id="subcatTitle" style="font-size:19px;margin-bottom:16px">
							<?php echo esc_html( (string) $args['subcat_title'] ); ?>
						</h2>
						<ul class="subcat-grid" id="subcatGrid" style="margin-bottom:40px">
							<?php foreach ( $star_children as $star_child ) : ?>
								<li>
									<a class="subcat-card" href="<?php echo esc_url( (string) get_term_link( $star_child ) ); ?>">
										<?php echo wp_kses_post( class_exists( 'Star_Electric_Shell' ) ? Star_Electric_Shell::category_icon( $star_child ) : '' ); ?>
										<span><?php echo esc_html( $star_child->name ); ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>
		<?php
	}

	/**
	 * The archive's filter sidebar, toolbar, grid, pager and family ranges.
	 *
	 * @param array $args Wording only.
	 */
	public static function archive_catalogue( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'filters_button' => __( 'Filters', 'star-electric' ),
				'count_none'     => __( 'No products found', 'star-electric' ),
				'show_results'   => __( 'Show results', 'star-electric' ),
				'empty_title'    => __( 'No products match those filters', 'star-electric' ),
				'empty_text'     => __( 'Try removing a filter or widening the price range to see more of the catalogue.', 'star-electric' ),
				'empty_button'   => __( 'Clear all filters', 'star-electric' ),
				'ranges_title'   => __( 'Ranges listed at family level', 'star-electric' ),
				'ranges_text'    => __( 'For these ranges the approved source does not publish individual product pages, so they are not listed as products. Tell us the rating or size you need and we will quote against it.', 'star-electric' ),
			)
		);

		$star_object = get_queried_object();
		$star_dept   = self::is_department();

		/*
		 * Ranges the source publishes without individual product pages.
		 * Navigation and enquiry only: never priced, never added to cart, and
		 * kept clearly below the products so the two are not confused.
		 */
		$star_ranges = '';
		if ( $star_dept ) {
			$star_ranges = do_shortcode(
				sprintf(
					'[star_ranges %s="%s" limit="60"]',
					'product_cat' === $star_object->taxonomy ? 'category' : 'department',
					esc_attr( $star_object->slug )
				)
			);
		}

		$star_after = '';
		if ( '' !== trim( $star_ranges ) ) {
			ob_start();
			?>
			<section class="panel" id="catFamilySection" style="margin-top:32px">
				<h2 class="section__title" style="font-size:20px" id="catFamilyTitle">
					<?php echo esc_html( (string) $args['ranges_title'] ); ?>
				</h2>
				<p class="t-sm t-muted" style="margin:8px 0 20px">
					<?php echo esc_html( (string) $args['ranges_text'] ); ?>
				</p>
				<div id="catFamilyList"><?php echo $star_ranges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</section>
			<?php
			$star_after = (string) ob_get_clean();
		}

		self::catalogue(
			array(
				'section_style'  => $star_dept ? 'padding-top:0' : '',
				'filters_button' => (string) $args['filters_button'],
				'count_none'     => (string) $args['count_none'],
				'show_results'   => (string) $args['show_results'],
				'empty_title'    => (string) $args['empty_title'],
				'empty_text'     => (string) $args['empty_text'],
				'empty_button'   => (string) $args['empty_button'],
				'empty_url'      => class_exists( 'Star_Electric_Filters' ) ? Star_Electric_Filters::clear_url() : self::url( 'shop' ),
				'after'          => $star_after,
			)
		);
	}
	/* --------------------------------------------------------------------- *
	 * The product page
	 * --------------------------------------------------------------------- *
	 *
	 * Three things the approved page is careful about are carried over
	 * deliberately, and none of them is reachable from an Elementor panel:
	 *
	 *   - a product whose source published no price is never given a cart. It
	 *     gets a quotation route instead, and no price appears anywhere on it;
	 *   - there is no delivery, returns or warranty row. The store has not
	 *     supplied those terms and a public product page must not imply a
	 *     policy that is not on record;
	 *   - the reviews tab says there are none rather than inventing a rating.
	 */

	/**
	 * The product being viewed, or null off a product page.
	 */
	private static function product() {
		global $product;
		if ( ! $product instanceof WC_Product && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( get_the_ID() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
		return $product instanceof WC_Product ? $product : null;
	}

	/**
	 * A product's department and subcategory, told apart.
	 *
	 * wp_get_object_terms() returns them in whatever order it likes, and the
	 * approved page names the department beside the brand and shows both in the
	 * breadcrumb, so "the first one" is not good enough.
	 *
	 * @param int $product_id Product id.
	 * @return array{0:?WP_Term,1:?WP_Term}
	 */
	private static function product_terms( int $product_id ): array {
		$terms = wp_get_object_terms( $product_id, 'product_cat' );
		$terms = is_wp_error( $terms ) ? array() : $terms;

		$dept = null;
		$sub  = null;
		foreach ( $terms as $term ) {
			if ( 0 === (int) $term->parent ) {
				$dept = $term;
			} else {
				$sub = $term;
			}
		}
		return array( $dept, $sub );
	}

	/**
	 * A product's feature bullets, as the importer recorded them.
	 *
	 * @param int $product_id Product id.
	 * @return array
	 */
	private static function product_features( int $product_id ): array {
		$features = json_decode( (string) get_post_meta( $product_id, '_star_electric_features', true ), true );
		return is_array( $features ) ? $features : array();
	}

	/**
	 * One specification value, as a customer can read it.
	 *
	 * Nearly every value is a string. The exception is a product sold in
	 * choices - a fan's face decor, a cable's conductor - where the source
	 * records the options as a list. Those were reaching the specification
	 * table through wp_json_encode(), so the page showed the shopper
	 * ["Urban Black","Grand Dark Wood"] instead of the two names. Joining them
	 * reads like every other row in the table.
	 *
	 * @param mixed $value The recorded value.
	 */
	private static function spec_value( $value ): string {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		if ( is_array( $value ) ) {
			$parts = array();
			foreach ( $value as $item ) {
				if ( is_scalar( $item ) && '' !== trim( (string) $item ) ) {
					$parts[] = trim( (string) $item );
				}
			}
			return implode( ', ', $parts );
		}

		return '';
	}

	/**
	 * The product page's breadcrumb.
	 *
	 * @param array $args home, shop.
	 */
	public static function product_head( array $args = array() ): void {
		$product = self::product();
		if ( ! $product ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'home' => __( 'Home', 'star-electric' ),
				'shop' => __( 'Shop', 'star-electric' ),
			)
		);

		list( $star_dept, $star_sub ) = self::product_terms( $product->get_id() );
		?>
		<div class="page-head">
			<div class="container" id="breadcrumb">
				<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'star-electric-child' ); ?>">
					<ol class="breadcrumb">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( (string) $args['home'] ); ?></a></li>
						<li class="sep" aria-hidden="true">/</li>
						<li><a href="<?php echo esc_url( self::url( 'shop' ) ); ?>"><?php echo esc_html( (string) $args['shop'] ); ?></a></li>
						<?php foreach ( array( $star_dept, $star_sub ) as $star_crumb ) : ?>
							<?php if ( $star_crumb instanceof WP_Term ) : ?>
								<li class="sep" aria-hidden="true">/</li>
								<li><a href="<?php echo esc_url( (string) get_term_link( $star_crumb ) ); ?>"><?php echo esc_html( $star_crumb->name ); ?></a></li>
							<?php endif; ?>
						<?php endforeach; ?>
						<li class="sep" aria-hidden="true">/</li>
						<li aria-current="page"><?php the_title(); ?></li>
					</ol>
				</nav>
			</div>
		</div>
		<?php
	}

	/**
	 * The description, specification, notes and reviews tabs.
	 *
	 * The reviews tab says there are none rather than inventing a rating, and
	 * the notes tab is not offered where the record carries none.
	 *
	 * @param array $args Wording only.
	 */
	public static function product_tabs( array $args = array() ): void {
		$product = self::product();
		if ( ! $product ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'tab_description' => __( 'Description', 'star-electric' ),
				'tab_specs'       => __( 'Specifications', 'star-electric' ),
				'tab_notes'       => __( 'Additional Information', 'star-electric' ),
				'tab_reviews'     => __( 'Reviews', 'star-electric' ),
				'key_points'      => __( 'Key points', 'star-electric' ),
				'no_description'  => __( 'This product’s source does not publish a description.', 'star-electric' ),
				'reviews_title'   => __( 'No reviews yet', 'star-electric' ),
				'reviews_text'    => __( 'No customer reviews have been published for this product.', 'star-electric' ),
			)
		);

		$star_id       = $product->get_id();
		$star_features = self::product_features( $star_id );
		$star_specs    = json_decode( (string) $product->get_meta( '_star_electric_specifications' ), true );
		$star_notes    = json_decode( (string) $product->get_meta( '_star_electric_notes' ), true );
		$star_specs    = is_array( $star_specs ) ? $star_specs : array();
		$star_notes    = is_array( $star_notes ) ? $star_notes : array();
		?>
		<section class="section section--sm">
			<div class="container">
				<div data-tabs>
					<div class="tabs" role="tablist" aria-label="<?php esc_attr_e( 'Product information', 'star-electric-child' ); ?>">
						<button role="tab" id="tabDesc" aria-controls="panelDesc" aria-selected="true"><?php echo esc_html( (string) $args['tab_description'] ); ?></button>
						<?php if ( $star_specs ) : ?>
							<button role="tab" id="tabSpecs" aria-controls="panelSpecs" aria-selected="false"><?php echo esc_html( (string) $args['tab_specs'] ); ?></button>
						<?php endif; ?>
						<?php if ( $star_notes ) : ?>
							<button role="tab" id="tabAdd" aria-controls="panelAdd" aria-selected="false"><?php echo esc_html( (string) $args['tab_notes'] ); ?></button>
						<?php endif; ?>
						<button role="tab" id="tabRev" aria-controls="panelRev" aria-selected="false"><?php echo esc_html( (string) $args['tab_reviews'] ); ?></button>
					</div>

					<div class="tab-panel" id="panelDesc" role="tabpanel" aria-labelledby="tabDesc">
						<div class="prose">
							<?php
							$star_desc = (string) $product->get_description();
							if ( '' !== trim( wp_strip_all_tags( $star_desc ) ) ) {
								echo wp_kses_post( $star_desc );
								if ( $star_features ) {
									echo '<h3>' . esc_html( (string) $args['key_points'] ) . '</h3><ul>';
									foreach ( $star_features as $star_feature ) {
										echo '<li>' . esc_html( (string) $star_feature ) . '</li>';
									}
									echo '</ul>';
								}
							} else {
								echo '<p>' . esc_html( (string) $args['no_description'] ) . '</p>';
							}
							?>
						</div>
					</div>

					<?php if ( $star_specs ) : ?>
						<div class="tab-panel" id="panelSpecs" role="tabpanel" aria-labelledby="tabSpecs" hidden>
							<table class="table table--specs">
								<caption class="sr-only"><?php esc_html_e( 'Product specifications', 'star-electric-child' ); ?></caption>
								<tbody>
									<?php foreach ( $star_specs as $star_key => $star_value ) : ?>
										<tr>
											<th scope="row"><?php echo esc_html( (string) $star_key ); ?></th>
											<td><?php echo esc_html( self::spec_value( $star_value ) ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php endif; ?>

					<?php if ( $star_notes ) : ?>
						<div class="tab-panel" id="panelAdd" role="tabpanel" aria-labelledby="tabAdd" hidden>
							<ul class="prose">
								<?php foreach ( $star_notes as $star_line ) : ?>
									<li><?php echo esc_html( (string) $star_line ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

					<div class="tab-panel" id="panelRev" role="tabpanel" aria-labelledby="tabRev" hidden>
						<div class="empty-state">
							<span class="empty-state__ico">
								<?php echo self::icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
							<h3><?php echo esc_html( (string) $args['reviews_title'] ); ?></h3>
							<p><?php echo esc_html( (string) $args['reviews_text'] ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Other products from the same department.
	 *
	 * @param array $args title, sub, link_label, count.
	 */
	public static function product_related( array $args = array() ): void {
		$product = self::product();
		if ( ! $product || ! class_exists( 'Star_Electric_Navigation' ) ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'title'      => __( 'Related Products', 'star-electric' ),
				'sub'        => __( 'Other items from the same department.', 'star-electric' ),
				'link_label' => __( 'Shop all', 'star-electric' ),
				'count'      => 4,
			)
		);

		$star_related = Star_Electric_Navigation::related( $product->get_id(), max( 1, (int) $args['count'] ) );
		if ( ! $star_related ) {
			return;
		}
		?>
		<section class="section section--tint" aria-labelledby="relTitle">
			<div class="container">
				<div class="section__head">
					<div>
						<h2 class="section__title" id="relTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
						<p class="section__sub"><?php echo esc_html( (string) $args['sub'] ); ?></p>
					</div>
					<a class="link-more" href="<?php echo esc_url( self::url( 'shop' ) ); ?>">
						<?php echo esc_html( (string) $args['link_label'] ); ?>
						<?php echo self::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>
				<ul class="product-grid">
					<?php
					$star_keep = $product;
					foreach ( $star_related as $star_rel_id ) {
						$star_rel = wc_get_product( $star_rel_id );
						if ( ! $star_rel instanceof WC_Product ) {
							continue;
						}
						$GLOBALS['post']    = get_post( $star_rel_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						$GLOBALS['product'] = $star_rel; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						setup_postdata( $GLOBALS['post'] );
						wc_get_template_part( 'content', 'product' );
					}
					wp_reset_postdata();
					$GLOBALS['product'] = $star_keep; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
					?>
				</ul>
			</div>
		</section>
		<?php
	}

	/**
	 * The visitor's own browsing history.
	 *
	 * The list is kept in the browser's sessionStorage exactly as the approved
	 * page keeps it, so nothing about who looked at what is stored on the
	 * server. The cards are rendered by WordPress from the ids the browser
	 * sends back, which keeps the price, quote and availability rules in one
	 * place.
	 *
	 * @param array $args title, sub.
	 */
	public static function product_recent( array $args = array() ): void {
		$product = self::product();
		if ( ! $product ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'title' => __( 'Recently Viewed', 'star-electric' ),
				'sub'   => __( 'Products you looked at in this browsing session.', 'star-electric' ),
			)
		);
		?>
		<section class="section" id="recentSection" aria-labelledby="recTitle" hidden>
			<div class="container">
				<div class="section__head">
					<div>
						<h2 class="section__title" id="recTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
						<p class="section__sub"><?php echo esc_html( (string) $args['sub'] ); ?></p>
					</div>
				</div>
				<ul class="product-grid" id="recentGrid" data-current="<?php echo esc_attr( (string) $product->get_id() ); ?>"></ul>
			</div>
		</section>
		<?php
	}
	/* --------------------------------------------------------------------- *
	 * The shop's own pages
	 * --------------------------------------------------------------------- */

	/**
	 * A WooCommerce page inside the approved page shell.
	 *
	 * The cart, the checkout and the account area are WooCommerce's, and they
	 * stay WooCommerce's: this renders the same shortcode the page held before
	 * it moved into Elementor, inside the same section and container the
	 * approved page wrapped it in. Nothing here touches an order, a total, a
	 * price or a customer record.
	 *
	 * @param array $args shortcode, layout.
	 */
	public static function woo_panel( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'shortcode' => 'woocommerce_cart',
				'layout'    => 'cart-layout',
			)
		);

		$allowed = array( 'woocommerce_cart', 'woocommerce_checkout', 'woocommerce_my_account' );
		$code    = in_array( $args['shortcode'], $allowed, true ) ? $args['shortcode'] : 'woocommerce_cart';
		$layout  = trim( (string) $args['layout'] );
		?>
		<section class="section section--sm">
			<div class="container<?php echo '' !== $layout ? ' ' . esc_attr( $layout ) : ''; ?>">
				<?php echo do_shortcode( '[' . $code . ']' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</section>
		<?php
	}

	/**
	 * The wishlist panel, its empty state and its two failure messages.
	 *
	 * Saved products live in the visitor's own browser, not on the server, so
	 * the table is filled in by script from ids the browser holds. A failed
	 * lookup must never be shown as an empty wishlist - a shopper would read
	 * that as their saved items having been thrown away - which is why the
	 * error and the empty state are two different things.
	 *
	 * @param array $args Wording only.
	 */
	public static function wishlist( array $args = array() ): void {
		$args = wp_parse_args(
			$args,
			array(
				'col_product'  => __( 'Product', 'star-electric' ),
				'col_price'    => __( 'Price', 'star-electric' ),
				'col_stock'    => __( 'Stock', 'star-electric' ),
				'col_actions'  => __( 'Actions', 'star-electric' ),
				'empty_title'  => __( 'Your wishlist is empty', 'star-electric' ),
				'empty_text'   => __( 'Use the heart icon on any product to save it here for later. Saved items are handy when you are pricing up a job over several visits.', 'star-electric' ),
				'shop_label'   => __( 'Browse the Shop', 'star-electric' ),
				'deals_label'  => __( 'See Current Deals', 'star-electric' ),
				'error_text'   => __( 'Your saved products could not be loaded just now. They are still saved in this browser — reload the page to try again.', 'star-electric' ),
				'noscript'     => __( 'The wishlist needs JavaScript, because saved products are stored in your browser rather than on this website.', 'star-electric' ),
			)
		);
		?>
		<section class="section section--sm">
			<div class="container">

				<div class="cart-panel" id="wishlistPanel" hidden>
					<table class="table table--stack">
						<caption class="sr-only"><?php esc_attr_e( 'Saved products', 'star-electric-child' ); ?></caption>
						<thead>
							<tr>
								<th scope="col"><?php echo esc_html( (string) $args['col_product'] ); ?></th>
								<th scope="col"><?php echo esc_html( (string) $args['col_price'] ); ?></th>
								<th scope="col"><?php echo esc_html( (string) $args['col_stock'] ); ?></th>
								<th scope="col"><?php echo esc_html( (string) $args['col_actions'] ); ?></th>
							</tr>
						</thead>
						<tbody id="wishlistBody"></tbody>
					</table>
				</div>

				<div id="wishlistEmpty" hidden>
					<div class="empty-state">
						<span class="empty-state__ico">
							<?php echo self::icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<h2><?php echo esc_html( (string) $args['empty_title'] ); ?></h2>
						<p><?php echo esc_html( (string) $args['empty_text'] ); ?></p>
						<div class="btn-row">
							<a class="btn btn--accent" href="<?php echo esc_url( self::url( 'shop' ) ); ?>"><?php echo esc_html( (string) $args['shop_label'] ); ?></a>
							<a class="btn btn--ghost" href="<?php echo esc_url( self::url( 'deals' ) ); ?>"><?php echo esc_html( (string) $args['deals_label'] ); ?></a>
						</div>
					</div>
				</div>

				<?php
				/*
				 * A failed lookup must never be shown as an empty wishlist: a
				 * shopper would read that as their saved items having been
				 * thrown away.
				 */
				?>
				<div id="wishlistError" hidden>
					<div class="alert alert--info">
						<?php echo self::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( (string) $args['error_text'] ); ?></span>
					</div>
				</div>

				<noscript>
					<div class="alert alert--info" style="margin-top:16px">
						<?php echo self::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( (string) $args['noscript'] ); ?></span>
					</div>
				</noscript>

			</div>
		</section>
		<?php
	}
	/**
	 * The gallery: the thumbnail rail, the main photograph and its badges.
	 *
	 * A product with no photograph says so rather than showing a stand-in.
	 *
	 * @param array $args quote_tag, no_image.
	 */
	public static function product_gallery( array $args = array() ): void {
		$product = self::product();
		if ( ! $product ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'quote_tag' => __( 'Request Quote', 'star-electric' ),
				'no_image'  => __( 'Product image unavailable — the approved source for this product does not publish a photograph.', 'star-electric' ),
			)
		);

		$star_quote = self::is_quote( $product );
		$star_off   = self::product_discount( $product );

		$star_gallery = array_values( array_filter( array_merge(
			array( (int) $product->get_image_id() ),
			$product->get_gallery_image_ids()
		) ) );

		$star_note       = (string) $product->get_meta( '_star_electric_image_note' );
		$star_image_type = (string) $product->get_meta( '_star_electric_image_type' );
		?>
		<div class="gallery">
			<?php
			/*
			 * The thumbnail rail is rendered whenever there is an image at all,
			 * not only when there are two. .gallery is a two-column grid, so
			 * with the rail missing the photograph dropped into the narrow
			 * thumbnail column and rendered at 78px wide.
			 */
			?>
			<?php if ( $star_gallery ) : ?>
				<div class="gallery__thumbs" id="galleryThumbs" role="tablist" aria-label="<?php esc_attr_e( 'Product images', 'star-electric-child' ); ?>">
					<?php foreach ( $star_gallery as $star_i => $star_att ) : ?>
						<button type="button" role="tab"
							aria-selected="<?php echo esc_attr( 0 === $star_i ? 'true' : 'false' ); ?>"
							class="<?php echo esc_attr( 0 === $star_i ? 'is-active' : '' ); ?>"
							data-full="<?php echo esc_url( (string) wp_get_attachment_image_url( $star_att, 'large' ) ); ?>">
							<?php echo wp_kses_post( wp_get_attachment_image( $star_att, 'woocommerce_gallery_thumbnail', false, array( 'alt' => '' ) ) ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="gallery__main">
				<div class="gallery__badges" id="galleryBadges">
					<?php if ( $star_off > 0 ) : ?>
						<span class="tag tag--sale">-<?php echo esc_html( (string) $star_off ); ?>%</span>
					<?php endif; ?>
					<?php if ( $star_quote ) : ?>
						<span class="tag tag--quote"><?php echo esc_html( (string) $args['quote_tag'] ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( $star_gallery ) : ?>
					<img id="galleryMain"
						src="<?php echo esc_url( (string) wp_get_attachment_image_url( $star_gallery[0], 'large' ) ); ?>"
						alt="<?php echo esc_attr( $product->get_name() ); ?>"
						width="1200" height="900" fetchpriority="high" decoding="async">
				<?php else : ?>
					<p class="gallery__noimage">
						<?php echo esc_html( (string) $args['no_image'] ); ?>
					</p>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $star_note && 'exact-image' !== $star_image_type ) : ?>
				<p class="gallery__imgnote"><?php echo esc_html( $star_note ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * The brand, the title, the SKU row, the price and the short description.
	 *
	 * The price block is Star_Electric_Quote_Only's: a product whose source
	 * published no price shows a quotation route and never a number.
	 *
	 * @param array $args in_label, no_ratings, sku_label, sku_missing.
	 */
	public static function product_info( array $args = array() ): void {
		$product = self::product();
		if ( ! $product ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'in_label'    => __( 'in', 'star-electric' ),
				'no_ratings'  => __( 'No customer ratings published', 'star-electric' ),
				'sku_label'   => __( 'SKU:', 'star-electric' ),
				'sku_missing' => __( 'Not specified', 'star-electric' ),
			)
		);

		$star_id    = $product->get_id();
		$star_quote = self::is_quote( $product );

		$star_brands = wp_get_object_terms( $star_id, 'star_brand', array( 'fields' => 'names' ) );
		$star_brand  = ( ! is_wp_error( $star_brands ) && $star_brands ) ? $star_brands[0] : '';

		list( $star_dept, $star_sub ) = self::product_terms( $star_id );
		$star_cat  = $star_dept ?? $star_sub;
		$star_pill = class_exists( 'Star_Electric_Shell' )
			? Star_Electric_Shell::availability_pill( $product )
			: array( '', '' );

		$star_features = self::product_features( $star_id );
		?>
		<p class="pdp__brandrow">
			<span class="pdp__brand"><?php echo esc_html( $star_brand ); ?></span>
			<?php if ( $star_cat instanceof WP_Term ) : ?>
				<span class="t-xs t-faint"><?php echo esc_html( (string) $args['in_label'] ); ?> <?php echo esc_html( $star_cat->name ); ?></span>
			<?php endif; ?>
		</p>

		<h1 class="pdp__title"><?php the_title(); ?></h1>

		<div class="pdp__ratingrow">
			<span class="rating">
				<span class="t-xs t-faint"><?php echo esc_html( (string) $args['no_ratings'] ); ?></span>
			</span>
			<span>
				<?php echo esc_html( (string) $args['sku_label'] ); ?>
				<strong>
					<?php
					$star_sku = (string) $product->get_sku();
					echo esc_html( '' !== $star_sku ? $star_sku : (string) $args['sku_missing'] );
					?>
				</strong>
			</span>
		</div>

		<div class="pdp__pricebox">
			<p class="price price--lg<?php echo esc_attr( $star_quote ? ' price--quote' : '' ); ?>">
				<?php echo wp_kses_post( Star_Electric_Quote_Only::price_block( $product, 'lg' ) ); ?>
			</p>
			<p style="margin-top:12px">
				<span class="pill <?php echo esc_attr( $star_pill[0] ); ?>"><?php echo esc_html( $star_pill[1] ); ?></span>
			</p>
		</div>

		<?php if ( '' !== $product->get_short_description() || $star_features ) : ?>
			<div class="pdp__short">
				<?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?>
				<?php if ( $star_features ) : ?>
					<ul id="pdpBullets">
						<?php foreach ( $star_features as $star_feature ) : ?>
							<li><?php echo esc_html( (string) $star_feature ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * The variants, the buying controls and the row of onward links.
	 *
	 * Whether a product can be bought at all is the catalogue's decision, not
	 * this renderer's: a product whose source published no price gets a
	 * quotation route and no cart, an out-of-stock product gets neither, and a
	 * variable product gets WooCommerce's own variation form.
	 *
	 * @param array $args Wording only.
	 */
	public static function product_purchase( array $args = array() ): void {
		$product = self::product();
		if ( ! $product ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'quote_button' => __( 'Request a Quote', 'star-electric' ),
				'quote_hint'   => __( 'This product’s source does not publish a price, so it is quoted on enquiry.', 'star-electric' ),
				'qty_label'    => __( 'Quantity', 'star-electric' ),
				'add_label'    => __( 'Add to Cart', 'star-electric' ),
				'buy_label'    => __( 'Buy Now', 'star-electric' ),
				'oos_label'    => __( 'Out of Stock', 'star-electric' ),
				'ask_label'    => __( 'Ask about this product', 'star-electric' ),
				'wish_label'   => __( 'Add to Wishlist', 'star-electric' ),
				'bulk_label'   => __( 'Request Bulk Quote', 'star-electric' ),
			)
		);

		$star_id    = $product->get_id();
		$star_quote = self::is_quote( $product );
		?>
		<?php
		/*
		 * A variable product with no price still has real variants, and the
		 * approved page shows them so an enquiry can name one. They are
		 * swatches rather than a WooCommerce variation form because nothing
		 * here is purchasable: there is no price to change.
		 */
		if ( $star_quote && $product->is_type( 'variable' ) ) :
			$star_attrs = $product->get_variation_attributes();
			if ( $star_attrs ) :
				$star_axis   = array_key_first( $star_attrs );
				$star_values = array_values( (array) $star_attrs[ $star_axis ] );
				?>
				<div class="variations" id="pdpVariations">
					<div class="variation">
						<p class="variation__label">
							<?php echo esc_html( wc_attribute_label( (string) $star_axis, $product ) ); ?>:
							<span id="varChosen"><?php echo esc_html( (string) $star_values[0] ); ?></span>
						</p>
						<div class="swatches">
							<?php foreach ( $star_values as $star_i => $star_value ) : ?>
								<button class="swatch" type="button"
									aria-pressed="<?php echo esc_attr( 0 === $star_i ? 'true' : 'false' ); ?>"
									data-val="<?php echo esc_attr( (string) $star_value ); ?>">
									<?php echo esc_html( (string) $star_value ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<?php
			endif;
		endif;
		?>

		<?php if ( $star_quote ) : ?>

			<div class="pdp__buy">
				<?php // No icon here: the approved product page's quote button carries none. ?>
				<a class="btn btn--accent btn--lg" href="<?php echo esc_url( Star_Electric_Shell::quote_url( $star_id ) ); ?>">
					<?php echo esc_html( (string) $args['quote_button'] ); ?>
				</a>
			</div>
			<p class="field__hint" style="margin-bottom:24px">
				<?php echo esc_html( (string) $args['quote_hint'] ); ?>
			</p>

		<?php elseif ( $product->is_type( 'variable' ) ) : ?>

			<div class="variations" id="pdpVariations">
				<?php woocommerce_variable_add_to_cart(); ?>
			</div>

		<?php elseif ( $product->is_in_stock() ) : ?>

			<form class="cart" method="post" enctype="multipart/form-data"
				action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>">
				<div class="field" style="max-width:170px;margin-bottom:20px">
					<label class="field__label" for="pdpQty"><?php echo esc_html( (string) $args['qty_label'] ); ?></label>
					<div class="qty">
						<button type="button" data-qty="down" aria-label="<?php esc_attr_e( 'Decrease quantity', 'star-electric-child' ); ?>">
							<?php echo self::icon( 'minus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
						<input id="pdpQty" name="quantity" type="number" min="1" value="1" aria-label="<?php echo esc_attr( (string) $args['qty_label'] ); ?>">
						<button type="button" data-qty="up" aria-label="<?php esc_attr_e( 'Increase quantity', 'star-electric-child' ); ?>">
							<?php echo self::icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					</div>
				</div>

				<div class="pdp__buy">
					<button class="btn btn--accent btn--lg" type="submit" name="add-to-cart" value="<?php echo esc_attr( (string) $star_id ); ?>">
						<?php echo self::icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo esc_html( (string) $args['add_label'] ); ?>
					</button>
					<a class="btn btn--primary btn--lg" href="<?php echo esc_url( add_query_arg( 'add-to-cart', $star_id, self::url( 'checkout' ) ) ); ?>">
						<?php echo esc_html( (string) $args['buy_label'] ); ?>
					</a>
				</div>
			</form>

		<?php else : ?>

			<div class="pdp__buy">
				<button class="btn btn--ghost btn--lg" type="button" disabled>
					<?php echo esc_html( (string) $args['oos_label'] ); ?>
				</button>
				<a class="btn btn--accent btn--lg" href="<?php echo esc_url( Star_Electric_Shell::quote_url( $star_id ) ); ?>">
					<?php echo esc_html( (string) $args['quote_button'] ); ?>
				</a>
			</div>

		<?php endif; ?>

		<div class="btn-row" style="margin-bottom:24px">
			<a class="btn btn--ghost" href="<?php echo esc_url( self::url( 'contact' ) ); ?>">
				<?php echo self::icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( (string) $args['ask_label'] ); ?>
			</a>
			<button class="btn btn--ghost js-wish" type="button" data-id="<?php echo esc_attr( (string) $star_id ); ?>">
				<?php echo self::icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( (string) $args['wish_label'] ); ?>
			</button>
			<a class="btn btn--ghost" href="<?php echo esc_url( self::url( 'quote' ) ); ?>">
				<?php echo esc_html( (string) $args['bulk_label'] ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Where the product record came from, and which department it sits in.
	 *
	 * Delivery, Returns and Warranty rows are deliberately absent, exactly as
	 * they are on the approved page. The store has not supplied those terms,
	 * and a public product page must not state or hint at a policy not on
	 * record.
	 *
	 * @param array $args Wording only.
	 */
	public static function product_record( array $args = array() ): void {
		$product = self::product();
		if ( ! $product ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'model_label'   => __( 'Model', 'star-electric' ),
				'source_label'  => __( 'Source', 'star-electric' ),
				'checked_label' => __( 'Reference checked', 'star-electric' ),
				'series_label'  => __( 'Series', 'star-electric' ),
				'cat_label'     => __( 'Category', 'star-electric' ),
			)
		);

		list( $star_dept, ) = self::product_terms( $product->get_id() );

		$star_domain  = (string) $product->get_meta( '_star_electric_source_domain' );
		$star_url     = (string) $product->get_meta( '_star_electric_source_url' );
		$star_model   = (string) $product->get_meta( '_star_electric_model' );
		$star_series  = (string) $product->get_meta( '_star_electric_series' );
		$star_checked = (string) $product->get_meta( '_star_electric_source_checked' );
		?>
		<dl class="pdp__meta">
			<?php if ( '' !== $star_model ) : ?>
				<div><dt><?php echo esc_html( (string) $args['model_label'] ); ?></dt><dd><?php echo esc_html( $star_model ); ?></dd></div>
			<?php endif; ?>
			<?php if ( '' !== $star_domain ) : ?>
				<div>
					<dt><?php echo esc_html( (string) $args['source_label'] ); ?></dt>
					<dd>
						<?php if ( '' !== $star_url ) : ?>
							<a class="link-inline" href="<?php echo esc_url( $star_url ); ?>" target="_blank" rel="noopener nofollow"><?php echo esc_html( $star_domain ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $star_domain ); ?>
						<?php endif; ?>
					</dd>
				</div>
			<?php endif; ?>
			<?php if ( '' !== $star_checked ) : ?>
				<div><dt><?php echo esc_html( (string) $args['checked_label'] ); ?></dt><dd><?php echo esc_html( $star_checked ); ?></dd></div>
			<?php endif; ?>
			<?php if ( '' !== $star_series ) : ?>
				<div><dt><?php echo esc_html( (string) $args['series_label'] ); ?></dt><dd><?php echo esc_html( $star_series ); ?></dd></div>
			<?php endif; ?>
		</dl>

		<?php
		/*
		 * Delivery, Returns and Warranty rows are deliberately absent, exactly
		 * as they are on the approved page.
		 */
		?>
		<dl class="pdp__meta">
			<?php if ( $star_dept instanceof WP_Term ) : ?>
				<div>
					<dt><?php echo esc_html( (string) $args['cat_label'] ); ?></dt>
					<dd><a class="link-inline" href="<?php echo esc_url( (string) get_term_link( $star_dept ) ); ?>"><?php echo esc_html( $star_dept->name ); ?></a></dd>
				</div>
			<?php endif; ?>
		</dl>
		<?php
	}

	/**
	 * Whether a product is quoted on enquiry rather than sold at a price.
	 *
	 * The decision belongs to Star_Electric_Quote_Only; this only asks.
	 *
	 * @param WC_Product $product Product.
	 */
	private static function is_quote( WC_Product $product ): bool {
		return class_exists( 'Star_Electric_Quote_Only' )
			? Star_Electric_Quote_Only::is_quote( $product )
			: ! $product->is_purchasable();
	}

	/**
	 * A product's reduction, as a whole percentage, or zero.
	 *
	 * A product quoted on enquiry can never have one.
	 *
	 * @param WC_Product $product Product.
	 */
	private static function product_discount( WC_Product $product ): int {
		if ( self::is_quote( $product ) ) {
			return 0;
		}
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_price();
		if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
			return (int) round( ( 1 - ( $sale / $regular ) ) * 100 );
		}
		return 0;
	}

	/**
	 * The whole product detail block, for a product with no document of its own.
	 *
	 * A seeded product draws these four as separate Elementor widgets inside a
	 * container that carries the section and the grid. This is the same markup,
	 * for a product that has not been seeded yet.
	 */
	public static function product_detail(): void {
		?>
		<section class="section section--sm">
			<div class="container pdp">
				<?php self::product_gallery(); ?>
				<div>
					<?php self::product_info(); ?>
					<?php self::product_purchase(); ?>
					<?php self::product_record(); ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * The product's own Elementor document, or the approved default layout.
	 *
	 * This is the whole job of the Single Product shell. Every product owns its
	 * own Elementor document, so what a product page looks like is that
	 * product's business and editing one cannot touch another. A product that
	 * has not been given a document yet - a brand new import, a seeding run
	 * that has not reached it - falls back to the approved default rather than
	 * showing nothing.
	 */
	public static function product_content(): void {
		$product = self::product();
		if ( ! $product ) {
			return;
		}

		$id = $product->get_id();

		if ( class_exists( '\Elementor\Plugin' )
			&& 'builder' === get_post_meta( $id, '_elementor_edit_mode', true ) ) {
			/*
			 * get_builder_content(), not get_builder_content_for_display():
			 * the latter refuses to render a post inside itself, which is a
			 * sensible guard for a Template widget pointing at some other
			 * document and exactly the wrong one here. Drawing the product's
			 * own layout on the product's own page is the whole point.
			 */
			$content = \Elementor\Plugin::$instance->frontend->get_builder_content( $id );
			if ( '' !== trim( (string) $content ) ) {
				echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return;
			}
		}

		self::product_head();
		self::product_detail();
		self::product_tabs();
		self::product_related();
		self::product_recent();
	}
}
