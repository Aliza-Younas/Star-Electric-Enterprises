<?php
/**
 * FAQ.
 *
 * The topic filter, the grouped accordions and the closing "still have a
 * question" block. Every question is a repeater row carrying its topic, so a
 * question can be reworded, moved to another topic or removed without touching
 * the others, and a new topic appears simply by naming one.
 *
 * An answer may carry a single link, written as [text](url). It is escaped
 * first and only then is that one tag put back, so an answer can never smuggle
 * markup onto the page.
 *
 * Several answers say that a term is not published. That is the point of the
 * page, not a gap in it - please do not edit a delivery charge, return window
 * or warranty into one before the store has set it.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FAQ widget.
 */
class Star_Electric_Widget_Faq extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-faq';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'FAQ', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-accordion';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_intro',
			array( 'label' => __( 'Notice', 'star-electric' ) )
		);
		$this->add_control(
			'alert_strong',
			array(
				'label'   => __( 'Notice heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Where an answer says something is not published, that is deliberate.', 'star-electric' ),
			)
		);
		$this->add_control(
			'alert_text',
			array(
				'label'   => __( 'Notice text', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'Delivery charges, timeframes, return windows and warranty terms are agreed with the store directly rather than stated here before they are set.', 'star-electric' ),
			)
		);
		$this->add_control(
			'all_label',
			array(
				'label'   => __( 'Filter — all questions', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'All questions', 'star-electric' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'star_questions',
			array( 'label' => __( 'Questions', 'star-electric' ) )
		);
		$this->note(
			__( 'Topics are created by typing one. Questions sharing a topic are grouped together, in the order the topics first appear. A link in an answer is written as [text](https://...).', 'star-electric' )
		);

		$q = new \Elementor\Repeater();
		$q->add_control(
			'topic',
			array(
				'label'   => __( 'Topic', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Ordering', 'star-electric' ),
			)
		);
		$q->add_control(
			'question',
			array( 'label' => __( 'Question', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$q->add_control(
			'answer',
			array(
				'label' => __( 'Answer', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 6,
			)
		);

		$this->add_control(
			'questions',
			array(
				'label'       => __( 'Questions', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $q->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ question }}}',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'star_closing',
			array( 'label' => __( 'Closing block', 'star-electric' ) )
		);
		$this->add_control(
			'closing_title',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Still have a question?', 'star-electric' ),
			)
		);
		$this->add_control(
			'closing_text',
			array(
				'label'   => __( 'Text', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'If your question is not covered here, get in touch and we will answer it directly.', 'star-electric' ),
			)
		);
		$this->add_control(
			'closing_primary',
			array(
				'label'   => __( 'Primary button', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Contact Us', 'star-electric' ),
			)
		);
		$this->add_control(
			'closing_second',
			array(
				'label'   => __( 'Second button', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Request a Quote', 'star-electric' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * One answer, escaped, with a single [text](url) link put back.
	 *
	 * @param string $answer Answer text.
	 */
	private function answer( string $answer ): string {
		return Star_Electric_Sections::rich( $answer );
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		if ( ! class_exists( 'Star_Electric_Shell' ) ) {
			return;
		}

		$s = $this->get_settings_for_display();

		/* Group by topic, keeping the order the topics first appear in. */
		$groups = array();
		foreach ( (array) ( $s['questions'] ?? array() ) as $row ) {
			$question = trim( (string) ( $row['question'] ?? '' ) );
			if ( '' === $question ) {
				continue;
			}
			$topic = trim( (string) ( $row['topic'] ?? '' ) );
			$topic = '' !== $topic ? $topic : __( 'General', 'star-electric' );
			$key   = sanitize_title( $topic );
			if ( ! isset( $groups[ $key ] ) ) {
				$groups[ $key ] = array( 'label' => $topic, 'questions' => array() );
			}
			$groups[ $key ]['questions'][] = array( $question, (string) ( $row['answer'] ?? '' ) );
		}

		if ( empty( $groups ) ) {
			return;
		}

		$contact = Star_Electric_Shell::url( 'contact' );
		$quote   = Star_Electric_Shell::url( 'quote' );
		$n       = 0;
		?>
		<section class="section section--sm">
			<div class="container container--mid">

				<div class="alert alert--info" style="margin-bottom:32px">
					<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span>
						<strong><?php echo esc_html( (string) $s['alert_strong'] ); ?></strong>
						<?php echo esc_html( (string) $s['alert_text'] ); ?>
					</span>
				</div>

				<div class="faq-cats" id="faqCats" role="group" aria-label="<?php esc_attr_e( 'Filter questions by topic', 'star-electric' ); ?>">
					<button type="button" data-faq-cat="all" aria-pressed="true"><?php echo esc_html( (string) $s['all_label'] ); ?></button>
					<?php foreach ( $groups as $key => $group ) : ?>
						<button type="button" data-faq-cat="<?php echo esc_attr( $key ); ?>" aria-pressed="false">
							<?php echo esc_html( $group['label'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<?php foreach ( $groups as $key => $group ) : ?>
					<div data-faq-group="<?php echo esc_attr( $key ); ?>">
						<h2 class="section__title" style="font-size:18px;margin-bottom:16px"><?php echo esc_html( $group['label'] ); ?></h2>
						<div class="accordion" style="margin-bottom:40px">
							<?php foreach ( $group['questions'] as $item ) : ?>
								<?php ++$n; ?>
								<div class="acc-item">
									<button class="acc-item__btn" type="button" data-toggle-panel aria-expanded="false" aria-controls="f<?php echo esc_attr( (string) $n ); ?>">
										<?php echo esc_html( $item[0] ); ?>
										<?php echo Star_Electric_Shell::icon( 'chevdown' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</button>
									<div class="acc-item__panel" id="f<?php echo esc_attr( (string) $n ); ?>" hidden>
										<?php echo wp_kses_post( $this->answer( $item[1] ) ); ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>

				<div class="empty-state" style="background:var(--navy-50);border-style:solid;border-color:var(--navy-100)">
					<span class="empty-state__ico">
						<?php echo Star_Electric_Shell::icon( 'headset' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<h2><?php echo esc_html( (string) $s['closing_title'] ); ?></h2>
					<p><?php echo esc_html( (string) $s['closing_text'] ); ?></p>
					<div class="btn-row">
						<a class="btn btn--accent" href="<?php echo esc_url( $contact ); ?>"><?php echo esc_html( (string) $s['closing_primary'] ); ?></a>
						<a class="btn btn--ghost" href="<?php echo esc_url( $quote ); ?>"><?php echo esc_html( (string) $s['closing_second'] ); ?></a>
					</div>
				</div>

			</div>
		</section>
		<?php
	}
}
