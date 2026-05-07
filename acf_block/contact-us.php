<?php

$heading                  = (function_exists('get_field') && $heading = get_field('heading')) ? $heading : '';
$heading_body             = (function_exists('get_field') && $heading_body = get_field('heading_body')) ? $heading_body : '';
$form_short_code          = (function_exists('get_field') && $form_short_code = get_field('form_short_code')) ? $form_short_code : '';
$side_information_heading = (function_exists('get_field') && $side_information_heading = get_field('side_information')['heading']) ? $side_information_heading : '';
$side_information_body    = (function_exists('get_field') && $side_information_body = get_field('side_information')['heading_body']) ? $side_information_body : '';

$social_links = [
    [
        'icon'  => 'twitter',
        'label' => 'Twitter',
        'url'   => 'https://twitter.com/EuSpending',
    ],
    [
        'icon'  => 'linkedin',
        'label' => 'LinkedIn',
        'url'   => 'https://linkedin.com/company/open-spending-eu-coalition/',
    ],
    [
        'icon'  => 'youtube',
        'label' => 'YouTube',
        'url'   => 'https://youtube.com/openspendingeu',
    ],
];

?>

<div class="contact-us pb-10 sm:pb-14 md:pb-28">
    <h2 class="font-bold"><?php echo wp_kses_post($heading); ?></h2>
    <p class="sm:mt-4 text-base sm:text-lg"><?php echo wp_kses_post($heading_body); ?></p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 mt-6 sm:mt-9">
        <div class="lg:col-span-5 px-8 py-10 sm:py-14 md:pt-24 sm:pl-14 md:pl-8 lg:pl-14 sm:pr-11 rounded-t-lg md:rounded-tr-none md:rounded-l-lg text-n-0 contact-us-background">
            <h3 class="font-bold"><?php echo wp_kses_post($side_information_heading); ?></h3>
            <p class="text-base pt-1 sm:pt-4"><?php echo wp_kses_post($side_information_body); ?></p>
            <ul class="mt-8 sm:mt-12 contact-admin">
                <?php foreach ($social_links as $link) { ?>
                <li class="flex gap-x-1.5 sm:gap-x-2.5 mt-4 sm:mt-6 icon-hover max-w-fit">
                    <a href="<?php echo esc_url($link['url']); ?>" target="_blank" rel="noopener noreferrer" class="flex gap-x-1.5 sm:gap-x-2.5 items-center">
                        <span class="flex justify-center items-center h-4 w-4 sm:h-6 sm:w-6 transition-all cursor-pointer duration-300">
                            <?php echo useSvg($link['icon']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
                        </span>
                        <span class="contact-links"><?php echo esc_html($link['label']); ?></span>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <div
            class="lg:col-start-6 lg:col-span-7 px-8 py-10 sm:px-14 md:px-8 sm:py-14 md:pt-24 md:pb-48 rounded-b-lg md:rounded-bl-none md:rounded-r-lg bg-n-10">
            <?php echo do_shortcode(wp_kses_post($form_short_code)); ?>
        </div>
    </div>
</div>
