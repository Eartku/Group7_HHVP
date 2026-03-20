<?php
// app/core/Model.php

class Model {
    protected static function db(): mysqli {
        return Database::getInstance();
    }
}
