<?php declare(strict_types=1);

namespace DasGrossNetSwitch\Events\Subscriber;

use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Page\Wishlist\GuestWishlistPageLoadedHook;
use Shopware\Storefront\Page\Wishlist\WishlistPageLoadedHook;
use Shopware\Storefront\Pagelet\Wishlist\GuestWishlistPageletLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DasStorefrontSubscriber implements EventSubscriberInterface
{
    private const DEFAULT_CONFIG = ['grossNetSwitch' => true];

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(
        private RequestStack $requestStack
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GenericPageLoadedEvent::class => 'onPageLoaded',
            GuestWishlistPageletLoadedEvent::class => 'onWishlistPageletLoaded',
            GuestWishlistPageLoadedHook::class => 'onWishlistPageLoaded',
            WishlistPageLoadedHook::class => 'onWishlistPageLoadedHook',
        ];
    }

    /**
     * @param GenericPageLoadedEvent $pageLoadedEvent
     */
    public function onPageLoaded(GenericPageLoadedEvent $pageLoadedEvent): void
    {
        $main = $this->requestStack->getMainRequest();
        $page = $pageLoadedEvent->getPage();
        $this->addPageExtention($main, $page);
    }

    /**
     * @param GuestWishlistPageletLoadedEvent $pageLoadedEvent
     */
    public function onWishlistPageletLoaded(GuestWishlistPageletLoadedEvent $pageLoadedEvent): void
    {
        $main = $this->requestStack->getMainRequest();
        $page = $pageLoadedEvent->getPagelet();
        $this->addPageExtention($main, $page);
    }

    /**
     * @param GuestWishlistPageLoadedHook $pageLoadedEvent
     */
    public function onWishlistPageLoaded(GuestWishlistPageLoadedHook $pageLoadedEvent): void
    {
        $main = $this->requestStack->getMainRequest();
        $page = $pageLoadedEvent->getPage();
        $this->addPageExtention($main, $page);
    }

    /**
     * @param WishlistPageLoadedHook $pageLoadedEvent
     */
    public function onWishlistPageLoadedHook(WishlistPageLoadedHook $pageLoadedEvent): void
    {
        $main = $this->requestStack->getMainRequest();
        $page = $pageLoadedEvent->getPage();
        $this->addPageExtention($main, $page);
    }

    /**
     * @param $main
     * @param $page
     */
    private function addPageExtention($main, $page):void
    {
        $config = self::DEFAULT_CONFIG;

        if ($main) {
            $session = $main->getSession();
            if ($session->isStarted()) {
                $sessionConfig = $session->get('dasConfig', []);
                $config = array_merge(self::DEFAULT_CONFIG, $sessionConfig);
                $session->set('dasConfig', $config);
            }
        }

        $page->addExtension('dasGrossNetConfig', new ArrayStruct($config));
    }
}