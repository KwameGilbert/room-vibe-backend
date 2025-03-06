<?php
// Import all routes
return function($app) {
    
    (require_once __DIR__ . '/php/HostelRoute.php')($app);
    (require_once __DIR__ . '/php/AmenityRoute.php')($app);
    (require_once __DIR__ . '/php/ReviewRoute.php')($app);
    (require_once __DIR__ . '/php/StudentRoute.php')($app);
   // (require_once __DIR__ . '/php/HostelImageRoute.php')($app);
};
