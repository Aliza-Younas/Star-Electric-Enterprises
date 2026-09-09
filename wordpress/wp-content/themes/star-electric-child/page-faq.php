<?php
/**
 * Frequently Asked Questions.
 *
 * A port of faq.html. Two answers had to change, because the static text is no
 * longer true of this site:
 *
 * - "How do I place an order?" described a checkout that completed. Online
 *   ordering is not open here either (no payment method is configured, which
 *   is the store's own decision), so the answer says so and points at the
 *   routes that do work.
 * - The damaged-item answer offered a complaint form "which accepts
 *   attachments". This build's complaint form deliberately has no attachment
 *   field, so promising one would send people looking for a control that is
 *   not there.
 *
 * Everything else is word for word, including every answer that says a term is
 * not published. Those are the point of the page, not gaps in it.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$star_contact   = Star_Electric_Shell::url( 'contact' );
$star_quote     = Star_Electric_Shell::url( 'quote' );
$star_complaint = Star_Electric_Shell::url( 'complaint' );
$star_track     = Star_Electric_Shell::url( 'track-order' );

/**
 * The question set, grouped exactly as the approved page groups it.
 *
 * Answers may carry a single anchor, given as [text](url) so that the copy
 * stays readable next to the questions rather than disappearing into markup.
 */
