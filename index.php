<?php
include 'common/config.php';
include 'common/header.php';

// Fetch Sliders
$stmt = $pdo->query("SELECT * FROM sliders ORDER BY id DESC");
$sliders = $stmt->fetchAll();

// Fetch Categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

<main class="max-w-7xl mx-auto px-4 py-6 sm:py-10 space-y-10">

    <!-- Slider Section -->
    <div class="relative w-full overflow-hidden rounded-[2.5rem] shadow-2xl group border-4 border-white">
        <div id="slider" class="flex transition-transform duration-700 ease-in-out">
            <?php if (empty($sliders)): ?>
                <div
                    class="min-w-full h-48 sm:h-96 bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white p-8">
                    <div class="text-center">
                        <i class="fa-solid fa-gamepad text-6xl mb-4 opacity-50"></i>
                        <h2 class="text-3xl font-bold">Welcome to Prime TopUp</h2>
                        <p class="mt-2 opacity-80">Add sliders from admin panel to see banners here!</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($sliders as $slider): ?>
                    <a href="<?php echo $slider['link'] ?? '#'; ?>" class="min-w-full">
                        <img src="<?php echo $slider['image']; ?>" class="w-full h-48 sm:h-96 object-cover" alt="Slider Image">
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Slider Dots -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
            <?php if (!empty($sliders)): ?>
                <?php foreach ($sliders as $i => $s): ?>
                    <div class="w-2.5 h-2.5 rounded-full bg-white opacity-40 transition-all slider-dot"
                        data-index="<?php echo $i; ?>"></div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="w-2.5 h-2.5 rounded-full bg-white opacity-100"></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section Title -->
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <h2 class="text-2xl font-black text-[#1E293B] tracking-tight flex items-center gap-3">
                <span class="w-2 h-8 bg-blue-600 rounded-full"></span>
                GAME SHOP
            </h2>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest pl-5">Select your favorite game</p>
        </div>
        <div class="hidden sm:flex items-center gap-2">
            <button
                class="w-10 h-10 rounded-full bg-white border border-gray-100 shadow-sm flex items-center justify-center hover:bg-gray-50 transition-all"><i
                    class="fa-solid fa-chevron-left text-xs"></i></button>
            <button
                class="w-10 h-10 rounded-full bg-white border border-gray-100 shadow-sm flex items-center justify-center hover:bg-gray-50 transition-all"><i
                    class="fa-solid fa-chevron-right text-xs"></i></button>
        </div>
    </div>

    <!-- Game Shop Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-6">
        <?php if (empty($categories)): ?>
            <div class="col-span-full py-20 text-center space-y-4">
                <div
                    class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto text-gray-300 text-3xl">
                    <i class="fa-solid fa-ghost"></i>
                </div>
                <h3 class="font-bold text-gray-400">No games available yet.</h3>
            </div>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
                <a href="game_detail.php?id=<?php echo $cat['id']; ?>"
                    class="group bg-white rounded-[2rem] p-3 shadow-md border border-gray-50 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                    <div class="aspect-square rounded-[1.5rem] overflow-hidden mb-4 relative">
                        <img src="<?php echo $cat['image'] ?? 'https://via.placeholder.com/200'; ?>"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            alt="<?php echo $cat['name']; ?>">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                            <span
                                class="text-white text-xs font-bold bg-blue-600/80 backdrop-blur-sm px-3 py-1.5 rounded-full">Top
                                Up Now</span>
                        </div>
                    </div>
                    <div class="px-2 text-center">
                        <h3
                            class="font-bold text-sm text-gray-800 line-clamp-1 group-hover:text-blue-600 transition-colors uppercase tracking-tight">
                            <?php echo $cat['name']; ?>
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">
                            <?php echo $cat['type'] == 'uid' ? 'UID Topup' : 'Voucher'; ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</main>

<script>
    // Slider Logic
    const slider = document.getElementById('slider');
    const dots = document.querySelectorAll('.slider-dot');
    let currentIndex = 0;
    const slideCount = <?php echo count($sliders); ?>;

    if (slideCount > 0) {
        function showSlide(index) {
            slider.style.transform = `translateX(-${index * 100}%)`;
            dots.forEach((dot, i) => {
                dot.style.opacity = i === index ? '1' : '0.4';
                dot.style.width = i === index ? '24px' : '10px';
            });
        }

        setInterval(() => {
            currentIndex = (currentIndex + 1) % slideCount;
            showSlide(currentIndex);
        }, 1500);

        dots.forEach((dot, i) => {
            dot.onclick = () => {
                currentIndex = i;
                showSlide(i);
            };
        });

        // Init
        showSlide(0);
    }
</script>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>