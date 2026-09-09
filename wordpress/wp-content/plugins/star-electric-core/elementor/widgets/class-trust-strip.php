<?php
/**
 * Home — Trust Strip.
 *
 * The three-up strip of assurances beneath the campaign banner. Each item is a
 * row in a repeater, so one can be reworded, reordered or removed without
 * touching the others.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trust strip widget.
 */
class Star_Electric_Widget_Trust_Strip extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-trust-strip';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Home — Trust Strip', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-check-circle';
	}

	/**
	 * The icon set the approved design ships.
	 *
	 * Elementor's own icon library is deliberately not offered: these are the
	 * design system's inline SVGs, and mixing in a different set would be a
	 * visible change.
	 *
	 * @return array<string,string>
	 */
	public static function icon_options(): array {
		return array(
			'shield'   => __( 'Shield', 'star-electric' ),
			'tag'      => __( 'Tag', 'star-electric' ),
			'headset'  => __( 'Headset', 'star-electric' ),
			'bolt'     => __( 'Bolt', 'star-electric' ),
			'box'      => __( 'Box', 'star-electric' ),
			'pin'      => __( 'Location pin', 'star-electric' ),
			'truck'    => __( 'Truck', 'star-electric' ),
			'check'    => __( 'Check', 'star-electric' ),
			'doc'      => __( 'Document', 'star-electric' ),
			'info'     => __( 'Information', 'star-electric' ),
			'lock'     => __( 'Lock', 'star-electric' ),
			'star'     => __( 'Star', 'star-electric' ),
			'refresh'  => __( 'Refresh', 'star-electric' ),
			'building' => __( 'Building', 'star-electric' ),
			'user'     => __( 'Person', 'star-electric' ),
			'mail'     => __( 'Envelope', 'star-electric' ),
			'map'      => __( 'Map', 'star-electric' ),
			'clock'    => __( 'Clock', 'star-electric' ),
			'phone'    => __( 'Phone', 'star-electric' ),
		);
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_items_section',
			array( 'label' => __( 'Items', 'star-electric' ) )
		);

		$item = new \Elementor\Repeater();

		$item->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => self::icon_options(),
				'default' => 'shield',
			)
		);
		$item->add_control(
			'title',
			array(
				'label' => __( 'Heading', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$item->add_control(
			'text',
			array(
				'label' => __( 'Supporting line', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Strip items', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $item->get_controls(),
				'default'     => Star_Electric_Sections::default_trust_items(),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$items = $this->rows(
			(array) ( $s['items'] ?? array() ),
			static function ( array $row ): array {
				return array(
					'icon'  => (string) ( $row['icon'] ?? 'shield' ),
					'title' => (string) ( $row['title'] ?? '' ),
					'text'  => (string) ( $row['text'] ?? '' ),
				);
			}
		);

		Star_Electric_Sections::trust_strip(
			array( 'items' => $items ? $items : Star_Electric_Sections::default_trust_items() )
		);
	}
}
