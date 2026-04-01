<?php
function generateUUID() {
    return bin2hex(random_bytes(32)); // 64 chars
}

function generateApiKey() {
    return bin2hex(random_bytes(32)); // 64 chars
}
?>