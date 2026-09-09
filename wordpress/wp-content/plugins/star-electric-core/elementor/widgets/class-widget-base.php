<?php
/**
 * The shared base for every Star Electric Elementor widget.
 *
 * Each widget is a thin wrapper: it collects content from Elementor controls
 * and hands it to Star_Electric_Sections, which renders the approved markup.
 * No widget contains markup of its own, so a change to a section is made in one
 * place and both the Elementor page and any PHP template that still calls it
 * pick it up together.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base widget.
 */
abstract class Star_Electric_Widget_Base extends \Elementor\Widget_Base {

	/**
	 * Which panel category this widget belongs to.
	 *
	 * @return string[]
	 */
	public function get_categories(): array {
		return array( Star_Electric_Elementor::CATEGORY );
	}

	/**
	 * Search terms that find this widget in the panel.
	 *
	 * @return string[]
	 */
	public function get_keywords(): array {
		return array( 'star', 'electric', 'shop', 'catalogue' );
	}

	/**
	 * The approved stylesheets are enqueued by the child theme on every page,
	 * so a widget needs no styles of its own. Saying so explicitly keeps
	 * Elementor from being asked to print any.
	 *
	 * @return string[]
	 */
	public function get_style_depends(): array {
		return array();
	}

	/**
	 * Repeater rows as plain arrays, with empty rows dropped.
	 *
	 * An editor who clears every field of a row means to remove it; leaving a
	 * blank card on a live page would be worse than honouring that.
	 *
	 * @param array    $rows     Repeater settings.
	 * @param callable $map      Maps one row to the section's own shape.
	 * @param string   $required Field that must be present for the row to count.
	 * @return array
	 */
	protected function rows( array $rows, callable $map, string $required = 'title' ): array {
		$out = array();
		foreach ( $rows as $row ) {
			if ( '' === trim( (string) ( $row[ $required ] ?? '' ) ) ) {
				continue;
			}
			$out[] = $map( $row );
		}
		return $out;
	}

	/**
	 * A link control's URL, or a fallback when the editor left it empty.
	 *
	 * @param array  $link     Elementor URL control value.
	 * @param string $fallback URL to use when none was chosen.
	 */
	protected function url( $link, string $fallback = '' ): string {
		$url = is_array( $link ) ? (string) ( $link['url'] ?? '' ) : '';
		return '' !== $url ? $url : $fallback;
	}

	/**
	 * A one-line text control.
	 *
	 * Wording controls are most of what these widgets have, and spelling out
	 * the same five-line array for each of forty of them buries the one thing
	 * that differs between them.
	 *
	 * @param string $key     Setting name.
	 * @param string $label   Panel label.
	 * @param string $default Approved wording.
	 */
	protected function text( string $key, string $label, string $default = '' ): void {
		$this->add_control(
			$key,
			array(
				'label'   => $label,
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => $default,
			)
		);
	}

	/**
	 * A multi-line text control.
	 *
	 * @param string $key     Setting name.
	 * @param string $label   Panel label.
	 * @param string $default Approved wording.
	 * @param int    $rows    Height of the box.
	 */
	protected function area( string $key, string $label, string $default = '', int $rows = 4 ): void {
		$this->add_control(
			$key,
			array(
				'label'   => $label,
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => $rows,
				'default' => $default,
			)
		);
	}

