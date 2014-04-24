<?php
/**
 * Плагин для отправки сообщений на телефон через сервис SMS Traffic.
 *
 * @package    wa-plugins
 * @category   sms
 * @author     Anton F <antonfedonjuk@gmail.com>
 * @copyright  2014 Anton F
 * @link       http://smstraffic.ru
 */
class smstrafficSMS extends waSMSAdapter
{
    const URL = 'https://www.smstraffic.ru/multi.php';

    /**
     * Возвращает массив настроек плагина.
     *
     * @return array
     */
    public function getControls()
    {
        return array(
            'login' => array(
                'title'       => 'Логин',
                'description' => 'Введите логин в сервисе SMS Traffic.'
            ),
            'password' => array(
                'title'       => 'Пароль',
                'description' => 'Введите пароль в сервисе SMS Traffic.'
            ),
        );
    }

    /**
     * Отправка сообщения.
     *
     * @param  string  $to    Номер тeлeфона получателя
     * @param  string  $text  Сообщение (70 символов)
     * @param  string  $from  Имя отправителя
     * @return string
     */
    public function send($to, $text, $from = '999')
    {
        if (!function_exists('curl_init')) {
           // Записываем ошибку в лог
            $this->log($to, $text, 'PHP extension cURL not required');

           return false;
        }

        // Формируем массив параметров
        $fields = array(
            'originator' => $from,
            'phones'     => $to,
            'message'    => $text,
            'login'      => $this->getOption('login'),
            'password'   => $this->getOption('password'),
            'rus'        => '0' // Транслит текста сообщений
        );

        // Выполняем удаленный запрос
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);

        // Записываем результат в лог
        $this->log($to, $text, $result);

        return $result;
    }
}