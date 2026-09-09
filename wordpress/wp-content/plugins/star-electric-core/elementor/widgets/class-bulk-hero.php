<?php
/**
 * Bulk Hero.
 *
 * The navy band the quote page opens with: a light breadcrumb, a heading, a
 * paragraph, a row of audience tags and the three numbered steps.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bulk hero widget.
 */
class Star_Electric_Widget_Bulk_Hero extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-bulk-hero';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Bulk Hero', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-banner';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Hero', 'star-electric' ) )
		);

		$this->add_control(
			'title',
			array( 'label' => __( 'Heading', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$this->add_control(
			'text',
			array(
				'label' => __( 'Text', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 4,
			)
		);
		$this->add_control(
			'heading_id',
			array(
				'label'       => __( 'Heading id', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'quoteTitle',
				'description' => __( 'Screen readers use this to announce the section.', 'star-electric' ),
			)
		);

		$crumb = new \Elementor\Repeater();
		$crumb->add_control(
			'label',
			array( 'label' => __( 'Label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$crumb->add_control(
			'url',
			array(
				'label'       => __( 'Link', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'description' => __( 'Leave the last one empty - it is the current page.', 'star-electric' ),
			)
		);
		$this->add_control(
			'crumbs',
			array(
				'label'       => __( 'Breadcrumb', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $crumb->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ label }}}',
			)
		);

		$tag = new \Elementor\Repeater();
		$tag->add_control(
			'title',
			array( 'label' => __( 'Tag', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$this->add_control(
			'tags',
			array(
				'label'       => __( 'Who it is for', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $tag->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_steps_section',
			array( 'label' => __( 'Steps', 'star-electric' ) )
		);

		$this->note( __( 'The 01, 02, 03 numbers are added automatically.', 'star-electric' ) );

		$step = new \Elementor\Repeater();
		$step->add_control(
			'title',
			array( 'label' => __( 'Step', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$this->add_control(
			'steps',
			array(
				'label'       => __( 'Steps', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $step->get_controls(),
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

		$crumbs = array();
		foreach ( (array) ( $s['crumbs'] ?? array() ) as $row ) {
			$label = trim( (string) ( $row['label'] ?? '' ) );
			if ( '' !== $label ) {
				$crumbs[] = array( 'label' => $label, 'url' => $this->url( $row['url'] ?? array() ) );
			}
		}

		$list = static function ( $rows ): array {
			$out = array();
			foreach ( (array) $rows as $row ) {
				$text = trim( (string) ( $row['title'] ?? '' ) );
				if ( '' !== $text ) {
					$out[] = $text;
				}
			}
			return $out;
		};

		Star_Electric_Sections::bulk_hero(
			array(
				'crumbs'     => $crumbs,
				'title'      => (string) ( $s['title'] ?? '' ),
				'text'       => (string) ( $s['text'] ?? '' ),
				'tags'       => $list( $s['tags'] ?? array() ),
				'steps'      => $list( $s['steps'] ?? array() ),
				'heading_id' => (string) ( $s['heading_id'] ?? '' ) ?: 'quoteTitle',
			)
		);
	}
}
