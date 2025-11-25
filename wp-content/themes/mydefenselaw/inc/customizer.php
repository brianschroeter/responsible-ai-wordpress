<?php
/**
 * Theme Customizer
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

/**
 * Add customizer settings
 */
function mydefenselaw_customize_register($wp_customize) {
    // Contact Information Section
    $wp_customize->add_section('mydefenselaw_contact', array(
        'title'       => __('Contact Information', 'mydefenselaw'),
        'priority'    => 30,
        'description' => __('Configure contact details displayed throughout the site. Note: If ACF Pro is active, these may be overridden by Theme Settings.', 'mydefenselaw'),
    ));

    // Phone Number
    $wp_customize->add_setting('contact_phone', array(
        'default'           => '888.444.0253',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('contact_phone', array(
        'label'       => __('Primary Phone Number', 'mydefenselaw'),
        'section'     => 'mydefenselaw_contact',
        'type'        => 'text',
        'description' => __('Main phone number displayed site-wide', 'mydefenselaw'),
    ));

    // Secondary Phone Number
    $wp_customize->add_setting('mydefenselaw_phone', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('mydefenselaw_phone', array(
        'label'       => __('Secondary Phone Number', 'mydefenselaw'),
        'section'     => 'mydefenselaw_contact',
        'type'        => 'text',
        'description' => __('Optional secondary phone number', 'mydefenselaw'),
    ));

    // Email Address
    $wp_customize->add_setting('mydefenselaw_email', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('mydefenselaw_email', array(
        'label'       => __('Email Address', 'mydefenselaw'),
        'section'     => 'mydefenselaw_contact',
        'type'        => 'email',
        'description' => __('Enter email address for contact', 'mydefenselaw'),
    ));

    // Office Address
    $wp_customize->add_setting('mydefenselaw_address', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('mydefenselaw_address', array(
        'label'       => __('Office Address', 'mydefenselaw'),
        'section'     => 'mydefenselaw_contact',
        'type'        => 'textarea',
        'description' => __('Enter office address', 'mydefenselaw'),
    ));

    // Social Media Section
    $wp_customize->add_section('mydefenselaw_social', array(
        'title'    => __('Social Media Links', 'mydefenselaw'),
        'priority' => 31,
    ));

    // Facebook
    $wp_customize->add_setting('mydefenselaw_facebook', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('mydefenselaw_facebook', array(
        'label'   => __('Facebook URL', 'mydefenselaw'),
        'section' => 'mydefenselaw_social',
        'type'    => 'url',
    ));

    // Twitter
    $wp_customize->add_setting('mydefenselaw_twitter', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('mydefenselaw_twitter', array(
        'label'   => __('Twitter URL', 'mydefenselaw'),
        'section' => 'mydefenselaw_social',
        'type'    => 'url',
    ));

    // LinkedIn
    $wp_customize->add_setting('mydefenselaw_linkedin', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('mydefenselaw_linkedin', array(
        'label'   => __('LinkedIn URL', 'mydefenselaw'),
        'section' => 'mydefenselaw_social',
        'type'    => 'url',
    ));

    // Instagram
    $wp_customize->add_setting('mydefenselaw_instagram', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('mydefenselaw_instagram', array(
        'label'   => __('Instagram URL', 'mydefenselaw'),
        'section' => 'mydefenselaw_social',
        'type'    => 'url',
    ));

    // YouTube
    $wp_customize->add_setting('mydefenselaw_youtube', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('mydefenselaw_youtube', array(
        'label'   => __('YouTube URL', 'mydefenselaw'),
        'section' => 'mydefenselaw_social',
        'type'    => 'url',
    ));

    // Hero Section
    $wp_customize->add_section('mydefenselaw_hero', array(
        'title'       => __('Hero Section', 'mydefenselaw'),
        'priority'    => 35,
        'description' => __('Customize the homepage hero section. Note: If using ACF fields on the homepage, those will take priority.', 'mydefenselaw'),
    ));

    // Hero Title
    $wp_customize->add_setting('hero_title', array(
        'default'           => 'Protecting Your Rights. Fighting for Your Future.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('hero_title', array(
        'label'   => __('Hero Title', 'mydefenselaw'),
        'section' => 'mydefenselaw_hero',
        'type'    => 'text',
    ));

    // Hero Subtitle
    $wp_customize->add_setting('hero_subtitle', array(
        'default'           => 'Trusted legal representation in South Florida for over 25 years.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('hero_subtitle', array(
        'label'   => __('Hero Subtitle', 'mydefenselaw'),
        'section' => 'mydefenselaw_hero',
        'type'    => 'textarea',
    ));

    // Footer Section
    $wp_customize->add_section('mydefenselaw_footer', array(
        'title'    => __('Footer Settings', 'mydefenselaw'),
        'priority' => 40,
    ));

    // Footer Tagline
    $wp_customize->add_setting('footer_tagline', array(
        'default'           => 'Protecting your rights and fighting for your future for over 25 years.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('footer_tagline', array(
        'label'   => __('Footer Tagline', 'mydefenselaw'),
        'section' => 'mydefenselaw_footer',
        'type'    => 'text',
    ));

    // Copyright Text
    $wp_customize->add_setting('copyright_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('copyright_text', array(
        'label'       => __('Custom Copyright Text', 'mydefenselaw'),
        'section'     => 'mydefenselaw_footer',
        'type'        => 'text',
        'description' => __('Leave empty for default copyright text', 'mydefenselaw'),
    ));
}
add_action('customize_register', 'mydefenselaw_customize_register');

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously
 */
function mydefenselaw_customize_preview_js() {
    wp_enqueue_script(
        'mydefenselaw-customizer',
        get_template_directory_uri() . '/assets/js/customizer.js',
        array('customize-preview'),
        MYDEFENSELAW_VERSION,
        true
    );
}
add_action('customize_preview_init', 'mydefenselaw_customize_preview_js');
