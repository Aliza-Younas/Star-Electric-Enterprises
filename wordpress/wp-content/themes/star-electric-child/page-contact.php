<?php
/**
 * Contact.
 *
 * A port of contact.html. The form now sends; on the approved static site it
 * was visual only.
 *
 * The contact details panel carries only what is on record: the store name and
 * the area. No phone number, WhatsApp number, street address or opening hours
 * appears, because none has been supplied - and the store photograph stays a
 * labelled placeholder rather than another business's premises.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

list( $star_notice_type, $star_notice ) = Star_Electric_Forms::notice();
?>

<div class="page-head">
	<div class="container">
		<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'star-electric-child' ); ?>">
			<ol class="breadcrumb">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'star-electric-child' ); ?></a></li>
				<li class="sep" aria-hidden="true">/</li>
				<li aria-current="page"><?php esc_html_e( 'Contact', 'star-electric-child' ); ?></li>
			</ol>
		</nav>
		<h1 class="page-head__title"><?php esc_html_e( 'Contact Us', 'star-electric-child' ); ?></h1>
		<p class="page-head__sub"><?php esc_html_e( 'Questions about a product, a rating, availability or a project requirement — send us a message and we will come back to you.', 'star-electric-child' ); ?></p>
	</div>
</div>

<section class="section section--sm">
	<div class="container form-layout">

		<div class="panel">
			<div class="panel__head">
				<div>
					<h2 class="panel__title"><?php esc_html_e( 'Send us a message', 'star-electric-child' ); ?></h2>
					<p class="t-sm t-muted" style="margin-top:4px"><?php esc_html_e( 'We reply to the email address you give us.', 'star-electric-child' ); ?></p>
				</div>
				<span class="t-xs t-faint"><span class="t-accent">*</span> <?php esc_html_e( 'required', 'star-electric-child' ); ?></span>
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
						<label class="field__label" for="cName"><?php esc_html_e( 'Full name', 'star-electric-child' ); ?> <span class="req">*</span></label>
						<input class="input" id="cName" name="name" type="text" autocomplete="name" required>
					</div>
					<div class="field">
						<label class="field__label" for="cPhone"><?php esc_html_e( 'Phone', 'star-electric-child' ); ?> <span class="req">*</span></label>
						<input class="input" id="cPhone" name="phone" type="tel" autocomplete="tel" required>
					</div>
					<div class="field">
						<label class="field__label" for="cEmail"><?php esc_html_e( 'Email', 'star-electric-child' ); ?> <span class="req">*</span></label>
						<input class="input" id="cEmail" name="email" type="email" autocomplete="email" required>
					</div>
					<div class="field">
						<label class="field__label" for="cSubject"><?php esc_html_e( 'Subject', 'star-electric-child' ); ?> <span class="req">*</span></label>
						<select class="select" id="cSubject" name="subject" required>
							<option value=""><?php esc_html_e( 'Select a subject…', 'star-electric-child' ); ?></option>
							<?php
							$star_subjects = array(
								__( 'Product enquiry', 'star-electric-child' ),
								__( 'Stock availability', 'star-electric-child' ),
								__( 'Order status', 'star-electric-child' ),
								__( 'Bulk / project quotation', 'star-electric-child' ),
								__( 'Delivery question', 'star-electric-child' ),
								__( 'Returns or replacement', 'star-electric-child' ),
								__( 'Other', 'star-electric-child' ),
							);
							foreach ( $star_subjects as $star_subject ) {
								printf( '<option>%s</option>', esc_html( $star_subject ) );
							}
							?>
						</select>
					</div>
					<div class="field span-2">
						<label class="field__label" for="cMessage"><?php esc_html_e( 'Message', 'star-electric-child' ); ?> <span class="req">*</span></label>
						<textarea class="textarea" id="cMessage" name="message" required
							placeholder="<?php esc_attr_e( 'Tell us what you need — include ratings, sizes or model numbers where you know them.', 'star-electric-child' ); ?>"></textarea>
					</div>
				</div>

				<label class="check">
					<input type="checkbox" name="consent" value="yes" required>
					<span>
						<?php
						printf(
							/* translators: %s: link to the privacy policy */
							esc_html__( 'I agree that my details may be used to respond to this message, as described in the %s.', 'star-electric-child' ),
							'<a class="link-inline" href="' . esc_url( Star_Electric_Shell::url( 'privacy' ) ) . '">' . esc_html__( 'Privacy Policy', 'star-electric-child' ) . '</a>'
						);
						?>
						<span class="req">*</span>
					</span>
				</label>

				<button class="btn btn--accent btn--lg" type="submit" style="justify-self:start">
					<?php esc_html_e( 'Send Message', 'star-electric-child' ); ?>
				</button>
			</form>
		</div>

		<aside>
			<div class="info-card">
				<h3><?php esc_html_e( 'Store details', 'star-electric-child' ); ?></h3>
				<ul class="info-list">
					<li>
						<?php echo Star_Electric_Shell::icon( 'building' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><strong><?php bloginfo( 'name' ); ?></strong><?php esc_html_e( 'Saddar, Rawalpindi', 'star-electric-child' ); ?></span>
					</li>
				</ul>
				<?php
				/*
				 * The street address, phone number, WhatsApp number and opening hours
				 * are deliberately absent, exactly as they are on the approved page.
				 * None has been supplied, and this is a public page: an invented
				 * contact detail is worse than a missing one.
				 */
				?>
			</div>

			<div class="info-card">
				<h3><?php esc_html_e( 'Other ways to get help', 'star-electric-child' ); ?></h3>
				<ul class="info-list">
					<li>
						<?php echo Star_Electric_Shell::icon( 'box' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><a class="link-inline" href="<?php echo esc_url( Star_Electric_Shell::url( 'track-order' ) ); ?>"><?php esc_html_e( 'Track an order', 'star-electric-child' ); ?></a></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'doc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><a class="link-inline" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Request a bulk quote', 'star-electric-child' ); ?></a></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><a class="link-inline" href="<?php echo esc_url( Star_Electric_Shell::url( 'faq' ) ); ?>"><?php esc_html_e( 'Read the FAQs', 'star-electric-child' ); ?></a></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'headset' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><a class="link-inline" href="<?php echo esc_url( Star_Electric_Shell::url( 'complaint' ) ); ?>"><?php esc_html_e( 'Submit a complaint', 'star-electric-child' ); ?></a></span>
					</li>
				</ul>
			</div>
		</aside>
	</div>
</section>

<section class="section section--tint" aria-labelledby="mapTitle">
	<div class="container">
		<div>
			<p class="section__eyebrow"><?php esc_html_e( 'Visit the store', 'star-electric-child' ); ?></p>
			<h2 class="section__title" id="mapTitle"><?php esc_html_e( 'Find Us in Saddar', 'star-electric-child' ); ?></h2>
			<p class="section__sub" style="margin-bottom:24px">
				<?php esc_html_e( 'Come in to see products before buying, collect an order, or talk through a specification at the counter.', 'star-electric-child' ); ?>
			</p>

			<?php
			/*
			 * No map, street address, opening hours or collection process. None is
			 * on record, and the approved page leaves those rows out rather than
			 * showing placeholders. The enquiry form above is the route that works.
			 */
			?>

			<div class="btn-row">
				<a class="btn btn--accent" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>">
					<?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
