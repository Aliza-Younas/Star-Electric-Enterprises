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
}
