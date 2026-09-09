<?php
/**
 * Why Choose Us.
 *
 * A card grid where each card is a repeater row: icon, heading, description.
 * Cards can be reworded, reordered, added or removed from the Elementor panel.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Why choose us widget.
 */
class Star_Electric_Widget_Why_Choose_Us extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-why-choose-us';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Why Choose Us', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-info-box';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_head_section',
			array( 'label' => __( 'Section heading', 'star-electric' ) )
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Small label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Our approach', 'star-electric' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Why Choose Star Electric Enterprises', 'star-electric' ),
			)
		);
		$this->add_control(
			'sub',
			array(
				'label'   => __( 'Supporting text', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => __( 'How we work with retail customers and trade buyers.', 'star-electric' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_cards_section',
			array( 'label' => __( 'Cards', 'star-electric' ) )
		);

		$card = new \Elementor\Repeater();

		$card->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => Star_Electric_Widget_Trust_Strip::icon_options(),
				'default' => 'shield',
			)
		);
		$card->add_control(
			'title',
			array(
				'label' => __( 'Card heading', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$card->add_control(
			'text',
			array(
				'label' => __( 'Card text', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 2,
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Cards', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $card->get_controls(),
				'default'     => Star_Electric_Sections::default_why_cards(),
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

		$cards = $this->rows(
			(array) ( $s['cards'] ?? array() ),
			static function ( array $row ): array {
				return array(
					'icon'  => (string) ( $row['icon'] ?? 'shield' ),
					'title' => (string) ( $row['title'] ?? '' ),
					'text'  => (string) ( $row['text'] ?? '' ),
				);
			}
		);

		Star_Electric_Sections::why_choose_us(
			array(
				'eyebrow' => (string) ( $s['eyebrow'] ?? '' ),
				'title'   => (string) ( $s['title'] ?? '' ),
				'sub'     => (string) ( $s['sub'] ?? '' ),
				'cards'   => $cards ? $cards : Star_Electric_Sections::default_why_cards(),
			)
		);
	}
}
