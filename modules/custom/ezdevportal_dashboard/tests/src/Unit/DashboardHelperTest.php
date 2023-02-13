<?php

namespace Drupal\Tests\ezdevportal_dashboard\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\ezdevportal_dashboard\DashboardHelper;
use Drupal\Core\DependencyInjection\ContainerBuilder;

/**
 * Simple test to ensure that asserts pass.
 *
 * @group ezdevportal_dashboard
 */
class DashboardHelperTest extends UnitTestCase {

  /**
   * The dashboard variable.
   *
   * @var Drupal\ezdevportal_dashboard\GeneralFunctional
   */
  protected $dashboard;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $currentUser;

  /**
   * The mocked url generator.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  protected $urlGenerator;

  /**
   * The container.
   *
   * @var \Drupal\Core\DependencyInjection\ContainerBuilder
   */
  protected $container;

  /**
   * Create new GeneralFunctional object.
   */
  public function setUp() {
    $this->container = new ContainerBuilder();

    $this->currentUser = $this->createMock('Drupal\Core\Session\AccountProxyInterface');
    $this->currentUser->expects($this->any())
      ->method('id')
      ->willReturn(1);

    $this->urlGenerator = $this->getMockBuilder('\Drupal\Core\Routing\UrlGenerator')
      ->disableOriginalConstructor()
      ->getMock();

    $this->container->set('current_user', $this->currentUser);
    $this->container->set('url_generator', $this->urlGenerator);

    \Drupal::setContainer($this->container);

    $this->dashboard = new DashboardHelper();
  }

  /**
   * Tests Navigation Default Data in array.
   */
  public function testdeveloperNavigationDefaultData() {

    $expected_value = [
      [
        'text' => 'Go to your Apps',
        'path' => '/dashboard?view=app',
        'visibility' => 'developer',
      ],
      [
        'text' => 'Get Started',
        'path' => '/quick-guide',
        'visibility' => 'developer',
      ],
      [
        'text' => 'FAQs',
        'path' => '/faqs',
        'visibility' => 'developer',
      ],
      [
        'text' => 'ACCOUNT SETTINGS',
        'path' => '',
        'visibility' => 'siteadmin',
      ],
      [
        'text' => 'VIEW SITE',
        'path' => '/home',
        'visibility' => 'siteadmin',
      ],
    ];

    $actual_value = $this->dashboard->developerNavigationDefaultData();

    $this->assertSame($actual_value, $expected_value);

  }

  /**
   * Tests navigation class.
   */
  public function testgetNavigationClass() {

    $expected_value = 'dashboard-dom-master-link';

    $actual_value = $this->dashboard->getNavigationClass('Dom master');

    $this->assertSame($actual_value, $expected_value);

  }

  /**
   * Tests Navigation Default Data in array.
   */
  public function testdashboardNavigationDefaultData() {
    $expected_value = [
      [
        'type' => 'Applications',
        'text' => 'My Application',
        'path' => '/dashboard',
        'add_icon_path' => '/user/apps',
      ],
      [
        'type' => 'Support',
        'text' => 'Support',
        'path' => '?view=support',
        'add_icon_path' => '/node/add/issue',
      ],
      [
        'type' => 'Forum',
        'text' => 'Forum',
        'path' => '?view=forum',
        'add_icon_path' => '/node/add/forum',
      ],
    ];

    $actual_value = $this->dashboard->dashboardNavigationDefaultData();

    $this->assertSame($actual_value, $expected_value);

  }

  /**
   * Tests icon path.
   */
  public function testgetIconPath() {
    $expected_value = '/add/node/forum';

    $actual_value = $this->dashboard->getIconPath('/add/node/forum');

    $this->assertSame($actual_value, $expected_value);

  }

  /**
   * Tests icon path when if condition will be true.
   */
  public function testIconPath() {
    // Test case for when if condition will be true.
    $this->urlGenerator->expects($this->once())
      ->method('generateFromRoute')
      ->with('entity.developer_app.add_form_for_developer', ['user' => '1'])
      ->willReturn('/user/1/create-app');

    $expected_value = '/user/1/create-app';

    $actual_value = $this->dashboard->getIconPath('/user/apps');

    $this->assertSame($actual_value, $expected_value);

  }

}
