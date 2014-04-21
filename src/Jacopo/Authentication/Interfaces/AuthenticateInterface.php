<?php namespace Jacopo\Authentication\Interfaces;

interface AuthenticateInterface
{
  /**
   * Force authentication on a user
   *
   * @param $credentials
   * @param $remember
   * @return mixed
   */
  public function authenticate($credentials, $remember);

  /**
   * @param $user
   * @param $remember
   * @return mixed
   */
  public function loginById($id, $remember);

  /**
   * Logout
   *
   * @return mixed
   */
  public function logout();

  /**
   * @return mixed
   */
  public function getErrors();

  /**
   * Obtain the user with his email
   *
   * @param $email
   * @return mixed
   * @throws \Jacopo\Authentication\Exceptions\UserNotFoundException
   * @return mixed
   */
  public function getUser($email);

  /**
   * Gets the user activaction token
   *
   * @param $email
   * @return String
   */
  public function getActivationToken($email);

  /**
   * Obtains a user given his user id
   *
   * @param $id
   * @return mixed
   */
  public function getUserById($id);

  /**
   * Obtain the current logged user
   *
   * @return mixed
   */
  public function getLoggedUser();

}