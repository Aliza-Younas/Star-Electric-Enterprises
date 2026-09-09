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
}
