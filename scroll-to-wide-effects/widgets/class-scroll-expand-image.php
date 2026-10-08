<?php
/**
 * Widget: Scroll Expand Image.
 *
 * A sticky image frame that grows (width, height, radius) while the section scrolls.
 *
 * @package scroll-to-wide-effects
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sei_Scroll_Expand_Image_Widget extends Widget_Base {

	public function get_name() {
		return 'sei_scroll_expand_image';
	}

	public function get_title() {
		return esc_html__( 'Scroll Expand Image', 'scroll-to-wide-effects' );
	}

	public function get_icon() {
		return 'eicon-image-rollover';
	}

	public function get_categories() {
		return [ 'scroll-to-wide-effects' ];
	}

	public function get_keywords() {
		return [ 'image', 'scroll', 'expand', 'zoom', 'sticky', 'full width', 'parallax' ];
	}

	public function get_style_depends() {
		return [ 'sei-scroll-expand-image' ];
	}

	public function get_script_depends() {
		return [ 'sei-scroll-expand-image' ];
	}

	/**
	 * Optimized DOM: no .elementor-widget-container wrapper.
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/* ---------------------------------------------------------------------
	 * Controls
	 * ------------------------------------------------------------------ */

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	private function register_content_controls() {

		/* ---- Image ---- */
		$this->start_controls_section(
			'section_image',
			[
				'label' => esc_html__( 'Image', 'scroll-to-wide-effects' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose Image', 'scroll-to-wide-effects' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [ 'active' => true ],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'alt_text',
			[
				'label'       => esc_html__( 'Alt Text', 'scroll-to-wide-effects' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Falls back to the media library alt text', 'scroll-to-wide-effects' ),
				'description' => esc_html__( 'Leave empty for a decorative image (alt="").', 'scroll-to-wide-effects' ),
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
			]
		);

		$this->add_control(
			'image_loading',
			[
				'label'       => esc_html__( 'Loading', 'scroll-to-wide-effects' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'lazy',
				'options'     => [
					'lazy'     => esc_html__( 'Lazy', 'scroll-to-wide-effects' ),
					'eager'    => esc_html__( 'Eager', 'scroll-to-wide-effects' ),
					'priority' => esc_html__( 'Eager + High priority (LCP image)', 'scroll-to-wide-effects' ),
				],
				'description' => esc_html__( 'Use "High priority" only when this image is at the top of the page.', 'scroll-to-wide-effects' ),
			]
		);

		$this->end_controls_section();

		/* ---- Content (overlay on the image) ---- */
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'scroll-to-wide-effects' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label'       => esc_html__( 'Subtitle', 'scroll-to-wide-effects' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Subtitle', 'scroll-to-wide-effects' ),
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'scroll-to-wide-effects' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => esc_html__( 'Your Title Here', 'scroll-to-wide-effects' ),
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Title HTML Tag', 'scroll-to-wide-effects' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Text', 'scroll-to-wide-effects' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Add a short description here. It sits on top of the image and moves with it while you scroll.', 'scroll-to-wide-effects' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$this->add_control(
			'heading_button_content',
			[
				'label'     => esc_html__( 'Button', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'scroll-to-wide-effects' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Learn More', 'scroll-to-wide-effects' ),
				'description' => esc_html__( 'Leave empty to hide the button.', 'scroll-to-wide-effects' ),
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'       => esc_html__( 'Link', 'scroll-to-wide-effects' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'https://example.com',
			]
		);

		$this->add_control(
			'button_icon',
			[
				'label' => esc_html__( 'Icon', 'scroll-to-wide-effects' ),
				'type'  => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'button_icon_position',
			[
				'label'     => esc_html__( 'Icon Position', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => [
					'before' => esc_html__( 'Before', 'scroll-to-wide-effects' ),
					'after'  => esc_html__( 'After', 'scroll-to-wide-effects' ),
				],
				'condition' => [
					'button_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'scroll-to-wide-effects' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_section();

		/* ---- Scroll animation ---- */
		$this->start_controls_section(
			'section_animation',
			[
				'label' => esc_html__( 'Scroll Animation', 'scroll-to-wide-effects' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'scroll_distance',
			[
				'label'       => esc_html__( 'Section Height (Scroll Distance)', 'scroll-to-wide-effects' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'vh' ],
				'range'       => [
					'vh' => [
						'min'  => 100,
						'max'  => 600,
						'step' => 5,
					],
				],
				'default'     => [
					'size' => 200,
					'unit' => 'vh',
				],
				'mobile_default' => [
					'size' => 170,
					'unit' => 'vh',
				],
				'description' => esc_html__( 'Taller = slower animation. Must be at least 100vh.', 'scroll-to-wide-effects' ),
				'selectors'   => [
					'{{WRAPPER}} .sei' => '--sei-section-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'easing',
			[
				'label'   => esc_html__( 'Easing', 'scroll-to-wide-effects' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'linear',
				'options' => [
					'linear'      => esc_html__( 'Linear', 'scroll-to-wide-effects' ),
					'ease-in'     => esc_html__( 'Ease In', 'scroll-to-wide-effects' ),
					'ease-out'    => esc_html__( 'Ease Out', 'scroll-to-wide-effects' ),
					'ease-in-out' => esc_html__( 'Ease In Out', 'scroll-to-wide-effects' ),
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_controls() {

		/* ---- Layout / Box ---- */
		$this->start_controls_section(
			'style_layout',
			[
				'label' => esc_html__( 'Layout / Box', 'scroll-to-wide-effects' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'stage_justify',
			[
				'label'     => esc_html__( 'Horizontal Alignment', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Start', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-justify-start-h',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end'   => [
						'title' => esc_html__( 'End', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-justify-end-h',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .sei__stage' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'stage_align',
			[
				'label'     => esc_html__( 'Vertical Alignment', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-align-start-v',
					],
					'center'     => [
						'title' => esc_html__( 'Middle', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-align-center-v',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Bottom', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-align-end-v',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .sei__stage' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'stage_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .sei__stage',
			]
		);

		$this->end_controls_section();

		/* ---- Image frame (animated box) ---- */
		$this->start_controls_section(
			'style_frame',
			[
				'label' => esc_html__( 'Image Frame', 'scroll-to-wide-effects' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_start',
			[
				'label' => esc_html__( 'Start State (scroll = 0%)', 'scroll-to-wide-effects' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_var_slider(
			'start_width',
			esc_html__( 'Width', 'scroll-to-wide-effects' ),
			'--sei-w0',
			[ 'vw', '%', 'px' ],
			$this->width_ranges(),
			[ 'size' => 70, 'unit' => 'vw' ],
			[ 'size' => 88, 'unit' => 'vw' ]
		);

		$this->add_var_slider(
			'start_height',
			esc_html__( 'Height', 'scroll-to-wide-effects' ),
			'--sei-h0',
			[ 'vh', '%', 'px' ],
			$this->height_ranges(),
			[ 'size' => 75, 'unit' => 'vh' ],
			[ 'size' => 65, 'unit' => 'vh' ]
		);

		$this->add_control(
			'start_square',
			[
				'label'        => esc_html__( 'Square Start Frame', 'scroll-to-wide-effects' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'sei-square-start-',
				'description'  => esc_html__( 'Starts as a 1:1 box (the smaller of width/height), so Border Radius 100 gives a perfect circle.', 'scroll-to-wide-effects' ),
			]
		);

		$this->add_radius_slider( 'start_radius', '--sei-r0', 6 );

		$this->add_control(
			'heading_end',
			[
				'label'     => esc_html__( 'End State (scroll = 100%)', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_var_slider(
			'end_width',
			esc_html__( 'Width', 'scroll-to-wide-effects' ),
			'--sei-w1',
			[ '%', 'vw', 'px' ],
			$this->width_ranges(),
			[ 'size' => 100, 'unit' => '%' ]
		);

		$this->add_var_slider(
			'end_height',
			esc_html__( 'Height', 'scroll-to-wide-effects' ),
			'--sei-h1',
			[ '%', 'vh', 'px' ],
			$this->height_ranges(),
			[ 'size' => 100, 'unit' => '%' ]
		);

		$this->add_radius_slider( 'end_radius', '--sei-r1', 0 );

		$this->add_control(
			'heading_frame_box',
			[
				'label'     => esc_html__( 'Box', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'frame_bg',
			[
				'label'     => esc_html__( 'Background Color', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sei__frame' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'frame_border',
				'selector' => '{{WRAPPER}} .sei__frame',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'frame_shadow',
				'selector' => '{{WRAPPER}} .sei__frame',
			]
		);

		$this->end_controls_section();

		/* ---- Image ---- */
		$this->start_controls_section(
			'style_image',
			[
				'label' => esc_html__( 'Image', 'scroll-to-wide-effects' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'object_fit',
			[
				'label'     => esc_html__( 'Object Fit', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => [
					'cover'      => esc_html__( 'Cover', 'scroll-to-wide-effects' ),
					'contain'    => esc_html__( 'Contain', 'scroll-to-wide-effects' ),
					'fill'       => esc_html__( 'Fill', 'scroll-to-wide-effects' ),
					'none'       => esc_html__( 'None', 'scroll-to-wide-effects' ),
					'scale-down' => esc_html__( 'Scale Down', 'scroll-to-wide-effects' ),
				],
				'selectors' => [
					'{{WRAPPER}} .sei__img' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'object_position',
			[
				'label'     => esc_html__( 'Object Position', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => [
					'center center' => esc_html__( 'Center Center', 'scroll-to-wide-effects' ),
					'center top'    => esc_html__( 'Center Top', 'scroll-to-wide-effects' ),
					'center bottom' => esc_html__( 'Center Bottom', 'scroll-to-wide-effects' ),
					'left center'   => esc_html__( 'Left Center', 'scroll-to-wide-effects' ),
					'right center'  => esc_html__( 'Right Center', 'scroll-to-wide-effects' ),
					'left top'      => esc_html__( 'Left Top', 'scroll-to-wide-effects' ),
					'right top'     => esc_html__( 'Right Top', 'scroll-to-wide-effects' ),
					'left bottom'   => esc_html__( 'Left Bottom', 'scroll-to-wide-effects' ),
					'right bottom'  => esc_html__( 'Right Bottom', 'scroll-to-wide-effects' ),
				],
				'selectors' => [
					'{{WRAPPER}} .sei__img' => 'object-position: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_css_filter',
				'selector' => '{{WRAPPER}} .sei__img',
			]
		);

		$this->end_controls_section();

		$this->register_content_style_controls();
	}

	private function register_content_style_controls() {

		/* ---- Overlay (shade over the image) ---- */
		$this->start_controls_section(
			'style_overlay',
			[
				'label' => esc_html__( 'Image Overlay', 'scroll-to-wide-effects' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_background',
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .sei__shade',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => 'rgba(0,0,0,0.35)' ],
				],
			]
		);

		$this->end_controls_section();

		/* ---- Content box ---- */
		$this->start_controls_section(
			'style_content_box',
			[
				'label' => esc_html__( 'Content Box', 'scroll-to-wide-effects' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_justify',
			[
				'label'     => esc_html__( 'Horizontal Position', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Start', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-justify-start-h',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end'   => [
						'title' => esc_html__( 'End', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-justify-end-h',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .sei__layer' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label'     => esc_html__( 'Vertical Position', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-align-start-v',
					],
					'center'     => [
						'title' => esc_html__( 'Middle', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-align-center-v',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Bottom', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-flex eicon-align-end-v',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .sei__layer' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_text_align',
			[
				'label'     => esc_html__( 'Text Alignment', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'scroll-to-wide-effects' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .sei__content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label'      => esc_html__( 'Max Width', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 100, 'max' => 1600 ],
					'%'  => [ 'min' => 10, 'max' => 100 ],
					'vw' => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => [
					'size' => 720,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .sei__content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'layer_padding',
			[
				'label'      => esc_html__( 'Distance From Image Edges', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'vw', 'vh' ],
				'default'    => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .sei__layer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Inner Padding', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .sei__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .sei__content',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'content_border',
				'selector' => '{{WRAPPER}} .sei__content',
			]
		);

		$this->add_responsive_control(
			'content_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .sei__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_shadow',
				'selector' => '{{WRAPPER}} .sei__content',
			]
		);

		$this->add_control(
			'content_backdrop_blur',
			[
				'label'     => esc_html__( 'Backdrop Blur', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [ 'min' => 0, 'max' => 40 ],
				],
				'selectors' => [
					'{{WRAPPER}} .sei__content' => '-webkit-backdrop-filter: blur({{SIZE}}px); backdrop-filter: blur({{SIZE}}px);',
				],
			]
		);

		$this->end_controls_section();

		/* ---- Text elements ---- */
		$this->add_text_style_section( 'subtitle', esc_html__( 'Subtitle', 'scroll-to-wide-effects' ), '.sei__subtitle', 16, 600, 16 );
		$this->add_text_style_section( 'title', esc_html__( 'Title', 'scroll-to-wide-effects' ), '.sei__title', 56, 700, 16 );
		$this->add_text_style_section( 'text', esc_html__( 'Text', 'scroll-to-wide-effects' ), '.sei__text', 18, 400, 28 );

		/* ---- Button ---- */
		$this->start_controls_section(
			'style_button',
			[
				'label' => esc_html__( 'Button', 'scroll-to-wide-effects' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .sei__button',
				'fields_options' => [
					'typography' => [ 'default' => 'custom' ],
					'font_size'  => [ 'default' => [ 'size' => 16, 'unit' => 'px' ] ],
					'font_weight' => [ 'default' => '600' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'button_text_shadow',
				'selector' => '{{WRAPPER}} .sei__button',
			]
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab(
			'button_tab_normal',
			[ 'label' => esc_html__( 'Normal', 'scroll-to-wide-effects' ) ]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => [
					'{{WRAPPER}} .sei__button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .sei__button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'button_background',
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .sei__button',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => '#ffffff' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .sei__button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .sei__button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_tab_hover',
			[ 'label' => esc_html__( 'Hover', 'scroll-to-wide-effects' ) ]
		);

		$this->add_control(
			'button_color_hover',
			[
				'label'     => esc_html__( 'Text Color', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sei__button:hover, {{WRAPPER}} .sei__button:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .sei__button:hover svg, {{WRAPPER}} .sei__button:focus svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .sei__button:hover, {{WRAPPER}} .sei__button:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'button_border_hover',
				'selector' => '{{WRAPPER}} .sei__button:hover, {{WRAPPER}} .sei__button:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow_hover',
				'selector' => '{{WRAPPER}} .sei__button:hover, {{WRAPPER}} .sei__button:focus',
			]
		);

		$this->add_control(
			'button_transition',
			[
				'label'     => esc_html__( 'Transition Duration (s)', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [ 'min' => 0, 'max' => 3, 'step' => 0.1 ],
				],
				'default'   => [ 'size' => 0.3 ],
				'selectors' => [
					'{{WRAPPER}} .sei__button' => 'transition: color {{SIZE}}s, background-color {{SIZE}}s, border-color {{SIZE}}s, box-shadow {{SIZE}}s, transform {{SIZE}}s;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'default'    => [
					'top'      => '14',
					'right'    => '32',
					'bottom'   => '14',
					'left'     => '32',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .sei__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'default'    => [
					'top'      => '999',
					'right'    => '999',
					'bottom'   => '999',
					'left'     => '999',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .sei__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label'      => esc_html__( 'Space Above Button', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 200 ],
				],
				'default'    => [ 'size' => 12, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .sei__button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 6, 'max' => 80 ],
					'em' => [ 'min' => 0.5, 'max' => 5, 'step' => 0.1 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .sei__button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .sei__button-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'button_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_gap',
			[
				'label'      => esc_html__( 'Icon Spacing', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 60 ],
				],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .sei__button' => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'button_icon[value]!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style section for one text element (subtitle / title / text).
	 *
	 * @param string $prefix      Control id prefix.
	 * @param string $label       Already-translated section label.
	 * @param string $selector    Class selector inside the widget wrapper.
	 * @param int    $font_size   Default font size (px).
	 * @param int    $font_weight Default font weight.
	 * @param int    $spacing     Default bottom spacing (px).
	 */
	private function add_text_style_section( $prefix, $label, $selector, $font_size, $font_weight, $spacing ) {
		$target = '{{WRAPPER}} ' . $selector;

		$this->start_controls_section(
			'style_' . $prefix,
			[
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			$prefix . '_color',
			[
				'label'     => esc_html__( 'Color', 'scroll-to-wide-effects' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					$target => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => $prefix . '_typography',
				'selector'       => $target,
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_size'   => [ 'default' => [ 'size' => $font_size, 'unit' => 'px' ] ],
					'font_weight' => [ 'default' => (string) $font_weight ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => $prefix . '_text_shadow',
				'selector' => $target,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => $prefix . '_text_stroke',
				'selector' => $target,
			]
		);

		$this->add_responsive_control(
			$prefix . '_spacing',
			[
				'label'      => esc_html__( 'Space Below', 'scroll-to-wide-effects' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 200 ],
					'em' => [ 'min' => 0, 'max' => 10, 'step' => 0.1 ],
				],
				'default'    => [ 'size' => $spacing, 'unit' => 'px' ],
				'selectors'  => [
					$target => 'margin: 0 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * Control helpers
	 * ------------------------------------------------------------------ */

	private function width_ranges() {
		return [
			'vw' => [ 'min' => 10, 'max' => 100 ],
			'%'  => [ 'min' => 10, 'max' => 100 ],
			'px' => [ 'min' => 100, 'max' => 2400 ],
		];
	}

	private function height_ranges() {
		return [
			'vh' => [ 'min' => 10, 'max' => 100 ],
			'%'  => [ 'min' => 10, 'max' => 100 ],
			'px' => [ 'min' => 100, 'max' => 1600 ],
		];
	}

	/**
	 * Roundness slider: 0 = square corners, 100 = fully round (radius = half of the start frame's
	 * shorter side). Start and end values are interpolated linearly in px while scrolling.
	 *
	 * @param string $id      Control id.
	 * @param string $css_var Custom property name.
	 * @param int    $default Desktop default.
	 */
	private function add_radius_slider( $id, $css_var, $default ) {
		$this->add_responsive_control(
			$id,
			[
				'label'       => esc_html__( 'Border Radius', 'scroll-to-wide-effects' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'     => [
					'size' => $default,
					'unit' => 'px',
				],
				'description' => esc_html__( '0 = square, 100 = fully round. Use "Square Start Frame" for a perfect circle.', 'scroll-to-wide-effects' ),
				'selectors'   => [
					'{{WRAPPER}} .sei' => $css_var . ': {{SIZE}};',
				],
			]
		);
	}

	/**
	 * Responsive slider that writes a CSS custom property on .sei.
	 *
	 * @param string $id             Control id.
	 * @param string $label          Already-translated label.
	 * @param string $css_var        Custom property name.
	 * @param array  $units          Allowed units.
	 * @param array  $ranges         Range per unit.
	 * @param array  $default        Desktop default.
	 * @param array  $mobile_default Optional mobile default.
	 */
	private function add_var_slider( $id, $label, $css_var, array $units, array $ranges, array $default, array $mobile_default = [] ) {
		$args = [
			'label'      => $label,
			'type'       => Controls_Manager::SLIDER,
			'size_units' => $units,
			'range'      => $ranges,
			'default'    => $default,
			'selectors'  => [
				'{{WRAPPER}} .sei' => $css_var . ': {{SIZE}}{{UNIT}};',
			],
		];

		if ( $mobile_default ) {
			$args['mobile_default'] = $mobile_default;
		}

		$this->add_responsive_control( $id, $args );
	}

	/* ---------------------------------------------------------------------
	 * Render
	 * ------------------------------------------------------------------ */

	/**
	 * Resolve src / srcset / dimensions / alt for the selected image.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private function get_image_data( array $settings ) {
		$image = $settings['image'];
		$id    = ! empty( $image['id'] ) ? absint( $image['id'] ) : 0;
		$data  = [
			'src'    => '',
			'srcset' => '',
			'width'  => 0,
			'height' => 0,
			'alt'    => '',
		];

		if ( $id ) {
			$size = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'full';

			if ( 'custom' === $size ) {
				$src = Group_Control_Image_Size::get_attachment_image_src( $id, 'image', $settings );

				if ( $src ) {
					$data['src'] = $src;
				}

				$dimension      = isset( $settings['image_custom_dimension'] ) ? $settings['image_custom_dimension'] : [];
				$data['width']  = isset( $dimension['width'] ) ? absint( $dimension['width'] ) : 0;
				$data['height'] = isset( $dimension['height'] ) ? absint( $dimension['height'] ) : 0;
			} else {
				$src = wp_get_attachment_image_src( $id, $size );

				if ( $src ) {
					$data['src']    = $src[0];
					$data['width']  = (int) $src[1];
					$data['height'] = (int) $src[2];
				}

				$srcset = wp_get_attachment_image_srcset( $id, $size );

				if ( $srcset ) {
					$data['srcset'] = $srcset;
				}
			}

			$data['alt'] = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		}

		if ( empty( $data['src'] ) && ! empty( $image['url'] ) ) {
			$data['src'] = $image['url'];
		}

		if ( ! empty( $settings['alt_text'] ) ) {
			$data['alt'] = $settings['alt_text'];
		}

		return $data;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['image']['url'] ) && empty( $settings['image']['id'] ) ) {
			return;
		}

		$data = $this->get_image_data( $settings );

		if ( empty( $data['src'] ) ) {
			return;
		}

		$loading = ! empty( $settings['image_loading'] ) ? $settings['image_loading'] : 'lazy';

		$this->add_render_attribute(
			'root',
			[
				'class'           => 'sei',
				'data-sei'        => '',
				'data-sei-easing' => $settings['easing'],
			]
		);

		$img_attrs = [
			'class'    => 'sei__img',
			'src'      => esc_url( $data['src'] ),
			'alt'      => $data['alt'],
			'decoding' => 'async',
			'loading'  => 'lazy' === $loading ? 'lazy' : 'eager',
		];

		if ( 'priority' === $loading ) {
			$img_attrs['fetchpriority'] = 'high';
		}

		if ( $data['width'] && $data['height'] ) {
			$img_attrs['width']  = $data['width'];
			$img_attrs['height'] = $data['height'];
		}

		if ( $data['srcset'] ) {
			$img_attrs['srcset'] = $data['srcset'];
			$img_attrs['sizes']  = '100vw';
		}

		$this->add_render_attribute( 'img', $img_attrs );

		$subtitle    = isset( $settings['subtitle'] ) ? $settings['subtitle'] : '';
		$title       = isset( $settings['title'] ) ? $settings['title'] : '';
		$description = isset( $settings['description'] ) ? $settings['description'] : '';
		$button_text = isset( $settings['button_text'] ) ? $settings['button_text'] : '';
		$title_tag   = Utils::validate_html_tag( $settings['title_tag'] );

		$has_content = '' !== trim( (string) $subtitle ) || '' !== trim( (string) $title ) || '' !== trim( wp_strip_all_tags( (string) $description ) ) || '' !== trim( (string) $button_text );

		$button_tag = 'span';

		if ( '' !== $button_text ) {
			$this->add_render_attribute( 'button', 'class', 'sei__button' );

			if ( ! empty( $settings['button_hover_animation'] ) ) {
				$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['button_hover_animation'] );
			}

			if ( ! empty( $settings['button_link']['url'] ) ) {
				$button_tag = 'a';
				$this->add_link_attributes( 'button', $settings['button_link'] );
			}
		}

		$has_icon = ! empty( $settings['button_icon']['value'] );
		?>
		<div <?php $this->print_render_attribute_string( 'root' ); ?>>
			<div class="sei__stage">
				<div class="sei__frame">
					<img <?php $this->print_render_attribute_string( 'img' ); ?>>
					<?php if ( $has_content ) : ?>
						<div class="sei__shade"></div>
						<div class="sei__layer">
							<div class="sei__content">
								<?php if ( '' !== trim( (string) $subtitle ) ) : ?>
									<p class="sei__subtitle"><?php echo esc_html( $subtitle ); ?></p>
								<?php endif; ?>
								<?php if ( '' !== trim( (string) $title ) ) : ?>
									<<?php echo esc_html( $title_tag ); ?> class="sei__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>>
								<?php endif; ?>
								<?php if ( '' !== trim( wp_strip_all_tags( (string) $description ) ) ) : ?>
									<div class="sei__text"><?php echo wp_kses_post( $description ); ?></div>
								<?php endif; ?>
								<?php if ( '' !== $button_text ) : ?>
									<<?php echo esc_html( $button_tag ); ?> <?php $this->print_render_attribute_string( 'button' ); ?>>
										<?php if ( $has_icon && 'before' === $settings['button_icon_position'] ) : ?>
											<span class="sei__button-icon"><?php Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
										<?php endif; ?>
										<span class="sei__button-text"><?php echo esc_html( $button_text ); ?></span>
										<?php if ( $has_icon && 'before' !== $settings['button_icon_position'] ) : ?>
											<span class="sei__button-icon"><?php Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
										<?php endif; ?>
									</<?php echo esc_html( $button_tag ); ?>>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Editor live preview. Must mirror render().
	 */
	protected function content_template() {
		?>
		<#
		var image = settings.image;

		if ( ! image.url && ! image.id ) {
			return;
		}

		var imageObj = {
			id: image.id,
			url: image.url,
			size: settings.image_size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};

		var src = elementor.imagesManager.getImageUrl( imageObj ) || image.url;
		var loading = settings.image_loading || 'lazy';
		var dimension = settings.image_custom_dimension || {};
		var customSize = 'custom' === settings.image_size && dimension.width && dimension.height;

		var hasText = '' !== ( settings.description || '' ).replace( /<[^>]*>/g, '' ).trim();
		var hasContent = settings.subtitle || settings.title || hasText || settings.button_text;
		var titleTag = elementor.helpers.validateHTMLTag( settings.title_tag );
		var buttonTag = settings.button_link && settings.button_link.url ? 'a' : 'span';
		var buttonClass = 'sei__button' + ( settings.button_hover_animation ? ' elementor-animation-' + settings.button_hover_animation : '' );
		var iconHTML = settings.button_icon && settings.button_icon.value ? elementor.helpers.renderIcon( view, settings.button_icon, { 'aria-hidden': true }, 'i', 'object' ) : null;
		#>
		<div class="sei" data-sei data-sei-easing="{{ settings.easing }}">
			<div class="sei__stage">
				<div class="sei__frame">
					<img
						class="sei__img"
						src="{{ src }}"
						alt="{{ settings.alt_text }}"
						decoding="async"
						loading="{{ 'lazy' === loading ? 'lazy' : 'eager' }}"
						<# if ( 'priority' === loading ) { #> fetchpriority="high"<# } #>
						<# if ( customSize ) { #> width="{{ dimension.width }}" height="{{ dimension.height }}"<# } #>
					>
					<# if ( hasContent ) { #>
						<div class="sei__shade"></div>
						<div class="sei__layer">
							<div class="sei__content">
								<# if ( settings.subtitle ) { #>
									<p class="sei__subtitle">{{ settings.subtitle }}</p>
								<# } #>
								<# if ( settings.title ) { #>
									<{{{ titleTag }}} class="sei__title">{{ settings.title }}</{{{ titleTag }}}>
								<# } #>
								<# if ( hasText ) { #>
									<div class="sei__text">{{{ settings.description }}}</div>
								<# } #>
								<# if ( settings.button_text ) { #>
									<{{{ buttonTag }}} class="{{ buttonClass }}">
										<# if ( iconHTML && 'before' === settings.button_icon_position ) { #>
											<span class="sei__button-icon">{{{ iconHTML.value }}}</span>
										<# } #>
										<span class="sei__button-text">{{ settings.button_text }}</span>
										<# if ( iconHTML && 'before' !== settings.button_icon_position ) { #>
											<span class="sei__button-icon">{{{ iconHTML.value }}}</span>
										<# } #>
									</{{{ buttonTag }}}>
								<# } #>
							</div>
						</div>
					<# } #>
				</div>
			</div>
		</div>
		<?php
	}
}
