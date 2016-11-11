<?php
namespace Tayron;

use \Exception;
use \InvalidArgumentException;

/**
 * Classe que gerencia os dados na sessão.
 *
 * @author Tayron Miranda <dev@tayron.com.br>
 */
class Session
{
    /**
     * Armazena a instancia de Sessao
     *
     * @var Session
     */
    private static $instance;

    /**
	 * Session::__construct
	 *
     * Impede com que o objeto seja instanciado
     */
    final private function __construct()
    {
    }

    /**
	 * Session::__clone
	 *
     * Impede que a classe Requisição seja clonada
     *
     * @throws Exception Lança execção caso o usuário tente clonar este classe
     *
     * @return void
     */
    final public function __clone()
    {
        throw new Exception('A classe Requisicao não pode ser clonada.');
    }

    /**
	 * Session::__wakeup
	 *
     * Impede que a classe Requisição execute __wakeup
     *
     * @throws Exception Lança execção caso o usuário tente executar este método
     *
     * @return void
     */
    final public function __wakeup()
    {
        throw new Exception('A classe Requisicao não pode executar __wakeup.');
    }

	/**
	 * Session::getInstancia
	 *
	 * Retorna uma instância única de uma classe.
	 *
	 * @staticvar Singleton $instancia A instância única dessa classe.
	 *
	 * @return Singleton A Instância única.
	 */
	public static function getInstance($minute = 1)
	{
		if (!static::$instance) {
			static::$instance = new static();
		}

        session_start();

        if(!static::$instance->debug()){
            static::$instance->set('session_id', session_id());
            static::$instance->set('session_time', intval($minute));
            static::$instance->set('session_start', new \DateTime());
        }

        self::$instance->verifyTimeout();

		return self::$instance;
	}

    /**
     * Session::verifyTimeout
     *
     * Método que verifica o tempo limite para expiração da sessão e o atualiza,
     * caso o tempo já tenha passado a sessão é destruida
     *
     * @return void
     */
    private function verifyTimeout()
    {
        $sessionStart = $this->get('session_start');
        $dateTimeNow = new \DateTime();
        $time = $dateTimeNow->diff($sessionStart);

        ($time->i >= $this->get('session_time'))
            ? $this->destroy() : $this->renew();
    }

    /**
     * Session::set
     *
     * Método que seta os dados na sessão
     *
     * @param string $key Indenfificador da sessão
     * @param mixed $value Valor a ser armazenado na sessão
     *
     * @return void
     */
    public function set($key, $value)
    {
        if (empty($key)) {
            throw new InvalidArgumentException('Deve-se informar um indentificador para o valor a ser armazenado');
        }

        if (empty($value)) {
            throw new InvalidArgumentException('Deve-se informar um valor para o indentificador informado');
        }

        $_SESSION[$key] = $value;
    }

    /**
     * Session::set
     *
     * Método que retorna um dado na sessão
     *
     * @param string $key Indenfificador da sessão
     * @return mixed Retorna o conteúdo de um indenfificador na sessão
     */
    public function get($key)
    {
        return (isset($_SESSION[$key]))
            ? $_SESSION[$key] : null;
    }

    /**
     * Session::remove
     *
     * Método que remove um item da sessão
     *
     * @param string $key - Indenfificador da sessão
     * @return void
     */
    public function remove($key)
    {
        if (empty($key)) {
            throw new InvalidArgumentException('Deve-se informar um indentificador para remover o valor armazenado');
        }

        unset($_SESSION[$key]);
    }

    /**
     * Session::clear
     *
     * Método que limpa todos os registros da sessao
     *
     * @return void
     */
    public function clear()
    {
        $_SESSION = array();
    }

    /**
     * Session::destroy
     *
     * Método que destroy uma sessão
     *
     * @return void
     */
    public function destroy()
    {
        session_destroy();
        $_SESSION = array();
    }

    /**
     * Session::isRegistered
     *
     * Métpdp que verifica se a sessão está registrada
     *
     * @return boolean
     */
    public function isRegistered()
    {
        return $this->get('session_id') ? true : false;
    }

    /**
     * Session::getSessionId
     *
     * Método que retorna o Id da sessão
     *
     * @return integer Id da sessão
     */
    public function getSessionId()
    {
        return $this->get('session_id');
    }

    /**
     * Session::renew
     *
     * Método que atualiza o tempo de inicio da sessão
     *
     * @return boolean
     */
    private function renew()
    {
        $this->set('session_start', new \DateTime());
    }

    /**
     * Session::debug
     *
     * Método que retorna todos os dados do sessão
     *
     * @return void
     */
    public function debug()
    {
        return $_SESSION;
    }
}
