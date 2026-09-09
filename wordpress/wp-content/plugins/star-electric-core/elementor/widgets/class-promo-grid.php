<?php
/**
 * Promo Grid.
 *
 * The three wide cards the deals page opens with.
 *
 * A card takes its picture from a department, from the approved banner artwork,
 * or from an image chosen here - in that order, so the approved artwork stays
 * unless it is replaced deliberately.
 *
 * These are signposts into the catalogue, not offers. Nothing here should claim
 * a discount, a price or an offer period; the products below say what their own
 * sources publish.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promo grid widget.
 */
class Star_Electric_Widget_Promo_Grid extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-promo-grid';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Promo Grid', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-image-box';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Cards', 'star-electric' ) )
		);

		$this->note(
			__( 'Signposts into the catalogue, not offers. Please do not write a price, a discount or an offer period into one.', 'star-electric' )
		);

		$card = new \Elementor\Repeater();
		$card->add_control(
			'title',
			array( 'label' => __( 'Heading', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$card->add_control(
			'text',
			array(
				'label' => __( 'Text', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);
		$card->add_control(
			'label',
			array( 'label' => __( 'Button label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$card->add_control(
			'url',
			array( 'label' => __( 'Button link', 'star-electric' ), 'type' => \Elementor\Controls_Manager::URL )
		);
		$card->add_control(
			'style',
			array(
				'label'   => __( 'Button style', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'accent',
				'options' => array(
					'accent'  => __( 'Primary (red)', 'star-electric' ),
					'primary' => __( 'Navy', 'star-electric' ),
					'ghost'   => __( 'Outline', 'star-electric' ),
				),
			)
		);
		$card->add_control(
			'dark',
			array(
				'label'        => __( 'Dark card', 'star-electric' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);
		$card->add_control(
			'department',
			array(
				'label'       => __( 'Department picture', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'A department slug, e.g. fans-ventilation. Its own photograph is used.', 'star-electric' ),
			)
		);
		$card->add_control(
			'banner',
			array(
				'label'       => __( 'Approved artwork name', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'promo-lighting or promo-protection. Ignored when a department is set.', 'star-electric' ),
			)
		);
		$card->add_control(
			'image',
			array(
				'label'       => __( 'Or an image of your own', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => __( 'Leave empty to keep the approved artwork.', 'star-electric' ),
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Cards', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $card->get_controls(),
				'default'     => array(),
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

		Star_Electric_Sections::promo_grid(
			array(
				'cards' => $this->rows(
					(array) ( $s['cards'] ?? array() ),
					function ( array $row ): array {
						$image = is_array( $row['image'] ?? null ) ? (string) ( $row['image']['url'] ?? '' ) : '';
						return array(
							'title'      => (string) ( $row['title'] ?? '' ),
							'text'       => (string) ( $row['text'] ?? '' ),
							'label'      => (string) ( $row['label'] ?? '' ),
							'url'        => $this->url( $row['url'] ?? array(), home_url( '/' ) ),
							'style'      => (string) ( $row['style'] ?? 'accent' ),
							'dark'       => 'yes' === (string) ( $row['dark'] ?? '' ),
							'department' => trim( (string) ( $row['department'] ?? '' ) ),
							'banner'     => trim( (string) ( $row['banner'] ?? '' ) ),
							'image'      => $image,
						);
					}
				),
			)
		);
	}
}
