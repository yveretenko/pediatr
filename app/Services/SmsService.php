<?php

namespace App\Services;

class SmsService
{
    public $mode='HTTPS'; //HTTP or HTTPS
    protected $_server='://alphasms.com.ua/api/http.php';
    protected $_errors=[];
    protected $_last_response=[];
    private $_version='1.9';

    //IN: login and password or key on platform (AlphaSMS)
    public function __construct($login='', $password='', $key='')
    {
        $this->_login=$login;
        $this->_password=$password;
        $this->_key=$key;
    }

    //IN: 	sender name, phone of receiver, text message in UTF-8 - if long - will be auto split
    //		send_dt - date-time of sms sending, wap - url for Wap-Push link, flash - for Flash sms.
    //OUT: 	message_id to track delivery status, if empty message_id - check errors via $this->getErrors()
    public function sendSMS($from, $to, $message, $send_dt=0, $wap='', $flash=0)
    {
        if (!$send_dt)
            $send_dt=date('Y-m-d H:i:s');
        $d=is_numeric($send_dt) ? $send_dt : strtotime($send_dt);
        $data=['from'         =>$from,
               'to'           =>$to,
               'message'      =>$message,
               'ask_date'     =>date(DATE_ISO8601, $d),
               'wap'          =>$wap,
               'flash'        =>$flash,
               'class_version'=>$this->_version];
        $result=$this->execute('send', $data);

        if (isset($result['errors']) && is_countable($result['errors']) && count($result['errors']))
            $this->_errors=$result['errors'];

        return @$result['id'];
    }

    //IN: 	message_id to track delivery status
    //OUT: 	text name of status
    public function receiveSMS($sms_id)
    {
        $data=['id'=>$sms_id];
        $result=$this->execute('receive', $data);
        if (count(@$result['errors']))
            $this->_errors=$result['errors'];

        return @$result['status'];
    }

    //IN: 	message_id to delete
    //OUT: 	text name of status
    public function deleteSMS($sms_id)
    {
        $data=['id'=>$sms_id];
        $result=$this->execute('delete', $data);
        if (count(@$result['errors']))
            $this->_errors=$result['errors'];

        return @$result['status'];
    }

    //OUT:	amount in UAH, if no return - check errors
    public function getBalance()
    {
        $result=$this->execute('balance');
        if (count(@$result['errors']))
            $this->_errors=$result['errors'];

        return @$result['balance'];
    }

    //OUT:	returns number of errors
    public function hasErrors()
    {
        return count($this->_errors);
    }

    //OUT:	returns array of errors
    public function getErrors()
    {
        return $this->_errors;
    }

    public function getResponse()
    {
        return $this->_last_response;
    }

    public function translit($string)
    {
        $converter=[
            'а'=>'a', 'б'=>'b', 'в'=>'v',
            'г'=>'g', 'д'=>'d', 'е'=>'e', 'є'=>'ye',
            'ё'=>'yo', 'ж'=>'zh', 'з'=>'z', 'і'=>'i',
            'и'=>'i', 'й'=>'j', 'к'=>'k', 'ї'=>'yi',
            'л'=>'l', 'м'=>'m', 'н'=>'n',
            'о'=>'o', 'п'=>'p', 'р'=>'r',
            'с'=>'s', 'т'=>'t', 'у'=>'u',
            'ф'=>'f', 'х'=>'kh', 'ц'=>'ts',
            'ч'=>'ch', 'ш'=>'sh', 'щ'=>'shch',
            'ь'=>'\'', 'ы'=>'y', 'ъ'=>'"',
            'э'=>'e', 'ю'=>'yu', 'я'=>'ya',

            'А'=>'A', 'Б'=>'B', 'В'=>'V',
            'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Є'=>'Ye',
            'Ё'=>'Yo', 'Ж'=>'Zh', 'З'=>'Z', 'І'=>'I',
            'И'=>'I', 'Й'=>'J', 'К'=>'K', 'Ї'=>'Yi',
            'Л'=>'L', 'М'=>'M', 'Н'=>'N',
            'О'=>'O', 'П'=>'P', 'Р'=>'R',
            'С'=>'S', 'Т'=>'T', 'У'=>'U',
            'Ф'=>'F', 'Х'=>'Kh', 'Ц'=>'Ts',
            'Ч'=>'Ch', 'Ш'=>'Sh', 'Щ'=>'Shch',
            'Ь'=>'\'', 'Ы'=>'Y', 'Ъ'=>'"',
            'Э'=>'E', 'Ю'=>'Yu', 'Я'=>'Ya',
        ];
        $result=strtr($string, $converter);

        //upper case if needed
        if (mb_strtoupper($string)==$string)
            $result=mb_strtoupper($result);

        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $result);
    }

    protected function execute($command, $params=[])
    {
        $this->_errors=[];

        //HTTP GET
        if (strtolower($this->mode)=='http')
        {
            $response=@file_get_contents($this->generateUrl($command, $params));

            return @unserialize($this->base64_url_decode($response));
        }
        else
        {
            $params['login']=$this->_login;
            $params['password']=$this->_password;
            $params['key']=$this->_key;
            $params['command']=$command;
            $params_url='';
            foreach ($params as $key=>$value)
                $params_url.='&'.$key.'='.$this->base64_url_encode($value);

            //cURL HTTPS POST
            $ch=curl_init(strtolower($this->mode).$this->_server);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_POST, count($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response=@curl_exec($ch);
            curl_close($ch);

            $this->_last_response=@unserialize($this->base64_url_decode($response));

            return $this->_last_response;
        }
    }

    protected function generateUrl($command, $params=[])
    {
        $params_url='';
        if (count($params))
            foreach ($params as $key=>$value)
                $params_url.='&'.$key.'='.$this->base64_url_encode($value);
        if (!$this->_key)
        {
            $auth='?login='.$this->base64_url_encode($this->_login).'&password='.$this->base64_url_encode($this->_password);
        }
        else
        {
            $auth='?key='.$this->base64_url_encode($this->_key);
        }
        $command='&command='.$this->base64_url_encode($command);

        return strtolower($this->mode).$this->_server.$auth.$command.$params_url;
    }

    public function base64_url_encode($input)
    {
        return strtr(base64_encode($input), '+/=', '-_,');
    }

    public function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_,', '+/='));
    }
}
