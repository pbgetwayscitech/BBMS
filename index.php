<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Management System</title>
    <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <!-- Page Specific CSS -->
    <link href="/src/css/index.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons (for SVG icons) -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="min-h-screen flex flex-col" style="background: #093c66ff">

    <!-- Header/Navigation -->
    <header class="shadow-sm py-4" style="background: #093c66ff;">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <img src="/src/assets/logo.png" alt="Blood Bank Logo" class="img-fluid" style="height: 30px; width: 30px;">
            <a href="#" class="text-2xl font-bold text-white">Blood Bank Management System</a>
            <nav>
                <ul class="flex space-x-6">

                    <li><a href="/src/pages/auth/registeruser.php"
                            class="text-white hover:text-blue-200 font-medium">Register Donor</a>
                    </li>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
                        <li><a href="/src/pages/search.php" class="text-white hover:text-blue-200 font-medium">Search</a>
                        </li>
                        <li><a href="/src/pages/auth/logout.php"
                                class="text-orange-500 hover:text-orange-200 font-bold">Logout</a>
                        </li>
                        <?php if ($_SESSION['role'] == "admin") { ?>
                            <li><a href="/src/pages/admin/dashboard.php"
                                    class="text-orange-500 hover:text-orange-200 font-bold">Admin
                                    Dashboard</a>
                            </li>
                        <?php } else { ?>
                            <li><a href="/src/pages/user/dashboard.php"
                                    class="text-orange-500 hover:text-orange-200 font-bold">User
                                    Dashboard</a>
                            </li>
                        <?php } ?>
                    <?php } else { ?>
                        <li><a href="/src/pages/auth/login.php"
                                class="text-orange-500 hover:text-orange-200 font-bold">Login</a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section text-center relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <h1 class="text-5xl md:text-6xl font-extrabold leading-tight mb-6 animate-fade-in-down">
                Empowering Life-Saving Connections
            </h1>
            <p class="text-xl md:text-2xl font-light mb-10 max-w-3xl mx-auto animate-fade-in-up">
                The Blood Bank Management System streamlines donor registration, blood inventory tracking, and emergency
                coordination, ensuring timely and secure blood availability.
            </p>
            <div class="flex flex-col md:flex-row justify-center space-y-4 md:space-y-0 md:space-x-6">
                <a href="/src/pages/auth/registeruser.php" class="btn-primary animate-scale-in">
                    Donor: Register
                </a>
                <a href="/src/pages/auth/registerbank.php" class="btn-secondary animate-scale-in delay-100">
                    Blood Bank: Register
                </a>
            </div>
        </div>

        <!-- Blood drop SVG background with animation -->
        <div class="blood-drop-svg">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid slice">
                <defs>
                    <radialGradient id="grad" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
                        <stop offset="0%" stop-color="rgba(18, 128, 253, 1)" />
                        <stop offset="100%" stop-color="rgba(23, 131, 202, 0.95)" />
                    </radialGradient>
                </defs>
                <path fill="url(#grad)"
                    d="M50 0 C 70 20, 90 40, 90 60 C 90 80, 70 100, 50 100 C 30 100, 10 80, 10 60 C 10 40, 30 20, 50 0 Z" />
            </svg>
        </div>
    </section>

    <!-- Blood Group Compatibility Section -->
    <section id="compatibility" class="py-16 md:py-24 bg-gray-100">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-12">Blood Group Compatibility Chart</h2>
            <p class="text-lg text-gray-700 mb-8 max-w-3xl mx-auto">
                *This chart shows the donation and reception compatibility between different blood groups.
            </p>
            <div class="overflow-x-auto rounded-xl shadow-lg">
                <table class="w-full text-sm text-left text-gray-700 compatibility-table">
                    <thead class="text-xs uppercase">
                        <tr>
                            <th scope="col" class="py-3 px-6 rounded-tl-xl">Blood Group</th>
                            <th scope="col" class="py-3 px-6">Can Donate To</th>
                            <th scope="col" class="py-3 px-6 rounded-tr-xl">Can Receive From</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-4 px-6">A+</td>
                            <td class="py-4 px-6">A+, AB+</td>
                            <td class="py-4 px-6">A+, A-, O+, O-</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">A-</td>
                            <td class="py-4 px-6">A+, A-, AB+, AB-</td>
                            <td class="py-4 px-6">A-, O-</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">B+</td>
                            <td class="py-4 px-6">B+, AB+</td>
                            <td class="py-4 px-6">B+, B-, O+, O-</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">B-</td>
                            <td class="py-4 px-6">B+, B-, AB+, AB-</td>
                            <td class="py-4 px-6">B-, O-</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">AB+</td>
                            <td class="py-4 px-6">AB+</td>
                            <td class="py-4 px-6">All Blood Groups (Universal Recipient)</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">AB-</td>
                            <td class="py-4 px-6">AB+, AB-</td>
                            <td class="py-4 px-6">AB-, A-, B-, O-</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">O+</td>
                            <td class="py-4 px-6">O+, A+, B+, AB+</td>
                            <td class="py-4 px-6">O+, O-</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">O-</td>
                            <td class="py-4 px-6">All Blood Groups (Universal Donor)</td>
                            <td class="py-4 px-6">O-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-sm text-gray-500 mt-6">
                *This chart provides general compatibility information. Always consult with medical professionals for
                specific transfusion needs.
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-12">Key Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-8 flex flex-col items-center">
                    <div class="p-4 rounded-full mb-6 bg-white bg-opacity-20">
                        <!-- Semi-transparent white background -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-user-plus text-white icon-large">
                            <!-- White icon -->
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="19" x2="19" y1="8" y2="14" />
                            <line x1="22" x2="16" y1="11" y2="11" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Effortless Donor Registration</h3>
                    <p class="text-white text-opacity-80">Simplify donor sign-up and management with accurate, secure,
                        and easily
                        accessible records.</p>
                </div>
                <!-- Feature 2 -->
                <div class="feature-card p-8 flex flex-col items-center">
                    <div class="p-4 rounded-full mb-6 bg-white bg-opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-boxes text-white icon-large">
                            <path
                                d="M2.97 14.999L12 19.5l9.03-4.501M12 4.5l-9.03 4.501L12 13.5l9.03-4.501L12 4.5zM2.97 19.5l9.03-4.501L21.03 19.5M12 13.5v6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Real-time Inventory Tracking</h3>
                    <p class="text-white text-opacity-80">Monitor blood stock in real-time by type and expiration,
                        reducing shortages
                        and wastage.</p>
                </div>
                <!-- Feature 3 -->
                <div class="feature-card p-8 flex flex-col items-center">
                    <div class="p-4 rounded-full mb-6 bg-white bg-opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-shield-check text-white icon-large">
                            <path d="M20 13c0 5-4 9-8 9s-8-4-8-9V5l8-3 8 3v8Z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Robust Data Security</h3>
                    <p class="text-white text-opacity-80">Ensure accurate, private, and secure data storage with
                        advanced encryption
                        and access controls.</p>
                </div>
                <!-- Feature 4 -->
                <div class="feature-card p-8 flex flex-col items-center">
                    <div class="p-4 rounded-full mb-6 bg-white bg-opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-heart-pulse text-white icon-large">
                            <path
                                d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                            <path d="M3.22 10H9.5L12 22l3.5-10h5.28" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Swift Emergency Support</h3>
                    <p class="text-white text-opacity-80">Match and notify donors swiftly in urgent cases,
                        significantly reducing
                        response times.</p>
                </div>
                <!-- Feature 5 -->
                <div class="feature-card p-8 flex flex-col items-center">
                    <div class="p-4 rounded-full mb-6 bg-white bg-opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-bar-chart-2 text-white icon-large">
                            <path d="M18 20V10" />
                            <path d="M12 20V4" />
                            <path d="M6 20v-6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Insightful Reports & Analytics</h3>
                    <p class="text-white text-opacity-80">Gain valuable insights on blood usage and inventory trends
                        for better
                        decision-making.</p>
                </div>
                <!-- Feature 6 -->
                <div class="feature-card p-8 flex flex-col items-center">
                    <div class="p-4 rounded-full mb-6 bg-white bg-opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-megaphone text-white icon-large">
                            <path d="m3 11 18-2L13 22 3 11Z" />
                            <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6" />
                            <path d="m11.6 16.8 6.4-6.4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Boost Awareness & Engagement</h3>
                    <p class="text-white text-opacity-80">Educate, recognize donors, and promote regular donations to
                        foster
                        community participation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-14 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-12">Frequently Asked Questions</h2>

            <div class="max-w-5xl mx-auto">
                <!-- FAQ Item 1 -->
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        What is the Blood Bank Management System?
                        <svg class="faq-icon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>It's a web-based application designed to streamline the process of blood donation, inventory
                            management for blood banks, and blood request handling. It connects donors with blood banks
                            efficiently.</p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        How do I register as a donor?
                        <svg class="faq-icon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Click on the "Donor: Register / Login" button on the homepage. You will be directed to a
                            registration form where you can provide your personal, contact, and medical details. A
                            unique ID will be generated for your login.</p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        Can blood banks register on this platform?
                        <svg class="faq-icon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, blood banks can register by clicking the "Blood Bank: Register / Login" button. They
                            will need to provide their bank details, contact information, and create an account to
                            manage their stock and requests.</p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        Is my personal and medical information secure?
                        <svg class="faq-icon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>We prioritize the security of your data. All sensitive information, including passwords, is
                            hashed. We employ secure database practices to protect your privacy. For more details,
                            please refer to our <a href="/src/pages/privacypolicy.php"
                                class="text-blue-600 hover:underline">Privacy Policy</a>.</p>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        How can I request blood as a donor?
                        <svg class="faq-icon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Once logged in as a donor, navigate to your dashboard and click on "Request Blood." You can
                            then select a blood bank, specify the blood group needed, and provide details for the
                            patient.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Healthcare Authorities Section -->
    <section id="authorities" class="py-16 md:py-24 bg-gray-100">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-12">Key Healthcare & Pharmaceutical Authorities in India</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Authority Card 1 -->
                <div class="authority-card">
                    <h3>Ministry of Health & Family Welfare (MoHFW)</h3>
                    <p>The apex body for health policy, planning, and implementation in India. It oversees various
                        departments and initiatives related to public health.</p>
                    <a href="https://www.mohfw.gov.in/" target="_blank" rel="noopener noreferrer">Visit Website
                        &rarr;</a>
                </div>

                <!-- Authority Card 2 -->
                <div class="authority-card">
                    <h3>Indian Council of Medical Research (ICMR)</h3>
                    <p>India's premier body for the formulation, coordination, and promotion of biomedical research. It
                        plays a crucial role in health policy and research ethics.</p>
                    <a href="https://www.icmr.gov.in/" target="_blank" rel="noopener noreferrer">Visit Website
                        &rarr;</a>
                </div>

                <!-- Authority Card 3 -->
                <div class="authority-card">
                    <h3>Central Drugs Standard Control Organization (CDSCO)</h3>
                    <p>The national regulatory body for Indian pharmaceuticals and medical devices. It ensures the
                        quality, safety, and efficacy of drugs and cosmetics.</p>
                    <a href="https://cdsco.gov.in/" target="_blank" rel="noopener noreferrer">Visit Website &rarr;</a>
                </div>

                <!-- Authority Card 4 -->
                <div class="authority-card">
                    <h3>National Blood Transfusion Council (NBTC)</h3>
                    <p>An apex body that formulates policies and guidelines for blood transfusion services in India,
                        ensuring safe and adequate blood supply.</p>
                    <a href="https://naco.gov.in/national-blood-transfusion-council-nbtc" target="_blank"
                        rel="noopener noreferrer">Visit Website &rarr;</a>
                </div>

                <!-- Authority Card 5 -->
                <div class="authority-card">
                    <h3>National Health Authority (NHA)</h3>
                    <p>Responsible for implementing India's flagship public health insurance scheme, Ayushman Bharat -
                        Pradhan Mantri Jan Arogya Yojana (AB-PMJAY).</p>
                    <a href="https://nha.gov.in/" target="_blank" rel="noopener noreferrer">Visit Website &rarr;</a>
                </div>

                <!-- Authority Card 6 -->
                <div class="authority-card">
                    <h3>Pharmacy Council of India (PCI)</h3>
                    <p>A statutory body that regulates pharmacy education and practice in India, ensuring standards for
                        pharmacists and pharmaceutical services.</p>
                    <a href="https://www.pci.nic.in/" target="_blank" rel="noopener noreferrer">Visit Website &rarr;</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-16 md:py-24 bg-blue-700 text-white text-center">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold mb-6">Ready to Make a Difference?</h2>
            <p class="text-xl mb-10 max-w-2xl mx-auto">
                Join our mission to save lives. Whether you're a donor or a blood bank, your participation is crucial.
            </p>
            <div class="flex flex-col md:flex-row justify-center space-y-4 md:space-y-0 md:space-x-6">
                <a href="/src/pages/auth/registeruser.php" class="btn-primary-inverted">
                    Become a Donor
                </a>
                <a href="/src/pages/auth/registerbank.php" class="btn-secondary-inverted">
                    Register Your Blood Bank
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p><?php echo date("d/M/Y"); ?> Blood Bank Management System</p>
            <p class="mt-2 text-sm">Phone: +00 000 00000 </p>
            <a href="/src/pages/privacypolicy.php" class="mt-2 text-sm">Privacy Policy</a>
        </div>
    </footer>

    <!-- Script to initialize Lucide icons and FAQ functionality -->
    <script>
        lucide.createIcons();

        document.addEventListener('DOMContentLoaded', function () {
            const faqQuestions = document.querySelectorAll('.faq-question');

            faqQuestions.forEach(button => {
                button.addEventListener('click', () => {
                    const answer = button.nextElementSibling;
                    const icon = button.querySelector('.faq-icon');

                    // Toggle active class on the question button
                    button.classList.toggle('active');

                    // Toggle open class on the answer
                    if (answer.classList.contains('open')) {
                        answer.classList.remove('open');
                        answer.style.maxHeight = null; // Reset max-height to enable smooth collapse
                        answer.style.padding = '0 1.5rem'; // Reset padding to initial state
                    } else {
                        // Close all other open FAQs
                        faqQuestions.forEach(otherButton => {
                            if (otherButton !== button && otherButton.classList.contains('active')) {
                                otherButton.classList.remove('active');
                                const otherAnswer = otherButton.nextElementSibling;
                                const otherIcon = otherButton.querySelector('.faq-icon');
                                otherAnswer.classList.remove('open');
                                otherAnswer.style.maxHeight = null;
                                otherAnswer.style.padding = '0 1.5rem';
                                otherIcon.style.transform = 'rotate(0deg)';
                            }
                        });
                        answer.classList.add('open');
                        // Set max-height to scrollHeight to animate to full height
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                        answer.style.padding = '1.2rem 1.5rem 1.5rem'; // Set padding when open
                    }
                });
            });
        });
    </script>
</body>

</html>
