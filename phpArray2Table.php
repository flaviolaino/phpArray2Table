<?php

/**
 * phpArray2Table
 *
 * based on mathieuviossat/arraytotexttable
 *
 * @author      Flavio Laino <info@flaviolaino.it>
 * @license     http://opensource.org/licenses/MIT
 * @link        TODO
 */

class phpArray2Table
{

	const CELL_ALIGN_TO_LEFT   = STR_PAD_RIGHT;
	const CELL_ALIGN_TO_CENTER = STR_PAD_BOTH;
	const CELL_ALIGN_TO_RIGHT  = STR_PAD_LEFT;

	protected $data;
	protected $keys;
	protected $widths;
	protected $decorator;
	protected $indentation;
	protected $displayKeys;
	protected $upperKeys;
	protected $keysAlignment;
	protected $valuesAlignment;
	protected $formatter;
	protected $valueMaxLength;

	public function __construct($data = []) {
		$this->setData($data)
			->setDecorator(new decorator())
			->setIndentation('')
			->setDisplayKeys('auto')
			->setUpperKeys(true)
			->setKeysAlignment(self::CELL_ALIGN_TO_CENTER)
			->setValuesAlignment(self::CELL_ALIGN_TO_LEFT)
			->setFormatter(function(){})
			->setValueMaxLength(null);
	}

	public function __toString() {
		return $this->getTable();
	}

	public function getTable($data = null) {
		if (!is_null($data))
			$this->setData($data);

		$data = $this->prepare();
		$i = $this->indentation;
		$d = $this->decorator;

		$displayKeys = $this->displayKeys;
		if ($displayKeys === 'auto') {
			$displayKeys = false;
			foreach ($this->keys as $key)
				if (!is_int($key)) {
					$displayKeys = true;
					break;
				}
		}

		$table = $i . $this->line($d->getTopLeft(), $d->getHorizontal(), $d->getHorizontalDown(), $d->getTopRight()) . PHP_EOL;

		if ($displayKeys) {
			$keysRow = array_combine($this->keys, $this->keys);
			if ($this->upperKeys)
				$keysRow = array_map('mb_strtoupper', $keysRow);
			$table .= $i . $this->row($keysRow, $this->keysAlignment) . PHP_EOL;

			$table .= $i . $this->line($d->getVerticalRight(), $d->getHorizontal(), $d->getCross(), $d->getVerticalLeft()) . PHP_EOL;
		}

		foreach ($data as $row){
			$table .= $i . $this->row($row, $this->valuesAlignment) . PHP_EOL;
		}

		$table .= $i . $this->line($d->getBottomLeft(), $d->getHorizontal(), $d->getHorizontalUp(), $d->getBottomRight()) . PHP_EOL;

		return $table;
	}

	public function getData() {
		return $this->data;
	}

	public function getDecorator() {
		return $this->decorator;
	}

	public function getIndentation() {
		return $this->indentation;
	}

	public function getDisplayKeys() {
		return $this->displayKeys;
	}

	public function getUpperKeys() {
		return $this->upperKeys;
	}

	public function getKeysAlignment() {
		return $this->keysAlignment;
	}

	public function getValuesAlignment() {
		return $this->valuesAlignment;
	}

	public function getFormatter() {
		return $this->formatter;
	}

	public function getValueMaxLength() {
		return $this->valueMaxLength;
	}

	public function setData($data) {
		if (!is_array($data))
			$data = [];

		$arrayData = [];
		foreach ($data as $row) {
			if (is_array($row))
				$arrayData[] = $row;
			else if (is_object($row))
				$arrayData[] = get_object_vars($row);
		}

		$this->data = $arrayData;
		return $this;
	}

	public function setDecorator($decorator) {
		$this->decorator = $decorator;
		return $this;
	}

	public function setIndentation($indentation) {
		$this->indentation = $indentation;
		return $this;
	}

	public function setDisplayKeys($displayKeys) {
		$this->displayKeys = $displayKeys;
		return $this;
	}

	public function setUpperKeys($upperKeys) {
		$this->upperKeys = $upperKeys;
		return $this;
	}

	public function setKeysAlignment($keysAlignment) {
		$this->keysAlignment = $keysAlignment;
		return $this;
	}

	public function setValuesAlignment($valuesAlignment) {
		$this->valuesAlignment = $valuesAlignment;
		return $this;
	}

	public function setFormatter($formatter) {
		$this->formatter = $formatter;
		return $this;
	}

	public function setValueMaxLength($valueMaxLength) {
		$this->valueMaxLength = (int)$valueMaxLength;
		return $this;
	}

	protected function line($left, $horizontal, $link, $right) {
		$line = $left;
		foreach ($this->keys as $key)
			$line .= str_repeat($horizontal, $this->widths[$key]+2) . $link;

		if (mb_strlen($line) > mb_strlen($left))
			$line = mb_substr($line, 0, -mb_strlen($horizontal));

		return $line . $right;
	}

	protected function row($row, $alignment) {
		$line = $this->decorator->getVertical();
		foreach ($this->keys as $key) {
			$value = isset($row[$key]) ? $row[$key] : '';
			$value_type = gettype($value);
			$value_alignment = ($value_type == 'double' || $value_type == 'integer') ? self::CELL_ALIGN_TO_RIGHT : $alignment;
			if ($value_type == 'string' && strlen($value) > $this->valueMaxLength && !empty($this->valueMaxLength)) {
				$value = mb_substr($value, 0, $this->valueMaxLength) . '…';
			}
			$line .= ' ' . static::mb_str_pad($value, $this->widths[$key], ' ', $value_alignment) . ' ' . $this->decorator->getVertical();
		}

		if (empty($row))
			$line .= $this->decorator->getVertical();

		return $line;
	}

	protected function prepare() {
		$this->keys = [];
		$this->widths = [];

		$data = $this->data;

		foreach ($data as &$row)
			array_walk($row, $this->formatter, $this);
		unset($row);

		foreach ($data as $row)
			$this->keys = array_merge($this->keys, array_keys($row));
		$this->keys = array_unique($this->keys);

		foreach ($this->keys as $key)
			$this->setWidth($key, $key);

		foreach ($data as $row)
			foreach ($row as $columnKey => $columnValue)
				$this->setWidth($columnKey, $columnValue);

		return $data;
	}

	protected function setWidth($key, $value) {
		if (!isset($this->widths[$key]))
			$this->widths[$key] = 0;

		$width = mb_strlen($value);
		if ($width > $this->widths[$key])
			$this->widths[$key] = $width;
	}

	protected static function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT, $encoding = null) {
		if ($encoding === null)
			$encoding = mb_internal_encoding();

		$diff = strlen($input) - mb_strlen($input, $encoding);
		return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
	}

}


class decorator
{
	public function getBottomLeft()
	{
		return '└';
	}

	public function getBottomRight()
	{
		return '┘';
	}

	public function getCross()
	{
		return '┼';
	}

	public function getHorizontal()
	{
		return '─';
	}

	public function getHorizontalDown()
	{
		return '┬';
	}

	public function getHorizontalUp()
	{
		return '┴';
	}

	public function getTopLeft()
	{
		return '┌';
	}

	public function getTopRight()
	{
		return '┐';
	}

	public function getVertical()
	{
		return '│';
	}

	public function getVerticalLeft()
	{
		return '┤';
	}

	public function getVerticalRight()
	{
		return '├';
	}
}