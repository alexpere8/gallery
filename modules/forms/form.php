<?php

namespace Forms;

class Form
{
    protected const FIELDS = [];

    private static function getInitialValue($fldName, $fldParams, $initial = [])
    {
        $val = '';
        if (isset($initial[$fldName])) {
            $val = $initial[$fldName];
        } else if (isset($fldParams['initial'])) {
            $val = $fldParams['initial'];
        }

        if ($fldParams['type'] == 'timestamp') {
            if (gettype($val) == 'integer') {
                $val = date('Y-m-d H:M:S', $val);
            }
            $val = explode(' ', $val);
        }
        return $val;
    }

    protected static function afterInitializeData(&$data) {}

    public static function getInitialData($initial = [])
    {
        $data = [];
        foreach (static::FIELDS as $fldName => $fldParams) {
            $data[$fldName] = self::getInitialValue($fldName, $fldParams, $initial);
        }
        static::afterInitializeData($data);
        return $data;
    }

    protected static function afterNormalizeData(&$data, &$errors) {}

    public static function getNormalizedData($formData)
    {
        $data = [];
        $errors = [];
        foreach (static::FIELDS as $fldName => $fldParams) {
            $fldType = (isset($fldParams['type'])) ? $fldParams['type'] : 'string';

            if (empty($formData[$fldName]) && $fldType != 'boolean') {
                $data[$fldName] = self::getInitialValue($fldName, $fldParams);
                if (!isset($fldParams['optional'])) {
                    $errors[$fldName] = 'Это поле обязательно ' .
                    'к заполнению';
                }
            } else {
                $fldValue = $formData[$fldName];
                switch ($fldType) {
                    case 'boolean':
                        $data[$fldName] = !empty($formData[$fldName]);
                        break;
                    case 'integer':
                        $filtredValue = filter_var($fldValue, FILTER_SANITIZE_NUMBER_INT);
                        if ($filtredValue) {
                            $data[$fldName] = $filtredValue;
                        } else {
                            $errors[$fldName] = 'Введите ' . 'целое число';
                        }
                        break;
                    case 'float':
                        $filtredValue = filter_var($fldValue, FILTER_SANITIZE_NUMBER_FLOAT,
                        ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
                        if ($filtredValue) {
                            $data[$fldName] = $filtredValue;
                        } else { 
                            $errors[$fldName] = 'Введите ' . 'вещественное число';
                        }
                        break;
                    case 'timestamp':
                        $filtredValue = strtotime(implode(' ', $fldValue));
                        if ($filtredValue) {
                            $data[$fldName] = $fldValue;
                        } else {
                            $errors[$fldName] = 'Выберите ' . 'дату и время';
                        }
                        break;
                    case 'email':
                        $filtredValue = filter_var($fldValue, FILTER_SANITIZE_EMAIL);
                        if ($filtredValue) {
                            $data[$fldName] = $filtredValue;
                        } else {
                            $errors[$fldName] = 'Введите ' . 'адрес электронной почты';
                        }
                        break;
                    default:
                        $data[$fldName] = filter_var($fldValue, FILTER_SANITIZE_STRING);
                }
            }
        }

        static::afterNormalizeData($data, $errors);
        
        if ($errors) {
            $data['__errors'] = $errors;
        }
        return $data;
    }

    protected static function afterPrepareData(&$data, &$normData) {}

    public static function getPreparedData($normData)
    {
        $data = [];
        foreach (static::FIELDS as $fldName => $fldParams) {
            if (!isset($fldParams['nosave']) && isset($normData[$fldName])) {
                $val = $normData[$fldName];
                if ($fldParams['type'] == 'timestamp') {
                    $data[$fldName] = implode(' ', $val);
                } else {
                    $data[$fldName] = $val;
                }
            }
        }
        static::afterPrepareData($data, $normData);
        return $data;
    }
}