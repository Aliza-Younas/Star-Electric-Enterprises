<?php
/**
 * The global header and footer, as renderers.
 *
 * Moved out of the child theme's header.php and footer.php so that Elementor's
 * Theme Builder and the theme's own templates render the same markup. The
 * theme still owns the document itself - doctype, <head>, wp_head(), the body
 * tag - because those are not part of a header template and Elementor does not
 * output them.
 *
 * The structure is the approved storefront's, class for class: the stylesheets
 * this site ships are that storefront's own files. What an editor controls is
 * the wording, the links and which departments appear.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Header and footer renderers.
 */
class Star_Electric_Chrome {

	/**
	 * An icon from the shell.
	 *
	 * @param string $name Icon name.
	 * @param string $cls  Extra class.
	 */
	private static function icon( string $name, string $cls = '' ): string {
		if ( ! class_exists( 'Star_Electric_Shell' ) ) {
			return '';
		}
		return Star_Electric_Shell::icon( $name, $cls );
	}

	/**
	 * A shell URL.
	 *
	 * @param string $key Page key.
	 */
	private static function url( string $key ): string {
		return class_exists( 'Star_Electric_Shell' ) ? Star_Electric_Shell::url( $key ) : home_url( '/' );
	}

	/**
	 * True when the child theme's shell helper is present.
	 */
	public static function available(): bool {
		return class_exists( 'Star_Electric_Shell' );
	}

	/* --------------------------------------------------------------------- *
	 * Header
	 * --------------------------------------------------------------------- */

	/**
	 * The approved topbar links.
	 *
	 * @return array<int,array<string,string>>
	 */
	public static function default_topbar_links(): array {
		return array(
			array( 'icon' => 'mail', 'label' => __( 'Contact the store', 'star-electric' ), 'url' => self::url( 'contact' ) ),
			array( 'icon' => 'doc', 'label' => __( 'Request a quotation', 'star-electric' ), 'url' => self::url( 'quote' ) ),
		);
	}

	/**
	 * Everything from the topbar down to the mobile drawer.
	 *
	 * @param array $args Header arguments.
	 */
	public static function site_header( array $args = array() ): void {
		if ( ! self::available() ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'location'      => __( 'Saddar, Rawalpindi', 'star-electric' ),
				'topbar_links'  => self::default_topbar_links(),
				'search_all'    => __( 'All Categories', 'star-electric' ),
				'search_place'  => __( 'Search cables, switches, breakers, lighting, fans and more...', 'star-electric' ),
				'search_button' => __( 'Search', 'star-electric' ),
				'account_small' => __( 'Customer', 'star-electric' ),
				'account_label' => __( 'Account', 'star-electric' ),
				'wish_small'    => __( 'Saved', 'star-electric' ),
				'wish_label'    => __( 'Wishlist', 'star-electric' ),
				'cart_small'    => __( 'Cart', 'star-electric' ),
				'quote_label'   => __( 'Request a Quote', 'star-electric' ),
				'quote_url'     => self::url( 'quote' ),
				'mega_note'     => __( 'Every product listed is an individual item published by one of our approved supplier sources.', 'star-electric' ),
				'mega_link'     => __( 'Browse the full shop', 'star-electric' ),
				'drawer_title'  => __( 'Menu', 'star-electric' ),
				'drawer_cats'   => __( 'Shop by category', 'star-electric' ),
				'drawer_help'   => __( 'Customer service', 'star-electric' ),
			)
		);

		$active = Star_Electric_Shell::active();
		$nav    = Star_Electric_Shell::nav();
		$tree   = Star_Electric_Shell::category_tree();
		?>
		<div id="siteHeader">

