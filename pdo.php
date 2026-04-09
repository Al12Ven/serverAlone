<?php
class DB extends PDO {
    public function __construct() {
        parent::__construct(
            "mysql:host=127.0.0.1;dbname=alr1;charset=utf8",
            "root",
            ""
        );
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
}
