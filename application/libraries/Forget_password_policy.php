<?php
defined('BASEPATH') OR define('BASEPATH', dirname(__FILE__));

class Forget_password_policy
{
    private $valid_roles = array('student', 'parent', 'teacher', 'staff', 'leader');

    public function is_valid_role($role)
    {
        return in_array($role, $this->valid_roles, true);
    }

    public function uses_npp($role)
    {
        return $this->identity_field($role) === 'npp';
    }

    public function identity_field($role)
    {
        if ($role === 'student' || $role === 'parent') {
            return 'nis';
        }

        if ($role === 'teacher' || $role === 'staff') {
            return 'email';
        }

        if ($role === 'leader') {
            return 'npp';
        }

        return false;
    }

    public function validation_field($role)
    {
        if ($role === 'student' || $role === 'parent') {
            return 'tgl_lahir';
        }

        if ($role === 'teacher' || $role === 'staff') {
            return 'npp';
        }

        return false;
    }

    public function normalize_birth_date($value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        $value = trim((string) $value);
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})(?:[ T]\d{2}:\d{2}:\d{2})?$/', $value, $matches) !== 1) {
            return false;
        }

        $year = (int) $matches[1];
        $month = (int) $matches[2];
        $day = (int) $matches[3];
        if ($year < 1 || !checkdate($month, $day, $year)) {
            return false;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    public function birth_dates_match($input, $stored)
    {
        $input_date = $this->normalize_birth_date($input);
        $stored_date = $this->normalize_birth_date($stored);

        return $input_date !== false && $input_date === $stored_date;
    }
}
