<?php
/**
 * Request a Quote.
 *
 * A port of quote-request.html, with one difference that matters: on the
 * approved static site this form carries a data-inactive-form notice and
 * submits nowhere, because a static page has nothing to submit to. Here it
 * actually sends. For a catalogue where 2,548 of 4,348 products are priced on
 * enquiry, this page is the buying route for most of the shop.
 *
 * The file attachment field is deliberately absent rather than shown disabled:
 * the approved page tells the visitor attachments are not accepted, and an
 * input that looks usable but is not would be worse.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

list( $star_notice_type, $star_notice ) = Star_Electric_Forms::notice();

// A "Request a Quote" button on a product carries its id here.
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$star_product_id = isset( $_GET['product'] ) ? absint( $_GET['product'] ) : 0;
$star_product    = $star_product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $star_product_id ) : null;
?>

<section class="section bulk section--sm" aria-labelledby="quoteTitle">
	<div class="container">
		<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'star-electric-child' ); ?>" style="margin-bottom:16px">
			<ol class="breadcrumb">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:rgba(255,255,255,.6)"><?php esc_html_e( 'Home', 'star-electric-child' ); ?></a></li>
				<li class="sep" aria-hidden="true" style="color:rgba(255,255,255,.35)">/</li>
				<li aria-current="page" style="color:#fff"><?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?></li>
			</ol>
		</nav>

		<div class="bulk__inner">
			<div>
				<h1 class="bulk__title" id="quoteTitle"><?php esc_html_e( 'Bulk & Project Orders', 'star-electric-child' ); ?></h1>
				<p class="bulk__text"><?php esc_html_e( 'Supplying contractors, electricians, builders and commercial projects. Send us your item list or bill of quantities and we will prepare a written quotation for the materials you need.', 'star-electric-child' ); ?></p>
				<ul class="hero__cta" style="gap:8px;flex-wrap:wrap">
					<li class="hero__eyebrow" style="margin:0"><?php esc_html_e( 'Contractors', 'star-electric-child' ); ?></li>
					<li class="hero__eyebrow" style="margin:0"><?php esc_html_e( 'Electricians', 'star-electric-child' ); ?></li>
					<li class="hero__eyebrow" style="margin:0"><?php esc_html_e( 'Builders', 'star-electric-child' ); ?></li>
					<li class="hero__eyebrow" style="margin:0"><?php esc_html_e( 'Commercial Projects', 'star-electric-child' ); ?></li>
				</ul>
			</div>
			<ol class="bulk__steps">
				<li><b>01</b> <?php esc_html_e( 'Submit your requirements below', 'star-electric-child' ); ?></li>
				<li><b>02</b> <?php esc_html_e( 'We review availability and pricing', 'star-electric-child' ); ?></li>
				<li><b>03</b> <?php esc_html_e( 'You receive a written quotation', 'star-electric-child' ); ?></li>
			</ol>
		</div>
	</div>
</section>

<section class="section section--sm">
	<div class="container form-layout">

		<div class="panel">
			<div class="panel__head">
				<div>
					<h2 class="panel__title"><?php esc_html_e( 'Quote Request', 'star-electric-child' ); ?></h2>
					<p class="t-sm t-muted" style="margin-top:4px"><?php esc_html_e( 'The more detail you provide, the more accurate the quotation.', 'star-electric-child' ); ?></p>
				</div>
				<span class="t-xs t-faint"><span class="t-accent">*</span> <?php esc_html_e( 'required', 'star-electric-child' ); ?></span>
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
							<?php esc_html_e( 'Quoting for:', 'star-electric-child' ); ?>
							<strong><?php echo esc_html( $star_product->get_name() ); ?></strong>
						</p>
					</div>
				<?php endif; ?>

				<fieldset style="border:0;padding:0;margin:0">
					<legend class="field__label" style="margin-bottom:16px;font-size:14px"><?php esc_html_e( 'Your details', 'star-electric-child' ); ?></legend>
					<div class="form-grid">
						<div class="field">
							<label class="field__label" for="qName"><?php esc_html_e( 'Full name', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<input class="input" id="qName" name="name" type="text" autocomplete="name" required>
						</div>
						<div class="field">
							<label class="field__label" for="qCompany"><?php esc_html_e( 'Company / Contractor name', 'star-electric-child' ); ?></label>
							<input class="input" id="qCompany" name="company" type="text" autocomplete="organization">
						</div>
						<div class="field">
							<label class="field__label" for="qPhone"><?php esc_html_e( 'Phone', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<input class="input" id="qPhone" name="phone" type="tel" autocomplete="tel" required>
						</div>
						<div class="field">
							<label class="field__label" for="qEmail"><?php esc_html_e( 'Email', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<input class="input" id="qEmail" name="email" type="email" autocomplete="email" required>
						</div>
					</div>
				</fieldset>

				<hr>

				<fieldset style="border:0;padding:0;margin:0">
					<legend class="field__label" style="margin-bottom:16px;font-size:14px"><?php esc_html_e( 'Project details', 'star-electric-child' ); ?></legend>
					<div class="form-grid">
						<div class="field">
							<label class="field__label" for="qType"><?php esc_html_e( 'Project type', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<select class="select" id="qType" name="project_type" required>
								<option value=""><?php esc_html_e( 'Select a project type…', 'star-electric-child' ); ?></option>
								<?php
								$star_types = array(
									__( 'Residential — new build', 'star-electric-child' ),
									__( 'Residential — renovation / rewiring', 'star-electric-child' ),
									__( 'Commercial fit-out', 'star-electric-child' ),
									__( 'Office installation', 'star-electric-child' ),
									__( 'Industrial / factory', 'star-electric-child' ),
									__( 'Shop or retail unit', 'star-electric-child' ),
									__( 'Maintenance / repair contract', 'star-electric-child' ),
									__( 'Other', 'star-electric-child' ),
								);
								foreach ( $star_types as $star_type ) {
									printf( '<option>%s</option>', esc_html( $star_type ) );
								}
								?>
							</select>
						</div>
						<div class="field">
							<label class="field__label" for="qDate"><?php esc_html_e( 'Required delivery date', 'star-electric-child' ); ?></label>
							<input class="input" id="qDate" name="required_date" type="date">
							<p class="field__hint"><?php esc_html_e( 'Approximate is fine.', 'star-electric-child' ); ?></p>
						</div>
						<div class="field span-2">
							<label class="field__label" for="qItems"><?php esc_html_e( 'Product requirements', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<textarea class="textarea" id="qItems" name="requirements" required
								placeholder="<?php echo esc_attr__( "List the products you need — for example:\n\n1.5mm single core copper wire — 20 coils\n32A MCB single pole — 40 units\n12W LED bulb B22 — 150 units", 'star-electric-child' ); ?>"><?php
								echo $star_product instanceof WC_Product
									? esc_textarea( $star_product->get_name() . "\n" )
									: '';
							?></textarea>
							<p class="field__hint"><?php esc_html_e( 'Include ratings, sizes and any brand preference where it matters.', 'star-electric-child' ); ?></p>
						</div>
						<div class="field span-2">
							<label class="field__label" for="qQty"><?php esc_html_e( 'Estimated total quantities / order value', 'star-electric-child' ); ?></label>
							<input class="input" id="qQty" name="quantities" type="text"
								placeholder="<?php esc_attr_e( 'e.g. approx. 400 items, or an approximate budget range', 'star-electric-child' ); ?>">
						</div>
						<div class="field span-2">
							<label class="field__label" for="qMessage"><?php esc_html_e( 'Additional notes', 'star-electric-child' ); ?></label>
							<textarea class="textarea" id="qMessage" name="message" style="min-height:110px"
								placeholder="<?php esc_attr_e( 'Site location, phased delivery, access restrictions, anything else we should know…', 'star-electric-child' ); ?>"></textarea>
						</div>
					</div>
				</fieldset>

				<hr>

				<label class="check">
					<input type="checkbox" name="consent" value="yes" required>
					<span>
						<?php
						printf(
							/* translators: %s: link to the privacy policy */
							esc_html__( 'I agree that my details may be used to respond to this quote request, as described in the %s.', 'star-electric-child' ),
							'<a class="link-inline" href="' . esc_url( Star_Electric_Shell::url( 'privacy' ) ) . '">' . esc_html__( 'Privacy Policy', 'star-electric-child' ) . '</a>'
						);
						?>
						<span class="req">*</span>
					</span>
				</label>

				<button class="btn btn--accent btn--lg" type="submit" style="justify-self:start">
					<?php esc_html_e( 'Submit Quote Request', 'star-electric-child' ); ?>
				</button>
			</form>
		</div>

		<aside>
			<div class="info-card">
				<h3><?php esc_html_e( 'What happens next', 'star-electric-child' ); ?></h3>
				<ul class="info-list">
					<li>
						<?php echo Star_Electric_Shell::icon( 'doc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><strong><?php esc_html_e( 'We review your list', 'star-electric-child' ); ?></strong><?php esc_html_e( 'Availability and current pricing are checked.', 'star-electric-child' ); ?></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><strong><?php esc_html_e( 'You receive a quotation', 'star-electric-child' ); ?></strong><?php esc_html_e( 'Sent in writing to the contact details you provide.', 'star-electric-child' ); ?></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'truck' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><strong><?php esc_html_e( 'Collection or delivery', 'star-electric-child' ); ?></strong><?php esc_html_e( 'Arranged once the quotation is accepted.', 'star-electric-child' ); ?></span>
					</li>
				</ul>
				<p class="field__hint" style="margin-top:16px">
					<?php esc_html_e( 'The store will come back to you once it has priced your list.', 'star-electric-child' ); ?>
				</p>
				<?php
				/*
				 * No response time is promised here. The store has not supplied one,
				 * and a turnaround commitment on a public page would be a business
				 * fact this project does not have.
				 */
				?>
			</div>

			<div class="info-card">
				<h3><?php esc_html_e( 'Prefer to talk it through?', 'star-electric-child' ); ?></h3>
				<ul class="info-list">
					<li>
						<?php echo Star_Electric_Shell::icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><strong><?php esc_html_e( 'Visit the store', 'star-electric-child' ); ?></strong><?php esc_html_e( 'Saddar, Rawalpindi', 'star-electric-child' ); ?></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><strong><?php esc_html_e( 'Send a message', 'star-electric-child' ); ?></strong><a class="link-inline" href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>"><?php esc_html_e( 'Use the contact form', 'star-electric-child' ); ?></a></span>
					</li>
				</ul>
				<a class="btn btn--ghost btn--block btn--sm" style="margin-top:16px" href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>">
					<?php esc_html_e( 'Contact page', 'star-electric-child' ); ?>
				</a>
			</div>

			<div class="info-card">
				<h3><?php esc_html_e( 'Browse before you ask', 'star-electric-child' ); ?></h3>
				<p class="t-sm t-muted" style="margin-bottom:16px">
					<?php esc_html_e( 'Check the catalogue to confirm the products and specifications you need.', 'star-electric-child' ); ?>
				</p>
				<a class="btn btn--accent btn--block btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>">
					<?php esc_html_e( 'Browse the Shop', 'star-electric-child' ); ?>
				</a>
			</div>
		</aside>
	</div>
</section>

<?php
get_footer();
