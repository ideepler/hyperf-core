<?php

namespace Ideepler\HyperfCore\Utils;

/**
 * 接口签名相关方法
 */
class SignUtil
{
	/**
	 * 校验签名信息
	 * @param array $arrInput 输入参数.
	 * @return bool
	 */
	public static function checkSign(array $arrInput): bool
	{
		if(empty($arrInput['sign'])){
			return false;
		}
		$sign = $arrInput['sign'];
		unset($arrInput['sign']);
		return $sign == self::getSign($arrInput);
	}

	/**
	 * 返回签名信息
	 * @param array $arrInput 输入参数.
	 * @return string
	 */
	public static function getSign(array $arrInput): string
	{
		$str = self::getQueryString($arrInput);
		return md5(sha1($str));
	}

	/**
	 * 根据参数拼装签名字符串
	 * @param array $arrInput 输入参数.
	 * @param string $superKey 不为空表示该健为数组.
	 * @return string
	 */
	private static function getQueryString(array $arrInput, string $superKey = ''): string
	{
		$str = '';
		ksort($arrInput);
		foreach ($arrInput as $key => $value) {
			if ($superKey != '') {
				$key = $superKey.'['.$key.']';
			}
			if (is_array($value)) {
				$str .= self::getQueryString($value, $key) . '&';
			} else {
				$str .= $key . '=' . $value . '&';
			}
		}
		return trim($str, '&');
	}
}