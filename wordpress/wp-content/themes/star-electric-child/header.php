<?php
/**
 * The global header.
 *
 * A direct port of SEE_UI.renderHeader() and SEE_UI.renderDrawer() from the
 * approved storefront's js/components.js. The markup and class names are
 * deliberately identical, because the stylesheets this theme ships are the
 * same files that storefront uses - changing a class here would break the
 * approved design rather than migrate it.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$star_shell   = 'Star_Electric_Shell';
$star_active  = Star_Electric_Shell::active();
$star_nav     = Star_Electric_Shell::nav();
$star_tree    = Star_Electric_Shell::category_tree();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to main content', 'star-electric-child' ); ?></a>

<div id="siteHeader">

	<div class="topbar">
		<div class="container topbar__inner">
			<p class="topbar__brand">
				<strong><?php bloginfo( 'name' ); ?></strong>
				<span class="topbar__dot" aria-hidden="true">&bull;</span>
				<span class="topbar__loc">
					<?php echo Star_Electric_Shell::icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Saddar, Rawalpindi', 'star-electric-child' ); ?>
				</span>
			</p>
			<ul class="topbar__links">
				<li>
					<a href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>">
						<?php echo Star_Electric_Shell::icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php esc_html_e( 'Contact the store', 'star-electric-child' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>">
						<?php echo Star_Electric_Shell::icon( 'doc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php esc_html_e( 'Request a quotation', 'star-electric-child' ); ?>
					</a>
				</li>
			</ul>
		</div>
	</div>

	<header class="header" id="siteHeaderBar">
		<div class="container header__inner">
			<button class="icon-btn header__burger" id="navToggle" type="button"
				aria-label="<?php esc_attr_e( 'Open menu', 'star-electric-child' ); ?>"
				aria-expanded="false" aria-controls="mobileNav">
				<?php echo Star_Electric_Shell::icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>

			<?php echo Star_Electric_Shell::logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<form class="search" role="search" id="searchForm" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
				<label class="sr-only" for="searchCat"><?php esc_html_e( 'Search within category', 'star-electric-child' ); ?></label>
				<div class="search__catwrap">
					<select class="search__cat" id="searchCat" name="product_cat">
						<option value=""><?php esc_html_e( 'All Categories', 'star-electric-child' ); ?></option>
						<?php foreach ( $star_tree as $star_branch ) : ?>
							<option value="<?php echo esc_attr( $star_branch['term']->slug ); ?>">
								<?php echo esc_html( $star_branch['term']->name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php echo Star_Electric_Shell::icon( 'chevdown', 'search__caret' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<label class="sr-only" for="searchInput"><?php esc_html_e( 'Search products', 'star-electric-child' ); ?></label>
				<input class="search__input" id="searchInput" name="s" type="search" autocomplete="off"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="<?php esc_attr_e( 'Search cables, switches, breakers, lighting, fans and more...', 'star-electric-child' ); ?>">
				<input type="hidden" name="post_type" value="product">
				<button class="search__btn" type="submit">
					<?php echo Star_Electric_Shell::icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php esc_html_e( 'Search', 'star-electric-child' ); ?></span>
				</button>
				<div class="search__suggest" id="searchSuggest" hidden></div>
			</form>

			<div class="header__actions">
				<a class="action" href="<?php echo esc_url( Star_Electric_Shell::url( 'account' ) ); ?>">
					<span class="action__ico"><?php echo Star_Electric_Shell::icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="action__txt">
						<small><?php esc_html_e( 'Customer', 'star-electric-child' ); ?></small>
						<strong><?php esc_html_e( 'Account', 'star-electric-child' ); ?></strong>
					</span>
				</a>
				<a class="action" href="<?php echo esc_url( Star_Electric_Shell::url( 'wishlist' ) ); ?>">
					<span class="action__ico">
						<?php echo Star_Electric_Shell::icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="badge" data-count="wishlist">0</span>
					</span>
					<span class="action__txt">
						<small><?php esc_html_e( 'Saved', 'star-electric-child' ); ?></small>
						<strong><?php esc_html_e( 'Wishlist', 'star-electric-child' ); ?></strong>
					</span>
				</a>
				<a class="action" href="<?php echo esc_url( Star_Electric_Shell::url( 'cart' ) ); ?>">
					<span class="action__ico">
						<?php echo Star_Electric_Shell::icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="badge" data-count="cart"><?php echo esc_html( (string) star_electric_child_cart_count() ); ?></span>
					</span>
					<span class="action__txt">
						<small><?php esc_html_e( 'Cart', 'star-electric-child' ); ?></small>
						<strong data-cart-total><?php echo wp_kses_post( star_electric_child_cart_total() ); ?></strong>
					</span>
				</a>
				<a class="btn btn--accent header__quote" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>">
					<?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?>
				</a>
			</div>
		</div>
	</header>

	<nav class="nav" aria-label="<?php esc_attr_e( 'Primary', 'star-electric-child' ); ?>">
		<div class="container nav__inner">
			<ul class="nav__list">
				<?php foreach ( $star_nav as $star_item ) : ?>
					<?php $star_is_active = ( $star_item['key'] === $star_active ) ? ' is-active' : ''; ?>
					<?php if ( ! empty( $star_item['mega'] ) ) : ?>
						<li class="has-mega">
							<a class="nav__link<?php echo esc_attr( $star_is_active ); ?>"
								href="<?php echo esc_url( $star_item['url'] ); ?>"
								aria-haspopup="true" aria-expanded="false">
								<?php echo esc_html( $star_item['label'] ); ?>
								<?php echo Star_Electric_Shell::icon( 'chevdown' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
							<div class="mega">
								<div class="mega__grid">
									<?php foreach ( $star_tree as $star_branch ) : ?>
										<div class="mega__col">
											<a class="mega__title" href="<?php echo esc_url( (string) get_term_link( $star_branch['term'] ) ); ?>">
												<?php echo Star_Electric_Shell::icon( 'chevright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												<?php echo esc_html( $star_branch['term']->name ); ?>
											</a>
											<div class="mega__links">
												<?php foreach ( $star_branch['children'] as $star_child ) : ?>
													<a href="<?php echo esc_url( (string) get_term_link( $star_child ) ); ?>">
														<?php echo esc_html( $star_child->name ); ?>
													</a>
												<?php endforeach; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
								<div class="mega__foot">
									<span><?php esc_html_e( 'Every product listed is an individual item published by one of our approved supplier sources.', 'star-electric-child' ); ?></span>
									<a class="link-more" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>">
										<?php esc_html_e( 'Browse the full shop', 'star-electric-child' ); ?>
										<?php echo Star_Electric_Shell::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
								</div>
							</div>
						</li>
					<?php else : ?>
						<li>
							<a class="nav__link<?php echo esc_attr( $star_is_active ); ?>" href="<?php echo esc_url( $star_item['url'] ); ?>">
								<?php echo esc_html( $star_item['label'] ); ?>
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
	<div class="drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'star-electric-child' ); ?>">
		<div class="drawer__head">
			<span class="drawer__title"><?php esc_html_e( 'Menu', 'star-electric-child' ); ?></span>
			<button class="icon-btn" type="button" data-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'star-electric-child' ); ?>">
				<?php echo Star_Electric_Shell::icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
		<div class="drawer__body">
			<ul class="drawer__nav">
				<?php foreach ( $star_nav as $star_item ) : ?>
					<li>
						<a href="<?php echo esc_url( $star_item['url'] ); ?>"
							class="<?php echo esc_attr( $star_item['key'] === $star_active ? 'is-active' : '' ); ?>">
							<?php echo esc_html( $star_item['label'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="drawer__sep"><?php esc_html_e( 'Shop by category', 'star-electric-child' ); ?></p>
			<ul class="drawer__nav">
				<?php foreach ( $star_tree as $star_branch ) : ?>
					<li>
						<a href="<?php echo esc_url( (string) get_term_link( $star_branch['term'] ) ); ?>">
							<?php echo esc_html( $star_branch['term']->name ); ?>
							<?php echo Star_Electric_Shell::icon( 'chevright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="drawer__sep"><?php esc_html_e( 'Customer service', 'star-electric-child' ); ?></p>
			<ul class="drawer__nav">
				<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'track-order' ) ); ?>"><?php esc_html_e( 'Track Order', 'star-electric-child' ); ?></a></li>
				<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'faq' ) ); ?>"><?php esc_html_e( 'FAQs', 'star-electric-child' ); ?></a></li>
				<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'complaint' ) ); ?>"><?php esc_html_e( 'Submit a Complaint', 'star-electric-child' ); ?></a></li>
			</ul>
		</div>
		<div class="drawer__foot">
			<a class="btn btn--accent btn--block" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>">
				<?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?>
			</a>
		</div>
	</div>
</div>

<main id="main">
