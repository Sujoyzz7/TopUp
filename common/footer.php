<footer class="bg-[#1E293B] text-white pt-16 pb-24 sm:pb-16 mt-20">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="space-y-6">
            <a href="index.php" class="flex items-center gap-2">
                <div class="bg-blue-600 text-white p-2 rounded-lg"><i class="fa-solid fa-bolt"></i></div>
                <span class="font-bold text-2xl uppercase tracking-tighter">Prime TopUp</span>
            </a>
            <p class="text-gray-400 text-sm leading-relaxed">
                Connect and browse safely on Prime TopUp. We provide the quickest and safest way to top up your favorite
                games.
            </p>
            <div class="flex gap-4">
                <a href="#"
                    class="w-10 h-10 rounded-full border border-gray-700 flex items-center justify-center hover:bg-white hover:text-[#1E293B] transition-all"><i
                        class="fa-brands fa-facebook-f"></i></a>
                <a href="#"
                    class="w-10 h-10 rounded-full border border-gray-700 flex items-center justify-center hover:bg-white hover:text-[#1E293B] transition-all"><i
                        class="fa-brands fa-instagram"></i></a>
                <a href="#"
                    class="w-10 h-10 rounded-full border border-gray-700 flex items-center justify-center hover:bg-white hover:text-[#1E293B] transition-all"><i
                        class="fa-brands fa-youtube"></i></a>
            </div>
        </div>

        <div>
            <h4 class="font-bold text-lg mb-6">Quick Links</h4>
            <ul class="space-y-4 text-gray-400 text-sm">
                <li><a href="index.php" class="hover:text-blue-500 transition-colors">Home</a></li>
                <li><a href="profile.php" class="hover:text-blue-500 transition-colors">My Profile</a></li>
                <li><a href="order.php" class="hover:text-blue-500 transition-colors">Track Order</a></li>
                <li><a href="addmoney.php" class="hover:text-blue-500 transition-colors">Add Balance</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-bold text-lg mb-6">Support Center</h4>
            <ul class="space-y-4 text-gray-400 text-sm">
                <li class="flex items-start gap-3">
                    <i class="fa-solid fa-clock mt-1 text-blue-500"></i>
                    <span>Help Line [9AM-12PM]<br>WhatsApp HelpLine</span>
                </li>
                <li class="flex items-start gap-3">
                    <i class="fa-solid fa-paper-plane mt-1 text-blue-500"></i>
                    <span>Help Line [9AM-12PM]<br>Telegram Support</span>
                </li>
            </ul>
        </div>

        <div>
            <h4 class="font-bold text-lg mb-6">Stay Connected</h4>
            <p class="text-gray-400 text-sm mb-4">Subscribe to get latest offers.</p>
            <div class="flex gap-2">
                <input type="email" placeholder="Email"
                    class="bg-gray-800 border-none rounded-lg px-4 py-2 text-sm w-full focus:ring-1 focus:ring-blue-500">
                <button class="bg-blue-600 px-4 py-2 rounded-lg"><i class="fa-solid fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-6 mt-16 pt-8 border-t border-gray-800 text-center text-gray-500 text-xs">
        ©
        <?php echo date('Y'); ?>
        <?php echo $settings['site_name']; ?> | All Rights Reserved | Developed by Team Prime
    </div>
</footer>

</body>

</html>