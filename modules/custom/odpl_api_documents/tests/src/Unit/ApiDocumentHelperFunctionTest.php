<?php

namespace Drupal\Tests\odpl_api_documents\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\odpl_sdk\GenerateSdk;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\file\FileInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\taxonomy\TermInterface;
use Drupal\odpl_api_documents\ApiDocumentHelper;
use Drupal\Core\Path\CurrentPathStack;

/**
 * Simple test to ensure that asserts pass.
 *
 * @group odpl_api
 */
class ApiDocumentHelperFunctionTest extends UnitTestCase {
  use StringTranslationTrait;

  /**
   * GenerateSdk variable.
   *
   * @var Drupal\odpl_sdk\GenerateSdk
   */
  protected $generateSdk;

  /**
   * Api Document Helper variable.
   *
   * @var Drupal\odpl_api_documents\ApiDocumentHelper
   */
  protected $apiDocumentHelper;


  /**
   * The container.
   *
   * @var \Drupal\Core\DependencyInjection\ContainerBuilder
   */
  protected $container;

  /**
   * The request stack used for testing.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The current path.
   *
   * @var \Drupal\Core\Path\CurrentPathStack|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $nodeStorage;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * File Storage.
   *
   * @var Drupal\file\FileStorage
   */
  protected $fileStorage;

  /**
   * Taxonomy Term Storage.
   *
   * @var Drupal\taxonomy\TermStorage
   */
  protected $taxonomyTermStorage;


  /**
   * Mock HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $mockHttpClient;

  /**
   * Drupal\Core\Messenger\MessengerInterface definition.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Drupal\Core\Logger\LoggerChannelFactoryInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The mocked url generator.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  protected $fileUrlGenerator;

  /**
   * Create new ApiDocumentCommonFunction object.
   */
  public function setUp() {

    parent::setUp();
    $this->container = new ContainerBuilder();

    $this->nodeStorage = $this->createMock('Drupal\node\NodeStorage');
    $this->entity_type_manager = $this->createMock('Drupal\Core\Entity\EntityTypeManagerInterface');
    $this->messenger = $this->createMock('Drupal\Core\Messenger\MessengerInterface');
    $this->logger_factory = $this->createMock('\Drupal\Core\Logger\LoggerChannelFactoryInterface');
    $this->mockHttpClient = $this->createMock('\GuzzleHttp\ClientInterface');

    $this->fileUrlGenerator = $this->getMockBuilder('\Drupal\Core\File\FileUrlGenerator')
      ->disableOriginalConstructor()
      ->getMock();

    \Drupal::setContainer($this->container);

    $this->generateSdk = new GenerateSdk($this->entity_type_manager, $this->mockHttpClient, $this->messenger, $this->logger_factory, $this->fileUrlGenerator);
    $this->apiDocumentHelper = new ApiDocumentHelper();
  }