$star_faq = array(
	'ordering' => array(
		'label'     => __( 'Ordering', 'star-electric-child' ),
		'questions' => array(
			array(
				__( 'How do I place an order?', 'star-electric-child' ),
				sprintf(
					/* translators: 1: contact page link, 2: quote request link */
					__( 'Browse the shop or search for what you need and add items to your cart. Online ordering is not open yet, so nothing is charged and no order is placed on this website: send the list through the %1$s or a %2$s and the store will confirm price, availability and collection or delivery with you. You can also buy at the counter in Saddar, Rawalpindi.', 'star-electric-child' ),
					'[' . __( 'contact form', 'star-electric-child' ) . '](' . $star_contact . ')',
					'[' . __( 'quotation request', 'star-electric-child' ) . '](' . $star_quote . ')'
				),
			),
			array(
				__( 'Do I need an account to order?', 'star-electric-child' ),
				__( 'Customer accounts are not open yet, so no account is needed. Contact the store or send a quotation request with what you need.', 'star-electric-child' ),
			),
			array(
				__( 'Can I change or cancel an order after placing it?', 'star-electric-child' ),
				__( 'Contact the store as soon as possible with your order details. Whether a change is possible depends on how far the order has progressed; the store will tell you when you get in touch.', 'star-electric-child' ),
			),
			array(
				__( 'How do I track my order?', 'star-electric-child' ),
				sprintf(
					/* translators: %s: track order page link */
					__( 'Use the %s page with your order number and the billing email used when the order was taken.', 'star-electric-child' ),
					'[' . __( 'Track Order', 'star-electric-child' ) . '](' . $star_track . ')'
				),
			),
		),
	),
	'products' => array(
		'label'     => __( 'Products', 'star-electric-child' ),
		'questions' => array(
			array(
				__( 'How do I know I am buying the right rating or size?', 'star-electric-child' ),
				__( 'Each product page lists its specifications, and variable products let you pick the rating, wattage or size you need. If you are unsure, contact us before ordering — we would rather confirm the specification than have the wrong part sent out.', 'star-electric-child' ),
			),
			array(
				__( 'Are your products genuine?', 'star-electric-child' ),
				__( 'We stock through our regular supply channels. We do not claim any brand authorisation or dealership on this site, and will only state such a relationship once it is verified.', 'star-electric-child' ),
			),
			array(
				__( 'What if a product is out of stock?', 'star-electric-child' ),
				__( 'Stock status is shown on every product card and product page. Where a supplier source publishes no availability at all, the product says so rather than claiming to be in stock. For anything marked out of stock, contact us — we may be able to source it or suggest an equivalent.', 'star-electric-child' ),
			),
			array(
				__( 'Do products come with a warranty?', 'star-electric-child' ),
				__( 'Warranty terms vary by product and manufacturer. No warranty period is stated on this site because none has been confirmed for our own sales — ask the store about a specific product before you buy.', 'star-electric-child' ),
			),
			array(
				__( 'Why do so many products say “Request a Quote” instead of a price?', 'star-electric-child' ),
				__( 'Because the source those products are catalogued from publishes no price for them. Rather than invent one, or show a zero, the product carries a quotation route instead. Send the ratings and quantities you need and the store will price them.', 'star-electric-child' ),
			),
		),
	),
	'payment'  => array(
		'label'     => __( 'Payment', 'star-electric-child' ),
		'questions' => array(
			array(
				__( 'Which payment methods do you accept?', 'star-electric-child' ),
				__( 'No payment is taken on this website. The store confirms your order and arranges payment with you directly.', 'star-electric-child' ),
			),
			array(
				__( 'Is it safe to pay online?', 'star-electric-child' ),
				__( 'No payment is taken on this website, so no card details are entered or stored here. Payment is arranged with the store directly.', 'star-electric-child' ),
			),
			array(
				__( 'Can I get an invoice for my business?', 'star-electric-child' ),
				__( 'Tell the store your company name and invoicing requirements when you get in touch, and it will confirm what it can issue.', 'star-electric-child' ),
			),
		),
	),
	'delivery' => array(
		'label'     => __( 'Delivery', 'star-electric-child' ),
		'questions' => array(
			array(
				__( 'Where do you deliver?', 'star-electric-child' ),
				__( 'Collection from the Saddar, Rawalpindi store is available. Delivery coverage beyond that is not set out on this site — ask the store what is possible for your address.', 'star-electric-child' ),
			),
			array(
				__( 'How much does delivery cost?', 'star-electric-child' ),
				__( 'No delivery charges are published, because none have been set. Any charge is agreed with you before an order is placed.', 'star-electric-child' ),
			),
			array(
				__( 'How long does delivery take?', 'star-electric-child' ),
				__( 'No delivery timeframes are published. Ask the store when you enquire and it will tell you what is realistic for the items and quantity you need.', 'star-electric-child' ),
			),
			array(
				__( 'Can I collect from the store instead?', 'star-electric-child' ),
				__( 'Yes. Orders can be collected from the Saddar, Rawalpindi store. Contact the store to arrange a time.', 'star-electric-child' ),
			),
		),
	),
	'returns'  => array(
		'label'     => __( 'Returns', 'star-electric-child' ),
		'questions' => array(
			array(
				__( 'Can I return a product?', 'star-electric-child' ),
				__( 'Return terms are not published yet. If something is faulty, damaged or not what you ordered, contact the store and it will tell you how it can be put right.', 'star-electric-child' ),
			),
			array(
				__( 'What if I receive a damaged or faulty item?', 'star-electric-child' ),
				sprintf(
					/* translators: %s: complaint form link */
					__( 'Tell us as soon as you notice the problem, using the %s. Describe what arrived and what is wrong with it; the store will come back to you about photographs or an inspection if it needs them.', 'star-electric-child' ),
					'[' . __( 'complaint form', 'star-electric-child' ) . '](' . $star_complaint . ')'
				),
			),
			array(
				__( 'How are refunds issued?', 'star-electric-child' ),
				__( 'Refund arrangements are not published yet. Contact the store and it will explain what applies to your purchase.', 'star-electric-child' ),
			),
		),
	),
	'bulk'     => array(
		'label'     => __( 'Bulk Orders', 'star-electric-child' ),
		'questions' => array(
			array(
				__( 'Do you supply contractors and electricians?', 'star-electric-child' ),
				sprintf(
					/* translators: %s: quote request link */
					__( 'Yes. Contractors, electricians, builders and commercial projects are a core part of what we do. Use the %s to send an item list or bill of quantities.', 'star-electric-child' ),
					'[' . __( 'quote request form', 'star-electric-child' ) . '](' . $star_quote . ')'
				),
			),
			array(
				__( 'Is there a minimum order for a bulk quote?', 'star-electric-child' ),
				__( 'No minimum order value or quantity is published. Send your list and the store will advise.', 'star-electric-child' ),
			),
			array(
				__( 'How quickly will I get a quotation?', 'star-electric-child' ),
				__( 'No turnaround time is published. Larger or more specialised lists take longer to price accurately; the store will tell you what to expect when it receives yours.', 'star-electric-child' ),
			),
			array(
				__( 'Can you deliver in phases to a site?', 'star-electric-child' ),
				__( 'Mention phased delivery in the notes on your quotation request and the store will tell you whether it can be arranged for your order.', 'star-electric-child' ),
			),
		),
	),
);

