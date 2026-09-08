<?php
/**
 * Submit a Complaint.
 *
 * A port of complaint.html. The form now sends; on the approved static site it
 * was visual only.
 *
 * The attachment field is deliberately absent rather than shown disabled, and
 * no resolution time is promised: the store has supplied no complaints
 * procedure or turnaround, and a public page must not imply one.
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
				<li aria-current="page"><?php esc_html_e( 'Submit a Complaint', 'star-electric-child' ); ?></li>
			</ol>
		</nav>
		<h1 class="page-head__title"><?php esc_html_e( 'Submit a Complaint', 'star-electric-child' ); ?></h1>
		<p class="page-head__sub"><?php esc_html_e( 'If something has gone wrong with an order or a product, tell us what happened and we will look into it.', 'star-electric-child' ); ?></p>
	</div>
</div>

<section class="section section--sm">
	<div class="container form-layout">

		<div class="panel">
			<div class="panel__head">
				<div>
					<h2 class="panel__title"><?php esc_html_e( 'Complaint Details', 'star-electric-child' ); ?></h2>
					<p class="t-sm t-muted" style="margin-top:4px"><?php esc_html_e( 'The more detail you give, the faster we can look into it.', 'star-electric-child' ); ?></p>
				</div>
				<span class="t-xs t-faint"><span class="t-accent">*</span> <?php esc_html_e( 'required', 'star-electric-child' ); ?></span>
			</div>

			<?php if ( '' !== $star_notice ) : ?>
				<div class="notice notice--<?php echo esc_attr( $star_notice_type ); ?>" role="status" style="margin-bottom:20px">
					<p><?php echo esc_html( $star_notice ); ?></p>
				</div>
			<?php endif; ?>

			<form class="stack-5" method="post" action="<?php echo esc_url( Star_Electric_Forms::action() ); ?>">
				<?php Star_Electric_Forms::fields( 'complaint' ); ?>

				<fieldset style="border:0;padding:0;margin:0">
					<legend class="field__label" style="margin-bottom:16px;font-size:14px"><?php esc_html_e( 'Your details', 'star-electric-child' ); ?></legend>
					<div class="form-grid">
						<div class="field">
							<label class="field__label" for="xName"><?php esc_html_e( 'Full name', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<input class="input" id="xName" name="name" type="text" autocomplete="name" required>
						</div>
						<div class="field">
							<label class="field__label" for="xOrder"><?php esc_html_e( 'Order number', 'star-electric-child' ); ?></label>
							<input class="input" id="xOrder" name="order" type="text"
								placeholder="<?php esc_attr_e( 'The reference on your order confirmation', 'star-electric-child' ); ?>">
						</div>
						<div class="field">
							<label class="field__label" for="xPhone"><?php esc_html_e( 'Phone', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<input class="input" id="xPhone" name="phone" type="tel" autocomplete="tel" required>
						</div>
						<div class="field">
							<label class="field__label" for="xEmail"><?php esc_html_e( 'Email', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<input class="input" id="xEmail" name="email" type="email" autocomplete="email" required>
						</div>
					</div>
				</fieldset>

				<hr>

				<fieldset style="border:0;padding:0;margin:0">
					<legend class="field__label" style="margin-bottom:16px;font-size:14px"><?php esc_html_e( 'What went wrong', 'star-electric-child' ); ?></legend>
					<div class="form-grid">
						<div class="field">
							<label class="field__label" for="xType"><?php esc_html_e( 'Complaint type', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<select class="select" id="xType" name="type" required>
								<option value=""><?php esc_html_e( 'Select the type of problem…', 'star-electric-child' ); ?></option>
								<?php
								$star_types = array(
									__( 'Wrong item received', 'star-electric-child' ),
									__( 'Item damaged on arrival', 'star-electric-child' ),
									__( 'Item faulty or not working', 'star-electric-child' ),
									__( 'Missing item from the order', 'star-electric-child' ),
									__( 'Delivery problem or delay', 'star-electric-child' ),
									__( 'Billing or pricing issue', 'star-electric-child' ),
									__( 'Service at the store', 'star-electric-child' ),
									__( 'Other', 'star-electric-child' ),
								);
								foreach ( $star_types as $star_type ) {
									printf( '<option>%s</option>', esc_html( $star_type ) );
								}
								?>
							</select>
						</div>
						<div class="field">
							<label class="field__label" for="xSubject"><?php esc_html_e( 'Subject', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<input class="input" id="xSubject" name="subject" type="text" required
								placeholder="<?php esc_attr_e( 'A short summary of the problem', 'star-electric-child' ); ?>">
						</div>
						<div class="field span-2">
							<label class="field__label" for="xDesc"><?php esc_html_e( 'Description', 'star-electric-child' ); ?> <span class="req">*</span></label>
							<textarea class="textarea" id="xDesc" name="description" required style="min-height:160px"
								placeholder="<?php esc_attr_e( 'What was ordered, what arrived, when it happened, and what you would like us to do.', 'star-electric-child' ); ?>"></textarea>
						</div>
					</div>
				</fieldset>

				<label class="check">
					<input type="checkbox" name="consent" value="yes" required>
					<span>
						<?php
						printf(
							/* translators: %s: link to the privacy policy */
							esc_html__( 'I agree that my details may be used to investigate and respond to this complaint, as described in the %s.', 'star-electric-child' ),
							'<a class="link-inline" href="' . esc_url( Star_Electric_Shell::url( 'privacy' ) ) . '">' . esc_html__( 'Privacy Policy', 'star-electric-child' ) . '</a>'
						);
						?>
						<span class="req">*</span>
					</span>
				</label>

				<button class="btn btn--accent btn--lg" type="submit" style="justify-self:start">
					<?php esc_html_e( 'Submit Complaint', 'star-electric-child' ); ?>
				</button>
			</form>
		</div>

		<aside>
			<div class="info-card">
				<h2 class="panel__title" style="font-size:16px"><?php esc_html_e( 'Before you send', 'star-electric-child' ); ?></h2>
				<ul class="prose" style="margin-top:12px">
					<li><?php esc_html_e( 'Include the order or invoice reference if you have one.', 'star-electric-child' ); ?></li>
					<li><?php esc_html_e( 'Describe the item by rating, size or model where you can.', 'star-electric-child' ); ?></li>
					<li><?php esc_html_e( 'Tell us what outcome you are looking for.', 'star-electric-child' ); ?></li>
				</ul>
				<?php
				/*
				 * No resolution time is promised. The store has supplied no
				 * complaints procedure or turnaround, and stating one here would
				 * be inventing a commitment.
				 */
				?>
			</div>

			<div class="info-card" style="margin-top:16px">
				<h2 class="panel__title" style="font-size:16px"><?php esc_html_e( 'Not a complaint?', 'star-electric-child' ); ?></h2>
				<p class="t-sm t-muted" style="margin-top:8px"><?php esc_html_e( 'For product questions, availability or a quotation, the contact form is the quicker route.', 'star-electric-child' ); ?></p>
				<a class="btn btn--ghost btn--block" style="margin-top:12px" href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>">
					<?php esc_html_e( 'Contact the Store', 'star-electric-child' ); ?>
				</a>
			</div>
		</aside>
	</div>
</section>

<?php
get_footer();
