<?php
/**
 * Validator Class
 * Validation du lieu dau vao
 */
class Validator {
    
    private $errors = [];
    private $data = [];
    
    /**
     * Khoi tao voi du lieu can validate
     */
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * Validate du lieu theo rules
     * 
     * @param array $rules Mang rules, vi du:
     *  [
     *      'email' => 'required|email',
     *      'password' => 'required|min:6',
     *      'age' => 'required|numeric|min:18|max:100'
     *  ]
     */
    public function validate($rules) {
        foreach ($rules as $field => $rule) {
            $ruleList = explode('|', $rule);
            
            foreach ($ruleList as $singleRule) {
                // Tach rule va parameter (vi du: min:6)
                $ruleParts = explode(':', $singleRule);
                $ruleName = $ruleParts[0];
                $ruleParam = isset($ruleParts[1]) ? $ruleParts[1] : null;
                
                // Goi method validate tuong ung
                $methodName = 'validate' . ucfirst($ruleName);
                
                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $ruleParam);
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Bat buoc nhap
     */
    private function validateRequired($field, $param = null) {
        $value = $this->getValue($field);
        
        if (empty($value) && $value !== '0') {
            $this->addError($field, ucfirst($field) . ' la bat buoc');
        }
    }
    
    /**
     * Validate email
     */
    private function validateEmail($field, $param = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, ucfirst($field) . ' khong dung dinh dang email');
        }
    }
    
    /**
     * Do dai toi thieu
     */
    private function validateMin($field, $param) {
        $value = $this->getValue($field);
        
        if (empty($value)) {
            return;
        }
        
        if (is_numeric($value)) {
            // So
            if ($value < $param) {
                $this->addError($field, ucfirst($field) . ' phai lon hon hoac bang ' . $param);
            }
        } else {
            // Chuoi
            if (strlen($value) < $param) {
                $this->addError($field, ucfirst($field) . ' phai co it nhat ' . $param . ' ky tu');
            }
        }
    }
    
    /**
     * Do dai toi da
     */
    private function validateMax($field, $param) {
        $value = $this->getValue($field);
        
        if (empty($value)) {
            return;
        }
        
        if (is_numeric($value)) {
            // So
            if ($value > $param) {
                $this->addError($field, ucfirst($field) . ' phai nho hon hoac bang ' . $param);
            }
        } else {
            // Chuoi
            if (strlen($value) > $param) {
                $this->addError($field, ucfirst($field) . ' khong duoc qua ' . $param . ' ky tu');
            }
        }
    }
    
    /**
     * Validate so
     */
    private function validateNumeric($field, $param = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, ucfirst($field) . ' phai la so');
        }
    }
    
    /**
     * Validate so nguyen
     */
    private function validateInteger($field, $param = null) {
        $value = $this->getValue($field);
        
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, ucfirst($field) . ' phai la so nguyen');
        }
    }
    
    /**
     * Validate so dien thoai Viet Nam
     */
    private function validatePhone($field, $param = null) {
        $value = $this->getValue($field);
        
        if (!empty($value)) {
            // Format: 0xxx-xxx-xxx hoac 84xxxxxxxxx
            $pattern = '/^(0|\+84|84)[0-9]{9,10}$/';
            
            if (!preg_match($pattern, $value)) {
                $this->addError($field, ucfirst($field) . ' khong dung dinh dang');
            }
        }
    }
    
    /**
     * Khop voi field khac (dung cho confirm password)
     */
    private function validateMatch($field, $param) {
        $value = $this->getValue($field);
        $matchValue = $this->getValue($param);
        
        if ($value !== $matchValue) {
            $this->addError($field, ucfirst($field) . ' khong khop voi ' . $param);
        }
    }
    
    /**
     * Gia tri duy nhat trong database
     */
    private function validateUnique($field, $param) {
        $value = $this->getValue($field);
        
        if (empty($value)) {
            return;
        }
        
        // Param format: "table,column,exceptId"
        $parts = explode(',', $param);
        $table = $parts[0];
        $column = isset($parts[1]) ? $parts[1] : $field;
        $exceptId = isset($parts[2]) ? $parts[2] : null;
        
        $db = Database::getInstance();
        
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
        $params = [$value];
        
        // Loai tru ban ghi hien tai (khi update)
        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }
        
        $result = $db->fetchOne($sql, $params);
        
        if ($result['count'] > 0) {
            $this->addError($field, ucfirst($field) . ' đã tồn tại');
        }
    }
    
    /**
     * Validate file upload
     */
    private function validateFile($field, $param = null) {
        if (!isset($_FILES[$field])) {
            return;
        }
        
        $file = $_FILES[$field];
        
        // Kiem tra loi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->addError($field, 'Loi khi upload file');
            return;
        }
        
        // Kiem tra kich thuoc
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            $maxMB = UPLOAD_MAX_SIZE / (1024 * 1024);
            $this->addError($field, 'Kich thuoc file khong duoc vuot qua ' . $maxMB . 'MB');
        }
        
        // Kiem tra dinh dang
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, UPLOAD_ALLOWED_TYPES)) {
            $this->addError($field, 'Dinh dang file khong hop le. Chi chap nhan: ' . implode(', ', UPLOAD_ALLOWED_TYPES));
        }
    }
    
    /**
     * Validate image
     */
    private function validateImage($field, $param = null) {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return;
        }
        
        $file = $_FILES[$field];
        
        // Kiem tra co phai anh khong
        $imageInfo = @getimagesize($file['tmp_name']);
        
        if ($imageInfo === false) {
            $this->addError($field, 'File khong phai la hinh anh hop le');
        }
    }
    
    /**
     * Lay gia tri tu data
     */
    private function getValue($field) {
        return isset($this->data[$field]) ? $this->data[$field] : '';
    }
    
    /**
     * Them loi
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
     * Lay tat ca loi
     */
    public function errors() {
        return $this->errors;
    }
    
    /**
     * Lay loi cua mot field
     */
    public function error($field) {
        return isset($this->errors[$field]) ? $this->errors[$field][0] : '';
    }
    
    /**
     * Lay loi dau tien
     */
    public function firstError() {
        if (empty($this->errors)) {
            return '';
        }
        
        $firstField = array_key_first($this->errors);
        return $this->errors[$firstField][0];
    }
}
?>