#!/bin/bash
set -e

echo "=== WashingStore Container Starting ==="

# ---------------------------------------------------------
# 1. Trích xuất và cấp quyền chứng chỉ SSL từ biến $MYSQL_ATTR_SSL_CA
# ---------------------------------------------------------
if [ -n "$MYSQL_ATTR_SSL_CA" ]; then
    SSL_DIR="/etc/ssl/mysql"
    SSL_CERT_PATH="${SSL_DIR}/ca.pem"
    mkdir -p "$SSL_DIR"

    if [ -f "$MYSQL_ATTR_SSL_CA" ]; then
        echo "Using existing SSL CA certificate file at: $MYSQL_ATTR_SSL_CA"
        # Sao chép vào thư mục khả ghi trong container để tránh lỗi Read-only file system (Render Secret Files)
        cp "$MYSQL_ATTR_SSL_CA" "$SSL_CERT_PATH"
        chmod 644 "$SSL_CERT_PATH" 2>/dev/null || true
        export MYSQL_ATTR_SSL_CA="$SSL_CERT_PATH"
    else
        echo "Creating SSL CA certificate from environment variable..."
        if echo "$MYSQL_ATTR_SSL_CA" | grep -q "BEGIN CERTIFICATE"; then
            echo "$MYSQL_ATTR_SSL_CA" > "$SSL_CERT_PATH"
        else
            # Thử decode base64, nếu không thành công thì ghi trực tiếp
            if echo "$MYSQL_ATTR_SSL_CA" | base64 -d > "$SSL_CERT_PATH" 2>/dev/null && grep -q "BEGIN CERTIFICATE" "$SSL_CERT_PATH"; then
                echo "Decoded SSL CA from base64 string."
            else
                echo "$MYSQL_ATTR_SSL_CA" > "$SSL_CERT_PATH"
            fi
        fi
        chmod 644 "$SSL_CERT_PATH" 2>/dev/null || true
        export MYSQL_ATTR_SSL_CA="$SSL_CERT_PATH"
    fi
    echo "SSL CA certificate ready at: $MYSQL_ATTR_SSL_CA"
fi

# ---------------------------------------------------------
# 2. Gán biến $PORT vào Nginx conf
# ---------------------------------------------------------
export PORT="${PORT:-80}"
echo "Configuring Nginx to listen on port: $PORT"
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# ---------------------------------------------------------
# Đảm bảo phân quyền lưu trữ và cache
# ---------------------------------------------------------
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Tạo symbolic link cho storage nếu chưa có
php artisan storage:link --no-interaction || true

# Tối ưu hóa bộ nhớ đệm Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# ---------------------------------------------------------
# 3. Chạy migrations (mặc định tự động chạy trừ khi RUN_MIGRATIONS=false)
# ---------------------------------------------------------
if [ "$RUN_MIGRATIONS" != "false" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# ---------------------------------------------------------
# 4. Chạy seeders khởi tạo dữ liệu
# ---------------------------------------------------------
if [ "$RUN_SEEDERS" = "true" ] || [ "$RUN_SEEDERS" = "1" ]; then
    echo "Running full database seeders..."
    php artisan db:seed --force || true
else
    # Tự động nạp danh mục và sản phẩm mẫu vào database
    echo "Ensuring product and category catalog data..."
    php artisan db:seed --class=ProductCategorySeeder --force || true
fi

# ---------------------------------------------------------
# 5. Khởi động Nginx và PHP-FPM song song
# ---------------------------------------------------------
echo "Starting PHP-FPM in background..."
php-fpm -D

echo "Starting Nginx in foreground on port $PORT..."
exec nginx -g "daemon off;"