			<div class="topbar">
				<div class="container topbar__inner">
					<p class="topbar__brand">
						<strong><?php bloginfo( 'name' ); ?></strong>
						<span class="topbar__dot" aria-hidden="true">&bull;</span>
						<span class="topbar__loc">
							<?php echo self::icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php echo esc_html( (string) $args['location'] ); ?>
						</span>
					</p>
					<ul class="topbar__links">
						<?php foreach ( (array) $args['topbar_links'] as $link ) : ?>
							<li>
								<a href="<?php echo esc_url( (string) $link['url'] ); ?>">
									<?php echo self::icon( (string) ( $link['icon'] ?? 'mail' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php echo esc_html( (string) $link['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>

			<header class="header" id="siteHeaderBar">
				<div class="container header__inner">
					<button class="icon-btn header__burger" id="navToggle" type="button"
						aria-label="<?php esc_attr_e( 'Open menu', 'star-electric' ); ?>"
						aria-expanded="false" aria-controls="mobileNav">
						<?php echo self::icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>

					<?php echo Star_Electric_Shell::logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

					<form class="search" role="search" id="searchForm" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
						<label class="sr-only" for="searchCat"><?php esc_html_e( 'Search within category', 'star-electric' ); ?></label>
						<div class="search__catwrap">
							<select class="search__cat" id="searchCat" name="product_cat">
								<option value=""><?php echo esc_html( (string) $args['search_all'] ); ?></option>
								<?php foreach ( $tree as $branch ) : ?>
									<option value="<?php echo esc_attr( $branch['term']->slug ); ?>">
										<?php echo esc_html( $branch['term']->name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<?php echo self::icon( 'chevdown', 'search__caret' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<label class="sr-only" for="searchInput"><?php esc_html_e( 'Search products', 'star-electric' ); ?></label>
						<input class="search__input" id="searchInput" name="s" type="search" autocomplete="off"
							value="<?php echo esc_attr( get_search_query() ); ?>"
							placeholder="<?php echo esc_attr( (string) $args['search_place'] ); ?>">
						<input type="hidden" name="post_type" value="product">
						<button class="search__btn" type="submit">
							<?php echo self::icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( (string) $args['search_button'] ); ?></span>
						</button>
						<div class="search__suggest" id="searchSuggest" hidden></div>
					</form>

					<div class="header__actions">
						<a class="action" href="<?php echo esc_url( self::url( 'account' ) ); ?>">
							<span class="action__ico"><?php echo self::icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="action__txt">
								<small><?php echo esc_html( (string) $args['account_small'] ); ?></small>
								<strong><?php echo esc_html( (string) $args['account_label'] ); ?></strong>
							</span>
						</a>
						<a class="action" href="<?php echo esc_url( self::url( 'wishlist' ) ); ?>">
							<span class="action__ico">
								<?php echo self::icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="badge" data-count="wishlist">0</span>
							</span>
							<span class="action__txt">
								<small><?php echo esc_html( (string) $args['wish_small'] ); ?></small>
								<strong><?php echo esc_html( (string) $args['wish_label'] ); ?></strong>
							</span>
						</a>
						<a class="action" href="<?php echo esc_url( self::url( 'cart' ) ); ?>">
							<span class="action__ico">
								<?php echo self::icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="badge" data-count="cart"><?php echo esc_html( (string) star_electric_child_cart_count() ); ?></span>
							</span>
							<span class="action__txt">
								<small><?php echo esc_html( (string) $args['cart_small'] ); ?></small>
								<strong data-cart-total><?php echo wp_kses_post( star_electric_child_cart_total() ); ?></strong>
							</span>
						</a>
						<a class="btn btn--accent header__quote" href="<?php echo esc_url( (string) $args['quote_url'] ); ?>">
							<?php echo esc_html( (string) $args['quote_label'] ); ?>
						</a>
					</div>
				</div>
			</header>

			<nav class="nav" aria-label="<?php esc_attr_e( 'Primary', 'star-electric' ); ?>">
				<div class="container nav__inner">
					<ul class="nav__list">
						<?php foreach ( $nav as $item ) : ?>
							<?php $is_active = ( $item['key'] === $active ) ? ' is-active' : ''; ?>
							<?php if ( ! empty( $item['mega'] ) ) : ?>
								<li class="has-mega">
									<a class="nav__link<?php echo esc_attr( $is_active ); ?>"
										href="<?php echo esc_url( $item['url'] ); ?>"
										aria-haspopup="true" aria-expanded="false">
										<?php echo esc_html( $item['label'] ); ?>
										<?php echo self::icon( 'chevdown' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
									<div class="mega">
										<div class="mega__grid">
											<?php foreach ( $tree as $branch ) : ?>
												<div class="mega__col">
													<a class="mega__title" href="<?php echo esc_url( (string) get_term_link( $branch['term'] ) ); ?>">
														<?php echo self::icon( 'chevright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
														<?php echo esc_html( $branch['term']->name ); ?>
													</a>
													<div class="mega__links">
														<?php foreach ( $branch['children'] as $child ) : ?>
															<a href="<?php echo esc_url( (string) get_term_link( $child ) ); ?>">
																<?php echo esc_html( $child->name ); ?>
															</a>
														<?php endforeach; ?>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
										<div class="mega__foot">
											<span><?php echo esc_html( (string) $args['mega_note'] ); ?></span>
											<a class="link-more" href="<?php echo esc_url( self::url( 'shop' ) ); ?>">
												<?php echo esc_html( (string) $args['mega_link'] ); ?>
												<?php echo self::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</a>
										</div>
									</div>
								</li>
							<?php else : ?>
								<li>
									<a class="nav__link<?php echo esc_attr( $is_active ); ?>" href="<?php echo esc_url( $item['url'] ); ?>">
										<?php echo esc_html( $item['label'] ); ?>
									</a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			</nav>

		</div>

		<div class="drawer" id="mobileNav" hidden>
			<div class="drawer__scrim" data-drawer-close></div>
			<div class="drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'star-electric' ); ?>">
				<div class="drawer__head">
					<span class="drawer__title"><?php echo esc_html( (string) $args['drawer_title'] ); ?></span>
					<button class="icon-btn" type="button" data-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'star-electric' ); ?>">
						<?php echo self::icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
				<div class="drawer__body">
					<ul class="drawer__nav">
						<?php foreach ( $nav as $item ) : ?>
							<li>
								<a href="<?php echo esc_url( $item['url'] ); ?>"
									class="<?php echo esc_attr( $item['key'] === $active ? 'is-active' : '' ); ?>">
									<?php echo esc_html( $item['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<p class="drawer__sep"><?php echo esc_html( (string) $args['drawer_cats'] ); ?></p>
					<ul class="drawer__nav">
						<?php foreach ( $tree as $branch ) : ?>
							<li>
								<a href="<?php echo esc_url( (string) get_term_link( $branch['term'] ) ); ?>">
									<?php echo esc_html( $branch['term']->name ); ?>
									<?php echo self::icon( 'chevright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<p class="drawer__sep"><?php echo esc_html( (string) $args['drawer_help'] ); ?></p>
					<ul class="drawer__nav">
						<li><a href="<?php echo esc_url( self::url( 'track-order' ) ); ?>"><?php esc_html_e( 'Track Order', 'star-electric' ); ?></a></li>
						<li><a href="<?php echo esc_url( self::url( 'faq' ) ); ?>"><?php esc_html_e( 'FAQs', 'star-electric' ); ?></a></li>
						<li><a href="<?php echo esc_url( self::url( 'complaint' ) ); ?>"><?php esc_html_e( 'Submit a Complaint', 'star-electric' ); ?></a></li>
					</ul>
				</div>
				<div class="drawer__foot">
					<a class="btn btn--accent btn--block" href="<?php echo esc_url( (string) $args['quote_url'] ); ?>">
						<?php echo esc_html( (string) $args['quote_label'] ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}

	/* --------------------------------------------------------------------- *
	 * Footer
	 * --------------------------------------------------------------------- */

	/**
	 * The approved footer link columns.
	 *
	 * The Shop column leads with the first six departments, which come from the
	 * taxonomy rather than being typed out, so it cannot list a department the
	 * catalogue no longer has.
	 *
	 * @return array<string,array<int,array<string,string>>>
	 */
	public static function default_footer_links(): array {
		return array(
			'shop'     => array(
				array( 'label' => __( 'Brands', 'star-electric' ), 'url' => self::url( 'brands' ) ),
				array( 'label' => __( 'Deals', 'star-electric' ), 'url' => self::url( 'deals' ) ),
			),
			'service'  => array(
				array( 'label' => __( 'Contact Us', 'star-electric' ), 'url' => self::url( 'contact' ) ),
				array( 'label' => __( 'FAQs', 'star-electric' ), 'url' => self::url( 'faq' ) ),
				array( 'label' => __( 'Track Order', 'star-electric' ), 'url' => self::url( 'track-order' ) ),
				array( 'label' => __( 'Returns', 'star-electric' ), 'url' => self::url( 'returns' ) ),
				array( 'label' => __( 'Submit a Complaint', 'star-electric' ), 'url' => self::url( 'complaint' ) ),
				array( 'label' => __( 'My Account', 'star-electric' ), 'url' => self::url( 'account' ) ),
			),
			'business' => array(
				array( 'label' => __( 'About Us', 'star-electric' ), 'url' => self::url( 'about' ) ),
				array( 'label' => __( 'Bulk / Project Orders', 'star-electric' ), 'url' => self::url( 'quote' ) ),
				array( 'label' => __( 'Request a Quote', 'star-electric' ), 'url' => self::url( 'quote' ) ),
				array( 'label' => __( 'Shipping', 'star-electric' ), 'url' => self::url( 'shipping' ) ),
				array( 'label' => __( 'Privacy Policy', 'star-electric' ), 'url' => self::url( 'privacy' ) ),
				array( 'label' => __( 'Terms & Conditions', 'star-electric' ), 'url' => self::url( 'terms' ) ),
			),
			'legal'    => array(
				array( 'label' => __( 'Privacy Policy', 'star-electric' ), 'url' => self::url( 'privacy' ) ),
				array( 'label' => __( 'Terms & Conditions', 'star-electric' ), 'url' => self::url( 'terms' ) ),
				array( 'label' => __( 'Shipping', 'star-electric' ), 'url' => self::url( 'shipping' ) ),
				array( 'label' => __( 'Returns', 'star-electric' ), 'url' => self::url( 'returns' ) ),
			),
		);
	}

	/**
	 * The site footer.
	 *
	 * @param array $args Footer arguments.
	 */
	public static function site_footer( array $args = array() ): void {
		if ( ! self::available() ) {
			return;
		}

		$links = self::default_footer_links();
		$args  = wp_parse_args(
			$args,
			array(
				'about'          => __( 'Star Electric Enterprises is an electrical products store based in Saddar, Rawalpindi, supplying wiring, protection, lighting, fans and power equipment to homes, offices, commercial projects, electricians and contractors.', 'star-electric' ),
				'shop_title'     => __( 'Shop', 'star-electric' ),
				'shop_depts'     => 6,
				'shop_links'     => $links['shop'],
				'service_title'  => __( 'Customer Service', 'star-electric' ),
				'service_links'  => $links['service'],
				'business_title' => __( 'Business', 'star-electric' ),
				'business_links' => $links['business'],
				'contact_title'  => __( 'Contact', 'star-electric' ),
				'contact_store'  => __( 'Store', 'star-electric' ),
				'contact_area'   => __( 'Area', 'star-electric' ),
				'area'           => __( 'Saddar, Rawalpindi', 'star-electric' ),
				'contact_enq'    => __( 'Enquiries', 'star-electric' ),
				'contact_enq_l'  => __( 'Contact form', 'star-electric' ),
				'contact_quo'    => __( 'Quotations', 'star-electric' ),
				'contact_quo_l'  => __( 'Request a quotation', 'star-electric' ),
				'copyright'      => __( 'Star Electric Enterprises, Saddar, Rawalpindi. All rights reserved.', 'star-electric' ),
				'legal_links'    => $links['legal'],
			)
		);

		$tree = array_slice( Star_Electric_Shell::category_tree(), 0, max( 0, (int) $args['shop_depts'] ) );
		?>
		<div id="siteFooter">
			<footer class="footer">
				<div class="container footer__grid">

					<div class="footer__col footer__col--about">
						<?php echo Star_Electric_Shell::logo( 'logo--footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<p class="footer__about"><?php echo esc_html( (string) $args['about'] ); ?></p>
					</div>

					<div class="footer__col">
						<h2 class="footer__title"><?php echo esc_html( (string) $args['shop_title'] ); ?></h2>
						<ul class="footer__links">
							<?php foreach ( $tree as $branch ) : ?>
								<li>
									<a href="<?php echo esc_url( (string) get_term_link( $branch['term'] ) ); ?>">
										<?php echo esc_html( $branch['term']->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
							<?php foreach ( (array) $args['shop_links'] as $link ) : ?>
								<li><a href="<?php echo esc_url( (string) $link['url'] ); ?>"><?php echo esc_html( (string) $link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="footer__col">
						<h2 class="footer__title"><?php echo esc_html( (string) $args['service_title'] ); ?></h2>
						<ul class="footer__links">
							<?php foreach ( (array) $args['service_links'] as $link ) : ?>
								<li><a href="<?php echo esc_url( (string) $link['url'] ); ?>"><?php echo esc_html( (string) $link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="footer__col footer__col--business">
						<h2 class="footer__title"><?php echo esc_html( (string) $args['business_title'] ); ?></h2>
						<ul class="footer__links">
							<?php foreach ( (array) $args['business_links'] as $link ) : ?>
								<li><a href="<?php echo esc_url( (string) $link['url'] ); ?>"><?php echo esc_html( (string) $link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="footer__col">
						<h2 class="footer__title"><?php echo esc_html( (string) $args['contact_title'] ); ?></h2>
						<ul class="footer__contact">
							<li><span><?php echo esc_html( (string) $args['contact_store'] ); ?></span> <?php bloginfo( 'name' ); ?></li>
							<li><span><?php echo esc_html( (string) $args['contact_area'] ); ?></span> <?php echo esc_html( (string) $args['area'] ); ?></li>
							<li><span><?php echo esc_html( (string) $args['contact_enq'] ); ?></span> <a href="<?php echo esc_url( self::url( 'contact' ) ); ?>"><?php echo esc_html( (string) $args['contact_enq_l'] ); ?></a></li>
							<li><span><?php echo esc_html( (string) $args['contact_quo'] ); ?></span> <a href="<?php echo esc_url( self::url( 'quote' ) ); ?>"><?php echo esc_html( (string) $args['contact_quo_l'] ); ?></a></li>
						</ul>
					</div>
				</div>

				<div class="footer__bar">
					<div class="container footer__barinner">
						<p>
							&copy; <span data-year><?php echo esc_html( gmdate( 'Y' ) ); ?></span>
							<?php echo esc_html( (string) $args['copyright'] ); ?>
						</p>
						<ul class="footer__legal">
							<?php foreach ( (array) $args['legal_links'] as $link ) : ?>
								<li><a href="<?php echo esc_url( (string) $link['url'] ); ?>"><?php echo esc_html( (string) $link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</footer>
		</div>
		<?php
	}
}
