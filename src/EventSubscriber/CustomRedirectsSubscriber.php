<?php

declare(strict_types=1);

namespace Drupal\custom_redirects\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\node\NodeInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Handles custom redirects based on kernel events.
 */
final class CustomRedirectsSubscriber implements EventSubscriberInterface {

  /**
   * Kernel request event handler.
   */
  public function onKernelRequest(RequestEvent $event): void {
    $request = $event->getRequest();
    $route_name = $request->attributes->get('_route');
    $node = $request->attributes->get('node');

    // Nid 2 is just an example. Set the route you wish. 
    if ($route_name === 'entity.node.canonical' && $node instanceof NodeInterface && $node->id() == 2) {
      user_logout();

      // Drupal.org is just an example. Set the web you wish.
      $response = new TrustedRedirectResponse('https://www.drupal.org', Response::HTTP_FOUND);
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

