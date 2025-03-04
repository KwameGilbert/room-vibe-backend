<?php
// Import all routes
return function($app) {
    
    (require_once __DIR__ . '/php/HostelRoute.php')($app);
    
};
