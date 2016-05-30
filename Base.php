<?php
namespace Enum;

/**
 * Базовый класс enum значений
 */
class Base implements Iface {
	protected $_elementEnumName;

	/**
	 * Для поддержки передачи кастомных наименований элементов enum. Пример:
	 *    static protected $_itemsToString = array(
	 *        self::TO_APROVE => 'в апрув',
	 *        self::TO_GROUND => 'на площадку'
	 *    );
	 *
	 * @var array
	 */
	protected static $_itemsToString = [];

	/**
	 * Для поддержки передачи данных элементов enum. Пример:
	 *    static protected $_itemsToString = array(
	 *        self::TO_APROVE => array(
	 *           'data1' => 1,
	 *           'data2' => 2
	 *        ),
	 *        self::TO_GROUND => array(
	 *           'data1' => 11,
	 *           'data1' => 22
	 *        )
	 *    );
	 *
	 * @var array
	 */
	protected static $_itemsToData = array();

	/**
	 * @param null|mixed $elementEnum
	 */
	public function __construct($elementEnum = null) {
		if (self::isValid($elementEnum)) {
			$this->_elementEnumName = $elementEnum;
		} else {
			throw new Exception('неопределенное значение ' . $elementEnum);
		}
	}

	/**
	 * Предотвращаем попытки что-либо засетить дополнительное
	 * @todo_puzo (необходимость сомнительна)
	 *
	 * @param $name
	 * @param $value
	 *
	 * @throws Exception
	 */
	public function __set($name, $value) {
		throw new Exception(get_called_class() . ' нельзя изменить');
	}

	/**
	 * Использование в строковом контексте
	 *
	 * @return string
	 */
	public function __toString() {
		return (string)$this->_elementEnumName;
	}

	/**
	 * Получение массива всех значений как ключ массива, название как значение
	 *
	 * @param $raw
	 * @return array
	 */
	public static function items($raw = false) {
		/** @var $_itemsToString array */
		if (!empty(static::$_itemsToString) && !$raw) {
			return static::$_itemsToString;
		}

		return @array_flip(self::getAllConstants());
	}

	/**
	 * Получение массива всех значений как ключ массива, массив данных как значение
	 *        self::TO_APROVE => array(
	 *           'title' => 'в апрув' // <-- 'title' - зарезервированное поле (данные для него из $_itemsToString)
	 *           'data1' => 1,
	 *           'data2' => 2
	 *        ),
	 *        self::TO_GROUND => array(
	 *           'title' => 'на площадку'
	 *           'data1' => 11,
	 *           'data1' => 22
	 *        )
	 * Если не задан массив $_itemsToData результат == вызову items()
	 *
	 * @return array
	 */
	public static function itemsWithData() {
		if (empty(static::$_itemsToData)) {
			return self::items();
		}
		$result = [];
		foreach (static::items() as $key => $value) {
			$v = static::$_itemsToData[$key];
			$result[$key] = array_merge((is_array($v) ? $v : []), ['title' => $value]);
		}

		return $result;
	}

	/**
	 * Перезапись значения дополнительных данных
	 *
	 * @param string $key
	 * @param array $new_data
	 *
	 * @return bool
	 */
	public static function overrideItemData($key, $newData = []) {
		/** @var $_itemsToData array */
		if (empty(static::$_itemsToData) || !isset(static::$_itemsToData[$key])) {
			// нет дополнительных данных
			return false;
		}

		static::$_itemsToData[$key] = array_merge(static::$_itemsToData[$key], $newData);

		return true;
	}

	/**
	 * Получение всех констант
	 *
	 * @return array
	 */
	public static function getAllConstants() {
		$refl = new \ReflectionClass(get_called_class());
		return $refl->getConstants();
	}

	/**
	 * Проверка корректности значения
	 *
	 * @abstract
	 *
	 * @param {string} $value
	 *
	 * @return bool
	 */
	public static function isValid($value) {
		return in_array($value, array_values(self::getAllConstants()));
	}

	/**
	 * Получение кастомных наименований элементов enum
	 *
	 * @return array
	 */
	public static function getItemsToString() {
		return static::$_itemsToString;
	}

	/**
	 * Получение данных элементов enum
	 *
	 * @return array
	 */
	public static function getItemsToData() {
		return static::$_itemsToData;
	}
}