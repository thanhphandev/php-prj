</main>
<footer class="bg-gray-900 text-white pt-16 pb-8">
    <div class="container mx-auto px-4">
        <!-- Top Footer Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- About Company -->
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <img class="w-10 h-10 object-contain bg-white p-1 rounded" src="/assets/images/logo.png" alt="Zesty AI Logo">
                    <h3 class="text-xl font-bold text-white">Zesty AI</h3>
                </div>
                <p class="text-gray-400 mb-6">Chúng tôi cung cấp các giải pháp trí tuệ nhân tạo tiên tiến, giúp doanh nghiệp tự động hóa và tối ưu hóa quy trình làm việc.</p>
                <div class="flex space-x-4">
                    <a href="#" class="social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="footer-title">Liên kết nhanh</h4>
                <div class="gradient-line mb-6"></div>
                <ul class="space-y-3">
                    <li><a href="/about.php" class="footer-link">Về chúng tôi</a></li>
                    <li><a href="/services.php" class="footer-link">Dịch vụ</a></li>
                    <li><a href="/case-studies.php" class="footer-link">Case studies</a></li>
                    <li><a href="/blog.php" class="footer-link">Blog</a></li>
                    <li><a href="/careers.php" class="footer-link">Tuyển dụng</a></li>
                    <li><a href="/contact.php" class="footer-link">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Products/Services -->
            <div>
                <h4 class="footer-title">Sản phẩm & Dịch vụ</h4>
                <div class="gradient-line mb-6"></div>
                <ul class="space-y-3">
                    <li><a href="/products/chatbot.php" class="footer-link">Zesty Chatbot</a></li>
                    <li><a href="/products/analytics.php" class="footer-link">Phân tích dữ liệu</a></li>
                    <li><a href="/products/automation.php" class="footer-link">Tự động hóa</a></li>
                    <li><a href="/products/personalization.php" class="footer-link">Cá nhân hóa</a></li>
                    <li><a href="/products/enterprise.php" class="footer-link">Giải pháp doanh nghiệp</a></li>
                    <li><a href="/pricing.php" class="footer-link">Bảng giá</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="footer-title">Thông tin liên hệ</h4>
                <div class="gradient-line mb-6"></div>
                <ul class="space-y-4">
                    <li class="flex items-start space-x-3">
                        <i class="fas fa-map-marker-alt mt-1 text-indigo-400"></i>
                        <span class="text-gray-400">Tầng 16, Tòa nhà Viettel, 285 Cách Mạng Tháng 8, P.12, Q.10, TP.HCM</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <i class="fas fa-phone-alt text-indigo-400"></i>
                        <span class="text-gray-400">+84 28 3868 7979</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <i class="fas fa-envelope text-indigo-400"></i>
                        <span class="text-gray-400">contact@zestyai.vn</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <i class="fas fa-clock text-indigo-400"></i>
                        <span class="text-gray-400">Thứ Hai - Thứ Sáu: 8:30 - 17:30</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Newsletter Section -->
        <div class="bg-gray-800 rounded-lg p-8 mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                <div class="lg:col-span-2">
                    <h4 class="text-xl font-bold mb-2">Đăng ký nhận thông tin</h4>
                    <p class="text-gray-400">Nhận các bài viết, tin tức và cập nhật mới nhất từ Zesty AI</p>
                </div>
                <div>
                    <form class="flex">
                        <input type="email" placeholder="Email của bạn" class="px-4 py-3 w-full rounded-l-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 px-4 py-3 rounded-r-lg transition duration-300">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="border-t border-gray-800 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-500 text-sm mb-4 md:mb-0">
                    <p>&copy; <?php echo date('Y'); ?> Zesty AI. Tất cả các quyền được bảo lưu.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="/terms.php" class="text-gray-500 hover:text-white text-sm">Điều khoản sử dụng</a>
                    <a href="/privacy.php" class="text-gray-500 hover:text-white text-sm">Chính sách bảo mật</a>
                    <a href="/cookies.php" class="text-gray-500 hover:text-white text-sm">Chính sách cookie</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back To Top Button -->
<div id="back-to-top" class="fixed bottom-6 right-6 w-12 h-12 bg-indigo-600 text-white rounded-full cursor-pointer shadow-lg flex items-center justify-center opacity-0 invisible transition-all duration-300 hover:bg-indigo-700">
    <i class="fas fa-chevron-up"></i>
</div>

<script>
    $(document).ready(function() {
        const $backToTopButton = $("#back-to-top");

        $(window).on("scroll", function() {
            if ($(window).scrollTop() > 300) {
                $backToTopButton.removeClass("opacity-0 invisible").addClass("opacity-100 visible");
            } else {
                $backToTopButton.addClass("opacity-0 invisible").removeClass("opacity-100 visible");
            }
        });

        $backToTopButton.on("click", function() {
            $("html, body").animate({
                scrollTop: 0
            }, "smooth");
        });
    });
</script>
</body>

</html>