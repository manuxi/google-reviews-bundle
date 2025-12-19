<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Tests\DependencyInjection;

use Manuxi\GoogleReviewsBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    private Configuration $configuration;
    private Processor $processor;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testImplementsConfigurationInterface(): void
    {
        $this->assertInstanceOf(ConfigurationInterface::class, $this->configuration);
    }

    public function testMinimalConfiguration(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'test-cid',
                    'api_key' => 'test-key',
                ],
            ],
        ]);

        $this->assertSame('en', $config['connector']['locale']);
        $this->assertSame('test-cid', $config['connector']['cid']);
        $this->assertSame('test-key', $config['connector']['api_key']);
        $this->assertTrue($config['cache']['enabled']);
        $this->assertSame('cache.app', $config['cache']['pool']);
        $this->assertSame(86400, $config['cache']['ttl']);
    }

    public function testMissingCidThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'api_key' => 'test-key',
                ],
            ],
        ]);
    }

    public function testMissingApiKeyThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'test-cid',
                ],
            ],
        ]);
    }

    public function testEmptyCidThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => '',
                    'api_key' => 'test-key',
                ],
            ],
        ]);
    }

    public function testEmptyApiKeyThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'test-cid',
                    'api_key' => '',
                ],
            ],
        ]);
    }

    public function testConnectorConfiguration(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'my-cid-123',
                    'api_key' => 'my-api-key-456',
                    'locale' => 'de',
                ],
            ],
        ]);

        $this->assertSame('my-cid-123', $config['connector']['cid']);
        $this->assertSame('my-api-key-456', $config['connector']['api_key']);
        $this->assertSame('de', $config['connector']['locale']);
    }

    public function testCacheConfiguration(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'test-cid',
                    'api_key' => 'test-key',
                ],
                'cache' => [
                    'enabled' => false,
                    'pool' => 'cache.system',
                    'ttl' => 3600,
                ],
            ],
        ]);

        $this->assertFalse($config['cache']['enabled']);
        $this->assertSame('cache.system', $config['cache']['pool']);
        $this->assertSame(3600, $config['cache']['ttl']);
    }

    public function testCacheTtlMinimum(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'test-cid',
                    'api_key' => 'test-key',
                ],
                'cache' => [
                    'ttl' => 60,
                ],
            ],
        ]);

        $this->assertSame(60, $config['cache']['ttl']);
    }

    public function testCacheTtlMaximum(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'test-cid',
                    'api_key' => 'test-key',
                ],
                'cache' => [
                    'ttl' => 2419200,
                ],
            ],
        ]);

        $this->assertSame(2419200, $config['cache']['ttl']);
    }

    public function testCacheTtlBelowMinimumThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'test-cid',
                    'api_key' => 'test-key',
                ],
                'cache' => [
                    'ttl' => 59,
                ],
            ],
        ]);
    }

    public function testCacheTtlAboveMaximumThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'test-cid',
                    'api_key' => 'test-key',
                ],
                'cache' => [
                    'ttl' => 2419201,
                ],
            ],
        ]);
    }

    public function testFullConfiguration(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'business-cid',
                    'api_key' => 'secret-key',
                    'locale' => 'fr',
                ],
                'cache' => [
                    'enabled' => true,
                    'pool' => 'cache.redis',
                    'ttl' => 43200,
                ],
            ],
        ]);

        $this->assertSame('business-cid', $config['connector']['cid']);
        $this->assertSame('secret-key', $config['connector']['api_key']);
        $this->assertSame('fr', $config['connector']['locale']);
        $this->assertTrue($config['cache']['enabled']);
        $this->assertSame('cache.redis', $config['cache']['pool']);
        $this->assertSame(43200, $config['cache']['ttl']);
    }

    public function testTreeBuilderRootName(): void
    {
        $treeBuilder = $this->configuration->getConfigTreeBuilder();

        $this->assertSame('manuxi_google_reviews', $treeBuilder->buildTree()->getName());
    }

    public function testMultipleConfigurationMerge(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'first-cid',
                    'api_key' => 'first-key',
                ],
            ],
            [
                'connector' => [
                    'cid' => 'first-cid',
                    'api_key' => 'merged-key',
                ],
            ],
        ]);

        $this->assertSame('first-cid', $config['connector']['cid']);
        $this->assertSame('merged-key', $config['connector']['api_key']);
    }

    public function testCacheDisabled(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'connector' => [
                    'cid' => 'test-cid',
                    'api_key' => 'test-key',
                ],
                'cache' => [
                    'enabled' => false,
                ],
            ],
        ]);

        $this->assertFalse($config['cache']['enabled']);
    }
}
