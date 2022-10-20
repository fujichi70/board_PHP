<?php

// エスケープ処理
function h ($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}