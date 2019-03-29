<?php
/*
Plugin Name: Helcim Commerce for Magento 2
Plugin URI: https://github.com/helcim/magento-2/
Description: Helcim Commerce for Magento 2
Version: 1.1.2
Author: Helcim Inc.
Author URI: https://www.helcim.com/
*/

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Helcim_HelcimAPI',
    __DIR__
);
