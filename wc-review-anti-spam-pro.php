<?php
/**
 * Plugin Name: WooCommerce Review Anti-Spam Pro
 * Description:  A plugin to manage Blocks spam and inappropriate content in WooCommerce product reviews.
 * Version: 2.1
 * Author: biplob043013
 * Author URI: https://automattic.com
 * Text Domain: woocommerce-review-anti-spam-pro
 * Domain Path: /languages
 * Requires PHP: 8.0
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins:  woocommerce
 *
 * @package woocommerce-review-anti-spam-pro
 * @author biplob043013
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


add_action( 'plugins_loaded', 'wc_review_anti_spam_pro_init' );

function wc_review_anti_spam_pro_init() {
    // Check if WooCommerce is active
    if ( class_exists( 'WooCommerce' ) ) {
        add_filter( 'pre_comment_approved', 'wc_block_spammy_product_reviews', 10, 2 );
    }
}

function wc_block_spammy_product_reviews( $approved, $commentdata ) {
    // Apply only to WooCommerce products
    if ( isset( $commentdata['comment_post_ID'] ) && get_post_type( $commentdata['comment_post_ID'] ) === 'product' ) {
        $content = strtolower( $commentdata['comment_content'] );

        $blocked_keywords = array(
            'bitcoin', 'crypto', 'xxx', 'porn', 'viagra', 'casino', 'betting',
            'sex', 'nude', 'loan', 'free money', 'hack', 'scam', 'earn fast',
            'যৌন', 'জুয়া', 'হ্যাকার', 'টাকা ইনকাম', 'ফ্রি টাকা'
        );

        $blocked_domains = array(
            '1xbet', 'bet365', 'pornhub', 'xvideos', 'bangbros', 'viagra.com',
            'casino', 'gambling', 'betting', 'darkweb', 'escort', 'sextoys'
        );

        // Check for URLs
        if ( preg_match( '/https?:\/\/|www\.|\.com|\.net|\.org/i', $content ) ) {
            foreach ( $blocked_domains as $domain ) {
                if ( strpos( $content, $domain ) !== false ) {
                    return 'spam';
                }
            }
            return 'spam';
        }

        // Check for email addresses
        if ( preg_match( '/[\w\.-]+@[\w\.-]+\.[a-z]{2,6}/', $content ) ) {
            return 'spam';
        }

        // Check for blacklisted words
        foreach ( $blocked_keywords as $word ) {
            if ( strpos( $content, $word ) !== false ) {
                return 'spam';
            }
        }

        // Emoji or Unicode spam detection
        if ( preg_match( '/[\x{1F600}-\x{1F64F}\x{2700}-\x{27BF}]/u', $content ) ) {
            return 'spam';
        }
    }

    return $approved;
}



