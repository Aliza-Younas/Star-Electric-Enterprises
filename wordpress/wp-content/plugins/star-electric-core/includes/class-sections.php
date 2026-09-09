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
				'eyebrow'    => '',
				'title'      => __( 'Shop by Brand', 'star-electric' ),
				'sub'        => '',
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
				'steps'          => self::default_bulk_steps(),
			)
		);
		?>
		<section class="section bulk" aria-labelledby="bulkTitle">
			<div class="container bulk__inner">
				<div>
					<h2 class="bulk__title" id="bulkTitle"><?php echo esc_html( (string) $args['title'] ); ?></h2>
					<p class="bulk__text"><?php echo esc_html( (string) $args['text'] ); ?></p>
					<div class="btn-row">
						<a class="btn btn--accent btn--lg" href="<?php echo esc_url( (string) $args['primary_url'] ); ?>"><?php echo esc_html( (string) $args['primary_label'] ); ?></a>
						<?php if ( '' !== (string) $args['second_label'] ) : ?>
							<a class="btn btn--light btn--lg" href="<?php echo esc_url( (string) $args['second_url'] ); ?>">
								<?php echo self::icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
}
