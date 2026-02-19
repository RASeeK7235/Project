<?php
if(mail('shtraseek0@gmail.com', 'Test Email', 'This is a test.')) {
    echo 'Mail sent!';
} else {
    echo 'Mail failed!';
}
?>