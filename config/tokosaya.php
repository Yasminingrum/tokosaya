<?php
// File: config/tokosaya.php

return [
    /*
    |--------------------------------------------------------------------------
    | TokoSaya Application Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to the TokoSaya
    | e-commerce application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    */
    'app' => [
        'name' => env('TOKOSAYA_APP_NAME', 'TokoSaya'),
        'tagline' => env('TOKOSAYA_TAGLINE', 'Your Favorite Online Store'),
        'version' => '1.0.0',
        'timezone' => env('TOKOSAYA_TIMEZONE', 'Asia/Jakarta'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    */
    'currency' => [
        'code' => env('TOKOSAYA_CURRENCY', 'IDR'),
        'symbol' => env('TOKOSAYA_CURRENCY_SYMBOL', 'Rp'),
        'symbol_position' => env('TOKOSAYA_SYMBOL_POSITION', 'before'), // before or after
        'decimal_places' => env('TOKOSAYA_DECIMAL_PLACES', 0),
        'thousands_separator' => env('TOKOSAYA_THOUSANDS_SEPARATOR', '.'),
        'decimal_separator' => env('TOKOSAYA_DECIMAL_SEPARATOR', ','),
    ],

    /*
    |--------------------------------------------------------------------------
    | Order Settings
    |--------------------------------------------------------------------------
    */
    'orders' => [
        'prefix' => env('TOKOSAYA_ORDER_PREFIX', 'TS'),
        'auto_confirm_payment' => env('TOKOSAYA_AUTO_CONFIRM_PAYMENT', false),
        'expiry_hours' => env('TOKOSAYA_ORDER_EXPIRY_HOURS', 24),
        'min_order_amount_cents' => env('TOKOSAYA_MIN_ORDER_AMOUNT_CENTS', 1000), // Rp 10
        'max_order_amount_cents' => env('TOKOSAYA_MAX_ORDER_AMOUNT_CENTS', 1000000000), // Rp 10 Million
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory Settings
    |--------------------------------------------------------------------------
    */
    'inventory' => [
        'low_stock_threshold' => env('TOKOSAYA_LOW_STOCK_THRESHOLD', 5),
        'auto_reduce_stock' => env('TOKOSAYA_AUTO_REDUCE_STOCK', true),
        'allow_backorder' => env('TOKOSAYA_ALLOW_BACKORDER', false),
        'track_stock_by_default' => env('TOKOSAYA_TRACK_STOCK_DEFAULT', true),
        'reserve_stock_minutes' => env('TOKOSAYA_RESERVE_STOCK_MINUTES', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping Settings
    |--------------------------------------------------------------------------
    */
    'shipping' => [
        'default_weight_unit' => env('TOKOSAYA_DEFAULT_WEIGHT_UNIT', 'grams'),
        'default_dimension_unit' => env('TOKOSAYA_DEFAULT_DIMENSION_UNIT', 'mm'),
        'free_shipping_threshold_cents' => env('TOKOSAYA_FREE_SHIPPING_THRESHOLD_CENTS', 10000000), // Rp 100,000
        'default_shipping_method_id' => env('TOKOSAYA_DEFAULT_SHIPPING_METHOD_ID', 1),
        'calculate_by' => env('TOKOSAYA_SHIPPING_CALCULATE_BY', 'weight'), // weight, amount, or both
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Settings
    |--------------------------------------------------------------------------
    */
    'tax' => [
        'enabled' => env('TOKOSAYA_TAX_ENABLED', true),
        'default_rate' => env('TOKOSAYA_DEFAULT_TAX_RATE', 11), // 11% VAT for Indonesia
        'include_shipping' => env('TOKOSAYA_TAX_INCLUDE_SHIPPING', false),
        'display_inclusive' => env('TOKOSAYA_TAX_DISPLAY_INCLUSIVE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Product Settings
    |--------------------------------------------------------------------------
    */
    'products' => [
        'per_page' => env('TOKOSAYA_PRODUCTS_PER_PAGE', 12),
        'max_images' => env('TOKOSAYA_MAX_PRODUCT_IMAGES', 10),
        'image_max_size_mb' => env('TOKOSAYA_IMAGE_MAX_SIZE_MB', 2),
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'webp'],
        'auto_generate_sku' => env('TOKOSAYA_AUTO_GENERATE_SKU', false),
        'sku_prefix' => env('TOKOSAYA_SKU_PREFIX', 'PRD'),
        'featured_products_count' => env('TOKOSAYA_FEATURED_PRODUCTS_COUNT', 8),
        'related_products_count' => env('TOKOSAYA_RELATED_PRODUCTS_COUNT', 6),
        'enable_reviews' => env('TOKOSAYA_ENABLE_REVIEWS', true),
        'require_review_approval' => env('TOKOSAYA_REQUIRE_REVIEW_APPROVAL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cart Settings
    |--------------------------------------------------------------------------
    */
    'cart' => [
        'session_lifetime_days' => env('TOKOSAYA_CART_SESSION_LIFETIME_DAYS', 7),
        'user_lifetime_days' => env('TOKOSAYA_CART_USER_LIFETIME_DAYS', 30),
        'max_quantity_per_item' => env('TOKOSAYA_MAX_QUANTITY_PER_ITEM', 10),
        'auto_cleanup_expired' => env('TOKOSAYA_AUTO_CLEANUP_EXPIRED_CARTS', true),
        'merge_on_login' => env('TOKOSAYA_MERGE_CART_ON_LOGIN', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Settings
    |--------------------------------------------------------------------------
    */
    'search' => [
        'enabled' => env('TOKOSAYA_SEARCH_ENABLED', true),
        'min_query_length' => env('TOKOSAYA_MIN_SEARCH_QUERY_LENGTH', 2),
        'max_results' => env('TOKOSAYA_MAX_SEARCH_RESULTS', 100),
        'cache_results_minutes' => env('TOKOSAYA_CACHE_SEARCH_RESULTS_MINUTES', 60),
        'enable_suggestions' => env('TOKOSAYA_ENABLE_SEARCH_SUGGESTIONS', true),
        'log_searches' => env('TOKOSAYA_LOG_SEARCHES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'products_ttl' => env('TOKOSAYA_CACHE_PRODUCTS_TTL', 3600), // 1 hour
        'categories_ttl' => env('TOKOSAYA_CACHE_CATEGORIES_TTL', 7200), // 2 hours
        'brands_ttl' => env('TOKOSAYA_CACHE_BRANDS_TTL', 7200), // 2 hours
        'settings_ttl' => env('TOKOSAYA_CACHE_SETTINGS_TTL', 3600), // 1 hour
        'search_ttl' => env('TOKOSAYA_CACHE_SEARCH_TTL', 1800), // 30 minutes
        'homepage_ttl' => env('TOKOSAYA_CACHE_HOMEPAGE_TTL', 600), // 10 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'max_login_attempts' => env('TOKOSAYA_MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration_minutes' => env('TOKOSAYA_LOCKOUT_DURATION_MINUTES', 30),
        'password_expires_days' => env('TOKOSAYA_PASSWORD_EXPIRES_DAYS', 90),
        'require_email_verification' => env('TOKOSAYA_REQUIRE_EMAIL_VERIFICATION', true),
        'enable_two_factor' => env('TOKOSAYA_ENABLE_TWO_FACTOR', false),
        'session_timeout_minutes' => env('TOKOSAYA_SESSION_TIMEOUT_MINUTES', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Settings
    |--------------------------------------------------------------------------
    */
    'email' => [
        'order_confirmation' => env('TOKOSAYA_EMAIL_ORDER_CONFIRMATION', true),
        'payment_confirmation' => env('TOKOSAYA_EMAIL_PAYMENT_CONFIRMATION', true),
        'shipping_notification' => env('TOKOSAYA_EMAIL_SHIPPING_NOTIFICATION', true),
        'delivery_confirmation' => env('TOKOSAYA_EMAIL_DELIVERY_CONFIRMATION', true),
        'welcome_email' => env('TOKOSAYA_EMAIL_WELCOME', true),
        'password_reset' => env('TOKOSAYA_EMAIL_PASSWORD_RESET', true),
        'low_stock_alert' => env('TOKOSAYA_EMAIL_LOW_STOCK_ALERT', true),
        'new_order_admin' => env('TOKOSAYA_EMAIL_NEW_ORDER_ADMIN', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => env('TOKOSAYA_NOTIFICATIONS_ENABLED', true),
        'auto_delete_read_after_days' => env('TOKOSAYA_AUTO_DELETE_READ_NOTIFICATIONS_DAYS', 30),
        'max_notifications_per_user' => env('TOKOSAYA_MAX_NOTIFICATIONS_PER_USER', 100),
        'enable_push_notifications' => env('TOKOSAYA_ENABLE_PUSH_NOTIFICATIONS', false),
        'enable_sms_notifications' => env('TOKOSAYA_ENABLE_SMS_NOTIFICATIONS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Settings
    |--------------------------------------------------------------------------
    */
    'media' => [
        'disk' => env('TOKOSAYA_MEDIA_DISK', 'public'),
        'max_file_size_mb' => env('TOKOSAYA_MAX_FILE_SIZE_MB', 10),
        'allowed_file_types' => [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
            'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            'videos' => ['mp4', 'avi', 'mov', 'wmv'],
        ],
        'image_sizes' => [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'small' => ['width' => 300, 'height' => 300],
            'medium' => ['width' => 600, 'height' => 600],
            'large' => ['width' => 1200, 'height' => 1200],
        ],
        'enable_cdn' => env('TOKOSAYA_ENABLE_CDN', false),
        'cdn_url' => env('TOKOSAYA_CDN_URL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Settings
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'default_meta_title' => env('TOKOSAYA_DEFAULT_META_TITLE', 'TokoSaya - Your Favorite Online Store'),
        'default_meta_description' => env('TOKOSAYA_DEFAULT_META_DESCRIPTION', 'Shop the best products at TokoSaya with fast delivery and great prices'),
        'default_meta_keywords' => env('TOKOSAYA_DEFAULT_META_KEYWORDS', 'online store, shopping, ecommerce, tokosaya'),
        'enable_sitemap' => env('TOKOSAYA_ENABLE_SITEMAP', true),
        'enable_schema_markup' => env('TOKOSAYA_ENABLE_SCHEMA_MARKUP', true),
        'enable_open_graph' => env('TOKOSAYA_ENABLE_OPEN_GRAPH', true),
        'enable_twitter_cards' => env('TOKOSAYA_ENABLE_TWITTER_CARDS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Settings
    |--------------------------------------------------------------------------
    */
    'analytics' => [
        'google_analytics_id' => env('TOKOSAYA_GOOGLE_ANALYTICS_ID', ''),
        'facebook_pixel_id' => env('TOKOSAYA_FACEBOOK_PIXEL_ID', ''),
        'enable_ecommerce_tracking' => env('TOKOSAYA_ENABLE_ECOMMERCE_TRACKING', true),
        'track_product_views' => env('TOKOSAYA_TRACK_PRODUCT_VIEWS', true),
        'track_cart_events' => env('TOKOSAYA_TRACK_CART_EVENTS', true),
        'track_checkout_events' => env('TOKOSAYA_TRACK_CHECKOUT_EVENTS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media Settings
    |--------------------------------------------------------------------------
    */
    'social' => [
        'facebook_url' => env('TOKOSAYA_FACEBOOK_URL', ''),
        'instagram_url' => env('TOKOSAYA_INSTAGRAM_URL', ''),
        'twitter_url' => env('TOKOSAYA_TWITTER_URL', ''),
        'youtube_url' => env('TOKOSAYA_YOUTUBE_URL', ''),
        'linkedin_url' => env('TOKOSAYA_LINKEDIN_URL', ''),
        'whatsapp_number' => env('TOKOSAYA_WHATSAPP_NUMBER', ''),
        'enable_social_login' => env('TOKOSAYA_ENABLE_SOCIAL_LOGIN', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Settings
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'auto_cleanup_logs_days' => env('TOKOSAYA_AUTO_CLEANUP_LOGS_DAYS', 30),
        'auto_cleanup_temp_files_hours' => env('TOKOSAYA_AUTO_CLEANUP_TEMP_FILES_HOURS', 24),
        'auto_optimize_images' => env('TOKOSAYA_AUTO_OPTIMIZE_IMAGES', true),
        'backup_database_days' => env('TOKOSAYA_BACKUP_DATABASE_DAYS', 7),
        'enable_debug_mode' => env('TOKOSAYA_ENABLE_DEBUG_MODE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    */
    'api' => [
        'enabled' => env('TOKOSAYA_API_ENABLED', false),
        'version' => env('TOKOSAYA_API_VERSION', 'v1'),
        'rate_limit' => env('TOKOSAYA_API_RATE_LIMIT', 60), // requests per minute
        'enable_cors' => env('TOKOSAYA_API_ENABLE_CORS', true),
        'require_api_key' => env('TOKOSAYA_API_REQUIRE_KEY', true),
        'enable_documentation' => env('TOKOSAYA_API_ENABLE_DOCS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        'enable_wishlist' => env('TOKOSAYA_ENABLE_WISHLIST', true),
        'enable_compare' => env('TOKOSAYA_ENABLE_COMPARE', true),
        'enable_reviews' => env('TOKOSAYA_ENABLE_REVIEWS', true),
        'enable_coupons' => env('TOKOSAYA_ENABLE_COUPONS', true),
        'enable_newsletters' => env('TOKOSAYA_ENABLE_NEWSLETTERS', true),
        'enable_blogs' => env('TOKOSAYA_ENABLE_BLOGS', false),
        'enable_multi_vendor' => env('TOKOSAYA_ENABLE_MULTI_VENDOR', false),
        'enable_pos' => env('TOKOSAYA_ENABLE_POS', false),
        'enable_mobile_app' => env('TOKOSAYA_ENABLE_MOBILE_APP', false),
        'enable_advanced_search' => env('TOKOSAYA_ENABLE_ADVANCED_SEARCH', true),
        'enable_live_chat' => env('TOKOSAYA_ENABLE_LIVE_CHAT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Integrations
    |--------------------------------------------------------------------------
    */
    'integrations' => [
        'payment_gateways' => [
            'midtrans' => [
                'enabled' => env('MIDTRANS_ENABLED', false),
                'server_key' => env('MIDTRANS_SERVER_KEY', ''),
                'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
                'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
            ],
            'xendit' => [
                'enabled' => env('XENDIT_ENABLED', false),
                'secret_key' => env('XENDIT_SECRET_KEY', ''),
                'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', ''),
            ],
            'paypal' => [
                'enabled' => env('PAYPAL_ENABLED', false),
                'client_id' => env('PAYPAL_CLIENT_ID', ''),
                'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
                'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
            ],
        ],
        'shipping_providers' => [
            'jne' => [
                'enabled' => env('JNE_ENABLED', false),
                'api_key' => env('JNE_API_KEY', ''),
                'username' => env('JNE_USERNAME', ''),
                'api_url' => env('JNE_API_URL', ''),
            ],
            'tiki' => [
                'enabled' => env('TIKI_ENABLED', false),
                'api_key' => env('TIKI_API_KEY', ''),
                'api_url' => env('TIKI_API_URL', ''),
            ],
            'pos' => [
                'enabled' => env('POS_ENABLED', false),
                'api_key' => env('POS_API_KEY', ''),
                'api_url' => env('POS_API_URL', ''),
            ],
        ],
        'sms_providers' => [
            'twilio' => [
                'enabled' => env('TWILIO_ENABLED', false),
                'account_sid' => env('TWILIO_ACCOUNT_SID', ''),
                'auth_token' => env('TWILIO_AUTH_TOKEN', ''),
                'from_number' => env('TWILIO_FROM_NUMBER', ''),
            ],
        ],
        'email_providers' => [
            'mailgun' => [
                'enabled' => env('MAILGUN_ENABLED', false),
                'domain' => env('MAILGUN_DOMAIN', ''),
                'secret' => env('MAILGUN_SECRET', ''),
            ],
            'sendgrid' => [
                'enabled' => env('SENDGRID_ENABLED', false),
                'api_key' => env('SENDGRID_API_KEY', ''),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'enable_query_cache' => env('TOKOSAYA_ENABLE_QUERY_CACHE', true),
        'enable_view_cache' => env('TOKOSAYA_ENABLE_VIEW_CACHE', true),
        'enable_route_cache' => env('TOKOSAYA_ENABLE_ROUTE_CACHE', true),
        'enable_config_cache' => env('TOKOSAYA_ENABLE_CONFIG_CACHE', true),
        'enable_opcache' => env('TOKOSAYA_ENABLE_OPCACHE', true),
        'enable_redis_cache' => env('TOKOSAYA_ENABLE_REDIS_CACHE', false),
        'enable_memcached' => env('TOKOSAYA_ENABLE_MEMCACHED', false),
        'enable_cdn' => env('TOKOSAYA_ENABLE_CDN', false),
        'enable_image_optimization' => env('TOKOSAYA_ENABLE_IMAGE_OPTIMIZATION', true),
        'enable_gzip_compression' => env('TOKOSAYA_ENABLE_GZIP_COMPRESSION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization Settings
    |--------------------------------------------------------------------------
    */
    'localization' => [
        'default_locale' => env('TOKOSAYA_DEFAULT_LOCALE', 'id'),
        'available_locales' => ['id', 'en'],
        'enable_multi_language' => env('TOKOSAYA_ENABLE_MULTI_LANGUAGE', false),
        'date_format' => env('TOKOSAYA_DATE_FORMAT', 'd/m/Y'),
        'time_format' => env('TOKOSAYA_TIME_FORMAT', 'H:i'),
        'datetime_format' => env('TOKOSAYA_DATETIME_FORMAT', 'd/m/Y H:i'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    */
    'contact' => [
        'company_name' => env('TOKOSAYA_COMPANY_NAME', 'TokoSaya Indonesia'),
        'email' => env('TOKOSAYA_CONTACT_EMAIL', 'info@tokosaya.com'),
        'phone' => env('TOKOSAYA_CONTACT_PHONE', '+62-21-12345678'),
        'whatsapp' => env('TOKOSAYA_WHATSAPP', '+62-812-3456-7890'),
        'address' => env('TOKOSAYA_ADDRESS', 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta'),
        'working_hours' => env('TOKOSAYA_WORKING_HOURS', 'Mon-Fri: 9:00-18:00, Sat: 9:00-15:00'),
        'support_email' => env('TOKOSAYA_SUPPORT_EMAIL', 'support@tokosaya.com'),
        'sales_email' => env('TOKOSAYA_SALES_EMAIL', 'sales@tokosaya.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Legal Information
    |--------------------------------------------------------------------------
    */
    'legal' => [
        'company_registration' => env('TOKOSAYA_COMPANY_REGISTRATION', ''),
        'tax_number' => env('TOKOSAYA_TAX_NUMBER', ''),
        'business_license' => env('TOKOSAYA_BUSINESS_LICENSE', ''),
        'terms_url' => env('TOKOSAYA_TERMS_URL', '/terms'),
        'privacy_url' => env('TOKOSAYA_PRIVACY_URL', '/privacy'),
        'return_policy_url' => env('TOKOSAYA_RETURN_POLICY_URL', '/return-policy'),
        'shipping_policy_url' => env('TOKOSAYA_SHIPPING_POLICY_URL', '/shipping-info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    */
    'development' => [
        'enable_debug_toolbar' => env('TOKOSAYA_ENABLE_DEBUG_TOOLBAR', false),
        'enable_sql_logging' => env('TOKOSAYA_ENABLE_SQL_LOGGING', false),
        'enable_api_documentation' => env('TOKOSAYA_ENABLE_API_DOCS', false),
        'seed_demo_data' => env('TOKOSAYA_SEED_DEMO_DATA', false),
        'faker_locale' => env('TOKOSAYA_FAKER_LOCALE', 'id_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Settings
    |--------------------------------------------------------------------------
    */
    'admin' => [
        'items_per_page' => env('TOKOSAYA_ADMIN_ITEMS_PER_PAGE', 20),
        'session_timeout_minutes' => env('TOKOSAYA_ADMIN_SESSION_TIMEOUT', 60),
        'enable_dark_mode' => env('TOKOSAYA_ADMIN_ENABLE_DARK_MODE', true),
        'dashboard_refresh_seconds' => env('TOKOSAYA_ADMIN_DASHBOARD_REFRESH', 30),
        'enable_quick_actions' => env('TOKOSAYA_ADMIN_ENABLE_QUICK_ACTIONS', true),
        'enable_bulk_operations' => env('TOKOSAYA_ADMIN_ENABLE_BULK_OPERATIONS', true),
        'max_bulk_operations' => env('TOKOSAYA_ADMIN_MAX_BULK_OPERATIONS', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Settings
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'enabled' => env('TOKOSAYA_BACKUP_ENABLED', true),
        'disk' => env('TOKOSAYA_BACKUP_DISK', 'local'),
        'schedule' => env('TOKOSAYA_BACKUP_SCHEDULE', 'daily'),
        'keep_backups_days' => env('TOKOSAYA_BACKUP_KEEP_DAYS', 30),
        'include_files' => env('TOKOSAYA_BACKUP_INCLUDE_FILES', true),
        'exclude_directories' => [
            'node_modules',
            'vendor',
            'storage/logs',
            'storage/cache',
        ],
        'notification_email' => env('TOKOSAYA_BACKUP_NOTIFICATION_EMAIL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'log_queries' => env('TOKOSAYA_LOG_QUERIES', false),
        'log_requests' => env('TOKOSAYA_LOG_REQUESTS', false),
        'log_errors' => env('TOKOSAYA_LOG_ERRORS', true),
        'log_user_actions' => env('TOKOSAYA_LOG_USER_ACTIONS', true),
        'log_admin_actions' => env('TOKOSAYA_LOG_ADMIN_ACTIONS', true),
        'max_log_files' => env('TOKOSAYA_MAX_LOG_FILES', 30),
        'log_level' => env('TOKOSAYA_LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    */
    'queue' => [
        'default_connection' => env('TOKOSAYA_QUEUE_CONNECTION', 'sync'),
        'email_queue' => env('TOKOSAYA_EMAIL_QUEUE', 'emails'),
        'image_processing_queue' => env('TOKOSAYA_IMAGE_PROCESSING_QUEUE', 'images'),
        'notification_queue' => env('TOKOSAYA_NOTIFICATION_QUEUE', 'notifications'),
        'backup_queue' => env('TOKOSAYA_BACKUP_QUEUE', 'backups'),
        'analytics_queue' => env('TOKOSAYA_ANALYTICS_QUEUE', 'analytics'),
    ],
];
