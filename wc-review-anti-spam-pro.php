<?php
/**
 * Plugin Name: WooCommerce Review Anti-Spam (Pro)
 * Description: Blocks spam and inappropriate content in WooCommerce product reviews.
 * Version: 2.1
 * Author: Biplob
 */

add_filter('pre_comment_approved', 'wc_block_spammy_product_reviews', 10, 2);

function wc_block_spammy_product_reviews($approved, $commentdata) {
    // Apply only to WooCommerce products
    if (isset($commentdata['comment_post_ID']) && get_post_type($commentdata['comment_post_ID']) === 'product') {
        $content = strtolower($commentdata['comment_content']);

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
        if (preg_match('/https?:\/\/|www\.|\.com|\.net|\.org/i', $content)) {
            foreach ($blocked_domains as $domain) {
                if (strpos($content, $domain) !== false) {
                    return 'spam';
                }
            }
            return 'spam';
        }

        // Check for email addresses
        if (preg_match('/[\w\.-]+@[\w\.-]+\.[a-z]{2,6}/', $content)) {
            return 'spam';
        }

        // Check for blacklisted words
        foreach ($blocked_keywords as $word) {
            if (strpos($content, $word) !== false) {
                return 'spam';
            }
        }

        // Emoji or Unicode spam detection
        if (preg_match('/[\x{1F600}-\x{1F64F}\x{2700}-\x{27BF}]/u', $content)) {
            return 'spam';
        }
    }

    return $approved;
}
