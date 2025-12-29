<?php
/**
 * @category  ElielWeb
 * @package   ElielWeb_InstagramFeed
 * @author    Elie <elie@redline.paris>
 * @copyright Copyright (c) 2025 RedLine
 */
/**
 * Copyright © Redline. All rights reserved.
 * Instagram Feed Module for Hyva Theme
 */
declare(strict_types=1);

namespace Elie\InstagramFeed\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;

class Feed extends Template
{
    private const XML_PATH_ENABLED = 'elie_instagram/general/enabled';
    private const XML_PATH_ACCESS_TOKEN = 'elie_instagram/general/access_token';
    private const XML_PATH_USERNAME = 'elie_instagram/general/username';
    private const XML_PATH_LIMIT = 'elie_instagram/general/limit';

    public function __construct(
        Context $context,
        private readonly Curl $curl,
        private readonly Json $json,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Check if module is enabled
     */
    public function isEnabled(): bool
    {
        return (bool) $this->_scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Instagram username
     */
    public function getUsername(): string
    {
        return (string) $this->_scopeConfig->getValue(
            self::XML_PATH_USERNAME,
            ScopeInterface::SCOPE_STORE
        ) ?: 'redline_paris';
    }

    /**
     * Get number of posts to display
     */
    public function getLimit(): int
    {
        $limit = (int) $this->_scopeConfig->getValue(
            self::XML_PATH_LIMIT,
            ScopeInterface::SCOPE_STORE
        );
        return $limit > 0 ? $limit : 6;
    }

    /**
     * Get Instagram posts
     *
     * @return array
     */
    public function getPosts(): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        $accessToken = $this->_scopeConfig->getValue(
            self::XML_PATH_ACCESS_TOKEN,
            ScopeInterface::SCOPE_STORE
        );

        if (!$accessToken) {
            return $this->getMockPosts();
        }

        try {
            $url = sprintf(
                'https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,permalink,thumbnail_url,timestamp&access_token=%s&limit=%d',
                $accessToken,
                $this->getLimit()
            );

            $this->curl->get($url);
            $response = $this->curl->getBody();
            $data = $this->json->unserialize($response);

            if (isset($data['data']) && is_array($data['data'])) {
                return array_filter($data['data'], function($item) {
                    return isset($item['media_type']) && in_array($item['media_type'], ['IMAGE', 'CAROUSEL_ALBUM']);
                });
            }
        } catch (\Exception $e) {
            $this->_logger->error('Instagram Feed Error: ' . $e->getMessage());
        }

        return $this->getMockPosts();
    }

    /**
     * Get mock posts for demo/fallback
     *
     * @return array
     */
    private function getMockPosts(): array
    {
        return [
            [
                'id' => '1',
                'media_url' => 'https://via.placeholder.com/400x400/f0f0f0/333333?text=Instagram+1',
                'permalink' => 'https://instagram.com/' . $this->getUsername(),
                'caption' => 'Redline Paris Jewelry'
            ],
            [
                'id' => '2',
                'media_url' => 'https://via.placeholder.com/400x600/f0f0f0/333333?text=Instagram+2',
                'permalink' => 'https://instagram.com/' . $this->getUsername(),
                'caption' => 'Redline Paris Collection'
            ],
            [
                'id' => '3',
                'media_url' => 'https://via.placeholder.com/400x400/f0f0f0/333333?text=Instagram+3',
                'permalink' => 'https://instagram.com/' . $this->getUsername(),
                'caption' => 'Haute Couture Jewelry'
            ],
            [
                'id' => '4',
                'media_url' => 'https://via.placeholder.com/600x400/f0f0f0/333333?text=Instagram+4',
                'permalink' => 'https://instagram.com/' . $this->getUsername(),
                'caption' => 'Diamond Collection'
            ],
            [
                'id' => '5',
                'media_url' => 'https://via.placeholder.com/400x400/f0f0f0/333333?text=Instagram+5',
                'permalink' => 'https://instagram.com/' . $this->getUsername(),
                'caption' => 'Redline Bracelets'
            ],
            [
                'id' => '6',
                'media_url' => 'https://via.placeholder.com/400x500/f0f0f0/333333?text=Instagram+6',
                'permalink' => 'https://instagram.com/' . $this->getUsername(),
                'caption' => 'Luxury Jewelry'
            ]
        ];
    }

    /**
     * Get Instagram profile URL
     */
    public function getProfileUrl(): string
    {
        return 'https://www.instagram.com/' . $this->getUsername();
    }
}
