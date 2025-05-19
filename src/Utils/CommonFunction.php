<?php

use function Hyperf\Support\env;

/**
 * 格式化字符串
 * @param string  $str       文本内容.
 * @param integer $trimTag   是否移除标签.
 * @param integer $trimSpace 是否替换空格换行.
 * @param integer $cutNum    截取内容长度.
 * @return string
 */
if ( ! function_exists('trim_char')) {

	function trim_char(string $str, int $trimTag = 1, int $trimSpace = 0, int $cutNum = 0): string
	{
		if (empty($str)) {
			return '';
		}

		if ($trimTag == 1) {
			$str = strip_tags($str);
		}

		if ($trimSpace == 1) {
			$search = array(" ", "　", "\r\n", "\n", "\r");
			$replace = '';
			$str = str_replace($search, $replace, $str);
		}

		if ($cutNum > 0) {
			$str = mb_substr($str, 0, $cutNum);
		}

		$search = [
			'&amp;',
			'&nbsp;',
			'&quot;',
			'&#039;',
			'&ldquo;',
			'&rdquo;',
			'&mdash;',
			'&lt;',
			'&gt;',
			'&middot;',
			'&hellip;'
		];
		return str_replace($search, '', $str);
	}

}

/**
 * 产生随机字符串
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
if ( ! function_exists('random'))
{

	function random($length, $chars = '0123456789') {
		if($chars=='chars'){$chars='abcdefghigklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';}
		$hash = '';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
		return $hash;
	}

}

/**
 * 对数值型字符串进行编码,适用大于4位的且为数字的情况
 * @param    int   $number  数字
 * @return   string   字符串，此验证码仅支持数字
 */
if ( ! function_exists('encode_num')){
	function encode_num($number) {
		$char_length = strlen($number);
		if ($char_length < 2) {
			return $number;
		}

		//维持验证码长度为偶数位
		$pad_length = $char_length % 2 == 0 ? 2 : 1;

		//计算初始偏移量
		$mod_len = $number % ($char_length / 2) + 1;//截取长度
		$mod_start = $mod_len - 1;//开始字符

		$char_pad = '';
		for ($i = 0; $i < $pad_length; $i++) {
			$char_pad .= substr($number, $mod_start + $i, $mod_len) % $char_length - $i;
		}
		$char_pad = str_replace('-', '', $char_pad);
		return $number . $char_pad;
	}
}

/**
 * 判断验证码是否符合encode_num规则
 * @param  int $number 数字
 * @param  int $length 未编码字符长度
 * @return string 返回正常编码字符串
 */
if ( ! function_exists('decode_num')) {
	function decode_num($number, $length)
	{
		$char_pad = substr($number, 0, $length);
		return encode_num($char_pad);
	}
}

/**
 * 判断验证码是否为生产环境(通常用以返回相应环境的配置)
 * @param  string $env 当前环境标识
 * @return bool 返回正常编码字符串
 */
if ( ! function_exists('is_production')) {
	function is_production(): bool
	{
		$env = env('APP_ENV', 'dev');
		if($env == 'production'){
			return true;
		}
		return false;
	}
}

/**
 * 获取errno对应的msg信息.
 *
 * @param int $errno 错误编码.
 * @param string|array $params 占位符替换数组.
 * @param string $key 根据对应健返回响应结果(en、cn、key).
 * @return string
 * @description
 * en => 返回英文项(英文备注)
 * cn => 返回中文项(中文备注)
 * key => 国际化对应的语言包
 */
if ( ! function_exists('get_msg_by_code')) {

	function get_msg_by_code(int $errno, $params = [], $key = 'en'): string
	{

		// 初始化提示信息
		$msg = '';

		// 获取错误编码配置
		$msgCodeConfig = \Hyperf\Config\config('errorcode');

		// 判断编码是否有效
		if(!isset($msgCodeConfig[$errno])){
			throw new \Exception('This errno must be set in config files');
		}

		// 如果消息字段为字符串且不为空，表示自定义消息，直接返回(无需获取)
		if(is_string($params) && !empty($params)){
			return $params;
		}

		// 获取提示信息
		$msgItem = $msgCodeConfig[$errno];
		if(in_array($key, ['en', 'cn'])){
			$msg = $msgItem[$key];
		}else if($key === 'key'){
			$translationConfig = \Hyperf\Config\config('translation');
			$translationErrorConfig = [];
			if(file_exists($translationConfig['path'].'/'.$translationConfig['locale'])){
				$translationErrorConfig = require(
					$translationConfig['path'] . '/' . $translationConfig['locale'] . '/errorcode.php'
				);
			}elseif(file_exists($translationConfig['path'].'/'.$translationConfig['fallback_locale'])){
				$translationErrorConfig = require(
					$translationConfig['path'] . '/' . $translationConfig['fallback_locale'] . '/errorcode.php'
				);
			}
			if(!empty($translationErrorConfig[$msgItem['key']])){
				$msg = $translationErrorConfig[$msgItem['key']];
			}
		}

		// 参数替换
		if(!empty($msg) && !empty($params) && is_array($params)){
			$msg = vsprintf($msg, $params);
		}

		return $msg;
	}
}

/**
 * 获取用户IP地址.
 */
if ( ! function_exists('ip')) {
	function ip() {
		$ip = '';
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$ip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '0.0.0.0';
	}
}

/**
 * 将对象转换为数组(该方法暂不确保稳定)
 * 特别简单的对象可用,仅一层属性
 * @param object $obj 对象.
 */
if ( ! function_exists('obj2array')) {
    function obj2array(object $obj) {
        $objArray = (array) $obj;
        $rtn = [];
        foreach ($objArray as $key => $value){
            $key = ltrim(str_replace('*', '', $key));
            $rtn[$key] = $value;
        }
        return $rtn;
    }
}

/**
 * 将时间戳转换为10分钟的倍数
 * @param int $time 时间戳.
 */
if (!function_exists('time2minute')) {
    function time2minute(int $time)
    {
        $prefix = date('YmdH', $time);
        $minute = date('i', $time);
        return (int)($prefix . substr($minute, 0,1) . '0');
    }
}

/**
 * 将时间戳转换为10分钟的倍数
 * @param int $time 时间戳.
 */
if (!function_exists('genUniqueId')) {
    function genUniqueId($prefix = '')
    {
        return uniqid($prefix.date('YmdHis').'.', true);
    }
}