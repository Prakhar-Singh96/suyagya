<?php
// यह कमांड एडमिन फोल्डर का शॉर्टकट बनाएगी
symlink(__DIR__ . '/public/admin', __DIR__ . '/admin');
echo "Admin link created! Now refresh your dashboard.";
?>