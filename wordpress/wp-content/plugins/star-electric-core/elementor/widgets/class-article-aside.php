<?php
/**
 * Article & Sidebar.
 *
 * The body of a written page: an article of headings and paragraphs beside a
 * sidebar of two small cards - a list of facts, and a card of links.
 *
 * The article is a list of blocks rather than one rich-text field on purpose.
 * Each heading and each paragraph is its own row, so they can be reworded,
 * reordered or removed one at a time, and one of the block types is a
 * department list that reads the live catalogue instead of being typed out.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Article and sidebar widget.
 */
class Star_Electric_Widget_Article_Aside extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-article-aside';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Article & Sidebar', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-sidebar';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {

		/* ------------------------------------------------------ the article */
		$this->start_controls_section(
			'star_article',
			array( 'label' => __( 'Article', 'star-electric' ) )
		);

		$this->note(
			__( 'Write {departments} where the number of departments belongs and {site} where the business name belongs. Both are filled in from the catalogue, so the page cannot disagree with the shop.', 'star-electric' )
		);

		$block = new \Elementor\Repeater();
		$block->add_control(
			'kind',
			array(
				'label'   => __( 'Block', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'heading'     => __( 'Heading', 'star-electric' ),
					'text'        => __( 'Paragraph', 'star-electric' ),
					'departments' => __( 'Department list (from the catalogue)', 'star-electric' ),
				),
			)
		);
		$block->add_control(
			'text',
			array(
				'label'     => __( 'Text', 'star-electric' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'rows'      => 5,
				'condition' => array( 'kind!' => 'departments' ),
			)
		);

		$this->add_control(
			'blocks',
			array(
				'label'       => __( 'Blocks', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $block->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ kind === "departments" ? "Department list" : text.substr(0, 48) }}}',
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------- the facts card */
		$this->start_controls_section(
			'star_facts',
			array( 'label' => __( 'Sidebar: facts card', 'star-electric' ) )
		);

		$this->note(
			__( 'Only facts the business has confirmed belong here. Leave the heading empty to remove the card.', 'star-electric' )
		);

		$this->add_control(
			'facts_title',
			array(
				'label' => __( 'Card heading', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$fact = new \Elementor\Repeater();
		$fact->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'info',
				'options' => Star_Electric_Widget_Trust_Strip::icon_options(),
			)
		);
		$fact->add_control(
			'label',
			array( 'label' => __( 'Label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$fact->add_control(
			'value',
			array(
				'label' => __( 'Value', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);

		$this->add_control(
			'facts',
			array(
				'label'       => __( 'Facts', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $fact->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------- the links card */
		$this->start_controls_section(
			'star_links',
			array( 'label' => __( 'Sidebar: links card', 'star-electric' ) )
		);

		$this->add_control(
			'links_title',
			array(
				'label'       => __( 'Card heading', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Leave empty to remove the card.', 'star-electric' ),
			)
		);
		$this->add_control(
			'links_text',
			array(
				'label' => __( 'Intro line', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);

		$link = new \Elementor\Repeater();
		$link->add_control(
			'label',
			array( 'label' => __( 'Button label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$link->add_control(
			'url',
			array( 'label' => __( 'Link', 'star-electric' ), 'type' => \Elementor\Controls_Manager::URL )
		);
		$link->add_control(
			'style',
			array(
				'label'   => __( 'Style', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ghost',
				'options' => array(
					'accent' => __( 'Primary (red)', 'star-electric' ),
					'ghost'  => __( 'Outline', 'star-electric' ),
				),
			)
		);

		$this->add_control(
			'links',
			array(
				'label'       => __( 'Buttons', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $link->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$blocks = array();
		foreach ( (array) ( $s['blocks'] ?? array() ) as $row ) {
			$kind = (string) ( $row['kind'] ?? 'text' );
			$text = trim( (string) ( $row['text'] ?? '' ) );
			if ( 'departments' !== $kind && '' === $text ) {
				continue;
			}
			$blocks[] = array( 'kind' => $kind, 'text' => $text );
		}

		$facts = $this->rows(
			(array) ( $s['facts'] ?? array() ),
			static function ( array $row ): array {
				return array(
					'icon'  => (string) ( $row['icon'] ?? 'info' ),
					'label' => (string) ( $row['label'] ?? '' ),
					'value' => (string) ( $row['value'] ?? '' ),
				);
			},
			'label'
		);

		$links = $this->rows(
			(array) ( $s['links'] ?? array() ),
			function ( array $row ): array {
				return array(
					'label' => (string) ( $row['label'] ?? '' ),
					'url'   => $this->url( $row['url'] ?? array(), home_url( '/' ) ),
					'style' => (string) ( $row['style'] ?? 'ghost' ),
				);
			},
			'label'
		);

		Star_Electric_Sections::article_aside(
			array(
				'blocks'      => $blocks,
				'facts_title' => (string) ( $s['facts_title'] ?? '' ),
				'facts'       => $facts,
				'links_title' => (string) ( $s['links_title'] ?? '' ),
				'links_text'  => (string) ( $s['links_text'] ?? '' ),
				'links'       => $links,
			)
		);
	}
}
