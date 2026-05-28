<?php declare(strict_types=1);

namespace DasGrossNetSwitch\Controller;

use Shopware\Core\Content\Category\SalesChannel\AbstractCategoryRoute;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Cms\CmsPageLoadedHook;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\Package;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
#[Package(SwitchController::class)]
#[Route(defaults: ['_routeScope' => ['storefront']])]
#[Package('storefront')]
class SwitchController extends StorefrontController
{
    private const DEFAULT_CONFIG = ['grossNetSwitch' => true];

    public function __construct(
        private RequestStack $requestStack,
        private AbstractCategoryRoute $categoryRoute
    )
    {
    }

    /**
     * @param Request $request
     * @param Context $context
     * @param string $toggle
     * @return JsonResponse
     */
    #[Route(path: '/das-gross-net/{toggle}', name: "frontend.das.grossNet.toggle", methods: ['GET'])]
    public function toggleGrossNet(Request $request, Context $context, string $toggle): JsonResponse
    {
        $mainRequest = $this->requestStack->getMainRequest();

        if ($mainRequest) {
            $session = $mainRequest->getSession();

            // Ensure the toggle parameter is a boolean
            $toggle = filter_var($toggle, FILTER_VALIDATE_BOOLEAN);

            // Get the current config or default if not set
            $config = $session->get('dasConfig', self::DEFAULT_CONFIG);

            // Update the grossNetSwitch value
            $config['grossNetSwitch'] = $toggle ? true : false;
            $session->set('dasConfig', $config);
            // Return the new state
            return new JsonResponse([
                "grossNetSwitch" => $toggle ? 'gross' : 'net'
            ]);
        }


        // Return a failed state if we couldn't get the session or main request
        return new JsonResponse([
            "grossNetSwitch" => 'failed'
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string|null $navigationId
     * @param Request $request
     * @param SalesChannelContext $salesChannelContext
     * @return Response
     */
    #[Route(path: '/widgets/das/cms/navigation/{navigationId}', name: 'frontend.das.cms.navigation.page', defaults: ['navigationId' => null, 'XmlHttpRequest' => true, '_httpCache' => true], methods: ['GET', 'POST'])]
    public function category(?string $navigationId, Request $request, SalesChannelContext $salesChannelContext): Response
    {
        if (!$navigationId) {
            throw RoutingException::missingRequestParameter('navigationId');
        }

        $mainRequest = $this->requestStack->getMainRequest();
        $category = $this->categoryRoute->load($navigationId, $request, $salesChannelContext)->getCategory();

        if ($mainRequest) {
            $session = $mainRequest->getSession();

            // Get the current config or default if not set
            $config = $session->get('dasConfig', self::DEFAULT_CONFIG);
        }
        
        $page = $category->getCmsPage();
        $page->addExtension('dasGrossNetConfig', new ArrayStruct($config));
        
        if (!$page) {
            throw new PageNotFoundException('');
        }

        $this->hook(new CmsPageLoadedHook($page, $salesChannelContext));

        $response = $this->renderStorefront('@Storefronts/storefront/page/content/detail.html.twig', ['cmsPage' => $page, 'page' => $page]);
        $response->headers->set('x-robots-tag', 'noindex');

        return $response;
    }
}
