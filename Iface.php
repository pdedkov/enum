<?php
namespace Enum;

/**
 * Интерфейс для создания перечислений
 */
interface Iface {

	/**
	 * Получение массива всех значений как ключ массива, название как значение
	 *
	 * @return array
	 */
	public static function items();

	/**
	 * Проверка корректности значения
	 *
	 * @abstract
	 * @param $value
	 * @return bool
	 */
	public static function isValid($value);
}