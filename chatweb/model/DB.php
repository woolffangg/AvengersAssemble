<?php
class DB {
    public static function connect() {
        return new PDO('mysql:host=localhost;dbname=chat_web;charset=utf8', 'root', '');
    }
}
