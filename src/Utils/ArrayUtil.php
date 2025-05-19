<?php

declare(strict_types=1);

namespace Ideepler\HyperfCore\Utils;

/**
 * 数组相关的常用处理函数.
 */
class ArrayUtil
{
	/**
	 * 获取数组中指定keys.
	 * @param array $data 数据.
	 * @param array $fields 字段.
	 * @param boolean $filterEmpty 是否排除空字段，由于在参数校验时可以要求有值时校验且存在置空需求，故默认false.
	 * @param boolean $filterNull 是否排除NULL字段.
	 * @return array
	 */
	public static function arrayOnly(array $data, array $fields = [], bool $filterEmpty = false, bool $filterNull = true): array
	{
		$res = [];
		foreach ($fields as $field) {
			if($filterEmpty){
				!empty($data[$field]) && ($res[$field] = $data[$field]);
			}elseif($filterNull) {
				isset($data[$field]) && ($res[$field] = $data[$field]);
			} else {
				key_exists($field, $data) && ($res[$field] = $data[$field]);
			}
		}
		return $res;
	}

	/**
	 * 将数组中指定key进行json解码.
	 * @param array $data 数据.
	 * @param array $fields 字段.
	 * @param boolean $isForce 是否强转换，为1时，转为失败则返回空数组，0失败返回原数据
	 * @return array
	 */
	public static function arrayKeyJsonDecode(array $data, array $fields = [], bool $isForce = false): array
	{
		foreach ($fields as $field) {
			// 转换前置条件
			if(!key_exists($field, $data)){
				continue;
			}
			if(!is_string($data[$field])){
				continue;
			}

			// 对字段进行转化
			try {
				$data[$field] = json_decode($data[$field], true);
			}catch (\Throwable $ex){
				if($isForce){
					$data[$field] = [];
				}
			}
		}
		return $data;
	}

	/**
	 * 将数组中指定key进行json编码.
	 * @param array $data 数据.
	 * @param array $fields 字段.
	 * @param boolean $isForce 是否强转换，为1时，转为失败则返回空{}，0失败返回原数据
	 * @return array
	 */
	public static function arrayKeyJsonEncode(array $data, array $fields = [], bool $isForce = false): array
	{

		foreach ($fields as $field) {
			// 转换前置条件
			if(!key_exists($field, $data)){
				continue;
			}
			if(!is_array($data[$field])){
				continue;
			}

			// 对字段进行转化
			try {
				$data[$field] = json_encode($data[$field]);
			}catch (\Throwable $ex){
				if($isForce){
					$data[$field] = '{}';
				}
			}
		}
		return $data;
	}

	/**
	 * 将数组中指定key进行脱敏
	 * @param array $data 数据.
	 * @param array $fields 字段.
	 * @return array
	 */
	public static function arrayKeyMasking(array $data, array $fields = []): array
	{
		// 默认脱敏关键字
		if(empty($fields)){
			$fields = ['password', 'password_confirm', 'password_old', 'password_approval'];
		}
		foreach ($fields as $field) {
			// 转换前置条件
			if(!key_exists($field, $data)){
				continue;
			}
			// 对字段进行转化
			$data[$field] = '******';
		}
		return $data;
	}

    /**
     * 对数组中指定keys的值进行trim.
     * @param array   $data           数据.
     * @param array   $fields         字段.
     * @param string  $characters     额外需要trim的元素，支持多个字符，与trim存在同样的缺陷.
     * @param boolean $trimBlankSpace 默认去除换行等空白.
     * @return array.
     */
    public static function arrayTrim(
        array $data,
        array $fields = [],
        string $characters = '',
        bool $trimBlankSpace = true
    ): array {

        if (empty($fields)) {
            return [];
        }
        foreach ($fields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                if ($trimBlankSpace) {
                    $data[$field] = trim($data[$field]);
                }
                if ($characters != '') {
                    $data[$field] = trim($data[$field], $characters);
                }
            }
        }
        return $data;
    }

    /**
     * 从数组中取一个字段的值出来.
     * @param array          $data  数据.
     * @param integer|string $field 字段.
     * @param mixed|null     $def   默认.
     * @return mixed|null
     */
    public static function get(array $data, int|string $field, mixed $def = null)
    {
        return key_exists($field, $data) ? $data[$field] : $def;
    }

    /**
     * 将数据以指定字段为健重新赋值
     * @param array  $data  数据.
     * @param string $field 字段.
     * @return array
     */
    public static function groupChunk(array $data, string $field): array
    {
        $res = [];
        foreach ($data as $v) {
            $res[$v[$field]][] = $v;
        }
        return $res;
    }

	/**
	 * 将平铺数据遍历生成树结构.
	 * @param array $dataList 数据列表.
	 * @param string $parentNode 父节点名称.
	 * @param string $childrenNode 生成树的子节点名称.
	 * @param string $idNode 唯一id节点.
	 * @return array
	 */
	public static function arrayToTree(array $dataList, string $parentNode = 'pid', string $childrenNode = 'children', string $idNode = 'id'): array
	{
		// 为空直接返回
		if(empty($dataList)){
			return [];
		}

		// 必须存在父节点字段
		if(!isset($dataList[0][$parentNode])){
			throw new \Exception($parentNode . ' not fount');
		}

		// 子节点名称已被占用
		if(isset($dataList[0][$childrenNode])){
			throw new \Exception($childrenNode . ' already exists');
		}

		// 对数据按pid分组
		$dataListGroup = [];
		$minParentNode = PHP_INT_MAX;
		foreach ($dataList as $item){
			if(isset($item[$parentNode])){
				$dataListGroup[$item[$parentNode]][] = $item;
				if($item[$parentNode] < $minParentNode){
					$minParentNode = $item[$parentNode];
				}
			}
		}

		return self::recursiveChildren($dataListGroup[$minParentNode], $dataListGroup, $idNode, $childrenNode);

	}

	/**
	 * 将平铺数据遍历生成树结构.
	 * @param array $listCurrent 当前层数据.
	 * @param array $listGroup 数据列表.
	 * @param string $idNode 唯一id节点.
	 * @param string $childrenNode 生成树的子节点名称.
	 * @return array
	 */
	public static function recursiveChildren(array $listCurrent, array $listGroup,
		string $idNode, string $childrenNode): array
	{
		$dataListTree = [];
		foreach ($listCurrent as $item){
			if(!empty($listGroup[$item[$idNode]])){
				$item[$childrenNode] = self::recursiveChildren(
					$listGroup[$item[$idNode]], $listGroup, $idNode, $childrenNode
				);
			}
			$dataListTree[] = $item;
		}

		return $dataListTree;

	}

    /**
     * 将数组字段转换成int类型
     * @param array $data 数组参数
     * @return array
     */
    public static function arrayFieldToInt(array $data) : array
    {
        foreach ($data as $key => $value) {
            $data[$key] = (int)$value;
        }
        return $data;
    }
}
