<?php
/**
 * About.
 *
 * A port of about.html. Every claim on this page is one the approved site
 * already makes, and the closing note is kept word for word: no establishment
 * year, customer count, award, certification, dealership or partnership is
 * stated anywhere, because the business has confirmed none of them.
 *
 * The department list is read from the live taxonomy rather than hard-coded,
 * so it cannot drift from the catalogue the way a written list would.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

Star_Electric_Shell::page_head(
	array(
		__( 'Home', 'star-electric-child' )  => home_url( '/' ),
		__( 'About', 'star-electric-child' ) => '',
	),
	__( 'About Star Electric Enterprises', 'star-electric-child' ),
	__( 'An electrical products store based in Saddar, Rawalpindi, supplying both everyday customers and the trade.', 'star-electric-child' )
);

$star_tree  = Star_Electric_Shell::category_tree();
$star_depts = count( $star_tree );
?>

<section class="section section--sm">
	<div class="container form-layout">

		<div class="prose">
			<h2><?php esc_html_e( 'Who we are', 'star-electric-child' ); ?></h2>
			<p><?php esc_html_e( 'Star Electric Enterprises is an electrical products store located in Saddar, Rawalpindi. We supply the materials used in everyday electrical work — wiring and cable, switches and sockets, circuit protection, lighting, fans, power equipment, smart devices and the tools that go with them.', 'star-electric-child' ); ?></p>
			<p><?php esc_html_e( 'Our customers range from homeowners buying a single replacement fitting to electricians and contractors ordering materials for a full installation. The store and this website are set up to serve both.', 'star-electric-child' ); ?></p>

			<h2><?php esc_html_e( 'Our location', 'star-electric-child' ); ?></h2>
			<p><?php esc_html_e( 'Saddar is one of Rawalpindi’s established commercial areas, and our physical store means customers can see products before buying, collect an order the same day, or talk through a specification in person.', 'star-electric-child' ); ?></p>

			<h2><?php esc_html_e( 'What we supply', 'star-electric-child' ); ?></h2>
			<p>
				<?php
				printf(
					/* translators: %s: number of departments in the catalogue */
					esc_html__( 'The catalogue is organised into %s departments so that customers can find a part by what it does rather than by guessing at a product name:', 'star-electric-child' ),
					esc_html( number_format_i18n( $star_depts ) )
				);
				?>
			</p>
			<ul>
				<?php foreach ( $star_tree as $star_node ) : ?>
					<?php
					$star_term  = $star_node['term'];
					$star_names = wp_list_pluck( $star_node['children'], 'name' );
					?>
					<li>
						<strong><?php echo esc_html( $star_term->name ); ?></strong>
						<?php if ( ! empty( $star_names ) ) : ?>
							&mdash; <?php echo esc_html( strtolower( implode( ', ', array_slice( $star_names, 0, 5 ) ) ) ); ?>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<h2><?php esc_html_e( 'How we work with customers', 'star-electric-child' ); ?></h2>
			<p><?php esc_html_e( 'Buying electrical materials usually comes down to getting the rating, size and specification right. We would rather spend a few minutes confirming that with a customer than have the wrong part go out of the door.', 'star-electric-child' ); ?></p>
			<p><?php esc_html_e( 'For larger requirements we quote in writing. Contractors, electricians and builders can send an item list or bill of quantities and receive a quotation covering the materials for a job, rather than pricing line by line.', 'star-electric-child' ); ?></p>

			<h2><?php esc_html_e( 'Retail and trade', 'star-electric-child' ); ?></h2>
			<p><?php esc_html_e( 'Retail customers get a straightforward online shop with clear pricing and stock status. Professional buyers get the same catalogue plus a quotation route for project quantities, and can order the same materials repeatedly without re-explaining a specification.', 'star-electric-child' ); ?></p>
		</div>

		<aside>
			<div class="info-card">
				<h3><?php esc_html_e( 'At a glance', 'star-electric-child' ); ?></h3>
				<ul class="info-list">
					<li>
						<?php echo Star_Electric_Shell::icon( 'building' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><strong><?php esc_html_e( 'Business', 'star-electric-child' ); ?></strong><?php bloginfo( 'name' ); ?></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><strong><?php esc_html_e( 'Location', 'star-electric-child' ); ?></strong><?php esc_html_e( 'Saddar, Rawalpindi', 'star-electric-child' ); ?></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'box' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span>
							<strong><?php esc_html_e( 'Departments', 'star-electric-child' ); ?></strong>
							<?php
							printf(
								/* translators: %s: number of product categories */
								esc_html__( '%s product categories', 'star-electric-child' ),
								esc_html( number_format_i18n( $star_depts ) )
							);
							?>
						</span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><strong><?php esc_html_e( 'We serve', 'star-electric-child' ); ?></strong><?php esc_html_e( 'Homes, offices, commercial projects, electricians and contractors', 'star-electric-child' ); ?></span>
					</li>
				</ul>
			</div>

			<div class="info-card">
				<h3><?php esc_html_e( 'Get in touch', 'star-electric-child' ); ?></h3>
				<p class="t-sm t-muted" style="margin-bottom:16px"><?php esc_html_e( 'Visit the store, or send a project list for a quotation.', 'star-electric-child' ); ?></p>
				<div class="stack" style="gap:8px">
					<a class="btn btn--accent btn--block btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?></a>
					<a class="btn btn--ghost btn--block btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact Us', 'star-electric-child' ); ?></a>
					<a class="btn btn--ghost btn--block btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>"><?php esc_html_e( 'Browse the Shop', 'star-electric-child' ); ?></a>
				</div>
			</div>
		</aside>
	</div>
</section>

<section class="section section--tint" aria-labelledby="valTitle">
	<div class="container">
		<div class="section__head section__head--center">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Our approach', 'star-electric-child' ); ?></p>
				<h2 class="section__title" id="valTitle"><?php esc_html_e( 'How We Do Business', 'star-electric-child' ); ?></h2>
				<p class="section__sub"><?php esc_html_e( 'The service standards we hold ourselves to.', 'star-electric-child' ); ?></p>
			</div>
		</div>

		<ul class="value-grid">
			<?php
			$star_values = array(
				array( 'shield', __( 'Genuine products', 'star-electric-child' ), __( 'We stock through our regular supply channels and stand behind what we sell.', 'star-electric-child' ) ),
				array( 'headset', __( 'Straight answers', 'star-electric-child' ), __( 'If a product is not right for the job, we will say so rather than sell it anyway.', 'star-electric-child' ) ),
				array( 'tag', __( 'Clear pricing', 'star-electric-child' ), __( 'Prices on the shop, written quotations for project quantities. No surprises.', 'star-electric-child' ) ),
				array( 'box', __( 'Trade support', 'star-electric-child' ), __( 'Repeat orders, project lists and bulk quantities handled as a normal part of business.', 'star-electric-child' ) ),
				array( 'pin', __( 'A real shop', 'star-electric-child' ), __( 'A physical counter in Saddar you can walk into, not just an online listing.', 'star-electric-child' ) ),
				array( 'refresh', __( 'After the sale', 'star-electric-child' ), __( 'Questions, replacements and complaints handled through a clear support route.', 'star-electric-child' ) ),
			);
			foreach ( $star_values as $star_value ) :
				?>
				<li class="value-card">
					<span class="value-card__ico"><?php echo Star_Electric_Shell::icon( $star_value[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<h3><?php echo esc_html( $star_value[1] ); ?></h3>
					<p><?php echo esc_html( $star_value[2] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>

		<p class="placeholder-note" style="margin-top:32px">
			<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'This page deliberately makes no claims about establishment year, customer numbers, awards, certifications, dealerships or partnerships. Those will only be added once the business confirms them.', 'star-electric-child' ); ?></span>
		</p>
	</div>
</section>

<section class="section" aria-labelledby="deptTitle">
	<div class="container">
		<div class="section__head">
			<div>
				<p class="section__eyebrow"><?php esc_html_e( 'Capabilities', 'star-electric-child' ); ?></p>
				<h2 class="section__title" id="deptTitle"><?php esc_html_e( 'What We Stock', 'star-electric-child' ); ?></h2>
				<p class="section__sub">
					<?php
					printf(
						/* translators: %s: number of departments */
						esc_html__( '%s departments covering the electrical range.', 'star-electric-child' ),
						esc_html( number_format_i18n( $star_depts ) )
					);
					?>
				</p>
			</div>
			<a class="link-more" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>">
				<?php esc_html_e( 'Browse the shop', 'star-electric-child' ); ?>
				<?php echo Star_Electric_Shell::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
		<?php get_template_part( 'template-parts/category-grid' ); ?>
	</div>
</section>

<section class="section bulk" aria-labelledby="aboutCta">
	<div class="container bulk__inner">
		<div>
			<h2 class="bulk__title" id="aboutCta"><?php esc_html_e( 'Ready to order?', 'star-electric-child' ); ?></h2>
			<p class="bulk__text"><?php esc_html_e( 'Browse the catalogue for retail purchases, or send a project list if you need a quotation for larger quantities.', 'star-electric-child' ); ?></p>
			<div class="btn-row">
				<a class="btn btn--accent btn--lg" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>"><?php esc_html_e( 'Browse the Shop', 'star-electric-child' ); ?></a>
				<a class="btn btn--light btn--lg" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?></a>
			</div>
		</div>
		<ol class="bulk__steps">
			<li><b>01</b> <?php esc_html_e( 'Find the products you need', 'star-electric-child' ); ?></li>
			<li><b>02</b> <?php esc_html_e( 'Order online or request a quote', 'star-electric-child' ); ?></li>
			<li><b>03</b> <?php esc_html_e( 'Collect in Saddar or arrange delivery', 'star-electric-child' ); ?></li>
		</ol>
	</div>
</section>

<?php
get_footer();
