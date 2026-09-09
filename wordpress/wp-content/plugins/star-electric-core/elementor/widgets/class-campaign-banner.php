<?php
/**
 * Home — Campaign Banner.
 *
 * The department rail, the campaign carousel and the three promo tiles beneath
 * it. They are one widget because the approved stylesheet lays them out as a
 * single grid: .hcom__grid puts the rail in the first column and the banner and
 * tiles, stacked, in the second. Split into three top-level Elementor sections
 * they would no longer be that grid.
 *
 * Each of the three still has its own controls, so a campaign, a tile or the
 * page heading can be edited without touching the others.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Campaign banner widget.
 */
class Star_Electric_Widget_Campaign_Banner extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-campaign-banner';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Home — Campaign Banner', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-slides';
	}

	/**
	 * The tones the approved design defines for a slide.
	 *
	 * @return array<string,string>
	 */
	private function tones(): array {
		return array(
			'navy' => __( 'Navy', 'star-electric' ),
			'deep' => __( 'Deep', 'star-electric' ),
			'ink'  => __( 'Ink', 'star-electric' ),
		);
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_heading_section',
			array( 'label' => __( 'Page heading', 'star-electric' ) )
		);

		$this->note(
			__( 'The heading below is read by screen readers and search engines. It is not shown on screen, which is why the campaign titles carry the visible wording.', 'star-electric' )
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Accessible page heading (H1)', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => __( 'Star Electric Enterprises — electrical supplies, wiring, protection, lighting and fans', 'star-electric' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------- campaigns */
		$this->start_controls_section(
			'star_campaigns_section',
			array( 'label' => __( 'Campaign slides', 'star-electric' ) )
		);

		$slide = new \Elementor\Repeater();

		$slide->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Label above the headline', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Circuit Protection', 'star-electric' ),
			)
		);
		$slide->add_control(
			'title',
			array(
				'label'   => __( 'Headline', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Built to Protect Every Circuit', 'star-electric' ),
			)
		);
		$slide->add_control(
			'text',
			array(
				'label' => __( 'Supporting text', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);
		$slide->add_control(
			'cta',
			array(
				'label'   => __( 'Button label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Shop now', 'star-electric' ),
			)
		);
		$slide->add_control(
			'href',
			array(
				'label'       => __( 'Button link', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => home_url( '/shop/' ),
			)
		);
		$slide->add_control(
			'cta2',
			array(
				'label'       => __( 'Second button label', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Leave empty for a single button.', 'star-electric' ),
			)
		);
		$slide->add_control(
			'href2',
			array(
				'label'     => __( 'Second button link', 'star-electric' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'condition' => array( 'cta2!' => '' ),
			)
		);
		$slide->add_control(
			'tone',
			array(
				'label'   => __( 'Colour tone', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->tones(),
				'default' => 'navy',
			)
		);
		$slide->add_control(
			'image',
			array(
				'label'       => __( 'Campaign image', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => __( 'Leave empty to keep the approved artwork for this campaign.', 'star-electric' ),
			)
		);
		$slide->add_control(
			'art',
			array(
				'label'       => __( 'Approved artwork name', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'protection',
				'description' => __( 'Used when no image is chosen: protection, cables, switches, lighting or smart.', 'star-electric' ),
			)
		);

		$defaults = array();
		foreach ( Star_Electric_Sections::default_campaigns() as $row ) {
			$defaults[] = array(
				'eyebrow' => $row['eyebrow'],
				'title'   => $row['title'],
				'text'    => $row['text'],
				'cta'     => $row['cta'],
				'href'    => array( 'url' => $row['href'] ),
				'cta2'    => $row['cta2'] ?? '',
				'href2'   => array( 'url' => $row['href2'] ?? '' ),
				'tone'    => $row['tone'],
				'art'     => $row['art'],
			);
		}

		$this->add_control(
			'campaigns',
			array(
				'label'       => __( 'Slides', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $slide->get_controls(),
				'default'     => $defaults,
				'title_field' => '{{{ eyebrow }}}',
			)
		);

		$this->end_controls_section();

		/* ----------------------------------------------------- promo tiles */
		$this->start_controls_section(
			'star_tiles_section',
			array( 'label' => __( 'Promo tiles', 'star-electric' ) )
		);

		$tile = new \Elementor\Repeater();

		$tile->add_control(
			'kicker',
			array(
				'label' => __( 'Small label', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$tile->add_control(
			'title',
			array(
				'label' => __( 'Tile heading', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$tile->add_control(
			'cta',
			array(
				'label' => __( 'Link text', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$tile->add_control(
			'href',
			array(
				'label' => __( 'Link', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);
		$tile->add_control(
			'image',
			array(
				'label'       => __( 'Tile image', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => __( 'Leave empty to use the department picture below.', 'star-electric' ),
			)
		);
		$tile->add_control(
			'term',
			array(
				'label'       => __( 'Department picture', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'A department slug, for example circuit-protection.', 'star-electric' ),
			)
		);

		$tile_defaults = array();
		foreach ( Star_Electric_Sections::default_promo_tiles() as $row ) {
			$tile_defaults[] = array(
				'kicker' => $row['kicker'],
				'title'  => $row['title'],
				'cta'    => $row['cta'],
				'href'   => array( 'url' => $row['href'] ),
				'term'   => $row['term'],
			);
		}

		$this->add_control(
			'tiles',
			array(
				'label'       => __( 'Tiles', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $tile->get_controls(),
				'default'     => $tile_defaults,
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

		$campaigns = $this->rows(
			(array) ( $s['campaigns'] ?? array() ),
			function ( array $row ): array {
				return array(
					'tone'    => (string) ( $row['tone'] ?? 'navy' ),
					'art'     => (string) ( $row['art'] ?? 'protection' ),
					'image'   => (string) ( $row['image']['url'] ?? '' ),
					'eyebrow' => (string) ( $row['eyebrow'] ?? '' ),
					'title'   => (string) ( $row['title'] ?? '' ),
					'text'    => (string) ( $row['text'] ?? '' ),
					'cta'     => (string) ( $row['cta'] ?? '' ),
					'href'    => $this->url( $row['href'] ?? array(), home_url( '/shop/' ) ),
					'cta2'    => (string) ( $row['cta2'] ?? '' ),
					'href2'   => $this->url( $row['href2'] ?? array() ),
				);
			}
		);

		$tiles = $this->rows(
			(array) ( $s['tiles'] ?? array() ),
			function ( array $row ): array {
				return array(
					'kicker' => (string) ( $row['kicker'] ?? '' ),
					'title'  => (string) ( $row['title'] ?? '' ),
					'cta'    => (string) ( $row['cta'] ?? '' ),
					'href'   => $this->url( $row['href'] ?? array(), home_url( '/shop/' ) ),
					'image'  => (string) ( $row['image']['url'] ?? '' ),
					'term'   => (string) ( $row['term'] ?? '' ),
				);
			}
		);

		Star_Electric_Sections::campaign_banner(
			array(
				'heading'   => (string) ( $s['heading'] ?? '' ),
				'campaigns' => $campaigns ? $campaigns : Star_Electric_Sections::default_campaigns(),
				'tiles'     => $tiles ? $tiles : Star_Electric_Sections::default_promo_tiles(),
			)
		);
	}
}
