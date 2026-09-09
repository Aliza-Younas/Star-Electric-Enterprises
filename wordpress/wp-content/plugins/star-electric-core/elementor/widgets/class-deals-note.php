<?php
/**
 * Deals Note.
 *
 * The standing note that closes the deals page.
 *
 * No countdown, no urgency, no offer period: every discount shown is one the
 * product's own source publishes, taken on the date recorded against that
 * product. Please leave this note in place - it is what keeps the page honest
 * about where its prices come from.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deals note widget.
 */
class Star_Electric_Widget_Deals_Note extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-deals-note';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Deals Note', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-info-circle-o';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Note', 'star-electric' ) )
		);

		$this->note(
			__( 'This note is why the page can show reductions at all: they are the source\'s, not the store\'s. Please leave it in place.', 'star-electric' )
		);

		$this->area( 'strong', __( 'First sentence (bold)', 'star-electric' ), __( 'No countdown timers or urgency claims are used on this page.', 'star-electric' ), 3 );
		$this->area( 'text', __( 'Rest of the note', 'star-electric' ), __( 'Every discount shown is one the product’s own source publishes: the previous price and the current price are both taken from that source on the date recorded against the product. Nothing is marked down here, and no offer period is claimed.', 'star-electric' ), 5 );

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::deals_note(
			array(
				'strong' => (string) ( $s['strong'] ?? '' ),
				'text'   => (string) ( $s['text'] ?? '' ),
			)
		);
	}
}
