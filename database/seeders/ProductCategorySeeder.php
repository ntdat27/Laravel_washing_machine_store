<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. Tạo Danh Mục Máy Giặt & Máy Sấy
        // -------------------------------------------------------------
        $categories = [
            [
                'name' => 'Máy giặt cửa trước',
                'slug' => 'may-giat-cua-truoc',
                'description' => 'Máy giặt lồng ngang tiết kiệm nước, bảo vệ sợi vải và tích hợp nhiều công nghệ giặt hiện đại.'
            ],
            [
                'name' => 'Máy giặt cửa trên',
                'slug' => 'may-giat-cua-tren',
                'description' => 'Máy giặt lồng đứng truyền thống, thiết kế nhỏ gọn, dễ thêm bớt đồ giặt và giá thành hợp lý.'
            ],
            [
                'name' => 'Máy giặt sấy kết hợp',
                'slug' => 'may-giat-say-ket-hop',
                'description' => 'Giải pháp 2 trong 1 tiện lợi cho căn hộ hiện đại, giặt sạch và sấy khô quần áo tức thì.'
            ],
            [
                'name' => 'Máy sấy quần áo',
                'slug' => 'may-say-quan-ao',
                'description' => 'Máy sấy bơm nhiệt Heatpump và ngưng tụ giúp quần áo khô nhanh, chống nhăn và kháng khuẩn tối ưu.'
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['slug']] = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description']
                ]
            );
        }

        // -------------------------------------------------------------
        // 2. Tạo Danh Sách Sản Phẩm Mẫu
        // -------------------------------------------------------------
        $products = [
            [
                'category_slug' => 'may-giat-cua-truoc',
                'name' => 'Máy giặt LG AI DD Inverter 9 kg FV1409S4W',
                'brand' => 'LG',
                'capacity_kg' => 9.0,
                'price' => 8490000,
                'stock_quantity' => 25,
                'image' => 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?w=800&auto=format&fit=crop&q=80',
                'description' => 'Công nghệ AI DD tự động phát hiện độ mềm sợi vải và khối lượng đồ giặt để đưa ra chuyển động giặt tối ưu nhất. Động cơ truyền động trực tiếp Inverter vận hành êm ái, bảo hành 10 năm.',
                'variants' => [
                    ['color_name' => 'Trắng Tinh Tế', 'color_code' => '#FFFFFF', 'sku' => 'LG-FV1409S4W-WHT', 'price' => 8490000, 'stock' => 15],
                    ['color_name' => 'Xám Đậm Sang Trọng', 'color_code' => '#4A4A4A', 'sku' => 'LG-FV1409S4W-GRY', 'price' => 8790000, 'stock' => 10],
                ]
            ],
            [
                'category_slug' => 'may-giat-cua-truoc',
                'name' => 'Máy giặt Electrolux UltimateCare 500 Inverter 10 kg EWF1024P5WB',
                'brand' => 'Electrolux',
                'capacity_kg' => 10.0,
                'price' => 10290000,
                'stock_quantity' => 20,
                'image' => 'https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?w=800&auto=format&fit=crop&q=80',
                'description' => 'Công nghệ UltraMix hòa tan hoàn toàn bột giặt, không bám cặn lên quần áo. Giặt hơi nước Hygienic Care tiêu diệt 99.9% vi khuẩn và tác nhân gây dị ứng.',
                'variants' => [
                    ['color_name' => 'Trắng Cổ Điển', 'color_code' => '#FFFFFF', 'sku' => 'ELX-EWF1024-WHT', 'price' => 10290000, 'stock' => 12],
                    ['color_name' => 'Bạc Titan', 'color_code' => '#C0C0C0', 'sku' => 'ELX-EWF1024-SLV', 'price' => 10690000, 'stock' => 8],
                ]
            ],
            [
                'category_slug' => 'may-giat-cua-truoc',
                'name' => 'Máy giặt Samsung AI EcoBubble Inverter 9.5 kg WW95TA046AX',
                'brand' => 'Samsung',
                'capacity_kg' => 9.5,
                'price' => 9190000,
                'stock_quantity' => 18,
                'image' => 'https://images.unsplash.com/photo-1582735689369-4fe89db7114c?w=800&auto=format&fit=crop&q=80',
                'description' => 'Bong bóng siêu mịn EcoBubble thẩm thấu sâu gấp 40 lần, đánh bật vết bẩn cứng đầu ngay cả ở nhiệt độ nước thường. Chế độ ngâm Bubble Soak làm mềm vết bẩn.',
                'variants' => [
                    ['color_name' => 'Đen Inox Huyền Bí', 'color_code' => '#212529', 'sku' => 'SAM-WW95TA-BLK', 'price' => 9190000, 'stock' => 18],
                ]
            ],
            [
                'category_slug' => 'may-giat-cua-tren',
                'name' => 'Máy giặt Panasonic Inverter 9.5 kg NA-FD95V1BRV',
                'brand' => 'Panasonic',
                'capacity_kg' => 9.5,
                'price' => 7490000,
                'stock_quantity' => 30,
                'image' => 'https://images.unsplash.com/photo-1545173168-9f1947eebb7f?w=800&auto=format&fit=crop&q=80',
                'description' => 'Công nghệ giặt nước nóng StainMaster+ loại bỏ vi khuẩn và vết dầu mỡ cứng đầu. Mâm giặt 8 cánh tạo luồng xoáy nước cực mạnh Water Bazooka.',
                'variants' => [
                    ['color_name' => 'Đen Bóng', 'color_code' => '#1A1A1A', 'sku' => 'PAN-NAFD95-BLK', 'price' => 7490000, 'stock' => 30],
                ]
            ],
            [
                'category_slug' => 'may-giat-cua-tren',
                'name' => 'Máy giặt Toshiba Inverter 8.5 kg AW-DUK950WV(KK)',
                'brand' => 'Toshiba',
                'capacity_kg' => 8.5,
                'price' => 5990000,
                'stock_quantity' => 22,
                'image' => 'https://images.unsplash.com/photo-1517677208171-0bc6725a3e60?w=800&auto=format&fit=crop&q=80',
                'description' => 'Công nghệ siêu bọt khí Nano Ultra Fine Bubble tạo ra hàng tỷ bọt khí siêu nhỏ thẩm thấu vào từng thớ vải. Động cơ truyền động trực tiếp Origin Inverter siêu bền.',
                'variants' => [
                    ['color_name' => 'Xám Bạc', 'color_code' => '#808080', 'sku' => 'TOS-AWDUK950-SLV', 'price' => 5990000, 'stock' => 22],
                ]
            ],
            [
                'category_slug' => 'may-giat-say-ket-hop',
                'name' => 'Máy giặt sấy Electrolux UltimateCare 700 Giặt 10 kg Sấy 7 kg EWW1042R7WC',
                'brand' => 'Electrolux',
                'capacity_kg' => 10.0,
                'price' => 15990000,
                'stock_quantity' => 15,
                'image' => 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?w=800&auto=format&fit=crop&q=80',
                'description' => 'Máy giặt tích hợp sấy tiện lợi, công nghệ cảm biến SensorWash tự động điều chỉnh chu trình giặt theo độ bẩn. Kết nối thông minh qua ứng dụng Electrolux Life.',
                'variants' => [
                    ['color_name' => 'Trắng Sữa', 'color_code' => '#F8F9FA', 'sku' => 'ELX-EWW1042-WHT', 'price' => 15990000, 'stock' => 10],
                    ['color_name' => 'Xám Onyx', 'color_code' => '#343A40', 'sku' => 'ELX-EWW1042-ONX', 'price' => 16490000, 'stock' => 5],
                ]
            ],
            [
                'category_slug' => 'may-giat-say-ket-hop',
                'name' => 'Máy giặt sấy LG AI DD Giặt 11 kg Sấy 7 kg FV1411H3BA',
                'brand' => 'LG',
                'capacity_kg' => 11.0,
                'price' => 17490000,
                'stock_quantity' => 12,
                'image' => 'https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?w=800&auto=format&fit=crop&q=80',
                'description' => 'Công nghệ sấy ngưng tụ EcoHybrid tiết kiệm nước và điện năng. Giặt nhanh 39 phút với công nghệ TurboWash 360 độ cực kỳ tiện lợi cho người bận rộn.',
                'variants' => [
                    ['color_name' => 'Đen Thép Titan', 'color_code' => '#212529', 'sku' => 'LG-FV1411H3-BLK', 'price' => 17490000, 'stock' => 12],
                ]
            ],
            [
                'category_slug' => 'may-say-quan-ao',
                'name' => 'Máy sấy bơm nhiệt Heatpump Samsung 9 kg DV90T7240BB/SV',
                'brand' => 'Samsung',
                'capacity_kg' => 9.0,
                'price' => 14290000,
                'stock_quantity' => 10,
                'image' => 'https://images.unsplash.com/photo-1582735689369-4fe89db7114c?w=800&auto=format&fit=crop&q=80',
                'description' => 'Công nghệ sấy bơm nhiệt Heatpump bảo vệ sợi vải tối đa và tiết kiệm đến 50% điện năng tiêu thụ. Bảng điều khiển thông minh AI Control tự ghi nhớ thói quen sấy.',
                'variants' => [
                    ['color_name' => 'Đen Thép', 'color_code' => '#2B2D42', 'sku' => 'SAM-DV90T-BLK', 'price' => 14290000, 'stock' => 10],
                ]
            ],
        ];

        foreach ($products as $pData) {
            $cat = $categoryModels[$pData['category_slug']] ?? null;
            if (!$cat) continue;

            $product = Product::updateOrCreate(
                ['name' => $pData['name']],
                [
                    'category_id' => $cat->id,
                    'brand' => $pData['brand'],
                    'capacity_kg' => $pData['capacity_kg'],
                    'price' => $pData['price'],
                    'stock_quantity' => $pData['stock_quantity'],
                    'image' => $pData['image'],
                    'description' => $pData['description']
                ]
            );

            if (!empty($pData['variants'])) {
                foreach ($pData['variants'] as $v) {
                    ProductVariant::updateOrCreate(
                        ['sku' => $v['sku']],
                        [
                            'product_id' => $product->id,
                            'color_name' => $v['color_name'],
                            'color_code' => $v['color_code'],
                            'price' => $v['price'],
                            'stock' => $v['stock'],
                            'image' => $pData['image']
                        ]
                    );
                }
            }
        }

        // -------------------------------------------------------------
        // 3. Tạo Mã Giảm Giá Mẫu (Coupons)
        // -------------------------------------------------------------
        $coupons = [
            [
                'code' => 'CHAOBANMOI',
                'discount_type' => 'fixed',
                'discount_value' => 200000,
                'min_order_value' => 5000000,
                'usage_limit' => 100,
                'used_count' => 0,
                'expires_at' => now()->addMonths(6),
            ],
            [
                'code' => 'GIAM10',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_value' => 7000000,
                'usage_limit' => 50,
                'used_count' => 0,
                'expires_at' => now()->addMonths(3),
            ],
            [
                'code' => 'FREESHIP',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'min_order_value' => 1000000,
                'usage_limit' => 500,
                'used_count' => 0,
                'expires_at' => now()->addYear(),
            ],
        ];

        foreach ($coupons as $c) {
            Coupon::updateOrCreate(
                ['code' => $c['code']],
                $c
            );
        }

        $this->command?->info('Khởi tạo thành công: Danh mục, Sản phẩm, Biến thể và Mã giảm giá mẫu!');
    }
}