/**
 * Turn the single [text](url) anchor an answer may carry into a real link.
 *
 * Deliberately narrow: it escapes first and only then puts back the one tag it
 * knows about, so an answer can never smuggle markup onto the page.
 *
 * @param string $answer Answer text.
 */
$star_answer = static function ( string $answer ): string {
	return (string) preg_replace(
		'/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/',
		'<a class="link-inline" href="$2">$1</a>',
		esc_html( $answer )
	);
};

Star_Electric_Shell::page_head(
	array(
		__( 'Home', 'star-electric-child' ) => home_url( '/' ),
		__( 'FAQs', 'star-electric-child' ) => '',
	),
	__( 'Frequently Asked Questions', 'star-electric-child' ),
	__( 'Common questions about ordering, products, payment, delivery, returns and bulk supply.', 'star-electric-child' )
);
?>

<section class="section section--sm">
	<div class="container container--mid">

		<div class="alert alert--info" style="margin-bottom:32px">
			<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span>
				<strong><?php esc_html_e( 'Where an answer says something is not published, that is deliberate.', 'star-electric-child' ); ?></strong>
				<?php esc_html_e( 'Delivery charges, timeframes, return windows and warranty terms are agreed with the store directly rather than stated here before they are set.', 'star-electric-child' ); ?>
			</span>
		</div>

		<div class="faq-cats" id="faqCats" role="group" aria-label="<?php esc_attr_e( 'Filter questions by topic', 'star-electric-child' ); ?>">
			<button type="button" data-faq-cat="all" aria-pressed="true"><?php esc_html_e( 'All questions', 'star-electric-child' ); ?></button>
			<?php foreach ( $star_faq as $star_key => $star_group ) : ?>
				<button type="button" data-faq-cat="<?php echo esc_attr( $star_key ); ?>" aria-pressed="false">
					<?php echo esc_html( $star_group['label'] ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php $star_n = 0; ?>
		<?php foreach ( $star_faq as $star_key => $star_group ) : ?>
			<div data-faq-group="<?php echo esc_attr( $star_key ); ?>">
				<h2 class="section__title" style="font-size:18px;margin-bottom:16px"><?php echo esc_html( $star_group['label'] ); ?></h2>
				<div class="accordion" style="margin-bottom:40px">
					<?php foreach ( $star_group['questions'] as $star_item ) : ?>
						<?php ++$star_n; ?>
						<div class="acc-item">
							<button class="acc-item__btn" type="button" data-toggle-panel aria-expanded="false" aria-controls="f<?php echo esc_attr( (string) $star_n ); ?>">
								<?php echo esc_html( $star_item[0] ); ?>
								<?php echo Star_Electric_Shell::icon( 'chevdown' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</button>
							<div class="acc-item__panel" id="f<?php echo esc_attr( (string) $star_n ); ?>" hidden>
								<?php echo wp_kses_post( $star_answer( $star_item[1] ) ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="empty-state" style="background:var(--navy-50);border-style:solid;border-color:var(--navy-100)">
			<span class="empty-state__ico">
				<?php echo Star_Electric_Shell::icon( 'headset' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
			<h2><?php esc_html_e( 'Still have a question?', 'star-electric-child' ); ?></h2>
			<p><?php esc_html_e( 'If your question is not covered here, get in touch and we will answer it directly.', 'star-electric-child' ); ?></p>
			<div class="btn-row">
				<a class="btn btn--accent" href="<?php echo esc_url( $star_contact ); ?>"><?php esc_html_e( 'Contact Us', 'star-electric-child' ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_url( $star_quote ); ?>"><?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?></a>
			</div>
		</div>

	</div>
</section>

<?php
get_footer();
