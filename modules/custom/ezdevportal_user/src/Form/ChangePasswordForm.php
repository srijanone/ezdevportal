<?php

namespace Drupal\ezdevportal_user\Form;

use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Password\PasswordInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a user password reset form.
 */
class ChangePasswordForm extends FormBase {

  /**
   * The Password Hasher.
   *
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected $passwordHasher;

  /**
   * The user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $userProfile;

  /**
   * Constructs a UserPasswordForm object.
   *
   * @param \Drupal\Core\Password\PasswordInterface $password_hasher
   *   The password service.
   */
  public function __construct(PasswordInterface $password_hasher) {
    $this->passwordHasher = $password_hasher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('password')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'change_pwd_form';
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\user\UserInterface $account */
    $this->userProfile = $account = User::load(\Drupal::currentUser()->id());
    $user = $this->currentUser();
    $config = \Drupal::config('user.settings');
    $form['#cache']['tags'] = $config->getCacheTags();
    $register = $account->isAnonymous();

    // Account information.
    $form['account'] = [
      '#type'   => 'container',
      '#weight' => -10,
    ];

    // Display password field only for existing users or when user is allowed to
    // assign a password during registration.
    if (!$register) {
      $form['account']['pass'] = [
        '#type' => 'password_confirm',
        '#size' => 25,
        '#description' => $this->t('To change the current user password, enter the new password in both fields.'),
        '#required' => TRUE,
      ];
      // The user must enter their current password to change to a new one.
      if ($user->id() == $account->id()) {
        $form['account']['current_pass'] = [
          '#type' => 'password',
          '#title' => $this->t('Old password'),
          '#size' => 25,
          '#access' => !$form_state->get('user_pass_reset'),
          '#weight' => -5,
          // Do not let web browsers remember this password, since we are
          // trying to confirm that the person submitting the form actually
          // knows the current one.
          '#attributes' => ['autocomplete' => 'off'],
          '#required' => TRUE,
        ];
        $form_state->set('user', $account);
      }
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $current_pass_input = trim($form_state->getValue('current_pass'));
    if ($current_pass_input) {
      $user = User::load(\Drupal::currentUser()->id());
      if (!$this->passwordHasher->check($current_pass_input, $user->getPassword())) {
        $form_state->setErrorByName('current_pass', $this->t('The current password you provided is incorrect.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = User::load($this->userProfile->id());
    $user->setPassword($form_state->getValue('pass'));
    $user->save();
    $this->messenger()->addStatus($this->t('Your password has been changed.'));
  }

}