	/**
	 * Note shown at the top of a widget's panel.
	 *
	 * @param string $text What the editor needs to know.
	 */
	protected function note( string $text ): void {
		$this->add_control(
			'star_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html( $text ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
	}

	/**
	 * The sidebar cards every page with a main column beside one shares.
	 *
	 * Each card is built from whichever parts it is given: an intro line, a list
	 * of rows, a closing note and one or more buttons. Clearing a card's heading
	 * removes the card, so a page that needs fewer simply leaves the rest empty.
	 *
	 * @param int $count How many cards to offer.
	 */
	protected function sidebar_controls( int $count = 3 ): void {
		$row = new \Elementor\Repeater();
		$row->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'info',
				'options' => Star_Electric_Widget_Trust_Strip::icon_options(),
			)
		);
		$row->add_control(
			'label',
			array( 'label' => __( 'Label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$row->add_control(
			'value',
			array(
				'label'       => __( 'Value', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Shown after the label.', 'star-electric' ),
			)
		);
		$row->add_control(
			'url',
			array(
				'label'       => __( 'Link', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'description' => __( 'The value becomes this link - or the label does, when there is no value.', 'star-electric' ),
			)
		);

		$button = new \Elementor\Repeater();
		$button->add_control(
			'label',
			array( 'label' => __( 'Button label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$button->add_control(
			'url',
			array( 'label' => __( 'Link', 'star-electric' ), 'type' => \Elementor\Controls_Manager::URL )
		);
		$button->add_control(
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

		$labels = array(
			__( 'Sidebar: first card', 'star-electric' ),
			__( 'Sidebar: second card', 'star-electric' ),
			__( 'Sidebar: third card', 'star-electric' ),
		);

		for ( $i = 1; $i <= $count; $i++ ) {
			$key = 'card' . $i;

			$this->start_controls_section(
				'star_' . $key,
				array( 'label' => $labels[ $i - 1 ] ?? sprintf( 'Sidebar: card %d', $i ) )
			);

			$this->add_control(
				$key . '_title',
				array(
					'label'       => __( 'Card heading', 'star-electric' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'description' => __( 'Leave empty to remove the card.', 'star-electric' ),
				)
			);
			$this->add_control(
				$key . '_text',
				array(
					'label' => __( 'Intro line', 'star-electric' ),
					'type'  => \Elementor\Controls_Manager::TEXTAREA,
					'rows'  => 3,
				)
			);
			$this->add_control(
				$key . '_rows',
				array(
					'label'       => __( 'Rows', 'star-electric' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $row->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ label }}}',
				)
			);
			$this->add_control(
				$key . '_hint',
				array(
					'label' => __( 'Closing note', 'star-electric' ),
					'type'  => \Elementor\Controls_Manager::TEXTAREA,
					'rows'  => 3,
				)
			);
			$this->add_control(
				$key . '_buttons',
				array(
					'label'       => __( 'Buttons', 'star-electric' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $button->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ label }}}',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Those cards in the shape Star_Electric_Sections::info_cards wants.
	 *
	 * @param array $s     Widget settings.
	 * @param int   $count How many cards to read.
	 * @return array
	 */
	protected function sidebar_cards( array $s, int $count = 3 ): array {
		$cards = array();

		for ( $i = 1; $i <= $count; $i++ ) {
			$key = 'card' . $i;

			$cards[] = array(
				'title'   => (string) ( $s[ $key . '_title' ] ?? '' ),
				'text'    => (string) ( $s[ $key . '_text' ] ?? '' ),
				'hint'    => (string) ( $s[ $key . '_hint' ] ?? '' ),
				'rows'    => $this->rows(
					(array) ( $s[ $key . '_rows' ] ?? array() ),
					function ( array $row ): array {
						return array(
							'icon'  => (string) ( $row['icon'] ?? 'info' ),
							'label' => (string) ( $row['label'] ?? '' ),
							'value' => (string) ( $row['value'] ?? '' ),
							'url'   => $this->url( $row['url'] ?? array() ),
						);
					},
					'label'
				),
				'buttons' => $this->rows(
					(array) ( $s[ $key . '_buttons' ] ?? array() ),
					function ( array $row ): array {
						return array(
							'label' => (string) ( $row['label'] ?? '' ),
							'url'   => $this->url( $row['url'] ?? array(), home_url( '/' ) ),
							'style' => (string) ( $row['style'] ?? 'ghost' ),
						);
					},
					'label'
				),
			);
		}

		return $cards;
	}
}
