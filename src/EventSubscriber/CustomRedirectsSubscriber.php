<?php

declare(strict_types=1);

namespace Drupal\custom_redirects\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\node\NodeInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Handles custom redirects based on kernel events.
 */
final class CustomRedirectsSubscriber implements EventSubscriberInterface {

  /**
   * The configuration factory.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The request stack.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a CustomRedirectsSubscriber object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(ConfigFactoryInterface $configFactory, RequestStack $requestStack) {
    $this->configFactory = $configFactory;
    $this->requestStack = $requestStack;
  }

  /**
   * Kernel request event handler.
   */
  public function onKernelRequest(RequestEvent $event): void {
    $custom_url = $this->configFactory->get('custom_redirects.settings')->get('url');
    $request = $this->requestStack->getCurrentRequest();
    $base_url = $request->getHost();
    $current_url = $request->getUri();
    $final_redirect = !empty($custom_url) ? $custom_url : $base_url;

    if (str_contains($current_url, 'user/logout')) {
      user_logout();
      $response = new TrustedRedirectResponse($final_redirect, Response::HTTP_FOUND);
      $event->setResponse($response);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['onKernelRequest'],
    ];
  }

}

