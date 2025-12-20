<?php include __DIR__.'/includes/header.php'; ?>
<?php include __DIR__.'/includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="max-w-7xl mx-auto p-6 mt-10 grid md:grid-cols-2 gap-12 items-center">
  <div>
    <h1 class="text-5xl font-extrabold mb-6 text-green-700">Agricultural supply chain & Demand forecasting system</h1>
    <p class="mb-8 text-gray-700 text-lg">
      AgriChain connects farmers, inspectors, transporters, packagers, and customers with verifiable, blockchain-ready records for a fully transparent supply chain.
    </p>
    <div class="flex flex-wrap gap-4">
      <a href="/agrichain_php_tailwind/auth/register.php" class="px-6 py-3 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 transition">Get Started</a>
      <a href="/agrichain_php_tailwind/auth/login.php" class="px-6 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">Login</a>
    </div>
  </div>

  <!-- Roles / Features -->
  <div class="bg-white border rounded-2xl p-6 shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Roles & Features</h2>
    <ul class="space-y-3 text-gray-700 text-sm md:text-base">
      <li>👩‍🌾 <strong>Farmer:</strong> Crops, fertilizer & harvest analytics</li>
      <li>🔍 <strong>Inspector:</strong> Inspections with photos and PDF reports</li>
      <li>🚚 <strong>Transporter:</strong> Live GPS & route planner</li>
      <li>📦 <strong>Packaging:</strong> Batches, labels, QR, inventory</li>
      <li>🛒 <strong>Customer:</strong> Orders, tracking & feedback</li>
      <li>🛡️ <strong>Admin:</strong> Users & system reports</li>
    </ul>
  </div>
</section>

<!-- Farm-to-Fork Process -->
<section class="max-w-7xl mx-auto p-6 mt-16">
  <h2 class="text-3xl font-bold text-green-700 mb-8 text-center">Farm-to-Fork Process</h2>
  <div class="grid md:grid-cols-5 gap-6 text-center">
    <div class="p-6 bg-white shadow rounded-xl">
      🌱<p class="mt-2 font-semibold">Farmers</p>
    </div>
    <div class="p-6 bg-white shadow rounded-xl">
      🔍<p class="mt-2 font-semibold">Inspections</p>
    </div>
    <div class="p-6 bg-white shadow rounded-xl">
      🚚<p class="mt-2 font-semibold">Transport</p>
    </div>
    <div class="p-6 bg-white shadow rounded-xl">
      📦<p class="mt-2 font-semibold">Packaging</p>
    </div>
    <div class="p-6 bg-white shadow rounded-xl">
      🛒<p class="mt-2 font-semibold">Customers</p>
    </div>
  </div>
</section>

<!-- Statistics Section -->
<section class="max-w-7xl mx-auto p-6 mt-16 bg-green-50 rounded-2xl">
  <h2 class="text-3xl font-bold mb-6 text-green-700 text-center">AgriChain at a glance</h2>
  <div class="grid grid-cols-1 md:grid-cols-5 gap-6 text-center">
    <div class="bg-white shadow rounded-xl p-6">
      <h3 class="text-4xl font-bold text-green-600" data-count="120">0</h3>
      <p class="mt-2 text-gray-700">Farmers</p>
    </div>
    <div class="bg-white shadow rounded-xl p-6">
      <h3 class="text-4xl font-bold text-green-600" data-count="350">0</h3>
      <p class="mt-2 text-gray-700">Batches</p>
    </div>
    <div class="bg-white shadow rounded-xl p-6">
      <h3 class="text-4xl font-bold text-green-600" data-count="540">0</h3>
      <p class="mt-2 text-gray-700">Inspections</p>
    </div>
    <div class="bg-white shadow rounded-xl p-6">
      <h3 class="text-4xl font-bold text-green-600" data-count="420">0</h3>
      <p class="mt-2 text-gray-700">Shipments</p>
    </div>
    <div class="bg-white shadow rounded-xl p-6">
      <h3 class="text-4xl font-bold text-green-600" data-count="890">0</h3>
      <p class="mt-2 text-gray-700">Orders</p>
    </div>
  </div>
</section>

<script>
// Simple counter animation
document.querySelectorAll('[data-count]').forEach(el => {
  let count = 0;
  const target = parseInt(el.getAttribute('data-count'));
  const step = Math.ceil(target / 100);
  const interval = setInterval(() => {
    count += step;
    if(count >= target) { count = target; clearInterval(interval); }
    el.textContent = count;
  }, 20);
});
</script>

<!-- Testimonials Section -->
<section class="max-w-7xl mx-auto p-6 mt-16">
  <h2 class="text-3xl font-bold mb-8 text-green-700 text-center">What users say</h2>
  <div class="grid md:grid-cols-3 gap-6">
    <div class="bg-white shadow rounded-xl p-6 text-center">
      <p class="italic mb-3">“AgriChain made managing my crops so much easier and transparent!”</p>
      <p class="font-semibold">– Farmer John</p>
    </div>
    <div class="bg-white shadow rounded-xl p-6 text-center">
      <p class="italic mb-3">“Inspection reports are now seamless and verifiable.”</p>
      <p class="font-semibold">– Inspector Maria</p>
    </div>
    <div class="bg-white shadow rounded-xl p-6 text-center">
      <p class="italic mb-3">“I can track my orders in real-time. Fantastic platform!”</p>
      <p class="font-semibold">– Customer Alex</p>
    </div>
  </div>
</section>

<!-- Call-to-Action Banner -->
<section class="max-w-7xl mx-auto p-6 mt-16 bg-green-600 text-white rounded-2xl text-center">
  <h2 class="text-3xl font-bold mb-4">Join AgriChain Today</h2>
  <p class="mb-6">Connect with farmers, monitor shipments, and ensure transparency in your supply chain.</p>
  <a href="/agrichain_php_tailwind/auth/register.php" class="px-8 py-3 rounded-xl bg-white text-green-700 font-semibold hover:bg-gray-100 transition">Get Started</a>
</section>

<?php include __DIR__.'/includes/footer.php'; ?>
