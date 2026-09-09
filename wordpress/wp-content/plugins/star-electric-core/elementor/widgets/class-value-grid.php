<?php
/**
 * Value Grid.
 *
 * A tinted band of icon cards describing how the business works, with a
 * standing note beneath it.
 *
 * The note is the About page's record of what it deliberately does not claim -
 * no establishment year, customer count, award, certification, dealership or
 * partnership. It is content rather than decoration, and it should stay until
 * the business confirms the facts it is holding open.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Value grid widget.
 */
class Star_Electric_Widget_Value_Grid extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-value-grid';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Value Grid', 'star-electric' );
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
			'star_head',
			array( 'label' => __( 'Heading', 'star-electric' ) )
		);

		$this->add_control(
			'eyebrow',
			array( 'label' => __( 'Small label above', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$this->add_control(
			'title',
			array( 'label' => __( 'Heading', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$this->add_control(
			'sub',
			array(
				'label' => __( 'Supporting line', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_cards',
			array( 'label' => __( 'Cards', 'star-electric' ) )
		);

		$card = new \Elementor\Repeater();
		$card->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'shield',
				'options' => Star_Electric_Widget_Trust_Strip::icon_options(),
			)
		);
		$card->add_control(
			'title',
			array( 'label' => __( 'Heading', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$card->add_control(
			'text',
			array(
				'label' => __( 'Description', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 3,
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

		$this->start_controls_section(
			'star_note_section',
			array( 'label' => __( 'Standing note', 'star-electric' ) )
		);

		$this->note(
			__( 'This note records what the page deliberately does not claim. Please leave it in place until the business confirms those facts.', 'star-electric' )
		);

		$this->add_control(
			'note_text',
			array(
				'label'       => __( 'Note', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'description' => __( 'Leave empty to remove the note.', 'star-electric' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::value_grid(
			array(
				'eyebrow' => (string) ( $s['eyebrow'] ?? '' ),
				'title'   => (string) ( $s['title'] ?? '' ),
				'sub'     => (string) ( $s['sub'] ?? '' ),
				'cards'   => $this->rows(
					(array) ( $s['cards'] ?? array() ),
					static function ( array $row ): array {
						return array(
							'icon'  => (string) ( $row['icon'] ?? 'info' ),
							'title' => (string) ( $row['title'] ?? '' ),
							'text'  => (string) ( $row['text'] ?? '' ),
						);
					}
				),
				'note'    => (string) ( $s['note_text'] ?? '' ),
			)
		);
	}
}
