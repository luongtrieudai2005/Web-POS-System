<?php
/**
 * Validator Class
 * Validation du lieu dau vao
 */

class Validator {
    
    private $data = [];
    private $errors = [];
    private $rules = [];
    
    /**
     * Constructor
     */
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * Validate du lieu theo rules
     * 
     * @param array $rules
     * @return bool
     */
    public function validate($rules) {
        $this->rules = $rules;
        $this->errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $this->validateField($field, $ruleString);
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate mot field
     */
    private function validateField($field, $ruleString) {
        // Tach cac rule
        $rules = explode('|', $ruleString);
        
        // Lay gia tri field
        $value = isset($this->data[$field]) ? $this->data[$field] : null;
        
        foreach ($rules as $rule) {
            // Tach rule va parameter (neu co)
            // Vi du: min:6 -> rule = min, param = 6
            if (strpos($rule, ':') !== false) {
                list($ruleName, $param) = explode(':', $rule, 2);
            } else {
                $ruleName = $rule;
                $param = null;
            }
            
            // Goi method validate tuong ung
            $method = 'validate' . ucfirst($ruleName);
            
            if (method_exists($this, $method)) {
                $this->$method($field, $value, $param);
            }
        }
    }
    
    /**
     * Validate: required
     */
    private function validateRequired($field, $value, $param = null) {
        if (empty($value) && $value !== '0') {
            $this->addError($field, ucfirst($field) . ' khong duoc de trong');
        }
    }
    
    /**
     * Validate: email
     */
    private function validateEmail($field, $value, $param = null) {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, ucfirst($field) . ' khong hop le');
        }
    }
    
    /**
     * Validate: min length
     */
    private function validateMin($field, $value, $param) {
        if (!empty($value) && strlen($value) < $param) {
            $this->addError($field, ucfirst($field) . ' phai co it nhat ' . $param . ' ky tu');
        }
    }
    
    /**
     * Validate: max length
     */
    private function validateMax($field, $value, $param) {
        if (!empty($value) && strlen($value) > $param) {
            $this->addError($field, ucfirst($field) . ' khong duoc qua ' . $param . ' ky tu');
        }
    }
    
    /**
     * Validate: match (xac nhan password)
     */
    private function validateMatch($field, $value, $param) {
        $matchValue = isset($this->data[$param]) ? $this->data[$param] : null;
        
        if ($value !== $matchValue) {
            $this->addError($field, ucfirst($field) . ' khong khop');
        }
    }
    
    /**
     * Validate: unique in database
     * Vi du: unique:users,email
     */
    private function validateUnique($field, $value, $param) {
        if (empty($value)) return;
        
        list($table, $column) = explode(',', $param);
        
        $db = Database::getInstance();
        $result = $db->fetchOne(
            "SELECT COUNT(*) as count FROM $table WHERE $column = ?",
            [$value]
        );
        
        if ($result['count'] > 0) {
            $this->addError($field, ucfirst($field) . ' da ton tai');
        }
    }
    
    /**
     * Validate: numeric
     */
    private function validateNumeric($field, $value, $param = null) {
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, ucfirst($field) . ' phai la so');
        }
    }
    
    /**
     * Validate: alpha (chi chu cai)
     */
    private function validateAlpha($field, $value, $param = null) {
        if (!empty($value) && !ctype_alpha($value)) {
            $this->addError($field, ucfirst($field) . ' chi duoc chua chu cai');
        }
    }
    
    /**
     * Validate: alphanumeric
     */
    private function validateAlphanumeric($field, $value, $param = null) {
        if (!empty($value) && !ctype_alnum($value)) {
            $this->addError($field, ucfirst($field) . ' chi duoc chua chu va so');
        }
    }
    
    /**
     * Validate: in (trong danh sach)
     * Vi du: in:admin,salesperson
     */
    private function validateIn($field, $value, $param) {
        $allowed = explode(',', $param);
        
        if (!empty($value) && !in_array($value, $allowed)) {
            $this->addError($field, ucfirst($field) . ' khong hop le');
        }
    }
    
    /**
     * Validate: confirmed (password confirmation)
     */
    private function validateConfirmed($field, $value, $param = null) {
        $confirmField = $field . '_confirmation';
        $confirmValue = isset($this->data[$confirmField]) ? $this->data[$confirmField] : null;
        
        if ($value !== $confirmValue) {
            $this->addError($field, ucfirst($field) . ' xac nhan khong khop');
        }
    }
    
    /**
     * Them loi vao mang errors
     */
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    /**
     * Kiem tra co loi khong
     */
    public function fails() {
        return !empty($this->errors);
    }
    
    /**
     * Kiem tra thanh cong
     */
    public function passes() {
        return empty($this->errors);
    }
    
    /**
     * Lay tat ca loi
     */
    public function errors() {
        return $this->errors;
    }
    
    /**
     * Lay loi cua 1 field
     */
    public function error($field) {
        return isset($this->errors[$field]) ? $this->errors[$field][0] : null;
    }
    
    /**
     * Lay du lieu da validate
     */
    public function validated() {
        $validated = [];
        
        foreach ($this->rules as $field => $rule) {
            if (isset($this->data[$field])) {
                $validated[$field] = $this->data[$field];
            }
        }
        
        return $validated;
    }
}