  /**
   * Test for Api File Url.
   */
  public function testgetApiSpecFromNode() {

    $field_definition = $this->createMock(FieldDefinitionInterface::class);
    /** @var \Drupal\Core\Field\FieldItemList|\PHPUnit\Framework\MockObject\MockObject $field_list */
    $field_list = $this->getMockBuilder(FieldItemList::class)
      ->onlyMethods(['defaultValueWidget', 'getValue'])
      ->setConstructorArgs([$field_definition])
      ->getMock();

    $field_list->expects($this->any())
      ->method('getValue')
      ->willReturn([['target_id' => '49']]);

    $nid = '42';
    /** @var \Drupal\node\NodeInterface|\PHPUnit\Framework\MockObject\MockObject $node */
    $node = $this->createMock(NodeInterface::class);
    $file = $this->createMock(FileInterface::class);

    $node->expects($this->any())
      ->method('get')
      ->with('field_document')
      ->will($this->returnValue($field_list));

    $this->nodeStorage = $this->getMockBuilder('Drupal\node\NodeStorage')
      ->disableOriginalConstructor()
      ->getMock();

    $this->nodeStorage->expects($this->any())
      ->method('load')
      ->with($nid)
      ->willReturn($node);

    $file->expects($this->any())
      ->method('getFileUri')
      ->willReturn('public://2021-11/sample_1_0_0.json');

    $this->fileStorage = $this->getMockBuilder('Drupal\file\FileStorage')
      ->disableOriginalConstructor()
      ->getMock();

    $this->fileStorage->expects($this->any())
      ->method('load')
      ->with('49')
      ->willReturn($file);

    $this->entity_type_manager->expects($this->any())
      ->method('getStorage')
      ->with($this->logicalOr(
            $this->equalTo('node'),
            $this->equalTo('file')
          ))
      ->will($this->returnCallback(
              function ($param) {
                if ($param == 'node') {
                  return $this->nodeStorage;
                }
                else {
                  return $this->fileStorage;
                }
              }
            ));

    $this->fileUrlGenerator->expects($this->any())
      ->method('generateAbsoluteString')
      ->with('public://2021-11/sample_1_0_0.json')
      ->willReturn('public://2021-11/sample_1_0_0.json');

    $this->assertNotNull($this->generateSdk->getApiSpecFromNode($nid));

  }

  /**
   * Test for Sdk Languages.
   */
  public function testgetSdkLanguages() {

    $field_definition = $this->createMock(FieldDefinitionInterface::class);
    /** @var \Drupal\Core\Field\FieldItemList|\PHPUnit\Framework\MockObject\MockObject $field_list */
    $field_list = $this->getMockBuilder(FieldItemList::class)
      ->onlyMethods(['defaultValueWidget', 'getValue'])
      ->setConstructorArgs([$field_definition])
      ->getMock();

    $field_list_results = [
      0 => ['target_id' => '32'],
    ];

    $field_list->expects($this->any())
      ->method('getValue')
      ->will($this->returnValue($field_list_results));

    $nid = '42';
    /** @var \Drupal\node\NodeInterface|\PHPUnit\Framework\MockObject\MockObject $node */
    $node = $this->createMock(NodeInterface::class);

    $node->expects($this->any())
      ->method('get')
      ->with('field_sdk_language')
      ->will($this->returnValue($field_list));

    $this->nodeStorage = $this->getMockBuilder('Drupal\node\NodeStorage')
      ->disableOriginalConstructor()
      ->getMock();

    $this->nodeStorage->expects($this->any())
      ->method('load')
      ->with($nid)
      ->willReturn($node);

    $term = $this->createMock(TermInterface::class);

    $term->expects($this->any())
      ->method('getName')
      ->will($this->returnValue('java'));

    $this->taxonomyTermStorage = $this->getMockBuilder('Drupal\taxonomy\TermStorage')
      ->disableOriginalConstructor()
      ->getMock();

    $this->taxonomyTermStorage->expects($this->any())
      ->method('load')
      ->with('32')
      ->willReturn($term);

    $this->entity_type_manager->expects($this->any())
      ->method('getStorage')
      ->with($this->logicalOr(
            $this->equalTo('node'),
            $this->equalTo('taxonomy_term')
          ))
      ->will($this->returnCallback(
              function ($param) {
                if ($param == 'node') {
                  return $this->nodeStorage;
                }
                else {
                  return $this->taxonomyTermStorage;
                }
              }
            ));

    $notExpected = ['-none' => 'Select'];
    $actual = $this->generateSdk->getSdkLanguages($nid);

    $this->assertNotSame($actual, $notExpected);

  }

  /**
   * Test for Get Node id.
   */
  public function testgetNodeId() {

    /** @var \Drupal\Core\Path\CurrentPathStack $path_matcher */
    $path_current = $this->prophesize(CurrentPathStack::class);
    $path_current->getPath()->willReturn('/node/1');
    $this->container->set('path.current', $path_current->reveal());

    $expected = '1';
    $actual = $this->apiDocumentHelper->getNodeId();

    $this->assertSame($actual, $expected);

  }

}
