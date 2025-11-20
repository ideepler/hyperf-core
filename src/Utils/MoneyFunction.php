<?php

declare(strict_types=1);

namespace Ideepler\HyperfCore\Utils;

/**
 * 货币操作相关函数.
 * 获取单位精度(元)(角)(分)(厘)(毫)(丝)
 */
class MoneyFunction
{
    // 精度设置为分
	private static int $minPrecision = 100;

	/**
	 * 将金额转换为固定小数位的字符串
	 * @param float $amount 金额.
	 * @return string
	 */
	public static function showMoney(float $amount): string
	{
		return sprintf("%.2f", $amount);
	}

	/**
	 * 将货币单位元按精度转换为整型
	 * @param float $amount 金额.
	 * @param int $isFloor 是否为舍弃取整.
	 * @return int
	 */
	public static function amountDecimalToInteger(float $amount, int $isFloor = 1): int
	{
		if($isFloor){
			return (int)ceil($amount * self::$minPrecision);
		}else{
			return (int)floor($amount * self::$minPrecision);
		}
	}

	/**
	 * 将指定精度的货币转换为单位元
	 * @param int $amount 金额.
	 * @return float
	 */
	public static function amountIntegerToDecimal(int $amount): float
	{
		return floor($amount / (self::$minPrecision / 100)) / 100;
	}

	/**
	 * 将数组中指定金额字段转换为整型
	 * @param array $data 数据.
	 * @param array $fields 字段.
	 * @return array
	 */
	public static function arrayAmountDecimalToInteger(array $data, array $fields): array
	{
		foreach ($fields as $field) {
			// 转换前置条件
			if(!key_exists($field, $data)){
				continue;
			}
			if(!is_numeric($data[$field])){
				continue;
			}
			$data[$field] = self::amountDecimalToInteger((float)$data[$field]);
		}
		return $data;
	}

	/**
	 * 将数组中指定金额字段转换为元
	 * @param array $data 数据.
	 * @param array $fields 字段.
	 * @return array
	 */
	public static function arrayAmountIntegerToDecimal(array $data, array $fields): array
	{
		foreach ($fields as $field) {
			// 转换前置条件
			if(!key_exists($field, $data)){
				continue;
			}
			if(!is_numeric($data[$field])){
				continue;
			}
			$data[$field] = self::amountIntegerToDecimal((int)$data[$field]);
		}
		return $data;
	}

	/**
	 * 金额格式化为指定小数长度
	 * @param float $amount 金额.
	 * @param int $scale 小数位数.
	 * @return float
	 * @description
	 * 用户展示为2为小数，故默认为2；当第三方返回的值超过我们长度(厘)时，可以转换至长度3
	 */
	public static function amountDecimalNum(float $amount, int $scale = 2): float
	{
		$moneyPrecision = pow(10, $scale);
		return floor($amount * $moneyPrecision) / $moneyPrecision;
	}

    /**
     * 将指定精度的货币转换为单位分
     * @param int $amount 金额.
     * @return float
     */
    public static function amountIntegerToDivide(int $amount): float
    {
        return floor($amount / (self::$minPrecision / 100));
    }

    /**
     * 将分转换为指定精度的货币单位
     * @param int $amount 金额.
     * @return float
     */
    public static function amountDivideToInteger(int $amount): float
    {
        return floor($amount * (self::$minPrecision / 100));
    }

}
