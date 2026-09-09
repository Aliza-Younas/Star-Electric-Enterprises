<?php
/**
 * Home — Deals & Promotions.
 *
 * Three promo cards above the discount rail. The rail itself is every product
 * whose own source publishes a reduced price, biggest reduction first - the
 * store marks nothing down, so there is deliberately no control here to invent
 * an offer, a countdown or a discount.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deals widget.
 */
class Star_Electric_Widget_Deals extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-deals';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Home — Deals & Promotions', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-price-table';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_head_section',
			array( 'label' => __( 'Section heading', 'star-electric' ) )
		);

		$this->note(
			__( 'The rail below shows only reductions the product’s own source publishes. There is no control to create an offer here, because the store does not mark anything down.', 'star-electric' )
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Small label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Current offers', 'star-electric' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Deals & Promotions', 'star-electric' ),
			)
		);
		$this->add_control(
			'sub',
			array(
				'label'   => __( 'Supporting text', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'Every reduction here is one the product’s own source publishes. Nothing is marked down by us.', 'star-electric' ),
			)
		);
		$this->add_control(
			'link_label',
			array(
				'label'   => __( 'Link text', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'All deals', 'star-electric' ),
			)
		);
		$this->add_control(
			'rail_title',
			array(
				'label'   => __( 'Rail heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Discounted at source', 'star-electric' ),
			)
		);
		$this->add_control(
			'rail_limit',
			array(
				'label'   => __( 'Products in the rail', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 4,
				'max'     => 24,
				'default' => 10,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_cards_section',
			array( 'label' => __( 'Promo cards', 'star-electric' ) )
		);

		$card = new \Elementor\Repeater();

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
		$card->add_control(
			'cta',
			array(
				'label' => __( 'Button label', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$card->add_control(
			'href',
			array(
				'label' => __( 'Button link', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);
		$card->add_control(
			'variant',
			array(
				'label'   => __( 'Card style', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					''     => __( 'Light', 'star-electric' ),
					'dark' => __( 'Dark', 'star-electric' ),
				),
				'default' => 'dark',
			)
		);
		$card->add_control(
			'button',
			array(
				'label'   => __( 'Button colour', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'btn--accent'  => __( 'Red', 'star-electric' ),
					'btn--primary' => __( 'Navy', 'star-electric' ),
				),
				'default' => 'btn--accent',
			)
		);
		$card->add_control(
			'term',
			array(
				'label'       => __( 'Department picture', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'A department slug. Leave empty to use the banner image below.', 'star-electric' ),
			)
		);
		$card->add_control(
			'image',
			array(
				'label'       => __( 'Banner image name', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'An approved banner: promo-lighting, promo-protection or promo-tools.', 'star-electric' ),
				'condition'   => array( 'term' => '' ),
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Cards', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $card->get_controls(),
				'default'     => $this->card_defaults(),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * The approved cards, shaped for the repeater.
	 *
	 * @return array
	 */
	private function card_defaults(): array {
		$out = array();
		foreach ( Star_Electric_Sections::default_deal_cards() as $row ) {
			$out[] = array(
				'title'   => $row['title'],
				'text'    => $row['text'],
				'cta'     => $row['cta'],
				'href'    => array( 'url' => $row['href'] ),
				'variant' => $row['variant'],
				'button'  => $row['button'],
				'term'    => $row['term'] ?? '',
				'image'   => $row['image'] ?? '',
			);
		}
		return $out;
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$cards = $this->rows(
			(array) ( $s['cards'] ?? array() ),
			function ( array $row ): array {
				return array(
					'variant' => (string) ( $row['variant'] ?? '' ),
					'term'    => (string) ( $row['term'] ?? '' ),
					'image'   => (string) ( $row['image'] ?? '' ),
					'title'   => (string) ( $row['title'] ?? '' ),
					'text'    => (string) ( $row['text'] ?? '' ),
					'cta'     => (string) ( $row['cta'] ?? '' ),
					'button'  => (string) ( $row['button'] ?? 'btn--accent' ),
					'href'    => $this->url( $row['href'] ?? array(), home_url( '/shop/' ) ),
				);
			}
		);

		Star_Electric_Sections::deals(
			array(
				'eyebrow'    => (string) ( $s['eyebrow'] ?? '' ),
				'title'      => (string) ( $s['title'] ?? '' ),
				'sub'        => (string) ( $s['sub'] ?? '' ),
				'link_label' => (string) ( $s['link_label'] ?? '' ),
				'cards'      => $cards ? $cards : Star_Electric_Sections::default_deal_cards(),
				'rail_title' => (string) ( $s['rail_title'] ?? '' ),
				'rail_limit' => max( 4, min( 24, (int) ( $s['rail_limit'] ?? 10 ) ) ),
			)
		);
	}
}
