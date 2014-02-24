<?php namespace Jacopo\Authentication\Interfaces;

interface AuthenticateInterface
{
    /**
     * Effettua l'autenticazione di un utente
     * @param $credentials
     * @param $remember
     * @return mixed
     */
    public function authenticate($credentials, $remember);

    /**
     * Logga un'utente prestabilito
     *
     * @param $user
     * @param $remember
     * @return mixed
     */
    public function loginById($id, $remember);

    /**
     * Logout
     * @return mixed
     */
    public function logout();

    /**
     * Ritorna errori di autenticazione o altro
     * @return mixed
     */
    public function getErrors();

    /**
     * Ritorna l'utente data la sua mail
     *
     * @param $email
     * @return mixed
     * @throws \Jacopo\Authentication\Exceptions\UserNotFoundException
     * @return mixed
     */
    public function getUser($email);

    /**
     * Ritorna il token associato alla mail
     * @param $email
     * @return String
     */
    public function getToken($email);
}